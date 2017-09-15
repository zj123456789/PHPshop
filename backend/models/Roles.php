<?php
namespace backend\models;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class Roles extends Model{
    public $name;
    public $description;
    public $permissions;

    public function rules(){
        return [
            [['name','description'],'required'],
            ['permissions','safe'],
        ];
    }
    //权限
    public static function Permissions(){
        $permissions = \Yii::$app->authManager->getPermissions();
//        var_dump($permissions);exit;
            return ArrayHelper::map($permissions,'name','description');
   /*     $per = [];
            foreach ($permissions as $permission){
                $per[$permission->name]=$permission->description;
            }
        return $per;*/
    }
    //标签
    public function attributeLabels(){
        return [
            'name'=>'角色',
            'description'=>'描述',
            'permissions'=>'权限'
        ];
    }
}
