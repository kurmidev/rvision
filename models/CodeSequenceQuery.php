<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[CodeSequence]].
 *
 * @see CodeSequence
 */
class CodeSequenceQuery extends \app\models\BaseQuery {
    /* public function active()
      {
      return $this->andWhere('[[status]]=1');
      } */

    /**
     * @inheritdoc
     * @return CodeSequence[]|array
     */
    public function all($db = null) {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CodeSequence|array|null
     */
    public function one($db = null) {
        return parent::one($db);
    }

}
