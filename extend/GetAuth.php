<?php

/**
 * 通过正则表达式获取文件注释
 * Class GetAuth
 * @package app\index\controller
 */
class GetAuth{
    //注释Dom
    private $params = array ();
    private $file_path = '';

    /**
     * 实现思路：
     * 1、先用文件流读取整个文件内容
     * 2、使用正则搜索类名、注释，解析出注释的Dom
     * 3、使用正则搜索所有方法名、方法注释，解析出注释的Dom
     * @author zhangxinyu
     * @auth_name 得到文件类与方法的注释
     */
    public function index($file_path){
        $this->file_path = $file_path;
        //权限数组
        $authArr = [];
        $file_path=iconv('UTF-8','GB2312',$file_path);

        if(!file_exists($file_path)){
            return $authArr;
        }

        $this->read_dirs($file_path, $authArr);

//        write_file_for_service('test',json_encode($authArr));
        return $authArr;
    }

    /**
     * @authName 递归文件
     * @author zxy
     * @createTime 2021-09-09 10:26:45
     * @param $name
     */
    private function read_dirs($file_path, &$authArr) {
        if($handle = opendir($file_path)) {
            while (($filename = readdir($handle))!==false) {
                if ($filename!="." && $filename!="..") {
                    //临时目录名，用来判断是否是子目录
                    $temp_file = $file_path."/".$filename;
                    //如果临时目录是个目录，递归
                    if(is_dir($temp_file)) {
                        $this->read_dirs($temp_file, $authArr);
                    } elseif ($this->get_extension($filename) == 'php') {
                        $this->parseDom($file_path, $filename, $authArr);
                    }
                }
            }
        }
    }

    private function parseDom($file_path, $file, &$authArr){
        $key = count($authArr);
        //指定文件路径
        $con_file_path = $file_path . '\\' . $file;
        if (!$this->isWin()) {
            //指定文件路径
            $con_file_path = $file_path . '/' . $file;
        }
        //如果文件不存在则跳过
        if(!file_exists($con_file_path)){
            return 0;
        }
        $file_obj = fopen($con_file_path,"r");
        //指定读取大小，这里把整个文件内容读取出来
        $file_content = fread($file_obj,filesize($con_file_path));
        //得到类名
        $reg = "#class\s*(.*?)\s*{#";
        preg_match_all($reg , $file_content , $classRes);
        //如果不存在类
        if(empty($classRes[1][0])){
            return 0;
        }
        $className = trim($classRes[1][0]);
        //去掉类名后的接口等字符串
        $number = stripos($className,' ');
        if($number){
            $className = substr($className,0,$number);
        }
        //没有类则跳过
        if(empty($className)){
            return 0;
        }
        //得到类注释
        $reg = "#\<\?php([\S|\s|\d|\D|\w|\W]*)\s*".$classRes[0][0]."#";
        preg_match_all($reg , $file_content , $classComRes);
        $comments = null;
        if(!empty($classComRes[1][0])){
            $comments = $classComRes[1][0];
        }
        //截取到类前面的最后一个注释块
        $number = strripos($comments,'/**');
        $comments = substr($comments,$number,strlen($comments));
        $number = strripos($comments,'*/');
        $comments = substr($comments,0,$number+2);
        $comments = trim($comments);

        //生成注释Dom
        $noteDom = $this->parse($comments);
        $authConfigArr = [getConfigValue('auth_name')];
        if (!empty(getConfigValue('author'))) {
            $authConfigArr[] = getConfigValue('author');
        }
        if (!empty(getConfigValue('create_time'))) {
            $authConfigArr[] = getConfigValue('create_time');
        }
        if (!empty(getConfigValue('auth_status'))) {
            $authConfigArr[] = getConfigValue('auth_status');
        }
        //不为空则取值
        foreach ($authConfigArr as $emptyK => $emptyV){
            $classArr[$emptyV] = '';
            if(!empty($noteDom[$emptyV])){
                $classArr[$emptyV] = trim($noteDom[$emptyV]);
            }
        }
        $prefix = str_replace($this->file_path, "", $file_path);
        $classArr['name'] = $prefix . '/' . $className;
        $authArr[$key]['class'] = $classArr;
        //1、(\s*)忽略空白或换行
        //读取所有函数名
        $reg = "#(public|private|protected|\w*)\s*function\s*([\w]*)\s*\\(#";
        preg_match_all($reg , $file_content , $methods);
        //如果该控制器无方法
        if(empty($methods)){
            $methods[0] = null;
        }
        $methodArr = [];
        foreach($methods[0] as $k => $v){
            //去掉(
            $v = substr($v,0,(strlen($v)-1));
            //得到该方法的前面所有字符串
            $reg = "#\<\?php([\S|\s|\d|\D|\w|\W]*)\s*".$v."\(#";
            preg_match_all($reg , $file_content , $code);
            $frontCode = '';
            if(!empty($code[1][0])){
                $frontCode = $code[1][0];
            }
            $number = strripos($frontCode,'}');
            if(!$number){
                $number = strripos($frontCode,'{');
            }
            //裁切掉多余部分
            $noteDom = substr($frontCode ,$number+1,strlen($frontCode));
            //裁切到/**位置，如果没有，则是未编写
            $number = strripos($noteDom,'/**');
            if(!$number){
                $noteDom = null;
            }else{
                $noteDom = substr($noteDom,$number,strlen($noteDom));
            }
            //生成注释Dom
            $noteDom = $this->parse($noteDom);
            //得到配置
            foreach ($authConfigArr as $index => $item){
                $method[$item] = '';
                if(!empty($noteDom[$item])){
                    $method[$item] = trim($noteDom[$item]);
                }
            }
            $method['modifier'] = $methods[1][$k];  //函数修饰符
            $method['name'] = trim($methods[2][$k]); //函数名称
            //方法名，则添加
            if(!empty($method['name'])){
                $methodArr[$k] = $method;
            }
        }
        $authArr[$key]['methods'] = $methodArr;
        return 1;
    }

    private function isWin(){
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return true;
        }else{
            return false;
        }
    }

    /**
     * @auth_name 得到文件扩展名
     * @author zhangxinyu
     * @param $file 文件名
     * @return bool|string 扩展名
     */
    private function get_extension($file)
    {
        return substr(strrchr($file, '.'), 1);
    }

    /**
     * @auth_name 去掉文件后缀
     * @author zhangxinyu
     * @param $filename 文件名
     * @return string 结果
     */
    private function del_file_suffix($filename){
        $suffix = substr(strrchr($filename, '.'), 1);
        $result = basename($filename,".".$suffix);
        return $result;
    }

    /**
     * @auth_name 扩展方法，将注释解析成数组
     * @author 开源中国
     * @param string $doc 要解析的内容
     * @return array 解析后的数组
     */
    private function parse($doc = '') {
        $this->params = array();
        if ($doc == '') {
            return $this->params;
        }
        // Get the comment
        if (preg_match ( '#^/\*\*(.*)\*/#s', $doc, $comment ) === false)
            return $this->params;
        if(!empty($comment[1])){
            $comment = trim ( $comment [1] );
        }else{
            $comment = null;
        }

        // Get all the lines and strip the * from the first character
        if (preg_match_all ( '#^\s*\*(.*)#m', $comment, $lines ) === false)
            return $this->params;
        $this->parseLines ( $lines [1] );
        return $this->params;
    }

    /**
     * @auth_name 扩展方法
     * @author 开源中国
     * @param $lines
     */
    private function parseLines($lines) {
        $desc = array();
        foreach ( $lines as $line ) {
            $parsedLine = $this->parseLine ( $line ); // Parse the line

            if ($parsedLine === false && ! isset ( $this->params ['description'] )) {
                if (isset ( $desc )) {
                    // Store the first line in the short description
                    $this->params ['description'] = implode ( PHP_EOL, $desc );
                }
                $desc = array ();
            } elseif ($parsedLine !== false) {
                $desc [] = $parsedLine; // Store the line in the long description
            }
        }
        $desc = implode ( ' ', $desc );
        if (! empty ( $desc ))
            $this->params ['long_description'] = $desc;
    }

    /**
     * @auth_name 扩展方法
     * @author 开源中国
     * @param $line
     * @return bool|string
     */
    private function parseLine($line) {
        // trim the whitespace from the line
        $line = trim ( $line );

        if (empty ( $line ))
            return false; // Empty line

        if (strpos ( $line, '@' ) === 0) {
            if (strpos ( $line, ' ' ) > 0) {
                // Get the parameter name
                $param = substr ( $line, 1, strpos ( $line, ' ' ) - 1 );
                $value = substr ( $line, strlen ( $param ) + 2 ); // Get the value
            } else {
                $param = substr ( $line, 1 );
                $value = '';
            }
            // Parse the line and return false if the parameter is valid
            if ($this->setParam ( $param, $value ))
                return false;
        }

        return $line;
    }

    /**
     * @auth_name 扩展方法
     * @author 开源中国
     * @param $param
     * @param $value
     * @return bool
     */
    private function setParam($param, $value) {
        if ($param == 'param' || $param == 'return')
            $value = $this->formatParamOrReturn ( $value );
        if ($param == 'class')
            list ( $param, $value ) = $this->formatClass ( $value );

        if (empty ( $this->params [$param] )) {
            $this->params [$param] = $value;
        } else if ($param == 'param') {
            $arr = array (
                $this->params [$param],
                $value
            );
            $this->params [$param] = $arr;
        } else {
            $this->params [$param] = $value + $this->params [$param];
        }
        return true;
    }

    /**
     * @auth_name 扩展方法
     * @author 开源中国
     * @param $string
     * @return string
     */
    private function formatParamOrReturn($string) {
        $pos = strpos ( $string, ' ' );

        $type = substr ( $string, 0, $pos );
        return '(' . $type . ')' . substr ( $string, $pos + 1 );
    }
}