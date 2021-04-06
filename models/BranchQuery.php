<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Branch]].
 *
 * @see Branch
 */
class BranchQuery extends \app\models\BaseQuery {
    /* public function active()
      {
      return $this->andWhere('[[status]]=1');
      } */

    /**
     * @inheritdoc
     * @return Branch[]|array
     */
    public function all($db = null) {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Branch|array|null
     */
    public function one($db = null) {
        return parent::one($db);
    }

}
