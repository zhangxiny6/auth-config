/**
 * 权限修改
 */
function authTypeChange(){
    $("#btn_search").click();
}

$(function(){
    // setTimeout(function () {
        GetGrid();
    // },300);

    //搜索
    $("#btn_search").click(function(){
        loadingData('');
    });
});

function loadingData(is_clear) {
    var formData = $("#form_search").serializeArray();
    var formDataArray = new Array();
    var objFormData = {};
    $.each(formData, function (i, value) {
        objFormData[value["name"]] = value["value"];
    });
    $("#gridTable").jqGrid('setGridParam', {
        url: listUrl,//你的搜索程序地址
        datatype: 'json',
        postData: objFormData, //发送数据
        page: 1,
    }).trigger("reloadGrid"); //重新载入
}

var jqGrid = null;
function GetGrid(){
    jqGrid = jQuery("#gridTable").jqGrid(
        {
            url: listUrl,
            datatype : "json",
            colNames : [ '权限组名称','权限组类别', '组描述', '启用状态','操作',''], //列
            colModel : [ // 行 jqGrid每一列的配置信息。包括名字，索引，宽度,对齐方式.....
                {name : 'title',index : 'title',width : 90,align:"center"},
                {name : 'module_name',index : 'module_name',width : 90,align:"center", sortable: false },
                {name : 'description',index : 'description',width : 100,align:"center"},
                {name : 'status',index : 'status',width : 80,align : "center",formatter:statusFormat},
                { name: 'opera', index: 'opera', width: 60, align: "center", sortable: false },
                { name: 'id', index: 'id', hidden: true },
            ],
            autowidth : true, //自动调整宽度
            //分页部分
            rowList : [20,30,40,50,100],//可供用户选择一页显示多少条
            pager : '#gridPager',//分页的容器
            viewrecords : true, //定义是否在Pager Bar中显示记录数信息。
            recordpos:"center",
            pagerpos:"right", //设置分页容器的位置
//                         pgtext ：显示当前页码状态的字符串，这个与所选用的language文件有关，具体的文本格式定义在里面。例如默认的英文格式就是“Page {0} of {1}”，其中{0}代表当前载入的页码；{1}代表页码的总数。
            //分页结束
            sortorder : "asc",//排序方式,可选desc,asc
            sortname:"id",//默认排序字段
            mtype : "post",//向后台请求数据的ajax的类型。可选post,get
            viewrecords : true,
            multiselect: true, //checkbox多选操作
            multiselectWidth: 25,// 左侧列宽
            jsonReader: {
                root: "data", //返回的数据集
                page: "currPage", //当前页
                total: "sumpage", //总页数
                records: "totalPages", //总条数
                cell: "cell",
                id : "id",
                repeatitems: false, // 如果设为false，则jqGrid在解析json时，会根据name来搜索对应的数据元素（即可以json中元素可以不按顺序）；而所使用的name是来自于colModel中的name设定。
            },
            loadComplete: function () {
                var records = jqGrid.getGridParam('records');
                if (records == 0) {  //追加一条空数据提示数据
                    var colLength = jqGrid.getGridParam('colNames').length;
                    var trStr = "<tr class='ui-widget-content jqgrow ui-row-ltr'><td class='empty-tr-info ' colspan='" + colLength + "'>没有查询记录！</td></tr>";
                    $("#gridTable").find("tbody").append(trStr);
                }
            },
            gridComplete: function () {
                // $("#gridTable").setLabel('gridTable_rn', '序号');
                var ids=jQuery("#gridTable").jqGrid('getDataIDs');
                for(var i=0; i<ids.length; i++){
                    var id=ids[i];
                    var rowData = $('#gridTable').jqGrid('getRowData',id); //获得本列对象
                    var getAccessBtn = "<a href='javascript:;'  group_id='" + rowData.id + "'  onclick='authForGroup(" + rowData.id + ",\""+rowData.title+"\")' ><i class='fa fa-wrench'></i>授权</a> | ";
                    var updateBtn = "<a href='javascript:;' group_id='" + rowData.id + "'  onclick='addOrEdit(" + rowData.id + ")'><i class='fa fa-pencil'></i>修改</a>";
                    var delBtn = '';
                    jQuery("#gridTable").jqGrid('setRowData',ids[i],
                        {
                            opera:getAccessBtn+updateBtn+delBtn
                        }
                    );
                }
                //数据加载完成初始化高度
                initialMainGirdTablePage();
            }
        }).navGrid('#pager1', {
        edit : false,
        add : false,
        del : false
    });
}

//授权
function authForGroup(data_id,authTitle){
    var module = $("#auth_type").val();
    var url = authorizeGroupUrl+data_id+'&module='+module;
    var title = '用户组授权('+authTitle+')';
    _es_auto_open_dialog(url,title,'750px', '90%', 'add');
}

//新增和修改
function  addOrEdit(data_id){
    var module = $("#auth_type").val();
    var url=addAuthUrl+module;
    var title='新增权限组';
    if (typeof (data_id)!='undefined'&& parseInt(data_id)> 0) {
        title='修改权限组';
        url=editAuthUrl+data_id;
    }
    _es_auto_open_dialog(url,title,'750px','410px','add');
}

//删除用户组
function delData(data_id){
    art.dialog.confirm('您确认删除吗?删除之后将不能恢复!',function(){
        $.ajax({
            url:deleteAuthUrl,
            data:{
                'group_id':data_id,
                'module':$("#module").val()
            },
            dataType:"json",
            type:'GET',
            success:function(data){
                if(data.code){
                    //刷新grid数据列表
                    showvfmsg('success',data.msg,1500);
                }else{
                    showvfmsg('error',data.msg,1500);
                }
            },
            error:function(){
                alert('网络请求失败,请稍后重试!');
            }
        });
    });
}

//状态格式化
function statusFormat(cellvalue) {
    var str = '<span class="label label-invalid label-wi">已禁用</span>';
    if(cellvalue == 1 ){
        str='<span class="label label-success label-wi">已启用</span>';
    }
    return str;
}

//启用或禁用管理员
function changeUserStatus(status) {
    var arrId = [];
    var arrRowIndex = jQuery("#gridTable").jqGrid('getGridParam', 'selarrrow');
    if (arrRowIndex.length > 0) {
        $.each(arrRowIndex, function (i, item) {
            //获取选中行的对象
            var rowObj = jQuery("#gridTable").jqGrid('getRowData', item);
            arrId.push(rowObj.id);
        });
        if (arrId.length > 0) {
            $.ajax({
                url: changeUserStatusUrl,
                data: {
                    'id': arrId,
                    'status': status,
                    'module':$("#module").val()
                },
                type: 'GET',
                dataType: 'Json',
                success: function (data) {
                    if (data.code) {
                        showvfmsg('success', data.msg, 1500);
                        //刷新grid数据列表
                        refreshPage();
                    } else {
                        showvfmsg('success', data.msg, 1500);
                    }
                }, error: function () {
                    alert("服务器请求失败,请稍后重试!");
                }
            })
        }
    } else {
        showvfmsg('error', '请选择需要操作的列', 1500);
    }
}