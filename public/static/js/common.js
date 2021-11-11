$(function() {
    //刷新界面
    $("#refresh_btn").click(function() {
        var frmUrl = $("#form_search").attr('action');
        if (typeof(frmUrl) != 'undefined') {
            window.location.href = frmUrl;
        }
    });
    //图片移除按钮的显示与隐藏
    $(".upload_preview").on('mouseover', '.preview-pic-item', function() {
        $(this).children('span.pic-remove-btn').show();
    }).on('mouseleave', '.preview-pic-item', function() {
        $(this).children('span.pic-remove-btn').hide();
    });
    //移除按钮事件
    $(".upload_preview").on("click", 'span.pic-remove-btn', function() {
        var currObj = $(this);
        art.dialog.confirm("您确认移除吗?", function() {
            currObj.parents('.upload_preview').next().find("input[name='pic_name']").val("");
            currObj.parent().remove();
        });
    });
    //页面顶部搜索按钮事件触发
    $("#btn_search").click(function() {
        var formData = $("#form_search").serializeArray();
        //必须将formData 转换成Json格式
        var formDataArray = new Array();
        var objFormData = {};
        $.each(formData, function(i, value) {
            objFormData[value["name"]] = value["value"];
        });
        var gridObj = $("#gridTable");
        if (gridObj != 'undefined') {
            //重新加载Grid数据
            $("#gridTable").jqGrid('setGridParam', {
                url: listUrl, //你的搜索程序地址
                datatype: 'json',
                postData: objFormData, //发送数据
                page: 1,
            }).trigger("reloadGrid"); //重新载入
        }
    });
    //分页条控制事件触发
    $(document).on('click', "#pagination ul.pagination a", function() {
        var goToPageNum = $(this).attr('data-go-page');
        if (goToPageNum != 'undefined' && goToPageNum != 0) {
            getListData(goToPageNum); //如果使用该分页控件，页面必须有此方法
        }
    });
 
});
/**
 * 操作完成之后的提示
 * @param tip_type tip提示级别 1成功 -1请求中 0操作失败 -2请求服务器异常
 * @param content 提示内容
 */
function showvfmsg(tip_type, content, time) {
    var str = '';
    var i_str = '';
    var _type_str = "info";
    if (!isNaN(tip_type)) {
        tip_type = parseInt(tip_type);
    }
    switch (tip_type) {
        case 0:
        case 'error':
            str = 'alert-danger';
            i_str = 'alert_danger_icon';
            _type_str = "error";
            break;
        case 1:
        case 'success':
            str = 'alert-success';
            i_str = 'alert_success_icon';
            _type_str = "success";
            break;
        case -1:
        case 'info':
            str = 'alert-info';
            i_str = 'alert_info_icon';
            _type_str = "load";
            break;
        case -2:
        case 'warning':
            str = 'alert-warning';
            i_str = 'alert_warning_icon';
            _type_str = "error";
            break;
    }
    _notif(_type_str, content, "right", time);
}
/**
 * 操作完成之后的提示
 *
 * @param content 提示内容
 */
function art_alter(content){
    art.dialog.alert(content)
}
//提示插件
function _notif(type, msg, position, timeout) {
    var icon = "";
    var types_type = isNaN(type);
    if (type == "success" || (types_type == false && parseInt(type) == 1)) {
        type = 'success';
        icon = '<img height="30" src="https://statics.hdcnooc.com//common/pc/plugins/notifit/images/ok.png"/>';
    } else if (type == "error" || (types_type == false && parseInt(type) == 0)) {
        type = 'error';
        icon = '<img height="30" src="https://statics.hdcnooc.com//common/pc/plugins/notifit/images/error.png"/>';
    } else if (type == "info" || type == "load" || (types_type == false && parseInt(type) == -1)) {
        icon = '<img  height="30"  src="https://statics.hdcnooc.com//common/pc/plugins/notifit/images/load9.gif"/>';
        type = "info";
    }
    var notif_str = "<table style='width:90%;margin:auto; height:60px;'><tr><td style='width:50px;'>" + icon + "</td><td style='text-align:left;font-weight:bold;color:#fff !important;'>" + msg + "</td></tr></table>";
    notif({
        msg: notif_str,
        type: type,
        position: position,
        timeout: timeout
    });
}
/*提交检索信息*/
function search_filter_dataList() {
    var frmObj = $('.form-search');
    if (frmObj.length > 0) {
        var url = frmObj.attr('action');
        if (typeof(url) != 'undefined') {
            var queryObj = frmObj.find('input,select');
            //去除空格
            $.each(queryObj, function(i, item) {
                var curVal = $.trim($(this).val());
                $(item).val(curVal);
            });
            var query = frmObj.find('input,select').serialize();
            query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
            query = query.replace(/^&/g, '');
            if (url.indexOf('?') > 0) {
                url += '&' + query;
            } else {
                url += '?' + query;
            }
            _get_page_dataList(url, 'dataList_div');
        }
    }
}
/*
 *界面请求数据方法
 * @param page_ajax_url 数据请求的链接
 * */
function _load_page_dataList(page_ajax_url, id) {
    var paddingId = typeof(id) == 'undefined' ? 'dataList_div' : id;
    _get_page_dataList(page_ajax_url, paddingId);
}
/*
 *请求数据
 * @param url数据请求的链接
 * @param paddingStr 填充界面的字符串（可能是id或class）
 * */
function _get_page_dataList(url, paddingStr) {
    $.ajax({
        url: url,
        type: 'POST',
        beforeSend: function() {},
        dataType: 'HTML',
        success: function(data) {
            $("#" + paddingStr).html(data);
        },
        error: function() {
            alert('服务请请求失败,请稍后重试!');
        },
        complete: function() {}
    });
}
/**
 * 点击页数加载
 * @param a_obj
 * @returns {boolean}
 */
function goPage(a_obj) {
    var href = $(a_obj).attr('href');
    if (href !== 'javascript:;') _get_page_dataList(href, "dataList_div");
    return false;
}
//改变当前页面的分页数量
function changePageNum(obj) {
    var currUrl = $("#btn_search").parents('form').attr('action');
    var currNum = $(obj).val(); //获取跳转的页码
    var newUrl = '';
    if (!isNaN(currNum)) {
        //分解URL
        var index = currUrl.lastIndexOf('.html');
        if (index > 0) {
            newUrl = currUrl.substr(0, index);
            //newUrl+='/rows/'+currNum+'.html';   //拼接字符串
            var queryObj = $("#btn_search").parents('form').find('input,select');
            //去除空格
            $.each(queryObj, function(i, item) {
                var curVal = $.trim($(this).val());
                $(item).val(curVal);
            });
            var query = $('.search-form').find('input,select').serialize();
            query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
            query = query.replace(/^&/g, '');
            if (newUrl.indexOf('?') > 0) {
                newUrl += '&' + query + "&rows=" + currNum;
            } else {
                newUrl += '?' + query + "&rows=" + currNum;
            }
            //加载数据
            _get_page_dataList(newUrl, 'dataList_div');
        }
    }
}
//跳转到指定页码
function jumpPageNum(obj) {
    //获取刷新链接
    var currUrl = $(".datalist-current-page").attr('data-url');
    if (typeof(currUrl) == "undefined") {
        currUrl = $("#search_btn").parents('form').attr('action');
    }
    var currNum = $(obj).val(); //获取跳转的页码
    var newUrl = '';
    if (!isNaN(currNum)) {
        //分解URL
        var index = currUrl.lastIndexOf('.html');
        if (index > 0) {
            newUrl = currUrl.substr(0, index);
            newUrl += '/jp/' + currNum + '.html'; //拼接字符串
            //加载数据
            _get_page_dataList(newUrl, 'dataList_div');
        }
    }
}
//获取默认加载出错的图片
function getDefErrorPic(obj, type) {
    var picUrl = '';
    switch (parseInt(type)) {
        case 1:
            picUrl = '/static/pc/ui/img/logo.png';
            break; //红包默认logo
        default:
            picUrl = '/static/pc/ui/img/logo.png';
            break;
    }
    $(obj).attr({
        'src': picUrl,
        'onerror': null
    });
}
//默认选中上传图片
function selectUploadPics(module_name, is_one, elementid, wd, ht) {
    if (typeof(wd) == 'undefined') {
        wd = 950; //默认宽度
    }
    if (typeof(ht) == 'undefined') {
        ht = 500; //默认高度
    }
    var url = "/index.php/index/Attachment/index/mn/" + module_name + "/one/" + is_one + "/elementid/" + elementid;
    art.dialog.open(url, {
        title: '选择附件',
        width: wd,
        height: ht,
        lock: true
    });
}

//选择上传资质图片
function UploadPics(module_name, is_one, elementid, wd, ht,usertype,typeid) {
    if (typeof(wd) == 'undefined') {
        wd = 950; //默认宽度
    }
    if (typeof(ht) == 'undefined') {
        ht = 500; //默认高度
    }
    var url = "/index.php/index/Attachment/index/mn/" + module_name + "/one/" + is_one + "/elementid/" + elementid+"/usertype/"+usertype+"/typeid/"+typeid;
    art.dialog.open(url, {
        title: '选择附件',
        width: wd,
        height: ht,
        lock: true
    });
}

//刷新列表数据
function refreshPage() {
    //判断元素是否存在，如果不存在，则不执行，避免调用报错，导致后续代码无法执行
    if($("#gridTable").get(0)==null){
        return;
    }
    var currPageNum = $("#gridTable").jqGrid('getGridParam').page;
    var reqListDataUrl = listUrl;
    if (reqListDataUrl != 'undefined') {
        $("#gridTable").jqGrid('setGridParam', {
            url: reqListDataUrl, //你的搜索程序地址
            datatype: 'json',
            page: currPageNum
        }).trigger("reloadGrid"); //重新载入
    }
}

//初始化主页面gird高度
function initialMainGirdTablePage() {
    var pageHg = $(window).height(); //页面的高度
    var girdOffTopHg = $(".gridPanel").get(0).offsetTop; //距离顶部的高度
    // console.log(pageHg + "|" + girdOffTopHg);
    var pageMaxGirdTableHg = pageHg - (girdOffTopHg + 72);
    var currGirdTabHg = $('#gridTable').height(); //gird内容的高度
    if (currGirdTabHg > pageMaxGirdTableHg) {
        currGirdTabHg = pageMaxGirdTableHg
    } else if (currGirdTabHg == 0) {
        currGirdTabHg = 32;
    }
    //设置gird的高度和宽度
    $("#gridTable").setGridHeight(currGirdTabHg);
    //resize重设(表格、树形)宽高
    $('#gridTable').setGridWidth(($('.gridPanel').width()));
}

//二级页面

/**
 * 默认获取加载出错的图片
 * @param string obj js对象
 * @param int sourceId 来源id
 * @return string imgPath 返回图片相对路径
 */
function loadingErrorImg(obj, sourceId) {
    var imgPath = __uri('https://statics.hdcnooc.com/common/pc/image/nopic.jpg');
    if (typeof(sourceId) != 'undefined') {
        switch (parseInt(sourceId)) {
            case 1:
                imgPath = imgPath;
                break;
            case 2:
                imgPath = '';
                break;
        }
    }
    $(obj).attr({
        'src': imgPath,
        'onerror': null
    });
}
//设置时间
function handleDateQuickPickClick(obj) {
    var t = $(obj),
        s = 864e5,
        o = +t.attr("data-days"),
        i = new Date,
        a = new Date(i + 1 * s),
        r = new Date(a - o * s);
    _setDate(r, 0, 0, 0);
    _setDate(a, 23, 59, 59);
    $(".btn-time").removeClass("btn-active");
    t.addClass("btn-active");
    start_timepicker.datetimepicker("setDate", r);
    end_timepicker.datetimepicker("setDate", a);
    start_timepicker.datetimepicker("option", "maxDate", a);
    end_timepicker.datetimepicker("option", "minDate", r);
}
//设置日期
function _setDate(e, t, n, s) {
    e.setHours(t), e.setMinutes(n), e.setSeconds(s)
}
/*
 * 显示全局的加载中效果
 * */
function _show_public_loading(){
    $("#_show_public_loading_div").css({
        "top": "0px"
    });
}
/*
 * 隐藏加载中效果
 * */
function _hide_public_loading() {
    $("#_show_public_loading_div").css({
        "top": "-70px"
    });
}
/**
 * 限制文本框只能输入数字，保留两位小数点
 * @param obj
 */
function clearNoNum(obj) {
    obj.value = obj.value.replace(/[^\d.]/g, ""); //清除“数字”和“.”以外的字符
    obj.value = obj.value.replace(/\.{2,}/g, "."); //只保留第一个. 清除多余的
    obj.value = obj.value.replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");
    obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/, '$1$2.$3'); //只能输入两个小数
    if (obj.value.indexOf(".") < 0 && obj.value != "") {
        //以上已经过滤，此处控制的是如果没有小数点，首位不能为类似于 01、02的金额
        obj.value = parseFloat(obj.value);
    }
}
//显示时间
function showTime(cellvalue) {
    if (cellvalue == '0' || cellvalue == undefined) {
        return '-';
    }
    var date = new Date(cellvalue * 1000);
    var Y = date.getFullYear() + '-';
    var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '-';
    var D = date.getDate() + ' ';
    var h = date.getHours() + ':';
    var m = date.getMinutes() + ':';
    var s = date.getSeconds();
    return Y + M + D + h + m + s;
}
/**
 * 通用根据物流单号获取物流信息方法
 * @param track_number  物流单号
 * @param shipping_code 物流公司code
 * @param div_id        展示跟踪信息的divid
 */
function getLogisticsTrack(track_number, shipping_code, div_id) {
    if (track_number == '' || typeof(track_number) == "undefined") {
        _show_base_tips('物流单号不存在', 2000);
        return;
    }
    $('#' + div_id).html('获取中，请稍等...');
    //执行请求
    $.ajax({
        url: '/index.php/Common/getLogisticsTrack',
        data: {
            'track_number': track_number,
            'shipping_code': shipping_code
        },
        dataType: 'json',
        type: 'GET',
        success: function(data) {
            if (data.code == 1) {
                var _data = eval("(" + data.data + ")");
                if (_data.Traces.length == 0) {
                    $('#' + div_id).html('暂无物流信息，请稍后再试');
                    return;
                }
                //循环处理得到的物流数据
                // var curdata, tardata;
                var _str = '';
                for (var i = _data.Traces.length - 1; i >= 0; i--) {
                    // tardata = new Date(result.Traces[i].AcceptTime.replace(/-/g, "/")).Format('yyyy-MM-dd');
                    // tartime = new Date(result.Traces[i].AcceptTime.replace(/-/g, "/")).Format('hh:MM:ss');
                    var tartime = _data.Traces[i].AcceptTime;
                    var AcceptStation = _data.Traces[i].AcceptStation;
                    var _classname = '';
                    var _classname_newbg = '';
                    switch (i) {
                        case 0: //第一行=当前状态
                            break;
                        case _data.Traces.length - 1: //最后一行
                            _classname = 'first';
                            _classname_newbg = 'mh-icon-new';
                            break;
                        default:
                            break;
                    }
                    _str += '<li class="' + _classname + '"><p>' + tartime + '</p><p>' + AcceptStation + '</p><span class="before"></span>';
                    _str += '<span class="after"></span><i class="mh-icon ' + _classname_newbg + '"></i></li>';
                }
                $('#' + div_id).html(_str);
            } else {
                $('#' + div_id).html('物流信息获取超时，请刷新重试');
            }
        },
        error: function() {
            $('#' + div_id).html('网络超时，请联系客服');
        },
        complete: function() {}
    });
}

function __uri(url) {
    return url;
}



//异步提交中显示加载中图标，完成后关闭
$(document).ajaxStart(function(){
    _show_public_loading();
})
//显示物流
function show_logistics(data_id,type){
    var url=show_logistics_url+"?data_id="+data_id+"&type="+type;
    var title="物流信息";
    _es_openDilogLarge80(url,title);
}


//加法函数，用来得到精确的加法结果
//说明：javascript的加法结果会有误差，在两个浮点数相加的时候会比较明显。这个函数返回较为精确的加法结果。
//调用：accAdd(arg1,arg2)
//返回值：arg1加上arg2的精确结果
function accAdd(arg1,arg2){
    var r1,r2,m;
    try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
    try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
    m=Math.pow(10,Math.max(r1,r2))
    return (arg1*m+arg2*m)/m
}


//加法函数，用来得到精确的加法结果
//说明：javascript的加法结果会有误差，在两个浮点数相加的时候会比较明显。这个函数返回较为精确的加法结果。
//调用：accSub(arg1,arg2)
//返回值：arg1加上arg2的精确结果
function accSub(arg1,arg2){
    var r1,r2,m,n;
    try{r1=arg1.toString().split(".")[1].length}catch(e){r1=0}
    try{r2=arg2.toString().split(".")[1].length}catch(e){r2=0}
    m=Math.pow(10,Math.max(r1,r2));
    //last modify by deeka
    //动态控制精度长度
    n=(r1>=r2)?r1:r2;
    return ((arg1*m-arg2*m)/m).toFixed(n);
}



//除法函数，用来得到精确的除法结果
//说明：javascript的除法结果会有误差，在两个浮点数相除的时候会比较明显。这个函数返回较为精确的除法结果。
//调用：accDiv(arg1,arg2)
//返回值：arg1除以arg2的精确结果
function accDiv(arg1,arg2){
    var t1=0,t2=0,r1,r2;
    try{t1=arg1.toString().split(".")[1].length}catch(e){}
    try{t2=arg2.toString().split(".")[1].length}catch(e){}
    with(Math){
        r1=Number(arg1.toString().replace(".",""))
        r2=Number(arg2.toString().replace(".",""))
        return (r1/r2)*pow(10,t2-t1);
    }
}


//乘法函数，用来得到精确的乘法结果
//说明：javascript的乘法结果会有误差，在两个浮点数相乘的时候会比较明显。这个函数返回较为精确的乘法结果。
//调用：accMul(arg1,arg2)
//返回值：arg1乘以arg2的精确结果
function accMul(arg1,arg2)
{

    var m=0,s1=arg1.toString(),s2=arg2.toString();
    try{m+=s1.split(".")[1].length}catch(e){}
    try{m+=s2.split(".")[1].length}catch(e){}
    return Number(s1.replace(".",""))*Number(s2.replace(".",""))/Math.pow(10,m)
}

var _detailsArr = [
    ['purchase_confirm_sign','签章','/index/purchase_confirm/sign?id=','1161px', '603px'],
    ['createConfirmSingle','生成采购确认单','/index/SelectedQuotedManage/createConfirmSingle?quoted_ids=','1161px', '603px'],
    ['SelectedQuotedManageDetails','已选报价书详情','/index/SelectedQuotedManage/details?id=','1161px', '603px'],
    ['notCompleteConfirm','待办详情','/index/PurchaseConfirm/index?type=','1161px', '90%'],
    ['inquiryBookSelected','待办详情','/index/InquiryBook/index?type=','1161px', '90%'],
    ['SelectedQuotedManageUpcoming','待办详情','/index/SelectedQuotedManage/index?type=','1161px', '90%'],
    ['confirmMaintenance','运维结果待确认','/index/OperationMaintenance/confirm?id=','800px', '500px']

];

/**
 * 显示详情根据类别
 * @param $type
 * @param $id
 */
function showDetailsByType(type,id,title){

    for(var index in _detailsArr){
        var item = _detailsArr[index];
        if(item[0]==type){
            _es_openDilog(item[2]+id,item[1], item[3], item[4], true, 0.2, item[0]);
            return;
        }
    }

    //供应商详情
    if(type=='supplier_details'){
        // _es_openDilog70id('/index/SupplierManage/detailsSupplier.html?id='+id, '供应商详情');
        _es_openDilog('/index/SupplierManage/detailsSupplier.html?id='+id,'供应商详情', '550px', '470px', true, 0.2, 'supplier_details');
    }
    //新增供应商
    else if(type=='supplier_add'){
        _esOpendialogmini('/index/SupplierManage/add.html', '新增供应商');
    }
    //新增工厂
    else if(type=='factory_add'){
        _es_auto_open_dialog('/index/FactoryManage/add.html', '新增工厂', "800px", "550px", "factory_add");
    }
    //新增油库
    else if(type=='factory_oil_depot_add'){
        _es_auto_open_dialog('/index/OilDepotManage/addOilDepot.html', "新增油库", "60%%", "80%", "add");
    }
    //油库详情
    else if(type=='factory_oil_details'){
        _es_openDilog70id('/index/OilDepotManage/details.html?stock_id='+id,'油库详情');
    }
    //新增油品
    else if(type=='oils_add'){
        _esOpendialogmini('/index/OilsManage/add.html', '新增油品');
    }
    //客户详情
    else if(type=='store_details'){
        var url = '/index/StoreManage/detailsSupplier.html?id='+id;
        _es_openDilog(url,'客户详情', '550px', '470px', true, 0.2, id);
        // _es_openDilog70id(url, '客户详情');
    }
    //询价书详情
    else if(type=='price_book_details'){
        // _es_open_dialog_w1000_h80('/index/InquiryBook/details.html?id='+id, '询价书详情');
        _es_auto_open_dialog('/index/InquiryBook/details.html?id='+id, '询价书详情', "1000px", "90%", 'price_book_details');
    }
    //油品详情
    else if(type=='oils_details'){
        _es_openDilog70id('/index/OilsManage/detailsOils.html?id='+id, '油品详情');
    }
    //省公司详情
    else if(type=='company_details'){
        if(id==0){
            return;
        }
        var url = '/index/CompanyManage/details.html?companyId='+id;
        _es_openDilog(url,'分公司详情', '500px', '460px', true, 0.2, id);
        // _es_openDilog70id('/index/CompanyManage/details.html?companyId='+id, "分公司详情");
    }
    //采购单详情
    else if(type=='purchase_order_details'){
        _es_openDilogMiddle('/index/purchaseOrder/details.html?id='+id, "采购单详情");
    }
    //内购模式采购单详情
    else if(type=='sup_purchase_order_details'){
        _es_openDilogMiddle('/index/SupPurchaseOrder/details.html?id='+id, "采购单详情");
    }
    //确认单详情
    else if(type=='purchase_confirm_details'){
        _es_openDilog('/index/purchase_confirm/details.html?id='+id,'确认单详情', '1161px', '90%', true, 0.2, 'confirm_details');
        // _es_openDilogMiddle('/index/purchase_confirm/details.html?id='+id, "确认单详情");
    }

    //确认单审批
    else if(type=='purchase_confirm_check'){
        _es_openDilog('/index/purchase_confirm/updateApproval1.html?id='+id+'&is_check=1','确认单变更申请', '801px', '90%', true, 0.2, 'purchase_confirm_check');
        // _es_openDilog('/index/purchase_confirm/details.html?id='+id+'&is_check=1','确认单详情', '1161px', '90%', true, 0.2, 'confirm_details');
        // _es_openDilogMiddle('/index/purchase_confirm/details.html?id='+id, "确认单详情");
    }

    //内购模式确认单详情
    else if(type=='sup_purchase_confirm_details'){
        _es_openDilogMiddle('/index/sup_purchase_confirm/details.html?id='+id, "确认单详情");
    }
    //通知消息详情
    else if(type=='notice_details'){
        _es_openDilogSmall('/index/MessageNotice/details.html?id='+id,'消息详情');
    }
    //发货地详情
    else if(type=='send_address_details'){
        if(title==null){
            title='发货地详情';
        }
        _es_openDilog70id('/index/SendAddressManage/details.html?send_address_id='+id,title);
    }
    //合同详情
    else if(type=='contract_details'){
        var url = '/index/ContractInformation/details.html?id='+id;
        _es_openDilog(url,'合同信息', '550px', '480px', true, 0.2, id);
        // _es_openDilog70id(,'合同信息');
    }
    //发货单详情
    else if(type=='goods_send_details'){
        _es_openDilogMiddle('/index/InvoiceManage/details.html?id='+id,'发货单详情');
    }
    //已选报价书详情
    else if(type=='price_book_quoted_details'){
        _es_openDilogMiddle('/index/inquiry_book/quotedDetails.html?id='+id,'查看报价');
    }
    //消息详情
    else if(type=='message_notice_details'){
        _es_openDilogSmall('/index/MessageNotice/details.html?id='+id+'&type=1','消息详情');
    }
    //供应询价书详情
    else if(type=='supplier_inquiry_book_details'){
        _es_open_dialog_w1000_h80('/index/QuotedPriceManage/details.html?id=' + id, "询价书详情", "supplier_inquiry_book_details");
    }
    //内部供应商询价书详情
    else if(type=='sup_inquiry_book_details'){
        _es_open_dialog_w1000_h80('/index/SupQuotedPriceManage/details.html?id=' + id, "询价书详情", "sup_inquiry_book_details");
    }
    //查看报价
    else if(type=='inquiry_quoted_details'){
        _es_openDilogMiddle('/index/inquiry_book/quotedDetails.html?id='+id, "报价列表", 'inquiry_quoted_details');
    }
    //入库详情
    else if(type=='storage'){
        _es_openDilogMiddle('/index/invoice_manage/storage_details.html?id='+id, "入库单详情", 'storage');
    }
    //供应商合同信息
    else if(type=='supplier_contract_details'){
        _es_openDilogMiddle('/index/ContractInformation/index.html?supplier_id='+id, "供应商合同信息", 'contract_details');
    }
    //设置手机号
    else if(type=='update_user_info') {
        _es_auto_open_dialog('/index/UserManage/edit.html?is_update_info=1', "修改基本信息", "800px", "500px", "update_user_info");
    }
    //关注并绑定微信
    else if(type=='follow_and_binding'){
        _es_openDilogSmall('/index/WeChatBinding/index.html','微信绑定','follow_and_binding');
    }
    //关联油品
    else if(type=='oil_relation'){
        _es_auto_open_dialog('/index/AddRelationOilsManage/index.html', "请选择需要关联的油品", "60%%", "90%", "oil_relation");
    }
    //添加发货地址
    else if(type=='send_address_add'){
        _es_auto_open_dialog('/index/SendAddressManage/addOne.html', "添加发货地址", "60%%", "90%", "send_address_add");
    }
    //修改供应商信息
    else if(type=='supplier_update_info'){
        _es_auto_open_dialog('/index/Index/editInfo.html', "修改基本信息", "800px", "500px", "supplier_update_info");
    }
    //询价书审批
    else if(type=='inquiry_approval_flow'){
        // _es_open_dialog_w1000_h80('/index/InquiryBook/approval.html?id='+id, '询价书审批','inquiry_approval_flow');
        _es_auto_open_dialog('/index/InquiryBook/approval.html?id='+id, '询价书审批', "1000px", "90%", 'inquiry_approval_flow');
    }
    //已选报价书审批
    else if(type=='quoted_approval_flow'){
        _es_openDilogMiddle('/index/selected_quoted_manage/approval.html?id='+id, '已选报价书审批','quoted_approval_flow');
    }
    //已选报价详情
    else if(type=='selected_quoted_details'){
        _es_openDilog('/index/selected_quoted_manage/details.html?id='+id,'已选报价书详情', '1200px', '95%', true, 0.2, 'selected_quoted_details');
        // _es_openDilogMiddle('/index/selected_quoted_manage/details.html?id='+id, '已选报价书详情','selected_quoted_details');
    }
    //报价详情
    else if(type=='quoted_details'){
        _es_openDilogLarge80('/index/InquiryBook/quotedInfoDetails.html?quoted_id='+id,'报价详情','quoted_details');
    }
    //修改登录密码
    else if(type=='update_user_pass'){
        _es_auto_open_dialog('/index/Index/editLoginPwd.html?type=1','修改登录密码',"450px","300px");
    }
    //上传会签文件
    else if(type=='upload_jointly_sign'){
        var url = '/index/SelectedQuotedManage/uploadJointlySign.html?id='+id+'&form='+title;
        _es_openDilog(url,'上传会签文件', '800px', '280px', true, 0.2, id);
    }
    //废标原因
    else if(type=='abolish_record'){
        if(title==null){
            title='废标原因';
        }
        _es_auto_open_dialog('/index/inquiry_book/abolishRecord.html?id='+id, title, "400px", "300px", "abolishRecord");
    }
    //废标审批
    else if(type=='abolish_approval_flow'){
        _es_openDilog('/index/inquiry_book/abolishApproval.html?id='+id, "询价书废标审批", '600px', '550px', true, 0.2, 'abolish_approval_flow');
    }
    //确认单关闭审批
    else if(type=='confirm_abolish_approval_flow'){
        _es_openDilog('/index/purchase_confirm/abolishApproval.html?id='+id, "确认单关闭审批", '600px', '550px', true, 0.2, 'confirm_abolish_approval_flow');
    }
    //供应商用户列表
    else if(type=='supplier_user_list'){
        _es_auto_open_dialog('/index/supplierManage/getSupplierUser.html?id='+id, "用户信息", "60%", "80%", "supplier_user_list");
    }
    //确认单闭合
    else if(type=='confirm_closure_approval_flow'){
        _es_openDilog('/index/purchase_confirm/closureApproval.html?id='+id, "确认单闭口审批", '650px', '550px', true, 0.2, 'confirm_abolish_approval_flow');
    }
    //登记记录
    else if(type=='register_record'){
        var url = '/index/purchase_confirm/regRecord.html?id='+id;
        _es_openDilog(url, "登记记录", '650px', '550px', true, 0.2, 'register_record'+id);
    }
    //修改用户信息
    else if(type=='update_user'){
        _es_auto_open_dialog('/index/UserManage/edit?id=' + id, "修改公司用户", "800px", "500px", "update_user");
    }
    //查看交接详情
    else if(type=='handover_details'){
        _es_auto_open_dialog('/index/UserHandover/details?id=' + id, "交接详情", "700px", "400px", "handover_details");
    }
    //考核详情
    else if(type=='sup_check_details'){

        _es_auto_open_dialog('/index/UserHandover/details?id=' + id, "交接详情", "700px", "400px", "handover_details");
    }
    //考核审批
    else if(type=='sup_check_approval'){
        _es_auto_open_dialog('/index/SupplierCheck/checkList?id=' + id+'&type=approval', "供应商考核审批", "1250px", "90%", "handover_details");

    }
    //供应商信息补录
    else if(type=='sup_info_supplement'){
        if(title==null){
            title = 'details';
        }
        _es_auto_open_dialog('/index/SupplierManage/infoSupplement?id=' + id+'&type='+title, "供应商信息补录", "950px", "90%", "handover_details");
    }
    //供应商信息补录审批
    else if(type=='sup_info_supplement_approval'){
        _es_auto_open_dialog('/index/SupplierManage/infoSupplement?id=' + id+'&type=approval', "供应商信息补录审批", "950px", "90%", "handover_details");
    }
    //查看分值详情
    else if(type=='check_score_details'){
        _es_auto_open_dialog("/index/CheckResult/index?is_check_detail=1&check_type="+title+"&check_details_type=2&type=1&sup_id="+id, "分值详情", "90%", "90%", id);
    }
    //冻结供应商
    else if(type=='supplier_frozen'){
        _es_auto_open_dialog("/index/SupplierManage/frozen?id="+id+"&frozen_type="+title, "供应商冻结/解冻", "600px", "400px", id);
    }
    //查看供应商考核分详情
    else if(type=='sup_check_score_details'){
        _es_auto_open_dialog("/index/CheckResult/index", '考核得分详情', '900px', '90%', 'showCheckResult');
    }else if(type=='inquiry_update_record'){
        if(title==null){
            title= 0;
        }
        _es_openDilog('/index/inquiry_book/getInquiryUpdateList?price_book_id='+id+'&data_type='+title, "修改记录", '60%', '65%', true, 0.2, 'sup_changes_record');
    }
    else if(type=='operation_maintenance_details'){

        if(title==null){
            title= 0;
        }

        _es_openDilog('/index/OperationMaintenance/detailsOne?id='+id+'&data_type='+title, "工单详情", '850px', '585px', true, 0.2, 'sup_changes_record');
    }
    else if(type=='submitBackupSupplement'){
        _es_openDilog('/index/BackupManage/index?inquiry_id='+id+'&type='+title, "报备记录", '700px', '450px', true, 0.2, 'submitBackupSupplement');
    }
}
//
// (function($){
//     //首先备份下jquery的ajax方法
//     var _ajax=$.ajax;
//
//     //重写jquery的ajax方法
//     $.ajax=function(opt){
//         //备份opt中error和success方法
//         var fn = {
//             error:function(XMLHttpRequest, textStatus, errorThrown){},
//             success:function(data, textStatus){}
//         }
//         if(opt.error){
//             fn.error=opt.error;
//         }
//         if(opt.success){
//             fn.success=opt.success;
//         }
//
//         //扩展增强处理
//         var _opt = $.extend(opt,{
//             error:function(XMLHttpRequest, textStatus, errorThrown){
//                 //错误方法增强处理
//                 fn.error(XMLHttpRequest, textStatus, errorThrown);
//             },
//             success:function(data, textStatus){
//                 // if(data['code']=='40001'){
//                 //     self.location=login_url;
//                 //     return;
//                 // }
//                 //成功回调方法增强处理
//                 fn.success(data, textStatus);
//             },
//             beforeSend:function(XHR){
//
//             },
//             complete:function(XHR, TS){
//
//             }
//         });
//         return _ajax(_opt);
//     };
// })(jQuery);

/**
 * 列表select修改时查询
 */
function listChangeSearch() {
    $("#btn_search").click();
}

//打开办理页面
function showUpcomingDetails(obj){
    var id = $(obj).attr('_id');
    var type = $(obj).attr('_type');
    showDetailsByType(type,id);
}

/**
 * 是否为空
 * @param value 值
 */
function empty(value){
    if(typeof(value) == "undefined"){
        return false;
    }
    if(/\S/.test(value)){
        return false;
    }
    return true;
}

//只能输入数字
function limitInputValueNumber(id) {
    var $obj =$("#"+id); //文本框jq对象

    $($obj).keyup(function(){
        var inputTxt=$(this).val();
        $(this).val(inputTxt.replace(/\D|^0/g,''));
    }).bind("paste",function(){
        var inputTxt=$(this).val();
        $(this).val(inputTxt.replace(/\D|^0/g,''));
    });
}

/**
 * 查看审批结果
 */
function showApprovalResult(type,idVal){
    var url = _show_approval_result + "?type="+type+"&id="+idVal;
    _es_openDilog(url, "查看审批结果", '880px', '90%', true, 0.2, 'approvalResult'+idVal);
}

/**
 * 提醒审批人办理
 */
function remindHandleApproval(obj,id,flowId,nodeId){
    var btnObj = $(obj);
    btnObj.attr("disabled", true);
    $.ajax({
        url: _remind_approval_handle,
        data: {'id': id,'flowId':flowId,'nodeId':nodeId},
        dataType: "JSON",
        type: 'POST',
        success: function (result) {
            if (result.code==1) {
                showvfmsg('success', '提醒成功', 1500);
                //延时60秒后才可再次提醒
                var second = 60;
                var timerFun =  function() {
                    if (second == 0) {
                        btnObj.html("提醒办理");
                        btnObj.attr("disabled",false);
                        second = 60;
                    } else {
                        btnObj.html(second + "秒后可再次提醒");
                        second--;
                        setTimeout(function() {
                            timerFun();
                        }, 1000);
                    }
                }
                timerFun();
            }else{
                btnObj.attr("disabled",false);
                showvfmsg('error', result.msg, 1500);
            }
        },
        error: function () {
            btnObj.attr("disabled",false);
            showvfmsg('error', '网络请求失败，请稍后重试！', 1500);
        }
    });
}


/*报表统计图表自定义色调*/
var colorArray = ['#199ED8','#48CDA6','#FFA500','#C1232B','#B5C334','#FCCE10','#E87C25','#27727B','#FE8463','#9BCA63','#FAD860','#F3A43B','#60C0DD','#D7504B','#C6E579','#F4E001','#F0805A','#26C0C0'];


/**
 * 获取文件格式
 * @param fileName 文件名称
 * @param [isCN] 是否返回中文值
 */
function getFileFormat(fileName){
    //是否需要返回中文名称
    var isCN = arguments[1]?arguments[1]:false;
    if(fileName==undefined || fileName==''){
        return isCN?'未知类型':'unknown';
    }
    //从文件名称中截取格式
    var fileNameArr = fileName.split('.');
    if(fileNameArr.length < 2){
        return isCN?'未知类型':'unknown';
    }
    var fileFormatVal = fileNameArr[fileNameArr.length-1].toLowerCase();
    if(isCN){
        //文件格式列表
        var fileTypeList = {
            'pdf': 'PDF文档',
            'doc|docx': 'WORD文档',
            'xls|xlsx': 'EXCEL文档',
            'ppt|ppts': 'PPT文档',
            'txt': '文本文档',
            'jpg|png|jpeg|gif|bmp': '图片文件',
            'zip|rar|7z': '压缩文件',
        };
        var isFind = false;//是否找到类型名称
        for(var item in fileTypeList){
            if(item.indexOf(fileFormatVal)!=-1){
                fileFormatVal = fileTypeList[item];
                isFind = true;
                break;
            }
        }
        if(!isFind){
            fileFormatVal = '未知类型';
        }
    }
    return fileFormatVal;
}

/**
 * 显示文件列表
 */
function showFileList (id){
    var fileListHtml = $("#fileList_"+id).html();
    art.dialog({
        lock: true,
        opacity: 0.1,	// 透明度
        title:'已上传文件列表',
        width:250,
        content: fileListHtml
    });
}


/**
 * JqGrid表格Tr点击上色
 * @param id
 */
function jqgridTableTrActive(id){
    $("#"+id+" tr").click(function(){
        var id = $(this).attr('id');
        //得到之前点击过的老颜色
        var old_color = $('[is_color_active=true]').attr('old_color');
        //当前背景色
        var curr_old_color = $('#'+id+' td').css('background');
        //已经是点击状态
        if($('#'+id+' td').attr('is_color_active')=='true'){
            return;
        }
        if(typeof(curr_old_color)!='undefined'&&curr_old_color.indexOf('rgb(243, 248, 255)')!=-1){
            curr_old_color = '#fff';
        }
        //之前点击过的颜色还原，并取消之前点击状态
        $('[is_color_active=true]').css('background',old_color).attr('is_color_active','');
        //设置颜色，并设置点击状态
        $('#'+id+' td').attr('old_color',curr_old_color).css('background','#fffcef').attr('is_color_active','true');

    });
}

$(document).ajaxComplete(function(res,a,b){
    _hide_public_loading();
    var result = a.responseJSON;
    //登录超时
    if(typeof result=="object" && typeof(result.code)!='undefined'&&result.code == 40001&&b.type!='HTML'){
        openSimpleLogin();
    }
});

function openSimpleLogin(){
    _es_auto_open_dialog('/index/Login/simpleIndex.html?is_ajax=1', "快捷登录", "500px", "400px", "openSimpleLogin");
}
