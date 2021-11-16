<?php

namespace app\index\controller;

use think\Db;

/**
 * @authName 首页
 * @authStatus 1
 * @author zhangxiny
 * @createTime 2017-11-16 16:01:28
 * @qqNumber 2639347794
 *
 * Class Index
 * @package app\index\controller
 */
class Index extends Base
{


    /**
     * @authName 首页
     * @authStatus 1
     * @author zhangxiny
     * @createTime 2017-11-16 16:01:53
     * @qqNumber 2639347794
     *
     * @return mixed
     */
    public function index()
    {
        $userInfo = _getUserLoginInfoSession();
        $this->assign('userInfo', $userInfo);
        return $this->fetch();
    }
}
