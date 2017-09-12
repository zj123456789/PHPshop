<?php
use yii\web\JsExpression;
use \kucha\ueditor\UEditor;
//开始表单
$form = yii\bootstrap\ActiveForm::begin();
//商品名称
echo $form->field($model,'name')->textInput();
//==================
//编辑器
//商品简介
echo $form->field($model_intro,'content')->widget('kucha\ueditor\UEditor',[
    'clientOptions' => [
        //编辑区域大小
        'initialFrameHeight' => '200',
        //设置语言
        'lang' =>'zh-cn', //中文为 zh-cn
    ]
]);
//================
//商品分类
echo $form->field($model,'goods_category_id')->dropDownList($GC);
//品牌分类
echo $form->field($model,'brand_id')->dropDownList($Bd);
//市场价
echo $form->field($model,'market_price')->textInput();
//商品价格
echo $form->field($model,'shop_price')->textInput();
//库存stock
echo $form->field($model,'stock')->textInput(['type'=>'number']);
//是否在售is_on_sale
echo $form->field($model,'is_on_sale')->inline(['type'=>true])->radioList(['1'=>'是','0'=>'否']);
//商品状态
echo $form->field($model,'status')->inline(['type'=>true])->radioList(['1'=>'正常','0'=>'隐藏']);
//=====================================
//外部TAG
echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
//logo
echo $form->field($model,'LOGO')->hiddenInput();
//文件处理
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['someKey' => 'someValue'],//上传文件的同时传参
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
        $('#goods-logo').val(data.fileUrl);
        //用户选择图片后回显在页面
        $('#img').attr('src',data.fileUrl);
    }
}
EOF
        ),
    ]
]);
//=====================================
echo yii\bootstrap\Html::img($model->LOGO,['class'=>'img-circule','style'=>'width:80px','id'=>'img']);
//echo $form->field($model,'file')->fileInput();
echo $form->field($model,'sort')->textInput();
echo yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
//结束表单
yii\bootstrap\ActiveForm::end();