<?php

namespace backend\controllers;

use backend\filter\RbacFilter;
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
                $auth = \Yii::$app->authManager;
                $rolesNames = $model->roles;
//                var_dump($rolesNames);exit;
                //beforeSave方法在保存之前自动调用
                $model->save();
                $userId = $model->id;
                //如果有选角色
                if($rolesNames){
                    //添加角色
                    foreach ($rolesNames as $name){
                        $role = $auth->getRole($name);
//                var_dump($userId);exit;
                        $auth->assign($role,$userId);
                }
                }


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
        $auth = \Yii::$app->authManager;
        //找到该用户所有的已有角色
        $roles = $auth->getRolesByUser($id);
//        var_dump(array_keys($roles));exit;
        //交给模型回显
        $model->roles = array_keys($roles);
        //实例化请求模型
        $request = new Request();
        if($request->isPost){
            //接收数据
            $model->load($request->post());
            //验证数据
            if($model->validate()){
                //beforeSave方法在保存之前自动调用
                $model->save();
                $userId = $model->id;
                //移除用户已有的角色 如果角色存在
//                var_dump($roles);exit;
                if($roles){
                    foreach ($roles as $k=>$v){
                        $role1 = $auth->getRole($k);
                        $auth->revoke($role1,$userId);
                    }
                }
//                var_dump($roles);exit;
                //赋予角色名
//                var_dump($model->roles);exit;
                $rolesName = $model->roles;
//                var_dump($rolesName);exit;
                if($rolesName){
                    //添加角色
                    foreach ($rolesName as $name){
                        $role = $auth->getRole($name);
                        $auth->assign($role,$userId);
                    }
                }
                \Yii::$app->session->setFlash('seccess','添加成功');
                return $this->redirect(['admin/index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //删除员工
    public function actionDelete(){

        $id = \Yii::$app->request->post('id');
        $model = Admin::findOne(['id'=>$id]);
        $auth = \Yii::$app->authManager;
        $auth->revokeAll($id);
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
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'except'=>['login','logout','captcha','error'],
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
