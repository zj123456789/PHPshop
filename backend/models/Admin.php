<?php

namespace backend\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "admin".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property string $password_reset
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $last_login_time
 * @property string $last_login_ip
 */
class Admin extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $password;
    public $repassword;
    public $newpassword;
    public $oldpassword;
    //添加管理员时使用
    const SCENARIO_ADD = 'add';
    //管理员自己修改密码时使用
    const SCENARIO_EDIT = 'edit';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username','status', 'email'], 'required'],
            ['repassword', 'compare', 'compareAttribute'=>'password','on'=>[self::SCENARIO_ADD,self::SCENARIO_EDIT]],//验证两次密码是否一致
            [['repassword','password'],'required','on'=>[self::SCENARIO_ADD,self::SCENARIO_EDIT]],//确认密码和密码
            [['oldpassword'],'required','on'=>[self::SCENARIO_EDIT]],// 旧密码

            ['newpassword','editpwd'],//调用该方法验证旧密码
            [['status'], 'integer'],
            ['username','unique','message'=>'用户名已存在'],
            ['email','unique','message'=>'邮箱已存在'],
            [['username', 'password', 'email'], 'string', 'max' => 255],
        ];
    }
    //修改密码
    public function editpwd(){
        //数据库密码
        $password_hash = $this->password_hash;
        //输入旧密码
        $oldpassword = Yii::$app->security->generatePasswordHash($this->oldpassword);
//        var_dump($password);exit;
        if($oldpassword != $password_hash){
           return $this->addError('password','旧密码错误');
        }
    }
    //添加修改
    public function beforeSave($insert)
    {
        //$insert-----是否是添加
        if($insert){
            //添加
            $this->created_at = time();
            $this->auth_key = Yii::$app->security->generateRandomString();
            $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        }else{
            //修改
            $this->updated_at = time();
            $this->auth_key = Yii::$app->security->generateRandomString();
            if($this->password != null){
                $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
            }
        }
        return parent::beforeSave($insert);
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'password' => '密码',
            'auth_key' => 'Auth Key',
            'password_reset' => '重置密码',
            'email' => '邮箱',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
            'last_login_time' => '最后登录时间',
            'last_login_ip' => '最后登录ip',
            'repassword'=>'确认密码',
            'oldpassword'=>'旧密码',
        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id'=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $authKey == $this->auth_key;
    }
}
