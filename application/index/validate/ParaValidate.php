<?php


namespace app\index\validate;

use think\Validate;

class ParaValidate extends Validate
{
    //系统信息验证规则
    public static $create_db_rule = [
        'db_hostname|数据库服务器' => 'require',
        'db_username|数据库用户名' => 'require',
        'db_password|数据库密码' => 'require',
        'db_hostport|数据库端口' => 'require',
        'u_username|管理员账号' => 'require',
        'u_password|管理员密码' => 'require|confirm:u_confirm_password',
        'u_confirm_password|管理员密码' => 'require',
    ];
}