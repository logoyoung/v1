<?php

namespace  Wcs;


final class Config
{
    //version
    const WCS_SDK_VER = "1.3.3";


    //url设置
    const WCS_PUT_URL	= 'http://PUT_URL';   //WCS put 上传路径
    //const WCS_GET_URL	= 'http://GET_URL';    //WCS get 上传路径
    const WCS_GET_URL	= 'http://6huanpeng.up11.v1.wcsapi.com';
    const WCS_MGR_URL	= 'http://6huanpeng.mgr11.v1.wcsapi.com';    //WCS MGR 路径

    //access key and secret key
    const WCS_ACCESS_KEY	= '65a6d979f6d2f2a3ffaae8b62c91ac0aa8d5823a';
    const WCS_SECRET_KEY	= 'ca437c484e8a6340088be4f58a7302d20bc9b910';

    //token的deadline,默认是1小时,也就是3600s
    const  WCS_TOKEN_DEADLINE = 3600;

    //上传文件设置
    const WCS_OVERWRITE = 1; //默认文件不覆盖
    //超时时间
    const WCS_TIMEOUT = 0;


    //分片上传参数设置
    const WCS_BLOCK_SIZE = 4194304; //4 * 1024 * 1024 默认块大小4M
    const WCS_CHUNK_SIZE = 4194304; //  4 * 1024 * 1024 默认片大小4M
    const WCS_RECORD_URL = './'; //默认当前文件目录
    const WCS_COUNT_FOR_RETRY = 3;  //超时重试次数

    //并发请求数目
    const WCS_CONCURRENCY = 5;



}

