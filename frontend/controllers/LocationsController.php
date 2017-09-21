<?php

namespace frontend\controllers;

use frontend\models\Locations;
use frontend\models\LoginForm;

class LocationsController extends \yii\web\Controller
{
    //省
    public function actionProvince($depth)
    {
        $model = Locations::find()->where(['depth'=>$depth])->asArray()->all();

        echo json_encode($model);
    }
    //市
    public function actionCity($id)
    {
        $model = Locations::find()->where(['parent_id'=>$id])->asArray()->all();

        echo json_encode($model);
    }
    //区
    public function actionArea($id)
    {
        $model = Locations::find()->where(['parent_id'=>$id])->asArray()->all();

        echo json_encode($model);
    }


}
