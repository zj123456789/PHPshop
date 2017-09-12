<?php
$form = \yii\widgets\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'parent_id')->hiddenInput();
//=========ztree==========
echo '<div><ul id="treeDemo" class="ztree"></ul></div>';
/**
 * @var $this \yii\web\View
 */
//注册css文件
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');//相对路径
$this->registerCssFile('@web/zTree/css/demo.css');//相对路径
//注册js文件  依赖于jquery
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
//注册ztree的js
$this->registerJs(new \yii\web\JsExpression(
    <<<JS
    var zTreeObj;
           var setting = {
                data: {
                    simpleData: {
                        enable: true,//使用简单数据模式
                        idKey: "id",
                        pIdKey: "parent_id",
                        rootPId: 0
                    }
                },  
                callback: {
		            onClick: function(event, treeId, treeNode) {
                        $('#goodscategory-parent_id').val(treeNode.id);
                    },
	           } 
           };
        var zNodes = {$category};
        //输出分类列表
        zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
         //展开全部节点
        zTreeObj.expandAll(true);
        //修改回显  
        var node = zTreeObj.getNodeByParam("id", "{$model->parent_id}", null);//防止value为空报错,加""
        //选中老爸
        zTreeObj.selectNode(node);   
JS
));
//=========ztree==========
echo $form->field($model,'intro')->textarea();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
$form = \yii\widgets\ActiveForm::end();
