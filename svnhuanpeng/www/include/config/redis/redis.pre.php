<?php

return [

    'huanpeng'   => [

        'prefix' => '',
        'debug'  => true,

        'master' => [
                0 =>
                [
                    ['host' => '192.168.21.65', 'port' => 6379, 'timeout' => 2, 'auth' => ''],
                ],

        ],

        'slave'  => [

                0 =>
                [
                    ['host' => '192.168.21.65', 'port' => 6379, 'timeout' => 2, 'auth' => ''],
                ],

        ],

    ],


];