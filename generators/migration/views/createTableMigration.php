<?php
/**
 * This view is used by console/controllers/MigrateController.php.
 *
 * The following variables are available in this view:
 */
/* @var $className string the new migration class name without namespace */
/* @var $namespace string the new migration class namespace */
/* @var $table string the name table */
/* @var $tableComment string the comment table */
/* @var $fields array the fields */
/* @var $foreignKeys array the foreign keys */

echo "<?php\n";
if (!empty($namespace)) {
    echo "\nnamespace {$namespace};\n";
}
?>

use yii\db\Migration;

/**
 * Handles the creation of table `<?= $table ?>`.
<?= $this->render('_foreignTables', [
    'foreignKeys' => $foreignKeys,
]) ?>
 */
class <?= $className ?> extends  \components\migration\Migration
{
public $tableName='<?=$table?>';
public $modelName='<?=formatModelName($table)?>';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
<?= $this->render('_createTable', [
    'table' => $table,
    'fields' => $fields,
    'foreignKeys' => $foreignKeys,
])
?>
     $this->createIndex(
                'idx-' . $this->tableName . '-status', $this->tableName, ['status']
        );
    /* $this->createIndex(
                'iux-' . $this->tableName . '-type', $this->tableName, ['type'],1
        );
     */
    /*
        $this->addForeignKey(
                'fk-' . $this->tableName . '-cas_id', $this->tableName, 'cas_id', 'cas_vendor', 'id', 'CASCADE'
        );
     */   
        $this->createPermission();
        
<?php if (!empty($tableComment)) {
    echo $this->render('_addComments', [
        'table' => $table,
        'tableComment' => $tableComment,
    ]);
}
?>
    }
    public function callcreatePermission() {
        $this->createPermission();
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->removePermission();
        $this->dropTableForcefully();
    }
}
<?php
function formatModelName($tn) {
        $t = explode('_', $tn);
        $ret='';
        foreach($t as $v){
            $ret.= ucwords($v);
        }
        return $ret;
    }
   ?>