<?php

use yii\db\Migration;

class m160324_081605_gallery_image_table extends Migration
{
    public $tableName = '{{%gallery_image}}';

    public function up()
    {
        $this->createTable(
            $this->tableName,
            array(
                'id' => $this->primaryKey(),
                'type' => $this->string(),
                'ownerId' => $this->integer() . ' NOT NULL',
                'rank' => $this->integer() . ' NOT NULL DEFAULT 0',
                'name' => $this->string(),
                'description' => $this->text(),
                'liked' => $this->integer() . ' NOT NULL DEFAULT 0',
            )
        );
        $this->addForeignKey('FK_gallery_image_owner', '{{%gallery_image}}', 'ownerId', '{{%gallery}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable($this->tableName);
    }
}
