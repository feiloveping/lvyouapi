<?php



class SignUtil
{
    static public function createLinkString($para,$k)
    {
        if ($para == NULL || !is_array($para))
            return "";
        $linkString = "";
        ksort($para);
        foreach ($para as $key => $value) {
            if ($para[$key] != '') {
                $linkString .= $key . "=" . $value . "&";
            }
        }// 去掉最后一个&字符
        $linkString = rtrim($linkString,'&') . $k;
        return $linkString;
    }
    static public function getRand( $len = 7 )
    {
        $numbers = range(10, 99);
        //shuffle 将数组顺序随即打乱   
        shuffle($numbers);
        $start = mt_rand(1, 10);
        //取从指定定位置开始的若干数
        $result = array_slice($numbers, $start, $len);
        $random = '';
        for ($i = 0; $i < $len; $i++) {
            $random = $random . $result[$i];
        }
        return $random;
    }

    static public function http_post_my_data($url, $post_data){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);

        if (curl_errno($ch)) {
            echo '错误:'.curl_error($ch);
        }
        curl_close($ch);
        return $output;
    }
}


        $url            =   "https://qr-test2.chinaums.com";
        $query = [
            "msgSrc" => "WWW.TEST.COM",
            "billNo" => "3194" . date('Ymd', time()) . SignUtil::getRand(8),
            "billDate" => date('Y-m-d', time()), // 账单日期，格式yyyy-MM-dd
            "totalAmount" => 1, // 支付总金额
            'msgType' => 'bills.getQRCode',
            'requestTimestamp' => date('Y-m-d H:i:s', time()),
            'mid' => '898340149000005',
            'tid' => '88880001',
            'instMid' => 'QRPAYDEFAULT',
            'tid' => '88880001',
        ];
        $data =  ['goods'=>
            [
                "body"=>"课程套餐",
                "goodsCategory"=>"Auto",
                "goodsId"=>"258",
                "goodsName"=>"课程套餐",
                "price"=>"2",
                "quantity"=>"1"
            ]
        ];

        $post_data           =   json_encode($data);
        ksort($query, SORT_STRING);
        $query_string = '';
        foreach($query as $key => $value) {
            $query_string .= $key.'='.$value.'&';
        }
        $key = 'fcAmtnx7MwismjWNhNKdHC44mNXtnEQeJkRrhKJwyrW2ysRR';
        $query_string   = rtrim($query_string, '&');
        $param   = $query_string . $post_data;
        $sign  = md5( $param . $key);
        $url = "https://qr-test2.chinaums.com/bills/qrCode.do?id={$qrCodeId}";
        //$url            = $url .'?'.$query_string  . '&sign=' .rawurlencode($sign);
	    $query['goods'] = $data['goods'] ;
	    $query['sign'] = $sign;
	    $post_datas = json_encode($query);

        $qrCodeId = '3194' . date('Ymdhis', time()) . SignUtil::getRand(5);
        $url = "https://qr-test2.chinaums.com/bills/qrCode.do?id={$qrCodeId}";

	    $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_datas);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);

        if (curl_errno($ch)) {
            echo '错误:'.curl_error($ch);
        }
        curl_close($ch);
        print_r($output);



