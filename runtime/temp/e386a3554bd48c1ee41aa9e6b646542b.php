<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:79:"D:\2code\1php\zxy\auth-config\public/../application/index\view\index\index.html";i:1631072248;s:72:"D:\2code\1php\zxy\auth-config\application\index\view\common\notifit.html";i:1630918396;s:74:"D:\2code\1php\zxy\auth-config\application\index\view\common\artdialog.html";i:1630918397;}*/ ?>
<!DOCTYPE html>
<!--[if IE 9]>         <html class="ie9 no-focus" lang="zh"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-focus" lang="zh"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <title><?php echo $WEB_SITE_TITLE; ?></title>

    <meta name="description" content="<?php echo config('WEB_SITE_DESCRIPTION'); ?>">
    <meta name="author" content="caiweiming">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">
    <link href="/favicon.ico" rel="shortcut icon"/>
    <!--css样式文件引用-->
    <link href="/static/ui/css/font-awesome.min.css?v=<?php echo $version; ?>" type="text/css" rel="stylesheet"/>
    <!-- bootstrap.css-->
    <link href="/static/plugins/bootstrap/bootstrap.css?v=<?php echo $version; ?>" type="text/css" rel="stylesheet"/>
    <!--  Theme Style-->
    <link href="/static/ui/css/style.css?v=<?php echo $version; ?>" type="text/css" rel="stylesheet"/>
    <!-- Responsive Style-->
    <link href="/static/ui/css/responsive.css?v=<?php echo $version; ?>" type="text/css" rel="stylesheet"/>
    <!--Shortcuts Css Codes -->
    <link href="/static/ui/css/shortcuts.css?v=<?php echo $version; ?>" type="text/css" rel="stylesheet"/>
    <link href="/static/css/index.css?v=<?php echo $version; ?>1" type="text/css" rel="stylesheet"/>
    <style>
        #min_title_list{
            right: 150px !important;
        }
    </style>
</head>
<body>
<!--首页头部-->
<div class="index-top">
    <div class="index-top-bg">
        <div class="index-top-left">
            <img src="/static/image/logintitle.png"/>
        </div>
        <div class="index-top-right">
            <div class="top-right-text">

                <span title="使用中的问题，或您的建议">
                    <i class="fa fa-qq">&nbsp;</i>开发者QQ:2639347794</span>
                &nbsp;|&nbsp;
                <a title="点击查看系统帮助" target="_blank" >
                    <i class="fa fa-question-circle">&nbsp;</i>帮助
                </a>
                &nbsp;|&nbsp;
                <a title="安全退出系统" href="javascript:;" onclick="logout('/index/Login/logout.html')">
                    <i class="fa fa-power-off">&nbsp;</i>退出</a>
            </div>
            <div class="top-right-username aui-ellipsis" _attr_user_id="<?php echo $userInfo['user_id']; ?>" _attr_href="<?php echo url('Index/userInformation'); ?>" >
                <i class="fa fa-user"></i>&nbsp;<?php echo $userInfo['real_name']; ?>
            </div>
        </div>
    </div>
    <div class="top-line"></div>
</div>
<div class="clearfix menu_wp navigation-side">
    <div class="nav-header">
        <div class="nav-header-text">菜单导航</div>
    </div>
    <ul class="sidebar-panel na sidebar">
        <li class="menu-item" title="">
            <a href="javascript:void(0)" level="1" class="leftmenu_parent group_ active_p">
                <span class="icon color5"><i class="fa fa-key"></i></span>
                <em>权限管理</em>
                <i class="nav_i fa fa-caret-down"></i>
            </a>
            <ul class="child-menu-list" style="display: block;">
                <li class="menu-item left_menus_17">
                    <a level="2" _href="/index/AuthConfig/index.html" class="active">
                        <span class="icon color5"><i class="fa "></i></span>权限配置
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</div>
<div class="container-iframe">
    <div class="ifram-header-nav">
        <div id="ifram_tab_nav" class="ifram-tab-nav">
            <button class="roll-nav roll-left j_tabLeft">
                <i class="fa fa-backward"></i>
            </button>
            <div class="iframe-tab-wp">
                <ul id="min_title_list" class="acrossTab cl">
                    <li class="active default" id="default_li"><a title="工作台" data-href="<?php echo url('Index/welcome'); ?>">工作台</a></li>
                </ul>
                <div class="iframe-btn-group">
                    <button class="roll-nav roll-right j_tabRight">
                        <i class="fa fa-forward"></i>
                    </button>
                    <button class="roll-nav roll-right j_tabRight iframe-border-left">
                        |
                    </button>
                    <button title="去首页" onclick="selectHomeFrame()" class="roll-nav roll-right j_tabRight hover_back_btn">
                        <i class="fa fa-home"></i>
                    </button>
                    <button title="刷新本页" onclick="refreshCurrIframe()" class="roll-nav roll-right j_tabRight hover_back_btn">
                        <i class="fa fa-refresh"></i>
                    </button>
                    <button title="关闭本页" onclick="closeCurrFrame()" class="roll-nav roll-right j_tabRight hover_back_btn">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="iframe_box" class="Hui-article">
        <div class="show_iframe">
            <div hidden class="loading"></div>
            <iframe scrolling="yes" frameborder="0" src="<?php echo url('AuthConfig/index'); ?>"></iframe>
        </div>
    </div>
</div>

<!--TabBar上的快捷菜单-->
<div id="shortcut_menu_bar" hidden>
    <ul>
        <li><a href="javascript:;" id="sm_tabclose" title="关闭">当前关闭</a></li>
        <li><a href="javascript:;" id="sm_tabclose_all" title="关闭所有">关闭所有</a></li>
    </ul>
</div>

<script type="text/javascript" src="/static/js/jquery.min.js?v=<?php echo $version; ?>"></script>
<script type="text/javascript" src="/static/js/common.js?v=<?php echo $version; ?>"></script>

<script src="/static/plugins/notifit/notifIt.js?v=<?php echo $version; ?>"></script>
<link href="/static/plugins/notifit/notifIt.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css" />
<script src="/static/plugins/artdialog/artDialog.js?v=<?php echo $version; ?>"  type="text/javascript"></script>
<script src="/static/plugins/artdialog/plugins/iframeTools.source.js?v=<?php echo $version; ?>" type="text/javascript"></script>
<!--<script src="/static/plugins/artdialog/plugins/iframeTools.js?v=<?php echo $version; ?>" type="text/javascript"></script>-->
<script src="/static/plugins/artdialog/art_common_funs.js?v=<?php echo $version; ?>1" type="text/javascript"></script>
<link href="/static/plugins/artdialog/skins/twitter.css?v=<?php echo $version; ?>" type="text/css" rel="stylesheet">

<script>
    var openSupSystemUrl = '<?php echo url("Index/openSupSystem"); ?>';
</script>
<!-- 自定义js -->
<script type="text/javascript" src="/static/js/frame_index.js?v=<?php echo $version; ?>1"></script>
<script type="text/javascript" src='/static/js/admin_index.js?v=<?php echo $version; ?>1'></script>

</body>
</html>