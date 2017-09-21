<?php
namespace backend\models;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class Roles extends Model{
    public $name;
    public $description;
    public $permissions;
    const ADD='add';
    const EDIT='edit';
    public function rules(){
        return [
            [['name','description'],'required'],
            ['name','validateName','on'=>self::ADD],
            ['name','validateEditName','on'=>self::EDIT],
            ['permissions','safe'],
        ];
    }
    //
    public function validateName(){
        //根据提交的角色名字去数据库找是否存在
        if(\Yii::$app->authManager->getRole($this->name)){
                $this->addError('name','该角色已存在');
        }
    }
    public function validateEditName(){
        //如果修改了名字
        if(\Yii::$app->request->get('name') != $this->name){
            //验证是否存在
            if(\Yii::$app->authManager->getRole($this->name)){
                $this->addError('name','该角色已存在');
            }
        }//没修改不做处理

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
