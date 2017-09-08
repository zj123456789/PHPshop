<?php
namespace backend\controllers;
use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;

class ArticleController extends Controller{
    //文章列表
        public function actionIndex(){
            $count = Article::find();
            //实例化一个分页模型
            $pager = new Pagination([
                'totalCount'=>$count->count(),
                'defaultPageSize'=>3,
            ]);
            $models = $count->limit($pager->limit)->offset($pager->offset)->all();
            return $this->render('index',['models'=>$models,'pager'=>$pager]);
        }
        //添加文章
    public function actionAdd(){
            //实例化表单模型
        $model = new Article();
        $model_d = new ArticleDetail();
        //实例化请求模型
        $request = new Request();
        if($request->isPost){
            //接收数据
            $model->load($request->post());
            //验证数据
            if($model->validate()){
                //添加时间
                $model->create_time = time();
                $model->save();

                return $this->redirect(['article/index']);
            }
        }
        //回显下拉
       $articleCategory = ArticleCategory::find()->all();
        $a=[];
        foreach ($articleCategory as $value) {
            $a[$value->id] = $value->name;
        }
//        var_dump($a);exit;
        //展示表单页面
        return $this->render('add',['model'=>$model,'model_d'=>$model_d,'a'=>$a]);
    }
}
