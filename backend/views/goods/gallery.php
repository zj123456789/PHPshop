<?php
echo "<a href='index'class='btn btn-success'>返回列表</a>";
echo "<hr>";
echo \yii\helpers\Html::fileInput('test',null,['id'=>'test']);
echo \flyok666\uploadifive\Uploadifive::widget([
    'url' => yii\helpers\Url::to(['s-gallery']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['goods_id' =>$id],//上传文件的同时传参
        'width' => 120,//按钮宽度
        'height' => 40,
        'onError' => new \yii\web\JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadComplete' => new \yii\web\JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        console.log(data.fileUrl);
                //将处理好的图片放入表单一起提交
               // $('#goods-logo').val(data.fileUrl);
                //用户选择图片后回显在页面
               // $('#img').attr('src',data.fileUrl);
                 var html='<tr data-id="'+data.id+'">';
                    html += '<td><img src="'+data.fileUrl+'" style="width: 50px;height: 50px"/></td>';
                    html += '<td><button type="button" class="btn btn-danger del_btn">删除</button></td>';
                    html += '</tr>';
                 $("table").append(html);
            }
        }
EOF
        )
    ]
]);

?>

<table class="table">
    <tr>
        <th>图片</th>
        <th>操作</th>
    </tr>
    <?php foreach($gallerys as $goods):?>
        <tr data-id="<?=$goods->id?>">
            <td><?=\yii\helpers\Html::img($goods->path)?></td>
            <td><?=\yii\helpers\Html::button('删除',['class'=>'btn btn-danger del_btn'])?></td>
        </tr>
    <?php endforeach;?>
</table>

<?php
/**
 * @var $this \yii\web\View
 */
$url = \yii\helpers\Url::to(['del-gallery']);
$this->registerJs(new \yii\web\JsExpression(
        <<<JS
        $('table').on('click','.del_btn',function() {
            if(confirm('你确定删除吗')){
                var tr = $(this).closest('tr');
                var id = tr.attr('data-id');
                $.post("{$url}",{id:id},function(data) {
                    console.debug(data);
                if(data == 'true'){
                    alert('删除成功');
                    tr.hide('slow');
                }else{
                    alert('删除失败');
                }
                });
            }
        })
JS

));