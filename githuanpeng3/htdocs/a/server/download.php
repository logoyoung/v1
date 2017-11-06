<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/10/17
 * Time: 下午8:05
 */

$file = "/usr/local/huanpeng/tmp/HuanpengSetup_930.exe.zip";

header("Content-type: application/octet-stream");
header('Content-Disposition: attachment; filename="' . basename($file) . '"');
header("Content-Length: ". filesize($file));
readfile($file);