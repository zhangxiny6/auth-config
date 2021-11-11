/*=======================jqGrid.extend 扩展js===================================*/
//查询为空的默认值显示
function emptyDefVal(val, data, row) {
    var defVal = val;
    if (defVal == undefined || defVal == '') {
        defVal = '-';
    }
    return defVal;
}

/**
 * 设置行号标题
 * @param jqGridId jqGridId表格id
 * @param text    标题名称
 * @param width   宽度
 */
function setJqGridRowNumberHeaderText(jqGridId, text,width) {
    if(typeof(width)=="undefined"||width==""){
        width="38px";
    }
    if(typeof(text)=="undefined"||text==""){
        text="序号";
    }
    $('#jqgh_' + jqGridId + '_rn').prepend(text);
    $("#gridTable_rn").width(30);
    $(".jqgfirstrow").find('td').eq(0).css("width",width);
}
