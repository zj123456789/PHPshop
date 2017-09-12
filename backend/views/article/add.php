<?php

//开始表单
$form = yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textarea(['rows'=>8]);
echo $form->field($model_d,'content')->widget('kucha\ueditor\UEditor',[
    'name'=>'upload',
    'clientOptions' => [
        //编辑区域大小
        'initialFrameHeight' => '200',
        //设置语言
        'lang' =>'zh-cn', //中文为 zh-cn
        //定制菜单
        /* 'toolbars' => [
             [
                 'fullscreen', 'source', 'undo', 'redo', '|',
                 'fontsize',
                 'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat',
                 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|',
                 'forecolor', 'backcolor', '|',
                 'lineheight', '|',
                 'indent', '|'
             ],
         ]*/
    ]
]);
echo $form->field($model,'status')->inline(['type'=>true])->radioList(['0'=>'隐藏','1'=>'正常']);
echo $form->field($model,'article_category_id')->dropDownList($a);
echo $form->field($model,'sort')->textInput();
echo yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
//结束表单
yii\bootstrap\ActiveForm::end();