<?php

use yii\db\Migration;

/**
 * Handles the creation of table `admin`.
 */
class m170913_044642_create_admin_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('admin', [
            'id' => $this->primaryKey(),
            'username'=>$this->string()->comment('用户名'),
            'password'=>$this->string()->comment('密码'),
            'auth_key'=>$this->string()->comment(''),
            'password_reset'=>$this->string()->comment('重置密码'),
            'email'=>$this->string()->comment('邮箱'),
            'status'=>$this->smallInteger()->notNull()->comment('状态'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'last_login_time'=>$this->integer()->comment('最后登录时间'),
            'last_login_ip'=>$this->string()->comment('最后登录ip')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('admin');
    }
}
