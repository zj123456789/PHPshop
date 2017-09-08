<?php
namespace backend\models;
use yii\db\ActiveRecord;

class Brand extends ActiveRecord{
    //上传头像属性
    public $file;
    //验证规则
    public function rules(){
        return [
            [['name','intro','status','sort'],'required'],
            ['name','string','max'=>20],
            ['intro','string','max'=>255],
            ['sort','integer'],
            //此时这个头像属性应该是自定义那个,并不是数据库那个字段
            ['logo','string','max'=>255,],
            //['file','file','skipOnEmpty'=>false,'extensions'=>['jpg','png',' gif','jpeg']],
        ];
    }
    //设置表单名
    public function attributeLabels(){
        return [
            'name'=>'名称',
            'intro'=>'简介',
            'status'=>'状态',
            //此时这个头像属性应该是自定义那个
            'logo'=>'LOGO',
            'sort'=>'排序'
        ];
    }

}
