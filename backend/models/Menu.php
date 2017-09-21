<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent_id
 * @property string $root
 * @property integer $sort
 */
class Menu extends \yii\db\ActiveRecord
{
    public $depth;//层级
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'sort','depth'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['root'], 'string', 'max' => 255],
            [['name','sort','parent_id'],'required'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'parent_id' => '上级菜单',
            'root' => '路由',
            'sort' => '排序',
        ];
    }
}
