<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%code_sequence}}`.
 */
class m210403_121942_create_code_sequence_table extends Migration {

    public $table = "code_sequence";

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->createTable($this->table, [
            'id' => $this->primaryKey(),
            "prefix" => $this->string(),
            "counter" => $this->integer(),
            "created_on" => $this->dateTime(),
            "updated_on" => $this->dateTime(),
            "created_by" => $this->integer(),
            "updated_by" => $this->integer()
        ]);

        $this->createIndex("{$this->table}-prefix", $this->table, ['prefix']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropTable($this->table);
    }

}
