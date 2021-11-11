<?php if (!defined('THINK_PATH')) exit(); /*a:8:{s:91:"D:\2code\1php\zxy\auth-config\public/../application/index\view\auth_config\config_menu.html";i:1630997282;s:72:"D:\2code\1php\zxy\auth-config\application\index\view\base\main_base.html";i:1630918397;s:76:"D:\2code\1php\zxy\auth-config\application\index\view\common\header_file.html";i:1630918397;s:76:"D:\2code\1php\zxy\auth-config\application\index\view\common\footer_file.html";i:1501032343;s:74:"D:\2code\1php\zxy\auth-config\application\index\view\common\artdialog.html";i:1630918397;s:72:"D:\2code\1php\zxy\auth-config\application\index\view\common\notifit.html";i:1630918396;s:70:"D:\2code\1php\zxy\auth-config\application\index\view\common\jGrid.html";i:1630918397;s:77:"D:\2code\1php\zxy\auth-config\application\index\view\common\searchSelect.html";i:1630918397;}*/ ?>
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
        
<link rel="stylesheet" type="text/css" href="/static/plugins/ztree/zTreeStyle/zTreeStyle.css?v=<?php echo $version; ?>" media="all">
<link rel="stylesheet" type="text/css" href="/static/css/config_menu.css?v=<?php echo $version; ?>"/>
<style>
    .select2-container .select2-selection--single {
        height: 32px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
    }
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
        <div class="config_content">
            <div class="panel-default panel-auto list-search-bar">
                <div class="auth_tab row float-l">
                    <button class="btn btn-default btn-auto btn-sm" onclick="exportSql()" >
                        导出菜单sql
                    </button>
                </div>
                <div class="row float-r">
                    <form action="<?php echo url('index'); ?>" class="form-search" id="form_search" onsubmit="return false;">
                        <div class="inline-block checkbox-block" >
                            <label for="last_seven_day" class="checkbox-label">最近七天</label>
                            <div class="checkbox-input">
                                <input role="checkbox" id="last_seven_day" name="last_seven_day" class="u-chk"
                                       type="checkbox"/>
                                <input id="is_last_seven_day" name="is_last_seven_day" value="false" type="hidden"/>
                            </div>
                        </div>
                        <div class="inline-block form-group">
                            <div class="form-group inline-block select_inline">
                                <ul _input="auth_status" class="menu_select_ul">
                                    <li _value="-1">全部</li>
                                    <li _value="1" class="msu_active">需要控制</li>
                                    <li _value="0">不需要</li>
                                </ul>
                                <input name="auth_status" id="auth_status" value="1" type="hidden"/>
                            </div>
                            <div class="form-group inline-block select_inline">
                                <ul _input="is_select" class="menu_select_ul">
                                    <li _value="-1">全部</li>
                                    <li _value="1" class="msu_active">未关联</li>
                                    <li _value="2">已关联</li>
                                </ul>
                                <input name="is_select" id="is_select" value="1" type="hidden"/>
                            </div>
                            <div class="form-group inline-block form-float-left">
                                <select title="选择控制器" name="controller_select" id="controller_select"
                                       onchange="selectChange()" style="width:200px;">
                                    <option value="-1">==全部控制器==</option>
                                    <?php if(is_array($ControllerSelectData) || $ControllerSelectData instanceof \think\Collection || $ControllerSelectData instanceof \think\Paginator): $i = 0; $__LIST__ = $ControllerSelectData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?>
                                        <option value="<?php echo $data['name']; ?>"><?php echo $data['name']; ?>(<?php echo $data['auth_name']; ?>)</option>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                            <div class="search-box inline-block">
                                <input title="可输入菜单地址/创建人进行搜索" class="search-inp" name="searchinfo" placeholder="菜单地址/创建人" style="width: 150px;" type="text"/>
                            </div>
                            <div class="btn-group inline-block" role="group">
                                <button title="搜索匹配的权限函数" class="btn btn-default btn-auto btn-sm" id="btn_search">
                                    <i class="fa fa-search"></i>
                                    搜索
                                </button>
                                <button class="btn btn-light btn-auto btn-sm" id="btn_search_all" onclick="window.location.reload();">
                                    查看全部
                                </button>
                                <button title="同步最新的权限函数" class="btn btn-default btn-auto btn-sm" onclick="updateAuth();">
                                    <i class="fa fa-refresh"></i>
                                    同步
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="content_left">
                <div class="menu_content">
                    <div class="menu_title">
                        <span>菜单列表</span>
                        <img title="已禁用：带删除线&#10;不显示：灰色字体&#10;" class="menu_list_img" src="/static/image/question.png"/>
                    </div>
                    <button title="展开或者折叠菜单" class="open_or_fold" onclick="expandAll()" >
                        <i class="fa fa-angle-double-down"></i>
                    </button>
                    <div class="menu_list">
                        <ul id="treeRule" class="ztree"></ul>
                    </div>
                    <button title="将选中的权限函数关联至选中的菜单" class="auth_main_btn btn btn-default btn-auto btn-sm" onclick="relationMenu()">
                        <i class="fa fa-refresh"></i>
                        关联
                    </button>
                </div>
            </div>
            <div class="content_right">
                <table id="gridTable">
                </table>
                <div id="gridPager">
                </div>
            </div>
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

<!--下拉选择-->
<link href="/static/plugins/select2/css/select2.min.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css"/>
<script src="/static/plugins/select2/js/select2.min.js?v=<?php echo $version; ?>" type="text/javascript"></script>

<script type="text/javascript">
    //权限编号
    var auth_config_id = '<?php echo $auth_config_id; ?>';
    //module类别
    var module = '<?php echo $module; ?>';
    var listUrl='<?php echo url("AuthConfig/getMethodAll"); ?>?auth_config_id=<?php echo $auth_config_id; ?>';
    var getMenuListUrl = "<?php echo URL('AuthConfig/getMenuList'); ?>";
    //配置菜单
    var indexUrl = "<?php echo url('AuthConfig/index'); ?>";
    //删除菜单
    var deleteMenuUrl = "<?php echo URL('AuthConfig/deleteMenu'); ?>?menu_id=";
    //更新权限配置
    var updateUrl = '<?php echo url("AuthConfig/updateAuthConfig"); ?>?auth_config_id=<?php echo $auth_config_id; ?>';
    //菜单排序
    var menuSort = "<?php echo url('AuthConfig/menuSort'); ?>";
    //下载菜单Url
    var downloadMenuUrl = "<?php echo url('AuthConfig/downloadMenu'); ?>?auth_config_id=<?php echo $auth_config_id; ?>";
    //导出SqlUrl
    var exportSqlUrl = "<?php echo url('AuthConfig/exportSql'); ?>?auth_config_id=<?php echo $auth_config_id; ?>";
    //上传菜单Url
    var uploadMenuUrl = "<?php echo url('AuthConfig/uploadMenu'); ?>";
    //菜单关联
    var relationMenuUrl = "<?php echo url('AuthConfig/relationMenu'); ?>";
    //修改菜单
    var editMenuUrl = "<?php echo URL('MenuAuth/edit_menu'); ?>?menu_id=";
    //添加菜单
    var addMenuUrl = "<?php echo URL('MenuAuth/add_menu'); ?>?auth_config_id=<?php echo $auth_config_id; ?>";
    //得到可上传数和可下载数
    var getUpAndOnNumberUrl = "<?php echo url('AuthConfig/getUpAndOnNumber'); ?>?auth_config_id=<?php echo $auth_config_id; ?>";
    //异步更新控制器
    var updateControllerSelectUrl = "<?php echo url('AuthConfig/updateControllerSelect'); ?>?auth_config_id=<?php echo $auth_config_id; ?>";
    //同步后刷新控制器列表
    var relSelectControllerUrl = "<?php echo url('AuthConfig/refSelectController'); ?>?auth_config_id="+auth_config_id;
</script>
<script type="text/javascript" src="/static/plugins/ztree/jquery.ztree.core.js?v=<?php echo $version; ?>"></script>
<script type="text/javascript" src="/static/plugins/ztree/jquery.ztree.excheck.js?v=<?php echo $version; ?>"></script>
<script type="text/javascript" src="/static/plugins/ztree/jquery.ztree.exedit.js?v=<?php echo $version; ?>"></script>
<script type="text/javascript" src="/static/js/config_menu.js?v=<?php echo $version; ?>"></script>

</html>