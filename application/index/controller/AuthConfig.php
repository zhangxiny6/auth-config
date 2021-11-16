<?php
/**
 * Created by PhpStorm.
 * User: zhangxinyu
 * Date: 2017/10/16
 * Time: 16:36
 */

namespace app\index\controller;

use app\index\service\AuthConfigService;
use think\Db;
use think\Exception;


/**
 * @authName 权限管理
 * @author zhangxiny
 * Class AuthConfig
 * @package app\index\controller
 */
class AuthConfig extends Base
{

    private $table = '';

    public function __construct()
    {
        $this->table = getConfigValue('menu_table');
        parent::__construct();
    }

    /**
     * 权限配置列表
     * @authName 权限配置列表
     * @authStatus 1
     * @author zhangxiny
     * @createTime 2021-11-16 10:23:50
     * @return mixed
     */
    public function index()
    {
        //ajax请求
        if (request()->isAjax()) {
            $this->auth_config_list();
        } else {
            $this->assign('menu_field_module', getConfigValue('menu_field_module'));
            return $this->fetch();
        }
    }

    /**
     * @authName 权限列表
     * @author zhangxiny
     * @createTime 2021-11-16 10:23:27
     */
    private function auth_config_list()
    {
        $where = array();
        //组装查询参数
        $sord = input('sord', '');
        $searchParam['where'] = $where;
        $searchParam['field'] = "file_path,name,module,sort,createtime,id";
        $searchParam['order'] = 'sort ' . $sord;
        $list = $this->_getPageResultFromModel('auth_config', $searchParam, true);
        if (isset($list['rowsDataList'])) {
            $data = $list['rowsDataList'];
            foreach ($data as $key => $item) {
                $data[$key]['createtime'] = date('Y-m-d H:i:s', $item['createtime']);
            }
            $list['rowsDataList'] = $data;
        }
        echo json_encode($list);
    }

    /**
     * @authName 录入或修改一条权限配置
     * @author zhangxiny
     * @createTime 2021-11-16 10:24:35
     */
    public function addAuthConfig()
    {
        //如果是ajax请求
        if (request()->isAjax()) {
            $this->save();
        }
        $id = input('id/d');
        if (empty($id)) {
            $info = get_empty_column_name('auth_config');
        } else {
            $info = Db::name('auth_config')
                ->where(array('id' => $id))
                ->field('id,name,file_path,module,sort')
                ->find();
        }
        $this->assign('info', $info);
        $this->assign('menu_field_module', getConfigValue('menu_field_module'));
        return $this->fetch('add_auth_config');
    }

    /**
     * @authName 保存权限配置
     * @author zhangxiny
     * @createTime 2021-11-16 10:25:29
     */
    private function save()
    {
        $data = [
            'id' => input('id/d'),
            'name' => input('name'),
            'file_path' => input('file_path'),
            'module' => input('module', ''),
            'sort' => input('sort/d'),
        ];
        if (empty($data['module'])) {
            $data['module'] = 'default';
        }
        $data['file_path'] = iconv('UTF-8', 'GB2312', $data['file_path']);
        //判断是否存在该路径
        if (!is_writable($data['file_path'])) {
            $this->error('文件路径不存在或无权限');
        }
        $service = new AuthConfigService();
        $service->save($data);
        $this->success('保存成功！' . $this->getCloseWinScript());
    }

    /**
     * @authName 更新权限配置
     * @author zhangxiny
     * @createTime 2021-09-06 17:41:41
     * @return mixed
     */
    public function updateAuthConfig()
    {
        $auth_config_id = input('auth_config_id/d');
        if (empty($auth_config_id)) {
            $this->error('配置编号不存在！');
        }
        $service = new AuthConfigService();
        $res = $service->updateAuthConfig($auth_config_id);
        $this->assign('msgArr', $res['msgArr']);
        $this->assign('type', $res['type']);
        return $this->fetch('update_msg');
    }

    /**
     * @authName 读取权限配置下子权限
     * @author zhangxiny
     * @createTime 2021-11-16 10:27:46
     * @return mixed
     */
    public function getClassAuth()
    {
        $id = input('id/d');
        if (empty($id)) {
            $this->error('权限配置编号不存在！');
        }
        if (request()->isAjax()) {
            $this->classAuthList($id);
        } else {
            $this->assign('auth_config_id', $id);
            return $this->fetch('get_class_auth');
        }
    }

    /**
     * @authName 得到控制器列表
     * @author zhangxiny
     * @createTime 2021-11-16 10:28:04
     * @param $id
     */
    private function classAuthList($id)
    {
        $module = Db::name('auth_config')
            ->where(array('id' => $id))
            ->value('module');
        $where = array();
        $where['module'] = $module;
        $where['level'] = 0;
        $searchStr = input('searchinfo/s', '', 'trim');
        //组装查询参数
        if (!empty($searchStr)) {
            $where['name'] = ['like', "%" . $searchStr . "%"];
        }
        $sord = input('sord', '');
        $searchParam['where'] = $where;
        $searchParam['field'] = "id,name,auth_name,author,class_createtime,qq_number";
        $searchParam['order'] = 'name ' . $sord;
        $list = $this->_getPageResultFromModel('auth_class', $searchParam, true);
        if (isset($list['rowsDataList'])) {
            $data = $list['rowsDataList'];
            //格式化空数据
            $emptyArr = ['author', 'auth_name', 'qq_number', 'class_createtime'];
            foreach ($data as $key => $item) {
                foreach ($emptyArr as $emptyKey => $emptyItem) {
                    $data[$key][$emptyItem] = empty($item[$emptyItem]) ? '-' : $item[$emptyItem];
                }
                //求出每个控制器子函数个数
                $data[$key]['method_num'] = Db::name('auth_class')
                    ->where(array('parent_class_id' => $item['id'], 'level' => 1))
                    ->count();
            }
            $list['rowsDataList'] = $data;
        }
        echo json_encode($list);
    }

    /**
     * @authName 读取控制器权限下函数权限
     * @author zhangxiny
     * @createTime 2021-11-16 10:29:13
     * @return mixed
     */
    public function getMethodAuth()
    {
        $id = input('class_id/d');
        if (empty($id)) {
            $this->error('控制器编号不存在！');
        }
        if (request()->isAjax()) {
            $this->methodAuthList($id);
        } else {
            $this->assign('class_id', $id);
            return $this->fetch('get_method_auth');
        }
    }

    /**
     * @authName 得到函数列表
     * @author zhangxiny
     * @createTime 2021-11-16 10:29:22
     * @param $class_id 类ID
     */
    private function methodAuthList($class_id)
    {
        $where = array();
        $where['parent_class_id'] = $class_id;
        $where['level'] = 1;
        $searchStr = input('searchinfo/s', '', 'trim');

        //组装查询参数
        if (!empty($searchStr)) {
            $where['name'] = ['like', "%" . $searchStr . "%"];
        }
        $sord = input('sord', '');
        $searchParam['where'] = $where;
        $searchParam['field'] = "id,name,auth_name,author,class_createtime";
        $searchParam['order'] = 'name ' . $sord;
        $list = $this->_getPageResultFromModel('auth_class', $searchParam, true);
        if (isset($list['rowsDataList'])) {
            $data = $list['rowsDataList'];
            //格式化空数据
            $emptyArr = ['author', 'auth_name', 'qq_number'];
            foreach ($data as $key => $item) {
                foreach ($emptyArr as $emptyKey => $emptyItem) {
                    $data[$key][$emptyItem] = empty($item[$emptyItem]) ? '-' : $item[$emptyItem];
                }
                if (empty($data[$key]['class_createtime'])) {
                    $data[$key]['class_createtime'] = '-';
                }
            }
            $list['rowsDataList'] = $data;
        }
        echo json_encode($list);
    }

    /**
     * @authName 读取所有函数权限
     * @author zhangxiny
     * @createTime 2021-11-16 10:29:41
     * @return mixed
     */
    public function getMethodAll()
    {
        if (request()->isAjax()) {
            $this->methodAllList();
        } else {
            return $this->fetch('get_method_auth');
        }
    }

    /**
     * @authName 函数列表
     * @authStatus 1
     * @author zhangxiny
     * @createTime 2017-11-27 14:57:45
     * @qqNumber 2639347794
     */
    private function methodAllList()
    {
        $where = array();
        $searchStr = input('searchinfo/s', '', 'trim');
        $auth_config_id = input('auth_config_id/d');    //权限配置id
        $is_select = input('is_select/d', 1);  //是否使用
        $auth_status = input('auth_status', 1);    //是否需要控制
        if (empty($auth_config_id)) {
            $this->error('权限配置编号不存在');
        }
        //得到该配置的module
        $authConfigData = Db::name('auth_config')
            ->where(array('id' => $auth_config_id))
            ->field('module')
            ->find();
        if (empty($authConfigData)) {
            $this->error('权限配置数据不存在');
        }
        $menuAuthWhere = [
            getConfigValue('menu_field_module') => $authConfigData['module'],
            getConfigValue('menu_field_pid') => ['NEQ', 0],
        ];
        //得到该module的所有菜单
        $urlArr = _menuDb()->name($this->table)
            ->where($menuAuthWhere)
            ->column(getConfigValue('menu_field_url'));
        $urlArr = array_map('strtolower', $urlArr);
        //组装查询参数
        if (!empty($searchStr)) {
            $where['url|author|qq_number'] = ['like', "%" . $searchStr . "%"];
        }
        $where['module'] = $authConfigData['module'];
        if ($is_select == 1) {
            $where['url'] = ['NOT IN', $urlArr];
        } else if ($is_select == 2) {
            $where['url'] = ['IN', $urlArr];
        }
        //是否需要控制
//        if($auth_status!=-1){
//            $where['auth_status'] = $auth_status;
//        }
        //是否加载最近七天的数据
        $last_seven_day = input('is_last_seven_day');
        if ($last_seven_day == 'true') {
            $date = date('Y-m-d', strtotime('-7 days'));
            // 时间区间查询
            $where[''] = ['EXP', 'date_format(class_createtime,"%Y-%m-%d") > "' . $date . '"'];
        }
        $where[''] = ['EXP', Db::raw("url NOT LIKE '%index/%' and url NOT LIKE '%login/%' 
        and url NOT LIKE '%upload/%' and url NOT LIKE '%/\_%' 
        and url NOT LIKE '%Appraisal/%'
        ")];
        //控制器选择
        $controller_select = input('controller_select', -1);
        if ($controller_select != -1 && !empty($controller_select)) {
            $controller_select = Db::name('auth_class')->where([
                'name' => $controller_select,
                'level' => 0,
                'module' => $authConfigData['module']
            ])->value('id');
            $where['parent_class_id'] = $controller_select;
        }
        $where['level'] = 1; //查出函数
        $where['name'] = ['NEQ', '__construct'];
        $sord = input('sord', 'asc');
        $sort_name = input('sidx', 'url');
        $searchParam['where'] = $where;
        $searchParam['field'] = "id,name,auth_name,author,auth_status,class_createtime,parent_class_id,url";
        $searchParam['order'] = $sort_name . ' ' . $sord;
        $list = $this->_getPageResultFromModel('auth_class', $searchParam, true);
        $data = $list['data'];
        foreach ($data as $key => $item) {
            if (empty($data[$key]['class_createtime'])) {
                $data[$key]['class_createtime'] = '-';
            }
            if (empty($data[$key]['author'])) {
                $data[$key]['author'] = '-';
            }
            if (empty($data[$key]['auth_name'])) {
                $data[$key]['auth_name'] = '-';
            }
            $data[$key]['is_select'] = 0;
            //是否被使用
            if (in_array(strtolower($item['url']), $urlArr)) {
                $data[$key]['is_select'] = 1;
                $menuAuthWhere = [
                    getConfigValue('menu_field_module') => $authConfigData['module'],
                    getConfigValue('menu_field_url') => $item['url'],
                ];
                //求出菜单名
                $data[$key]['menu_name'] = _menuDb()->name($this->table)
                    ->where($menuAuthWhere)
                    ->value(getConfigValue('menu_field_title'));
            }
            $className = Db::name('auth_class')->where(array('id' => $item['parent_class_id']))->value('name');
            $data[$key]['name'] = $className . '/' . $item['name'];
        }
        $list['data'] = $data;

        echo json_encode($list);
    }

    /**
     * @authName 得到菜单列表
     * @author zhangxiny
     * @createTime 2021-11-16 10:31:01
     */
    public function getMenuList()
    {
        $filedData = [
            'title' => getConfigValue('menu_field_title'),
            'sort' => getConfigValue('menu_field_sort'),
            'url' => getConfigValue('menu_field_url'),
            'module' => getConfigValue('menu_field_module'),
            'pid' => getConfigValue('menu_field_pid'),
            'id' => getConfigValue('menu_field_id')
        ];
        $parent_menu_id = input('menu_id'); //菜单编号
        $auth_config_id = input('auth_config_id/d');
        if (empty($auth_config_id)) {
            $this->error('权限配置编号不存在');
        }
        $authConfigData = Db::name('auth_config')
            ->where(array('id' => $auth_config_id))
            ->field('name,id,module,sort')
            ->order('sort')
            ->find();
        if (empty($authConfigData)) {
            $this->error('权限配置编号不存在');
        }
        $menuConfigArr = [];
        //查出该权限配置下的所有主菜单
        $menuWhere[$filedData['module']] = $authConfigData['module'];
        //如果存在，则读取该菜单的子数据
        if (!empty($parent_menu_id) && $parent_menu_id != 'root') {
            $menuWhere[$filedData['pid']] = $parent_menu_id;
        } else {
            $menuWhere[$filedData['pid']] = 0;
        }
        $menuFiled = $filedData['title'] . ',' . $filedData['sort'] . ',' . $filedData['id'] . ',' . $filedData['url'];
        $menuArr = _menuDb()->name($this->table)
            ->where($menuWhere)
            ->field($menuFiled)
            ->order('sort')
            ->select();
        //循环该权限配置下的所有主菜单
        foreach ($menuArr as $sonIndex => $menu) {
            $menu = $this->formatField($filedData, $menu);
            $menuConfigData['id'] = $menu['id'];
            $menuConfigData['pId'] = 'root';
            $title = $menu['title'];
            $menuConfigData['childOuter'] = false;  //禁止子节点拖走
            $menuConfigData['name'] = $title;
            $menuConfigArr[count($menuConfigArr)] = $menuConfigData;
            //查出该主菜单的子菜单
            $sonMenu = _menuDb()->name($this->table)
                ->where(array($filedData['module'] => $authConfigData['module'], $filedData['pid'] => $menu['id']))
                ->field($menuFiled)
                ->order('sort')
                ->select();
            foreach ($sonMenu as $sonMenuIndex => $sonMenuItem) {
                $sonMenuItem = $this->formatField($filedData, $sonMenuItem);
                $menuConfigData['id'] = $sonMenuItem['id'];
                $menuConfigData['pId'] = $menu['id'];
                $menuConfigData['name'] = $sonMenuItem['title'];
                $menuConfigData['childOuter'] = false;  //禁止子节点拖走
                $str_index = strrpos($sonMenuItem['url'], '/');
                $title = $sonMenuItem['title'] . '(' . substr($sonMenuItem['url'], 0, $str_index) . ')';
                $menuConfigData['name'] = $title;
                $menuConfigArr[count($menuConfigArr)] = $menuConfigData;
                //查出该子菜单的子菜单
                $sonMenu1 = _menuDb()->name($this->table)
                    ->where(array($filedData['module'] => $authConfigData['module'], $filedData['pid'] => $sonMenuItem['id']))
                    ->field($menuFiled)
                    ->order('sort')
                    ->select();
                foreach ($sonMenu1 as $sonMenuIndex1 => $sonMenuItem1) {
                    $sonMenuItem1 = $this->formatField($filedData, $sonMenuItem1);
                    $menuConfigData['id'] = $sonMenuItem1['id'];
                    $menuConfigData['pId'] = $sonMenuItem['id'];
                    $title = $sonMenuItem1['title'];
                    $menuConfigData['childOuter'] = false;  //禁止子节点拖走
                    $menuConfigData['name'] = $title;
                    $menuConfigArr[count($menuConfigArr)] = $menuConfigData;
                }
            }
        }
        if (empty($parent_menu_id)) {
            $menuConfigData['id'] = 'root';
            $menuConfigData['auth_config_id'] = $authConfigData['id'];
            $menuConfigData['pId'] = 0;
            $menuConfigData['name'] = $authConfigData['name'];
            $menuConfigData['childOuter'] = false;  //禁止子节点拖走
            $menuConfigData['childOuter'] = false;  //禁止子节点拖走
            $menuConfigData['open'] = true;
            $menuConfigArr[] = $menuConfigData;
        }
        echo json_encode($menuConfigArr);
        exit;
    }

    /**
     * @authName 格式化菜单
     * @author zhangxiny
     * @createTime 2021-08-20 09:15:55
     * @param $fieldArr
     * @param $data
     * @return mixed
     */
    public function formatField($fieldArr, $data)
    {
        foreach ($fieldArr as $index => $item) {
            if (isset($data[$item])) {
                $data[$index] = $data[$item];
            }
        }
        return $data;
    }

    /**
     * @authName 格式化菜单状态
     * @author zhangxiny
     * @createTime 2021-11-16 10:31:24
     * @param $status 是否启用
     * @param $title
     * @return string 状态文字
     */
    private function formatStatus($status, $title)
    {
        if ($status == 0) {
            $title = "<s>$title</s>";
        }
//        if($is_auth_show==0){
//            $title = '<span style="color:#a7a7a7;">'.$title.'</span>';
//        }
        return $title;
    }

    /**
     * @authName 配置菜单
     * @author zhangxiny
     * @createTime 2021-11-16 10:31:40
     * @return mixed
     */
    public function configMenu()
    {
        $auth_config_id = input('id/d');
        if (empty($auth_config_id)) {
            $this->error('权限配置编号不存在！');
        }
        $authConfigData = Db::name('auth_config')
            ->where(array('id' => $auth_config_id))
            ->field('module')
            ->find();
        $module = $authConfigData['module'];

        $where['module'] = $authConfigData['module'];
        $where['level'] = 0;
        $selectData = Db::name('auth_class')
            ->where($where)
            ->field('name,id,auth_name')
            ->select();
        $this->assign('ControllerSelectData', $selectData);
        $this->assign('auth_config_id', $auth_config_id);
        $this->assign('module', $module);
        return $this->fetch('config_menu');
    }

    /**
     * @authName 删除菜单
     * @author zhangxiny
     * @create_time 2021-11-16 10:31:56
     */
    public function deleteMenu()
    {
        $menu_id = input('menu_id/d');
        if (empty($menu_id)) {
            $this->error('请选择要删除菜单');
        }
        $pid = getConfigValue('menu_field_pid');
        //是否存在子菜单
        $isExistSon = _menuDb()->name($this->table)
            ->where($pid, $menu_id)
            ->count();
        if ($isExistSon > 0) {
            $this->error('删除失败！请选择删除子菜单');
        }
        $res = _menuDb()->name($this->table)->delete($menu_id);
        if ($res != 0) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * @authName 菜单排序
     * @author  zhangxinyu
     * @create_time 2021-11-16 10:32:03
     */
    public function menuSort()
    {
        Db::startTrans();
        try {
            //要排序的编号
            $ids = input('ids');
            //将编号分成数组
            $idArr = explode(',', $ids);
            //给编号进行排序
            foreach ($idArr as $key => $value) {
                $menuData = [
                    getConfigValue('menu_field_id') => $value,
                    getConfigValue('menu_field_sort') => $key,
                ];
                //存储数据
                $res = _menuDb()->name($this->table)->update($menuData);
                if ($res === false) {
                    Db::rollback();
                    $this->error('排序失败！');
                }
            }
            Db::commit();
            $this->success('排序成功！');
        } catch (Exception $ex) {
            Db::rollback();
            $this->error($ex->getMessage());
        }

    }

    /**
     * @authName 脚本关闭窗口
     * @author zhangxiny
     * @createTime 2021-11-16 10:32:10
     * @return string
     */
    public function getCloseWinScript()
    {
        return "<script>
                    setTimeout(function(){
                      var win=art.dialog.opener;
                      win.refreshPage();
                      art.dialog.close();
                    },1000);
            </script>";
    }

    /**
     * @authName 关联菜单
     * @author zhangxiny
     * @create_time 2017-10-23 11:43:21
     */
    public function relationMenu()
    {
        Db::startTrans();
        $relationLog = [];   //关联纪录
        $auth_config_id = input('auth_config_id/d');
        //权限编号不存在
        if (empty($auth_config_id)) {
            $this->error('权限编号不存在，请刷新页面重试~');
        }
        try {
            $method_ids = input('ids/s', '', 'trim');  //权限编号组
            //将编号分成数组
            $idArr = explode(',', $method_ids);
            //是否显示
            $is_show_ids = input('is_show_ids/s', '', 'trim');
            $isShowArr = explode(',', $is_show_ids);
            $menu_id = input('menu_id');  //菜单编号
            if (empty($menu_id)) {
                $this->error('请选择菜单！');
            } else if ($menu_id == 'root') {
                $menu_id = 0;
            }
            $authData = Db::name('auth_class')
                ->where(array('id' => array('in', $idArr)))
                ->field('id,auth_name,name,url,module')
                ->select();
            //循环赋值到菜单表
            foreach ($authData as $key => $value) {
                $relationLog[$key]['url'] = $value['url'];
                $menuData = [
                    getConfigValue('menu_field_title') => $value['auth_name'],
                    getConfigValue('menu_field_sort') => 0,
                    getConfigValue('menu_field_url') => $value['url'],
                    getConfigValue('menu_field_module') => $value['module'],
                    getConfigValue('menu_field_pid') => $menu_id,
                ];
                //如果该权限没有说明，则不能关联
                if (empty($value['auth_name'])) {
                    $relationLog[$key]['msg'] = '关联失败，菜单没有标题，请补充！';
                    $relationLog[$key]['status'] = 0;
                    continue;
                }
                $relationLog[$key]['status'] = 1;
                $res = _menuDb()->name($this->table)->insert($menuData);
                if ($res === false) {
                    $relationLog[$key]['msg'] = '关联失败';
                } else {
                    $relationLog[$key]['msg'] = '关联成功';
                }
            }
            Db::commit();
            $this->assign('relation_log', $relationLog);
            $this->assign('type', 'relation');
            return $this->fetch('auth_config/update_msg');
        } catch (Exception $ex) {
            Db::rollback();
            $error_text = $this->getLogText('关联失败', 'relationMenu', $ex->getMessage(), null);
            write_file_for_service('AuthConfig', $error_text);
            $this->error('关联失败！原因:' . $ex->getMessage());
        }
    }

    /**
     * @authName 异步更新控制器
     * @authStatus 1
     * @author zhangxiny
     * @createTime 2017-11-17 12:03:43
     */
    public function updateControllerSelect()
    {
        $auth_config_id = input('auth_config_id');
        //得到配置数据
        $authConfigData = Db::name('auth_config')
            ->where('id', $auth_config_id)
            ->field('module')
            ->find();
        $where['module'] = $authConfigData['module'];
        $where['level'] = 0;
        $selectData = Db::name('auth_class')
            ->where($where)
            ->field('name,id')
            ->select();
        echo json_encode($selectData);
        exit;
    }

    /**
     * @authName 得到日志文本
     * @author zhangxiny
     * @createTime 2021-11-16 10:33:07
     * @param $reason
     * @param $method
     * @param $text
     * @param $details_text
     * @return string
     */
    private function getLogText($reason, $method, $text, $details_text)
    {
        if (empty($details_text)) {
            return "$reason\n方法名：$method\n原因：$text\n";
        }
        return "$reason\n方法名：$method\n原因：$text\n详细原因：$details_text\n";
    }

    /**
     * @authName 刷新该module的控制器
     * @authStatus 1
     * @author zhangxiny
     * @createTime 2021-11-16 10:33:22
     */
    public function refSelectController()
    {
        $auth_config_id = input('auth_config_id/d');
        if (empty($auth_config_id)) {
            $this->error('权限编码不存在，请刷新页面重试~');
        }
        //由权限编码得到配置数据
        $authConfigData = Db::name('auth_config')
            ->where('id', $auth_config_id)
            ->field('module')
            ->find();
        $where['module'] = $authConfigData['module'];
        $where['level'] = 0;
        //得到该配置的菜单数据
        $selectData = Db::name('auth_class')
            ->where($where)
            ->field('name,id,auth_name')
            ->select();
        echo json_encode($selectData);
        exit;
    }

    /**
     * @authName 删除模块
     * @author zhangxiny
     * @createTime 2021-08-19 16:49:09
     */
    public function deleteAuthConfig()
    {
        $id = input('id/d');
        if (empty($id)) {
            $this->error('请传入ID');
        }
        Db::name('auth_config')->where('id', $id)->delete();
        $this->success('删除成功！');
    }

    /**
     * @authName 得到导出sql
     * @author zhangxiny
     * @createTime 2021-08-20 10:26:17
     */
    public function exportSql()
    {
        $menuArr = _menuDb()->name($this->table)->select();
        $sql = '';
        foreach ($menuArr as $index => $item) {
            $sql .= $this->get_insert_sql($this->table, $item);
        }
        $fileName = $this->write_sql_file($sql);
        $filename = $fileName;
        $filesize = filesize($filename);
        header("Content-Type:   application/force-download ");
        header("Content-Disposition:   attachment;   filename= " . basename($filename));
        header("Content-Length:   " . $filesize);
        $data = file_get_contents($filename);
        echo $data;
    }

    /**
     * @authName 写入sql文件
     * @author zhangxiny
     * @createTime 2021-08-20 10:26:24
     * @param $content
     * @return string
     */
    function write_sql_file($content)
    {
        $log_path = dirname($_SERVER['DOCUMENT_ROOT']) . '/runtime/export_sql/';
        //这里需要创建目录
        //并给予权限
        if (!is_dir($log_path)) {
            mkdir($log_path, 0777, true);
            chmod($log_path, 0777); //设置目录权限为0777
        }
        $time = time();
        $filename = $log_path . $time . '.sql';
        $Ts = fopen($filename, "a");
        fwrite($Ts, $content);
        fclose($Ts);
        return $filename;
    }

    /**
     * @authName 格式化sql
     * @author zhangxiny
     * @createTime 2021-08-20 10:26:52
     * @param $table
     * @param $row
     * @return string
     */
    private function get_insert_sql($table, $row)
    {
        $sql = "INSERT INTO `{$table}` VALUES (";
        $values = array();
        foreach ($row as $value) {
            $values[] = "'" . ($value) . "'";
        }
        $sql .= implode(', ', $values) . ");\n";
        return $sql;
    }

}
