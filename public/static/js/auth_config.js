
$(function(){
    GetGrid();
});

function GetGrid() {
    jqGrid = $("#gridTable").jqGrid({
        url: listUrl,
        datatype: "json",
        colNames: ["名称", '所属模块', '文件路径','排序', '操作'], //列
        colModel: [ // 行 jqGrid每一列的配置信息。包括名字，索引，宽度,对齐方式.....
            {name: 'name', index: 'name', width: 50, align: "center", sortable: false},
            {name: 'module', index: 'module', width: 40, align: "center", sortable: false,
                hidden:menu_field_module==''?true:false},
            {name: 'file_path', index: 'file_path', width: 120, align: "center", sortable: false},
            {name: 'sort', index: 'sort', width: 30, align: "center",},
            {name: 'id', index: 'id', width: 100, align: "center", sortable: false}
        ],
        autowidth: true, //自动调整宽度
        //width:1000,
        height: $(window).height(),
        //分页部分
        rowList: [10, 20, 30, 40], //可供用户选择一页显示多少条
        pager: '#gridPager', //分页的容器
        viewrecords: true, //定义是否在Pager Bar中显示记录数信息。
        recordpos: "center",
        pagerpos: "right", //设置分页容器的位置
        //pgtext ：显示当前页码状态的字符串，这个与所选用的language文件有关，具体的文本格式定义在里面。例如默认的英文格式就是“Page {0} of {1}”，其中{0}代表当前载入的页码；{1}代表页码的总数。
        //分页结束
        sortorder: "desc", //排序方式,可选desc,asc
        sortname: "sort", //默认排序字段
        mtype: "post", //向后台请求数据的ajax的类型。可选post,get
        viewrecords: true,
        rownumbers: true, // 如果为ture则会在表格左边新增一列，显示行顺序号，从1开始递增。此列名为'rn'.
        jsonReader: {
            //很重要 定义了 后台分页参数的名字
            /*
             * root 返回的行数据
             * page  当前页
             * total 总页数
             * records 每数据
             * repeatitems   指明每行的数据是可以重复的，如果设为false，则会从返回的数据中按名字来搜索元素，这个名字就是colModel中的名字
             * id  行id
             * */
            root: "data",
            page: "sumpage",
            total: "totalPagesNumber",
            records: "totalPages",
            cell: "cell",
            id: "id",
            userdata: 'userData',
            repeatitems: false, // 如果设为false，则jqGrid在解析json时，会根据name来搜索对应的数据元素（即可以json中元素可以不按顺序）；而所使用的name是来自于colModel中的name设定。
        },
        loadComplete: function() {
            var records = jqGrid.getGridParam('records');
            if (records == 0) { //追加一条空数据提示数据
                var colLength = jqGrid.getGridParam('colNames').length;
                var trStr = "<tr class='ui-widget-content jqgrow ui-row-ltr'><td class='empty-tr-info ' colspan='" + colLength + "'>没有查询记录！</td></tr>";
                $("#gridTable").find("tbody").append(trStr);
            }
        },
        gridComplete: function() {
            //当表格所有数据都加载完成而且其他的处理也都完成时触发此事件，排序，翻页同样也会触发此事件
            //在此事件中循环为每一行添加修改和删除链接
            var ids = jQuery("#gridTable").jqGrid('getDataIDs'); // 返回当前grid里所有数据的id
            for (var i = 0; i < ids.length; i++) {
                var id = ids[i]; //
                configMenuUrl = configMenuUrl + '' + id;
                var rowData = $('#gridTable').jqGrid('getRowData',id); //获得本列对象
                var strmodify = "<a href='javascript:addOrEdit(this," + rowData.id + ");'><i class='fa fa-pencil'></i>修改</a> | ";
                var strmenu = "<a target='_blank' href='"+configMenuUrl+"'><i class='fa fa-cog'></i>配置菜单</a> | ";
                var del = "<a href='javascript:delData(" + rowData.id + ");' class='del-c'><i class='fa fa-trash'></i>删除</a>";

                jQuery("#gridTable").jqGrid('setRowData', ids[i], {
                    id: strmodify + strmenu + del
                });
            }
            //数据加载完成初始化高度
            initialMainGirdTablePage();
        }
    }).navGrid('#pager1', {
        edit: false,
        add: false,
        del: false
    });
}

/*修改*/
function addOrEdit(obj) {
    var url = addUrl;
    // 获取调用这个方法时传递过来的参数，特殊的对象形式
    var id = arguments[1];
    var title = '新增模块';
    if (id != undefined) { //如果id存在
        title = '修改模块';
        url = addUrl+ id;
    }
    // 弹出新窗口
    _es_openDilog(url,title, '800px', '500px', true, 0.2, 'dmini');
}

/* 删除 */
function delData(data_id){
    art.dialog.confirm('您确认删除吗？',function(){
        $.ajax({
            url:delUrl,
            data:{'id':data_id},
            dataType:"json",
            type:'GET',
            success:function(data){
                if(data.code){
                    //刷新grid数据列表
                    refreshPage();
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

/*控制器列表*/
function getClassAuth(auth_config_id,name){
    _es_openDilogLarge(getClassAuthUrl+auth_config_id, '控制器列表('+name+')');
}

/*菜单配置*/
function openConfigMenu(auth_config_id,name){
    _es_openDilogLarge900(configMenuUrl+auth_config_id, '配置菜单('+name+')');
}

/*更新权限*/
function updateAuth(){
    art.dialog.confirm('您确认更新吗?', function() {
        // 弹出新窗口
        _es_openDilogLarge(updateUrl, '权限更新');
    });
}

/*菜单配置*/
function _audit_withdraw(){
    window.location.href = menuConfigUrl;
}