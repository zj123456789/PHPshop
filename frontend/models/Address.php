<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "address".
 *
 * @property integer $id
 * @property string $username
 * @property integer $tel
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property string $default_address
 */
class Address extends \yii\db\ActiveRecord
{
    public $cmbProvince;
    public $cmbCity;
    public $cmbArea;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username','tel','cmbProvince','cmbCity','cmbArea','address'],'required'],
            [['tel'], 'integer'],
            [['username'], 'string', 'max' => 20],
            [['province', 'city', 'area', 'address', 'default_address'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'tel' => 'Tel',
            'province' => 'Province',
            'city' => 'City',
            'area' => 'Area',
            'address' => 'Address',
            'default_address' => 'Default Address',
        ];
    }
}
