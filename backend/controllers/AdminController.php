<?php

namespace backend\controllers;

use backend\models\Admin;
use backend\models\AdminEdit;
use backend\models\LoginForm;
use Codeception\Module\Yii1;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Request;

class AdminController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model = Admin::find();
        $pager = new Pagination([
            'totalCount'=>$model->count(),
            'defaultPageSize'=>4
        ]);
        $admins = $model->limit($pager->limit)->offset($pager->offset)->orderBy('id DESC')->all();
        return $this->render('index',['admins'=>$admins,'pager'=>$pager]);
    }
    //添加员工
    public function actionAdd(){
        //实例化表单模型
        $model = new Admin();
        $model->scenario = Admin::SCENARIO_ADD;
        //实例化请求模型
        $request = new Request();
        if($request->isPost){
            //接收数据
            $model->load($request->post());
            //验证数据
            if($model->validate()){
                //beforeSave方法在保存之前自动调用
                $model->save();
                \Yii::$app->session->setFlash('seccess','添加成功');
                return $this->redirect(['admin/index']);
            }
        }
        //展示表单页面
        return $this->render('add',['model'=>$model]);
    }
    //修改员工信息
    public function actionEdit($id){
        //实例化表单模型
        $model = Admin::findOne(['id'=>$id]);
        //实例化请求模型
        $request = new Request();
        if($request->isPost){
            //接收数据
            $model->load($request->post());
            //验证数据
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('seccess','修改成功');
                return $this->redirect(['admin/index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //删除员工
    public function actionDelete(){

        $id = \Yii::$app->request->post('id');
        $model = Admin::findOne(['id'=>$id]);

        if($model->delete()){
            return 'true';
        }else{
            return 'false';
        }
    }
    //登录表单
    public function actionLogin(){
        $model = new LoginForm();
        //实例化一个请求类
        $request = new Request();
        //提交表单
        if($request->isPost) {
            //接收数据
            $model->load($request->post());

            if ($model->validate()) {
                //登录认证
                $denglu = $model->login();
                if ($denglu) {
                    \Yii::$app->session->setFlash('success', '登录成功');
                    //跳转
                    return $this->redirect(['admin/index']);
                }
            }
        }
        return $this->render('login',['model'=>$model]);
    }
    //注销
    public function actionLogout(){
        $user = \Yii::$app->user;
        $user->logout();
        //跳转
        return $this->redirect(['admin/login']);
    }
    //验证码
    public function actions(){
        return [
            'captcha'=>[
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                //设置验证码参数
                'minLength'=>3,
                'maxLength'=>3,
                'width'=>200
            ]
        ];
    }

    //验证是否游客
   /* public function isGuest(){
        if(\Yii::$app->user->isGuest){
            \Yii::$app->session->setFlash('faild','请先登录');
            return $this->redirect(['admin/login']);
        }
    }*/
    //过滤
    public function behaviors(){
        return [
            'acf'=>[
                'class'=>AccessControl::className(),
                'except'=>['login'],
                'rules'=>[
                    [
                    'allow'=>true,//允许
                    'actions'=>['login','index','captcha'],//操作
                    'roles'=>['?']//未登录  @已登录
                    ],
                    [
                        'allow'=>true,//允许
                        'actions'=>[],//操作
                        'roles'=>['@']//已登录
                    ]
                ],
            ]
        ];
    }

    //修改密码
    public function actionEditPwd(){
        $id = \Yii::$app->user->getId();
        $model = Admin::findOne($id);
        $model->scenario = Admin::SCENARIO_EDIT;
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->updated_at = time();
                $model->save();
                \Yii::$app->session->setFlash('success', '修改密码成功');
                //跳转
                return $this->redirect(['admin/index']);
            }
        }
//        var_dump($id);exit;
        return $this->render('edit-pwd',['model'=>$model]);
    }

}
