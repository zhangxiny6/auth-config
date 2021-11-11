
$(function() {
    var fromchk = $(".vform").Validform({
        tiptype: 2,
        tiptype: function(msg, o, cssctl) {

            if (!o.obj.is("form")) {
                if (o.type == 2) {
                    var parent_ele = o.obj.attr('parent_ele');
                    if (typeof parent_ele === 'undefined') {
                        //code
                        // var objtip = o.obj.siblings(".Validform_checktip");
                        var objtip = o.obj.parent().parent('.form-group');
                        objtip.removeClass('has-error').addClass('has-success');
                    } else {
                        var objtip = o.obj.parent().parent('.form-group');
                        objtip.removeClass('has-error').addClass('has-success');
                    }
                } else {
                    var parent_ele = o.obj.attr('parent_ele');
                    if (typeof parent_ele === 'undefined') {
                        // var objtip=o.obj.siblings(".Validform_checktip");
                        var objtip = o.obj.parent().parent('.form-group');
                    } else {
                        //var objtip=o.obj.parent().siblings(".Validform_checktip");
                        var objtip = o.obj.parent().parent('.form-group');
                    }
                    objtip.addClass('has-error');
                    //var objtip=o.obj.next().siblings(".Validform_checktip");
                    // cssctl(objtip,o.type);
                    // objtip.text(msg);
                    showvfmsg('0', msg, 1900);
                   
                }
                // o.type ==2//验证成功
            } else {
                $("#submit").attr("disabled", 'disabled');
                $("#submit").html("提交中...");
                $(".bt_submit").attr("disabled", 'disabled');
                $(".bt_submit").html("提交中...");
                showvfmsg("-1", '提交中，请稍后...', 999999);
            }
        },
        beforeSubmit:function(){
            if(typeof(validform_submit_fun)!=undefined&&typeof(validform_submit_fun)=='function'){
                return validform_submit_fun();
            }
        },
        datatype: {
            "idcard": function(gets, obj, curform, datatype) {
                if (gets.length == 15) {
                    return isValidityBrithBy15IdCard(gets);
                } else if (gets.length == 18) {
                    var a_idCard = gets.split(""); //得到身份证数组
                    if (isValidityBrithBy18IdCard(gets) && isTrueValidateCodeBy18IdCard(a_idCard)) {
                        return true;
                    }
                    return false;
                }
                return false;
            }
        },
        ajaxPost: true,
        tipSweep: true,
        callback: function(data) {
            if (data.code) {
                setTimeout(function() {
                    if (data.url == "0") {
                        window.location.reload();
                    } else if (typeof(data.url) != 'undefined' && data.url != "") {
                        window.location.href = data.url;
                    }
                }, 1300);
            } else {}
            setTimeout(function(){
                $("#submit").removeAttr("disabled");
                $("#submit").html("保 存");
                $(".bt_submit").removeAttr("disabled");
                $(".bt_submit").html("提交");
            },1000);

            var retstatus = data.code == 1 ? "1" : "0";
            var msg=data.msg||'网络请求连接超时，请刷新后重试';
            if(data.code==0){ 
                //隐藏右上角滑动窗口
                notifit_dismiss();
                art_alert(msg);
            }else{
                if(typeof(validform_sub_sus_fun)!=undefined&&typeof(validform_sub_sus_fun)=='function'){
                    validform_sub_sus_fun(data);
                }
                showvfmsg(retstatus, msg, 2900);
            }
            
        }

    });

});
var Wi = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1]; // 加权因子;
var ValideCode = [1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2]; // 身份证验证位值，10代表X;
function isTrueValidateCodeBy18IdCard(a_idCard) {
    var sum = 0; // 声明加权求和变量
    if (a_idCard[17].toLowerCase() == 'x') {
        a_idCard[17] = 10; // 将最后位为x的验证码替换为10方便后续操作
    }
    for (var i = 0; i < 17; i++) {
        sum += Wi[i] * a_idCard[i]; // 加权求和
    }
    valCodePosition = sum % 11; // 得到验证码所位置
    if (a_idCard[17] == ValideCode[valCodePosition]) {
        return true;
    }
    return false;
}

function isValidityBrithBy18IdCard(idCard18) {
    var year = idCard18.substring(6, 10);
    var month = idCard18.substring(10, 12);
    var day = idCard18.substring(12, 14);
    var temp_date = new Date(year, parseFloat(month) - 1, parseFloat(day));
    // 这里用getFullYear()获取年份，避免千年虫问题
    if (temp_date.getFullYear() != parseFloat(year) || temp_date.getMonth() != parseFloat(month) - 1 || temp_date.getDate() != parseFloat(day)) {
        return false;
    }
    return true;
}

function isValidityBrithBy15IdCard(idCard15) {
    var year = idCard15.substring(6, 8);
    var month = idCard15.substring(8, 10);
    var day = idCard15.substring(10, 12);
    var temp_date = new Date(year, parseFloat(month) - 1, parseFloat(day));
    // 对于老身份证中的你年龄则不需考虑千年虫问题而使用getYear()方法
    if (temp_date.getYear() != parseFloat(year) || temp_date.getMonth() != parseFloat(month) - 1 || temp_date.getDate() != parseFloat(day)) {
        return false;
    }
    return true;
}