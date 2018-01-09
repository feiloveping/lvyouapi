<?php

namespace app\modules\toilet\models;

/**
 * This is the ActiveQuery class for [[Toilet]].
 *
 * @see Toilet
 */
class ToiletQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Toilet[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Toilet|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
