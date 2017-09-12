<?php
?>

<div><a href="<?=\yii\helpers\Url::to(['article/add'])?>" class="btn btn-success">添加文章</a></div>
<table class="table table-bordered table-responsive">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>分类</th>
        <th>状态</th>
        <th>简介</th>
        <th>排序</th>
        <th>添加时间</th>
        <th>操作</th>
    </tr>
    <?php foreach ($models as $model):?>
        <tr data_id="<?=$model->id?>">
            <td><?=$model->id?></td>
            <td><?=$model->name?></td>
            <td><?=$model->articleCategory->name?></td>
            <td><?php
                if($model->status==1){
                    echo '正常';
                }else if($model->status==0){
                    echo '隐藏';
                }else{
                    echo '已删除';
                }?></td>
            <td><?=$model->intro?></td>
            <td><?=$model->sort?></td>
            <td><?=date("Y-m-d H:i:s",$model->create_time)?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['article/edit','id'=>$model->id])?>" class="btn btn-warning">修改</a>
                <a href="<?=\yii\helpers\Url::to(['article/show','id'=>$model->id])?>" class="btn btn-warning">查看文章</a>
                <a href="javascript:;" class="btn btn-danger del" >删除</a>
            </td>
        </tr>
    <?php endforeach;?>
</table>

<?php
/**
 * @var $this \yii\web\View
 */
    $ulr = \yii\helpers\Url::to(['article/delete']);
    $this->registerJs(new \yii\web\JsExpression(
        <<<JS
        $('.del').click(function() {
          if(confirm("你确定删除吗？")){
              var tr = $(this).closest('tr');
              var id = tr.attr('data_id');
              //地址  参数  回调
              $.post("{$ulr}",{'id':id},function(data) {
                if(data=='true'){
                    alert('删除成功');
                    tr.hide('slow');
                }else {
                    alert('删除失败')
                }
              })
          }
        })
        
JS

    ));

echo \yii\widgets\LinkPager::widget([
        'pagination'=>$pager,
    'firstPageLabel'=>'第一页',
    'lastPageLabel'=>'最后一页',
    'prevPageLabel'=>'prev',
    'nextPageLabel'=>'next'

]);?>
