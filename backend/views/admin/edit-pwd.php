<?php
//开始表单
$form = yii\bootstrap\ActiveForm::begin();
//用户名
echo $form->field($model,'username')->textInput();
//旧密码
echo $form->field($model,'oldpassword')->passwordInput(['placeholder'=>"输入旧密码"]);
//新密码
echo $form->field($model,'password')->passwordInput(['placeholder'=>"输入新密码"]);
//确认密码
echo $form->field($model,'repassword')->passwordInput(['placeholder'=>"确认密码"]);

echo yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
//结束表单
yii\bootstrap\ActiveForm::end();
