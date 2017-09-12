
<style>
    .sub{
        margin-bottom: 11px;
    }
</style>
<div><a href="<?=\yii\helpers\Url::to(['goods/add'])?>" class="btn btn-success">添加品牌</a></div>
<br>
<?php
$form = \yii\bootstrap\ActiveForm::begin([
    'method' => 'get',
    //get方式提交,需要显式指定action
    'action'=>\yii\helpers\Url::to(['goods/index']),
    'options'=>['class'=>'form-inline con']
]);
echo $form->field($search,'name')->textInput(['placeholder'=>'商品名'])->label(false);
echo $form->field($search,'sn')->textInput(['placeholder'=>'货号'])->label(false);
echo $form->field($search,'minPrice')->textInput(['placeholder'=>'￥'])->label(false);
echo $form->field($search,'maxPrice')->textInput(['placeholder'=>'￥'])->label('--');
echo "&emsp;";
echo '<button type="submit" class="btn btn-info glyphicon glyphicon-search sub"><strong style="font-size: 16px;">搜索</strong></button>';
\yii\bootstrap\ActiveForm::end();
?>

<br>
<table class="table table-bordered table-responsive">
    <tr>
        <th>ID</th>
        <th>商品名称</th>
        <th>货号</th>
        <th>状态</th>
        <th>库存</th>
        <th>价格</th>
        <th>LOGO</th>
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model):?>
        <tr data_id="<?=$model->id?>">
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->sn?></td>
            <td><?php
                if($model->status==1){
                    echo '正常';
                }else if($model->status==0){
                    echo '隐藏';
                }else{
                    echo '已回收';
                }?></td>
            <td><?=$model->stock?></td>
            <td><?=$model->shop_price?></td>
            <td><img src="<?=$model->LOGO?>" class="img-circle" style="width: 80px"></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['goods/gallery','id'=>$model->id])?>" class="btn btn-warning">相册</a>
                <a href="<?=\yii\helpers\Url::to(['goods/edit','id'=>$model->id])?>" class="btn btn-warning">修改</a>
                <a href="javascript:;" class="btn btn-danger del" >删除</a>
            </td>
        </tr>
    <?php endforeach;?>
</table>

<?php
/**
 * @var $this \yii\web\View
 */
    $url = yii\helpers\Url::to(['goods/delete']);
    $this->registerJs(new \yii\web\JsExpression(
            <<<JS
            $('.del').click(function() {
              if(confirm('你确定要删除吗')){
                  var tr = $(this).closest('tr');
                  var id = tr.attr('data_id');
                  $.post("{$url}",{id:id},function(data) {
                      console.debug(data);
                    if(data=='true'){
                        alert('删除成功!');
                        //移除节点
                        tr.hide('slow');
                    }else {
                        alert('删除失败!')
                    }
                  });
              }
            })
JS

    ));


//data-confirm="你确定删除吗？"



echo \yii\widgets\LinkPager::widget([
        'pagination'=>$pager,
    'firstPageLabel'=>'第一页',
    'lastPageLabel'=>'最后一页',
    'prevPageLabel'=>'prev',
    'nextPageLabel'=>'next'

]);?>
