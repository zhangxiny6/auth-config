<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:79:"D:\2code\1php\zxy\auth-config\public/../application/index\view\login\index.html";i:1631158824;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="renderer" content="webkit">
    <meta charset="utf-8"/>
    <meta content="IE=edge" http-equiv="X-UA-Compatible"/>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="keywords"/>
    <title>登录到权限管理系统</title>
    <!--css样式文件引用-->
    <link href="/static/ui/css/font-awesome.min.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css"/>
    <link href="/static/plugins/bootstrap/bootstrap.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css"/>
    <link href="/static/ui/css/style.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css"/>
    <link href="/static/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css"/>
    <link href="/static/ui/css/responsive.css?v=<?php echo $version; ?>" rel="stylesheet" type="text/css"/>
    <link href="/static/css/login.css?v=<?php echo $version; ?>2" rel="stylesheet" type="text/css"/>
</head>
<body class="bodybg center_bin_body_ui">
<div class="login-body">
    <div class="login-form" id='login_from_div'>
        <form action="<?php echo url('index'); ?>" onsubmit="return false;" autocomplete="off">
            <input id="page_title" name="page_title" type="hidden"/>
            <div class="top">
                <h1>权限管理系统</h1>
            </div>
            <div class="form-area">
                <input id="loginMode" type="hidden" name="loginMode" value="1"/>
                <div id="tab-content_1" class="tab-content-hi" >
                    <div class="group">
                        <input class="form-control" id="username1" name="username1" placeholder="请输入用户名" type="text" value="<?php echo $username; ?>" />
                        <label class="error loginName">请填写用户名</label>
                    </div>
                    <div class="group">
                        <input class="form-control" id="password" name="password" placeholder="请输入密码" type="password" />
                        <label class="error loginPass">请填写密码</label>
                    </div>

                    <div class="group form-validate-code">
                        <input class="form-control user_login_validation" maxlength="3" id="code_gg" name="validation"
                               placeholder="请输入验证码" type="text" nullmsg="验证码不能为空！"/>
                        <label class="error loginYzm">请填写验证码</label>
                        <!--后面加随机数的参数是为了兼容ie和火狐浏览器点击不更换图片的问题-->
                        <img class="validation_img" src="<?php echo captcha_src(); ?>" alt="captcha" id="yan_captcha" onclick="this.src='<?php echo captcha_src(); ?>?seed='+Math.random()"/>
                    </div>
                </div>
                <div class="checkbox checkbox-primary check-margin">
                    <input checked="" id="rememberCB" name="check_status" type="checkbox" value="1">
                    <label for="rememberCB">记住帐号</label><span class="check-tips"></span>
                    <a href="<?php echo url('FindPass/findPass'); ?>" target="_blank" title="找回登录密码" class="login_forget_pass">忘记密码？</a>
                </div>
                <button id="login_btn" class="btn btn-block submit_btn_login"  type="submit">
                    登&nbsp;&nbsp;录
                </button>
            </div>
            <div class="prompt">为了您更好的体验，请使用<a href='https://www.google.cn/chrome/' target="_blank" class="special_color">谷歌浏览器</a>或者<a  href='http://chrome.360.cn/' target="_blank" class="special_color">360极速浏览器</a></div>
        </form>
    </div>
</div>
<div id='browser_low_tips_div' hidden>
</div>

</body>
<script src="/static/js/jquery.min.js?v=<?php echo $version; ?>" type="text/javascript"></script>
<script src="/static/js/login.js?v=<?php echo $version; ?>1" type="text/javascript"></script>
</html>