
$("#controller_db").select2({
    language: "zh-CN",
});
$("#controller_db").on("change",function(){
    dbChange();
});
$("#controller_menu").select2({
    language: "zh-CN",
});
$("#controller_menu").on("change",function(){
    tableChange();
});
$("#menu_id").select2({
    language: "zh-CN",
});
$("#menu_title").select2({
    language: "zh-CN",
});
$("#menu_url").select2({
    language: "zh-CN",
});
$("#menu_sort").select2({
    language: "zh-CN",
});
$("#menu_pid").select2({
    language: "zh-CN",
});
$("#menu_module").select2({
    language: "zh-CN",
});
//菜单字段配置
var menuConfig = [
    {
        id:"menu_id",
        old_field:"id"
    },
    {
        id:"menu_title",
        old_field:"title"
    },
    {
        id:"menu_url",
        old_field:"url"
    },
    {
        id:"menu_sort",
        old_field:"sort"
    },
    {
        id:"menu_pid",
        old_field:"pid"
    },
    {
        id:"menu_module",
        old_field:"module"
    },
    {
        id:"menu_status",
        old_field:"stat"
    }
];
/**
 * 数据表变更
 */
function tableChange() {
    var table = $("#controller_menu").val();
    var db = $("#controller_db").val();
    if (table == 0) {
        for (var index in menuConfig) {
            var data = menuConfig[index];
            $("#" + data['id']).html('');
            $("#" + data['id']).append('<option value="">==请先选择数据表==</option>');
        }
        return;
    }
    $.ajax({
        url: getFieldsByTable,
        data: {
            table: table,
            db: db
        },
        type: "POST",
        dataType:'JSON',
        success: function(res) {
            if (res.code == 400) {
                showvfmsg('error', res.msg, 10000);
            } else {
                for (var index in menuConfig) {
                    var data = menuConfig[index];
                    var fieldObj = $("#" + data['id']);
                    fieldObj.html('');
                    fieldObj.append('<option value="">==请选择==</option>');
                    var fieldArr = res.data;
                    var value = '';
                    for (var index in fieldArr) {
                        var option_value = fieldArr[index]['COLUMN_NAME'];
                        var name = fieldArr[index]['COLUMN_NAME'] + ' [' + fieldArr[index]['COLUMN_COMMENT'] + ']';
                        fieldObj.append('<option value="'+ option_value +'">'+name+'</option>');
                        if (option_value.indexOf(data['old_field']) != -1) {
                            value = option_value;
                        }
                    }
                    if (value != '') {
                        fieldObj.val(value).trigger("change");
                    } else {
                        fieldObj.val('0').trigger("change");
                    }
                }
            }
        }
    });
}

/**
 * 数据库变更
 */
function dbChange() {
    var db = $("#controller_db").val();
    var obj= $("#controller_menu").select2();
    if (db == 0) {
        obj.html('');
        obj.append('<option value="0">==请先选择数据库==</option>');
        obj.val('0').trigger("change");
        return;
    }
    $.ajax({
        url: getTablesByDb,
        data: {
            db: db
        },
        type: "POST",
        dataType:'JSON',
        success: function(data) {
            if (data.code == 400) {
                showvfmsg('error', data.msg, 15000);
            } else {
                obj.html('');
                obj.append('<option value="0">==请选择==</option>');
                var tableArr = data.data;
                var value = '';
                for (var index in tableArr) {
                    var name = tableArr[index]['TABLE_NAME'];
                    obj.append('<option>'+name+'</option>');
                    if (name.indexOf('menu') != -1) {
                        value = name;
                    }
                }
                if (value != '') {
                    obj.val(value).trigger("change");
                    $("#menuAuthChoose").show().delay(5000).fadeOut();
                } else {
                    obj.val('0').trigger("change");
                }
            }
        }
    });
}

/**
 * 第3步的验证
 */
function steps3Validate() {
    var formObj = $("#create-db-form");
    $("#next-btn").attr('disabled',true);
    $("#next-btn").text('提交中……');
    $.ajax({
        url: formObj.attr('action'),
        data: formObj.serializeArray(),
        type: "POST",
        dataType:'JSON',
        success: function(data) {
            $("#next-btn").text('下一步');
            $("#next-btn").attr('disabled',false);
            if (data.code == 400) {
                showvfmsg('error', data.msg, 10000);
            } else {
                $("#controller_db").html('');
                var data = data.data.dbArr;
                $("#controller_db").append('<option value="0">==请选择==</option>');
                for (var index in data) {
                    $("#controller_db").append('<option>'+data[index]['Database']+'</option>');
                }
                changeSteps(1, false);
            }
        }, error:function() {
            showvfmsg('error', '数据库连接失败', 10000);
            $("#next-btn").text('下一步');
            $("#next-btn").attr('disabled',false);
        }
    });
}

/**
 * 第4步的验证
 */
function steps4Validate() {
    var menu_id = $.trim($("#menu_id").val());
    if (menu_id == 0) {
        showvfmsg('error', '请选择菜单ID字段', 3000);
        return;
    }
    var menu_title = $.trim($("#menu_title").val());
    if (menu_title == 0) {
        showvfmsg('error', '请选择菜单名称字段', 3000);
        return;
    }
    var menu_url = $.trim($("#menu_url").val());
    if (menu_url == 0) {
        showvfmsg('error', '请选择菜单地址字段', 3000);
        return;
    }
    var menu_sort = $.trim($("#menu_sort").val());
    if (menu_sort == 0) {
        showvfmsg('error', '请选择排序字段', 3000);
        return;
    }
    var menu_pid = $.trim($("#menu_pid").val());
    if (menu_pid == 0) {
        showvfmsg('error', '请选择父级字段', 3000);
        return;
    }
    var auth_authName = $.trim($("#auth_authName").val());
    if (auth_authName == '') {
        showvfmsg('error', '请输入权限名称', 3000);
        return;
    }
    var formObj = $("#create-auth-form");
    $("#next-btn").attr('disabled',true);
    $("#next-btn").text('提交中……');
    $.ajax({
        url: formObj.attr('action'),
        data: formObj.serializeArray(),
        type: "POST",
        dataType:'JSON',
        success: function(data) {
            $("#next-btn").text('下一步');
            $("#next-btn").attr('disabled',false);
            if (data.code == 400) {
                showvfmsg('error', data.msg, 10000);
            } else {
                changeSteps(1, false);
                $("#next-btn").hide();
            }
        }
    });
}

/**
 * 下一步
 * @param stepNum 步数
 */
function changeSteps(stepNum, checked){
    $("#next-btn").show();
    var thisObj = $('.steps-content .body.current');
    var curStepNum = parseInt($("#curStepNum").val());
    var stepObj = null;//待显示的步骤对象
    if(curStepNum==2 && stepNum == 1 && checked) {
        if ($("span[error_code=1]").html() != undefined) {
            showvfmsg('error', '文件无权限，请检查！', 3000);
            return;
        }
    }
    if(curStepNum==3 && stepNum == 1 && checked) {
        steps3Validate();
        return;
    }
    if(curStepNum==4 && stepNum == 1 && checked) {
        steps4Validate();
        return;
    }
    if(stepNum==-1){
        //上一步
        stepObj = thisObj.prev('section');
        curStepNum -= 1;
    }else{
        //下一步
        stepObj = thisObj.next('section');
        curStepNum += 1;
    }
    if(stepObj != null && stepObj.length > 0) {
        stepObj.addClass('current').siblings().removeClass('current');
        if(curStepNum==1){
            $("#prev-btn").hide();
        }else{
            $("#prev-btn").show();
        }
        $("#curStepNum").val(curStepNum);
        step.goStep(curStepNum)
    }
}