<?php

namespace backend\controllers;

use backend\models\Roles;

class RolesController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $auth = \Yii::$app->authManager;
        $roles = $auth->getRoles();
        return $this->render('index',['roles'=>$roles]);
    }
    //添加角色
    public function actionAdd(){
        $model = new Roles();
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

}
