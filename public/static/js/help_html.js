$(function () {
    var window_height = ($(window).height()-60);
    $(".help_body").height(window_height);
    $(".help_menu").height(window_height);
    //循环所有图片
    $(".help_body img").each(function (index,item) {
        $(item).attr('title','双击查看大图').addClass('htlp_img_css').attr('ondblclick','openImg(this)').attr('alt','加载中……');
    });
    $(".help_keyword_input").focus(function () {
        $(".help_keyword_operation").addClass('help_keyword_operation_focus');
    });
    $(".help_keyword_input").blur(function () {
        $(".help_keyword_operation").removeClass('help_keyword_operation_focus');
    });

    // 获取window的引用:
    var $window = $(window);
    // 获取包含data-src属性的img，并以jQuery对象存入数组:
    var lazyImgs = _.map($('img[data-src]').get(), function (i) {
        return $(i);
    });
    // 定义事件函数:
    var onScroll = function() {
        // 获取页面滚动的高度:
        var wtop = $window.scrollTop();
        // 判断是否还有未加载的img:
        if (lazyImgs.length > 0) {
            // 获取可视区域高度:
            var wheight = $window.height();
            // 存放待删除的索引:
            var loadedIndex = [];
            // 循环处理数组的每个img元素:
            _.each(lazyImgs, function ($i, index) {
                // 判断是否在可视范围内:
                if ($i.offset().top - wtop < wheight) {
                    // 设置src属性:
                    $i.attr('src', $i.attr('data-src'));
                    // 添加到待删除数组:
                    loadedIndex.unshift(index);
                }
            });
            // 删除已处理的对象:
            _.each(loadedIndex, function (index) {
                lazyImgs.splice(index, 1);
            });
        }
    };
    // 绑定事件:
    $("div").scroll(onScroll);
    // 手动触发一次:
    onScroll();
});

//打开图片
function openImg(obj){
    var img_url = $(obj).attr('src');
    window.open(img_url);
}

var oldKey = "";
var index = -1;
var pos = new Array();
var oldCount = 0;

//搜索框被修改时
function helpInputChange(){
    var key = $("#help_keyword").val(); //取key值
    var body_text = $("body").text();
    if(key==''){
        $("#fa-chevron-up").removeClass('help_color_323332').addClass('help_color_e3e3e3');
        $("#fa-chevron-down").removeClass('help_color_323332').addClass('help_color_e3e3e3');
        return;
    }
    var is_exist = body_text.indexOf(key);
    if(is_exist!=-1){
        $("#fa-chevron-up").removeClass('help_color_e3e3e3').addClass('help_color_323332');
        $("#fa-chevron-down").removeClass('help_color_e3e3e3').addClass('help_color_323332');
    }else{
        $("#fa-chevron-up").removeClass('help_color_323332').addClass('help_color_e3e3e3');
        $("#fa-chevron-down").removeClass('help_color_323332').addClass('help_color_e3e3e3');
    }
}

//搜索
function search(flg) {
    if (!flg) {
        index++;
        index = index == oldCount ? 0 : index;
    }
    else {
        index--;
        index = index < 0 ? oldCount - 1 : index;
    }

    $(".result").removeClass("help_keyword");
    $("#toresult").remove();
    var key = $("#help_keyword").val(); //取key值
    if (!key) {
        oldKey = "";
        return; //key为空则退出
    }

    if (oldKey != key) {
        //重置
        index = 0;
        $(".result").each(function () {
            $(this).replaceWith($(this).html());
        });
        pos = new Array();

        $(".help_body").html($(".help_body").html().replace(new RegExp(key, "gm"), "<span id='result" + index + "' class='result'>" + key + "</span>")); // 替换

        $("#help_keyword").val(key);
        oldKey = key;
        $(".result").each(function () {
            pos.push($(this).offset().top-100);
        });
        oldCount = $(".result").length;
    }
    $(".result:eq(" + index + ")").addClass("help_keyword");
    $(".help_body").scrollTop(pos[index]);
}