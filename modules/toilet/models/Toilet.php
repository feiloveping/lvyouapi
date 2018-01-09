<?php

namespace app\modules\toilet\models;

use Yii;

/**
 * This is the model class for table "{{%toilet}}".
 *
 * @property integer $id
 * @property integer $webid
 * @property integer $aid
 * @property string $title
 * @property string $seotitle
 * @property string $content
 * @property string $address
 * @property integer $shownum
 * @property string $addtime
 * @property string $modtime
 * @property string $keyword
 * @property string $description
 * @property string $tagword
 * @property string $litpic
 * @property integer $ishidden
 * @property string $notice
 * @property string $piclist
 * @property string $opentime
 * @property string $closetime
 * @property integer $satisfyscore
 * @property integer $usecount
 * @property string $lng
 * @property string $lat
 * @property integer $finaldestid
 * @property string $threetype
 * @property integer $issmarty
 */
class Toilet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%toilet}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['webid', 'threetype', 'issmarty'], 'required'],
            [['webid', 'aid', 'shownum', 'ishidden', 'satisfyscore', 'usecount', 'finaldestid', 'issmarty'], 'integer'],
            [['content'], 'string'],
            [['addtime', 'modtime', 'opentime', 'closetime'], 'safe'],
            [['title', 'seotitle', 'address', 'keyword', 'description', 'tagword', 'litpic', 'notice', 'piclist', 'lng', 'lat', 'threetype'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'webid' => 'Webid',
            'aid' => 'Aid',
            'title' => 'Title',
            'seotitle' => 'Seotitle',
            'content' => 'Content',
            'address' => 'Address',
            'shownum' => 'Shownum',
            'addtime' => 'Addtime',
            'modtime' => 'Modtime',
            'keyword' => 'Keyword',
            'description' => 'Description',
            'tagword' => 'Tagword',
            'litpic' => 'Litpic',
            'ishidden' => 'Ishidden',
            'notice' => 'Notice',
            'piclist' => 'Piclist',
            'opentime' => 'Opentime',
            'closetime' => 'Closetime',
            'satisfyscore' => 'Satisfyscore',
            'usecount' => 'Usecount',
            'lng' => 'Lng',
            'lat' => 'Lat',
            'finaldestid' => 'Finaldestid',
            'threetype' => 'Threetype',
            'issmarty' => 'Issmarty',
        ];
    }

    /**
     * @inheritdoc
     * @return ToiletQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ToiletQuery(get_called_class());
    }
}
