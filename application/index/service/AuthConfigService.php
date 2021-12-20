<?php

namespace app\index\service;
use think\Db;
use think\Exception;

class AuthConfigService
{

    /**
     * @authName 更新权限配置
     * @author zhangxiny
     * @createTime 2021-09-06 17:39:37
     * @param $auth_config_id
     * @return array
     */
    public function updateAuthConfig($auth_config_id) {
        Db::startTrans();  //开启事务
        $msgArr = [];  //更新记录
        try {
            //得到权限配置
            $authConfigData = Db::name('auth_config')
                ->where(array('id'=>$auth_config_id))
                ->field('id,name,file_path,module,sort')
                ->find();
            //调用权限类根据路径生成权限组
            $getAuth = new \GetAuth();
            //更新的权限名
            $msgArr['authConfigMsg']['name'] = $authConfigData['name'];
            //清空这个配置下的权限组
            Db::name('auth_class')->where(array('auth_config_id'=>$authConfigData['id']))->delete(true);
            //重新从文件夹中得到权限组
            $authArr = $getAuth->index($authConfigData['file_path']);
            //成功数量
            $classSuccessNum = 0;
            //失败数量
            $classErrorNum = 0;
            //函数成功数量
            $methodSuccessNum = 0;
            //函数失败数量
            $methodErrorNum = 0;
            //待插入的函数
            $insertMethodData = [];
            //生成权限
            foreach ($authArr as $key_auth => $auth){
                //插入控制器权限
                $result = $this->insertClass($auth['class'], $authConfigData);
                //控制器权限插入成功
                if($result['code']==1){
                    $classSuccessNum += 1;
                }else{
                    $classErrorNum += 1;
                }
                //类更新情况
                $msgArr['authClassMsg']['successNum'] = $classSuccessNum;
                $msgArr['authClassMsg']['errorNum'] = $classErrorNum;
                //如果类插入失败，跳出
                if($result['code']==0){
                    continue;
                }
                //插入后返回的控制器编号
                $class_id = $result['class_id'];
                //遍历该控制器的所有函数
                foreach($auth['methods'] as $key_methods => $method){
                    $insertMethod['method'] = $method;
                    $insertMethod['authConfigData'] = $authConfigData;
                    $insertMethod['class_id'] = $class_id;
                    $insertMethod['class_name'] = $auth['class']['name'];
                    $insertMethodData[] = $insertMethod;
                }
                //函数更新情况
                $msgArr['authClassMsg']['methodSuccessNum'] = $methodSuccessNum;
                $msgArr['authClassMsg']['methodErrorNum'] = $methodErrorNum;
            }
            //函数信息批量插入到数据
            $insertResult = $this->insertMethodAll($insertMethodData);
            $msgArr['authClassMsg']['methodSuccessNum'] = $insertResult['executeNum'];
            Db::commit();
            return ['msgArr' => $msgArr, 'type' => 'update_auth_config'];
        }catch(Exception $ex){
            write_file_for_service('AuthConfigService-updateAuthConfig', $ex->getMessage());
            Db::rollback();
            return ['msgArr' => $msgArr, 'type' => 'update_auth_config'];
        }
    }

    /**
     * @authName 插入函数权限
     * @author zhangxiny
     * @createTime 2021-09-06 17:40:26
     * @param $methodArr
     * @return array
     */
    private function insertMethodAll($methodArr){
        try{
            $insertMethodArr = [];
            foreach($methodArr as $key => $val){
                $methodData['auth_name'] = $val['method'][getConfigValue('auth_name')];   //函数注释
                $methodData['name'] = $val['method']['name']; //函数名
                if (!empty(getConfigValue('author'))) {
                    $methodData['author'] = $val['method'][getConfigValue('author')]; //控制器创建人
                }
                if (!empty(getConfigValue('create_time'))) {
                    $methodData['class_createtime'] = $val['method'][getConfigValue('create_time')]; //控制器创建时间
                }
                if (!empty(getConfigValue('auth_status'))){
                    $methodData['auth_status'] = intval($val['method'][getConfigValue('auth_status')]); //是否需要控制
                }
                $methodData['module'] = $val['authConfigData']['module'];    //module标识
                $methodData['auth_config_id'] = $val['authConfigData']['id'];    //权限配置编号
                $methodData['parent_class_id'] = $val['class_id']; //所属控制器
                $methodData['url'] = '/'.$val['class_name'].'/'.$val['method']['name']; //菜单Url
                $methodData['url'] = str_replace('//', '/', $methodData['url']);
                $methodData['modifier'] =  $val['method']['modifier']; //菜单Url
                $methodData['createtime'] = time();
                $methodData['level'] = 1;    //级别（函数）
                $insertMethodArr[$key] = $methodData;
            }
            $res = Db::name('auth_class')->insertAll($insertMethodArr);
            //失败
            if($res === false){
                return array('code'=>0,'executeNum'=>0);
            } else{
                return array('code'=>1,'executeNum'=>$res);
            }
        }catch(Exception $ex){
            $error_text = $this->getLogText('函数插入错误','insertMethod',
                '函数名：'.$val['method']['name'].'',$ex->getMessage());
            write_file_for_service('AuthConfig',$error_text);
            return array('name'=>$val['method']['name'],'code'=>0,null);
        }
    }

    /**
     * @authName 插入控制器权限
     * @author zhangxiny
     * @createTime 2021-09-06 17:40:52
     * @param $classArr
     * @param $authConfigArr
     * @return array
     */
    private function insertClass($classArr,$authConfigArr){
        try{
            $classData['createtime'] = time();
            $classData['level'] = 0;    //级别（控制器）
            $classData['name'] = $classArr['name']; //控制器类名
            $classData['auth_name'] = $classArr[getConfigValue('auth_name')];   //控制器类注释
            if (!empty(getConfigValue('author'))) {
                $classData['author'] = $classArr[getConfigValue('author')]; //控制器创建人
            }
            if (!empty(getConfigValue('create_time'))) {
                $classData['class_createtime'] = $classArr[getConfigValue('create_time')]; //控制器创建时间
            }
            if (!empty(getConfigValue('auth_status'))){
                $classData['auth_status'] = intval($classArr[getConfigValue('auth_status')]); //是否需要控制
            }
            $classData['module'] = $authConfigArr['module'];    //module标识
            $classData['auth_config_id'] = $authConfigArr['id'];    //权限配置编号
            $res = Db::name('auth_class')->insertGetId($classData);
            //失败
            if($res === false){
                return array('name'=>$classArr['name'],'code'=>0,null);
            }
            else{
                return array('name'=>$classArr['name'],'code'=>1,'class_id'=>$res);
            }
        }catch(Exception $ex){
            $error_text = $this->getLogText('控制器插入错误','insertClass',
                '控制器名：'.$classArr['name'].'',$ex->getMessage());
            write_file_for_service('AuthConfig',$error_text);
            return array('name'=>$classArr['name'],'code'=>0,null);
        }
    }

    /**
     * @authName 得到日志文本
     * @author zhangxiny
     * @createTime 2021-09-06 17:41:08
     * @param $reason
     * @param $method
     * @param $text
     * @param $details_text
     * @return string
     */
    private function getLogText($reason,$method,$text,$details_text){
        if(empty($details_text)){
            return "$reason\n方法名：$method\n原因：$text\n";
        }
        return "$reason\n方法名：$method\n原因：$text\n详细原因：$details_text\n";
    }

    /**
     * @authName 保存
     * @author zhangxiny
     * @createTime 2021-09-06 17:46:09
     * @param $data
     */
    public function save($data)
    {
        if (empty($data['id'])) {
            $data['createtime'] = time();
            $id = Db::name('auth_config')->insertGetId($data);
            //同步权限
            $this->updateAuthConfig($id);
        } else {
            Db::name('auth_config')->update($data);
        }
    }
}