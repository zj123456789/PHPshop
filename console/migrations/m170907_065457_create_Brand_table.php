<?php

use yii\db\Migration;

/**
 * Handles the creation of table `Brand`.
 */
class m170907_065457_create_Brand_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('Brand', [
            'id' => $this->primaryKey(),
/*            id	primaryKey
name	varchar(50)	名称
intro	text	简介
logo	varchar(255)	LOGO图片
sort	int(11)	排序
status	int(2)	状态(-1删除 0隐藏 1正常)*/
            'name'=>$this->string(50)->comment('品牌'),
            'intro'=>$this->text()->comment('简介'),
            'logo'=>$this->string()->comment('logo图片'),
            'sort'=>$this->integer()->comment('排序'),
            'status'=>$this->smallInteger(2)->comment('状态')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('Brand');
    }
}
