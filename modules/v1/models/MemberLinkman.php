<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/7
 * Time: 14:01
 */

namespace app\modules\v1\models;


use yii\db\ActiveRecord;

class MemberLinkman extends ActiveRecord
{

    public function getMemberLinkmanById($id)
    {
        return MemberLinkman::find()
            ->select('id,linkman,mobile,idcard,cardtype')
            ->where('memberid='.$id)
            ->asArray()->all();
    }

    // 根据用户id获得联系地址总数
    public function getLinkmanCountByMemberId($id)
    {
        return MemberLinkman::find()->where(['memberid'=>$id])->count();
    }

    // 根据联系地址id获取所有满足条件的记录
    public function getLinkmanByIds(array $ids)
    {
        return MemberLinkman::find()->select('linkman,mobile,idcard,cardtype,sex')
            ->where(['id'=>$ids])       // 自动转化为 in_array
            ->asArray()->all();
    }

}