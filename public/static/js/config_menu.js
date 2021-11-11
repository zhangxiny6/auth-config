var is_open_menu = false;   //菜单是否展开
var treeObj;    //树对象
var is_show_arr = [];    //关联显示的函数

$(function(){
    $("#controller_select").select2({
        language: "zh-CN",
    });
    // loadAndUpNumber();
    $(".menu_select_ul li").click(function(){
        //如果已经是焦点
        if($(this).hasClass('msu_active')){
            return;
        }
        $(this).parent().children('.msu_active').removeClass('msu_active');
        $(this).addClass('msu_active');
        var value = $(this).attr('_value');
        var inputObj = $(this).parent().attr('_input');
        $("#"+inputObj).val(value);
        //查询
        $("#btn_search").click();
    });
    //加载全部数据
    $("#btn_search_all").click(function () {
        // $(".menu_select_ul li").removeClass('msu_active');
        // $(".menu_select_ul li[_value='-1']").addClass('msu_active');
        //
        // // $("#controller_select").val('-1');
        // $("#auth_status").val(-1);
        // $("#is_select").val(-1);
        // $("input[name='searchinfo']").val('');
        // //查询
        // $("#btn_search").click();
    });
    //加载最近七天的数据
    $("#last_seven_day").click(function () {
        $("#is_last_seven_day").val($(this).is(':checked'));
        //查询
        $("#btn_search").click();
    });
    //创建树
    createTree();
    //加载列表数据
    GetGrid();
});

function GetGrid() {
    jqGrid = $("#gridTable").jqGrid({
        url: listUrl,
        datatype: "json",
        colNames: ["菜单地址", "权限说明",'创建人', '创建时间',/*'QQ号',/*"所属模块",*/'状态'], //列
        colModel: [ // 行 jqGrid每一列的配置信息。包括名字，索引，宽度,对齐方式.....
            {name: 'name', index: 'name', width: 120, align: "left",formatter:nameFormatter},
            {name: 'auth_name', index: 'auth_name', width: 100, align: "left"},
            {name: 'author', index: 'author', width: 40, align: "center"},
            {name: 'class_createtime', index: 'class_createtime', width: 60, align: "center"},
            // {name: 'qq_number', index: 'qq_number', width: 40, align: "center"},
            // {name: 'application_module', index: 'application_module', width: 40, align: "center"},
            {name: 'is_select', index: 'is_select', width: 60, align: "center",sortable: false,
                formatter:statusFormatter},
        ],
        autowidth: true, //自动调整宽度
        height: $(window).height(),
        multiselect: true, //checkbox多选操作
        //分页部分
        rowList: [5, 10, 20, 40], //可供用户选择一页显示多少条
        pager: '#gridPager', //分页的容器
        viewrecords: true, //定义是否在Pager Bar中显示记录数信息。
        recordpos: "center",
        pagerpos: "right", //设置分页容器的位置
        //pgtext ：显示当前页码状态的字符串，这个与所选用的language文件有关，具体的文本格式定义在里面。例如默认的英文格式就是“Page {0} of {1}”，其中{0}代表当前载入的页码；{1}代表页码的总数。
        //分页结束
        sortorder: "asc", //排序方式,可选desc,asc
        sortname: "url", //默认排序字段
        mtype: "post", //向后台请求数据的ajax的类型。可选post,get
        viewrecords: true,
        rownumbers: false, // 如果为ture则会在表格左边新增一列，显示行顺序号，从1开始递增。此列名为'rn'.
        multiselectWidth: 25,// 左侧列宽
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
            root: "data", //返回的数据集
            page: "currPage", //当前页
            total: "sumpage", //总页数
            records: "totalPages", //总条数
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
            initialMainGirdTablePageNew();
            //列竖线对齐
            var lastThObj = $(".ui-jqgrid-htable thead th:last");
            $(lastThObj).css('cssText','width:'+(lastThObj.width()+11)+'px !important;');
        }
    }).navGrid('#pager1', {
        edit: false,
        add: false,
        del: false
    });
};

//格式化name
function nameFormatter(al, data, row){
    var str = '<i title="关联后不显示" _id="'+row['id']+'" onclick="switchShowStatus(this)"' +
        ' class="fa fa-eye-slash method_not_show"></i> '+row['name'];
    if(row['is_select']==1){
        str += '('+row['menu_name']+')';
    }
    str = '<span style="font-size:14px;">'+str+'</span>';
    return str;
}

//切换显示状态
function switchShowStatus(obj){
    //点子集不触发父集事件(事件冒泡)
    event.stopPropagation();
    var id = $(obj).attr('_id');
    //如果当前是不显示，则切换为显示
    if($(obj).hasClass('fa-eye-slash')){
        $(obj).removeClass('fa-eye-slash').addClass('fa-eye');
        $(obj).attr('title','关联后显示');
        is_show_arr[is_show_arr.length] = id;
    }else{
        $(obj).removeClass('fa-eye').addClass('fa-eye-slash');
        $(obj).attr('title','关联后不显示');
        //删除该显示数据
        deleteArrEleByParam(is_show_arr,id);
    }
}

//删除数组元素，根据属性
function deleteArrEleByParam(arr,param){
    for(var index in arr){
        var item = arr[index];
        if(item==param){
            arr.splice(index,index+1);
        }
    }
}

//备份菜单
function backMenu(){
    alert('开发中');
}

//格式化使用状态
function statusFormatter(al, data, row){
    var status_str = '';
    if(row['is_select']==1){
        status_str += '<span style="background:#9e9e9e;" class="label label-success label-wi">已关联</span>';
    }else{
        status_str += '<span class="label label-success label-wi">未关联</span>';
    }
    // if(row['auth_status']==1){
    //     status_str += ' <span class="label label-success label-wi">需要控制</span>';
    // }else{
    //     status_str += ' <span style="background:#9e9e9e;" class="label label-success label-wi">不需要</span>';
    // }
    return status_str;
}

//初始化主页面gird高度
function initialMainGirdTablePageNew() {

    var pageHg = $(window).height(); //页面的高度
    var girdOffTopHg = 100; //距离顶部的高度
    var pageMaxGirdTableHg = pageHg -girdOffTopHg-45;
    var cssHeight=pageMaxGirdTableHg+'px !important';
    $(".menu_list").css('height',pageMaxGirdTableHg);
    $('.ui-jqgrid .ui-jqgrid-bdiv').css("cssText","height:"+cssHeight);

    var gridTableObj = $('#gridTable');
    //resize重设(表格、树形)宽高
    gridTableObj.setGridWidth($(".content_right").width());
    // //resize重设(表格、树形)宽高
    // gridTableObj.setGridWidth($(".content_right").width());
    //设置gird的高度和宽度
    // $("#gridTable").setGridHeight(pageMaxGirdTableHg);
};

//ztree配置
var setting = {
    view: {
        nameIsHTML: true,
        selectedMulti: false,
        addHoverDom: addHoverDom,
        removeHoverDom: removeHoverDom,
    },
    edit: {
        drag: {
            autoExpandTrigger: true,
            prev: dropPrev,
            inner: dropInner,
            next: dropNext
        },
        enable: true,
        showRemoveBtn: showRemoveBtn,
        showRenameBtn: showRenameBtn,
        renameTitle:'修改菜单',
        removeTitle:'删除菜单'
    },
    check: {
        enable: false
    },
    //异步更新节点
    async: {
        enable: true,
        url:getMenuListUrl,
        otherParam:{"menu_id":null,"auth_config_id":auth_config_id},
        dataFilter: filter
    },
    data: {
        simpleData: {
            enable: true
        }
    },
    callback: {
        beforeDrag: beforeDrag,
        beforeRemove: beforeRemove,
        beforeEditName:beforeEditName,
        //拖动
        beforeDrag: beforeDrag,
        onDrop: onDrop,
    }

};

function filter(treeId, parentNode, childNodes) {
    if (!childNodes) return null;
    for (var i=0, l=childNodes.length; i<l; i++) {
        childNodes[i].name = childNodes[i].name.replace(/\.n/g, '.');
    }
    return childNodes;
};

//控制新增节点
function addHoverDom(treeId, treeNode) {
    //只有一级和二级才能新增
    if(treeNode['level']==3){
        return;
    }
    var menu_id = treeNode['id'];
    var sObj = $("#" + treeNode.tId + "_span");
    if (treeNode.editNameFlag || $("#addBtn_"+treeNode.tId).length>0) return;
    var addStr = "<span class='button add' id='addBtn_" + treeNode.tId
        + "' title='新增菜单' onfocus='this.blur();'></span>";
    sObj.after(addStr);
    var btn = $("#addBtn_"+treeNode.tId);
    if (btn) btn.bind("click", function(){
        treeObj.selectNode(treeNode);
        var url=addMenuUrl+"&module_code="+module+"&parent_menu_id="+menu_id;
        var title='新增菜单';
        _esOpendialogmini(url,title);
        return false;
    });
};

//控制删除节点
function showRemoveBtn(treeId, treeNode) {
    if(treeNode['pId']==null){
        return false;
    }
    return true;
};

//控制修改节点
function showRenameBtn(treeId, treeNode) {
    if(treeNode['pId']==null){
        return false;
    }
    return true;
};

function beforeDrag(treeId, treeNodes) {
    return false;
};

//删除节点
function beforeRemove(treeId, treeNode) {
    var zTree = $.fn.zTree.getZTreeObj("treeRule");
    zTree.selectNode(treeNode);
    var node_len = 0;
    //得到子节点长度
    if(treeNode['children']!=null){
        node_len = treeNode['children'].length;
    }
    if(node_len > 0){
        art.dialog.alert('请先删除该菜单下的子菜单！', null);
        return false;
    }
    art.dialog.confirm('您确认删除 "'+treeNode.name+'" 吗?', function() {
        $.ajax({
            url:deleteMenuUrl+treeNode['id'],
            data:{'auth_config_id':'{$auth_config_id}'},
            dataType:'json',
            Type:'POST',
            success:function(data){
                //刷新上传和下载数
                loadAndUpNumber();
                //刷新列表
                $("#btn_search").click();
                //刷新菜单
                refreshNode(treeNode.getParentNode());
            },
            error:function(){
                art.dialog.alert('网络请求失败,请稍后重试!', null);
            }
        })
    });
    return false;
};

//重新加载节点
function refreshNode(node) {
    var zTree = $.fn.zTree.getZTreeObj("treeRule"),
        type = 'refresh',
        silent = true;
    var menu_id = node['id'];
    treeObj.setting.async['otherParam']['menu_id'] = menu_id;
    zTree.reAsyncChildNodes(node, type, silent);
};

//修改菜单
function beforeEditName(treeId, treeNode) {
    var zTree = $.fn.zTree.getZTreeObj("treeRule");
    zTree.selectNode(treeNode);
    var menu_id = treeNode['id'];
    setTimeout(function() {
        var url=editMenuUrl+treeNode.id+"&auth_config_id="+auth_config_id+"&parent_menu_id="+menu_id;;
        var title='修改菜单';
        _esOpendialogmini(url,title);
    }, 0);
    return false;
}

//失去焦点后，删除新增按钮
function removeHoverDom(treeId, treeNode) {
    $("#addBtn_"+treeNode.tId).unbind().remove();
};

function dropPrev(treeId, nodes, targetNode) {
    var pNode = targetNode.getParentNode();
    if (pNode && pNode.dropInner === false) {
        return false;
    } else {
        for (var i=0,l=curDragNodes.length; i<l; i++) {
            var curPNode = curDragNodes[i].getParentNode();
            if (curPNode && curPNode !== targetNode.getParentNode() && curPNode.childOuter === false) {
                return false;
            }
        }
    }
    return true;
}
function dropInner(treeId, nodes, targetNode) {
    if (targetNode && targetNode.dropInner === false) {
        return false;
    } else {
        for (var i=0,l=curDragNodes.length; i<l; i++) {
            if (!targetNode && curDragNodes[i].dropRoot === false) {
                return false;
            } else if (curDragNodes[i].parentTId && curDragNodes[i].getParentNode() !== targetNode && curDragNodes[i].getParentNode().childOuter === false) {
                return false;
            }
        }
    }
    return true;
}
function dropNext(treeId, nodes, targetNode) {
    var pNode = targetNode.getParentNode();
    if (pNode && pNode.dropInner === false) {
        return false;
    } else {
        for (var i=0,l=curDragNodes.length; i<l; i++) {
            var curPNode = curDragNodes[i].getParentNode();
            if (curPNode && curPNode !== targetNode.getParentNode() && curPNode.childOuter === false) {
                return false;
            }
        }
    }
    return true;
}

//排序后触发事件
function onDrop(event, treeId, treeNodes, targetNode, moveType, isCopy) {
    if(targetNode==null){
        return null;
    }
    //进行排序
    var parentNode = targetNode.getParentNode();
    var sonNodes = parentNode['children'];
    var id_arr = [];
    for(var index in sonNodes){
        id_arr[index] = sonNodes[index]['id'];
    }
    var ids = id_arr.toString();
    $.ajax({
        url:menuSort,
        data:{'ids':ids},
        dataType:'json',
        Type:'POST',
        success:function(data){

        }, error:function(){
            art.dialog.alert('网络请求失败,请稍后重试!', null);
        }
    })
}

function beforeDrag(treeId, treeNodes) {
    for (var i=0,l=treeNodes.length; i<l; i++) {
        if (treeNodes[i].drag === false) {
            curDragNodes = null;
            return false;
        } else if (treeNodes[i].parentTId && treeNodes[i].getParentNode().childDrag === false) {
            curDragNodes = null;
            return false;
        }
    }
    curDragNodes = treeNodes;
    return true;
}

//更新权限
function updateAuth(){
    art.dialog.confirm('您确认同步所有控制器吗?', function() {
        $.ajax({
            url:updateUrl,
            dataType:'json',
            Type:'POST',
            success:function(result){
                // //刷新select
                getSelectController();
                // //查询
                selectChange();
                setTimeout(function(){
                    art.dialog({
                        title: '同步结果',
                        content: result,
                        lock:true,
                        icon: 'succeed',
                        follow: document.getElementById('btn2'),
                        ok: function () {
                            return true;
                        },
                        close:function(){
                            return true;
                        }
                    });
                },100);
            }, error:function(){
                art.dialog.alert('网络请求失败,请稍后重试!', null);
            }
        })
    });
}

//下载菜单
function downloadMenu(){
    art.dialog.confirm('您确认下载全部菜单吗?', function() {
        $.ajax({
            url:downloadMenuUrl,
            dataType:'json',
            Type:'POST',
            success:function(){
                //刷新上传和下载数
                loadAndUpNumber();
                //得到根节点
                var node = treeObj.getNodeByParam("id", 'root', null);
                //变为父节点
                node['isParent'] = true;
                treeObj.updateNode(node);
                refreshNode(node);
            }, error:function(){
                art.dialog.alert('网络请求失败,请稍后重试!', null);
            }
        })
    });
}

//上传菜单
function uploadMenu(){
    art.dialog.confirm('您确认上传全部菜单吗?', function() {
        $.ajax({
            url:uploadMenuUrl,
            dataType:'json',
            Type:'POST',
            success:function(result){
                //刷新上传和下载数
                loadAndUpNumber();
                art.dialog({
                    icon: 'succeed',
                    lock: true,
                    opacity: 0.3,	// 透明度
                    content: result.msg
                });
            }, error:function(){
                art.dialog.alert('网络请求失败,请稍后重试!', null);
            }
        })
    });
}

//创建ztree对象
function createTree() {
    treeObj = $.fn.zTree.init($("#treeRule"), setting);
}

//折叠或展开
function expandAll(){
    //展开
    if(is_open_menu){
        //得到根节点
        var rootNode = treeObj.getNodeByParam('id','root');
        //将根节点所有子节点折叠
        $.each(rootNode.children,function(index,item){
            treeObj.expandNode(item,false);
        });
        is_open_menu = false;
    }
    //折叠
    else{
        treeObj.expandAll(true);
        is_open_menu = true;
    }
}

//菜单关联
function relationMenu(){
    //得到当前选中的菜单
    var nodes = treeObj.getSelectedNodes();  //当前选中的Node
    var node = nodes[0];

    if(node==null){
        art.dialog.alert('请选择要操作的菜单');
        return;
    }else if(node['level']==3){
        art.dialog.alert('第三级子菜单不能关联');
        return;fii
    }
    //得到当前选中的权限
    var arrRowIndex=jQuery("#gridTable").jqGrid('getGridParam','selarrrow');
    var ids = arrRowIndex.toString();
    if(ids==''){
        art.dialog.alert('请选择要关联的权限', null);
        return;
    }
    var is_show_ids = is_show_arr.toString();
    var data = {
        auth_config_id:auth_config_id,
        ids:ids,
        menu_id:node.id,
        is_show_ids:is_show_ids,
    };
    art.dialog.confirm('您确认关联吗?', function() {
        $.ajax({
            data:data,
            url:relationMenuUrl,
            dataType:'json',
            Type:'POST',
            success:function(result){
                if(result['code']==0){
                    art.dialog.alert(result['msg'], null);
                    return;
                }
                //变为父节点
                node['isParent'] = true;
                treeObj.updateNode(node);
                //刷新菜单
                refreshNode(node);
                //刷新权限列表
                $("#btn_search").click();
                //刷新上传和下载数
                loadAndUpNumber();
                // var newNode = treeObj.getNodeByParam('id',node['id']);
                setTimeout(function(){                    // 弹出新窗口
                    art.dialog({
                        lock: true,
                        opacity: 0.1,	// 透明度
                        title:'关联结果',
                        content: result
                    });

                },100);
            }, error:function(){
                art.dialog.alert('网络请求失败,请稍后重试!', null);
            }
        })
    });
}

//刷新父菜单的数据
function refreshParsonNode(type){
    //得到当前选中的菜单
    var nodes = treeObj.getSelectedNodes();  //当前选中的Node
    var node = nodes[0];
    if(node==null){
        return;
    }
    if(type=='edit'){
        node = node.getParentNode();
    }
    //变为父节点
    node['isParent'] = true;
    //使该菜单变为父级
    treeObj.updateNode(node);
    refreshNode(node);
}

//可上传数和可下载数
function loadAndUpNumber(){
    return;
    $.ajax({
        url:getUpAndOnNumberUrl,
        dataType:'json',
        Type:'POST',
        success:function(data){
            var download_number = data['download_number'];
            $("#download_number").text(download_number);
            $("#download_number").attr("title",'可下载'+download_number+'条数据');
            var upload_number = data['upload_number'];
            $("#upload_number").text(upload_number)
            $("#upload_number").attr("title",'可上传'+upload_number+'条数据');
        },
        error:function(){
            art.dialog.alert('网络请求失败,请稍后重试!', null);
        }
    })
}

//控制器被修改
function selectChange(){
    $('#btn_search').click();
}

//异步得到控制器列表
function getSelectController() {
    $.ajax({
        url:relSelectControllerUrl,
        dataType:'json',
        Type:'POST',
        success:function(result){
            var selectObj = $("#controller_select");
            //清空之前的选择数据
            selectObj.empty();
            selectObj.append("<option value=''>-全部控制器-</option>");
            $.each(result, function (index, obj) {
                selectObj.append("<option value='" + obj.id + "'>" + obj.name + "("+obj.auth_name+")</option>");
            });
        },
        error:function(){
            art.dialog.alert('网络请求失败,请稍后重试!', null);
        }
    })
}

/**
 * 导出sql
 */
function exportSql() {
    window.location.href = exportSqlUrl;
}