<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cart`.
 */
class m170921_075459_create_cart_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('cart', [
            'id' => $this->primaryKey(),
            'goods_id'=>$this->integer()->comment('商品id'),
            'amount'=>$this->integer()->comment('商品数量'),
            'member_id'=>$this->integer()->comment('用户id')
            /*       字段名	类型	注释
id	primaryKey
goods_id	int	商品id
amount	int	商品数量
member_id	int	用户id*/
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('cart');


    }
}
