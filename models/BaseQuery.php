<?php

namespace app\models;

use app\components\Constants as C;
use \app\traits\CacheTrait;
use app\components\Cache\Cache;

class BaseQuery extends \yii\db\ActiveQuery {

    use CacheTrait;

    public $alias;

    function setAlias($alias) {
        $this->alias = $alias ? $alias . '.' : '';
        return $this->alias($alias);
    }

    function getAlias() {
        $alias = '';

        if (is_array($this->from)) {
            foreach ($this->from as $alias => $t) {
                return $alias . '.';
            }
        }
        return '';
    }

    function __construct($modelClass, $config = array()) {
        $this->qcd = Cache::getQCD();
        parent::__construct($modelClass, $config);
        $this->alias = $this->getAlias();
    }

    public function active() {
        return $this->andWhere(['status' => C::STATUS_ACTIVE]);
    }

    public function inactive() {
        return $this->andWhere(['status' => C::STATUS_INACTIVE]);
    }

    public function defaultScope($conf = []) {
        $NotActive = isset($conf['not']) ? $conf['not'] : false;
        return $this->active($NotActive);
    }

    public function andArrayLike($data, $not = false, $alias = null) {

        if ($alias !== false) {
            $alias = ($alias) ? $alias . '.' : $this->alias;
        }
        $condition = new \yii\db\Query;
        $isExactatch = false;
        foreach ($data as $column => $d) {
            $values = [];
            foreach ((array) $d as $v) {
                if ($isExactatch || self::isExactMatch($v)) {
                    $isExactatch = true;
                    $values[] = self::exactMatch($v);
                } else {
                    $condition->orFilterWhere(['like', $alias . $column, self::exactMatch($v), self::isLike($v)]);
                }
            }
            if ($values) {
                $condition->orFilterWhere([$alias . $column => $values]);
            }
        }
        if ($condition->where) {
            if ($not) {
                return $this->andFilterWhere(['not', $condition->where]);
            } else {

                return $this->andFilterWhere($condition->where);
            }
        } else {
            return $this;
        }
    }

    public function getClassName() {
        $path = explode('\\', $this->modelClass);
        return array_pop($path);
    }

}
