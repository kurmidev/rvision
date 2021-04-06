<?php

namespace app\traits;

use app\components\Cache\Cache;

trait CacheTrait {

    public $qcd;
    public $qcdNull = false;

    public function setQcdNull($v = true) {
        $this->qcdNull = $v;
    }

    public function applyCache($dependenceies = [], $nocache = false) {
        if ($nocache) {
            return $this->nocache();
        }
        if (is_array($dependenceies)) {
            $models = isset($dependenceies['dependency']) ? (array) $dependenceies['dependency'] : [];
            $id = isset($dependenceies['id']) ? $dependenceies['id'] : null;
        } else {
            $id = $dependenceies;
            $models = [];
        }
//        \components\helper\Utils::l( $this->getCD($id, $models));
        if (Cache::isCacheEnabled()) {
            return $this->cache($this->qcd, $this->getCD($id, $models));
        } else {
            return $this->nocache();
        }
    }

    public function getCD($id = null, $models = [], $checkQcq = false) {
        if ($checkQcq && $this->qcdNull) {
            return null;
        } else {
            array_push($models, $this->getClassName());
            return Cache::getCacheDependency($models, $id);
        }
    }

}
