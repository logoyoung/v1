<?php
require __DIR__.'/../../include/init.php';
use lib\live\LiveHelper;
use service\live\LiveService;

//var_dump(LiveHelper::getlivebyid(['656492','657316','657262'],['poster','status'],false));

print_r(LiveService::getSlaveDataByLiveId(657349));