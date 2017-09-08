<?php
namespace backend\controllers;
use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\UploadedFile;

class ArticleCategoryController extends Controller{
    //文章列表
    public function actionIndex(){
         $count = \backend\models\ArticleCategory::find();
        //实例化一个分页根据类
        $pager = new Pagination([
            'totalCount'=>$count->count(),//总条数
            'defaultPageSize'=>3 //每页多少条
        ]);
        $models = $count->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }
    //添加文章
    public function actionAdd(){
        $model = new ArticleCategory();
        $request = \yii::$app->request;
        if($request->isPost){
            //接收数据
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                \yii::$app->session->setFlash('seccess','添加成功');
                return $this->redirect(['article-category/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //修改文章
    public function actionEdit($id){
        $model = ArticleCategory::findOne(['id'=>$id]);
        $request = \yii::$app->request;
        if($request->isPost){
            //接收数据
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                \yii::$app->session->setFlash('seccess','添加成功');
                return $this->redirect(['article-category/index']);
            }else{
                var_dump($model->getErrors());
                exit;
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //删除文章
    public function actionDelete($id){
        $model = ArticleCategory::findOne(['id'=>$id]);
        $model->status = -1;
        if($model->save(false)){
            \Yii::$app->session->setFlash('seccess','删除成功');
            return $this->redirect(['article-category/index']);
        }else{
            \Yii::$app->session->setFlash('warning','删除失败');
        }
    }
}
