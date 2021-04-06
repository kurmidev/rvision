<?php

namespace app\models;

use Yii;
use app\components\Constants as C;

/**
 * This is the model class for table "VI_OPERATOR".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property string $contact_person
 * @property integer $parent_id
 * @property string $address
 * @property integer $mobileno
 * @property string $email
 * @property string $phoneno
 * @property integer $status
 * @property string $created_on
 * @property string $updated_on
 * @property integer $created_by
 * @property integer $updated_by
 * @property string $login_id
 * @property string $password
 * @property integer $deleted
 * @property string $remark
 * @property integer $operator_type
 * @property integer $branch_id
 * @property string $pan_no
 * @property string $service_tax_no
 * @property string $gst_no
 * @property string $tin_no
 *
 * @property VISUBSCRIBERS[] $vISUBSCRIBERSs
 */
class Operator extends \app\models\BaseModel {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'VI_OPERATOR';
    }

    public function scenarios() {

        return [
            self::SCENARIO_DEFAULT => ['*'], // Also tried without this line
            self::SCENARIO_CREATE => ['name', 'code', 'contact_person', 'parent_id', 'address', 'mobileno', 'email', 'phoneno', 'status', 'remark', 'operator_type', 'branch_id', 'pan_no', 'service_tax_no', 'gst_no', 'tin_no','login_id','password'],
            self::SCENARIO_CONSOLE => ['id', 'name', 'code', 'contact_person', 'parent_id', 'address', 'mobileno', 'email', 'phoneno', 'status', 'created_on', 'updated_on', 'created_by', 'updated_by', 'login_id', 'password', 'deleted', 'remark', 'operator_type', 'branch_id', 'pan_no', 'service_tax_no', 'gst_no', 'tin_no'],
            self::SCENARIO_UPDATE => ['name', 'contact_person', 'parent_id', 'address', 'mobileno', 'email', 'phoneno', 'status', 'remark', 'branch_id', 'pan_no', 'service_tax_no', 'gst_no', 'tin_no'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if ($insert) {
            $prefix = $this->operator_type == C::USER_TYPE_MSO ? C::PFX_OPT_MSO : (
                    $this->operator_type == C::USER_TYPE_DISTRIBUTOR ? C::PFX_OPT_DISTRIBUTOR : C::PFX_OPT_OPERATOR);
            $this->code = !empty($this->code) ? $this->code : $this->generateCode($prefix);
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
            [['name', 'mobileno', 'login_id', 'status', 'contact_person', 'operator_type', 'branch_id'], 'required'],
            [['parent_id', 'mobileno', 'status', 'created_by', 'updated_by', 'deleted', 'operator_type', 'branch_id'], 'integer'],
            [['created_on', 'updated_on'], 'safe'],
            [['name'], 'string', 'max' => 150],
            [['code'], 'string', 'max' => 30],
            [['contact_person'], 'string', 'max' => 100],
            [['address', 'password'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 60],
            [['phoneno', 'pan_no'], 'string', 'max' => 20],
            [['login_id'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 250],
            [['service_tax_no', 'gst_no', 'tin_no'], 'string', 'max' => 25],
            ['login_id', 'unique', 'targetClass' => ViAccess::class, 'targetAttribute' => 'login_id'],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMso() {
        return self::findOne(['operator_type' => C::USER_TYPE_MSO]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDistributor() {
        return $this->hasOne(Operator::className(), ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccess() {
        return $this->hasOne(ViAccess::className(), ['login_id' => 'login_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranch() {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubscriber() {
        return $this->hasMany(Subscriber::className(), ['operator_id' => 'id']);
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
            'code',
            'contact_person',
            'parent_id',
            'address',
            'mobileno',
            'email',
            'phoneno',
            'branch_id',
            'status',
            'operator_type',
            'pan_no',
            'service_tax_no',
            'login_id',
            'gst_no',
            'tin_no',
            'remark',
            'password',
            'deleted',
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

        $fields['branch_lbl'] = function () {
            return !empty($this->branch) ? $this->branch->name : "";
        };
        $fields['distributor_code_lbl'] = function () {
            return !empty($this->distributor) ? $this->distributor->code : "";
        };
        $fields['distributor_lbl'] = function () {
            return !empty($this->distributor) ? $this->distributor->name : "";
        };

        return $this->getFilterExtraFields($fields);
    }

}
