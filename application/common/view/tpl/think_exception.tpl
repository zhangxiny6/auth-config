<?php
if (!function_exists('parse_padding')) {
    function parse_padding($source)
    {
        $length = strlen(strval(count($source['source']) + $source['first']));
        return 40 + ($length - 1) * 8;
    }
}

if (!function_exists('parse_class')) {
    function parse_class($name)
    {
        $names = explode('\\', $name);
        return '<abbr title="' . $name . '">' . end($names) . '</abbr>';
    }
}

if (!function_exists('parse_file')) {
    function parse_file($file, $line)
    {
        return '<a class="toggle" title="' . "{$file} line {$line}" . '">' . basename($file) . " line {$line}" . '</a>';
    }
}

if (!function_exists('parse_args')) {
    function parse_args($args)
    {
        $result = [];

        foreach ($args as $key => $item) {
            switch (true) {
                case is_object($item):
                    $value = sprintf('<em>object</em>(%s)', parse_class(get_class($item)));
                    break;
                case is_array($item):
                    if (count($item) > 3) {
                        $value = sprintf('[%s, ...]', parse_args(array_slice($item, 0, 3)));
                    } else {
                        $value = sprintf('[%s]', parse_args($item));
                    }
                    break;
                case is_string($item):
                    if (strlen($item) > 20) {
                        $value = sprintf(
                            '\'<a class="toggle" title="%s">%s...</a>\'',
                            htmlentities($item),
                            htmlentities(substr($item, 0, 20))
                        );
                    } else {
                        $value = sprintf("'%s'", htmlentities($item));
                    }
                    break;
                case is_int($item):
                case is_float($item):
                    $value = $item;
                    break;
                case is_null($item):
                    $value = '<em>null</em>';
                    break;
                case is_bool($item):
                    $value = '<em>' . ($item ? 'true' : 'false') . '</em>';
                    break;
                case is_resource($item):
                    $value = '<em>resource</em>';
                    break;
                default:
                    $value = htmlentities(str_replace("\n", '', var_export(strval($item), true)));
                    break;
            }

            $result[] = is_int($key) ? $value : "'{$key}' => {$value}";
        }

        return implode(', ', $result);
    }
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>错误提示</title>
     <meta content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0" name="viewport"/>
    <style type="text/css">
        body{background: #FFF}
        .img{width: 80%; height: 180px;margin: 0 auto;text-align: center;margin: auto;margin-top:20px; }
        .img img{
            height: 180px;
        }
        .system-message {width:80%;margin:auto;}
        .system-message p{ font-size: 14px; line-height: 20px; color:#666; }
        .system-message .title{font-weight: bold;margin-top:20px;}
        p{margin:0;padding:0;}
    </style>
</head>
<body>

<div class="system-message">
    <div class="img" >
        <img src="<?php echo /static/pc/image/tpl_img/exception.png">
    </div>
    <div >
        <div class="title">啊哦，系统运行出现了错误，请先不要着急，参考解决方案处理</div>
        <p class="inf">错误编码：<?php echo $code; ?></p>
        <p class="inf">错误位置：<?php 
        $name=$name??'';
        $file=$file??'';
        $line=$line??'';
        echo sprintf('%s in %s', parse_class($name), parse_file($file, $line)); ?></p>
        <p class="inf">错误内容：<?php echo htmlentities($message); ?></p>
    </div>
    <div>
        <div class="title">解决方案：</div>
        <p>1、请检查网页链接是否正确</p>
        <p>2、请检查网络是否连接成功，可尝试刷新界面重试 &nbsp;&nbsp;&nbsp;<a href='javascript:void();' onclick="window.location.reload();">刷新</a></p>
        <p>3、如还是无法解决错误，可联系开发者，QQ：2639347794 ?></p>
    </div>

</div>


</body>
</html>
