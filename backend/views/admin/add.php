<?php
//开始表单
$form = yii\bootstrap\ActiveForm::begin();
//用户名
echo $form->field($model,'username')->textInput();
//密码
echo $form->field($model,'password')->passwordInput();
//确认密码
echo $form->field($model,'repassword')->passwordInput();
//邮箱
echo $form->field($model,'email')->textInput(['type'=>'email']);
//商品状态
echo $form->field($model,'status')->inline(['type'=>true])->radioList(['1'=>'正常','0'=>'隐藏']);
//角色
echo $form ->field($model,'roles')->inline(['type'=>'true'])->checkboxList(\backend\models\Admin::Roles());
echo yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
//结束表单
yii\bootstrap\ActiveForm::end();

