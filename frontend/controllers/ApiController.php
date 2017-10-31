<?php
namespace frontend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\LoginForm;
use frontend\models\Member;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii\db\Exception;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\Response;

class ApiController extends Controller{
    public $token = "ymc_8803";
    public $result = [
        "error" => false,//成功为true
        "msg" => "",//返回的错误信息
        "data" => [],//返回数据
    ];
    public $enableCsrfValidation=false;
    public function init(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        parent::init();
    }
    public function actionP(){
        var_dump(\Yii::$app->security->generatePasswordHash('123'));
    }
//=========收货地址==================
    //地址列表
    public function actionList(){
        if(!\Yii::$app->user->isGuest){
            $models = Address::find()->where(['user_id'=>\Yii::$app->user->identity->getId()])->all();
            if(!$models==false){
                $this->result['error']=false;
                $this->result['data'][]=$models;
            }else{
                $this->result['error']=false;
                $this->result['msg']='该用户没添加地址';
            }
        }else{
            $this->result['msg']='请登录';
        }
       return $this->result;
    }
    //修改地址 传地址id 地址 传收货人 地址 收货人电话
    public function actionEdit(){
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
                    $this->result['error']=false;
                    $this->result['msg']='修改成功';
                    $this->result['data'][]=[
                        'username'=>$model->username,
                        'tel'=>$model->tel,
                        'province'=>$model->province,
                        'city'=>$model->city,
                        'area'=>$model->area,
                        'address'=>$model->address,
                        'user_id'=>$model->user_id
                    ];

                }else{
                    $this->result['msg']=$model->getErrors();
                }
            }else{
                $this->result['msg']='提交方式错误';
            }

        }
        return $this->result;
    }
    //删除地址 传地址id
    public function actionDelete(){
        if(!\Yii::$app->user->isGuest){
            $id = \Yii::$app->request->post('id');
            $model = Address::findOne(['id'=>$id]);
            if($model->delete()){
                $this->result['error']=false;
                $this->result['msg']='删除成功';
            }else{
                $this->result['msg']='删除失败';
            }
        }else{
            $this->result['msg']='请登录';
        }
        return $this->result;

    }
    //添加地址 传收货人 地址 收货人电话
    public function actionAdd(){
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
                   $this->result['error']=false;
                   $this->result['msg']='添加成功';
                   $this->result['data'][]=[
                       'username'=>$model->username,
                       'tel'=>$model->tel,
                       'province'=>$model->province,
                       'city'=>$model->city,
                       'area'=>$model->area,
                       'address'=>$model->address,
                       'user_id'=>$model->user_id
                   ];

               }else{
                   $this->result['msg']=$model->getErrors();
               }
           }else{
               $this->result['msg']='提交方式错误';
           }

        }
        return $this->result;


    }
//=========用户==================
    //获取当前登录用户的信息 用户id
    public function actionInfo(){
        $info='';
        if(!\Yii::$app->user->isGuest){
            $info =  \Yii::$app->user->identity;
        }else{
            $this->result['error']=true;
            $this->result['msg']='未登录';
        }

       return $info;
    }
    //修改密码 传用户Id 旧密码 新密码
    public function actionUpdate(){

        if(\Yii::$app->request->isPost){
            //用户id
            $id = \Yii::$app->request->post('id');
            $model = Member::findOne(['id'=>$id]);
            if($model==null){
                $this->result['msg']='用户不存在';
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
                    $this->result['msg']='旧密码错误';
                }else{
                    $model->password_hash = \Yii::$app->security->generatePasswordHash($newpassword);
                    $model->updated_at = time();
                    $model->save(false);
                    $this->result['error']=false;
                    $this->result['msg']='密码修改成功';
                }
        }else{
            $this->result['msg']='提交方式不正确';
        }

        return $this->result;
    }
    //用户登录  传用户名 密码
    public function actionLogin(){
        $model = new LoginForm();
        //提交表单
        if(\Yii::$app->request->isPost) {
            //接收数据
            $model->load(\Yii::$app->request->post(),'');
            if ($model->validate()) {
                //登录认证
                $denglu = $model->login();
                if ($denglu) {
                    $this->result['error']=false;
                    $this->result['msg']='登录成功';
                }
            }else{
                $this->result['msg']=$model->getErrors();
            }
        }else{
            $this->result['msg']='提交方式不正确';
        }
        return $this->result;
    }
    //用户注册  需要传用户名 密码 邮箱 手机号
    public function actionRegist(){
        if(\Yii::$app->request->isPost){
            $model = new Member();
            $model->load(\Yii::$app->request->post(),'');
            if($model->validate()){
                $model->status = 1;
                $model->auth_key = \Yii::$app->security->generateRandomString();
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password);
                $model->created_at = time();
                $this->result['error']=false;
                $this->result['msg']='注册成功';
                $model->save(false);
                $this->result['data']['member_id']=$model->id;
            }else{
                $this->result['msg']=$model->getErrors();
            }
        }else{
            $this->result['msg']='提交方式不正确';
        }
        return $this->result;
    }
//=========商品分类==================
    //获取所有商品分类
    public function actionGoodsCategory(){
        if (\Yii::$app->request->isGet){//get请求
            $data = GoodsCategory::find()->all();
            $this->result['error'] = true;
            $this->result['data'] = $data;
        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
    //获取某分类的所有子分类
    public function actionChildren(){
        if (\Yii::$app->request->isGet){
            $id = \Yii::$app->request->get("id");//某一个分类的id
            if (isset($id)){
                $model = GoodsCategory::findOne($id);//得到分类
                if ($model){
                    $children = $model->children(2)->all();
                    $this->result['error'] = true;
                    $this->result['data'] = $children;
                }else{
                    $this->result['msg'] = "没有找到该分类";
                }
            }else{
                $this->result['msg'] = "缺少参数";
            }
        }else{
            $this->result['msg'] = "请求方式错误";
        }
        return $this->result;
    }
    //获取某分类的父分类
    public function actionParent(){
        if (\Yii::$app->request->isGet){
            $id = \Yii::$app->request->get("id");
            if (isset($id)){
                $model = GoodsCategory::findOne($id);//获取该分类
                if ($model){
                    $parent = $model->parents(1)->all();
                    $this->result['error'] = true;
                    $this->result['data'] = $parent;
                }else{
                    $this->result['msg'] = "没有找到该分类";
                }
            }else{
                $this->result['msg'] = "缺少参数";
            }
        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
//=========商品==================
    //获取某分类下面的所有商品
    public function actionGoodsByCategory(){
        if (\Yii::$app->request->isGet){
            $id = \Yii::$app->request->get("id");
            if (isset($id)){
                $category = GoodsCategory::findOne($id);//找到该分类
                if ($category) {
                    $query = Goods::find();
                    //三种情况  1级分类 2级分类 3级分类
                    if($category->depth == 2){//3级分类
                        //sql: select * from goods where goods_category_id = $category_id
                        $models = $query->andWhere(['goods_category_id'=>$id])->all();
                    }else{
                        //1级分类 2级分类
                        //$category id = 5
                        //3级分类ID  7 8
                        //SQL select *  from goods where goods_category_id  in (7,8)
                        /* $ids = [];//  [7,8]
                         foreach ($category->children()->andWhere(['depth'=>2])->all() as $category3){
                             $ids[]=$category3->id;
                         }*/
                        $ids = $category->children()->select('id')->andWhere(['depth'=>2])->column();
                        $models = $query->andWhere(['in','goods_category_id',$ids])->all();
                    }
                    $data = [];
                    foreach ($models as $model){
                        if ($model->status==1 && $model->is_on_sale==1){
                            $data[] = $model;
                        }
                    }
                    $this->result['error'] =true;
                    $this->result['data'] = $data;
                }else{
                    $this->result['msg'] = "没有找到这个分类";
                }
            }else{
                $this->result['msg'] = "缺少参数";
            }
        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
    //获取某品牌下面的所有商品
    public function actionGoodsByBrand(){
        if (\Yii::$app->request->isGet){
            $id = \Yii::$app->request->get("id");
            if (isset($id)){//判断id是否存在
                $brand = Brand::findOne($id);
                if ($brand) {//判断是否找到了这个品牌
                    $models = $brand->goods;//获取这个品牌的所有商品
                    $data = [];
                    foreach ($models as $model){
                        if ($model->status==1 && $model->is_on_sale==1){
                            $data[] = $model;
                        }
                    }
                    $this->result['error'] =true;
                    $this->result['data'] = $data;
                }else{
                    $this->result['msg'] = "没有找到这个分类";
                }
            }else{
                $this->result['msg'] = "缺少参数";
            }
        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
//=========文章==================
    //获取文章分类
    public function actionArticleCategory(){
        if (\Yii::$app->request->isGet){
            $data = ArticleCategory::find()->all();
            $this->result["error"] = true;
            $this->result["data"] = $data;
        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
    //获取某分类下面的所有文章
    public function actionArticleByCategory(){
        if (\Yii::$app->request->isGet){
            $id = \Yii::$app->request->get("id");
            if (isset($id)){//判断id是否存在
                $category = ArticleCategory::findOne($id);
                if ($category) {//判断是否找到了这个分类
                    $models = $category->article;//获取这个分类的所有文章
                    foreach ($models as $model){
                        $model->article_category_id = $category->name;
                    }
                    $this->result['error'] =true;
                    $this->result['data'] = $models;
                }else{
                    $this->result['msg'] = "没有找到这个分类";
                }
            }else{
                $this->result['msg'] = "缺少参数";
            }
        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
    //获取某文章所属分类
    public function actionCategoryByArticle(){
        if (\Yii::$app->request->isGet){
            $id = \Yii::$app->request->get("id");
            if (isset($id)){//判断id是否存在
                $article = Article::findOne($id);
                if ($article) {//判断是否找到了该文章
                    $models = $article->articleCategory;//获取该文章的分类
                    $this->result['error'] =true;
                    $this->result['data'] = $models;
                }else{
                    $this->result['msg'] = "没有找到该文章";
                }
            }else{
                $this->result['msg'] = "缺少参数";
            }
        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
//=========购物车==================
    //添加商品到购物车
    public function actionCartAdd(){
        if (\Yii::$app->request->isPost){
            $cart = new Cart();
            $cart->load(\Yii::$app->request->post(),'');//加载用户提交的数据
            if (!$cart->validate()){//如果验证不通过
                $this->result['msg'] = $cart->getErrors();
                return $this->result;
            }
            $goods_id = $cart->goods_id;
            $amount = $cart->amount;
            if (\Yii::$app->user->isGuest){
                //检测是否已经有了购物车
                $cookies = \Yii::$app->request->cookies;
                $val = $cookies->getValue('carts');
                if ($val){
                    $val = unserialize($val);
                }else{
                    $val = [];
                }

                //检测购物车中已经有了该商品
                if (array_key_exists($goods_id,$val)){
                    $val[$goods_id] += $amount;
                }else{
                    $val[$goods_id] = $amount;
                }

                //保存数据到cookie
                $cookies = \Yii::$app->response->cookies;
                $cookie = new Cookie();
                $cookie->name = 'carts';
                $cookie->value = serialize($val);
                $cookie->expire = time()+7*24*3600;//保存7天
                $cookies->add($cookie);
            }else{//登录的用户 将购物车信息保存到数据库
                $model = Cart::find()->andWhere(['and',['goods_id'=>$goods_id],['member_id'=>\Yii::$app->user->getId()]])->one();
                if ($model){
                    $model->amount += $amount;
                }else{
                    $model = new Cart();
                    $model->goods_id = $goods_id;
                    $model->amount = $amount;
                }
                $model->save(false);
            }
            $this->result["error"] = true;
        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
    //修改购物车某商品数量
    public function actionCartEdit(){
        if (\Yii::$app->request->isPost){
            $cart = new Cart();
            $cart->load(\Yii::$app->request->post(),'');
            if (!$cart->validate()){
                $this->result['msg'] = $cart->getErrors();
                return $this->result;
            }
            $goods_id = $cart->goods_id;
            $amount = $cart->amount;
            if(\Yii::$app->user->isGuest){
                $cookies = \Yii::$app->request->cookies;
                $value = $cookies->getValue('carts');
                if($value){
                    $carts = unserialize($value);
                }else{
                    $carts = [];
                }

                //检查购物车中是否存在当前需要添加的商品
                if(array_key_exists($goods_id,$carts)){
                    $carts[$goods_id] = $amount;
                }else{
                    $this->result["msg"] = "购物车中不存在该商品";
                    return $this->result;
                }

                $cookies = \Yii::$app->response->cookies;
                $cookie = new Cookie();
                $cookie->name = 'carts';
                $cookie->value = serialize($carts);
                $cookie->expire = time()+7*24*3600;//过期时间戳
                $cookies->add($cookie);
            }else{
                $member_id = \Yii::$app->user->getId();
                $model = Cart::find()->where(['and',['member_id'=>$member_id],['goods_id'=>$goods_id]])->one();
                if ($model){
                    $model->amount = $amount;
                    $model->save(false);
                }else{
                    $this->result["msg"] = "购物车中该商品不存在";
                    return $this->result;
                }
            }
            $this->result["error"] = true;
        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
    //删除购物车某商品
    public function actionCartDel(){
        if (\Yii::$app->request->isPost){
            $goods_id = \Yii::$app->request->post("goods_id");
            if (!isset($goods_id)){
                $this->result["msg"] = "缺少参数";
                return $this->result;
            }
            if (\Yii::$app->user->isGuest){
                $value = \Yii::$app->request->cookies->getValue('carts');//
                if ($value){
                    $carts = unserialize($value);
                }else{
                    $this->result["msg"] = "还没有购物车.";
                    return $this->result;
                }
                //var_dump($carts);exit();
                if (array_key_exists($goods_id,$carts)){//如果该商品在数组中
                    unset($carts[$goods_id]);//删除数组中的这个值
                }else{//如果不在
                    $this->result["msg"] = "购物车中没有这个商品.";
                    return $this->result;
                }
                $cookie = new Cookie();
                $cookie->name = 'carts';
                $cookie->value = serialize($carts);
                $cookie->expire = time()+7*24*3600;//过期时间戳
                \Yii::$app->response->cookies->add($cookie);
            }else{
                $member_id = \Yii::$app->user->getId();
                $cart = Cart::find()->where(['and',['member_id'=>$member_id],['goods_id'=>$goods_id]])->one();
                if ($cart!=null){
                    $cart->delete();
                }else{
                    $this->result["msg"] = "购物车中没有这个商品.";
                    return $this->result;
                }
            }
            $this->result["error"] = true;
        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
    //清空购物车
    public function actionCartClear(){
        if (\Yii::$app->request->isGet){
            if (\Yii::$app->user->isGuest){
                $value = \Yii::$app->request->cookies->getValue('carts');//
                if ($value){
                    \Yii::$app->response->cookies->remove('carts');
                }
            }else{
                $member_id = \Yii::$app->user->getId();
                Cart::deleteAll(['member_id'=>$member_id]);//删除这个用户的购物车
            }
            $this->result["error"] = true;
        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
    //获取购物车所有商品
    public function actionCartShow(){
        if (\Yii::$app->request->isGet){
            if (\Yii::$app->user->isGuest){
                $value = \Yii::$app->request->cookies->getValue('carts');//
                if ($value){
                    $carts = unserialize($value);
                }else{
                    $this->result["msg"] = "购物车为空.";
                    return $this->result;
                }
                $this->result["data"] = $carts;
            }else{
                $member_id = \Yii::$app->user->getId();
                $carts = Cart::findAll(["member_id"=>$member_id]);
                if ($carts!=[]){
                    $this->result["data"] = $carts;
                }else{
                    $this->result["msg"] = "购物车中没有这个商品.";
                    return $this->result;
                }
            }
            $this->result["error"] = true;
        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
///=========订单==================
    //获取支付方式->获取订单有哪些支付方法
    public function actionPay(){
        if (\Yii::$app->request->isGet){
            $paymethod = Order::$payment;
            $datas = [];
            foreach ($paymethod as $key=>$value){
                $data = new Json();
                $data->id = $key;
                $data->name = $value[0];
                $datas[] = $data;
            }
            $this->result["error"] = true;
            $this->result["data"] = $datas;;
        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
    //获取送货方式
    public function actionDelivery(){
        if (\Yii::$app->request->isGet){
            $shipping = Order::$delivery;
            $datas = [];
            foreach ($shipping as $key=>$value){
                $data = new Json();
                $data->id = $key;
                $data->name = $value[0];
                $datas[] = $data;
            }
            $this->result["error"] = true;
            $this->result["data"] = $datas;
        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
    //提交订单->结算购物车中的所有商品
    public function actionOrderAdd(){
        $request = \Yii::$app->request;
        if ($request->isPost){
            if (\Yii::$app->user->isGuest){
                $this->result["msg"] = "请先登录";
                return $this->result;
            }
            //获得数据
            $member_id = \Yii::$app->user->getId();
            $order = new Order();
            $order->load($request->post(),'');//获得送货方式id 和 支付方式id, 地址id
            if (!$order->validate()){
                $this->result["msg"] = $order->getErrors();
                return $this->result;
            }
            //商品清单
            $carts = Cart::findAll(['member_id'=>$member_id]);//获得用户购物车中的所有数据  在Carts模型中建立关系
            if (empty($carts)){//如果购物车中没有商品 , 就返回商城首页
                $this->result["msg"] = "购物车中没有要结算的商品";
                return $this->result;
            };
            foreach ($carts as $cart){
                if ($cart->amount > $cart->goods->stock){
                    return $this->result["msg"] = $cart->goods->name."库存不足,不能下单";
                }
            }

            $addr_id = $order->addr_id;//地址id
            $order->member_id = $member_id;
            $addr = Address::find()->where(['and',['member_id'=>$member_id],['id'=>$addr_id]])->one();
            $order->name = $addr->consignee;
            $order->province = $addr->prov;
            $order->city = $addr->city;
            $order->area = $addr->area;
            $order->address = $addr->de_address;
            $order->tel = $addr->tel;
            $delivery = Order::$delivery[$order->delivery_id];
            $order->delivery_name = $delivery[0];
            $order->delivery_price = $delivery[1];
            $payment = Order::$payment[$order->payment_id];
            $order->payment_name = $payment[0];
            $order->status = 1;
            $order->create_time = time();
            $price_totals = 0;
            foreach ($carts as $cart){
                //这个商品的总价
                $price_total = $cart->goods->shop_price*$cart->amount;//价格
                $price_totals += $price_total;//总价格
            }
            $order->total = $price_totals;

            //生成>订单商品详情表
            $transaction = \Yii::$app->db->beginTransaction();//开启事物
            try {//这里有bug  订单商品详情没有记录   订单却成功了
                $order->save(false);
                foreach ($carts as $cart) {

                    if ($cart->goods->stock < $cart->amount ) {
                        //抛出异常
                        throw new Exception($cart->goods->name."库存不足,不能下单");
                    }
                    $orderGoods = new OrderGoods();
                    $orderGoods->order_id = $order->id;
                    $orderGoods->goods_id = $cart->goods_id;
                    $orderGoods->goods_name = $cart->goods->name;
                    $orderGoods->logo = $cart->goods->goodsGallery[0]->path;
                    $orderGoods->price = $cart->goods->shop_price;
                    $orderGoods->amount = $cart->amount;
                    $orderGoods->total = $orderGoods->price * $orderGoods->amount;
                    //保存订单商品详情表
                    $orderGoods->save();
                    //清空购物车
                    $cart->delete();
                }
                //没有异常,提交事物
                $transaction->commit();
                //提交成功->
                //跳转到成功页面
                $this->result["error"] = true;
                return $this->result;
            }catch (Exception $e){
                //出现异常 回滚
                $transaction->rollBack();
                $order->delete();
                $this->result["msg"] = $e;
                return $this->result;
            }

        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
    //获取当前用户订单列表
    public function actionOrderShow(){
        if (\Yii::$app->request->isGet){
            $models = Order::find()->where(['member_id'=>\Yii::$app->user->getId()])->all();
            $this->result['error'] = true;
            $this->result['data'] = $models;
        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
    //取消订单
    public function actionOrderCancel(){
        if (\Yii::$app->request->isGet){
            $order_id = \Yii::$app->request->get('order_id');
            if (isset($order_id)){
                $order = Order::findOne(['id'=>$order_id]);
                if ($order_id!==null){
                    $order->status = 0;
                    $order->save(false);
                    $this->result["error"] = true;
                }else{
                    $this->result['msg'] = "订单不存在";
                }
            }else{
                $this->result['msg'] = "缺少参数";
            }
        }else{
            $this->result['msg'] = "请求方式出错";
        }
        return $this->result;
    }
    //防重放,防篡改,防伪造请求
    public function check(){
        $error = '';
        if (\Yii::$app->request->isPost){
            $data = \Yii::$app->request->post();
        }else{
            $data = \Yii::$app->request->get();
        }
        $time = isset($data['time'])?$data['time']:0;
        if ($time===0){
            //缺少参数(参数缺少时间戳)
            $error = "缺少参数";
        }
        if (time()-$time>60||$time>time()){
            //请求过期(,防止重放)
            $error = "请求过期";
        }
        $sign = isset($data['sign'])?$data['sign']:0;
        if ($sign===0){
            //缺少参数(缺少签名)
            $error = "缺少参数";
        }
        unset($data['sign']);//如果这个键不存在也不会报错.
        ksort($data);//按升序排序 索引
        $key = $this->token.http_build_query($data);//将排序好的参数拼接与请求端商量好的token
        $my_sign = strtoupper(md5($key));
        if ($my_sign!==$sign){
            //缺少参数(防止篡改,防止伪造请求).
            $error = "缺少参数";
        }

        if($error){
            \Yii::$app->response->data = [
                'error'=>false,
                'msg'=>$error,//错误信息,如果有
                'data'=>[]//返回数据
            ];
            \Yii::$app->response->send();
        }
    }
}
