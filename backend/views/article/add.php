<?php

//开始表单
$form = yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textInput();
echo $form->field($model,'status')->inline(['type'=>true])->radioList(['0'=>'隐藏','1'=>'正常']);
echo $form->field($model,'article_category_id')->dropDownList($a);
echo $form->field($model,'sort')->textInput();
echo yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
//结束表单
yii\bootstrap\ActiveForm::end();