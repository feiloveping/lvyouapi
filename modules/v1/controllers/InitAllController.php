<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/3
 * Time: 15:54
 */

namespace app\modules\v1\controllers;


use app\modules\v1\models\Notes;

class InitAllController extends DefaultController
{
    // 格式化酒店的服务的图片问题
    public function actionChangeHotelUrl()
    {

        // 获得所有的酒店信息
        $hotel = Hotel::find()->select('id,fuwu')->asArray()->all();
        foreach ($hotel as $k=>$v)
        {
            $fuwu = str_replace('http://lvyou.wmqt.net/','http://sq.wmqt.net/',$v['fuwu']);
            $hotelModel = Hotel::find()->where(['id'=>$v['id']])->one(); //获取name等于test的模型
            $hotelModel->fuwu = $fuwu; //修改age属性值
            $hotelModel->save();   //保存
        }

        // 批量修改
        var_dump($hotel);
    }

    // 格式化酒店的服务的图片问题
    public function actionChangeNotesUrl()
    {

        // 获得所有的酒店信息
        $notes = Notes::find()->select('id,content')->asArray()->all();
        foreach ($notes as $k=>$v)
        {
            $content = str_replace('http://lvyou.wmqt.net/','http://sq.wmqt.net/',$v['content']);
            $notesModel = Notes::find()->where(['id'=>$v['id']])->one(); //获取name等于test的模型
            $notesModel->content = $content; //修改age属性值
            $notesModel->save();   //保存
        }

        // 批量修改
        //var_dump($notes);
    }



}