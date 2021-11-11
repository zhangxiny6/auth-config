<?php if (!defined('THINK_PATH')) exit(); /*a:7:{s:85:"D:\2code\1php\zxy\auth-config\public/../application/index\view\auth_config\index.html";i:1631159739;s:72:"D:\2code\1php\zxy\auth-config\application\index\view\base\main_base.html";i:1630918397;s:76:"D:\2code\1php\zxy\auth-config\application\index\view\common\header_file.html";i:1630918397;s:76:"D:\2code\1php\zxy\auth-config\application\index\view\common\footer_file.html";i:1501032343;s:74:"D:\2code\1php\zxy\auth-config\application\index\view\common\artdialog.html";i:1630918397;s:72:"D:\2code\1php\zxy\auth-config\application\index\view\common\notifit.html";i:1630918396;s:70:"D:\2code\1php\zxy\auth-config\application\index\view\common\jGrid.html";i:1630918397;}*/ ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="renderer" content="webkit">
        <meta charset="utf-8"/>
        
        <title>
            <?php echo (isset($WEB_SITE_TITLE) && ($WEB_SITE_TITLE !== '')?$WEB_SITE_TITLE:''); ?>
        </title>
        
        <meta content="" name="description"/>
        <meta content="caiweiming" name="author"/>
        <meta content="noindex, nofollow" name="robots"/>
        <meta content="width=device-width,initial-scale=1,maximum-scale=1.0" name="viewport"/>
        <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
        <script type="text/javascript">
            //附件访问路径
            var _public_file_domain = "";
            var _public_file_domain_ym = '<?php echo config("FileManageGetUrl"); ?>';
            var show_logistics_url = "<?php echo url('Shipping/show_logistics'); ?>";
            var getUserListUrl = "<?php echo url('Common/getUserInfo'); ?>?keyword=";
        </script>
         <!-- 框架基本 css文件 -->
<link href="/static/plugins/bootstrap/bootstrap.css?v=<?php echo $version; ?>" type="text/css" rel="stylesheet" />
<link href="/static/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css?v=<?php echo $version; ?>" type="text/css" rel="stylesheet" />
<link href="/static/ui/css/font-awesome.min.css?v=<?php echo $version; ?>" type="text/css" rel="stylesheet" />
<link href="/static/ui/css/style.css?v=<?php echo $version; ?>" type="text/css" rel="stylesheet" />
<!-- 自定义基础 css文件 -->
<link href="/static/css/common.css?v=<?php echo $version; ?>" type="text/css" rel="stylesheet" />
<link href="/static/css/page.css?v=<?php echo $version; ?>" type="text/css" rel="stylesheet" />
<!-- 基础js文件 -->
<script type="text/javascript" src="/static/js/jquery.min.js?v=<?php echo $version; ?>"></script>
<script type="text/javascript" src="/static/js/common.js?v=<?php echo config('version.fileversion'); ?>"></script>
<script type="text/javascript" src="/static/plugins/bootstrap/bootstrap.min.js?v=<?php echo $version; ?>"></script>
        <style type="text/css">

        </style>
        <!--页面css-->
        
<style>

</style>

    </head>
    <body>
        <div class="_show_public_loading_div" id="_show_public_loading_div">
            <img src="/static/image/loading.gif" style="width:20px;vertical-align: middle;">
            处理中...
            </img>
        </div>
        <div class="main-base-content">
            <!-- 主体内容 -->
            
<div class="container-padding">
    <div class="gridPanel">
        <div class="panel-default panel-auto list-search-bar">
            <div class="row float-l">
                <button class="btn btn-default btn-auto btn-sm" onclick="addOrEdit(0);">
                    <i class="fa fa-plus-circle"></i>新增模块
                </button>
            </div>
        </div>
        <table id="gridTable">
        </table>
        <div id="gridPager">
        </div>
    </div>
</div>

        </div>
        <!--artdialog弹窗-->
<script src="/static/plugins/artdialog/artDialog.js?v=<?php echo $version; ?>"  type="text/javascript"></script>
<script src="/static/plugins/artdialog/plugins/iframeTools.source.js?v=<?php echo $version; ?>" type="text/javascript"></script>
<!--<script src="/static/plugins/artdialog/plugins/iframeTools.js?v=<?php echo $version; ?>" type="text/javascript"></script>-->
<script src="/static/plugins/artdialog/art_common_funs.js?v=<?php echo $version; ?>1" type="text/javascript"></script>
<link href="/static/plugins/artdialog/skins/twitter.css?v=<?php echo $version; ?>" type="text/css" rel="stylesheet">

<!-- notifit 提示文件 -->
<script src="/static/plugins/notifit/notifIt.js?v=<?php echo $version; ?>"></script>
<link href="/static/plugins/notifit/notifIt.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css" />
    </body>
    <!--页面js-->
    
<script src="/static/plugins/artdialog/artDialog.js?v=<?php echo $version; ?>"  type="text/javascript"></script>
<script src="/static/plugins/artdialog/plugins/iframeTools.source.js?v=<?php echo $version; ?>" type="text/javascript"></script>
<!--<script src="/static/plugins/artdialog/plugins/iframeTools.js?v=<?php echo $version; ?>" type="text/javascript"></script>-->
<script src="/static/plugins/artdialog/art_common_funs.js?v=<?php echo $version; ?>1" type="text/javascript"></script>
<link href="/static/plugins/artdialog/skins/twitter.css?v=<?php echo $version; ?>" type="text/css" rel="stylesheet">

<script src="/static/plugins/notifit/notifIt.js?v=<?php echo $version; ?>"></script>
<link href="/static/plugins/notifit/notifIt.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css" />
<link href="/static/plugins/jqgrid/css/jqgrid.css?v=20170811" rel="stylesheet" type="text/css" />
<script src="/static/plugins/jqgrid/grid.locale-cn.js?v=20170811" type="text/javascript"></script>
<script src="/static/plugins/jqgrid/jqGrid.js?v=20170811" type="text/javascript"></script>
<script src="/static/plugins/jqgrid/jqGrid.extend.js?v=20170811" type="text/javascript"></script>

<script>
    var listUrl='<?php echo url("AuthConfig/index"); ?>';
    var addUrl = '<?php echo url("AuthConfig/addAuthConfig"); ?>?id=';   //添加权限配置
    var getClassAuthUrl = "<?php echo url('AuthConfig/getClassAuth'); ?>?id=";    //得到类权限
    var configMenuUrl = "<?php echo url('AuthConfig/configMenu'); ?>?id=";  //配置菜单
    var menuConfigUrl = "<?php echo url('MenuAuth/index'); ?>?id=";  //菜单配置
    var delUrl = "<?php echo url('AuthConfig/deleteAuthConfig'); ?>"; //删除
    var menu_field_module = "<?php echo $menu_field_module; ?>";
</script>
<script type="text/javascript" src="/static/js/auth_config.js?v=<?php echo $version; ?>" ></script>

</html>