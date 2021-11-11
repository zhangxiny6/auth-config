$(function(){
    GetGrid();
    //搜索
    $("#btn_search").click(function(){
        loadingData('');
    });

    //选择模块
    $('#module_name').on('change',function(){
        $('#pid').val('0');
        commonShow(0,'');
        loadingData();
    });

    $(document).on('click','ul.item-list-tabs li',function(){
        $(this).addClass('current').siblings().removeClass('current');
        //所属端
        var module_name = $(this).attr('code');
        //所属模块
        var application_module = $(this).attr('application_module');

        if( is_load_list == 1 || curShowListType == module_name&&curApplicationModule==application_module){
            return;
        }
        is_load_list = 1;
        curShowListType = module_name;
        curApplicationModule = application_module;
        $('#pid').val('0');
        $('#module_name').val(module_name);
        $('#application_module').val(application_module);

        commonShow(0,'');
        loadingData(1);
    });

});

function loadingData(is_clear){
    if( is_clear != '' ){
        //$("#form_search").find('input[type=text]').val('');
    }
    var formData = $("#form_search").serializeArray();
    var  formDataArray=new Array();
    var objFormData = {};
    $.each(formData,function(i,value) {
        objFormData[value["name"]]=value["value"];
    });
    $("#gridTable").jqGrid('setGridParam',{
        url:listUrl,//你的搜索程序地址
        datatype:'json',
        postData:objFormData, //发送数据
        page:1,
    }).trigger("reloadGrid"); //重新载入
    is_load_list = 0;
}

var jqGrid = null;
function GetGrid(){
    jqGrid = $("#gridTable").jqGrid(
        {
            url: listUrl,
            datatype : "json",
            colNames : [ '菜单标题', "上级菜单","上级菜单id",'菜单地址','排序','是否显示', '是否启用','操作'], //列
            colModel : [ // 行 jqGrid每一列的配置信息。包括名字，索引，宽度,对齐方式.....
                {name : 'title',index : 'title',width : 100,align:"center",sortable:false},
                {name : 'p_title',index : 'p_title',width : 100,align:"center",formatter:emptyDefVal},
                {name : 'pid',index : 'pid',width : 80,align : "center",hidden:true },
                { name: 'url', index: 'url', width: 120, align : "center",formatter: emptyDefVal },
                {name : 'sort',index : 'sort',width : 35,align : "center"},
                {name : 'isauthshow',index : 'isauthshow',width : 50,align : "center",formatter:statusFormat,sortable:false},
                {name : 'status',index : 'status',width : 50,align : "center",formatter:statusFormat,sortable:false},
                {name : 'id',index : 'id',width :120,align : "center",sortable:false},
            ],
            autowidth : true, //自动调整宽度
//                         width:1000,
            //分页部分
            rowList : [5,10,20,30,40,50,100],//可供用户选择一页显示多少条
            pager : '#gridPager',//分页的容器
            viewrecords : true, //定义是否在Pager Bar中显示记录数信息。
            recordpos:"center",
            pagerpos:"right", //设置分页容器的位置
//                         pgtext ：显示当前页码状态的字符串，这个与所选用的language文件有关，具体的文本格式定义在里面。例如默认的英文格式就是“Page {0} of {1}”，其中{0}代表当前载入的页码；{1}代表页码的总数。
            //分页结束
            sortorder : "asc",//排序方式,可选desc,asc
            sortname:"sort",//默认排序字段
            mtype : "post",//向后台请求数据的ajax的类型。可选post,get
            viewrecords : true,
            rownumbers:true,   // 如果为ture则会在表格左边新增一列，显示行顺序号，从1开始递增。此列名为'rn'.
            caption : "<div style='display:inline-block;'><a style='display:none;margin-right:15px;' id='back_a' href='javascript:;' onclick='goBack();' >返回上一级</a>当前位置：<font id='menu_content'>顶级菜单</font></div>",//表格的标题名字
            jsonReader: {
                //很重要 定义了 后台分页参数的名字
                root: "data", //返回的数据集
                page: "currPage", //当前页
                total: "sumpage", //总页数
                records: "totalPages", //总条数
                cell: "cell",
                id: "id",
                userdata:'userData',
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
            gridComplete:function(){
                //当表格所有数据都加载完成而且其他的处理也都完成时触发此事件，排序，翻页同样也会触发此事件
                //在此事件中循环为每一行添加修改和删除链接
                var ids=jQuery("#gridTable").jqGrid('getDataIDs'); // 返回当前grid里所有数据的id
                for(var i=0; i<ids.length; i++){
                    var id=ids[i]; //
                    var rowData = $('#gridTable').jqGrid('getRowData',id); //获得本列对象
                    var goChildren = "<a href='#' menu_id='"+rowData.id+"' title='"+rowData.title+"' p_title='"+rowData.p_title+"' pid='"+rowData.pid+"' onclick='getChilds(this,"+rowData.id+")'>查看子菜单</a> | ";
                    var strmodify = "<a href='#' menu_id='" + rowData.id + "' onclick='addOrEdit(" + rowData.id + ")'><i class='fa fa-pencil'></i>修改</a> | ";
                    var strdel = "<a href='#' menu_id='" + rowData.id + "' class='del-c' onclick='delData(" + rowData.id + ")' ><i class='fa fa-trash'></i>删除</a>";
                    jQuery("#gridTable").jqGrid('setRowData',ids[i],
                        {
                            id:goChildren+strmodify+strdel
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

/* 查看子菜单 */
//当前的pid
var cur_pid = 0;
//当前的父title
var cur_title = '';

function getChilds(obj,data_id){
    $('#pid').val(data_id);
    var title = $(obj).attr('title');
    var pid = $(obj).attr('pid');
    var p_title = $(obj).attr('p_title');
    commonShow(1,title);
    //加载数据
    $("input[name='searchinfo']").val("");  //清空搜素的值
    loadingData();
}

//common function
function commonShow(pid,title){
    var html_content ='顶级菜单';
    if( parseInt(pid) == 0 ){
        $('#back_a').hide();
    }else{
        html_content='【'+title+'】>> 子菜单管理';
        $('#back_a').show();
    }
    $('#menu_content').html(html_content);
}

/* 回退上一级 */
function goBack(){
    var userdata = $("#gridTable").getGridParam('userData');
    cur_pid = userdata.curPid;
    cur_title = userdata.curPtitle;
    $('#pid').val(cur_pid);
    commonShow(cur_pid, cur_title);
    //加载数据
    loadingData();
}

/*新增和修改*/
function  addOrEdit(data_id){
    var module_code = $('#module_name').val();
    var url=addMenuUrl+$('#pid').val()+"&module_code="+module_code;
    var title='新增菜单';
    if (typeof data_id!='undefined' && data_id>0) {
        title='修改菜单';
        url=editMenuUrl+data_id;
    }
    _esOpendialogmini(url,title);
}
/* 删除菜单  该操作会级联删除该菜单的子菜单,并且 */
function delData(data_id){
    art.dialog.confirm('删除之后不能恢复!您确认删除吗?',function(){
        $.ajax({
            url:delMenuUrl,
            data:{'menu_id':data_id},
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

//状态格式化
function statusFormat(cellvalue) {
    var str = '否';
    if(cellvalue == 1 ){
        str='是';
    }
    return str;
}