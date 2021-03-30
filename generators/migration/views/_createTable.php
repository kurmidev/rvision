<?php

/**
 * Creates a call for the method `yii\db\Migration::createTable()`.
 */
/* @var $table string the name table */
/* @var $fields array the fields */
/* @var $foreignKeys array the foreign keys */

?>        $this->createTable($this->tableName, [
<?php foreach ($fields as $field):
    if (empty($field['decorators'])): ?>
            '<?= $field['property'] ?>',
<?php else: ?>
            <?= "'{$field['property']}' => \$this->{$field['decorators']}" ?>,
            'name' => $this->string(255)->notNull()->unique(),
            'code' => $this->string(50)->notNull()->unique(),
             /**/
            'description' => $this->string(1000)->null()->defaultExpression('null'),
            'status' => $this->integer(2)->notNull()->defaultValue(app\models\Status::ACTIVE),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->null(),
            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull()->defaultValue(0)
<?php endif;
endforeach; ?>
        ]);
<?= $this->render('_addForeignKeys', [
    'table' => $table,
    'foreignKeys' => $foreignKeys,
]);
