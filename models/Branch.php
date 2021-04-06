<?php

namespace app\models;

use Yii;
//use yii\behaviors\TimestampBehavior;
use app\models\User;

/**
 * This is the model class for table "VI_BRANCH".
 *
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property string $phoneno
 * @property string $branch_incharge
 * @property string $mobileno
 * @property string $faxno
 * @property integer $status
 * @property string $created_on
 * @property integer $created_by
 * @property string $updated_on
 * @property integer $updated_by
 * @property integer $deleted
 * @property string $remark
 */
class Branch extends \app\models\BaseModel {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'VI_BRANCH';
    }

    public function scenarios() {

        return [
            self::SCENARIO_DEFAULT => ['*'], // Also tried without this line
            self::SCENARIO_CREATE => ['name', 'address', 'phoneno', 'branch_incharge', 'mobileno', 'faxno', 'status', 'remark'],
            self::SCENARIO_CONSOLE => ['id', 'name', 'address', 'phoneno', 'branch_incharge', 'mobileno', 'faxno', 'status', 'created_on', 'created_by', 'updated_on', 'updated_by', 'deleted', 'remark'],
            self::SCENARIO_UPDATE => ['name', 'address', 'phoneno', 'branch_incharge', 'mobileno', 'faxno', 'status', 'remark'],
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
            [['name', 'address', 'mobileno'], 'required'],
            [['status', 'created_by', 'updated_by', 'deleted'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['name', 'branch_incharge'], 'string', 'max' => 150],
            [['address'], 'string', 'max' => 550],
            [['phoneno', 'mobileno'], 'string', 'max' => 15],
            [['faxno'], 'string', 'max' => 20],
            [['remark'], 'string', 'max' => 250],
            [['name'], 'unique'],
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
            'name',
            'address',
            'phoneno',
            'branch_incharge',
            'mobileno',
            'faxno',
            'status',
            'created_on',
            'created_by',
            'updated_on',
            'updated_by',
            'deleted',
            'remark',
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

    /**
     * @inheritdoc
     * @return BranchQuery the active query used by this AR class.
     */
    /* public static function find(){
      return new BranchQuery(get_called_class())->applycache();
      }
     */
}
