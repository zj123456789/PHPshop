<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to login:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model,'code')->widget(
            \yii\captcha\Captcha::className(), [
            'captchaAction'=>'admin/captcha',  //找到该方法,用于设置验证码样式
            'template'=>"<div class='row'><div class='col-lg-2'>{image}</div><div class='col-lg-2'>{input}</div></div>"
            ]);?>

            <?= $form->field($model, 'remember')->checkbox() ?>

            <div class="form-group">
                <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php
//开始表单
/*$form = yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'code')->widget(
    \yii\captcha\Captcha::className(), [
        'captchaAction'=>'admin/captcha',  //找到该方法,用于设置验证码样式
        'template'=>"<div class='row'><div class='col-lg-2'>{image}</div><div class='col-lg-2'>{input}</div></div>"
    ]);
echo $form->field($model,'remember')->checkbox();
echo yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
//结束表单
yii\bootstrap\ActiveForm::end();*/
