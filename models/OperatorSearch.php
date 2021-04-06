<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Operator;
use app\components\helpers\ArrayHelper;

/**
 * @property integer $FRM_id
 * @property integer $TO_id
 * @property integer $FRM_parent_id
 * @property integer $TO_parent_id
 * @property integer $FRM_branch_id
 * @property integer $TO_branch_id
 * @property datetime $FRM_created_on
 * @property datetime $TO_created_on
 * @property datetime $FRM_updated_on
 * @property datetime $TO_updated_on
 * @property integer $FRM_created_by
 * @property integer $TO_created_by
 * @property integer $FRM_updated_by
 * @property integer $TO_updated_by
 * @property integer $FRM_operator_type
 * @property integer $TO_operator_type
 * OperatorSearch represents the model behind the search form about `app\models\Operator`.
 */
class OperatorSearch extends Operator {

    use \app\traits\SearchTrait;

    public $FRM_id;
    public $TO_id;
    public $FRM_parent_id;
    public $TO_parent_id;
    public $FRM_branch_id;
    public $TO_branch_id;
    public $FRM_created_on;
    public $TO_created_on;
    public $FRM_updated_on;
    public $TO_updated_on;
    public $FRM_created_by;
    public $TO_created_by;
    public $FRM_updated_by;
    public $TO_updated_by;
    public $FRM_operator_type;
    public $TO_operator_type;

    /**
     * additional range attributes
     */
    public function attributes() {
        $arributes = parent::attributes();
        $arributes[] = 'FRM_id';
        $arributes[] = 'TO_id';
        $arributes[] = 'FRM_parent_id';
        $arributes[] = 'TO_parent_id';
        $arributes[] = 'FRM_branch_id';
        $arributes[] = 'TO_branch_id';
        $arributes[] = 'FRM_created_on';
        $arributes[] = 'TO_created_on';
        $arributes[] = 'FRM_updated_on';
        $arributes[] = 'TO_updated_on';
        $arributes[] = 'FRM_created_by';
        $arributes[] = 'TO_created_by';
        $arributes[] = 'FRM_updated_by';
        $arributes[] = 'TO_updated_by';
        $arributes[] = 'FRM_operator_type';
        $arributes[] = 'TO_operator_type';
        return $arributes;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'parent_id', 'branch_id', 'created_by', 'updated_by'], 'customValidator', 'params' => ['function' => '\components\helper\ArrayHelper::isIntegerOr1dArray', 'message' => '{attribute} must in an integer or array of integer']],
            [['name', 'code', 'contact_person', 'address', 'mobileno', 'email', 'phoneno', 'status', 'pan_no', 'service_tax_no', 'gst_no', 'tin_no', 'remark', 'created_on', 'updated_on', 'login_id', 'password', 'deleted', 'FRM_created_on', 'TO_created_on', 'FRM_updated_on', 'TO_updated_on', 'operator_type'], 'safe'],
            [['FRM_id', 'TO_id', 'FRM_parent_id', 'TO_parent_id', 'FRM_branch_id', 'TO_branch_id', 'FRM_created_by', 'TO_created_by', 'FRM_updated_by', 'TO_updated_by', 'FRM_operator_type', 'TO_operator_type'], 'integer'],
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
        $query = Operator::find();

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
        if (ArrayHelper::isZero($this->parent_id)) {
            $this->parent_id = null;
            $query->andWhere([
                $query->alias . 'parent_id' => $this->parent_id,
            ]);
        };
        $query->andFilterWhere([
            $query->alias . 'id' => $this->id,
            $query->alias . 'parent_id' => $this->parent_id,
            $query->alias . 'branch_id' => $this->branch_id,
            $query->alias . 'created_on' => $this->created_on,
            $query->alias . 'updated_on' => $this->updated_on,
            $query->alias . 'created_by' => $this->created_by,
            $query->alias . 'updated_by' => $this->updated_by,
            $query->alias . 'operator_type' => $this->operator_type,
        ]);

        $query->andArrayLike(['name' => $this->name], false)
                ->andArrayLike(['code' => $this->code], false)
                ->andArrayLike(['contact_person' => $this->contact_person], false)
                ->andArrayLike(['address' => $this->address], false)
                ->andArrayLike(['mobileno' => $this->mobileno], false)
                ->andArrayLike(['email' => $this->email], false)
                ->andArrayLike(['phoneno' => $this->phoneno], false)
                ->andArrayLike(['status' => $this->status], false)
                ->andArrayLike(['pan_no' => $this->pan_no], false)
                ->andArrayLike(['service_tax_no' => $this->service_tax_no], false)
                ->andArrayLike(['gst_no' => $this->gst_no], false)
                ->andArrayLike(['tin_no' => $this->tin_no], false)
                ->andArrayLike(['remark' => $this->remark], false)
                ->andArrayLike(['login_id' => $this->login_id], false)
                ->andArrayLike(['password' => $this->password], false)
                ->andArrayLike(['deleted' => $this->deleted], false);

        $query->andFilterWhere(['between', $query->alias . 'id', $this->FRM_id, $this->TO_id])
                ->andFilterWhere(['between', $query->alias . 'parent_id', $this->FRM_parent_id, $this->TO_parent_id])
                ->andFilterWhere(['between', $query->alias . 'branch_id', $this->FRM_branch_id, $this->TO_branch_id])
                ->andFilterWhere(['between', $query->alias . 'created_on', $this->FRM_created_on, $this->TO_created_on])
                ->andFilterWhere(['between', $query->alias . 'updated_on', $this->FRM_updated_on, $this->TO_updated_on])
                ->andFilterWhere(['between', $query->alias . 'created_by', $this->FRM_created_by, $this->TO_created_by])
                ->andFilterWhere(['between', $query->alias . 'updated_by', $this->FRM_updated_by, $this->TO_updated_by])
                ->andFilterWhere(['between', $query->alias . 'operator_type', $this->FRM_operator_type, $this->TO_operator_type]);

        if ($notparams) {

            $this->load(array_merge($default, $notparams), '');
            if (!$this->validate()) {
                // uncomment the following line if you do not want to return any records when validation fails
                throw new \yii\web\HttpException(422, json_encode($this->errors));

                //          return $query;
            }
            // grid filtering conditions
            if (ArrayHelper::isZero($this->parent_id)) {
                $this->parent_id = null;
                $query->andWhere(['not', [
                        $query->alias . 'parent_id' => $this->parent_id,
                ]]);
            };
            $query->andArrayLike(['name' => $this->name], true)
                    ->andArrayLike(['code' => $this->code], true)
                    ->andArrayLike(['contact_person' => $this->contact_person], true)
                    ->andArrayLike(['address' => $this->address], true)
                    ->andArrayLike(['mobileno' => $this->mobileno], true)
                    ->andArrayLike(['email' => $this->email], true)
                    ->andArrayLike(['phoneno' => $this->phoneno], true)
                    ->andArrayLike(['status' => $this->status], true)
                    ->andArrayLike(['pan_no' => $this->pan_no], true)
                    ->andArrayLike(['service_tax_no' => $this->service_tax_no], true)
                    ->andArrayLike(['gst_no' => $this->gst_no], true)
                    ->andArrayLike(['tin_no' => $this->tin_no], true)
                    ->andArrayLike(['remark' => $this->remark], true)
                    ->andArrayLike(['login_id' => $this->login_id], true)
                    ->andArrayLike(['password' => $this->password], true)
                    ->andArrayLike(['deleted' => $this->deleted], true);

            $query->andFilterWhere(['not in', $query->alias . 'id', $this->id])
                    ->andFilterWhere(['not in', $query->alias . 'parent_id', $this->parent_id])
                    ->andFilterWhere(['not in', $query->alias . 'branch_id', $this->branch_id])
                    ->andFilterWhere(['not in', $query->alias . 'created_on', $this->created_on])
                    ->andFilterWhere(['not in', $query->alias . 'updated_on', $this->updated_on])
                    ->andFilterWhere(['not in', $query->alias . 'created_by', $this->created_by])
                    ->andFilterWhere(['not in', $query->alias . 'updated_by', $this->updated_by])
                    ->andFilterWhere(['not in', $query->alias . 'operator_type', $this->operator_type]);

            $query->andFilterWhere(['not between', $query->alias . 'id', $this->FRM_id, $this->TO_id])
                    ->andFilterWhere(['not between', $query->alias . 'parent_id', $this->FRM_parent_id, $this->TO_parent_id])
                    ->andFilterWhere(['not between', $query->alias . 'branch_id', $this->FRM_branch_id, $this->TO_branch_id])
                    ->andFilterWhere(['not between', $query->alias . 'created_on', $this->FRM_created_on, $this->TO_created_on])
                    ->andFilterWhere(['not between', $query->alias . 'updated_on', $this->FRM_updated_on, $this->TO_updated_on])
                    ->andFilterWhere(['not between', $query->alias . 'created_by', $this->FRM_created_by, $this->TO_created_by])
                    ->andFilterWhere(['not between', $query->alias . 'updated_by', $this->FRM_updated_by, $this->TO_updated_by])
                    ->andFilterWhere(['not between', $query->alias . 'operator_type', $this->FRM_operator_type, $this->TO_operator_type]);
        }
        return $query;
    }

}
