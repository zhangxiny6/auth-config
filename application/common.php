<?php
use think\Db;

//应用公共文件
//全局通用函数

/**
 * @authName 得到用户ID
 * @return mixed|string
 */
function get_uid()
{
    return is_login();
}

/**
 * @authName 判断用户是否登录
 * @return mixed|string
 */
function is_login()
{
    $uid = session(config('CURR_UID'));
    if (empty($uid)) {
        return '';
    }
    //登录有效时间
    $LOGIN_VALID_TIME = 8 * 60 * 60;
    //登录时间
    $login_start_time = session('login_start_time');
    if(!empty($login_start_time)&&($login_start_time + $LOGIN_VALID_TIME)<=time()){
        return '';
    }
    return $uid;
}

/**
 * @authName 系统密码加密
 * @param $str 要加密的字符串
 * @param string $key
 * @return string
 */
function encrypt_password($str, $key = 'ThinkUCenter')
{
    //获取配置的加密字符串
    $encryptionStr = config('UC_AUTH_KEY') == '' ? $key : config('UC_AUTH_KEY');
    return '' === $str ? '' : md5(sha1($str) . $encryptionStr);
}

/**
 * @authName 写入日志文件
 * @param $file_name
 * @param $content
 */
function write_file_for_service($file_name, $content)
{
    $log_path = dirname($_SERVER['DOCUMENT_ROOT']) . '/runtime/custom_logs/';
    //这里需要创建目录
    //并给予权限
    if (!is_dir($log_path)) {
        mkdir($log_path, 0777, true);
        chmod($log_path, 0777); //设置目录权限为0777
    }
    $filename = $log_path . $file_name . '.txt';
    $Ts       = fopen($filename, "a");
    fwrite($Ts, date('Y-m-d H:i:s', time()) . ": " . $content . "\r\n");
    fclose($Ts);
}

/**
 * @authName 获取表的字段组成一个空数组,添加时使用
 * @param $table_name
 * @return array
 */
function get_empty_column_name($table_name)
{
    $database_name=config('database.database');
    $database_prefix=config('database.prefix');

    $info = array();
    $data = Db::table('INFORMATION_SCHEMA.COLUMNS')->
    where("table_name = '".$database_prefix . $table_name . "' AND table_schema = '".$database_name."'")->field('COLUMN_NAME')->select();
    foreach ($data as $key => $value) {
        $info[$value['COLUMN_NAME']] = '';
    }
    return $info;
}

/**
 * @authName 从当前session里获取用户登录信息
 * @return mixed
 */
function _getUserLoginInfoSession()
{
    //保存SESSION
    return session(config('CURR_USER_INFO')); //用户信息
}

/**
 * @authName 保存登录信息
 * @param $userInfo
 */
function saveLoginInfo($userInfo)
{
    //保存到session里的数据
    $userEntity                    = array();
    $userEntity['username']        = $userInfo['user_name'];
    $userEntity['real_name']        = $userInfo['real_name']; //真实姓名
    $userEntity['user_id']         = $userInfo['id']; //用户id
    //得到权限集合
    session(config('CURR_USER_INFO'), $userEntity); //用户信息
    session(config('CURR_UID'), $userInfo['id']); //uid
    session('login_start_time',time());
    $userEntity = null;
    $userInfo   = null;
}

/**
 * 清除登录信息
 */
function clearLoginInfo()
{
    session(config('CURR_USER_INFO'), null);
    session(config('CURR_UID'), null);
    session('user_openid', null);
}

/**
 * @authName 检查登录是否失效
 * @author zhangxiny
 * @createTime 2020-03-24 18:10:03
 * @qqNumber 2639347794
 */
function checkLoginInvalid($result){
    $request = \think\Request::instance();
    $notCheckArr = ['login/index'];
    //控制器名称
    $controller = $request->controller();
    //操作名称
    $action = $request->action();

    //获取访问的控制器名称和方法名称
    $current_rule = strtolower($controller . '/' . $action);

    if(in_array($current_rule,$notCheckArr)){
        return true;
    }
    if(empty($result)){
        return true;
    }else if($result['code']==40001){
        session(config('CURR_USER_INFO'), null); //用户信息
        session(config('CURR_UID'), null); //uid
        session('[destroy]'); //销毁所有session
        if ($request->isAjax()) {
            echo json_encode(array('code' => 40001,'msg' => '登录失效'));
            exit();
        }else{
            $pre_url = _get_current_view_page_url();
            $loginObj = new \app\index\controller\Login();
            if($current_rule=='index/index'){
                echo $loginObj->index();
                exit();
            }
            echo $loginObj->LoginInvalid($pre_url);
            exit();
        }
    }
}

/**
 * 得到当前浏览的界面链接
 * @param  integer $need_filter 是否需要过滤掉一些分享参数
 * @return [type]               [description]
 */
function _get_current_view_page_url($need_filter = 0)
{
    $Server_Host         = empty($_SERVER['HTTP_HOST']) ? '' : $_SERVER['HTTP_HOST']; //当前地址域名
    $Server_PHP_SELF     = empty($_SERVER['PHP_SELF']) ? '' : $_SERVER['PHP_SELF']; //当前正在执行脚本的文件名
    $Server_QUERY_STRING = empty($_SERVER['QUERY_STRING']) ? '' : $_SERVER['QUERY_STRING']; //获取查询语句，实例中可知，获取的是?后面的值
    $Server_REQUEST_URI  = empty($_SERVER['REQUEST_URI']) ? '' : $_SERVER['REQUEST_URI']; //获取http://yxbmobile.yisees.com后面的值，包括/
    $Server_QUERY_STRING_str = $Server_QUERY_STRING;
    //如果需要过滤参数
    if ($need_filter) {
        if (!empty($Server_QUERY_STRING_str)) {
            $Server_QUERY_STRING_str = "?" . $Server_QUERY_STRING_str;
        }
        $regary = array('openid', 'unionid', 'jssg', 'from', 'isappinstalled', 'login_random'); //清除参数信息
        foreach ($regary as $v) {
            $reg = "/(&|\?)" . $v . "=[^&]*/";
            $Server_QUERY_STRING_str = preg_replace($reg, '', $Server_QUERY_STRING_str);
        }
        //如果没有 ? 则追加
        if ($Server_QUERY_STRING_str && !strstr($Server_QUERY_STRING_str, "?")) {
            $Server_QUERY_STRING_str = "?" . $Server_QUERY_STRING_str;
        }
    }
    $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    $cur_url = $http_type . $Server_Host . $Server_REQUEST_URI;
    return $cur_url;
}

/**
 * @authName 得到系统配置
 * @author zhangxiny
 * @createTime 2021-08-20 10:08:30
 * @param $key
 * @return string
 */
function getConfigValue($key){
    session('error', false);
    $obj = new \app\index\controller\Install();
    $obj->check_dirfile();
    $error = session('error');
    if ($error) {
        return 0;
    }
    $arr = parse_ini_file(dirname($_SERVER['DOCUMENT_ROOT']) . '/config/config.ini');
    if (empty($arr[$key])) {
        return '';
    }
    return $arr[$key];
}

/**
 * @authName 写入系统配置
 * @author zhangxiny
 * @createTime 2021-08-23 15:34:05
 * @param $key
 * @param $value
 */
function writeConfigValue($group, $key, $value) {
    $file = dirname($_SERVER['DOCUMENT_ROOT']) . '/config/config.ini';
    $writeIni = new \WriteIni();
    $writeIni->updateIni($file, $group, $key, $value);
}

/**
 * @authName 返回菜单数据库连接
 * @authStatus 1
 * @createTime 2018-01-06 10:36:06
 * @qqNumber 281952182
 */
function _menuDb()
{
    return Db::connect('menu_db_config');
}