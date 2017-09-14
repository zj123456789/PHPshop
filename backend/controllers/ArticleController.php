<?php
namespace backend\controllers;
use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Request;

class ArticleController extends Controller{
         //文章列表
        public function actionIndex(){
            $count = Article::find();
            //实例化一个分页模型
            $pager = new Pagination([
                'totalCount'=>$count->where(['!=','status','-1'])->count(),
                'defaultPageSize'=>6,
            ]);
            $models = $count->limit($pager->limit)->offset($pager->offset)->where(['!=','status','-1'])->orderBy('id DESC')->all();

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
            $model_d->load($request->post());
            //验证数据  这里不能用且,要用或
            if($model->validate() && $model_d->validate()){
                //添加时间
                $model->create_time = time();
                $model->save();
                //获取新增文章id
                $id = $model->id;
                $model_d->article_id = $id;
                $model_d->save();
                return $this->redirect(['article/index']);
            }
        }
        //回显下拉
       $articleCategory = ArticleCategory::find()->where(['!=','status','-1'])->all();
        $a=[];
        foreach ($articleCategory as $value) {
            $a[$value->id] = $value->name;
        }
//        var_dump($a);exit;
        //展示表单页面
        return $this->render('add',['model'=>$model,'model_d'=>$model_d,'a'=>$a]);
    }
        //修改文章
        public function actionEdit($id){
        //实例化表单模型
        $model = Article::findOne(['id'=>$id]);
        $model_d = ArticleDetail::findOne(['article_id'=>$id]);
        //实例化请求模型
        $request = new Request();
        if($request->isPost){
            //接收数据
            $model->load($request->post());
            $model_d->load($request->post());
            //验证数据
            if($model->validate() && $model_d->validate()){
                //添加时间
                $model->create_time = time();
                $model->save();
                $model_d->save();

                return $this->redirect(['article/index']);
            }
        }
        //回显下拉
        $articleCategory = ArticleCategory::find()->where(['!=','status','-1'])->all();
        $a=[];
        foreach ($articleCategory as $value) {
            $a[$value->id] = $value->name;
        }
//        var_dump($a);exit;
        //展示表单页面
        return $this->render('add',['model'=>$model,'model_d'=>$model_d,'a'=>$a]);
    }
        //删除
        public function actionDelete(){
            $id = \Yii::$app->request->post('id');
            $model = Article::findOne(['id'=>$id]);
            $model->status = -1;
            if($model->save(false)){
                return 'true';
            }else{
                return 'false';
            }
        }
        //查看文章详情
        public function actionShow($id){
                $model_d = ArticleDetail::findOne(['article_id'=>$id]);

                $model = Article::findOne(['id'=>$id]);
//                var_dump($model_d);exit;
                return $this->render('show',['model'=>$model,'model_d'=>$model_d]);
        }
        //编辑器
        public function actions(){
            return [
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

