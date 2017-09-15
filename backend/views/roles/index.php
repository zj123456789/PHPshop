
<br>
<div><a href="<?=\yii\helpers\Url::to(['roles/add'])?>" class="btn btn-success">添加角色</a></div>
<br>
<table class="table table-bordered table-responsive " id="table_id">
    <tr>
        <th>角色</th>
        <th>描述</th>
        <th>权限</th>
        <th>操作</th>
    </tr>
    <?php foreach ($roles as $role):?>
        <tr data_id="<?=$role->name?>">
            <td><?=$role->name?></td>
            <td><?=$role->description?></td>
            <td><?=$role->description?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['rbac/edit','name'=>$role->name])?>" class="btn btn-warning">修改</a>
                <a href="javascript:;" class="btn btn-danger del" >删除</a>
            </td>
        </tr>
    <?php endforeach;?>
</table>

<?php
/**
 * @var $this \yii\web\View
 */
    $url = yii\helpers\Url::to(['rbac/delete']);
    $this->registerJs(new \yii\web\JsExpression(
            <<<JS
            $('.del').click(function() {
              if(confirm('你确定要删除吗')){
                  var tr = $(this).closest('tr');
                  var name = tr.attr('data_id');
                  $.post("{$url}",{name:name},function(data) {
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
           
} );
JS

    ));

