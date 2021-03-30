<?php

/**
 * This is the template for generating CRUD search class of the specified model.
 */
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$modelClass = StringHelper::basename($generator->modelClass);
if ($generator->ns !== $generator->searchNs) {
    $modelClass = '\\' . $generator->ns . '\\' . $modelClass;
}
$searchClass = StringHelper::basename($generator->searchClass);
if ($modelClass === $searchClass) {
    $modelAlias = $modelClass . 'Model';
}
$rules = $generator->generateSearchRules();
$labels = $generator->generateSearchLabels();
$searchAttributes = $generator->getSearchAttributes();
$rangeAttributes = $generator->getRangeAttributes();
$searchConditions = $generator->generateSearchConditions();
$searchConditionsNot = $generator->generateSearchConditions(true);


echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->searchClass, '\\')) ?>;

use Yii;
use yii\base\Model;
use <?= ltrim($generator->modelClass, '\\') . (isset($modelAlias) ? " as $modelAlias" : "") ?>;
use \components\helper\ArrayHelper;
/**
<?php
$columns = [];
foreach ($rangeAttributes as $column => $d):
    ?>
    * @property <?= "{$d['type']} \${$d['from']}\n" ?>
    * @property <?= "{$d['type']} \${$d['to']}\n" ?>
<?php $columns[] = $d['from'];$columns[] = $d['to']; ?>
<?php endforeach; ?>
* <?= $searchClass ?> represents the model behind the search form about `<?= $generator->modelClass ?>`.
*/
class <?= $searchClass ?> extends <?= isset($modelAlias) ? $modelAlias : $modelClass ?>

{
<?php
foreach ($columns as $column):
    ?>
    public  <?= " \${$column};\n" ?>
<?php endforeach; ?>
   
  /**
   * additional range attributes
   */
    public function attributes() {
        $arributes = parent::attributes();
<?php
foreach ($columns as $column):
    ?>
          $arributes[]=<?= "'{$column}';\n" ?>
<?php endforeach; ?>
        return $arributes;
    }

/**
* @inheritdoc
*/
public function rules()
{
return [
<?= implode(",\n            ", $rules) ?>,
];
}
public function beforeValidate() {
// bypass scenarios() implementation in the parent model

$p = get_parent_class(get_parent_class());

return $p::beforeValidate();
}
/**
* @inheritdoc
*/
public function scenarios()
{
// bypass scenarios() implementation in the parent class
return Model::scenarios();
}

/**
* Creates data provider instance with search query applied
*
* @param array $params
*
* @return ActiveDataProvider
*/
public function search($params,$notparams = null,$extra=[])
{
$query = <?= isset($modelAlias) ? $modelAlias : $modelClass ?>::find()->defaultScope(['self' => true]);
  $query->with($this->getSearchWith());

// add conditions that should always apply here
 $default = $this->attributes;
$this->load($params,'');

if (!$this->validate()) {
// uncomment the following line if you do not want to return any records when validation fails
throw new \yii\web\HttpException(422, json_encode($this->errors));

//          return $query;
}



// grid filtering conditions
<?= implode("\n        ", $generator->getZerotoNullSearchCondition()) ?>
<?= implode("\n        ", $searchConditions) ?>

if ($notparams) {

            $this->load(array_merge($default, $notparams), '');
            if (!$this->validate()) {
                // uncomment the following line if you do not want to return any records when validation fails
                throw new \yii\web\HttpException(422, json_encode($this->errors));

                //          return $query;
            }
            // grid filtering conditions
<?= implode("\n        ", $generator->getZerotoNullSearchCondition(true)) ?>
<?= implode("\n        ", $searchConditionsNot) ?>
   }
return $query;
}
}
