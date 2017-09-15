<?php
namespace backend\models;
use yii\base\Model;

class PermissionForm extends Model{
    public $name;//权限名字
    public $description;//权限描述
    const SCENARIO_ADD='add';
    public function rules(){
        return [
            [['name','description'],'required'],
            ['name','validateName','on'=>self::SCENARIO_ADD],
        ];
    }
    //权限名验证
    public function validateName(){
        //只管错误的
        $auth = \Yii::$app->authManager;
        if($auth->getPermission($this->name)){
            $this->addError('name','权限已存在');
        }
    }
    public function attributeLabels(){
        return[
            'name'=>'权限名(路由)',
            'description'=>'权限描述',
        ];
    }
}
