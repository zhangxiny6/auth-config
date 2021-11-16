<?php


namespace app\index\controller;


use app\index\validate\ParaValidate;
use think\Controller;
use think\Db;
use think\Exception;

/**
 * @authName 安装
 * @author zxy
 * @createTime 2021-08-20 10:48:22
 *
 * Class Install
 * @package app\index\controller
 */
class Install extends Controller
{

    /**
     * @authName 安装
     * @author zxy
     * @createTime 2021-08-20 10:48:16
     */
    public function index()
    {
        //文件缓存版本
        $this->assign('version', config('version.fileversion'));
        $this->assign('WEB_SITE_TITLE', '权限管理系统安装');
        $this->assign('value', input('value/d', 1));
        session('error', false);
        $func = $this->check_func();
        $env = $this->check_env();
        $dirfile = $this->check_dirfile();
        $runtimeCheckArr = [];
        $runtimeCheckArr[] = [
            'title' => '环境检查',
            'value' => $env
        ];
        $runtimeCheckArr[] = [
            'title' => '依赖性',
            'value' => $func
        ];
        $runtimeCheckArr[] = [
            'title' => '目录、文件权限检查',
            'value' => $dirfile
        ];
        $this->assign('runtimeCheckArr', $runtimeCheckArr);
        echo $this->fetch('index');
    }

    /**
     * @authName 创建数据库
     * @author zxy
     * @createTime 2021-08-23 15:11:05
     * @qqNumber 2639347794
     */
    public function createDb()
    {
        //验证参数
        $validate = new ParaValidate();
        if (!$validate->check(input(), $validate::$create_db_rule)) {
            echo json_encode(['code' => 400, 'msg' => $validate->getError()]);
            exit;
        }
        try {
            $config = [
                'type'     => 'mysql',
                'username' => input('db_username'),
                'password' => input('db_password'),
                'hostname' => input('db_hostname'),
                'hostport' => input('db_hostport'),
                'database' => ''
            ];
            $db = Db::connect($config, false);
            $db->execute('CREATE DATABASE IF NOT EXISTS auth_manage_db DEFAULT CHARSET utf8 COLLATE utf8_general_ci;');
            $config['database'] = 'auth_manage_db';
            $db = Db::connect($config, false);
            $path = dirname($_SERVER['DOCUMENT_ROOT']) . '/config/init.sql';
            $_sql = file_get_contents($path);
            $_arr = explode(';', $_sql);
            foreach ($_arr as $index => $item) {
                if (!empty($item)) {
                    $db->execute($item);
                }
            }
        }catch (\Exception $ex){
            echo json_encode(['code'=>400, 'msg' => '数据库错误：' . $ex->getMessage()]);exit;
        }
        $u_username = input('u_username');
        $u_password = input('u_password');
        //创建用户
        $db->table('am_manage_users')->insert([
            'real_name' => '管理员',
            'user_name' => $u_username,
            'password' => encrypt_password($u_password)
        ]);
        //得到所有数据库
        $dbArr = $db->query("SELECT SCHEMA_NAME AS `Database` FROM INFORMATION_SCHEMA.SCHEMATA;");
        $data = [
            'dbArr' => $dbArr
        ];
        writeConfigValue('db', 'db_hostname', $config['hostname']);
        writeConfigValue('db', 'db_username', $config['username']);
        writeConfigValue('db', 'db_password', $config['password']);
        writeConfigValue('db', 'db_hostport', $config['hostport']);
        writeConfigValue('db', 'db_database', 'auth_manage_db');
        writeConfigValue('db', 'db_type', 'mysql');
        echo json_encode(['code' => 200, 'msg' => '', 'data' => $data]);exit;
    }

    /**
     * @authName 创建权限信息
     * @author zxy
     * @createTime 2021-08-25 11:51:39
     */
    public function createAuth()
    {
        $menu_database = input('controller_db');
        $controller_menu = input('controller_menu');
        $menu_title = input('menu_title');
        $menu_url = input('menu_url');
        $menu_sort = input('menu_sort');
        $menu_pid = input('menu_pid');
        $menu_id = input('menu_id');
        $menu_module = input('menu_module/s', 'default');
        $auth_authName = input('auth_authName');
        $auth_author = input('auth_author');
        $auth_createTime = input('auth_createTime');
        $auth_authStatus = input('auth_authStatus');
        writeConfigValue('menu', 'menu_database', $menu_database);
        writeConfigValue('menu', 'menu_table', $controller_menu);
        writeConfigValue('menu', 'menu_field_title', $menu_title);
        writeConfigValue('menu', 'menu_field_url', $menu_url);
        writeConfigValue('menu', 'menu_field_sort', $menu_sort);
        writeConfigValue('menu', 'menu_field_pid', $menu_pid);
        writeConfigValue('menu', 'menu_field_module', $menu_module);
        writeConfigValue('menu', 'menu_field_id', $menu_id);
        writeConfigValue('auth', 'auth_name', $auth_authName);
        writeConfigValue('auth', 'author', $auth_author);
        writeConfigValue('auth', 'create_time', $auth_createTime);
        writeConfigValue('auth', 'auth_status', $auth_authStatus);
        writeConfigValue('install', 'is_install', 1);
        $log_path = dirname($_SERVER['DOCUMENT_ROOT']) . '/config/';
        //创建lock文件
        $Ts = fopen($log_path . 'install.lock', "a");
        fwrite($Ts, 1);
        fclose($Ts);
        echo json_encode(['code' => 200, 'msg' => '']);exit;
    }

    /**
     * @authName 得到数据库根据库
     * @author zxy
     * @createTime 2021-08-25 10:40:15
     */
    public function getTablesByDb()
    {
        $db = input('db');
        $db = addslashes($db);
        $tableArr = Db::query("SELECT TABLE_NAME FROM information_schema. TABLES WHERE table_schema = '" . $db . "'");
        echo json_encode(['code' => 200, 'msg' => '', 'data' => $tableArr]);exit;
    }

    /**
     * @authName 得到字段名根据表
     * @author zxy
     * @createTime 2021-08-25 11:15:24
     */
    public function getFieldsByTable()
    {
        $table = input('table');
        $db = input('db');
        $table = addslashes($table);
        $tableArr = Db::query("select COLUMN_NAME,COLUMN_COMMENT from information_schema.COLUMNS where table_name = '". $table ."'  and table_schema = '" . $db . "';");
        echo json_encode(['code' => 200, 'msg' => '', 'data' => $tableArr]);exit;
    }

    /**
     * 目录，文件读写检测
     * @return array 检测数据
     */
    public function check_dirfile(){
        $items = array(
            array('/config', '可写', 'success', 'dir'),
            array('/runtime', '可写', 'success', 'dir'),
        );
        foreach ($items as &$val) {
            $item =	dirname($_SERVER['DOCUMENT_ROOT']) . $val[0];
            write_file_for_service('test', dirname($_SERVER['DOCUMENT_ROOT']) . $val[0]);
            if('dir' == $val[3]){
                if(!is_writable($item)) {
                    $val[1] = '无权限';
                    $val[2] = 'error';
                    session('error', true);
                }
            } else {
                if(file_exists($item)) {
                    if(!is_writable($item)) {
                        $val[1] = '不可写';
                        $val[2] = 'error';
                        session('error', true);
                    }
                } else {
                    if(!is_writable(dirname($item))) {
                        $val[1] = '不存在';
                        $val[2] = 'error';
                        session('error', true);
                    }
                }
            }
        }
        foreach ($items as $index => $item) {
            unset($items[$index][3]);
        }
        return $items;
    }

    /**
     * 系统环境检测
     * @return array 系统环境数据
     */
    private function check_env(){
        $items = array(
//            'os'      => array('操作系统', '不限制', '类Unix', PHP_OS, 'success'),
            'php'     => array('PHP版本', '5.4', '5.4+', PHP_VERSION, 'success'),
        );

        //PHP环境检测
        if($items['php'][3] < $items['php'][1]){
            $items['php'][4] = 'error';
            session('error', true);
        }
        foreach ($items as $index => $item) {
            unset($items[$index][2]);
        }

        return $items;
    }

    /**
     * 函数检测
     * @return array 检测数据
     */
    private function check_func(){
        $items = array(
            array('pdo','支持','类','success'),
            array('pdo_mysql','支持','模块','success'),
        );

        foreach ($items as &$val) {
            if(('类'==$val[2] && !class_exists($val[0]))
                || ('模块'==$val[2] && !extension_loaded($val[0]))
                || ('函数'==$val[2] && !function_exists($val[0]))
            ){
                $val[1] = '不支持';
                $val[3] = 'error';
                session('error', true);
            }
        }

        return $items;
    }
}