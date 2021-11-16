<?php
/**
 * Created by PhpStorm.
 * member: Administrator
 * Date: 10/01/2017
 * Time: 21:43
 */

namespace app\index\controller;

use think\Controller;
use think\Db;

/**
 * @authName 登录页面
 * @authStatus 1
 * @author zhangxiny
 * @createTime 2017-11-16 16:30:41
 * @qqNumber 2639347794
 *
 * Class Login
 * @package app\index\controller
 */
class Login extends Controller
{
    /**
     * @authName 系统登录
     * @authStatus 1
     * @author zhangxiny
     * @createTime 2017-11-16 16:30:48
     * @qqNumber 2639347794
     *
     * @return mixed
     */
    public function index()
    {
        //当前模块
        define('CURMODULE', "large_area");
        //支持的浏览器
        $supportBrowserArr = ['Safari', 'Chrome', 'Firefox'];
        //是否支持
        $is_support = true;
        foreach ($supportBrowserArr as $index => $item) {
            //判断浏览器内核如果不是谷歌和火狐则跳转到提醒界面
            if (strpos($_SERVER['HTTP_USER_AGENT'], $item)) {
                break;
            }
            //不支持
            $is_support = false;
        }
        if (!$is_support) {
            $this->assign('version', config('version.fileversion'));
            //静态资源域名
            $this->assign('staticsResourceUrl', config('staticsResourceUrl'));
            //静态资源域名
            $this->assign('commonStaticsResourceUrl', config('commonStaticsResourceUrl'));
            return $this->fetch('browser_prompt/index');
        }
        if (request()->isAjax()) {
            $loginMode = input("loginMode/d"); //1登录帐号 2验证码登录

            //获取用户登录帐号
            if ($loginMode == 1) {
                $username = input("username1/s", '', 'trim'); //登录帐号
                $password = input("password/s"); //密码
                $captcha = input('validation', '', 'trim');//得到验证码
                if (empty($captcha)) {
                    $this->error('验证码不能为空！');
                } else if (!captcha_check($captcha)) {
                    $this->error('验证码错误');
                };
                if (empty($password)) {
                    $this->error('请填写密码');
                }
                $reqData['username'] = $username;
                $reqData['password'] = $password;
            } else if ($loginMode == 2) {
                $verifyCode = input("verifyCode", '', 'trim'); //验证码
                $username = input("username2/s", '', 'trim'); //密码（验证码）
                if (empty($username)) {
                    $this->error('请输入用户名');
                }
                if (empty($verifyCode)) {
                    $this->error('请输入验证码');
                }
                $reqData['username'] = $username;
                $reqData['verifyCode'] = $verifyCode;
                $session_send_mobile = session('send_pc');
                $session_verifyCode = session('mobile_code');
                if (($session_send_mobile != $username) || ($session_verifyCode != $verifyCode)) {
                    $this->error('手机验证码错误');
                }
            } else {
                $this->error("当前登录已失效，请重新登录");
            }
            $reqData['username'] = $username;
            $reqData['loginMode'] = $loginMode;
            $reqData['source'] = 0; //获取当前访问终端类别
            $reqData['page_title'] = input('page_title/s', '');
            $loginLogData = Db::name('manage_users')->where([
                'user_name' => $reqData['username'],
                'password' => encrypt_password($reqData['password'])
            ])->find();

            if (!empty($loginLogData)) {
                //登录成功 保存登录信息
                saveLoginInfo($loginLogData);
                /*返回链接，跳转页面*/
                $this->success('', url('index/index'));
            } else {
                $this->error('账号或密码错误');
            }
        } else {
            if (is_login()) {
                $this->redirect('index/index');
                exit;
            } else {
                //全局站点标题
                $WEB_SITE_TITLE = '';
                $this->assign('WEB_SITE_TITLE', $WEB_SITE_TITLE);
                //获取地址栏存储的用户名
                $username = input('param.username/s');
                $domain_name = input('server.SERVER_NAME');
                $this->assign('username', $username);
                $this->assign('domain_name', $domain_name);
                //文件缓存版本
                $this->assign('version', config('version.fileversion'));
                //静态资源域名
                $this->assign('staticsResourceUrl', config('staticsResourceUrl'));
                //静态资源域名
                $this->assign('commonStaticsResourceUrl', config('commonStaticsResourceUrl'));
                return $this->fetch();
            }
        }
    }

    /**
     * @authName 注销登录用户
     * @authStatus 1
     * @author zhangxiny
     * @createTime 2017-11-16 16:31:05
     * @qqNumber 2639347794
     */
    public function logout()
    {
        //判断用户是否勾选记住用户名按钮
        $checkStatus = session('check_status'); //登录用户名
        $username = '';
        if (!empty($checkStatus)) {
            $userInfo = session(config('CURR_USER_INFO'));
            $username = $userInfo['username'];
        }
        session(config('CURR_USER_INFO'), null); //用户信息
        session(config('CURR_UID'), null); //uid
        session('[destroy]'); //销毁所有session
        //重定向到登录页面
        $this->redirect('Login/index', ['username' => $username]);
    }

    /**
     * 浏览器版本过低的提醒html代码
     * @authName 浏览器版本过低的提醒html代码
     * @authStatus 0
     * @author zhangxiny
     * @createTime 2017-11-16 16:31:25
     * @qqNumber 2639347794
     *
     * @return mixed
     */
    public function brower_low()
    {
        //文件缓存版本
        $this->assign('version', config('version.fileversion'));
        return $this->fetch();

    }

    /**
     * @authName 登录(简单版)
     * @author zhangxiny
     * @createTime 2020-03-24 17:02:06
     * @qqNumber 2639347794
     */
    public function simpleIndex()
    {
        //全局站点标题
        $WEB_SITE_TITLE = '';

        $this->assign('WEB_SITE_TITLE', $WEB_SITE_TITLE);
        //获取地址栏存储的用户名
        $username = input('param.username/s');
        $this->assign('username', $username);

        //文件缓存版本
        $this->assign('version', config('version.fileversion'));
        //静态资源域名
        $this->assign('staticsResourceUrl', config('staticsResourceUrl'));
        //静态资源域名
        $this->assign('commonStaticsResourceUrl', config('commonStaticsResourceUrl'));
        $this->assign('is_ajax', input('is_ajax'));
        return $this->fetch('login/simple_index');
    }

    public function LoginInvalid($url = '')
    {
        //文件缓存版本
        $this->assign('version', config('version.fileversion'));
        //静态资源域名
        $this->assign('staticsResourceUrl', config('staticsResourceUrl'));
        //静态资源域名
        $this->assign('commonStaticsResourceUrl', config('commonStaticsResourceUrl'));
        $returnUrl = session('returnUrl');
        if (!empty($url)) {
            $returnUrl = $url;
        }
        $this->assign('returnUrl', $returnUrl);
        return $this->fetch('login/login_invalid');
    }
}
