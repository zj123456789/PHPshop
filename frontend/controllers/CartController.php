<?php

namespace frontend\controllers;

use backend\models\Goods;
use frontend\models\Cart;
use function PHPSTORM_META\elementType;
use yii\web\Cookie;

class CartController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;
    public function actionAddcart($goods_id,$amount){
//        var_dump($goods_id,$amount);exit;
        if(\Yii::$app->user->isGuest){//未登录,将数据放到cookie中
            $cookies = \Yii::$app->request->cookies;//读操作
            $value= $cookies->getValue('cart');//根据名字获取值
            if($value){//存在就反序列化
                $cart = unserialize($value);//反序列化才是一个数组,因为保存时是将数组序列化保存的
            }else{//不存在就定义一个空数组
                $cart = [];
            }
//            var_dump($cart);exit;
            //检查购物车中是否存在当前需要添加的商品
            if(array_key_exists($goods_id,$cart)){
                $cart[$goods_id] += $amount;
            }else{
                $cart[$goods_id] = $amount;
            }
//            var_dump($cart);exit;
            $cookies = \Yii::$app->response->cookies;//写操作
            $cookie = new Cookie();
            $cookie->name = 'cart';
            $cookie->value = serialize($cart);//值时字符串,所以需要序列化保存
            $cookie->expire = time()+7*24*3600;//过期时间戳---多久后过期
            $cookies->add($cookie);

        }else{//已登录

            $member_id = \Yii::$app->user->getId();
            //根据商品id和用户id查询数据库是否存在该商品
            $goods =  Cart::findOne(['member_id'=>$member_id,'goods_id'=>$goods_id]);
            if($goods){
                //如果存在就更新数量
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
        //直接跳转到购物车
        return $this->redirect(['cart']);
    }
    //购物车列表
    public function actionCart(){
        if(\Yii::$app->user->isGuest){//未登录
            //从cookie中取
            $cookies = \Yii::$app->request->cookies;
            $value = $cookies->getValue('cart');
            if($value){
                $cart = unserialize($value);//$carts = [1=>2,2=>10]
            }else{
                $cart = [];
            }
            $models = Goods::find()->where(['in','id',array_keys($cart)])->all();

        }else{//已登录
            $member_id = \Yii::$app->user->getId();
            //把当前用户的购物车里的商品id和数量取出来
            $carts = Cart::find()->where(['member_id'=>$member_id])->select(['goods_id','amount'])->all();
            $cart=[];
            //遍历成键值对的数组格式[goods_id=>amount]
            foreach ($carts as $v){
//                var_dump($v['goods_id']);exit;
                $cart[$v['goods_id']]=$v['amount'];
            }
            //去商品表将对应的商品取出来
            $models = Goods::find()->where(['in','id',array_keys($cart)])->all();
//            var_dump($cart,$models);exit;
        }
        return $this->renderPartial('cart',['models'=>$models,'cart'=>$cart]);
    }
    //ajax加减购物车数量  web下的js下的cart1.js发起
    public function actionAjax(){
        $goods_id = \Yii::$app->request->post('goods_id');
        $amount = \Yii::$app->request->post('amount');
        if(\Yii::$app->user->isGuest){//未登录
            //从cookie中取出值
            $cookies = \Yii::$app->request->cookies;
            $value = $cookies->getValue('cart');
            if($value){
                $carts = unserialize($value);
            }else{
                $carts = [];
            }
            //检查购物车中是否存在当前需要添加的商品
            if(array_key_exists($goods_id,$carts)){
                $carts[$goods_id] = $amount;//覆盖
            }
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'cart';
            $cookie->value = serialize($carts);
            $cookie->expire = time()+7*24*3600;//过期时间戳
            $cookies->add($cookie);
        }else{
            $model = Cart::findOne(['goods_id'=>$goods_id]);
            if($model){
                $model->amount = $amount;
                $model->save();
            }
        }
    }

    //删除购物车数据
    public function actionDelete(){
        $goods_id = \Yii::$app->request->post('goods_id');
        if(\Yii::$app->user->isGuest){//未登录
            //从cookie中取出值
            $cookies = \Yii::$app->request->cookies;
            $value = $cookies->getValue('cart');
            if($value){
                $carts = unserialize($value);//数组格式
                unset($carts[$goods_id]);
                //删除后保存
                $cookies = \Yii::$app->response->cookies;
                $cookie = new Cookie();
                $cookie->name = 'cart';
                $cookie->value = serialize($carts);
                $cookie->expire = time()+7*24*3600;//过期时间戳
                $cookies->add($cookie);
                return "true";
            }else{
                return "false";
            }
        }else{//已登录
            $model = Cart::findOne(['goods_id'=>$goods_id]);
            if ($model->delete()){
                return "true";
            }else{
                return "false";
            }
        }
    }

}
