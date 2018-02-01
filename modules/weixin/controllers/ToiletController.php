<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2018/1/24
 * Time: 16:39
 */

namespace app\modules\weixin\controllers;


use app\modules\components\helpers\UploadFile;
use yii\web\Controller;
use yii\web\UploadedFile;

class ToiletController extends Controller
{
    public $layout = false;
    public $enableCsrfValidation=false;
    public function actionIndex()
    {
        return $this->render('index');
    }
    public function actionFuwu()
    {
        return $this->render('fuwu');
    }

    public function actionSoket()
    {
         header('content-type:text/html;charset=utf-8');
        //创建一个socket套接流
        $socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
        /****************设置socket连接选项，这两个步骤你可以省略*************/
        //接收套接流的最大超时时间1秒，后面是微秒单位超时时间，设置为零，表示不管它
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 1, "usec" => 0));
        //发送套接流的最大超时时间为6秒
        socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array("sec" => 6, "usec" => 0));
        /****************设置socket连接选项，这两个步骤你可以省略*************/

        //连接服务端的套接流，这一步就是使客户端与服务器端的套接流建立联系
        if(socket_connect($socket,'127.0.0.1',8888) == false){
            echo 'connect fail massege:'.socket_strerror(socket_last_error());
        }else{
            $message = 'l love you 我爱你 socket';
            //转为GBK编码，处理乱码问题，这要看你的编码情况而定，每个人的编码都不同
            $message = mb_convert_encoding($message,'GBK','UTF-8');
            //向服务端写入字符串信息

            if(socket_write($socket,$message,strlen($message)) == false){
                echo 'fail to write'.socket_strerror(socket_last_error());

            }else{
                echo 'client write success'.PHP_EOL;
                //读取服务端返回来的套接流信息
                while($callback = socket_read($socket,1024)){
                    echo 'server return message is:'.PHP_EOL.$callback;
                }
            }
        }
        socket_close($socket);//工作完毕，关闭套接流
    }

    public function actionUpload(){
        $request = \Yii::$app->request;
        if($request->isPost){

            $up = new UploadFile();
            //设置属性(上传的位置， 大小， 类型， 名是是否要随机生成)
            $up -> set("path", "./img/");
            $up -> set("maxsize", 2000000);
            $up -> set("allowtype", array("gif", "png", "jpg","jpeg"));
            $up -> set("israndname", false);

            //使用对象中的upload方法， 就可以上传文件， 方法需要传一个上传表单的名子 pic, 如果成功返回true, 失败返回false
            if($up -> upload("pic")) {
                echo '<pre>';
                //获取上传后文件名子
                var_dump($up->getFileName());
                echo $up->getErrorMsg();
                exit();
            }else{
                echo $up->getErrorMsg();
                exit();
            }
        }
        return $this->render('upload');
    }


}