<?php
?>

<div><a href="<?=\yii\helpers\Url::to(['article/index'])?>" class="btn btn-success">文章列表</a></div>
<hr>
<div><h3>名称:</h3><p>&emsp;&emsp;<?=$model->name?></p></div>
<hr>
<div><h3>添加时间:</h3><p>&emsp;&emsp;<?=date("Y-m-d H:i:s",$model->create_time)?></p></div>
<hr>
<div><h3>内容:</h3><p></p><pre><?=$model_d->content?></pre></div>

