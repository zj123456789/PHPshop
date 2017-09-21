<?php
namespace backend\filter;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

class RbacFilter extends ActionFilter{
    public function beforeAction($action){
        //检查是否有当前路由的权限
        $url = \Yii::$app->user->can($action->uniqueId);
        if(!$url){
            //判断有没有登录,没有登录就跳转到登录页面
            if(\Yii::$app->user->isGuest){
                //跳转必须要执行send方法,确保页面直接跳转.否则该次操作没有被拦截,相当于返回了true.
                return $action->controller->redirect([\Yii::$app->user->loginUrl])->send();
            }
            throw new ForbiddenHttpException('对不起,您没有该操作权限');
        }
        return parent::beforeAction($action);
    }
}
