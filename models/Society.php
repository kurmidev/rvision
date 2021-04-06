<?php

namespace app\models;

use Yii;
//use yii\behaviors\TimestampBehavior;
use app\components\Constants as C;

/**
 * This is the model class for table "VI_SOCIETY_MASTER".
 *
 * @property integer $id
 * @property string $society_code
 * @property string $society_name
 * @property integer $area_id
 * @property integer $status
 * @property string $remark
 * @property integer $sms_id
 * @property string $created_on
 * @property integer $created_by
 * @property string $updated_on
 * @property integer $updated_by
 * @property integer $deleted
 *
 * @property VIAREACODE $area
 * @property VISUBSCRIBERS[] $vISUBSCRIBERSs
 */
class Society extends \app\models\BaseModel {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'VI_SOCIETY_MASTER';
    }

    public function scenarios() {

        return [
            self::SCENARIO_DEFAULT => ['*'], // Also tried without this line
            self::SCENARIO_CREATE => ['society_code', 'society_name', 'area_id', 'status', 'remark', 'sms_id'],
            self::SCENARIO_CONSOLE => ['id', 'society_code', 'society_name', 'area_id', 'status', 'remark', 'sms_id', 'created_on', 'created_by', 'updated_on', 'updated_by', 'deleted'],
            self::SCENARIO_UPDATE => ['society_name', 'area_id', 'status', 'remark', 'sms_id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if ($insert) {
            $this->society_code = !empty($this->society_code) ? $this->society_code : $this->generateCode(C::PFX_SOCIETY);
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
            [['society_name', 'area_id'], 'required'],
            [['area_id', 'sms_id', 'created_by', 'updated_by', 'status', 'deleted'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['society_code'], 'string', 'max' => 30],
            [['society_name'], 'string', 'max' => 150],
            [['remark'], 'string', 'max' => 250],
            [['area_id', 'society_code'], 'unique', 'targetAttribute' => ['area_id', 'society_code'], 'message' => 'The combination of Society Code and Area ID has already been taken.'],
            [['area_id', 'society_name'], 'unique', 'targetAttribute' => ['area_id', 'society_name'], 'message' => 'The combination of Society Name and Area ID has already been taken.'],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Area::className(), 'targetAttribute' => ['area_id' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArea() {
        return $this->hasOne(Area::className(), ['id' => 'area_id']);
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
        $retun['area_lbl'] = 'area';
        $retun['area_code_lbl'] = 'area';
        $retun['operator_lbl'] = 'area.operator';
        $retun['operator_code_lbl'] = 'area.operator';

        return $retun;
    }

    /**
     * @inheritdoc
     */
    public function fields() {
        $fields = [
            'id',
            'society_code',
            'society_name',
            'area_id',
            'status',
            'remark',
            'sms_id',
            'created_on',
            'created_by',
            'updated_on',
            'updated_by',
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

        $fields['area_lbl'] = function () {
            return $this->area ? $this->area->name : null;
        };

        $fields['area_code_lbl'] = function () {
            return $this->area ? $this->area->area_code : null;
        };

        $fields['operator_lbl'] = function () {
            return !empty($this->area->operator) ? $this->area->operator->name : null;
        };

        $fields['operator_code_lbl'] = function () {
            return !empty($this->area->operator) ? $this->area->operator->code : null;
        };
        return $this->getFilterExtraFields($fields);
    }

    /**
     * @inheritdoc
     * @return SocietyQuery the active query used by this AR class.
     */
    /* public static function find(){
      return new SocietyQuery(get_called_class())->applycache();
      }
     */
}
