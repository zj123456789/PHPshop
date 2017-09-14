
<style>
    .sub{
        margin-bottom: 11px;
    }
</style>
<div><a href="<?=\yii\helpers\Url::to(['admin/add'])?>" class="btn btn-success">添加管理员</a></div>
<br>


<br>
<table class="table table-bordered table-responsive">
    <tr>
        <th>ID</th>
        <th>管理员</th>
        <th>邮箱</th>
        <th>状态</th>
        <th>创建时间</th>
        <th>修改时间</th>
        <th>最后登录时间</th>
        <th>最后登录IP</th>
        <th>操作</th>
    </tr>
    <?php foreach ($admins as $admin):?>
        <tr data_id="<?=$admin->id?>">
            <td><?=$admin->id?></td>
            <td><?=$admin->username?></td>
            <td><?=$admin->email?></td>
            <td><?php
                if($admin->status==1){
                    echo '正常';
                }else {
                    echo '隐藏';
                }?></td>
            <td><?=date('Y-m-d H:i:s',$admin->created_at)?></td>
            <td><?=date('Y-m-d H:i:s',$admin->updated_at)?></td>
            <td><?=date('Y-m-d H:i:s',$admin->last_login_time)?></td>
            <td><?=$admin->last_login_ip?></td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['admin/edit','id'=>$admin->id])?>" class="btn btn-warning">修改</a>
                <a href="javascript:;" class="btn btn-danger del" >删除</a>
            </td>
        </tr>
    <?php endforeach;?>
</table>

<?php
/**
 * @var $this \yii\web\View
 */
    $url = yii\helpers\Url::to(['admin/delete']);
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
