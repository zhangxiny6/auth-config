
/**
 * 写入本地数据库
 * @param key 键
 * @param value 值
 */
function setLocalStorage(key, value) {
    localStorage.setItem(key,value);
}

/**
 * 读取本地数据库
 * @param name 键
 * @return 返回根据键获得的本地数据库值
 */
function getLocalStorage(name) {
    return localStorage.getItem(name);
}

if(typeof(is_simple_login)=='undefined') {
//如果能够获取到父级的元素，则说明是在子窗口里打开的登录界面，则跳转到整个浏览界面
    var pdoc = parent.document.getElementById('min_title_list');
    if (typeof(pdoc) != 'undefined' && pdoc != null && pdoc != '') {
        parent.location.href = '/';
    }
}
var username = "{:input('get.check_status')}";

/**
 * 移除本地数据库某项数据值
 * @param name 键
 * @return
 */
function removeLocalStorage(name) {
    return localStorage.removeItem(name);
}

/**
 * 页面加载时读取系统记录的登录帐号
 */
$(function(){
    //获取上一次登录的用户帐号
    var lastUserName = getLocalStorage("username");
    if(typeof (lastUserName)!='undefined' && lastUserName!='undefined' && lastUserName!=''){
        $('input[name=username]').val(lastUserName);
    }
});


$('form').submit(function () {
    login_submit();
});
//登录
function login_submit() {
    var self = $("form");
    var loginMode = $("#loginMode").val();
    if (loginMode == 1) {
        var username1_val = $('#username1').val();
        // if (username1_val == "") {
        //     login_tips('请输入手机号码');
        //     return;
        // }
        // if ($('#password').val() == "") {
        //     login_tips('请输入登录密码');
        //     return;
        // }
        // if ($('#code_gg').val() == "") {
        //     login_tips('请输入验证码');
        //     return;
        // }
        _set_localStorage('username1', username1_val);
    } else if (loginMode == 2) {
        var username2_val = $('#username2').val();
        // if (username2_val == "") {
        //     login_tips('请输入手机号码');
        //     return;
        // }
        // if ($('#verifyCode').val() == "") {
        //     login_tips('请输入短信验证码');
        //     return;
        // }
        _set_localStorage('username2', username2_val);
    } else {
        login_tips('参数错误');
    }

    //得到登录标题
    var page_title = document.title;
    $("#page_title").val(page_title);
    var _submit_data_url = $(self).attr('action');
    $.ajax({
        url: _submit_data_url,
        data: $(self).serializeArray(),
        dataType: "json",
        type: "POST",
        beforeSend: function () {
            $('.submit_btn_login').text("登录中...").attr("disabled", "disabled");
        },
        success: function (data) {
            setTimeout(function () {
                if (data.code == 1) {
                    // login_tips('登录成功');
                    if(loginMode==1){
                        if($("#rememberCB").prop('checked')){
                            setLocalStorage('username1',$.trim($('input[name=username1]').val()));
                        }else{
                            removeLocalStorage('username1');
                        }
                    }else if(login==2){
                        if($("#rememberCB").prop('checked')){
                            setLocalStorage('username2',$.trim($('input[name=username2]').val()));
                        }else{
                            removeLocalStorage('username2');
                        }
                    }
                    if(typeof(is_simple_login)!='undefined') {
                        var win=art.dialog.opener;
                        if(typeof(win.curr_page_url)!='undefined'){
                            win.location.href = win.curr_page_url;
                        }
                        art.dialog.close();
                    }else{
                        setTimeout(function () {
                            window.location.href = location.protocol +'//' + location.hostname;
                        }, 400);
                    }
                    // removeLocalStorage('username1');
                } else if (data.code == 0) {
                    $("#yan_captcha").click();
                    $('#code_gg').val(null);
                    $("#verifyCode").val(null);
                    //登录失败提示信息
                    // if (data.msg == "用户不存在") {
                    //     $("#username1").next().html('用户不存在').addClass("move");
                    // } else if (data.msg == "用户已离职，不能登录") {
                    //     $("#username1").next().html('用户已离职，不能登录').addClass("move");
                    // } else if (data.msg == "用户不存在，如果是业务员账户，请到手机端登录") {
                    //     $("#username1").next().html('用户不存在，如果是业务员账户，请到手机端登录').addClass("move");
                    // } else if (data.msg == "验证码错误") {
                    //     $("#code_gg").next().html('验证码错误').addClass("move");
                    // } else if (data.msg == "密码错误") {
                    //     $("#password").next().html('密码错误').addClass("move");
                    // } else if (data.msg == "手机验证码错误") {
                    //     $("#verifyCode").next().html('手机验证码错误').addClass("move");
                    // } else if (data.msg == "登录类别错误") {
                    //     $("#username2").next().html('登录类别错误').addClass("move");
                    // }
                    login_tips(data.msg);
                    $('.submit_btn_login').text("登录")
                    $('.submit_btn_login').removeAttr("disabled");
                } else {
                    $("#yan_captcha").click();
                    $('.submit_btn_login').removeAttr("disabled");
                    login_tips(data.msg);
                }
                is_load_submit = 0;
            }, 400);
        },
        error: function () {
            $('.submit_btn_login').text("登录")
            $('.submit_btn_login').removeAttr("disabled");
        },
        complete: function () {

        },
    });
}
/**
 *帐号  写入本地数据库
 * @param  {[type]} key   键
 * @param  {[type]} value 值
 */
function _set_localStorage(key, value) {
    localStorage.setItem(key, value);
}
/**
 * 读取本地数据库
 * @param  string name 键
 * @return string  返回根据键获得的本地数据库值
 */
function _get_localStorage(name) {
    return localStorage.getItem(name);
}
//清空本地数据库
function _remove_localStorage(name) {
    return localStorage.removeItem(name);
}
$(".menu_tab").click(function () {
    $(this).find('i').addClass('login_active').parent().siblings().find('i').removeClass("login_active");
    // console.log($(this).attr("type"));
    $(".tab-content-hi").hide();
    $("#tab-content_" + $(this).attr("type")).show();
    $('#loginMode').val($(this).attr("type"));
});
//获取验证码
var is_send_mobile = 0; //正在发送验证码
var is_load_submit = 0; //正在提交
var is_load_password_submit = 0; //找回密码提交
function send_mobile(obj) {
    if (is_send_mobile == 0) {
        is_send_mobile = 1;
        var mobile_phone = $.trim($('#username2').val());
        if (mobile_phone.length === 0) {
            login_tips('请输入手机号码');
            is_send_mobile = 0;
            return;
        }
        if (isNaN(mobile_phone)) {
            $("form").find('.check-tips').text('请输入正确的手机号码');
            login_tips('请输入正确的手机号码');
            is_send_mobile = 0;
            return;
        }
        $.ajax({
            url: _send_mobile_url,
            data: {
                mobile: mobile_phone,
                mobile_code: ""
            },
            dataType: "json",
            type: "POST",
            success: function (result) {
                setTimeout(function () {
                    if (result == null) {
                        $(obj).val('重新获取验证码');
                        is_send_mobile = 0;
                        return;
                    }
                    if (result.status == 1) {
                        login_tips('我们已发送短信验证码到' + mobile_phone);
                        RemainTime();
                    } else if (result.status == 0) {
                        login_tips(result.info);
                        // login_tips();
                        $(obj).val('获取验证码');
                        is_send_mobile = 0;
                    } else {
                        login_tips(result.info);
                        _show_base_tips(result.msg, 2000);
                        $(obj).val('获取验证码');
                        is_send_mobile = 0;
                    }
                }, 400);
            },
            error: function () {
                $(obj).html('重新获取验证码');
                is_send_mobile = 0;
            },
            complete: function () {
            }
        });
    }
}
var iTime = 59;
var Account;

function RemainTime() {
    var iSecond, sSecond = "",
        sTime = "";
    if (iTime >= 0) {
        iSecond = parseInt(iTime % 60);
        if (iSecond >= 0) {
            sSecond = iSecond + "s后可重发";
        }
        sTime = sSecond;
        if (iTime == 0) {
            clearTimeout(Account);
            sTime = '获取验证码';
            iTime = 59;
            is_send_mobile = 0;
        } else {
            Account = setTimeout("RemainTime()", 1000);
            iTime = iTime - 1;
        }
    } else {
        sTime = '没有倒计时';
    }
    $('.username_verification_code').val(sTime);
}
    //
    // //登录提示
    // function login_tips(text){
    //     $("#verifyCode").next().html(text).addClass("move");
    // }

// 登录提示
function login_tips(text) {
    $("form").find('.check-tips').text(text)
    //3秒后隐藏提示
    setTimeout(function () {
        $("form").find('.check-tips').text('');
    }, 4000);
}
/**
 * 检测当前浏览器版本
 * @return {[type]} [description]
 */
function checkCurrentBrowserVersion() {
    var browser = getBrowserInfo(); //浏览器信息
    // alert(browser);
    if (browser == 'IE') {
        //显示提示非ie浏览器访问div
        $('#login_from_div').hide();
        //加载提示界面内容填充到div里
        $.ajax({
            url: '/index.php/index/Login/brower_low',
            type: 'GET',
            dataType: 'html',
            success: function (data) {
                $('#browser_low_tips_div').show().html(data);
            }
        });
    }
}

function getBrowserInfo() {
    var agent = navigator.userAgent.toLowerCase();
    var regStr_ie = /msie [\d.]+;/gi;
    var regStr_ff = /firefox\/[\d.]+/gi;
    var regStr_chrome = /chrome\/[\d.]+/gi;
    var regStr_saf = /safari\/[\d.]+/gi;
    //IE11以下
    if (agent.indexOf("msie") > 0) {
        return 'IE'; //agent.match(regStr_ie);
    }
    //IE11版本中不包括MSIE字段
    if (agent.indexOf("trident") > 0 && agent.indexOf("rv") > 0) {
        return "IE"; // + agent.match(/rv:(\d+\.\d+)/)[1];
    }
    return '';

}

//回车调用登录
document.onkeydown = function (event_e) {
    if (window.event) {
        event_e = window.event;
    }

    var int_keycode = event_e.charCode || event_e.keyCode;
    if (int_keycode == '13') {
        //your handler function,please.
        login_submit();
        return false;
    }
}

/**
 * 移除本地数据库某项数据值
 * @param name 键
 * @return
 */
function removeLocalStorage(name) {
    return localStorage.removeItem(name);
}

/**
 * 提交表单之前的验证
 * @type {{login: jcPublic.login}}
 */
var jcPublic = {
    login: function () {
        //点击登录按钮触发
        $("#login_btn").on("click", function () {

            var type = $("#loginMode").val();
            //type为1 为用户名密码登录
            if (type == 1) {
                //获取到input的用户名 密码 验证码的值
                var username = $("#username1").val();
                var password = $("#password").val();
                var yanzm = $("#code_gg").val();

                //做逻辑判断
                if ((password.length == 0) && (username.length == 0) && (yanzm.length == 0)) {
                    //同时为空
                    $("#username1").next().addClass("move");
                    $("#password").next().addClass("move");
                    $("#code_gg").next().addClass("move");
                    return false;
                    //用户名为空
                } else if (username == "" || username == "undefined" || username == null) {
                    $("#username1").next().addClass("move");
                    return false;
                    //密码为空
                } else if (password == "" || password == "undefined" || password == null) {
                    $("#password").next().addClass("move");
                    return false;
                    //验证码为空
                } else if (yanzm == "" || yanzm == "undefined" || yanzm == null) {
                    $("#code_gg").next().html('请填写验证码').addClass("move");
                    return false;
                }

                //type不为1则为手机登录
            } else {
                var username = $("#username2").val();
                var verifyCode = $("#verifyCode").val();

                //做逻辑判断
                if ((verifyCode.length == 0) && (username.length == 0)) {
                    //同时为空
                    $("#username2").next().addClass("move");
                    $("#verifyCode").next().addClass("move");
                    return false;
                    //用户名为空
                } else if (username == "" || username == "undefined" || username == null) {
                    $("#username2").next().addClass("move");
                    return false;
                    //手机验证码为空
                } else if (verifyCode == "" || verifyCode == "undefined" || verifyCode == null) {
                    $("#verifyCode").next().html('请填写验证码').addClass("move");
                    return false;
                }
            }
        });
    }
};

//页面加载时读取用户帐号
$(function () {

    //提交表单之前的验证
    jcPublic.login();

    //如果输入框失去焦点则隐藏提示信息
    $(".form-area>div>div>input").focus(function () {
        $(this).css({"outline": "none"});
        var $this = $(this);
        $this.next("label").removeClass("move");//隐藏提示信息
    });

    //当前用户登录帐号
    var center_bin_user_name = _get_localStorage("username1");
    // console.log(center_bin_user_name);
    //用户帐号不为空
    if (typeof(center_bin_user_name) != 'undefined' && center_bin_user_name != 'undefined' && center_bin_user_name != '') {
        $('input[name=username1]').val(center_bin_user_name);
        $('input[name=username2]').val(center_bin_user_name);
    }
    //检测浏览器版本
    // checkCurrentBrowserVersion();
})