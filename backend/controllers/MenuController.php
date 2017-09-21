<?php

namespace backend\controllers;

use backend\filter\RbacFilter;
use backend\models\Menu;

class MenuController extends \yii\web\Controller
{
    //菜单列表
    public function actionIndex()
    {
        $model = Menu::find()->all();
//        $models = $this->Sort($model);
        return $this->render('index',['menus'=>$model]);
    }
    //
  /*  public function Sort(&$model,$parent_id=0){
        static $children = [];
        foreach ($model as $child){
            if($child['parent_id'] == $parent_id){
                $children[] = $child;
                $this->Sort($children,$child['id']);
            }
        }
        return $children;
    }*/
    //添加菜单
    public function actionAdd(){
        $model = new Menu();
        //获取当前路由
        $auth = \Yii::$app->authManager;
        $permissions = $auth->getPermissions();
        //菜单
        $menu = Menu::find()->asArray()->all();
        $top = ['id'=>'0','name'=>'顶级菜单'];
        $menu = array_merge([$top],$menu);
//        var_dump($menu);exit;
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
//                var_dump($model->parent_id);exit;
                if($model->parent_id==0){
                    $model->root = '';
                    $model->save();
                }else{
                    $model->save();
                }
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model,'permissions'=>$permissions,'menu'=>$menu]);
    }
    //修改菜单
    public function actionEdit($id){
        $model = Menu::findOne(['id'=>$id]);
        //获取路由
        $auth = \Yii::$app->authManager;
        $permissions = $auth->getPermissions();
        //菜单
        $menu = Menu::find()->asArray()->all();
        $top = ['id'=>'0','name'=>'顶级菜单'];
        $menu = array_merge([$top],$menu);
        $request = \Yii::$app->request;
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                if($model->parent_id==0){
                    $model->root = '';
                    $model->save();
                }else{
                    $model->save();
                }
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model,'permissions'=>$permissions,'menu'=>$menu]);
    }
    //删除菜单
    public function actionDelete(){
        $id = \Yii::$app->request->post('id');
        $model = Menu::findOne(['id'=>$id]);
        if($model->delete()){
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
