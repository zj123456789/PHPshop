<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form ->field($model,'name')->textInput();
echo $form ->field($model,'description')->textInput();
echo $form ->field($model,'permissions')->inline(['type'=>'true'])->checkboxList(\backend\models\Roles::Permissions());
echo yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
