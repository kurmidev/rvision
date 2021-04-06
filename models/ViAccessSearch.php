<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\ViAccess;
use \components\helper\ArrayHelper;

/**
 * @property bigint $FRM_id
 * @property bigint $TO_id
 * @property smallint $FRM_status
 * @property smallint $TO_status
 * @property datetime $FRM_created_on
 * @property datetime $TO_created_on
 * @property datetime $FRM_updated_on
 * @property datetime $TO_updated_on
 * @property bigint $FRM_created_by
 * @property bigint $TO_created_by
 * @property bigint $FRM_updated_by
 * @property bigint $TO_updated_by
 * @property bigint $FRM_operator_id
 * @property bigint $TO_operator_id
 * @property smallint $FRM_deleted
 * @property smallint $TO_deleted
 * @property bigint $FRM_sms_id
 * @property bigint $TO_sms_id
 * @property integer $FRM_access_token_expired_at
 * @property integer $TO_access_token_expired_at
 * @property integer $FRM_confirmed_at
 * @property integer $TO_confirmed_at
 * @property integer $FRM_last_login_at
 * @property integer $TO_last_login_at
 * @property integer $FRM_blocked_at
 * @property integer $TO_blocked_at
 * @property integer $FRM_role
 * @property integer $TO_role
 * @property integer $FRM_created_at
 * @property integer $TO_created_at
 * @property integer $FRM_updated_at
 * @property integer $TO_updated_at
 * ViAccessSearch represents the model behind the search form about `app\models\ViAccess`.
 */
class ViAccessSearch extends ViAccess {

    use \traits\SearchTrait;

    public $FRM_id;
    public $TO_id;
    public $FRM_status;
    public $TO_status;
    public $FRM_created_on;
    public $TO_created_on;
    public $FRM_updated_on;
    public $TO_updated_on;
    public $FRM_created_by;
    public $TO_created_by;
    public $FRM_updated_by;
    public $TO_updated_by;
    public $FRM_operator_id;
    public $TO_operator_id;
    public $FRM_deleted;
    public $TO_deleted;
    public $FRM_sms_id;
    public $TO_sms_id;
    public $FRM_access_token_expired_at;
    public $TO_access_token_expired_at;
    public $FRM_confirmed_at;
    public $TO_confirmed_at;
    public $FRM_last_login_at;
    public $TO_last_login_at;
    public $FRM_blocked_at;
    public $TO_blocked_at;
    public $FRM_role;
    public $TO_role;
    public $FRM_created_at;
    public $TO_created_at;
    public $FRM_updated_at;
    public $TO_updated_at;

    /**
     * additional range attributes
     */
    public function attributes() {
        $arributes = parent::attributes();
        $arributes[] = 'FRM_id';
        $arributes[] = 'TO_id';
        $arributes[] = 'FRM_status';
        $arributes[] = 'TO_status';
        $arributes[] = 'FRM_created_on';
        $arributes[] = 'TO_created_on';
        $arributes[] = 'FRM_updated_on';
        $arributes[] = 'TO_updated_on';
        $arributes[] = 'FRM_created_by';
        $arributes[] = 'TO_created_by';
        $arributes[] = 'FRM_updated_by';
        $arributes[] = 'TO_updated_by';
        $arributes[] = 'FRM_operator_id';
        $arributes[] = 'TO_operator_id';
        $arributes[] = 'FRM_deleted';
        $arributes[] = 'TO_deleted';
        $arributes[] = 'FRM_sms_id';
        $arributes[] = 'TO_sms_id';
        $arributes[] = 'FRM_access_token_expired_at';
        $arributes[] = 'TO_access_token_expired_at';
        $arributes[] = 'FRM_confirmed_at';
        $arributes[] = 'TO_confirmed_at';
        $arributes[] = 'FRM_last_login_at';
        $arributes[] = 'TO_last_login_at';
        $arributes[] = 'FRM_blocked_at';
        $arributes[] = 'TO_blocked_at';
        $arributes[] = 'FRM_role';
        $arributes[] = 'TO_role';
        $arributes[] = 'FRM_created_at';
        $arributes[] = 'TO_created_at';
        $arributes[] = 'FRM_updated_at';
        $arributes[] = 'TO_updated_at';
        return $arributes;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'status', 'created_by', 'updated_by', 'operator_id', 'deleted', 'sms_id', 'access_token_expired_at', 'confirmed_at', 'last_login_at', 'blocked_at', 'role', 'created_at', 'updated_at'], 'customValidator', 'params' => ['function' => '\components\helper\ArrayHelper::isIntegerOr1dArray', 'message' => '{attribute} must in an integer or array of integer']],
            [['name', 'login_id', 'password', 'usertype', 'mobileno', 'created_on', 'updated_on', 'remark', 'authkey', 'password_hash', 'password_reset_token', 'email', 'unconfirmed_email', 'registration_ip', 'last_login_ip', 'FRM_created_on', 'TO_created_on', 'FRM_updated_on', 'TO_updated_on'], 'safe'],
            [['FRM_id', 'TO_id', 'FRM_status', 'TO_status', 'FRM_created_by', 'TO_created_by', 'FRM_updated_by', 'TO_updated_by', 'FRM_operator_id', 'TO_operator_id', 'FRM_deleted', 'TO_deleted', 'FRM_sms_id', 'TO_sms_id', 'FRM_access_token_expired_at', 'TO_access_token_expired_at', 'FRM_confirmed_at', 'TO_confirmed_at', 'FRM_last_login_at', 'TO_last_login_at', 'FRM_blocked_at', 'TO_blocked_at', 'FRM_role', 'TO_role', 'FRM_created_at', 'TO_created_at', 'FRM_updated_at', 'TO_updated_at'], 'integer'],
        ];
    }

    public function fileSupportedFields() {
        return [
                //  'smartcardno',
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params, $notparams = null, $extra = []) {
        $query = ViAccess::find();

        if ($this->thisalias) {
            $query->setAlias($this->thisalias);
        }
        $query->defaultScope(['self' => true]);

        if (!isset($extra['no_with'])) {
            $query->with($this->getSearchWith());
        }


// add conditions that should always apply here
        $default = $this->attributes;
        $this->load($params, '');
        $this->processFileSearch();
        if (!$this->validate()) {
// uncomment the following line if you do not want to return any records when validation fails
            throw new \yii\web\HttpException(422, json_encode($this->errors));

//          return $query;
        }



// grid filtering conditions
        $query->andFilterWhere([
            $query->alias . 'id' => $this->id,
            $query->alias . 'status' => $this->status,
            $query->alias . 'created_on' => $this->created_on,
            $query->alias . 'updated_on' => $this->updated_on,
            $query->alias . 'created_by' => $this->created_by,
            $query->alias . 'updated_by' => $this->updated_by,
            $query->alias . 'operator_id' => $this->operator_id,
            $query->alias . 'deleted' => $this->deleted,
            $query->alias . 'sms_id' => $this->sms_id,
            $query->alias . 'access_token_expired_at' => $this->access_token_expired_at,
            $query->alias . 'confirmed_at' => $this->confirmed_at,
            $query->alias . 'last_login_at' => $this->last_login_at,
            $query->alias . 'blocked_at' => $this->blocked_at,
            $query->alias . 'role' => $this->role,
            $query->alias . 'created_at' => $this->created_at,
            $query->alias . 'updated_at' => $this->updated_at,
        ]);

        $query->andArrayLike(['name' => $this->name], false)
                ->andArrayLike(['login_id' => $this->login_id], false)
                ->andArrayLike(['password' => $this->password], false)
                ->andArrayLike(['usertype' => $this->usertype], false)
                ->andArrayLike(['mobileno' => $this->mobileno], false)
                ->andArrayLike(['remark' => $this->remark], false)
                ->andArrayLike(['authkey' => $this->authkey], false)
                ->andArrayLike(['password_hash' => $this->password_hash], false)
                ->andArrayLike(['password_reset_token' => $this->password_reset_token], false)
                ->andArrayLike(['email' => $this->email], false)
                ->andArrayLike(['unconfirmed_email' => $this->unconfirmed_email], false)
                ->andArrayLike(['registration_ip' => $this->registration_ip], false)
                ->andArrayLike(['last_login_ip' => $this->last_login_ip], false);

        $query->andFilterWhere(['between', $query->alias . 'id', $this->FRM_id, $this->TO_id])
                ->andFilterWhere(['between', $query->alias . 'status', $this->FRM_status, $this->TO_status])
                ->andFilterWhere(['between', $query->alias . 'created_on', $this->FRM_created_on, $this->TO_created_on])
                ->andFilterWhere(['between', $query->alias . 'updated_on', $this->FRM_updated_on, $this->TO_updated_on])
                ->andFilterWhere(['between', $query->alias . 'created_by', $this->FRM_created_by, $this->TO_created_by])
                ->andFilterWhere(['between', $query->alias . 'updated_by', $this->FRM_updated_by, $this->TO_updated_by])
                ->andFilterWhere(['between', $query->alias . 'operator_id', $this->FRM_operator_id, $this->TO_operator_id])
                ->andFilterWhere(['between', $query->alias . 'deleted', $this->FRM_deleted, $this->TO_deleted])
                ->andFilterWhere(['between', $query->alias . 'sms_id', $this->FRM_sms_id, $this->TO_sms_id])
                ->andFilterWhere(['between', $query->alias . 'access_token_expired_at', $this->FRM_access_token_expired_at, $this->TO_access_token_expired_at])
                ->andFilterWhere(['between', $query->alias . 'confirmed_at', $this->FRM_confirmed_at, $this->TO_confirmed_at])
                ->andFilterWhere(['between', $query->alias . 'last_login_at', $this->FRM_last_login_at, $this->TO_last_login_at])
                ->andFilterWhere(['between', $query->alias . 'blocked_at', $this->FRM_blocked_at, $this->TO_blocked_at])
                ->andFilterWhere(['between', $query->alias . 'role', $this->FRM_role, $this->TO_role])
                ->andFilterWhere(['between', $query->alias . 'created_at', $this->FRM_created_at, $this->TO_created_at])
                ->andFilterWhere(['between', $query->alias . 'updated_at', $this->FRM_updated_at, $this->TO_updated_at]);

        if ($notparams) {

            $this->load(array_merge($default, $notparams), '');
            if (!$this->validate()) {
                // uncomment the following line if you do not want to return any records when validation fails
                throw new \yii\web\HttpException(422, json_encode($this->errors));

                //          return $query;
            }
            // grid filtering conditions
            $query->andArrayLike(['name' => $this->name], true)
                    ->andArrayLike(['login_id' => $this->login_id], true)
                    ->andArrayLike(['password' => $this->password], true)
                    ->andArrayLike(['usertype' => $this->usertype], true)
                    ->andArrayLike(['mobileno' => $this->mobileno], true)
                    ->andArrayLike(['remark' => $this->remark], true)
                    ->andArrayLike(['authkey' => $this->authkey], true)
                    ->andArrayLike(['password_hash' => $this->password_hash], true)
                    ->andArrayLike(['password_reset_token' => $this->password_reset_token], true)
                    ->andArrayLike(['email' => $this->email], true)
                    ->andArrayLike(['unconfirmed_email' => $this->unconfirmed_email], true)
                    ->andArrayLike(['registration_ip' => $this->registration_ip], true)
                    ->andArrayLike(['last_login_ip' => $this->last_login_ip], true);

            $query->andFilterWhere(['not in', $query->alias . 'id', $this->id])
                    ->andFilterWhere(['not in', $query->alias . 'status', $this->status])
                    ->andFilterWhere(['not in', $query->alias . 'created_on', $this->created_on])
                    ->andFilterWhere(['not in', $query->alias . 'updated_on', $this->updated_on])
                    ->andFilterWhere(['not in', $query->alias . 'created_by', $this->created_by])
                    ->andFilterWhere(['not in', $query->alias . 'updated_by', $this->updated_by])
                    ->andFilterWhere(['not in', $query->alias . 'operator_id', $this->operator_id])
                    ->andFilterWhere(['not in', $query->alias . 'deleted', $this->deleted])
                    ->andFilterWhere(['not in', $query->alias . 'sms_id', $this->sms_id])
                    ->andFilterWhere(['not in', $query->alias . 'access_token_expired_at', $this->access_token_expired_at])
                    ->andFilterWhere(['not in', $query->alias . 'confirmed_at', $this->confirmed_at])
                    ->andFilterWhere(['not in', $query->alias . 'last_login_at', $this->last_login_at])
                    ->andFilterWhere(['not in', $query->alias . 'blocked_at', $this->blocked_at])
                    ->andFilterWhere(['not in', $query->alias . 'role', $this->role])
                    ->andFilterWhere(['not in', $query->alias . 'created_at', $this->created_at])
                    ->andFilterWhere(['not in', $query->alias . 'updated_at', $this->updated_at]);

            $query->andFilterWhere(['not between', $query->alias . 'id', $this->FRM_id, $this->TO_id])
                    ->andFilterWhere(['not between', $query->alias . 'status', $this->FRM_status, $this->TO_status])
                    ->andFilterWhere(['not between', $query->alias . 'created_on', $this->FRM_created_on, $this->TO_created_on])
                    ->andFilterWhere(['not between', $query->alias . 'updated_on', $this->FRM_updated_on, $this->TO_updated_on])
                    ->andFilterWhere(['not between', $query->alias . 'created_by', $this->FRM_created_by, $this->TO_created_by])
                    ->andFilterWhere(['not between', $query->alias . 'updated_by', $this->FRM_updated_by, $this->TO_updated_by])
                    ->andFilterWhere(['not between', $query->alias . 'operator_id', $this->FRM_operator_id, $this->TO_operator_id])
                    ->andFilterWhere(['not between', $query->alias . 'deleted', $this->FRM_deleted, $this->TO_deleted])
                    ->andFilterWhere(['not between', $query->alias . 'sms_id', $this->FRM_sms_id, $this->TO_sms_id])
                    ->andFilterWhere(['not between', $query->alias . 'access_token_expired_at', $this->FRM_access_token_expired_at, $this->TO_access_token_expired_at])
                    ->andFilterWhere(['not between', $query->alias . 'confirmed_at', $this->FRM_confirmed_at, $this->TO_confirmed_at])
                    ->andFilterWhere(['not between', $query->alias . 'last_login_at', $this->FRM_last_login_at, $this->TO_last_login_at])
                    ->andFilterWhere(['not between', $query->alias . 'blocked_at', $this->FRM_blocked_at, $this->TO_blocked_at])
                    ->andFilterWhere(['not between', $query->alias . 'role', $this->FRM_role, $this->TO_role])
                    ->andFilterWhere(['not between', $query->alias . 'created_at', $this->FRM_created_at, $this->TO_created_at])
                    ->andFilterWhere(['not between', $query->alias . 'updated_at', $this->FRM_updated_at, $this->TO_updated_at]);
        }
        return $query;
    }

}
