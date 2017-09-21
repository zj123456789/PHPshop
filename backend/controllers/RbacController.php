<?php

namespace backend\controllers;

use backend\filter\RbacFilter;
use backend\models\Permission;
use backend\models\PermissionForm;


class RbacController extends \yii\web\Controller
{
    //添加权限
    public function actionAdd(){
        //提交表单
        $permission = new PermissionForm();
        $permission->scenario = PermissionForm::SCENARIO_ADD;
        $request = \Yii::$app->request;
        if($request->isPost){
            $permission->load($request->post());
            if($permission->validate()){
                $auth = \Yii::$app->authManager;
                //创建权限  权限名使用路由 ,方便以后判断
                $perm = $auth->createPermission($permission->name);
                //添加权限描述
                $perm->description = $permission->description;
                //添加权限
                $auth->add($perm);
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['index']);
            }
        }
        return $this->render('add',['permission'=>$permission]);

    }
    //权限列表
    public function actionIndex()
    {
        $auth = \Yii::$app->authManager;
        $permissions = $auth->getPermissions();

        return $this->render('index',['permissions'=>$permissions]);
    }
    //修改权限
    public function actionEdit($name){
        $auth = \Yii::$app->authManager;
        //根据Name获取当前权限
        $permission = $auth->getPermission($name);
//        var_dump($permission);exit;
        //实例化表单模型回显
        $model = new PermissionForm();
        //老名字
        $oldname = $permission->name;
        $model->name = $permission->name;
        $model->description = $permission->description;
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            //如果修改角色,就调用验证规则
            if($oldname != $model->name){
                $model->scenario = PermissionForm::SCENARIO_ADD;
            }
            if($model->validate()){
                $auth = \Yii::$app->authManager;
                //创建权限  权限名使用路由 ,方便以后判断
                $perm = $auth->createPermission($model->name);
                //添加权限描述
                $perm->description = $model->description;
                //修改权限
                $auth->update($oldname,$perm);
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['index']);
            }
        }
        return $this->render('add',['permission'=>$model]);
    }
    //删除
    public function actionDelete(){
        $name = \Yii::$app->request->post('name');
        $auth = \Yii::$app->authManager;
        //找到要删除的权限
        $permission = $auth->getPermission($name);
        if($auth->remove($permission)){
            return 'true';
        }else{
            return 'false';
        }
    }
    //测试
    public function actionTest(){
        return $this->render('test');
    }

    //过滤
    public function behaviors(){
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'except'=>['login','logout','captcha'],
            ]
        ];
    }
}
