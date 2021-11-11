<?php
/**
 * Created by PhpStorm.
 * member: Administrator
 * Date: 11/01/2017
 * Time: 11:09
 */
namespace  app\index\validate;
use think\Validate;

class Menu extends Validate
{
    /*验证规则*/
    protected $rule = [
        'title'  => 'require',
        'pid' =>  'require',
    ];
    /*验证提示*/
    protected $message  =   [
        'title.require' => '菜单标题不能为空',
        'pid.require'   => '上级菜单必须选择',
    ];

}