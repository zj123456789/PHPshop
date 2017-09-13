<?php

namespace backend;

use backend\models\Admin;
use yii\data\Pagination;

class AdminController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $model = Admin::find();
        $pager = new Pagination([
            'totalCount'=>$model->count(),
            '$defaultPageSize'=>2
        ]);
        $admin = $model->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['admin'=>$admin,'pager'=>$pager]);
    }

}
