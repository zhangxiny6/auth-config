$(function () {
    //加载头部Tab选项卡 .child-menu-list
    $(".menu_wp").on("click", "ul li a", function () {
        var _href = $(this).attr('_href');
        if(_href!=undefined&&_href.indexOf('/TakeOils/')!=-1&&window.external.openTakeOils){
            window.external.openTakeOils();
            return;
        }
        if(typeof _href != 'undefined' && _href.trim().length != 0){
            var isFindIframe = false;
            var bStopIndex = 0;
            var titleName = $(this).html();
            var currMenuId = $(this).attr("id");
            var topWindow = $(window.parent.document);
            var showTabNavLi = topWindow.find("#min_title_list li");
            var lowerCaseHref = _href.toLowerCase();
            showTabNavLi.each(function () {
                var tempHref = $(this).find('a').attr("data-href").toLowerCase();
                if (tempHref == lowerCaseHref) {
                    isFindIframe = true;
                    bStopIndex = showTabNavLi.index($(this));
                    return false;
                }
            });
            if (!isFindIframe){
                creatIframe(_href, titleName);
            }else {
                showTabNavLi.removeClass("active").eq(bStopIndex).addClass("active");
                var iframe_box = topWindow.find("#iframe_box");
                iframe_box.find(".show_iframe").hide().eq(bStopIndex).show().find("iframe").attr("src", _href);
            }



        }

        var _level = $(this).attr('level');
        if(typeof _level != 'undefined' && _level ==1){
            $('.sidebar-panel .active').removeClass("active");
            $('.sidebar-panel .leftmenu_parent').removeClass("active_p");
            $(this).addClass("active_p");
        }



    });

    //显示点击的Tab选项卡
    $("#ifram_tab_nav").on("click", "#min_title_list li", function (e) {
        if (e.which == 1) {
            var bStopIndex = $(this).index();
            var iframe_box = $("#iframe_box");
            $("#min_title_list li").removeClass("active").eq(bStopIndex).addClass("active");
            var showIfram = iframe_box.find(".show_iframe").hide().eq(bStopIndex);
            var iframeSrc = showIfram.find('iframe').attr('src').toLowerCase();
            showIfram.show();
            activeLeftMenuSel(iframeSrc);
        }
    });

    //关闭Tab选项卡
    $("#ifram_tab_nav").on("click", "#min_title_list li i", function () {
        var aCloseIndex = $(this).parents("li").index();
        $(this).parent().remove();
        num==0?num:num--;  //减少
        var iframeBox = $('#iframe_box').find('.show_iframe').eq(aCloseIndex);
        var iframeSrc = iframeBox.find('iframe').attr('src').toLowerCase();
        iframeBox.remove();
        //激活左侧菜单的选中
        activeLeftMenuSel(iframeSrc);
        //移动Tab选项卡
        rollHeaderTabLocation();
    });

    //双击关闭Tab选项卡
    $("#ifram_tab_nav").on("dblclick", "#min_title_list li", function () {
        var aCloseIndex = $(this).index();
        var iframe_box = $("#iframe_box");
        if (aCloseIndex > 0) {
            $(this).remove();
            $('#iframe_box').find('.show_iframe').eq(aCloseIndex).remove();
            num==0?num:num--;  //减少
            $("#min_title_list li").removeClass("active").eq(aCloseIndex - 1).addClass("active");
            var iframeBox=iframe_box.find(".show_iframe").hide().eq(aCloseIndex - 1)
            var iframeSrc = iframeBox.find('iframe').attr('src').toLowerCase();
            iframeBox.show();
            //激活左侧菜单的选中
            activeLeftMenuSel(iframeSrc);
            //移动Tab选项卡
            rollHeaderTabLocation();
        } else {
            return false;
        }
    });

    //点击右键显示Tab选项卡的右键菜单
    $('#min_title_list').on('mousedown', 'li', function (e) {
        var index = $(this).index();
        if (e.which === 3 && index > 0) {
            //禁用默认的右键菜单
            $(this).bind('contextmenu',function (e) {
                return false;
            });
            //显示自定义的菜单
            $("#shortcut_menu_bar").css({
                'left': e.pageX - 10,
                'top': e.pageY + 10,
                'display': 'block',
            });

        }
    });
    //鼠标移出ul标签将隐藏自定义的右键菜单
    $("#shortcut_menu_bar").on("mouseleave", "ul", function () {
        hideShortCutMenuBar();  //隐藏Tab的自定义右键菜单
    });
    //点击头部Tab选项卡栏,隐藏右键菜单
    $("#ifram_tab_nav").click(function () {
        hideShortCutMenuBar(); //隐藏Tab的自定义右键菜单
    });

    //点击刷新"工作台"页签
    $("#default_li").click(function(){
        var url=$(this).find('a').attr('data-href');
        $("#workbench_iframe").attr('src',url);  //刷新
    });

    setTimeout(function () {
        $(".system_level_hint").hide();
    },20000);
});

var num=0;
var oUl=$("#min_title_list");
//向左移动
$('.j_tabLeft').click(function(){
    var tabBoxWd=$(".iframe-tab-wp").width();
    if(getTabCountWd()>tabBoxWd){
        num==oUl.find('li').length-1?num=oUl.find('li').length-1:num++;
        toNavPos();
    }
});

//向右移动
$('.j_tabRight').click(function(){
    var tabBoxWd=$(".iframe-tab-wp").width();
    if(getTabCountWd()>tabBoxWd){
        num==0?num=0:num--;
        toNavPos();
    }
});
//移动头部Tab选项卡
function toNavPos(){
    oUl.stop().animate({'left':-num*100},100);
}

//获取tab选项卡的总宽度
function getTabCountWd(){
    var countWd=0;
    var $tabLiObj=$("#min_title_list").find("li");
    $.each($tabLiObj,function(i,item){
        countWd+=$(item).outerWidth();
    });
    return countWd;
}

//创建一个iframe选项卡
function creatIframe(href, titleName) {
    var topWindow = $(window.parent.document);
    var show_nav = topWindow.find('#min_title_list');
    show_nav.find('li').removeClass("active");
    show_nav.append('<li class="active"><a data-href="' + href + '">' + titleName + '</a><i class="fa fa-times-circle"></i></li>');
    var iframe_box = topWindow.find('#iframe_box');
    var iframeBox = iframe_box.find('.show_iframe');
    iframeBox.hide();
    iframe_box.append('<div class="show_iframe"><div class="loading"></div><iframe frameborder="0" src=' + href + '></iframe></div>');
    var showBox = iframe_box.find('.show_iframe:visible');
    showBox.find('iframe').attr("src", href).load(function () {
        showBox.find('.loading').hide();
    });
    num==0?num:num++;  //新增一个Tab
    //大于顶部高度时,自动滚动Tab选项卡
    rollHeaderTabLocation()
}

//自动滚动Tab选项卡
function rollHeaderTabLocation(){
    var tabBoxWd=$(".iframe-tab-wp").width();  //选项卡的盒子的宽度
    var halfTabBoxWd=tabBoxWd/2;  //盒子宽度的一般
    //选项卡溢出盒子宽度一般的数值
    var temp=getTabCountWd()-tabBoxWd;
    //选项卡超出的一半的倍数
    var temp2=parseInt(temp/halfTabBoxWd);
    if(temp >0 && temp2 >=0){
        oUl.stop().animate({'left':-(halfTabBoxWd*(temp2+1))},100);
    }else{
        oUl.stop().animate({'left':0},100);
    }
}


//选中某个Tab选项卡
function selectTabMenuItem($obj) {
    var bStopIndex = $obj.index();
    var iframe_box = $("#iframe_box");

    $obj.addClass('active').siblings().removeClass('active');

    iframe_box.find(".show_iframe").hide().eq(bStopIndex).show();
}

function closeTabMenuIframIten($Obj, type) {
    var bStopIndex = $Obj.index();   //ѡ?????
    var iframeBox = $("#iframe_box");
    switch (type) {
        case 1: iframeBox.find(".show_iframe").eq(bStopIndex).remove(); break;
        case 2: iframeBox.find(".show_iframe:gt(0)").remove(); break;
        case 3: iframeBox.find(".show_iframe:hidden():gt(0)").remove(); break;
        case 4: iframeBox.find(".show_iframe:lt(" + bStopIndex + "):gt(0)").remove(); break;
        case 5: iframeBox.find(".show_iframe:gt(" + bStopIndex + ")").remove(); break;
    };
}

function hideShortCutMenuBar() {
    $("#shortcut_menu_bar").hide();
}

$("#sm_tabclose").click(function () {
    var currObj = $("#min_title_list").find('li.active');
    closeTabMenuIframIten(currObj, 1);
    if (currObj.next().length > 0) {
        selectTabMenuItem(currObj.next());
    } else {
        selectTabMenuItem(currObj.prev());
    }
    currObj.remove();
    hideShortCutMenuBar();
})

//关闭所有
$("#sm_tabclose_all").click(function () {
    var currLiObj = $("#min_title_list").find('li').eq(0);
    closeTabMenuIframIten(currLiObj, 2);
    $("#min_title_list").find('li:gt(0)').remove();

    //选中某个Tab选项卡
    selectTabMenuItem(currLiObj);
    hideShortCutMenuBar();
    //关闭所有,还原Tab选项卡的位置
    oUl.stop().animate({'left':0},100);
})

//选中首页
function selectHomeFrame(){
    var currLiObj = $("#min_title_list").find('li').eq(0);
    //选中某个Tab选项卡
    selectTabMenuItem(currLiObj);
}

//关闭当前选项卡
function closeCurrFrame(){
    var currObj = $("#min_title_list").find('li.active');
    if($(currObj).attr('id')=='default_li'){
        return;
    }
    closeTabMenuIframIten(currObj, 1);
    if (currObj.next().length > 0) {
        selectTabMenuItem(currObj.next());
    } else {
        selectTabMenuItem(currObj.prev());
    }
    currObj.remove();
    hideShortCutMenuBar();
}

//刷新当前选项卡
function refreshCurrIframe(){
    var currObj = $("#min_title_list").find('li.active');
    var bStopIndex = $(currObj).index();   //ѡ?????
    var iframeBox = $("#iframe_box");
    var iframe = iframeBox.find(".show_iframe").eq(bStopIndex).find('iframe');
    $(iframe).attr('src', $(iframe).attr('src'));
}

//导航栏的快捷菜单事件
$("#sm_tabclose_other").click(function () {
    var currObj = $("#min_title_list").find('li.active');

    closeTabMenuIframIten(currObj, 3);

    currObj.siblings().not('.default').remove();
    hideShortCutMenuBar();
})

$("#sm_tabclose_left").click(function () {
    var currObj = $("#min_title_list").find('li.active');

    closeTabMenuIframIten(currObj, 4);
    currObj.prevAll().not('.default').remove();
    hideShortCutMenuBar();
})

$("#sm_tabclose_right").click(function () {
    var currObj = $("#min_title_list").find('li.active');

    closeTabMenuIframIten(currObj, 5);

    currObj.nextAll().remove();
    hideShortCutMenuBar();
});


function activeLeftMenuSel(currHref) {
    var selMenuObj = null;
    var menuLiObj = $(".child-menu-list").find('li');
    $.each(menuLiObj, function (i,item) {
        var _href = $(item).children('a').attr('_href');
        if (typeof (_href) != 'undefined' && _href.length > 0) {
            _href = _href.toLowerCase();
            if (currHref == _href) {
                selMenuObj = $(item).children('a').first();
                return false;
            }
        }
    });
    if (selMenuObj instanceof Object) {
        $(".child-menu-list").hide().find('li a').removeClass('active');
        $('.sidebar-panel .leftmenu_parent').removeClass("active_p");
        selMenuObj.addClass('active').parents('ul').show();
        selMenuObj.parent().parent().parent().find('.leftmenu_parent').addClass('active_p');
    }
}

//打开新窗口
function openNewTab(parent_menu,son_menu){
    var parent_menu_obj = $("#"+parent_menu);
    var son_menu_obj = $("#"+son_menu);
    if(!$(parent_menu_obj).hasClass('active_p')){
        $(parent_menu_obj).click();
    }
    setTimeout(function(){
        $(son_menu_obj).click();
    },100);
}
