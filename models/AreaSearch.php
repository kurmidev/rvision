<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\Area;
use \components\helper\ArrayHelper;

/**
 * @property integer $FRM_id
 * @property integer $TO_id
 * @property integer $FRM_operator_id
 * @property integer $TO_operator_id
 * @property datetime $FRM_created_on
 * @property datetime $TO_created_on
 * @property datetime $FRM_updated_on
 * @property datetime $TO_updated_on
 * @property integer $FRM_created_by
 * @property integer $TO_created_by
 * @property integer $FRM_updated_by
 * @property integer $TO_updated_by
 * AreaSearch represents the model behind the search form about `app\models\Area`.
 */
class AreaSearch extends Area {

    use \app\traits\SearchTrait;

    public $FRM_id;
    public $TO_id;
    public $FRM_operator_id;
    public $TO_operator_id;
    public $FRM_created_on;
    public $TO_created_on;
    public $FRM_updated_on;
    public $TO_updated_on;
    public $FRM_created_by;
    public $TO_created_by;
    public $FRM_updated_by;
    public $TO_updated_by;

    /**
     * additional range attributes
     */
    public function attributes() {
        $arributes = parent::attributes();
        $arributes[] = 'FRM_id';
        $arributes[] = 'TO_id';
        $arributes[] = 'FRM_operator_id';
        $arributes[] = 'TO_operator_id';
        $arributes[] = 'FRM_created_on';
        $arributes[] = 'TO_created_on';
        $arributes[] = 'FRM_updated_on';
        $arributes[] = 'TO_updated_on';
        $arributes[] = 'FRM_created_by';
        $arributes[] = 'TO_created_by';
        $arributes[] = 'FRM_updated_by';
        $arributes[] = 'TO_updated_by';
        return $arributes;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'operator_id', 'created_by', 'updated_by'], 'customValidator', 'params' => ['function' => '\components\helper\ArrayHelper::isIntegerOr1dArray', 'message' => '{attribute} must in an integer or array of integer']],
            [['area_code', 'name', 'status', 'created_on', 'updated_on', 'remark', 'deleted', 'FRM_created_on', 'TO_created_on', 'FRM_updated_on', 'TO_updated_on'], 'safe'],
            [['FRM_id', 'TO_id', 'FRM_operator_id', 'TO_operator_id', 'FRM_created_by', 'TO_created_by', 'FRM_updated_by', 'TO_updated_by'], 'integer'],
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
        $query = Area::find();

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
            $query->alias . 'operator_id' => $this->operator_id,
            $query->alias . 'created_on' => $this->created_on,
            $query->alias . 'updated_on' => $this->updated_on,
            $query->alias . 'created_by' => $this->created_by,
            $query->alias . 'updated_by' => $this->updated_by,
        ]);

        $query->andArrayLike(['area_code' => $this->area_code], false)
                ->andArrayLike(['name' => $this->name], false)
                ->andArrayLike(['status' => $this->status], false)
                ->andArrayLike(['remark' => $this->remark], false)
                ->andArrayLike(['deleted' => $this->deleted], false);

        $query->andFilterWhere(['between', $query->alias . 'id', $this->FRM_id, $this->TO_id])
                ->andFilterWhere(['between', $query->alias . 'operator_id', $this->FRM_operator_id, $this->TO_operator_id])
                ->andFilterWhere(['between', $query->alias . 'created_on', $this->FRM_created_on, $this->TO_created_on])
                ->andFilterWhere(['between', $query->alias . 'updated_on', $this->FRM_updated_on, $this->TO_updated_on])
                ->andFilterWhere(['between', $query->alias . 'created_by', $this->FRM_created_by, $this->TO_created_by])
                ->andFilterWhere(['between', $query->alias . 'updated_by', $this->FRM_updated_by, $this->TO_updated_by]);

        if ($notparams) {

            $this->load(array_merge($default, $notparams), '');
            if (!$this->validate()) {
                // uncomment the following line if you do not want to return any records when validation fails
                throw new \yii\web\HttpException(422, json_encode($this->errors));

                //          return $query;
            }
            // grid filtering conditions
            $query->andArrayLike(['area_code' => $this->area_code], true)
                    ->andArrayLike(['name' => $this->name], true)
                    ->andArrayLike(['status' => $this->status], true)
                    ->andArrayLike(['remark' => $this->remark], true)
                    ->andArrayLike(['deleted' => $this->deleted], true);

            $query->andFilterWhere(['not in', $query->alias . 'id', $this->id])
                    ->andFilterWhere(['not in', $query->alias . 'operator_id', $this->operator_id])
                    ->andFilterWhere(['not in', $query->alias . 'created_on', $this->created_on])
                    ->andFilterWhere(['not in', $query->alias . 'updated_on', $this->updated_on])
                    ->andFilterWhere(['not in', $query->alias . 'created_by', $this->created_by])
                    ->andFilterWhere(['not in', $query->alias . 'updated_by', $this->updated_by]);

            $query->andFilterWhere(['not between', $query->alias . 'id', $this->FRM_id, $this->TO_id])
                    ->andFilterWhere(['not between', $query->alias . 'operator_id', $this->FRM_operator_id, $this->TO_operator_id])
                    ->andFilterWhere(['not between', $query->alias . 'created_on', $this->FRM_created_on, $this->TO_created_on])
                    ->andFilterWhere(['not between', $query->alias . 'updated_on', $this->FRM_updated_on, $this->TO_updated_on])
                    ->andFilterWhere(['not between', $query->alias . 'created_by', $this->FRM_created_by, $this->TO_created_by])
                    ->andFilterWhere(['not between', $query->alias . 'updated_by', $this->FRM_updated_by, $this->TO_updated_by]);
        }
        return $query;
    }

}
