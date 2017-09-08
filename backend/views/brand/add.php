<?php
use yii\web\JsExpression;
use \kucha\ueditor\UEditor;
//开始表单
$form = yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
//==================
//编辑器
//echo $form->field($model,'intro')->hiddenInput();

echo $form->field($model,'intro')->widget('kucha\ueditor\UEditor',[
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
//================
echo $form->field($model,'status')->inline(['type'=>true])->radioList(['0'=>'隐藏','1'=>'正常']);
echo $form->field($model,'logo')->hiddenInput();
//=====================================
//外部TAG
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
//文件处理
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['someKey' => 'someValue'],
        'width' => 120,
        'height' => 40,
        'onError' => new JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadComplete' => new JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        console.log(data.fileUrl);
        //将处理好的图片放入表单一起提交
        $('#brand-logo').val(data.fileUrl);
        //用户选择图片后回显在页面
        $('#img').attr('src',data.fileUrl);
    }
}
EOF
        ),
    ]
]);
//=====================================
echo yii\bootstrap\Html::img($model->logo,['class'=>'img-circule','style'=>'width:80px','id'=>'img']);
//echo $form->field($model,'file')->fileInput();
echo $form->field($model,'sort')->textInput();
echo yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
//结束表单
yii\bootstrap\ActiveForm::end();