<?php

namespace app\modules\components\helpers;


class TengchongOrders
{
    /**
     * 产品详情页中提取seo
     * @param $arr
     * @return array
     */
    public static function seo($arr)
    {
        $seoArr = array(
            'seotitle' => empty($arr['seotitle']) ? $arr['title'] : $arr['seotitle'],
            'keyword' => $arr['keyword'],
            'description' => $arr['description']
        );
        foreach ($seoArr as &$v)
        {
            $v = trim($v);
        }
        return $seoArr;
    }

    /**
     * 产品图片
     * @param $picStr
     * @return array
     */
    public static function pic_list($picStr)
    {
        if (empty($picStr))
        {
            return;
        }
        $arr = explode(',', $picStr);
        foreach ($arr as &$v)
        {
            $v = explode('||', $v);
        }
        return $arr;
    }

    /**
     * 产品编号 共6位,不足6位前面被0
     * @param $id
     * @param $prefixId
     * @return string
     */
    public static function product_number($id, $prefixId)
    {
        $arr = array(
            'A' => '01',
            'B' => '02',
            'C' => '05',
            'D' => '03',
            'E' => '08',
            'G' => '13',
            'H' => '14',
            'I' => '15',
            'J' => '16',
            'K' => '17',
            'L' => '18',
            'M' => '19',
            'N' => '20',
            'O' => '21',
            'P' => '22',
            'Q' => '23',
            'R' => '24',
            'S' => '25',
            'T' => '26'
        );
        return array_search($prefixId, $arr) . str_pad($id, 5, "0", STR_PAD_LEFT);
    }

    /**
     * 产品内容页去除style 图片如为相对路径加上图片域名
     * @param $str
     * @return mixed
     */
    /*    public static function strip_style($str)
        {
            $str = preg_replace('~\s?style=".*?"~', '', $str);
            $str = preg_replace('~<([^>]*)>(?:\s|&nbsp;)*</\1>~', '', $str);
            $str = preg_replace('~src="[^http](.*?)"~', "src=\"{$GLOBALS['cfg_m_main_url']}/\\1\"", $str);
            $str = preg_replace(array('~width\s*=\s*([\'"]).*?\1~', '~height\s*=\s*([\'"]).*?\1~'), '', $str);
            return $str;
        }*/

    /*
    * 生成订单编号
    * */
    public static function get_ordersn($kind)
    {
        return str_pad($kind, 3, 0, STR_PAD_LEFT) . date('ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    /*
     * 根据typeid生成msgtype
     * @param int $typeid
     * @param int $num ,第几个状态.
     * @return string $msgtype
     * */
    public static function get_msg_type($typeid, $num)
    {
        switch ($typeid)
        {
            case 1:
                $msgtype = 'line_order_msg' . $num;
                break;
            case 2:
                $msgtype = 'hotel_order_msg' . $num;
                break;
            case 3:
                $msgtype = 'car_order_msg' . $num;
                break;
            case 5:
                $msgtype = 'spot_order_msg' . $num;
                break;
            case 8:
                $msgtype = 'visa_order_msg' . $num;
                break;
            case 13:
                $msgtype = 'tuan_order_msg' . $num;
                break;
            default:
                $msgtype = 'reg';
                break;
        }
        return $msgtype;

    }

    /*
    * 发送短信方法
    * @param int phone
    * @param string prefix
    * @param string content
    * */
    public static function send_msg($phone, $prefix, $content)
    {
        require_once TOOLS_COMMON . 'sms/smsservice.php';

        $prefix = $GLOBALS['cfg_webname'];


        $status = SMSService::send_msg($phone, $prefix, $content);
        $status = json_decode($status);
        return $status;


    }

    /**
     * @param $msgtype
     * @param $num
     * @return array
     */
    public static function get_email_msg($msgtype, $num)
    {
        //参数为数字则为栏目ID
        if (is_numeric($msgtype))
        {
            $msgtype = self::get_msg_type($msgtype, $num);
        }
        $sql = "SELECT * FROM `sline_email_msg` WHERE msgtype='$msgtype'";
        $ar = DB::query(1, $sql)->execute()->as_array();
        $row = $ar[0] ? $ar[0] : array();
        return $row;
    }

    /**
     * @param $maillto
     * @param $title
     * @param $content
     * @return bool
     * 发送邮件
     */

    public static function order_email($maillto, $title, $content)
    {
        require_once TOOLS_COMMON . 'email/emailservice.php';
        $status = EmailService::send_email($maillto, $title, $content);
        return $status;
    }

    /**
     * @param $memberid
     * @param $content
     * @param $jifen
     * @param $type
     * 添加积分日志
     */

    public static function add_jifen_log($memberid, $content, $jifen, $type)
    {
        $model = ORM::factory('member_jifen_log');
        $model->memberid = $memberid;
        $model->content = $content;
        $model->jifen = $jifen;
        $model->type = $type;
        $model->addtime = time();
        $model->save();
    }

    //在线支付公共接口
    /*-
	   $ordersn:订单编号
	   $subject:商品名称
	   $price:总价
	   $showurl:商品url
	-*/

    public static function pay_online($ordersn, $subject, $price, $paytype, $showurl = '', $extra_para = '', $widbody = '')
    {


        if ($paytype == 1) //支付宝
        {
            $showurl = empty($showurl) ? $GLOBALS['cfg_cmspath'] : $showurl;
            $payurl = $GLOBALS['cfg_phone_cmspath'] . '/thirdpay/alipay';

            $html = "<form method='post' action='{$payurl}' name='alipayfrm'>";
            $html .= '<input type="hidden" name="ordersn" value="' . $ordersn . '">';
            $html .= '<input type="hidden" name="subject" value="' . $subject . '">';
            $html .= '<input type="hidden" name="price" value="' . $price . '">';
            $html .= '<input type="hidden" name="widbody" value="' . $widbody . '">';
            $html .= '<input type="hidden" name="showurl" value="' . $showurl . '">';
            $html .= '<input type="hidden" name="extra_common_param" value="' . $extra_para . '">';

            $html .= '</form>';
            $html .= "<script>document.forms['alipayfrm'].submit();</script>";
            return $html;


        } else if ($paytype == 2)  //快钱支付
        {
            $payurl = $GLOBALS['cfg_phone_cmspath'] . '/thirdpay/bill';

            $html = "<form method='post' action='{$payurl}' name='billfrm'>";
            $html .= '<input type="hidden" name="ordersn" value="' . $ordersn . '">';
            $html .= '<input type="hidden" name="subject" value="' . $subject . '">';
            $html .= '<input type="hidden" name="price" value="' . $price . '">';
            $html .= '<input type="hidden" name="showurl" value="' . $showurl . '">';
            $html .= '</form>';
            $html .= "<script>document.forms['billfrm'].submit();</script>";
            return $html;
        } else if ($paytype == 3) //微信支付
        {
            $payurl = $GLOBALS['cfg_phone_cmspath'] . '/thirdpay/weixinpay';
            $html = "<form method='post' action='{$payurl}' name='alipayfrm'>";
            $html .= '<input type="hidden" name="ordersn" value="' . $ordersn . '">';
            $html .= '<input type="hidden" name="subject" value="' . $subject . '">';
            $html .= '<input type="hidden" name="price" value="' . $price . '">';
            $html .= '<input type="hidden" name="widbody" value="' . $widbody . '">';
            $html .= '<input type="hidden" name="showurl" value="' . $showurl . '">';
            $html .= '<input type="hidden" name="extra_common_param" value="' . $extra_para . '">';

            $html .= '</form>';
            $html .= "<script>document.forms['alipayfrm'].submit();</script>";
            return $html;
        }
    }

    /**
     * 列表搜索页格式化
     * @param $data
     * @return mixed|string
     */
    public static function list_search_format($data, $page, $pagesize = 10)
    {
        $result['list'] = $data;
        $result['page'] = count($data) < $pagesize ? -1 : $page + 1;
        return json_encode($result);
    }


    /*
 * 获取编号
     * 线路  prefix 01
 * */
    //获取编号,共6位,不足6位前面被0
    public static function getSeries($id, $prefix)
    {
        $ar = array(
            '01' => 'A',
            '02' => 'B',
            '05' => 'C',
            '03' => 'D',
            '08' => 'E',
            '13' => 'G',
            '14' => 'H',
            '15' => 'I',
            '16' => 'J',
            '17' => 'K',
            '18' => 'L',
            '19' => 'M',
            '20' => 'N',
            '21' => 'O',
            '22' => 'P',
            '23' => 'Q',
            '24' => 'R',
            '25' => 'S',
            '26' => 'T'
        );
        $prefix = $ar[$prefix];
        $len = strlen($id);
        $needlen = 4 - $len;
        if ($needlen == 3)
            $s = '000';
        else if ($needlen == 2)
            $s = '00';
        else if ($needlen == 1)
            $s = '0';
        $out = $prefix . $s . "{$id}";
        return $out;
    }
}
