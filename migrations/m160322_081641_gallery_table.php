<?php

use yii\db\Migration;

class m160322_081641_gallery_table extends Migration
{
    public $tableName = '{{%gallery}}';

    public function up()
    {
        $this->createTable(
            $this->tableName,
            array(
                'id' => $this->primaryKey(),
                'sid' => $this->string().' NOT NULL',
                'active' => $this->boolean().' NOT NULL',
                'name' => $this->string().' NOT NULL',
                'description' => $this->text(),
            )
        );
        $this->insert($this->tableName, [
            'sid' => 'osnovnoj-albom',
            'name' => 'Основной альбом',
            'description' => 'Самые любимые фотки здесь!',
        ]);
    }

    public function down()
    {
        $this->dropTable($this->tableName);
    }
}
