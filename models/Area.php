<?php

namespace app\models;

use Yii;
use app\components\Constants as C;

/**
 * This is the model class for table "VI_AREACODE".
 *
 * @property integer $id
 * @property string $area_code
 * @property string $name
 * @property integer $status
 * @property integer $operator_id
 * @property string $created_on
 * @property string $updated_on
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $remark
 * @property integer $deleted
 *
 * @property VISOCIETYMASTER[] $vISOCIETYMASTERs
 */
class Area extends \app\models\BaseModel {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'VI_AREACODE';
    }

    public function scenarios() {

        return [
            self::SCENARIO_DEFAULT => ['*'], // Also tried without this line
            self::SCENARIO_CREATE => ['name', 'area_code', 'status', 'operator_id', 'remark'],
            self::SCENARIO_CONSOLE => ['id', 'area_code', 'name', 'status', 'operator_id', 'created_on', 'updated_on', 'created_by', 'updated_by', 'remark', 'deleted'],
            self::SCENARIO_UPDATE => ['name', 'status', 'operator_id', 'remark', 'deleted'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if ($insert) {
            $this->area_code = !empty($this->area_code) ? $this->area_code : $this->generateCode(C::PFX_AREA);
        }
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
            [['name', 'operator_id', 'status'], 'required'],
            [['operator_id', 'created_by', 'updated_by', 'status', 'deleted'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['area_code'], 'string', 'max' => 30],
            [['name'], 'string', 'max' => 200],
            [['remark'], 'string', 'max' => 250],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSociety() {
        return $this->hasMany(Society::className(), ['area_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperator() {
        return $this->hasOne(Operator::className(), ['id' => 'operator_id']);
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
        $retun['operator_lbl'] = 'operator';
        return $retun;
    }

    /**
     * @inheritdoc
     */
    public function fields() {
        $fields = [
            'id',
            'area_code',
            'name',
            'status',
            'operator_id',
            'created_on',
            'updated_on',
            'created_by',
            'updated_by',
            'remark',
            'deleted',
        ];

        $fields = array_merge(parent::fields(), $fields);
        return $this->getFields($fields);
    }

    /**
     * @inheritdoc
     */
    public function extraFields() {
        $fields = parent::extraFields();

        $fields['operator_lbl'] = function () {
            return $this->operator ? $this->operator->name : null;
        };

        $fields['operator_code_lbl'] = function () {
            return $this->operator ? $this->operator->code : null;
        };

        return $this->getFilterExtraFields($fields);
    }

}
