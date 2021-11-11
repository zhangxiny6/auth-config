<?php
namespace app\index\controller;

use think\Config;
use think\Controller;
use think\Db;
use think\Page;
use think\Request;
use think\Session;

/**
 * 后台通用控制器
 * @authName 后台通用控制器
 * @authStatus 1
 * @author zxy
 * @createTime 2017-11-16 14:48:50
 * @qqNumber 2639347794
 *
 * Class Base
 * @package app\index\controller
 */
class Base extends Controller
{
    public $dbPrefix = '';
    public $currentViewType =0;
    public $currentViewTitle ='';


    /**
     * 控制器里的方法出错，统一入口
     * @authName 控制器里的方法出错，统一入口
     * @authStatus 1
     * @author zxy
     * @createTime 2017-11-16 14:49:04
     * @qqNumber 2639347794
     *
     * @return mixed
     */
    public function _empty()
    {
        //展示404界面
       return $this->fetch('base/404');
    }

    /**
     * 初始化
     * @authName 初始化
     * @authStatus 1
     * @author zxy
     * @createTime 2017-11-16 14:49:21
     * @qqNumber 2639347794
     *
     * @return bool
     */
    protected function _initialize()
    {
        //是否安装
        if (!is_file(dirname($_SERVER['DOCUMENT_ROOT']) .'/config/install.lock')) {
            //跳转到安装页面
            $this->redirect('Install/index');
        }
        //判断是否登录，并定义用户ID常量
        $uid = is_login();
        $request   = Request::instance();
        if (empty($uid)) {
            //控制器名称
            $controller = $request->controller();
            //操作名称
            $action = $request->action();
            //获取访问的控制器名称和方法名称
            $current_rule = strtolower($controller . '/' . $action);
            if($current_rule=='index/index'){
                //跳转到登录界面
                $this->redirect('Login/index');
            }else{
                if ($request->isAjax()) {
                    echo json_encode(array('code' => 40001,'msg' => '登录失效'));exit();
                }else{
                    $this->assign('url', $current_rule);
                    $pre_url = _get_current_view_page_url();
                    session('returnUrl',$pre_url);
//                    $login_url = url('Login/loginInvalid');
//                    //跳转到登录界面
//                    $this->redirect($login_url);
                }
            }
        }
        $this->assign('WEB_SITE_TITLE', '权限管理系统');
        //定义DB数据库的前缀zhjq
        define('TAB_PF', config('database.prefix'));
        //设置用户登录标识
        define('UID', $uid);
        $this->assign('currentViewType', $this->currentViewType);
        $this->assign('currentViewTitle', $this->currentViewTitle);
        //文件缓存版本
        $this->assign('version', config('version.fileversion'));
        return true;
    }

    /**
     * 检查权限
     * @authName 检查权限
     * @authStatus 0
     * @author zxy
     * @createTime 2017-11-16 14:50:53
     * @qqNumber 2639347794
     *
     * @param $uid 认证用户的id
     * @return bool  通过验证返回true;失败返回false
     */
    public function check($uid)
    {
        $request = Request::instance();
        //模块名称
        $module = $request->module();
        //控制器名称
        $controller = $request->controller();
        //操作名称
        $action = $request->action();

        //获取访问的控制器名称和方法名称
        $current_rule            = strtolower($controller . '/' . $action);
        $current_controller_rule = strtolower("{$controller}/*");

        $flag = true; //授权标志 默认通过 true

        //直接通过验证的模块，不需要验证
        $pass_auth_module = Config::get('NOT_AUTH_MODULE');
        //print_r($pass_auth_module); die;
        foreach ($pass_auth_module as $key => $val) {
            $auth_module = strtolower($val);
            if (strpos($current_controller_rule, $auth_module) !== false || strpos($current_rule, $auth_module) !== false || $auth_module === '*') {
                //首先验证是否访问的当前控制器所有模块通过
                //后验证模块
                return true;
                break;
            }
        }

        //获取登录用户的访问权限【接口返回已经是小写】
        $auth_rules = session(config('SESSION_AUTH_MENUS'));
        $flag           = in_array($current_rule, $auth_rules); //存在返回true ,不存在返回false
        return $flag;
    }

    /**
     * 根据用户id获取用户组,返回值为数组
     * ('uid'=>'用户id','group_id'=>'用户组id','title'=>'用户组名称','rules'=>'用户组拥有的规则id,多个,号隔开'),
     * @authName 根据用户id获取用户组,返回值为数组
     * @authStatus 0
     * @author zxy
     * @createTime 2017-11-16 14:51:35
     * @qqNumber 2639347794
     *
     * @param $uid 用户id
     * @return mixed 用户所属的用户组
     */
    public function getGroups($uid)
    {
        static $groups = [];
        if (isset($groups[$uid])) {
            return $groups[$uid];
        }
        $user_groups = Db::view($this->config['auth_group_access'], 'uid,group_id')->view($this->config['auth_group'], 'title,rules', "{$this->config['auth_group_access']}.group_id={$this->config['auth_group']}.id")
            ->where(['uid' => $uid, 'status' => 1])->select();
        $groups[$uid] = $user_groups ? $user_groups : [];
        return $groups[$uid];
    }

    /**
     * 获得权限列表
     * @authName 获得权限列表
     * @authStatus 0
     * @author zxy
     * @createTime 2017-11-16 14:52:28
     * @qqNumber 2639347794
     *
     * @param $uid
     * @param $type
     * @return array|mixed
     */
    protected function getAuthList($uid, $type)
    {
        static $_authList = []; //保存用户验证通过的权限列表
        $t                = implode(',', (array) $type);
        if (isset($_authList[$uid . $t])) {
            return $_authList[$uid . $t];
        }
        if ($this->config['auth_type'] == 2 && Session::has('_auth_list_' . $uid . $t)) {
            return Session::get('_auth_list_' . $uid . $t);
        }
        //读取用户所属用户组
        $groups = $this->getGroups($uid);
        $ids    = []; //保存用户所属用户组设置的所有权限规则id
        foreach ($groups as $g) {
            $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
        }
        $ids = array_unique($ids);
        if (empty($ids)) {
            $_authList[$uid . $t] = [];
            return [];
        }
        $map = [
            'id'     => ['in', $ids],
            'type'   => $type,
            'status' => 1,
        ];
        //读取用户组所有权限规则
        $rules = Db::name($this->config['auth_rule'])->where($map)->field('condition,name')->select();
        //循环规则，判断结果。
        $authList = []; //
        foreach ($rules as $rule) {
            if (!empty($rule['condition'])) {
                //根据condition进行验证
                $this->getUserInfo($uid); //获取用户信息,一维数组
                $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
                @(eval('$condition=(' . $command . ');'));
                $condition && $authList[] = strtolower($rule['name']);
            } else {
                $authList[] = strtolower($rule['name']); //只要存在就记录
            }
        }
        $_authList[$uid . $t] = $authList;
        if ($this->config['auth_type'] == 2) {
            $_SESSION['_auth_list_' . $uid . $t] = $authList; //规则列表结果保存到session
        }

        return array_unique($authList);
    }

    /**
     * 测试页面
     * @authName 测试页面
     * @authStatus 0
     * @author zxy
     * @createTime 2017-11-16 14:52:54
     * @qqNumber 2639347794
     *
     * @return mixed
     */
    public function test()
    {
        return $this->fetch();
    }

    /**
     * 公共查询数据方法1
     * @authName 公共查询数据方法1
     * @authStatus 0
     * @author zxy
     * @createTime 2017-11-16 14:53:03
     * @qqNumber 2639347794
     *
     * @param $modelStr 模型名称（表名称）
     * @param $_where_order_field （条件）
     * @param bool $isReturnResult 是否返回结果
     * @param string $count （总数）
     * @return array
     */
    public function _getPageResultFromModel($modelStr, $_where_order_field, $isReturnResult = false, $count = 'count(*) as tp_count')
    {
        //定义变量
        $where  = array();
        $alias  = '';
        $field  = true;
        $having = '';
        $join   = array();
        $group  = '';
        //变量赋值
        if (isset($_where_order_field['where'])) {
            $where = $_where_order_field['where'];
        }
        if (isset($_where_order_field['field'])) {
            $field = $_where_order_field['field'];
        }
        if (isset($_where_order_field['order'])) {
            $order = $_where_order_field['order'];
        }
        if (isset($_where_order_field['join'])) {
            $join = $_where_order_field['join'];
        }
        if (isset($_where_order_field['having'])) {
            $having = $_where_order_field['having'];
        }
        if (isset($_where_order_field['alias'])) {
            $alias = $_where_order_field['alias'];
        }
        if (isset($_where_order_field['group'])) {
            $group = $_where_order_field['group'];
        }
        //判断model的类型
        $model = $modelStr;
        if (is_string($model)) {
            $model = db($model);

        }

        //获取总个数
        $count_ = $model->alias($alias)
            ->where($where)
            ->join($join)
            ->order($order)
            ->field($count)
            ->having($having)->find();

        //总条数
        $sum = $count_['tp_count'];
        //当前页
        $currPage = input('page', 1);
        if (is_numeric($currPage) && $currPage == 0) {
            $currPage = 1;
        }
        //每页分页的行数
        $rows = input('rows', 20);
        //总页数
        $pages = ceil($sum / $rows);
        if ($pages == 1) {
            $currPage = 1;
        }
        $limit = (($currPage - 1) * $rows) . ',' . $rows;
        //查询数据集合
        $lists = $model->alias($alias)
            ->where($where)
            ->join($join)
            ->field($field)
            ->order($order)
            ->group($group)->having($having)
            ->limit($limit)->select();
        //var_dump(Db::name($model)->getLastSql());die;
//        return dump($lists);
        //组装数据集
        $this->assign("data", $lists);
        $page_data = array(
            'totalPages'       => $sum,
            'sumpage'          => $pages,
            'pageindex'        => $currPage,
            'rowsize'          => $rows,
            'currPage'         => $currPage,
            'data'             => $lists,
        );
        if ($isReturnResult) {
            return $page_data;
        } else {
            echo json_encode($page_data);
        }
    }

    /**
     * 公共分组查询数据2
     * @authName 公共分组查询数据2
     * @authStatus 0
     * @author zxy
     * @createTime 2017-11-16 14:53:41
     * @qqNumber 2639347794
     *
     * @param $modelStr 模型名称（表名称）
     * @param $_where_order_field （条件）
     * @param bool $isReturnResult 是否返回结果
     * @param string $count （总数）
     * @return array
     */
    public function _getPageGroupFromModel($modelStr, $_where_order_field, $isReturnResult = false, $count = 'count(*) as tp_count')
    {
        //定义变量
        $where  = array();
        $alias  = '';
        $field  = true;
        $having = '';
        $join   = array();
        $group  = '';
        //变量赋值
        if (isset($_where_order_field['where'])) {
            $where = $_where_order_field['where'];
        }
        if (isset($_where_order_field['field'])) {
            $field = $_where_order_field['field'];
        }
        if (isset($_where_order_field['order'])) {
            $order = $_where_order_field['order'];
        }
        if (isset($_where_order_field['join'])) {
            $join = $_where_order_field['join'];
        }
        if (isset($_where_order_field['having'])) {
            $having = $_where_order_field['having'];
        }
        if (isset($_where_order_field['alias'])) {
            $alias = $_where_order_field['alias'];
        }
        if (isset($_where_order_field['group'])) {
            $group = $_where_order_field['group'];
        }
        //判断model的类型
        $model = $modelStr;
        if (is_string($model)) {
            $model = db($model);

        }

        //获取总个数
        $count_ = $model->alias($alias)
            ->where($where)
            ->join($join)
            ->group($group)
            ->having($having)->count();
        //总条数
        $sum = $count_;
        //当前页
        $currPage = input('page', 1);
        if (is_numeric($currPage) && $currPage == 0) {
            $currPage = 1;
        }
        //每页分页的行数
        $rows = input('rows', 20);
        //总页数
        $pages = ceil($sum / $rows);
        if ($pages == 1) {
            $currPage = 1;
        }
        $limit = (($currPage - 1) * $rows) . ',' . $rows;
        //查询数据集合
        $lists = $model->alias($alias)
            ->where($where)
            ->join($join)
            ->field($field)
            ->order($order)
            ->group($group)->having($having)
            ->limit($limit)->select();

        //组装数据集
        $this->assign("data", $lists);
        $page_data = array(
            'totalPagesNumber' => $pages,
            'totalPages'       => $sum,
            'currPage'         => $currPage,
            'rows'             => $rows,
            'currPage'         => $currPage,
            'rowsDataList'     => $lists,
        );
        if ($isReturnResult) {
            return $page_data;
        } else {
            echo json_encode($page_data);
        }
    }

    /**
     * 公共分组查询数据3
     * @authName 公共分组查询数据3
     * @authStatus 0
     * @author zxy
     * @createTime 2017-11-16 14:54:28
     * @qqNumber 2639347794
     *
     * @param $modelStr 模型字符或模型对象
     * @param array $_where_order_field where条件,order排序,field查询字段 ,join联表查询数组,having 用于别名操作
     * @param string $count 查询条数字段
     * @return array
     */
    public function _getListsFromModel($modelStr, $_where_order_field = array(), $count = 'count(*) as tp_count')
    {
        $where  = array();
        $alias  = '';
        $field  = true;
        $having = '';
        $join   = array();
        $group  = '';
        $order  = true;
        //别名
        if (isset($_where_order_field['alias'])) {
            $alias = $_where_order_field["alias"];
        }
        //条件
        if (isset($_where_order_field['where'])) {
            $where = $_where_order_field['where'];
        }
        //查询字段
        if (isset($_where_order_field['field'])) {
            $field = empty($_where_order_field['field']) ? true : $_where_order_field['field'];
        }
        if (isset($_where_order_field['order'])) {
            $order = $_where_order_field['order'];
        }
        if (isset($_where_order_field['join'])) {
            $join = $_where_order_field['join'];
        }
        if (isset($_where_order_field['count_where'])) {
            $count_where = $_where_order_field["count_where"];
        }
        if (isset($_where_order_field['having'])) {
            $having = $_where_order_field['having'];
        }
        if (isset($_where_order_field['group'])) {
            $group = $_where_order_field["group"];
        }
        if (isset($_where_order_field['is_group_count'])) {
            $is_group_count = intval($_where_order_field['is_group_count']);
        }
        if (isset($_where_order_field['listrows'])) {
            $listRows = $_where_order_field['listrows'];
        } else {
            $listRows = 20;
        }
        //可以自定义填充位置id的名称
        if (isset($_where_order_field['paddingId'])) {
            //$element_id=$_where_order_field['paddingId'];
        } else {
            //$element_id="dataList_div";
        }
        //实例化model
        $modelObj = $modelStr;
        if (is_string($modelObj)) {
            $modelObj = db($modelObj);
        }
        //计算总条数
        $totalInfo = $modelObj->alias($alias)->where($where)->join($join)->field($count)->find();
        //总记录条数
        $totalNum = $totalInfo['tp_count'];
        //当前页码
        $currentPage = input('page', 1);
        //jumpPage 跳转页数
        $jumpPage = input('jumpPage');
        if (!empty($jumpPage)) {
            $currentPage = $jumpPage;
        }
        //总页数 = 总条数/每页总页数
        $totalPage = ceil($totalNum / $listRows);
        //分页字符串
        $limit = (($currentPage - 1) * $listRows) . ',' . $listRows;
        //查询数据集
        $_lists = $modelObj->alias($alias)->where($where)
            ->join($join)->field($field)->group($group)
            ->having($having)->limit($limit)->order($order)
            ->select();

        //$options ="<option value='5'>5</option><option value='10'>10</option>";
        //$options.="<option value='20'>20</option><option value='50'>50</option>";
        //$options.="<option value='100'>100</option>";
        //$options = str_replace(">{$listRows}<"," selected >{$listRows}<",$options);
        $pageHtml = "<div class='pagination-info'>共<font style='color:blue;'>{$totalNum}</font>条记录,&nbsp;当前显示第<font style='color:blue;'>{$currentPage}</font>页&nbsp;</div>";
        //$pageHtml.="每页显示：<select class='select m-r-5'>{$options}</select>条</div>";

        $pageLinkBtn = "<li class='first'><a href='javascript:void(0);' data-go-page='1' title='跳转到第一页'><<</a></li>"; //跳转到"第一页"按钮
        $upPageNum   = $currentPage - 1; //上一页页码
        $pageLinkBtn .= "<li class='prev'><a href='javascript:void(0);' data-go-page='$upPageNum' title='上一页'><</a></li>"; //"上一页"按钮
        $pageNumBtn = ""; //页码跳转按钮html代码
        //第一页
        $range = 5; //"更多"按钮显示范围条件
        if ($totalPage > $range && $currentPage >= $range) {
            $pageNumBtn .= "<li><a href='javascript:void(0);' data-go-page='1'>1</a></li><li class='more'><a href='javascript:void(0);'>...</a></li>";
        }
        for ($i = 1; $i <= $totalPage; $i++) {
            //如果当前页小于每页显示范围
            //就相当于1-每页显示范围内条数，1 2 3至每页显示范围 ... 最后一页
            //如果当前页大于等于每页显示范围
            //就以每页显示范围为中心，1...前面两个+每页显示范围+后面两个...最后一页
            $_page = $i;
            if ($currentPage < $range) {
                if ($_page > 0 && $_page != $currentPage) {
                    if ($_page <= $range) {
                        $pageNumBtn .= "<li><a href='javascript:void(0);' data-go-page='$_page'>{$_page}</a></li>";
                    } else {
                        if (ceil($_page) === $totalPage) {
                            $pageNumBtn .= '&nbsp;<li><a href="javascript:void(0);">...</a></li>';
                        } elseif ($_page > $totalPage) {
                            break;
                        } else {
                            continue;
                        }
                    }
                } else {
                    //&& $totalPage != 1
                    if ($_page > 0) {
                        $pageNumBtn .= "<li class='active'><a href='javascript:void(0);' data-cur-page='$_page'>{$_page}</a></li>";
                    }
                }
            } else {
                if ($_page > 0 && $_page != $currentPage) {
                    //这个情况处理特殊
                    //1.当前页是倒数第二页
                    //2.中间页数
                    //3.最后一页
                    if ((($_page <= $currentPage + 2 && $_page >= $currentPage - 2) ||
                        ($currentPage + 1 == $totalPage && $_page <= $currentPage + 1 && $_page >= $currentPage - 3) ||
                        ($currentPage == $totalPage && $_page >= $currentPage - 4))) {
                        $pageNumBtn .= "<li><a href='javascript:void(0);' data-go-page='$_page'>{$_page}</a></li>";
                    } else {
                        if ($_page === 1) {
                            $pageNumBtn .= '&nbsp;';
                        } elseif (ceil($_page) === $totalPage) {
                            $pageNumBtn .= '<li class="more"><a href="javascript:void(0);">...</a></li>';
                        } elseif (ceil($_page) > $totalPage) {
                            break;
                        } else {
                            continue;
                        }
                    }
                } else {
                    //&& $totalPage != 1
                    if ($_page > 0) {
                        $pageNumBtn .= "<li class='active'><a href='javascript:void(0);' data-cur-page='$_page'>{$_page}</a></li>";
                    }
                }
            }
        }
        if ($totalPage > $range && ($totalPage - $currentPage) > $range) {
            //显示最后一页页码按钮
            $pageNumBtn .= "<li><a href='javascript:void(0);' data-go-page='$totalPage'>{$totalPage}</a></li>";
        }
        $pageLinkBtn .= $pageNumBtn;
        $nextPageNum = ($currentPage + 1) > $totalPage ? 0 : ($currentPage + 1); //下一页页码
        $pageLinkBtn .= "<li class='next'><a href='javascript:void(0);' data-go-page='$nextPageNum' title='下一页'>></a></li>"; //"下一页"按钮
        $pageLinkBtn .= "<li class='last'><a href='javascript:void(0);' data-go-page='$totalPage' title='跳转到最后一页'>>></a></li>"; //跳转到"最后一页"按钮
        //$pageUrl = url(request()->action());//获取分页数据请求链接
        $pageHtml .= "<ul class='pagination'>{$pageLinkBtn}</ul>";
        $pageHtml .= "<div class='pagination-goto'>跳转到第&nbsp;<input type='text' name='jumpPage' class='ipt form-control goto-input'/>页";
        $pageHtml .= "&nbsp;<button class='btn btn-default goto-button' title='点击跳转到指定页面'>GO</button></div>";
        $resultData = array(
            'firstRow'   => (($currentPage - 1) * $listRows),
            'page'       => $currentPage,
            'totalPages' => $totalPage,
            'page_size'  => $listRows,
            'lists'      => $_lists,
            'page_html'  => $pageHtml,
        );
        return $resultData;
    }

    /**
     * 生成分页跳转链接URL【暂没用】
     * @authName 生成分页跳转链接URL【暂没用】
     * @authStatus 0
     * @author zxy
     * @createTime 2017-11-16 14:54:59
     * @qqNumber 2639347794
     *
     * @param $pageNum
     * @return string
     */
    private function pageLinkUrl($pageNum)
    {
        if (empty($pageNum)) {
            return 'javascript:void(0);';
        }
        $postQueryData     = (array) input('post.');
        $getQueryData      = (array) input('get.');
        $queryData         = array_merge($postQueryData, $getQueryData);
        $queryData['page'] = $pageNum;
        unset($queryData['_page']);
        return url(request()->action(), $queryData);
    }

    /**
     * 公共分组查询数据4
     * @authName 公共分组查询数据4
     * @authStatus 0
     * @author zxy
     * @createTime 2017-11-16 14:55:35
     * @qqNumber 2639347794
     *
     * @param $modelStr 模型字符或模型对象
     * @param array $_where_order_field where条件,order排序,field查询字段 ,join联表查询数组,having 用于别名操作
     * @param string $count 查询条数字段
     * @return array
     */
    public function getDataListModel($modelStr, $_where_order_field = array(), $count = 'count(*) as tp_count')
    {
        $where  = array();
        $alias  = '';
        $field  = true;
        $having = '';
        $join   = array();
        $group  = '';
        $order  = true;
        //别名
        if (isset($_where_order_field['alias'])) {
            $alias = $_where_order_field["alias"];
        }
        //条件
        if (isset($_where_order_field['where'])) {
            $where = $_where_order_field['where'];
        }
        //查询字段
        if (isset($_where_order_field['field'])) {
            $field = empty($_where_order_field['field']) ? true : $_where_order_field['field'];
        }
        if (isset($_where_order_field['order'])) {
            $order = $_where_order_field['order'];
        }
        if (isset($_where_order_field['join'])) {
            $join = $_where_order_field['join'];
        }
        if (isset($_where_order_field['count_where'])) {
            $count_where = $_where_order_field["count_where"];
        }
        if (isset($_where_order_field['having'])) {
            $having = $_where_order_field['having'];
        }
        if (isset($_where_order_field['group'])) {
            $group = $_where_order_field["group"];
        }
        if (isset($_where_order_field['is_group_count'])) {
            $is_group_count = intval($_where_order_field['is_group_count']);
        }
        if (isset($_where_order_field['listrows'])) {
            $listRows = $_where_order_field['listrows'];
        } else {
            $listRows = 20;
        }
        //可以自定义填充位置id的名称
        if (isset($_where_order_field['paddingId'])) {
            $element_id = $_where_order_field['paddingId'];
        } else {
            $element_id = "dataList_div";
        }

        //实例化model
        $model = $modelStr;
        if (is_string($model)) {
            $model = db($model);
        }

        //计算总条数
        $count_ = $model
            ->alias($alias)
            ->where($where)
            ->join($join)
            ->field($count)->find();

        //总条数
        $sum = $count_['tp_count'];

        //当前页码
        $current_page = input('p', 1);
        //_page select跳转页数
        $_page = input('_page');
        if (!empty($_page)) {
            $current_page = $_page;
        }

        //总页数=总条数/每页总页数
        $pages = ceil($sum / $listRows);
        //分页类库
        $page  = new Page($sum, $listRows);
        $limit = $page->firstRow . ',' . $page->listRows; //分页字符串

        //$limit=(($current_page-1)*$listRows).','.$listRows;

        //查询数据集
        $_lists = $model->alias($alias)->where($where)
            ->join($join)
            ->field($field)
            ->group($group)
            ->having($having)
            ->limit($limit)
            ->order($order)
            ->select();

        //返回的数据集
        $page_data = array(
            'firstRow'   => (($current_page - 1) * $listRows),
            'page'       => $current_page,
            'totalPages' => $sum,
            'page_size'  => $listRows,
            'lists'      => $_lists,
            '_page'      => $page->show2(),
        );
        return $page_data;
    }

    /**
     * 接口数据返回通用判断
     * @logic 判断接口是否返回正确参数
     * 若参数异常或结果为空则报错
     * 通过后不进行处理
     * @authName 接口数据返回通用判断
     * @authStatus 0
     * @author zxy
     * @createTime 2017-11-16 14:56:04
     * @qqNumber 2639347794
     *
     * @param $result
     */
    public function resultJudge($result){
        if(empty($result)){
            $this->error('网络请求失败');
        }else if($result['code']==0){
            $this->error($result['msg']);
        }else if($result['code']==40001){
            //清除登录信息
            clearLoginInfo();
            //跳转到登录界面
            return $this->redirect('Login/index');
        }
    }
}
