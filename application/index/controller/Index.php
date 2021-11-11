<?php
namespace app\index\controller;

use think\Db;

/**
 * 首页
 * @authName 首页
 * @authStatus 1
 * @author zxy
 * @createTime 2017-11-16 16:01:28
 * @qqNumber 2639347794
 *
 * Class Index
 * @package app\index\controller
 */
class Index extends Base
{


    /**
     * 首页
     * @authName 首页
     * @authStatus 1
     * @author zxy
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

    /**
     * 欢迎页面
     * @authName 欢迎页面
     * @authStatus 1
     * @author zxy
     * @createTime 2017-11-16 16:03:21
     * @qqNumber 2639347794
     *
     * @return mixed
     */
    public function welcome()
    {
        //得到欢迎提示语
        $hello_text = $this->getHelloText();
        $this->assign('hello_text',$hello_text);
        //获得上次登录时间
        $userInfo = session(config('CURR_USER_INFO')); //登录信息

        $apiData['user_id'] = UID;  //用户编码
        $apiData['user_type'] = 1;  //用户类别(公司)
        //得到用户通知信息
        $result = callBizApi_POST('getIndexData',$apiData);
//        dump($result);die;
        $this->resultJudge($result);
        $data = $result['data'];
        $this->assign('upcomingArr',json_encode($data['upcomingArr']));
        $this->assign('messageNotice',json_encode($data['messageData']));
        $this->assign('message_not_read_number',$data['message_not_read_number']);
//        $this->assign('purchaseCountData',json_encode($data['purchaseCountData']));
//        $this->assign('saleCountData',json_encode($data['saleCountData']));
//        dump($data['transactionCount']);die;
//        $this->assign('transactionCount',json_encode($data['transactionCount']));
        $this->assign('back_log_number',is_array($data['backLogData'])?count($data['backLogData']):0);
        $this->assign('waitApprovalData',json_encode($data['waitApprovalData']));
        $this->assign('userInfo', $userInfo);
        return $this->fetch();
    }

    /**
     * @authName 得到欢迎提示
     * @authStatus 1
     * @author zhangxinyu
     * @createTime 2018-01-04 14:38:48
     * @qqNumber 2639347794
     * @return string
     */
    public function getHelloText()
    {
        $hour = date('H');

        $hello_text = '晚上好';
        if($hour < 6){
            $hello_text = "凌晨好";
        }
        else if ($hour < 9){
            $hello_text = "早上好";
        }
        else if ($hour < 12){
            $hello_text = "上午好";
        }
        else if ($hour < 14){
            $hello_text = "中午好";
        }
        else if ($hour <= 17){
            $hello_text = "下午好";
        }
        else if ($hour < 19){
            $hello_text = "傍晚好";
        }
        return $hello_text;
    }

    /**
     * 清除缓存
     * @authName 清除缓存
     * @authStatus 1
     * @author zxy
     * @createTime 2017-11-16 16:03:54
     * @qqNumber 2639347794
     *
     */
    public function clearRuntimeDir()
    {

        //清除 系统缓存
        //清除本系统缓存
        _system_clearRuntimeDir();

        $this->success('系统缓存清除成功！');
    }

    /**
     * 修改登录密码
     * @authName 修改登录密码
     * @authStatus 1
     * @author zxy
     * @createTime 2017-11-16 16:04:07
     * @qqNumber 2639347794
     *
     * @return mixed
     */
    public function editLoginPwd()
    {
        $type = input('type/d',0);  //来源，是来源于完善信息，还是修改密码
        if (request()->isPost()) {

            //请求修改密码接口
            $reqData = input();

            $result = callBizApi_POST('updatePWD',$reqData);
            $this->resultJudge($result);
            $this->success('修改成功');
        } else {
            $userInfo = _getUserLoginInfoSession();
            //用户名
            $username = $userInfo['username'];
            $this->assign('username',$username);
            $this->assign('type',$type);
            return $this->fetch('edit_login_pwd');
        }
    }

    /**
     * 用户信息
     * @authName 用户信息
     * @authStatus 1
     * @author zxy
     * @createTime 2017-11-16 16:04:18
     * @qqNumber 2639347794
     *
     * @return mixed
     */
    public function userInformation()
    {
        $type = input('type/d',0);
        $result = callBizApi_POST('getIndexUserInfo',[]);
        $this->resultJudge($result);
        $result['data']['nickname'] = userNickNameDecode($result['data']['nickname']);
        $this->assign("uInfo", $result['data']);
        $this->assign('type',$type);
        return $this->fetch('userInformation');

    }

    /**
     * @authName 采购数据统计
     * @authStatus 1
     * @author zhangxinyu
     * @createTime 2018-02-09 16:16:14
     * @qqNumber 2639347794
     */
    public function purchaseOrderCount()
    {
        $result = callBizApi_POST('purchaseOrderCount',input());
        $this->resultJudge($result);
        echo json_encode($result);
        exit;
    }

    /**
     * @authName 得到交易数据
     * @authStatus 1
     * @author zhangxinyu
     * @createTime 2018-02-10 10:47:29
     * @qqNumber 2639347794
     */
    public function TransactionCount()
    {
        $result = callBizApi_POST('transactionCount',input());
        $this->resultJudge($result);
        echo json_encode($result);
        exit;
    }

    /**
     * @authName 销售数据统计
     * @authStatus 1
     * @author zhangxinyu
     * @createTime 2018-02-09 16:16:14
     * @qqNumber 2639347794
     */
    public function saleOrderCount()
    {
        $result = callBizApi_POST('saleOrderCount',input());
        $this->resultJudge($result);
        echo json_encode($result);
        exit;
    }

    /**
     * @authName 加载审批数据
     * @authStatus 1
     * @author zhangxinyu
     * @createTime 2018-06-28 09:54:58
     * @qqNumber 2639347794
     */
    public function getApprovalData()
    {
        $result = callBizApi_POST('getWaitApprovalData',input());
        $this->resultJudge($result);
        echo json_encode($result);
        exit;
    }

    /**
     * @authName 得到待办数据
     * @author zhangxinyu
     * @createTime 2019-03-21 09:53:33
     * @qqNumber 2639347794
     */
    public function getUpcomingData()
    {
        $result = callBizApi_POST('getUpcomingData',input());
        $this->resultJudge($result);
        echo json_encode($result);
        exit;
    }

    /**
     * @authName 开发提示
     * @authStatus 1
     * @author zhangxinyu
     * @createTime 2018-01-17 23:35:22
     * @qqNumber 2639347794
     */
    public function developmentPrompt()
    {
        echo '第二期内容，正在开发中……';
    }

    /**
     * @authName 待办事项
     * @authStatus 1
     * @author zhangxinyu
     * @createTime 2018-04-11 18:06:45
     * @qqNumber 2639347794
     */
    public function backLog()
    {
        $result = callBizApi_POST('getBackLogData',input());
        $this->resultJudge($result);
        $this->assign('dataList',$result['data']);
        return $this->fetch('index/back_log');
    }

    /**
     * @authName 待办详情
     * @author zhangxinyu
     * @createTime 2019-03-21 10:19:34
     * @qqNumber 2639347794
     */
    public function notCompleteConfirm()
    {

        //确认单待收票
        $confirmWhere = [];
        $confirmWhere['collect_tickets_status'] = ['IN',[0,1]];
        $confirmWhere['contract_status'] = ['NOT IN',[4,5]];
        $confirmWhere['company_id'] = COMPANY_ID;
        $data = Db::name('purchase_confirm')->where($confirmWhere)->field('no,id')->select();
        $confirmIdArr = array_column($data,'id');

        //确认单待收货
        $confirmWhere = [];
        $confirmWhere['collect_goods_status'] = ['IN',[0,1]];
        $confirmWhere['contract_status'] = ['NOT IN',[4,5]];
        $confirmWhere['company_id'] = COMPANY_ID;
        $confirmWhere['id'] = ['NOT IN',$confirmIdArr];
        $data1 = Db::name('purchase_confirm')->where($confirmWhere)->field('no,id')->select();

        $res_data = array_merge($data,$data1);


        $this->assign('res_data',$res_data);
        return $this->fetch('index/not_complete_confirm');
    }

    /**
     * @authName 数据监控
     * @author zhangxinyu
     * @createTime 2019-03-21 16:09:13
     * @qqNumber 2639347794
     */
    public function dataMonitor()
    {
        $result = callBizApi_GET('dataMonitor',[]);
//        dump($result);die;
        $this->resultJudge($result);

        $data = $result['data'];

        $purchaseNumArr = $data['purchaseNumArr'];


        $this->assign('confirmStatusData',$data['confirmStatusData']);
        $this->assign('toDayPurchase',$data['toDayPurchase']);
        $this->assign('purchaseNumArr',json_encode($purchaseNumArr));
        $this->assign('toDayPurchaseArr',$data['toDayPurchaseArr']);
        $this->assign('profitArr',json_encode($data['profitArr']));
        $this->assign('stockArr',json_encode($data['stockArr']));
        $this->assign('enterStockArr',$data['enterStockArr']);
        $this->assign('outStockArr',$data['outStockArr']);
        $this->assign('salesChartArr','[]');
//        json_encode($data['salesChartArr'])
//        dump($result);die;
        return $this->fetch('data_monitor');
    }

    /**
     * @authName 反馈建议
     * @author zhangxinyu
     * @createTime 2019-07-04 21:33:13
     * @qqNumber 2639347794
     */
    public function feedbackList()
    {
        if(request()->isPost()){
            $result = callBizApi_POST('getFeedbackList',input());
            $this->resultJudge($result);
            echo json_encode($result['data']);
            exit;
        }
        return $this->fetch();
    }

    /**
     * @authName 反馈建议
     * @author zhangxinyu
     * @createTime 2019-07-04 21:33:13
     * @qqNumber 2639347794
     */
    public function feedback()
    {
        if(request()->isPost()){
            $result = callBizApi_POST('saveFeedback',input());
            $this->resultJudge($result);
            $this->success($result['msg'].'<script>setTimeout(function(){location.reload();},1500);</script>');
        }

        $result = callBizApi_GET('getAddMaintenanceData',[]);

        $userInfo = _getUserLoginInfoSession();
        $this->assign('userInfo',$userInfo);
        $this->assign('entity',$result['data']);
        return $this->fetch();
    }

    /**
     * @authName 打开供应商系统
     * @author zhangxinyu
     * @createTime 2019-08-23 09:44:22
     * @qqNumber 2639347794
     */
    public function openSupSystem()
    {
        $userInfo = _getUserLoginInfoSession();
//        dump($userInfo);die;

        $sup_system = config('Suppliers_System');
        $key = config('UC_AUTH_KEY');
        $time = time();
        $sign = md5($key.$time.$userInfo['rel_supplier_id']);
        $this->redirect($sup_system.'/index/Login/relSupplierLogin?sign='.$sign.'&time='.$time.'&rel_supplier_id='.$userInfo['rel_supplier_id']);
    }
}
