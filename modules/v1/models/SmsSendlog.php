<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/9
 * Time: 14:18
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class SmsSendlog extends ActiveRecord
{
    // 统计短信发送数
    public function MesCountLimit(array $where)
    {
        if(count($where) > 1)
        {
            $whereor[] = 'or';
            foreach ($where as $k=>$v)
            {
                $whereor[] = $k . '=' .$v ;
            }
        }
        return SmsSendlog::find()
            ->where( $whereor)
            ->asArray()
            ->count();
        // 根据where条件来做统计    同一 ip 或 mobile 算一个用户  ,一天内短信不能发送超过20条
    }

}