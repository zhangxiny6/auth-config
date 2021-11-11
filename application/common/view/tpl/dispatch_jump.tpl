{__NOLAYOUT__}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>提示窗口</title>
    <meta content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0" name="viewport"/>
    <style type="text/css">
        body{background: #ffffff;padding: 0px;margin:0;overflow: hidden;width: 100%;}
        .system-message{margin: auto;margin-top: 50px;}
        .img{width: 100%; height: 180px;margin: 0 auto;text-align: center; }
        .img img{height: 180px}
        .system-message .icon{width: 20px;vertical-align: sub;margin-right: 6px;}
        .inf{margin: 0 auto; text-align: center;font-size: 18px; text-decoration: none;}
        .system-message .jump{ padding-top:60px; margin: 0 auto; text-align: center;font-size: 14px;letter-spacing:2px; text-decoration: none;}
        .system-message .jump a{ color: #333; }
        .system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display: none; }
        .jump-success .inf{
            color:green;
        }
         .jump-error .inf{
            color:red;
        }
    </style>
</head>
<body>
 
<div class="system-message">
    <?php switch ($code) {?>
    <?php case 1:?>
    <div class="jump-success">
        <div class="img" >
            <img src="<?php echo /static/image/tpl_img/success.jpg?v=2019" />
        </div>
        <div class="inf"><img class='icon' src="<?php echo /static/pc/image/tpl_img/suc-icon.png"/><?php echo(strip_tags($msg));?></div>
    </div>
    <?php break;?>
    <?php case 0:?>
     <div class="jump-error">
        <div class="img"  >
            <img src="<?php echo /static/pc/image/tpl_img/error.jpg?v=2019" />
        </div>
        <div class="inf"><img class='icon' src="<?php echo /static/pc/image/tpl_img/err-icon.png"/><?php echo(strip_tags($msg));?></div>
    </div>
    <?php break;?>
    <?php } ?>
    <p class="detail"></p>
    <p class="jump">
       <a id="href" href="<?php echo($url);?>">返回或跳转</a> 
    </p>
</div>
 
    
</body>
</html>
