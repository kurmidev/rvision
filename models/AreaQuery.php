<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Area]].
 *
 * @see Area
 */
class AreaQuery extends \app\models\BaseQuery {
    /* public function active()
      {
      return $this->andWhere('[[status]]=1');
      } */

    /**
     * @inheritdoc
     * @return Area[]|array
     */
    public function all($db = null) {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Area|array|null
     */
    public function one($db = null) {
        return parent::one($db);
    }

}
