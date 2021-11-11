<?php if (!defined('THINK_PATH')) exit(); /*a:7:{s:95:"D:\2code\1php\zxy\auth-config\public/../application/index\view\auth_config\add_auth_config.html";i:1631159431;s:72:"D:\2code\1php\zxy\auth-config\application\index\view\base\main_base.html";i:1630918397;s:76:"D:\2code\1php\zxy\auth-config\application\index\view\common\header_file.html";i:1630918397;s:76:"D:\2code\1php\zxy\auth-config\application\index\view\common\footer_file.html";i:1501032343;s:74:"D:\2code\1php\zxy\auth-config\application\index\view\common\artdialog.html";i:1630918397;s:72:"D:\2code\1php\zxy\auth-config\application\index\view\common\notifit.html";i:1630918396;s:74:"D:\2code\1php\zxy\auth-config\application\index\view\common\validform.html";i:1630918397;}*/ ?>
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
    .checkbox.checkbox-inline{margin-right:10px;}
    .check-tips{
        color:#ADADAD;
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
            
<div class="container-padding" style="padding:15px;">
    <div class="panel-default">
        <div class="panel-body">
            <form action="<?php echo URL(''); ?>" class="vform form-horizontal" id="config_form" method="POST">
                <input type="hidden" name="id" value="<?php echo $info['id']; ?>"/>
                <div class="form-group">
                    <label class="col-sm-2 control-label form-label">
                        <span class="c-red">*</span>
                        模块名称
                    </label>
                    <div class="col-sm-10">
                        <input class="form-control input-w-40" datatype="*" id="name" name="name"
                               nullmsg="请输入模块名称" placeholder="请输入模块名称" value="<?php echo $info['name']; ?>" type="text"/>
                        <span class="help-block">
                            如crm管理系统
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label form-label">
                        <span class="c-red">*</span>
                        文件夹路径
                    </label>
                    <div class="col-sm-10">
                        <textarea class="form-control input-w-40" name="file_path" nullmsg="请输入文件路径"
                                  placeholder="请输入文件路径" rows="3"><?php echo $info['file_path']; ?></textarea>
                        <span class="help-block">
                            需要权限控制的文件夹路径，请填写绝对路径，支持Windows路径与Linux路径
                        </span>
                    </div>
                </div>
                <?php if($menu_field_module != ''): ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label form-label">
                        模块界定值
                    </label>
                    <div class="col-sm-10">
                        <input class="form-control input-w-40" name="module" placeholder="请输入模块界定值"
                               value="<?php echo $info['module']; ?>" datatype="*" nullmsg="请输入模块界定值"/>
                        <span class="help-block">
                            菜单表可能存在多个系统的菜单配置，需填写界定的值
                        </span>
                    </div>
                </div>
                <?php endif; ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label form-label">
                        排序
                    </label>
                    <div class="col-sm-10">
                        <input class="form-control input-w-40" id="sort" name="sort" onafterpaste="this.value=this.value.replace(/\D/g,'')" onkeyup="this.value=this.value.replace(/\D/g,'')" type="text"
                               value="<?php echo (isset($info['sort']) && ($info['sort'] !== '')?$info['sort']:'0'); ?>"/>
                        <span class="help-block" id="helpBlock">
                            值越大，排序越靠前
                        </span>
                    </div>
                </div>
                <div class="dialog_footer_div_height"></div>
                <div id="submit_buttonbar_postion">
                    <div class="f-r">
                        <button class="btn btn-default" id="submit" type="submit">
                            保存并生成权限
                        </button>
                        <button class="btn btn-light" type="button" value="" onclick="art.dialog.close();">
                            关闭
                        </button>
                    </div>
                </div>
            </form>
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
    
<link rel="stylesheet" type="text/css" href="/static/plugins/validform/css/validformstyle.css?v=<?php echo $version; ?>"/>
<script src="/static/plugins/validform/js/Validform_v5.3.2.js?v=<?php echo $version; ?>" type="text/javascript"></script>
<script src="/static/plugins/validform/js/validforminit.js?v=<?php echo $version; ?>12" type="text/javascript"></script>

<script>
</script>

</html>