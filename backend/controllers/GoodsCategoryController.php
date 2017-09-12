<?php

namespace backend\controllers;

use backend\models\GoodsCategory;
use yii\data\Pagination;

class GoodsCategoryController extends \yii\web\Controller
{
    //分类列表
    public function actionIndex()
    {
        $count = GoodsCategory::find();
        $pager = new Pagination([
            'totalCount'=>$count->count(),
            'defaultPageSize'=>3
        ]);
        $models = $count->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }
    //添加分类
    public function actionAdd(){
        $model = new GoodsCategory();
        $request = \yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //创建子分类 parent_id !=0
                if($model->parent_id){
                    $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                    $model->parent_id = $parent->id;
                    $model->prependTo($parent);
                }else{ //parent_id =0
                    //创建顶级分类
                    $model->parent_id = 0;
                    $model->makeRoot();
                }
                return $this->redirect(['index']);
            }
        }
        //回显所有分类
            //找到所有分类放到数组中
            $category = GoodsCategory::find()->select(['id','name','parent_id'])->asArray()->all();
            //添加一个顶级分类节点
            $parentCategory = ['id'=>0,'name'=>'顶级分类','parent_id'=>0];
            array_unshift($category,$parentCategory);
            //转成json字符串
            $category = json_encode($category);
//        var_dump($category);exit;
        return $this->render('add',['model'=>$model,'category'=>$category]);
    }
    //修改
    public function actionEdit($id){
        $model = GoodsCategory::findOne(['id'=>$id]);
        $request = \yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //创建子分类 parent_id !=0
                if($model->parent_id){
                    $parent = GoodsCategory::findOne(['id'=>$model->parent_id]);
                    $model->prependTo($parent);

                }else{ //parent_id =0  创建顶级分类

                    //原来就是顶级分类 Parent_id=0 就直接保存
                    if($model->getOldAttribute('parent_id')==0){
                        $model->save();
                    }else{ //否则就新建一个顶级分类
                        $model->makeRoot();
                    }
                }
                \Yii::$app->session->setFlash('seccess','修改成功');
                return $this->redirect(['index']);
            }
        }
        //回显所有分类
        //找到所有分类放到数组中
        $category = GoodsCategory::find()->select(['id','name','parent_id'])->asArray()->all();
        //添加一个顶级分类节点
        $parentCategory = ['id'=>0,'name'=>'顶级分类','parent_id'=>0];
        array_unshift($category,$parentCategory);
        //转成json字符串
        $category = json_encode($category);
//        var_dump($category);exit;
        return $this->render('add',['model'=>$model,'category'=>$category]);
    }
    //删除
    public function actionDelete(){
        $id = \Yii::$app->request->post('id');
        $model = GoodsCategory::findOne(['id'=>$id]);
        //是否是子节点  行为编辑器不提示
        if($model->isLeaf()){
            $model->deleteWithChildren();//删除根节点和子节点
            return 'true';
        }else{
            return 'false';
        }
        //$child = GoodsCategory::find()->where(['=','parent_id',$model->id])->all();
    }
    //测试
    public function actionTest(){
        $category = GoodsCategory::find()->select(['id','name','parent_id'])->asArray()->all();

        $category = json_encode($category);
        //不加载布局文件
        return $this->renderPartial('aa',['category'=>$category]);
    }

}
