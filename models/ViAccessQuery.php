<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[ViAccess]].
 *
 * @see ViAccess
 */
class ViAccessQuery extends BaseQuery {
    /* public function active()
      {
      return $this->andWhere('[[status]]=1');
      } */

    /**
     * {@inheritdoc}
     * @return ViAccess[]|array
     */
    public function all($db = null) {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ViAccess|array|null
     */
    public function one($db = null) {
        return parent::one($db);
    }

}
