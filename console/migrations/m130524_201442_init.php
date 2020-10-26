<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $this->createIndex('client_uid', '{{%users}}', 'client_uid');
        $this->createIndex('partner_id', '{{%users}}', 'partner_id');
        $this->createIndex('client_uid', '{{%accounts}}', 'client_uid');
        $this->createIndex('login', '{{%accounts}}', 'login');
        $this->createIndex('login', '{{%trades}}', 'login');
        $this->createIndex('open_time', '{{%trades}}', 'open_time');
        $this->createIndex('close_time', '{{%trades}}', 'close_time');
    }

    public function down()
    {
        $this->dropIndex('client_uid', '{{%users}}');
        $this->dropIndex('partner_id', '{{%users}}');
        $this->dropIndex('client_uid', '{{%accounts}}');
        $this->dropIndex('login', '{{%accounts}}');
        $this->dropIndex('login', '{{%trades}}');
        $this->dropIndex('open_time', '{{%trades}}');
        $this->dropIndex('close_time', '{{%trades}}');
    }
}
