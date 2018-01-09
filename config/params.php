<?php

$phoneCityCode = require __DIR__ .  '/phonecitycode.php';

return [
    'adminEmail' => 'admin@example.com',
    'app_url'=>'https://sq.wmqt.net',
    'api_url'=>'https://youapi.wmqt.net',
    'page_size'=>6,
    'spot_condition_other'=>[
        ['id'=>0,'name'=>'综合排序'],
        ['id'=>1,'name'=>'人气优先'],
        ['id'=>2,'name'=>'低价优先'],
        ['id'=>3,'name'=>'高价优先'],
        ['id'=>4,'name'=>'销量优先'],
    ],
    'article_condition_other'=>[
        ['id'=>0,'ordername'=>'综合排序'],
        ['id'=>1,'ordername'=>'点击量最高'],
        ['id'=>2,'ordername'=>'编辑时间最新'],
    ],

    'myEncrypt_key' => 'hellomyloveping' ,
    'linkmanCardType' => [
        ['id'=>1,'cardtype'=>'身份证'],
        ['id'=>2,'cardtype'=>'护照'],
        ['id'=>3,'cardtype'=>'台胞证'],
        ['id'=>4,'cardtype'=>'港澳通行证'],
        ['id'=>5,'cardtype'=>'军官证'],
    ],
    'typeid'=>[
        'line'      =>      1,
        'hotel'     =>      2,
        'article'   =>      4,
        'spot'      =>      5,
        'tuan'      =>      14,
        'notes'     =>      101,

    ],

    'typeNameforMemberCenter'=>[
        ['id'=>1,'typename'=>'线路'],
        ['id'=>2,'typename'=>'酒店'],
        ['id'=>5,'typename'=>'景点'],
        ['id'=>14,'typename'=>'团购'],
    ],
    'phoneCityCode' => $phoneCityCode,
];
