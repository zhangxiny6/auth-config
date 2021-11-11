<?php if (!defined('THINK_PATH')) exit(); /*a:8:{s:81:"D:\2code\1php\zxy\auth-config\public/../application/index\view\install\index.html";i:1632879492;s:72:"D:\2code\1php\zxy\auth-config\application\index\view\base\main_base.html";i:1630918397;s:76:"D:\2code\1php\zxy\auth-config\application\index\view\common\header_file.html";i:1630918397;s:76:"D:\2code\1php\zxy\auth-config\application\index\view\common\footer_file.html";i:1501032343;s:74:"D:\2code\1php\zxy\auth-config\application\index\view\common\artdialog.html";i:1630918397;s:72:"D:\2code\1php\zxy\auth-config\application\index\view\common\notifit.html";i:1630918396;s:77:"D:\2code\1php\zxy\auth-config\application\index\view\common\searchSelect.html";i:1630918397;s:70:"D:\2code\1php\zxy\auth-config\application\index\view\common\jGrid.html";i:1630918397;}*/ ?>
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
        
<link rel="stylesheet" href="/static/css/add_or_edit_style_01.css"/>
<link rel="stylesheet" href="/static/plugins/jquery-steps/jquery.step.css"/>
<link rel="stylesheet" href="/static/css/details_style_01.css"/>
<link rel="stylesheet" href="/static/css/install.css"/>

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
    <div class="step-body" id="myStep">
        <div class="step-header" style="width:1000px;">
            <ul>
                <li><p>欢迎使用</p></li>
                <li><p>运行环境检查</p></li>
                <li><p>Mysql配置</p></li>
                <li><p>权限规则配置</p></li>
                <li><p>安装完成</p></li>
            </ul>
        </div>
    </div>


    <div id="wizard" class="wizard clearfix">
        <div class="steps-content clearfix">
            <input type="hidden" id="curStepNum" value="<?php echo $value; ?>"/>
            <section id="wizard-p-1" role="tabpanel" aria-labelledby="wizard-h-1" class="body current" aria-hidden="false" style="left:0;">
                <div class="welcome_text">尊敬的用户，您好，欢迎使用权限管理系统！</div>
                <dl class="welcome">
                    <dt>一、产品背景：</dt>
                    <dd>对于海量的权限节点，人工添加维护权限极易出现重复、遗漏的现象。并且对于添加人员，也是一项机械式的重复劳动。通过此系统，实现一键获取权限，权限配置完全可视化。为提高权限配置效率，避免纰漏，提供支持。</dd>
                </dl>
                <dl class="welcome">
                    <dt>二、产品目标：</dt>
                    <dd>通过权限配置平台，将实现以下功能：<dd>
                    <dd>1、根据配置规则读取代码中所有类与函数，并生成权限结构<dd>
                    <dd>2、可视化配置权限，通过筛选、关联精准配置权限，减少操作<dd>
                </dl>
                <dl class="welcome">
                    <dt>三、产品须知：</dt>
                    <dd>1、请确保函数头符合规则，否则将无法扫描出权限，其中authStatus、createTime、author可无，或您的权限标识不是authName，下面是系统建议使用的权限标识（可自定义）。<dd>
                    <dd>
                        <img src="/static/image/code_demo.png"/>
                    </dd>
                </dl>
            </section>
            <section id="wizard-p-2" role="tabpanel" aria-labelledby="wizard-h-2" class="body " aria-hidden="true">
                <?php if(is_array($runtimeCheckArr) || $runtimeCheckArr instanceof \think\Collection || $runtimeCheckArr instanceof \think\Paginator): $i = 0; $__LIST__ = $runtimeCheckArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$checkItem): $mod = ($i % 2 );++$i;?>
                <dl class="check_runtime">
                    <dt><?php echo $checkItem['title']; ?></dt>
                    <?php if(is_array($checkItem['value']) || $checkItem['value'] instanceof \think\Collection || $checkItem['value'] instanceof \think\Paginator): $i = 0; $__LIST__ = $checkItem['value'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$checkValue): $mod = ($i % 2 );++$i;?>
                    <dd>
                        <?php if(is_array($checkValue) || $checkValue instanceof \think\Collection || $checkValue instanceof \think\Paginator): $i = 0; $__LIST__ = $checkValue;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$valueData): $mod = ($i % 2 );++$i;if($valueData == 'success'): ?>
                        <span class="check_success">√</span>
                        <?php elseif($valueData == 'error'): ?>
                        <span class="check_error" error_code="1">× 检测未通过</span>
                        <?php else: ?>
                        <div><?php echo $valueData; ?></div>
                        <?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    </dd>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </dl>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </section>
            <section id="wizard-p-3" role="tabpanel" aria-labelledby="wizard-h-3" class="body" aria-hidden="true">
                <form action="<?php echo url('createDb'); ?>" method='POST' id="create-db-form"
                      class="vform form-horizontal" onsubmit="return false;">
                    <div class="details-title">
                        <div class="details-title-div">
                            <span class="details-title-text">创建数据库</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label">数据库类型:</label>
                            <div class="col-sm-10">
                                <select name="db_type" id="db_type">
                                    <option value="0">mysql</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label"><span class="c-red">*</span>数据库服务器:</label>
                            <div class="col-sm-10">
                                <input type="text" name="db_hostname" id="db_hostname"
                                       class="text form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label"><span class="c-red">*</span>数据库用户名:</label>
                            <div class="col-sm-10">
                                <input type="text" id="db_username" name="db_username"
                                       class="text form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label"><span class="c-red">*</span>数据库密码:</label>
                            <div class="col-sm-10">
                                <input type="password" id="db_password" name="db_password"
                                       class="text form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label"><span class="c-red">*</span>数据库端口:</label>
                            <div class="col-sm-10">
                                <input type="text" id="db_hostport" name="db_hostport"
                                       class="text form-control" value="3306"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="details-title">
                            <div class="details-title-div">
                                <span class="details-title-text">创始人帐号信息</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label"><span class="c-red">*</span>管理员:</label>
                            <div class="col-sm-10">
                                <input type="text" id="u_username" name="u_username"
                                       class="text form-control" value="admin"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label"><span class="c-red">*</span>密码:</label>
                            <div class="col-sm-10">
                                <input type="password" id="u_password" name="u_password"
                                       class="text form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label"><span class="c-red">*</span>确认密码:</label>
                            <div class="col-sm-10">
                                <input type="password" id="u_confirm_password" name="u_confirm_password"
                                       class="text form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="details-title">
                        <div class="details-title-div">
                            <span class="details-title-text">生成的数据库与表（若之前生成过，将覆盖）</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label">数据库:</label>
                            <div class="col-sm-10 form-group-text">
                                auth_manage_db（1个）
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label">数据表:</label>
                            <div class="col-sm-10 form-group-text">
                                am_auth_class、am_auth_config、am_manage_users（3个）
                            </div>
                        </div>
                    </div>
                </form>
            </section>
            <section id="wizard-p-4" role="tabpanel" aria-labelledby="wizard-h-4" class="body" aria-hidden="true">
                <form action="<?php echo url('createAuth'); ?>" method='POST' id="create-auth-form"
                      class="vform form-horizontal" onsubmit="return false;">
                    <div class="details-title">
                        <div class="details-title-div">
                            <span class="details-title-text">需要控制权限的数据库</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label"><span class="c-red">*</span>数据库:</label>
                            <div class="col-sm-10">
                                <select id="controller_db" name="controller_db">
                                    <option value="0">==请选择==</option>
                                </select>
                                <span class="check-tips">需要控制权限的数据库</span>
                                <span id="menuAuthChoose" style="display: none;" class="help-block">系统已自动填充以下信息</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label"><span class="c-red">*</span>菜单表:</label>
                            <div class="col-sm-10">
                                <select id="controller_menu" name="controller_menu">
                                    <option value="0">==请先选择数据库==</option>
                                </select>
                                <span class="check-tips">需要控制权限的菜单表</span>
                            </div>
                        </div>
                    </div>
                    <div class="details-title">
                        <div class="details-title-div">
                            <span class="details-title-text">菜单表对齐</span>
                            <span class="check-tips">（系统只控制以下字段，其它字段如图标、菜单状态等，需要您自己管理）</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label"><span class="c-red">*</span>菜单主键字段:</label>
                            <div class="col-sm-10">
                                <select id="menu_id" name="menu_id">
                                    <option value="0">==请先选择数据表==</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label"><span class="c-red">*</span>菜单标题字段:</label>
                            <div class="col-sm-10">
                                <select id="menu_title" name="menu_title">
                                    <option value="0">==请先选择数据表==</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label"><span class="c-red">*</span>菜单地址字段:</label>
                            <div class="col-sm-10">
                                <select id="menu_url" name="menu_url">
                                    <option value="0">==请先选择数据表==</option>
                                </select>
                                <span class="check-tips">服务器验证的地址，非前端地址</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label"><span class="c-red">*</span>菜单排序字段:</label>
                            <div class="col-sm-10">
                                <select id="menu_sort" name="menu_sort">
                                    <option value="0">==请先选择数据表==</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label"><span class="c-red">*</span>菜单父级字段:</label>
                            <div class="col-sm-10">
                                <select id="menu_pid" name="menu_pid">
                                    <option value="0">==请先选择数据表==</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label">模块:</label>
                            <div class="col-sm-10">
                                <select id="menu_module" name="menu_module">
                                    <option value="">==请先选择数据表==</option>
                                </select>
                                <span class="check-tips">当一个菜单表存在多个系统的菜单时，请填写区分的字段</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="details-title">
                            <div class="details-title-div">
                                <span class="details-title-text">函数头定义</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label"><span class="c-red">*</span>权限名称:</label>
                            <div class="col-sm-10">
                                <input type="text" id="auth_authName" name="auth_authName"
                                       class="text form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label">作者:</label>
                            <div class="col-sm-10">
                                <input type="text" id="auth_author" name="auth_author"
                                       class="text form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label">时间:</label>
                            <div class="col-sm-10">
                                <input type="text" id="auth_createTime" name="auth_createTime"
                                       class="text form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-group-item">
                            <label class="col-sm-2 control-label form-label">是否控制:</label>
                            <div class="col-sm-10">
                                <input type="text" id="auth_authStatus" name="auth_authStatus"
                                       class="text form-control"/>
                            </div>
                        </div>
                    </div>
                </form>
            </section>
            <section id="wizard-p-5" role="tabpanel" aria-labelledby="wizard-h-5" class="body" aria-hidden="true">
                <div class="system-message">
                    <div class="jump-success">
                        <div class="img">
                            <img src="/static/image/tpl_img/success.jpg?v=2019">
                        </div>
                        <div class="inf">
                            <img class="icon" src="/static/image/tpl_img/suc-icon.png">安装配置成功！
                        </div>
                    </div>
                    <p class="detail"></p>
                    <p class="jump">
                        <a class="btn btn-default"  href="<?php echo url('Login/index'); ?>">
                            开始使用
                        </a>
                    </p>
                </div>
            </section>
        </div>
    </div>

    <div class="dialog_footer_div_height"></div>
    <div id="submit_buttonbar_postion">
        <div class="f-r">
            <button style="display: none;" id="prev-btn" class="btn btn-default" onclick="changeSteps(-1, false)">
                上一步
            </button>
            <button id="next-btn" class="btn btn-default" onclick="changeSteps(1, true)">
                下一步
            </button>
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
<link href="/static/plugins/select2/css/select2.min.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css"/>
<script src="/static/plugins/select2/js/select2.min.js?v=<?php echo $version; ?>" type="text/javascript"></script>

<link href="/static/plugins/jqgrid/css/jqgrid.css?v=20170811" rel="stylesheet" type="text/css" />
<script src="/static/plugins/jqgrid/grid.locale-cn.js?v=20170811" type="text/javascript"></script>
<script src="/static/plugins/jqgrid/jqGrid.js?v=20170811" type="text/javascript"></script>
<script src="/static/plugins/jqgrid/jqGrid.extend.js?v=20170811" type="text/javascript"></script>

<script type="text/javascript" src="/static/plugins/jquery-steps/jquery.step.js"></script>
<script type="text/javascript">
    var getTablesByDb='<?php echo Url("getTablesByDb"); ?>';
    var getFieldsByTable='<?php echo Url("getFieldsByTable"); ?>';
    $("section").removeClass('current');
    $("#wizard-p-"+'<?php echo $value; ?>').addClass('current');
</script>
<script type="text/javascript" src="/static/js/install.js"></script>

</html>