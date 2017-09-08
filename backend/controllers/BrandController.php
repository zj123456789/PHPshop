<?php

namespace backend\controllers;

use backend\models\Brand;
use yii\data\Pagination;
use yii\web\UploadedFile;
use flyok666\uploadifive\UploadAction;

class BrandController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $count = Brand::find();
        //实例化一个分页根据类
        $pager = new Pagination([
            'totalCount'=>$count->count(),//总条数
            'defaultPageSize'=>3 //每页多少条
        ]);
        $models = $count->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }

    //添加品牌
    public function actionAdd(){
        $model = new Brand();
        $request = \yii::$app->request;
        if($request->isPost){
            //接收数据
            $model->load($request->post());
            //实例化一个模型来处理文件
            //$model->file = UploadedFile::getInstance($model,'file');
            if($model->validate()){
              /*  //获得后缀名
                $ext = $model->file->getExtension();
                //放在指定路径
                $logo = '/upload/'.uniqid().$ext;
                //文件另存为
                $model->file->saveAs(\yii::getAlias('@webroot').$logo,false);
                //将路径保存到数据库
                $model->logo = $logo;*/
                $model->save();
                \yii::$app->session->setFlash('seccess','添加成功');
                return $this->redirect(['brand/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //修改品牌
    public function actionEdit($id){
        $model = Brand::findOne(['id'=>$id]);
        $request = \yii::$app->request;
        if($request->isPost){
            //接收数据
            $model->load($request->post());
            //实例化一个模型来处理文件
            //$model->file = UploadedFile::getInstance($model,'file');
            if($model->validate()){
              /*  //获得后缀名
                $ext = $model->file->getExtension();
                //放在指定路径
                $logo = '/upload/'.uniqid().$ext;
                //文件另存为
                $model->file->saveAs(\yii::getAlias('@webroot').$logo,false);
                //将路径保存到数据库
                $model->logo = $logo;*/
                $model->save();
                \yii::$app->session->setFlash('seccess','添加成功');
                return $this->redirect(['brand/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //删除
    public function actionDelete($id){
        $model = Brand::findOne(['id'=>$id]);
        $model->status = -1;
        if($model->save(false)){
            \Yii::$app->session->setFlash('seccess','删除成功');
            return $this->redirect(['brand/index']);
        }else{
            \Yii::$app->session->setFlash('warning','删除失败');
        }
    }

    //文件上传 与 文本编辑
    public function actions() {
        return [
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
                    $action->output['fileUrl'] = $action->getWebUrl();//返回文件路径
                    //$action->getFilename(); // "image/yyyymmddtimerand.jpg"
                    //$action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
                   // $action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
                },
            ],

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
}

