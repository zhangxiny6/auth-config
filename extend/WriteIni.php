<?php

require '../vendor/autoload.php';

use WriteiniFile\WriteiniFile;

/**
 * @authName 写入ini文件
 * @author zxy
 * @createTime 2021-08-23 15:48:22
 *
 * Class WriteIni
 */
class WriteIni
{
    /**
     * @authName 写入
     * @author zxy
     * @createTime 2021-08-23 15:52:24
     * @param $group
     * @param $key
     * @param $value
     */
    public function updateIni($file, $group, $key, $value)
    {
        $obj = new WriteiniFile($file);
        $obj->update([
            $group => [$key => $value]
        ]);
        $obj->write();
    }
}