<?php

namespace backend\controllers;

use backend\filter\RbacFilter;
use backend\models\Roles;
use yii\helpers\ArrayHelper;

class RolesController extends \yii\web\Controller
{
    //角色列表
    public function actionIndex()
    {
        $auth = \Yii::$app->authManager;
        $roles = $auth->getRoles();
        return $this->render('index',['roles'=>$roles]);
    }
    //添加角色
    public function actionAdd(){
        $model = new Roles();
        $model->scenario = Roles::ADD;
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //添加角色
                $auth = \Yii::$app->authManager;
                $role = $auth->createRole($model->name);
                $role->description = $model->description;
                $auth->add($role);
                //分配权限
                //如果多选框没√,返回null,√了返回数组
                if( $permissions = $model->permissions){
                    foreach ($permissions as $permission){
                        //根据名字获取对应的权限
                        $per = $auth->getPermission($permission);
//                    var_dump($per);exit;
                        $auth->addChild($role,$per);
                    }
                }
                return $this->redirect(['index']);
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //修改角色
    public function actionEdit($name){
        $auth = \Yii::$app->authManager;
        //找到该角色回显
        $role = $auth->getRole($name);
        //角色模型
        $model = new Roles();
        $model->scenario = Roles::EDIT;
        //将角色所拥有的名字,描述,权限交给模型回显
        $model->name = $role->name;
        $model->description = $role->description;
        //获取角色对应的权限
        $permissions = $auth->getPermissionsByRole($role->name);
//        var_dump($permissions);exit;
            //将权限名交给模型回显
        $model->permissions = array_keys($permissions);
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            //如果修改角色,就调用验证规则
            if($model->name != $name){
                $model->scenario=Roles::ADD;
            }
            //如果没修改角色
            if ($model->validate()) {
                $auth = \Yii::$app->authManager;
                //删除旧角色
                $auth->removeChildren($role);
                //创建新角色
                $role->name = $model->name;
                $role->description = $model->description;
                //修改角色
                $auth->update($name,$role);
                //分配权限
                //如果多选框没√,返回null,√了返回数组
                if ($permissions = $model->permissions) {
                    foreach ($permissions as $permission) {
                        //根据名字获取对应的权限
                        $per = $auth->getPermission($permission);
//                      var_dump($per);exit;
                        $auth->addChild($role, $per);
                    }
                }
                    return $this->redirect(['index']);
                }
        }
        return $this->render('add',['model'=>$model]);
    }
    //删除角色
    public function actionDelete(){
        $name = \Yii::$app->request->post('name');
        $auth = \Yii::$app->authManager;
        //找到要删除的角色
        $role = $auth->getRole($name);
//        $auth->removeChildren($role);
        if($auth->remove($role)){
            return 'true';
        }else{
            return 'false';
        }
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
