<?php

namespace app\models;

use Yii;
//use yii\behaviors\TimestampBehavior;
use app\components\helpers\Utils as U;

/**
 * This is the model class for table "code_sequence".
 *
 * @property integer $id
 * @property string $prefix
 * @property integer $counter
 * @property string $created_on
 * @property string $updated_on
 * @property integer $created_at
 * @property integer $updated_at
 */
class CodeSequence extends \app\models\BaseModel {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'code_sequence';
    }

    public function scenarios() {

        return [
            self::SCENARIO_DEFAULT => ['*'], // Also tried without this line
            self::SCENARIO_CREATE => ['prefix', 'counter'],
            self::SCENARIO_CONSOLE => ['id', 'prefix', 'counter', 'created_on', 'updated_on', 'created_at', 'updated_at'],
            self::SCENARIO_UPDATE => ['prefix', 'counter'],
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
    public function rules() {
        return [
            [['counter', 'created_at', 'updated_at'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['prefix'], 'string', 'max' => 255],
        ];
    }

    /**
     * with
     * @return type
     */
    function defaultWith() {
        return [];
    }

    static function extraFieldsWithConf() {
        $retun = parent::extraFieldsWithConf();
        return $retun;
    }

    /**
     * @inheritdoc
     */
    public function fields() {
        $fields = [
            'id',
            'prefix',
            'counter',
            'created_on',
            'updated_on',
            'created_by',
            'updated_by',
        ];

        $fields = array_merge(parent::fields(), $fields);
        return $this->getFields($fields);
    }

    /**
     * @inheritdoc
     */
    public function extraFields() {
        $fields = parent::extraFields();

        return $this->getFilterExtraFields($fields);
    }

    public static function genCode($prefix, $is_fy = false) {
        $model = CodeSequence::findOne(['prefix' => $prefix]);
        if (!$model instanceof CodeSequence) {
            $model = new CodeSequence(['scenario' => CodeSequence::SCENARIO_CREATE]);
            $model->prefix = $prefix;
            $model->counter = 1;
            if ($model->validate()) {
                $model->save();
            }
        }
        CodeSequence::updateAllCounters(['counter' => 1], ['prefix' => $model->prefix]);
        $fy = $is_fy ? "-" . U::getFinancialYear() . "-" : "-";
        return implode($fy, [$prefix, $model->counter]);
    }

    /**
     * @inheritdoc
     * @return CodeSequenceQuery the active query used by this AR class.
     */
    /* public static function find(){
      return new CodeSequenceQuery(get_called_class())->applycache();
      }
     */
}
