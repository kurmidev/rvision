<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Society]].
 *
 * @see Society
 */
class SocietyQuery extends \app\models\BaseQuery {
    /* public function active()
      {
      return $this->andWhere('[[status]]=1');
      } */

    /**
     * @inheritdoc
     * @return Society[]|array
     */
    public function all($db = null) {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Society|array|null
     */
    public function one($db = null) {
        return parent::one($db);
    }

}
