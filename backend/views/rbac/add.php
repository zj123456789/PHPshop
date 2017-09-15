<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form ->field($permission,'name')->textInput();
echo $form ->field($permission,'description')->textInput();
echo yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
