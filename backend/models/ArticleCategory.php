<?php
namespace backend\models;
use yii\db\ActiveRecord;

class ArticleCategory extends ActiveRecord{
    //验证规则
    public function rules(){
        return [
            [['name','intro','status','sort'],'required'],
            ['name','string','max'=>20],
            ['intro','string','max'=>255],
            ['sort','integer'],
        ];
    }
    //设置表单名
    public function attributeLabels(){
        return [
            'name'=>'名称',
            'intro'=>'简介',
            'status'=>'状态',
            'sort'=>'排序'
        ];
    }
}
