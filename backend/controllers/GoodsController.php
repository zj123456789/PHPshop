<?php

namespace backend\controllers;

use backend\models\GoodsGallery;
use backend\models\GoodsDayCount;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsIntro;
use backend\models\GoodsSearch;
use flyok666\qiniu\Qiniu;
use flyok666\uploadifive\UploadAction;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class GoodsController extends \yii\web\Controller
{
    //商品列表
    public function actionIndex()
    {
        $Goods = Goods::find();
        //表单模型
        $Form = new GoodsSearch();
        //接收表单提交的查询参数
        $Form->search($Goods);
        $pager = new Pagination([
            'totalCount'=>$Goods->count(),
            'defaultPageSize'=>3
        ]);

        $models = $Goods->limit($pager->limit)->offset($pager->offset)->orderBy('id DESC')->all();
        return $this->render('index',['models'=>$models,'pager'=>$pager,'search'=>$Form]);
    }
    //添加商品
    public function actionAdd(){
        //商品表
        $model = new Goods();
        //商品详情表
        $model_intro = new GoodsIntro();
        $request = \yii::$app->request;
        if($request->isPost){
            //接收数据
            $model->load($request->post());
            $model_intro->load($request->post());
            if($model->validate() && $model_intro->validate()){
                //得到今天的时间
                $date = date("Y-m-d",time());
                //找到今天添加的商品
                $GoodsDay = GoodsDayCount::findOne(['day'=>$date]);
//                var_dump($GoodsDay);exit;
                if($GoodsDay){//添加过
                    //条数加一
                   $GoodsDay->count = $GoodsDay->count+1;
                   //将数据库的时间转化成时间戳
                   $time = strtotime($GoodsDay->day);
                   //得到今天的时间格式
                   $day = date('Ymd',$time);
                    //拼接sn
                    $sn = str_pad($GoodsDay->count,4,'0',STR_PAD_LEFT);
                    $model->sn = $day.$sn;
                   /* if($count<10){
                        $model->sn = $date.'000'.($count+1);
                    }elseif ($count<100 && $count>=10){
                        $model->sn = $date.'00'.($count+1);
                    }elseif($count>=100 && $count<1000){
                        $model->sn = $date.'0'.($count+1);
                    }else{
                        $model->sn = $date .($count+1);
                    }*/
                }else{//今天没添加过商品
                    //添加条数
                    $GoodsDay = new GoodsDayCount();
                    $GoodsDay->count = 1;
                    //添加时间到商品时间表
                    $GoodsDay->day = $date;
                    //将数据库的时间转化成时间戳
                    $time = strtotime($GoodsDay->day);
                    //得到今天的时间格式
                    $day = date('Ymd',$time);
                    $model->sn = $day .'0001';
                }

                //保存商品表
                $model->create_time = time();
//                var_dump($model);exit;
                $model->save();
                $GoodsDay->save();
                //保存到详情表
                $model_intro->goods_id=$model->id;
                $model_intro->save();
                \yii::$app->session->setFlash('seccess','添加成功');
                return $this->redirect(['goods/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        //商品分类zTree
        $category = GoodsCategory::find()->select(['id','name','parent_id'])->asArray()->all();
        //添加一个顶级分类节点
        $parentCategory = ['id'=>0,'name'=>'顶级分类','parent_id'=>0];
        array_unshift($category,$parentCategory);
        //转成json字符串
        $goods_category = json_encode($category);
        $models = new GoodsCategory();
        //品牌下拉框回显
        $Brand = Brand::find()->where(['!=','status','-1'])->all();
        $Bd = [];
        foreach ($Brand as $value){
            $Bd[$value->id] = $value->name;
        }
        return $this->render('add',['model'=>$model,'models'=>$models,'model_intro'=>$model_intro,'goods_category'=>$goods_category,'Bd'=>$Bd]);
    }
    //修改商品
    public function actionEdit($id){
        $model = Goods::findOne(['id'=>$id]);
        $request = \Yii::$app->request;
        //商品详情表
        $model_intro = GoodsIntro::findOne(['goods_id'=>$id]);
        if($request->isPost){
            $model->load($request->post());
            $model_intro->load($request->post());
            if($model->validate() && $model_intro->validate()){
                $model_intro->save();
                $model->save();
                return $this->redirect('index');
            }else{
                var_dump($model_intro->getErrors());
                exit;
            }
        }
        //商品分类下拉框回显
        //商品分类下拉框回显
        $category = GoodsCategory::find()->select(['id','name','parent_id'])->asArray()->all();
        //添加一个顶级分类节点
        $parentCategory = ['id'=>0,'name'=>'顶级分类','parent_id'=>0];
        array_unshift($category,$parentCategory);
        //转成json字符串
        $goods_category = json_encode($category);
        $models =GoodsCategory::findOne(['id'=>$model->goods_category_id]);
        //品牌下拉框回显
        $Brand = Brand::find()->where(['!=','status','-1'])->all();
        $Bd = [];
        foreach ($Brand as $value){
            $Bd[$value->id] = $value->name;
        }

        return $this->render('add',['model'=>$model,'models'=>$models,'goods_category'=>$goods_category,'Bd'=>$Bd,'model_intro'=>$model_intro]);
    }
    //删除
    public function actionDelete(){
        $id = \Yii::$app->request->post('id');
        $model = Goods::findOne(['id'=>$id]);
        $model_intro = GoodsIntro::findOne(['goods_id'=>$id]);
        $date = date('Y-m-d',time());
        $GoodsDay = GoodsDayCount::findOne(['day'=>$date]);
        if($GoodsDay){
            $count = $GoodsDay->count;
            $GoodsDay->count= $count-1;
        }
        if( $model->delete() && $model_intro->delete() && $GoodsDay->save()){
            return 'true';
        }else{
            return 'false';
        }
    }
    //文件上传 与 文本编辑
    public function actions() {
        return [
            //七牛云
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
                /* 'format' => function (UploadAction $action) {
                     $fileext = $action->uploadfile->getExtension();
                     $filename = sha1_file($action->uploadfile->tempName);
                     return "{$filename}.{$fileext}";
                 },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png','gif','jpeg'],
                    'maxSize' => 2 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    //$action->output['fileUrl'] = $action->getWebUrl();//返回文件路径
                    //$action->getFilename(); // "image/yyyymmddtimerand.jpg"
                    //$action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
                    // $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    $qiniu = new Qiniu(\yii::$app->params['qiniuyun']);
                    $key = $action->getWebUrl();
                    $file = $action->getSavePath();

                    $qiniu->uploadFile($file,$key);
                    $url = $qiniu->getLink($key);//
                    $action->output['fileUrl'] = $url;
                },
            ],
            //相册
            's-gallery' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                //BEGIN METHOD
                //'format' => [$this, 'methodName'],
                //END METHOD
                //BEGIN CLOSURE BY-HASH
                'overwriteIfExist' => true,
                /* 'format' => function (UploadAction $action) {
                     $fileext = $action->uploadfile->getExtension();
                     $filename = sha1_file($action->uploadfile->tempName);
                     return "{$filename}.{$fileext}";
                 },*/
                //END CLOSURE BY-HASH
                //BEGIN CLOSURE BY TIME
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },
                //END CLOSURE BY TIME
                'validateOptions' => [
                    'extensions' => ['jpg', 'png','gif','jpeg'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    //$action->output['fileUrl'] = $action->getWebUrl();//返回文件路径
                    //$action->getFilename(); // "image/yyyymmddtimerand.jpg"
                    //$action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
                    // $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                    $id = \Yii::$app->request->post('goods_id');
                    $gallery = new GoodsGallery();
                    $gallery->goods_id = $id;
                    $gallery->path = $action->getWebUrl();
                    $gallery->save();
                    $action->output['id'] = $gallery->id;
                    $action->output['fileUrl'] = $gallery->path;
                },
            ],
            //文本编辑
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
                //配置
                'config' => [
                    //"imageUrlPrefix"  => "http://www.baidu.com",//图片访问路径前缀
                    "imagePathFormat" => "/edit_upload/image/{yyyy}{mm}{dd}/{time}{rand:6}" ,//上传保存路径
                    "imageRoot" => \Yii::getAlias("@webroot"),
                ]
            ]
        ];
    }
    //相册
    public function actionGallery($id){
        $gallerys = GoodsGallery::find()->where(['goods_id'=>$id])->all();
        return $this->render('gallery',['gallerys'=>$gallerys,'id'=>$id]);
    }
    //删除相册图片
    public function actionDelGallery(){
        $id = \Yii::$app->request->post('id');
        $model = GoodsGallery::findOne(['id'=>$id]);
        if($model && $model->delete()){
            return 'true';
        }else{
            return 'false';
        }
    }
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

}
