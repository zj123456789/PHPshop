
<br>
<div><a href="<?=\yii\helpers\Url::to(['rbac/add'])?>" class="btn btn-success">添加权限</a></div>
<br>
<table id="table_id_example" class="display">
    <thead>
    <tr>
        <th>权限</th>
        <th>描述</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($permissions as $permission):?>
        <tr data_id="<?=$permission->name?>">
            <td><?=$permission->name?></td>
            <td><?=$permission->description?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['rbac/edit','name'=>$permission->name])?>" class="btn btn-warning">修改</a>
                <a href="javascript:;" class="btn btn-danger del" >删除</a>
            </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

<?php
/**
 * @var $this \yii\web\View
 *///注册js文件  依赖于jquery
$this->registerCssFile('@web/fenye/data/css/jquery.dataTables.css');

$this->registerJsFile('@web/fenye/data/js/jquery.dataTables.js',['depends'=>\yii\web\JqueryAsset::className()]);
    $url = yii\helpers\Url::to(['rbac/delete']);
    $this->registerJs(new \yii\web\JsExpression(
            <<<JS
              $(document).ready( function () {
                 $('#table_id_example').DataTable(
                 
                 )
          } );
            $('.del').click(function() {
                if(confirm('你确定要删除吗')){
                  var tr = $(this).closest('tr');
                  var name = tr.attr('data_id');
                  $.post("{$url}",{name:name},function(data) {
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
            });

        
JS

    ));

