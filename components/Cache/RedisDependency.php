<?php

namespace app\components\Cache;

use Yii;
use yii\base\InvalidConfigException;

class RedisDependency extends \yii\caching\FileDependency {

    /**
     * @var string the file path or [path alias](guide:concept-aliases) whose last modification time is used to
     * check if the dependency has been changed.
     */
    public $fileName;

    /**
     * Generates the data needed to determine if dependency has been changed.
     * This method returns the file's last modification time.
     * @param CacheInterface $cache the cache component that is currently evaluating this dependency
     * @return mixed the data needed to determine if dependency has been changed.
     * @throws InvalidConfigException if [[fileName]] is not set
     */
    protected function generateDependencyData($cache) {
        if ($this->fileName === null) {
            throw new InvalidConfigException('FileDependency::fileName must be set');
        }
        $filemtime = 0;
        if (is_array($this->fileName)) {
            foreach ($this->fileName as $f) {
                $fileName = $f;
                $ft = Yii::$app->cache->get($fileName);
//                Yii::debug([$fileName, $ft], 'generateDependencyData-1');
//                Yii::trace([$fileName, $ft], 'CacheData/generateDependencyData');
                $filemtime = max([$filemtime, $ft ?: 0]);
            }
        } else {
            $fileName = $this->fileName;
            $ftime = Yii::$app->cache->get($fileName);
//            Yii::trace([$fileName, $ftime], 'generateDependencyData');
//            Yii::debug([$fileName, $ftime], 'generateDependencyData-2');
            $filemtime = $ftime ?: 0;
        }
        return $filemtime ?: 0;
    }

    public static function getDepedencyKeys($models) {
        if (!is_array($models)) {
            $models = [$models];
        }
        $return = [];
        foreach ($models as $model) {
            $return[] = 'cd-' . $model;
        }
//        Yii::debug($return, 'getDepedencyFile');
        return $return;
    }

    public static function updateCacheDependency($models) {
        $fnames = self::getDepedencyKeys($models);
        foreach ($fnames as $fname) {
//            Yii::trace($fname, 'CacheData/updateCacheDependency');
            \Yii::$app->cache->set($fname, \components\helper\DateHelper::getMicrotime());
//            Yii::trace(Yii::$app->cache->get($fname), 'updateCacheDependency updated value');
        }
    }

}
