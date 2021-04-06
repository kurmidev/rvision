<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Operator]].
 *
 * @see Operator
 */
class OperatorQuery extends \app\models\BaseQuery {
    /* public function active()
      {
      return $this->andWhere('[[status]]=1');
      } */

    /**
     * @inheritdoc
     * @return Operator[]|array
     */
    public function all($db = null) {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Operator|array|null
     */
    public function one($db = null) {
        return parent::one($db);
    }

}
