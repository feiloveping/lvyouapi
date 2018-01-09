<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/9
 * Time: 17:35
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class Member extends ActiveRecord
{
    public function hasMember($where)
    {
        return Member::find()->where($where)->count();
    }

    public function getMember($where)
    {
        return Member::find()->select('mid,nickname,mobile')->where($where)->asArray()->one();
    }

    // 根据mid获得用户的详细信息
    public function getMemberMessage($mid)
    {
        return Member::find()->select('nickname,sex,rank,email,mobile,litpic,native_place,province,
                                                city,cardid,address,postcode,qq,constellation,birth_date,idcard_pic,wechat')
            ->where(['mid'=>$mid])->asArray()->one();
    }

}