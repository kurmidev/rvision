<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Branch;
use app\components\helpers\ArrayHelper;

/**
 * @property bigint $FRM_id
 * @property bigint $TO_id
 * @property smallint $FRM_status
 * @property smallint $TO_status
 * @property datetime $FRM_created_on
 * @property datetime $TO_created_on
 * @property bigint $FRM_created_by
 * @property bigint $TO_created_by
 * @property datetime $FRM_updated_on
 * @property datetime $TO_updated_on
 * @property bigint $FRM_updated_by
 * @property bigint $TO_updated_by
 * @property smallint $FRM_deleted
 * @property smallint $TO_deleted
 * BranchSearch represents the model behind the search form about `app\models\Branch`.
 */
class BranchSearch extends Branch {

    use \app\traits\SearchTrait;

    public $FRM_id;
    public $TO_id;
    public $FRM_status;
    public $TO_status;
    public $FRM_created_on;
    public $TO_created_on;
    public $FRM_created_by;
    public $TO_created_by;
    public $FRM_updated_on;
    public $TO_updated_on;
    public $FRM_updated_by;
    public $TO_updated_by;
    public $FRM_deleted;
    public $TO_deleted;

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
        $arributes[] = 'FRM_created_by';
        $arributes[] = 'TO_created_by';
        $arributes[] = 'FRM_updated_on';
        $arributes[] = 'TO_updated_on';
        $arributes[] = 'FRM_updated_by';
        $arributes[] = 'TO_updated_by';
        $arributes[] = 'FRM_deleted';
        $arributes[] = 'TO_deleted';
        return $arributes;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'status', 'created_by', 'updated_by', 'deleted'], 'customValidator', 'params' => ['function' => '\components\helper\ArrayHelper::isIntegerOr1dArray', 'message' => '{attribute} must in an integer or array of integer']],
            [['name', 'address', 'phoneno', 'branch_incharge', 'mobileno', 'faxno', 'created_on', 'updated_on', 'remark', 'FRM_created_on', 'TO_created_on', 'FRM_updated_on', 'TO_updated_on'], 'safe'],
            [['FRM_id', 'TO_id', 'FRM_status', 'TO_status', 'FRM_created_by', 'TO_created_by', 'FRM_updated_by', 'TO_updated_by', 'FRM_deleted', 'TO_deleted'], 'integer'],
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
        $query = Branch::find();

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
            $query->alias . 'created_by' => $this->created_by,
            $query->alias . 'updated_on' => $this->updated_on,
            $query->alias . 'updated_by' => $this->updated_by,
            $query->alias . 'deleted' => $this->deleted,
        ]);

        $query->andArrayLike(['name' => $this->name], false)
                ->andArrayLike(['address' => $this->address], false)
                ->andArrayLike(['phoneno' => $this->phoneno], false)
                ->andArrayLike(['branch_incharge' => $this->branch_incharge], false)
                ->andArrayLike(['mobileno' => $this->mobileno], false)
                ->andArrayLike(['faxno' => $this->faxno], false)
                ->andArrayLike(['remark' => $this->remark], false);

        $query->andFilterWhere(['between', $query->alias . 'id', $this->FRM_id, $this->TO_id])
                ->andFilterWhere(['between', $query->alias . 'status', $this->FRM_status, $this->TO_status])
                ->andFilterWhere(['between', $query->alias . 'created_on', $this->FRM_created_on, $this->TO_created_on])
                ->andFilterWhere(['between', $query->alias . 'created_by', $this->FRM_created_by, $this->TO_created_by])
                ->andFilterWhere(['between', $query->alias . 'updated_on', $this->FRM_updated_on, $this->TO_updated_on])
                ->andFilterWhere(['between', $query->alias . 'updated_by', $this->FRM_updated_by, $this->TO_updated_by])
                ->andFilterWhere(['between', $query->alias . 'deleted', $this->FRM_deleted, $this->TO_deleted]);

        if ($notparams) {

            $this->load(array_merge($default, $notparams), '');
            if (!$this->validate()) {
                // uncomment the following line if you do not want to return any records when validation fails
                throw new \yii\web\HttpException(422, json_encode($this->errors));

                //          return $query;
            }
            // grid filtering conditions
            $query->andArrayLike(['name' => $this->name], true)
                    ->andArrayLike(['address' => $this->address], true)
                    ->andArrayLike(['phoneno' => $this->phoneno], true)
                    ->andArrayLike(['branch_incharge' => $this->branch_incharge], true)
                    ->andArrayLike(['mobileno' => $this->mobileno], true)
                    ->andArrayLike(['faxno' => $this->faxno], true)
                    ->andArrayLike(['remark' => $this->remark], true);

            $query->andFilterWhere(['not in', $query->alias . 'id', $this->id])
                    ->andFilterWhere(['not in', $query->alias . 'status', $this->status])
                    ->andFilterWhere(['not in', $query->alias . 'created_on', $this->created_on])
                    ->andFilterWhere(['not in', $query->alias . 'created_by', $this->created_by])
                    ->andFilterWhere(['not in', $query->alias . 'updated_on', $this->updated_on])
                    ->andFilterWhere(['not in', $query->alias . 'updated_by', $this->updated_by])
                    ->andFilterWhere(['not in', $query->alias . 'deleted', $this->deleted]);

            $query->andFilterWhere(['not between', $query->alias . 'id', $this->FRM_id, $this->TO_id])
                    ->andFilterWhere(['not between', $query->alias . 'status', $this->FRM_status, $this->TO_status])
                    ->andFilterWhere(['not between', $query->alias . 'created_on', $this->FRM_created_on, $this->TO_created_on])
                    ->andFilterWhere(['not between', $query->alias . 'created_by', $this->FRM_created_by, $this->TO_created_by])
                    ->andFilterWhere(['not between', $query->alias . 'updated_on', $this->FRM_updated_on, $this->TO_updated_on])
                    ->andFilterWhere(['not between', $query->alias . 'updated_by', $this->FRM_updated_by, $this->TO_updated_by])
                    ->andFilterWhere(['not between', $query->alias . 'deleted', $this->FRM_deleted, $this->TO_deleted]);
        }
        return $query;
    }

}
