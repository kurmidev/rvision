<?php
/**
 * This is the template for generating the model class of a specified table.
 */
/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;
//use yii\behaviors\TimestampBehavior;
use app\models\User;

/**
* This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
*
<?php
$columnsArr = [];
foreach ($tableSchema->columns as $column):
    ?>
    * @property <?= "{$column->phpType} \${$column->name}\n" ?>
    <?php $columnsArr[] = $column->name ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
    *
    <?php foreach ($relations as $name => $relation): ?>
        * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
    <?php endforeach; ?>
<?php endif; ?>
*/
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>{
/**
* @inheritdoc
*/
public static function tableName(){
return '<?= $generator->generateTableName($tableName) ?>';
}
<?php if ($generator->db !== 'db'): ?>

    /**
    * @return \yii\db\Connection the database connection used by this AR class.
    */
    public static function getDb(){
    return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>


public function scenarios() {

return [
self::SCENARIO_DEFAULT => ['*'], // Also tried without this line
self::SCENARIO_CREATE => [],
self::SCENARIO_CONSOLE => ['<?= implode("','", $columnsArr) ?>'],
self::SCENARIO_UPDATE => [],
];
}

/**
* @inheritdoc
*/
public function beforeSave($insert) {       

return parent::beforeSave($insert);
}

/**
* @inheritdoc
*/
public function afterSave($insert, $changedAttributes) {

 parent::afterSave($insert, $changedAttributes);
}

/**
* @inheritdoc
*/
public function rules(){
return [<?= "\n            " . implode(",\n            ", $rules) . ",\n        " ?>];
}

<?php foreach ($relations as $name => $relation): ?>

    /**
    * @return \yii\db\ActiveQuery
    */
    public function get<?= $name ?>() {
    <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
    /**
     * with
     * @return type
     */
    function defaultWith() {
        return [];
    }

    static function extraFieldsWithConf() {
        $retun = parent::extraFieldsWithConf();
        <?php if (isset($labels['operator_id'])): ?>
            $retun['operator_lbl'] = 'operator';
        <?php endif; ?>
            <?php if (isset($labels['location_id'])): ?>
            $retun['location_lbl'] = 'location';
        <?php endif; ?>
            <?php if (isset($labels['sublocation_id'])): ?>
            $retun['sublocation_lbl'] = 'sublocation';
        <?php endif; ?>
        return $retun;
    }
/**
* @inheritdoc
*/
public function fields(){
$fields= [
<?php foreach ($labels as $name => $label): ?>
    <?= "'$name' ,\n" ?>
<?php endforeach; ?>
];

    $fields = array_merge(parent::fields(), $fields);
    return $this->getFields($fields);
}
/**
* @inheritdoc
*/
public function extraFields(){
$fields = parent::extraFields();

<?php if (isset($labels['operator_id'])): ?>
    $fields['operator_lbl'] = function() {
    return $this->operator?$this->operator->name:null;
    };
<?php endif; ?>
<?php if (isset($labels['location_id'])): ?>
    $fields['location_lbl'] = function() {
    return $this->location?$this->location->name:null;
    
    };
<?php endif; ?>
<?php if (isset($labels['sublocation_id'])): ?>
    $fields['sublocation_lbl'] = function() {
    return $this->sublocation?$this->sublocation->name:null;
    };
<?php endif; ?>
        return $this->getFilterExtraFields($fields);
}
<?php if ($queryClassName): ?>
    <?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
    ?>
    /**
    * @inheritdoc
    * @return <?= $queryClassFullName ?> the active query used by this AR class.
    */
    /* public static function find(){
    return new <?= $queryClassFullName ?>(get_called_class())->applycache();
    }
    */
<?php endif; ?>
}