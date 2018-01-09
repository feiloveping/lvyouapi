<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/7
 * Time: 11:03
 */

namespace app\modules\v1\controllers;



use app\modules\components\helpers\FeiIdCard;
use app\modules\components\helpers\FeiValidate;
use app\modules\v1\models\MemberLinkman;
use yii\web\Response;

class MemberLinkmanController extends DefaultController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats'] = '';
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;
        return $behaviors;
    }

    public function init()
    {
        parent::init();

        if(! $this->logsign)
        {
            echo json_encode( ['code'=>401 , 'msg'=>'用户未登录' ,'data'=>''] );
            exit;
        }
    }

    public function actionLinkmanLister()
    {
        $id = $this->mid;
        // 根据id来获取地址列表
        $linkman = MemberLinkman::getMemberLinkmanById($id);
        if(empty($linkman))
            return ['code'=>404,'data'=>[],'msg'=>'未找到数据'];
        else
            return ['code'=>200,'data'=>$linkman,'msg'=>'ok'];
    }
    // 联系人添加
    /*
     * @linkman     联系人
     * @cardtype    证件类型
     * @idcard      证件号码
     * @sex         性别
     * @mobile       联系人手机号(可为空)
     * */
    public function actionAddLinkman()
    {

        $request = \Yii::$app->request;
        $data = $request->post();
        if (empty($data['linkman']) || empty($data['cardtype']) || empty($data['idcard'])
            || empty($data['sex']) )
            return ['code'=>4001,'data'=>'','msg'=>'参数不能为空'];

        if($data['sex'] == 1)
            $data['sex'] = '男';
        elseif($data['sex'] == 2)
            $data['sex'] = '女';
        else
            $data['sex'] = '保密';

        $card = \Yii::$app->params['linkmanCardType'];

        $cardname = $card[$data['cardtype' ] - 1]['cardtype'];
        $memberid = $this->mid;
        $data['memberid'] = $memberid ;
        switch ($data['cardtype'])
        {
            case 1 :
                if(! FeiIdCard::validateIDCard($data['idcard'])) return ['code'=>4001,'data'=>[],'msg'=>'身份证格式错误'];
                break;
            case 2:
                //验证护照
                break;
            case 3:
                // 台胞证
                break;
            case 4:
                // 港澳通行证
                break;
            case 5:
                // 军官证
                break;
        }
        $data['cardtype'] = $cardname ;
        // 对手机号进行验证
        if( !empty($data['mobile'])) if(!FeiValidate::isMobile($data['mobile']))  return ['code'=>4006,'data'=>'','msg'=>'手机号格式错误'];
        // 根据用户统计用户的联系人地址

        $count = MemberLinkman::getLinkmanCountByMemberId($memberid);
        if($count > 20 ) return ['code'=>4007,'data'=>'','msg'=>'用户联系人超过20条,请删除后再新增'];
        $re = \Yii::$app->db->createCommand()->insert('sline_member_linkman',$data)->execute();
        if($re === false)
            return ['code'=>403,'data'=>'','msg'=>'新增失败'];
        else
            return ['code'=>200,'data'=>'','msg'=>'新增成功'];

    }

    /*
     * @id          联系人id
     * @linkman     联系人
     * @cardtype    证件类型
     * @idcard      证件号码
     * @sex         性别
     * @mobile       联系人手机号(可为空)
     * */
    // 联系人编辑
    public function actionEditLinkman()
    {
        // 请求的参数  id,name,cardtype,cardnum,cardtype,mobile,sex
        $request = \Yii::$app->request;
        $data = $request->post();
        if (empty($data['id']) || empty($data['linkman']) || empty($data['cardtype']) || empty($data['idcard']) || empty($data['sex'])   )
            return ['code'=>4001,'data'=>'','msg'=>'参数不能为空'];

        if($data['sex'] == 1)
            $data['sex'] = '男';
        else
            $data['sex'] = '女';

        $card = \Yii::$app->params['linkmanCardType'];

        $cardname = $card[$data['cardtype' ] - 1]['cardtype'];
        $memberid = $this->mid;
        $data['memberid'] = $memberid ;
        switch ($data['cardtype'])
        {
            case 1 :
                if(! FeiIdCard::validateIDCard($data['idcard'])) return ['code'=>4001,'data'=>'','msg'=>'身份证格式错误'];
                break;
            case 2:
                //验证护照
                break;
            case 3:
                // 台胞证
                break;
            case 4:
                // 港澳通行证
                break;
            case 5:
                // 军官证
                break;
        }
        $data['cardtype'] = $cardname ;
        // 对手机号进行验证
        if( !empty($data['mobile'])) if(!FeiValidate::isMobile($data['mobile']))  return ['code'=>4006,'data'=>'','msg'=>'手机号格式错误'];
        //$re = MemberLinkman::createCommand()->update('user', ['status' => 1], 'age > 30')->execute();
        $re = \Yii::$app->db->createCommand()->update('sline_member_linkman',$data,['id'=>$data['id']])->execute();
        if($re === false)
            return ['code'=>404,'data'=>'','msg'=>'修改失败'];
        else
            return ['code'=>200,'data'=>'','msg'=>'修改成功'];
    }

    public function actionDelLinkman()
    {
        $request = \Yii::$app->request;
        $id = $request->post('id');
        if(! $id) return ['code'=>400,'data'=>'','msg'=>'参数不能为空'];
        $re = \Yii::$app->db->createCommand()->delete('sline_member_linkman',['id'=>$id])->execute();
        if($re != 1)
            return ['code'=>401,'data'=>'','msg'=>'删除失败,无此数据'];
        else
            return ['code'=>200,'data'=>'','msg'=>'删除成功'];
    }

    public function actionCardType()
    {
        return [
            'code'=>200,
            'data'=>\Yii::$app->params['linkmanCardType'],
            'msg'=>'ok'
        ];
    }

}