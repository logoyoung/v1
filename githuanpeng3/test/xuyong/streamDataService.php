<?php
require __DIR__.'/../../include/init.php';
use service\live\StreamDataService;


$stream = StreamDataService::getMultiStreamByAnchorUid(1870);

print_r($stream);

print_r(StreamDataService::getOldMasterStreamByMultiStream($stream));