<?php

namespace frontend\controllers;


use Behat\Gherkin\Loader\YamlFileLoader;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Locations;
use frontend\models\LoginForm;

use frontend\models\Member;
use frontend\models\SmsDemo;
use yii\base\Model;

class MemberController extends \yii\web\Controller
{
    //redis测试
    public function actionRedis(){
        //点击按钮时将电话号码传过来保存在redis里面
        //调用sms方法发送短信,同时将验证码保存在redis里面
        //表单提交时得到短信验证码与redis中的对比
        //对比成功保存数据库
        //对比失败提示验证码错误
        $reids = new \Redis();
        $reids->connect('127.0.0.1');
        $reids->set('name','zhangsan');
        echo $reids->get('name');
//echo phpinfo();
    }
    //前台短信验证
    public function actionValidateSms($tel,$sms){
        $redis= new \Redis();
        $redis->connect('127.0.0.1');
        $code = $redis->get('code'.$tel);
        if($code==null || $code!=$sms){
            return "false";
        }
        return "true";

    }
    //后台短信验证
    public function actionSms(){
        $tel = \Yii::$app->request->post('tel');
        $code = rand(1000,9999);

        $demo = new SmsDemo(
            "LTAIkyZwVVq1knti",//AK
            "zz4anOAEKhMhSLQpBqmVZI283RcPHv" //SK
        );
//SmsDemo::sendSms stdClass Object ( [Message] => OK [RequestId] => 1E730CC7-FFE7-45BC-AAFE-AD6BD9BE80B9 [BizId] => 296904805887351612^0 [Code] => OK )
        echo "SmsDemo::sendSms\n";
        $response = $demo->sendSms(
            "朱钧的茶馆", // 短信签名
            "SMS_97980003", // 短信模板编号
            $tel, // 短信接收者

           $code= Array(  // 短信模板中字段的值
                "code"=>rand(10000,99999),
            )
        );
//        print_r($response);
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $redis->set('code'.$tel,implode($code));
    }
    //短信测试
    public function actionTest(){
        $code = rand(1000,9999);
        $demo = new SmsDemo(
            "LTAIkyZwVVq1knti",//AK
            "zz4anOAEKhMhSLQpBqmVZI283RcPHv" //SK
        );
//SmsDemo::sendSms stdClass Object ( [Message] => OK [RequestId] => 1E730CC7-FFE7-45BC-AAFE-AD6BD9BE80B9 [BizId] => 296904805887351612^0 [Code] => OK )
        echo "SmsDemo::sendSms\n";
        $response = $demo->sendSms(
            "朱钧的茶馆", // 短信签名
            "SMS_97980003", // 短信模板编号
            "18482127307", // 短信接收者

            Array(  // 短信模板中字段的值
                "code"=>$code,
            )
        );
            print_r($response);
    }
    //修改地址
    public function actionEdit($id){
        //查询地址
        $model = Address::findOne(['id'=>$id]);
        //地址列表
        $models = Address::find()->where(['user_id'=>\Yii::$app->user->identity->getId()])->all();
        $request =\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post(),'');
            if($model->validate()){
                $model->province = $model->cmbProvince;
                $model->city = $model->cmbCity;
                $model->area = $model->cmbArea;
                $model->save();
                \Yii::$app->session->setFlash('success','修改成功');
//                var_dump($model);exit;

                return $this->redirect(['address']);
            }
        }

        return $this->renderPartial('address_edit',['model'=>$model,'models'=>$models]);
    }
    //删除地址
    public function actionDelete(){
        $id = \Yii::$app->request->post('id');
        $model = Address::findOne(['id'=>$id]);
        if($model->delete()){
            return "true";
        }else{
            return "false";
        }
    }
    //添加地址
    public function actionAddress(){
        if(\Yii::$app->user->isGuest){
            return $this->redirect(['login']);
        }
        $model = new Address();
        //地址列表
        $models = Address::find()->where(['user_id'=>\Yii::$app->user->identity->getId()])->all();
        $request =\Yii::$app->request;
        if($request->isPost){
            $model->load($request->post(),'');
            if($model->validate()){
                $model->province = $model->cmbProvince;
                $model->city = $model->cmbCity;
                $model->area = $model->cmbArea;
                $model->user_id = \Yii::$app->user->identity->getId();
//                var_dump($model->tel);exit;
                $model->save();
//                var_dump($model->tel);exit;
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['address']);
            }
        }

        return $this->renderPartial('address',['models'=>$models]);
    }
    //登录
    public function actionLogin(){
        $model = new LoginForm();
        $request = \Yii::$app->request;
        //提交表单
        if($request->isPost) {
            //接收数据
            $model->load($request->post(),'');
            if ($model->validate()) {
                //登录认证
                $denglu = $model->login();
                if ($denglu) {
                    //同步cookie数据到数据库
                    $this->Tongbu();
                    \Yii::$app->session->setFlash('success', '登录成功');
                    //跳转
                    return $this->redirect(['goods-category/index']);
                }
            }
        }

        return $this->renderPartial('login');
    }
    //同步操作
    public function Tongbu(){
        //   1.将cookie中数据取出来
        $cookies = \Yii::$app->request->cookies;
        $value = $cookies->getValue('cart');
        if($value){
            $carts = unserialize($value);
            //2.遍历成键值对的数组格式[goods_id=>amount]
            foreach ($carts as $goods_id=>$amount){
                //3.根据商品id和用户id去查询数据库该商品是否存在
                $member_id = \Yii::$app->user->getId();
                $goods = Cart::findOne(['goods_id'=>$goods_id,'member_id'=>$member_id]);
                if($goods){
                    //4.如果存在就更新数量
                    $goods->amount += $amount;
                }else{
                    //5.不存在就新增一条数据
                    $goods = new Cart();
                    $goods->goods_id = $goods_id;
                    $goods->amount = $amount;
                    $goods->member_id = $member_id;
                    $goods->save();
                }
            }
        }
        //6.清除cookie
        \Yii::$app->response->cookies->remove('cart');
    }
    //注销
    public function actionLogout(){
        $user = \Yii::$app->user;
        $user->logout();
        //跳转
        return $this->redirect(['login']);
    }
    //注册
    public function actionRegist(){
        $model = new Member();
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post(),'');
            if($model->validate()){
                $model->created_at = time();
                $model->status = 1;
                $model->auth_key = \Yii::$app->security->generateRandomString();
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password);
                $model->save(false);
                \Yii::$app->session->setFlash('success', '注册成功');
                return $this->redirect(['member/login']);
            }
        }
        return $this->renderPartial('regist');
    }
    //注册验证用户名的唯一性
    public function actionValidateMember($username){
        $model = Member::findOne(['username'=>$username]);
        if($model){
            return "false";
        }else{
            return "true";
        }
    }
    //注册验证邮箱的唯一性
    public function actionValidateEmail($email){
        $model = Member::findOne(['email'=>$email]);
        if($model){
            return "false";
        }else{
            return "true";
        }
    }
    //商城首页
    public function actionIndex()
    {
        return $this->redirect(['goods-category/index']);
    }

}
