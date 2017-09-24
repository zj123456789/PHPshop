<?php

namespace frontend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use yii\data\Pagination;

class GoodsCategoryController extends \yii\web\Controller
{
    public function actionIndex(){
        //先找到所有分类
        //遍历Parent_id=0的
        //然后再遍历parent_id=上级分类id (二级分类)
        //再遍历一次parent_id=上级分类id (三级分类)
        //之后根据分类id去商品表把商品列出来
        //根据商品id把相册显示出来

        $categorys = GoodsCategory::find()->where(['parent_id'=>0])->all();

        return $this->renderPartial('index',['categorys'=>$categorys]);
    }
    //商品列表
    public function actionList($category_id){
        $category = GoodsCategory::findOne(['id'=>$category_id]);
        $query = Goods::find();

        if($category->depth==2){//3级分类
            $query->andWhere(['goods_category_id'=>$category_id]);
        }else{////1级分类 2级分类

            $ids = $category->children()->select('id')->andWhere(['depth'=>2])->column();
            //var_dump($ids);exit;
            $query->andWhere(['in','goods_category_id',$ids]);
        }
        $pager = new Pagination();
        $pager->totalCount = $query->count();
        $pager->defaultPageSize = 2;
//        var_dump($pager);
        $models = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->renderPartial('list',['models'=>$models,'pager'=>$pager]);
    }
    //商品详情页
    public function actionDetail($goods_id){
        $model = Goods::findOne(['id'=>$goods_id]);
        $gallerys = GoodsGallery::find()->where(['goods_id'=>$goods_id])->all();
        return $this->renderPartial('goods',['model'=>$model,'gallerys'=>$gallerys]);
    }
    //到地址页面
    public function actionAddress(){
        return $this->redirect(['member/address']);
    }

}
