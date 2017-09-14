<?php
namespace backend\models;
use yii\db\ActiveRecord;

class LoginForm extends ActiveRecord {
    public $username;
    public $password;
    public $code;
    public $remember;

    public function rules()
    {
        return [
            [['username','password'],'required'],
            ['remember','integer'],
            ['code','captcha','captchaAction'=>'admin/captcha'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password'=>'密码',
            'code'=>'验证码',
            'remember'=>'自动登录'
        ];
    }
    public function login(){
        $info = Admin::findOne(['username'=>$this->username]);
        //验证用户名
        if($info) {
            //验证密码
            $pwd = \Yii::$app->security->validatePassword($this->password, $info->password_hash);
            if ($pwd) {
                //验证成功,保存用户信息到session
                $ip = \Yii::$app->request->userIP;
                $info->last_login_ip = $ip;
                $info->last_login_time = time();
                $info->save(false);
//                var_dump($this->remember);exit;
                if($this->remember){
                    return \Yii::$app->user->login($info,7*24*3600);
                }
                return \Yii::$app->user->login($info);
            } else {
                //验证失败
                $this->addError('password', '密码不正确');
            }
            return false;
        }
    }

}
