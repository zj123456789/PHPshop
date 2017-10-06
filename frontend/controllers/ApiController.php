<?php
namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\LoginForm;
use frontend\models\Member;
use yii\web\Controller;
use yii\web\Response;

class ApiController extends Controller{
    public $enableCsrfValidation=false;
    public function init(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        parent::init();
    }
    public function actionP(){
        var_dump(\Yii::$app->security->generatePasswordHash('123'));
    }
    //收货地址
    //地址列表
    public function actionList(){
        $result = [
            'error'=>true,
            'msg'=>'',
            'data'=>[]
        ];
        if(!\Yii::$app->user->isGuest){
            $models = Address::find()->where(['user_id'=>\Yii::$app->user->identity->getId()])->all();
            if(!$models==false){
                $result['error']=false;
                $result['data'][]=$models;
            }else{
                $result['error']=false;
                $result['msg']='该用户没添加地址';
            }
        }else{
            $result['msg']='请登录';
        }
       return $result;
    }
    //修改地址 传地址id 地址 传收货人 地址 收货人电话
    public function actionEdit(){
        $result = [
            'error'=>true,
            'msg'=>'',
            'data'=>[]
        ];
        if(!\Yii::$app->user->isGuest){
            if(\Yii::$app->request->isPost){
                $id=\Yii::$app->request->post('id');
                $model = Address::findOne(['id'=>$id]);
                $model->load(\Yii::$app->request->post(),'');
                if($model->validate()){
                    $model->user_id = \Yii::$app->user->id;
                    $model->province = $model->cmbProvince;
                    $model->city = $model->cmbCity;
                    $model->area = $model->cmbArea;
                    $model->save();
                    $result['error']=false;
                    $result['msg']='修改成功';
                    $result['data'][]=[
                        'username'=>$model->username,
                        'tel'=>$model->tel,
                        'province'=>$model->province,
                        'city'=>$model->city,
                        'area'=>$model->area,
                        'address'=>$model->address,
                        'user_id'=>$model->user_id
                    ];

                }else{
                    $result['msg']=$model->getErrors();
                }
            }else{
                $result['msg']='提交方式错误';
            }

        }
        return $result;
    }
    //删除地址 传地址id
    public function actionDelete(){
        $result = [
            'error'=>true,
            'msg'=>'',
            'data'=>[]
        ];
        if(!\Yii::$app->user->isGuest){
            $id = \Yii::$app->request->post('id');
            $model = Address::findOne(['id'=>$id]);
            if($model->delete()){
                $result['error']=false;
                $result['msg']='删除成功';
            }else{
                $result['msg']='删除失败';
            }
        }else{
            $result['msg']='请登录';
        }
        return $result;

    }
    //添加地址 传收货人 地址 收货人电话
    public function actionAdd(){
        $result = [
            'error'=>true,
            'msg'=>'',
            'data'=>[]
        ];
        if(!\Yii::$app->user->isGuest){
           $id=\Yii::$app->user->id;
           $model = new Address();
           if(\Yii::$app->request->isPost){
               $model->load(\Yii::$app->request->post(),'');
               if($model->validate()){
                   $model->user_id = $id;
                   $model->province = $model->cmbProvince;
                   $model->city = $model->cmbCity;
                   $model->area = $model->cmbArea;
                   $model->save();
                   $result['error']=false;
                   $result['msg']='添加成功';
                   $result['data'][]=[
                       'username'=>$model->username,
                       'tel'=>$model->tel,
                       'province'=>$model->province,
                       'city'=>$model->city,
                       'area'=>$model->area,
                       'address'=>$model->address,
                       'user_id'=>$model->user_id
                   ];

               }else{
                   $result['msg']=$model->getErrors();
               }
           }else{
               $result['msg']='提交方式错误';
           }

        }
        return $result;


    }
    //用户
    //获取当前登录用户的信息 用户id
    public function actionInfo(){
        $result = [
            'error'=>true,
            'msg'=>'',
            'data'=>[]
        ];
        $info='';
        if(!\Yii::$app->user->isGuest){
            $info =  \Yii::$app->user->identity;
        }else{
            $result['error']=true;
            $result['msg']='未登录';
        }

       return $info;
    }
    //修改密码 传用户Id 旧密码 新密码
    public function actionUpdate(){
        $result = [
            'error'=>true,
            'msg'=>'',
            'data'=>[]
        ];
        if(\Yii::$app->request->isPost){
            //用户id
            $id = \Yii::$app->request->post('id');
            $model = Member::findOne(['id'=>$id]);
            if($model==null){
                $result['msg']='用户不存在';
            }
            //旧密码
            $password = \Yii::$app->request->post('password');
            //新密码
            $newpassword = \Yii::$app->request->post('newpassword');
                //数据库密码
                $password_hash = $model->password_hash;
                //输入旧密码
                $oldpassword = \Yii::$app->security->generatePasswordHash($password);
                if(\Yii::$app->security->validatePassword($oldpassword,$password_hash)){
                    $result['msg']='旧密码错误';
                }else{
                    $model->password_hash = \Yii::$app->security->generatePasswordHash($newpassword);
                    $model->updated_at = time();
                    $model->save(false);
                    $result['error']=false;
                    $result['msg']='密码修改成功';
                }
        }else{
            $result['msg']='提交方式不正确';
        }

        return $result;
    }
    //用户登录  传用户名 密码
    public function actionLogin(){
        $result = [
            'error'=>true,
            'msg'=>'',
            'data'=>[]
        ];
        $model = new LoginForm();
        //提交表单
        if(\Yii::$app->request->isPost) {
            //接收数据
            $model->load(\Yii::$app->request->post(),'');
            if ($model->validate()) {
                //登录认证
                $denglu = $model->login();
                if ($denglu) {
                    $result['error']=false;
                    $result['msg']='登录成功';
                }
            }else{
                $result['msg']=$model->getErrors();
            }
        }else{
            $result['msg']='提交方式不正确';
        }
        return $result;
    }
    //用户注册  需要传用户名 密码 邮箱 手机号
    public function actionRegist(){
        $result = [
            'error'=>true,
            'msg'=>'',
            'data'=>[]
        ];
        if(\Yii::$app->request->isPost){
            $model = new Member();
            $model->load(\Yii::$app->request->post(),'');
            if($model->validate()){
                $model->status = 1;
                $model->auth_key = \Yii::$app->security->generateRandomString();
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password);
                $model->created_at = time();
                $result['error']=false;
                $result['msg']='注册成功';
                $model->save(false);
                $result['data']['member_id']=$model->id;
            }else{
                $result['msg']=$model->getErrors();
            }
        }else{
            $result['msg']='提交方式不正确';
        }
        return $result;
    }
}
