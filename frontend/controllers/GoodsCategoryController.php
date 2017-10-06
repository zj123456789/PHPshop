<?php

namespace frontend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use frontend\models\SphinxClient;
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
    //搜索
    public function actionSearch($keyword){

        $cl = new SphinxClient();
        $cl->SetServer ( '127.0.0.1', 9312);

        $cl->SetConnectTimeout ( 10 );//超时
        $cl->SetArrayResult ( true );//返回结果格式

        $cl->SetMatchMode ( SPH_MATCH_EXTENDED2);//匹配模式
        $cl->SetLimits(0, 1000);//设置返回结果

        $res = $cl->Query($keyword,'goods');
        $ids=[];
        if(isset($res['matches'])){
            //找到
            foreach ($res['matches'] as $match){
                $ids[]=$match['id'];
            }
        }//没找到不处理
        //根据id查出商品
        $model = Goods::find();
        if($keyword){
            $model->where(['in','id',$ids]);
        }
        $pager = new Pagination([
            'totalCount'=>$model->count(),
            'defaultPageSize'=>2
        ]);
        $goods = $model->limit($pager->limit)->offset($pager->offset)->all();
        return $this->renderPartial('list',['pager'=>$pager,'models'=>$goods]);


    }

}
