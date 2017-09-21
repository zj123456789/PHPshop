
<br>
<div><a href="<?=\yii\helpers\Url::to(['menu/add'])?>" class="btn btn-success">添加菜单</a></div>
<br>
<table id="table_id_example" class="display">
    <thead>
    <tr>
        <th>菜单名</th>
        <th>路由</th>
        <th>排序</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($menus as $menu):?>
        <tr data_id="<?=$menu->id?>">
            <td><?=$menu->name?></td>
            <td><?=$menu->root?></td>
            <td><?=$menu->sort?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['menu/edit','id'=>$menu->id])?>" class="btn btn-warning">修改</a>
                <a href="javascript:;" class="btn btn-danger del" >删除</a>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

<?php
/**
 * @var $this \yii\web\View
 */
$this->registerCssFile('@web/fenye/data/css/jquery.dataTables.css');

$this->registerJsFile('@web/fenye/data/js/jquery.dataTables.js',['depends'=>\yii\web\JqueryAsset::className()]);
    $url = yii\helpers\Url::to(['menu/delete']);
    $this->registerJs(new \yii\web\JsExpression(
            <<<JS
                 $(document).ready( function () {
                 $('#table_id_example').DataTable(
                 
                 )
          } );
           $('.del').click(function() {
                if(confirm('你确定要删除吗')){
                  var tr = $(this).closest('tr');
                  var id = tr.attr('data_id');
                  $.post("{$url}",{id:id},function(data) {
                      console.debug(data);
                    if(data=='true'){
                        alert('删除成功!');
                            //移除节点
                        tr.hide('slow')
                    }else {
                         alert('删除失败!')
                    }
                  })
                }
            })
JS

    ));

