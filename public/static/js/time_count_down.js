/**
 * Created by xwj on 2017/11/2.
 */
//格式化结果
function formatTimeTxt(type,day,hour,minute,seconds){
    var result = '';
    if(type==1){
        //第一种格式(D天HH时MM分SS秒)
        if(day){
            result = '<span>'+day+'<i>天</i></span>';
        }
        result += '<span>'+("0" + hour.toString()).substr(-2)+'<i>时</i></span>';
        result += '<span>'+("0" + minute.toString()).substr(-2)+'<i>分</i></span>';
        result += '<span>'+("0" + seconds.toString()).substr(-2)+'<i>秒</i></span>';
    }else if(type==2){
        //第二种格式(日历模式)
        if(day){
            result = '<label class="day">'+day+'</label><em>:</em>';
        }
        result += '<label class="hour">'+("0" + hour.toString()).substr(-2)+'</label><em>:</em>';
        result += '<label class="mini">'+("0" + minute.toString()).substr(-2)+'</label><em>:</em>';
        result += '<label class="sec">'+("0" + seconds.toString()).substr(-2)+'</label>';
    }else{
        //默认格式(HH:mm:ss)
        if(day){
            hour += day * 24;
        }
        result = hour;
        if(hour<10){
            result = ("0" + hour.toString()).substr(-2);
        }
        result += ':'+("0" + minute.toString()).substr(-2)+':'+("0" + seconds.toString()).substr(-2);
    }
    return result;
}
var timerArray = [];//定时器数组
//倒计时开始
function timeDownFun(type,target_id,date_end,endFun){
    var date_now = new Date($.ajax({
        url:'/index/InquiryBook/inquiryTimeAjax.html',
        async: false}).getResponseHeader("Date")).getTime();
    var date_between = (date_end-date_now)/1000-2;// 计算时间间隔
    // 计算天
    var day_tmp = (date_between/(3600*24)).toString();
    var day_for_hour = (date_between%(3600*24));
    var day = parseInt(day_tmp,10)>0?parseInt(day_tmp,10):0;// 计算倒计时天
    // 计算倒计时小时
    var hour_tmp = (day_for_hour/3600).toString();
    var hour_for_minute = ((date_between%(3600*24))%3600);
    var hour = parseInt(hour_tmp,10)>0?parseInt(hour_tmp,10):0;// 计算倒计时天
    // 倒计时算分钟跟秒
    var minute_tmp = (hour_for_minute/60).toString();
    var seconds_tmp = (hour_for_minute%60).toString();
    var minute = parseInt(minute_tmp,10)>0?parseInt(minute_tmp,10):0;// 计算倒计时天
    var seconds = parseInt(seconds_tmp,10)>0?parseInt(seconds_tmp,10):0;// 计算倒计时天
    // 初始化时间设置
    $(target_id).html(formatTimeTxt(type,day,hour,minute,seconds));
    var tid = setInterval(function () {
        var oTimeBox = $(target_id);
        var syTime = oTimeBox.text();
        var totalSec = getTotalSecond(syTime) - 1;
        if (totalSec >= 0) {
            var new_time = getNewSyTime(type,totalSec);

            oTimeBox.html(new_time);
            new_time = new_time.replace(/<.*?>/ig,"");
            oTimeBox.attr('title',new_time);
        } else {
            //刷新页面
            $("#btn_search").click();
            clearInterval(tid);
            if(endFun) {
                //执行页面回调函数
                endFun();
            }
        }
    }, 1000);
    timerArray.push(tid);
}

//根据剩余时间字符串计算出总秒数
function getTotalSecond(timeStr) {
    var reg = /\d+/g;
    var timeNum = new Array();
    var r;
    while ((r = reg.exec(timeStr)) != null) {
        r = r.toString();
        timeNum.push(parseInt(r,10));
    }
    var second = 0, i = 0;
    if (timeNum.length == 4) {
        second += timeNum[0] * 24 * 3600;
        i = 1;
    }
    second += timeNum[i] * 3600 + timeNum[++i] * 60 + timeNum[++i];
    return second;
}
//根据剩余秒数生成时间格式
function getNewSyTime(type,sec) {
    var s = sec % 60;
    sec = (sec - s) / 60; //min
    var m = sec % 60;
    sec = (sec - m) / 60; //hour
    var h = sec % 24;
    var d = (sec - h) / 24;//day
    return formatTimeTxt(type,d,h,m,s);
}
//清除浏览器定时器
function clearBrowserTimer(){
    for(var i=0,len=timerArray.length;i<len;i++){
        clearInterval(timerArray[i]);
    }
}