<?php

namespace app\index\controller;

use think\Db;
use think\Loader;
use think\Request;
use think\Config;

/**
 * @authName 权限菜单管理
 * @authStatus 1
 * @author zhangxiny
 * @createTime 2017-11-15 17:23:36
 *
 * Class MenuAuth
 * @package app\index\controller
 */
class MenuAuth extends Base
{

    private $menuModules;
    private $table = '';

    /**
     * @authName 初始化
     * @authStatus 0
     * @author zhangxiny
     * @createTime 2017-11-15 17:23:52
     *
     */
    protected function _initialize()
    {
        parent::_initialize();
        //查出所有端的配置信息
        $authConfigArr = Db::name('auth_config')
            ->field('module,name')
            ->select();
        foreach ($authConfigArr as $key => $val) {
            $this->menuModules[$val['module']] = $val['name'];
        }
        $this->table = getConfigValue('menu_table');
    }

    /**
     * @authName 权限菜单名称
     * @authStatus 1
     * @author zhangxiny
     * @createTime 2017-11-15 17:24:53
     * @return mixed
     */
    public function edit_menu()
    {
        $request = Request::instance();
        $menu_id = $request->param('menu_id', 0, 'intval');
        $auth_config_id = input('auth_config_id');
        if (empty($menu_id)) {
            $this->error('参数错误');
            exit;
        } else if (empty($auth_config_id)) {
            $this->error('权限配置编号不存在');
        }
        //查出所属端，模块名
        $authConfigData = Db::name('auth_config')
            ->where(array('id' => $auth_config_id))
            ->field('module')
            ->find();
        if ($request->isPost()) {
            $parent_menu_id = _menuDb()->name($this->table)->where('id', $menu_id)->value('pid');
            $data = [
                getConfigValue('menu_field_title') => $request->param('title', '', 'trim'),
                getConfigValue('menu_field_sort') => $request->param('sort', 0, 'intval'),
                getConfigValue('menu_field_url') => $request->param('menu_url', '', 'trim'),
                getConfigValue('menu_field_module') => $authConfigData['module'],
                getConfigValue('menu_field_pid') => $parent_menu_id,
            ];
            $validate = Loader::validate('Menu');
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            $updateRes = _menuDb()->name($this->table)->where('id', $menu_id)->update($data);
            if ($updateRes !== false) {
                $exe_script_str =
                    "<script>
                        setTimeout(function(){
                          var win=art.dialog.opener;
                          win.refreshParsonNode('edit');
                          art.dialog.close();
                        },1000);
                </script>";
                $this->success('操作成功' . $exe_script_str);
            } else {
                $this->error('操作失败');
            }
        }
        /* 获取数据 */
        $menu = _menuDb()->name($this->table)->where('id', $menu_id)->find();
        if ($menu['pid'] == 0) {
            $parent_menu_name = '顶级菜单';
        } else {
            //得到父级菜单名
            $parent_menu_name = _menuDb()->name($this->table)->where('id', $menu['pid'])->value('title');
        }
        $menu = [
            'title' => $menu[getConfigValue('menu_field_title')],
            'sort' => $menu[getConfigValue('menu_field_sort')],
            'url' => $menu[getConfigValue('menu_field_url')],
            'pid' => $menu[getConfigValue('menu_field_pid')],
            'id' => $menu[getConfigValue('menu_field_id')],
        ];
        $this->assign('parent_menu_name', $parent_menu_name);
        $this->assign('parent_menu_id', $menu['pid']);
        $this->assign('menu', $menu);
        $this->assign('auth_config_id', $auth_config_id);
        $this->assign('module_name', $authConfigData['module']);
        return $this->fetch('menu_auth/add_or_edit');
    }

    /**
     * @authName 新增菜单
     * @authStatus 1
     * @author zhangxiny
     * @createTime 2017-11-15 17:25:05
     * @return mixed
     */
    public function add_menu()
    {
        $parent_menu_id = input('parent_menu_id');
        $auth_config_id = input('auth_config_id');
        if (empty($auth_config_id)) {
            $this->error('权限配置编号不存在');
        } else if (empty($parent_menu_id)) {
            $this->error('父级菜单不存在');
        }
        //查出所属端，模块名
        $authConfigData = Db::name('auth_config')
            ->where(array('id' => $auth_config_id))
            ->field('module')
            ->find();
        $request = Request::instance();
        if ($request->isPost()) {
            if ($parent_menu_id == 'root') {
                $parent_menu_id = 0;
            }
            $data = [
                getConfigValue('menu_field_title') => $request->param('title', '', 'trim'),
                getConfigValue('menu_field_sort') => $request->param('sort', 0, 'intval'),
                getConfigValue('menu_field_url') => $request->param('menu_url', '', 'trim'),
                getConfigValue('menu_field_module') => $authConfigData['module'],
                getConfigValue('menu_field_pid') => $parent_menu_id,
            ];
            $validate = Loader::validate('Menu');
            if ($validate->check($data)) {
                _menuDb()->name($this->table)->insert($data);
                $menuId = _menuDb()->name($this->table)->getLastInsID();
                if ($menuId) {
                    $exe_script_str =
                        "<script>
                            setTimeout(function(){
                                var win=art.dialog.opener;
                                win.refreshParsonNode();
                                art.dialog.close();
                            },1000);
                    </script>";
                    $this->success('操作成功' . $exe_script_str);
                } else {
                    $this->error('操作失败');
                }
            } else {
                $this->error($validate->getError());
            }
            exit;
        }
        $menusWhere['module'] = $authConfigData['module'];
        //如果是新增一级菜单
        if ($parent_menu_id == 'root') {
            $menu_name = '顶级菜单';
        } else {
            //得到父级菜单名称
            $menu_name = _menuDb()->name($this->table)
                ->where(getConfigValue('menu_field_id'), $parent_menu_id)
                ->value(getConfigValue('menu_field_title'));
        }
        $menu = [
            'title' => '',
            'sort' => '',
            'url' => '',
            'pid' => $parent_menu_id,
            'id' => '',
        ];
        $this->assign('menu', $menu);
        //父级菜单名称
        $this->assign('parent_menu_name', $menu_name);
        //父级菜单编号
        $this->assign('parent_menu_id', $parent_menu_id);
        $this->assign('auth_config_id', $auth_config_id);
        return $this->fetch('menu_auth/add_or_edit');
    }

    /**
     * @authName 删除后台菜单
     * @authStatus 1
     * @author zhangxiny
     * @createTime 2017-11-15 17:25:56
     */
    public function del_menu()
    {
        $request = Request::instance();
        $menu_id = $request->param('menu_id', 0, 'intval');
        if (empty($menu_id)) {
            $this->error('请选择要删除的数据!');
        }
        $menuCount = _menuDb()->name($this->table)
            ->where(getConfigValue('menu_field_pid'), $menu_id)
            ->count();
        if (!empty($menuCount)) {
            $this->error('请先删除子菜单后再操作!');
        }
        $delResult = _menuDb()->name($this->table)
            ->where(getConfigValue('menu_field_id'), $menu_id)
            ->delete();
        if ($delResult !== false) {
            _menuDb()->name($this->table)->where(getConfigValue('menu_field_pid'), $menu_id)->delete();
            $exe_script_str =
                "<script>
                        setTimeout(function(){
                          var win=art.dialog.opener;
                          win.refreshPage();
                        },1000);
                </script>";
            $this->success('操作成功' . $exe_script_str);
        } else {
            $this->error('操作失败');
        }
    }

}
