<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_gallery`.
 */
class m170911_025607_create_goods_gallery_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods_gallery', [
            'id' => $this->primaryKey(),
/*字段名	类型	注释
id	primaryKey
goods_id	int	商品id
path	varchar(255)	图片地址*/
            'goods_id'=>$this->integer()->comment('商品id'),
            'path'=>$this->string(255)->comment('图片地址')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods_gallery');
    }
}
