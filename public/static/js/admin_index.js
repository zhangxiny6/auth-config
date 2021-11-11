$(function() {
    //Sidebar Menu 左边菜单
    $(".sidebar").on("click", "li a", function() {
        var level = $(this).attr('level');
        if (level == 1) {
            if ($(this).next().is(":hidden")) {
                $(".child-menu-list").hide();
            }
            if( $(this).next().length == 0 ){
                //不存在子菜单,则加样式
                // $(this).addClass('active').css({'border':'1px solid rgba(0, 0, 0, 0.08)'}).parent().siblings().find('a').removeClass('active').css({'border':'none'});
            }
            $(this).next().slideToggle();
        } else {
            $(".child-menu-list").find('li a').removeClass('active');
            $(this).addClass('active');
        }
    });

    //展开清除缓存的面板
    $('#clearcache_ul_btn').click(function() {
        //如果 clearcache_ul 已经展开，则隐藏
        if ($('#clearcache_ul').is(':hidden')) {
            $('#clearcache_ul').show();
        } else {
            $('#clearcache_ul').hide();
        }
    });

    $('#top').click(function(e) {
        var _trg = $(e.target).attr("id");
        if (typeof _trg != 'undefined' && _trg == 'top') {
            $('#clearcache_ul').hide();
        }
    });

    //清除缓存     
    $('#clearCacheOpItems button').click(function() {
        var url = $(this).attr('_href');
        _clear_cache_now($(this), url);
    });

    //头部下拉菜单的显示
    $(".dropdown-toggle").click(function(e) {
        var parentObj = $(this).parent();
        if (parentObj.length > 0) {
            var isOpen = parentObj.hasClass('open');
            if (isOpen) {
                parentObj.removeClass('open');
                $(this).attr('aria-expande', 'false');
            } else {
                $("#top").find('ul li').removeClass('open');
                parentObj.addClass('open');
                $("body").attr('onclick', "onceClickEvent()");
                $(this).attr('aria-expande', 'true');
                e.stopPropagation(); //阻止事件冒泡
            }
        }
    });
    //顶部栏菜单的选中事件
    $(".topmenu").on('click', 'li a', function() {
        var _href = $(this).attr('_href');
        var $liObj = $('.sidebar-panel').find('.child-menu-list li a');
        $.each($liObj, function(i, item) {
            var listHref = $(item).attr('_href');
            if (listHref == _href) {
                $(".child-menu-list").hide();
                $(item).click().parents('ul.child-menu-list').show();
                return false;
            }
        });
    });
});
//退出系统
function logout(url) {
    art.dialog.confirm('您确认退出系统吗?', function() {
        window.location.href = url;
    });
}
//隐藏dropdown的下拉层
function onceClickEvent() {
    var aObj = $("#top").find("a[aria-expande='true']");
    if (aObj.length > 0) {
        var liObj = aObj.parents('li');
        liObj.removeClass('open');
        $("body").removeAttr('onclick');
    }
}

//清除缓存
function _clear_cache_now(url,obj){
    $(obj).html("清除中...");
    $.ajax({
        url:url,
        type:"GET",
        dataType:"html",
        success:function(data){
            $(obj).html("清除成功");
        },error:function(){

        },complete:function(){
            setTimeout(function(){
                $(obj).html("清除缓存");
            },1500);
        }
    });
}