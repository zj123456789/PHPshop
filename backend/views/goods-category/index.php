<?php
?>

<div><a href="<?=\yii\helpers\Url::to(['goods-category/add'])?>" class="btn btn-success">添加分类</a></div>
<table class="table table-bordered table-responsive">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>简介</th>
        <th>父分类id</th>
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model):?>
        <tr data_id="<?=$model->id?>">
            <td><?=$model->id?></td>
            <td><?php
                echo  str_repeat('==',$model->depth).$model->name;
                ?></td>
            <td><?=$model->intro?></td>
            <td><?=$model->parent_id?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['goods-category/edit','id'=>$model->id])?>" class="btn btn-warning">修改</a>
                <a href="javascript:;" class="btn btn-danger del" >删除</a>
            </td>
        </tr>
    <?php endforeach;?>
</table>

<?php
/**
 * @var $this \yii\web\View
 */
    $url = yii\helpers\Url::to(['goods-category/delete']);
    $this->registerJs(new \yii\web\JsExpression(
            <<<JS
            $('.del').click(function() {
              if(confirm('你确定要删除吗')){
                  var tr = $(this).closest('tr');
                  var id = tr.attr('data_id');
                  $.post("{$url}",{id:id},function(data) {
                    if(data=='true'){
                        alert('删除成功!');
                        //移除节点
                        tr.hide('slow');
                    }else {
                        alert('删除失败,该分类下有子分类!')
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
