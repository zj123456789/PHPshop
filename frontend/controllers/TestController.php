<?php
namespace frontend\controllers;
use yii\web\Controller;

class TestController extends Controller{
    //测试模板静态化--ob缓存
    public function actionTest(){
        $data = $this->renderPartial('@frontend/views/goods-category/index.php');
        file_put_contents(\Yii::getAlias('@frontend/web/index.html'),$data);
    }
}
