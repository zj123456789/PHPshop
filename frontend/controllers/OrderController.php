<?php

namespace frontend\controllers;

use backend\models\Goods;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\db\Exception;

class OrderController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;
    public function actionIndex()
    {
        if(\Yii::$app->user->isGuest){
            \Yii::$app->session->setFlash('failed','你还没有登录,请登录');
            return $this->redirect(['member/login']);
        }

        //支付方式和送货方式
        /*$delivery = Order::$delivery;
        $payment = Order::$payment;*/
        //收货人信息
        $member_id = \Yii::$app->user->getId();
        $address = Address::find()->where(['user_id'=>$member_id])->all();
        //购物车列表
        $carts = Cart::find()->where(['member_id'=>$member_id])->select(['goods_id','amount'])->all();

        $cart=[];
        //遍历成键值对的数组格式[goods_id=>amount]
        foreach ($carts as $v){
            $cart[$v['goods_id']]=$v['amount'];
        }
        //去商品表将对应的商品取出来
        $goods = Goods::find()->where(['in','id',array_keys($cart)])->all();

        if(\Yii::$app->request->isPost){
            $order = new Order();
            $order->load(\Yii::$app->request->post(),'');
            $address_id=\Yii::$app->request->post('address_id');//地址Id
            $delivery_id=\Yii::$app->request->post('delivery_id');//送货方式Id
            $payment_id=\Yii::$app->request->post('payment_id');//支付方式Id
            $addr = Address::findOne(['user_id'=>$member_id,'id'=>$address_id]);//根据用户Id和地址id找到地址

            //--地址有关
            $order->name = $addr->username;//收货人
            $order->tel = $addr->tel;//收货人电话
            $order->province = $addr->province;//省
            $order->city = $addr->city;//市
            $order->area = $addr->area;//区
            $order->address = $addr->address;//具体地址
            $order->memeber_id = $member_id;//当前用户id
            //------送货方式
            $delivery = Order::$delivery[$delivery_id];
            $order->delivery_id = $delivery_id;
            $order->delivery_name = $delivery[0];
            $order->delivery_price = $delivery[1];
            //------支付方式
            $payment = Order::$payment[$payment_id];
            $order->payment_id = $payment_id;
            $order->payment_name = $payment[0];
            //--------其他
            $order->status = 1;
            $order->create_time = time();
            $order->total = 0;//遍历购物车表里面的商品,累加计算,加上运费
            foreach ($goods as $good){
                $goods_amount = $cart[$good->id];//商品数量
                $goods_price = $good->shop_price;//商品价格
                $order->total += $goods_amount*$goods_price;//一种商品价格
            }
           //加运费
            $order->total =  $order->delivery_price+$order->total;
            //开启事务  处理商品库存不够的情况
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                var_dump($order->getErrors());
                $order->save(false);
                //订单商品详情表
                $carts = Cart::find()->where(['member_id'=>$member_id])->all();
                foreach ($carts as $cart){
                    //检查库存
                    if($cart->amount>$cart->goods->stock){
                        //抛出异常
                        throw new Exception($cart->goods->name.'库存不足,不能下单');
                    }
                    $order_goods = new OrderGoods();
                    $order_goods->order_id = $order->id;
                    $order_goods->goods_id = $cart->goods_id;
                    $order_goods->goods_name = $cart->goods->name;
                    $order_goods->logo = $cart->goods->LOGO;
                    $order_goods->price = $cart->goods->shop_price;
                    $order_goods->amount += $cart->amount;
                    //一种商品的总价
                    $order_goods->total = ($cart->amount)*($cart->goods->shop_price);
                    $order_goods->save();
                    $order->total += $order_goods->total;//一条订单总价

                    //清空购物车
                    $cart->delete();
                }
                //提交
                $transaction->commit();
                //跳转
                return $this->redirect(['order-list']);
            }catch (Exception $e){
                //不能下单,回滚
                $transaction->rollBack();
            }
        }
        //支付方式和送货方式
        $delivery = Order::$delivery;
        $payment = Order::$payment;
//        var_dump($delivery);exit;
        return $this->renderPartial('order',['delivery'=>$delivery,'payment'=>$payment,'address'=>$address,'cart'=>$cart,'goods'=>$goods]);
    }

    //订单列表
    public function actionOrderList(){
        if(\Yii::$app->user->isGuest){
            return $this->redirect(['member/login']);
        }
        $orders = Order::find()->where(['member_id'=>\Yii::$app->user->getId()])->all();

        return $this->renderPartial('order_list',['orders'=>$orders]);
    }

    public function actionTest(){
        $cart =Cart::findOne(['id'=>14]);
        var_dump($cart->goods);
    }

}
