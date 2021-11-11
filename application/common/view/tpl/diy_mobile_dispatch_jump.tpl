{__NOLAYOUT__}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0" name="viewport"/>
    <meta content="telephone=no,email=no,date=no,address=no" name="format-detection"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <title>操作提示</title>

    <link rel="stylesheet" href="/static/mobile/plugins/weui/lib/weui.min.css"/>
    <style>
        body{background: #ffFFFF;}
        .warm_tip_title{
            font-size: 1.2rem;
            color: #8a819f;
        }
        .warm_tip_img{
            /*margin: auto;*/
            /*width: 2rem !important;*/
            /*float: left;*/
            /*margin-top: 0.2rem;*/
            width: 100%;
            margin: auto;
        }
        .tip_title{
            display: inline-block;
            width: 50%;
            height: 2.5rem;
            line-height: 2.5rem;
        }
        .tip_title font{
            display: inline-block;
            height: 2.5rem;
            line-height: 2.5rem;
            font-size: 1.5rem;
            color:#26A23D;
        }
    </style>
</head>
<body>

<div class="page msg_success js_show">
    <div class="weui-msg">
        <?php switch ($code) {?>
        <?php case 1:?>
        <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
        <?php break;?>
        <?php case 0:?>
        <img class='warm_tip_img' src="<?php echo /static/pc/image/tpl_img/tips.png" />
        <!--<div class="weui-msg__icon-area aui-iconfont"><i class="weui-icon-warn weui-icon_msg aui-iconfont"></i></div>-->
        <?php break;?>
        <?php } ?>

        <div style="clear: both;height: 1.5rem;width: 100%;display: inline-block;"></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title"><?php echo(strip_tags($msg));?></h2>
            <p class="weui-msg__desc"></p>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">

                <?php if(!empty($url)){?>
                <a href="<?php echo($url);?>" class="weui-btn weui-btn_primary">{$data['jumpTitle']|default='马上跳转'}</a>
                <?php }?>
                <a href="javascript:history.back(-1);" class="weui-btn weui-btn_default">返回上一页</a>
            </p>
        </div>
        <div class="weui-msg__extra-area">
            <div class="weui-footer">
                <!-- <a href="/" class="weui-footer__link">返回首页</a>  -->
            </div>
        </div>
    </div>
</div>


</body>
</html>
