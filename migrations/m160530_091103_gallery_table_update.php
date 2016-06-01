<?php

use yii\db\Migration;
use yii\db\Schema;

class m160530_091103_gallery_table_update extends Migration
{
    public function up() {
        $this->addColumn('{{%gallery}}', 'group_name', Schema::TYPE_STRING.' NULL');
    }

    public function down() {
        $this->dropColumn('{{%gallery}}', 'group_name');
    }
}
