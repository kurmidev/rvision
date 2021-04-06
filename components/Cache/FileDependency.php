<?php

namespace app\components\Cache;

use Yii;
use yii\base\InvalidConfigException;
Use app\components\helpers\DateHelper;

class FileDependency extends \yii\caching\FileDependency {

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
        $filemtime = null;
        if (is_array($this->fileName)) {
            foreach ($this->fileName as $f) {
                $fileName = Yii::getAlias($f);
                clearstatcache(false, $fileName);
                $ft = @filemtime($fileName);
                $filemtime = max([$filemtime, $ft]);
            }
        } else {
            $fileName = Yii::getAlias($this->fileName);
            clearstatcache(false, $fileName);
            $filemtime = @filemtime($fileName);
        }
        return $filemtime;
    }

    public static function getDepedencyKeys($models) {
        if (!is_array($models)) {
            $models = [$models];
        }
        $return = [];
        $path = \Yii::getAlias('@cache');
        foreach ($models as $model) {
            \yii\helpers\FileHelper::createDirectory($path);
            $return[] = "$path/" . $model . '.cd';
        }
        return $return;
    }

    public static function updateCacheDependency($models) {
        $fnames = self::getDepedencyKeys($models);
        foreach ($fnames as $fname) {
            $r = touch($fname);
        }
    }

}
