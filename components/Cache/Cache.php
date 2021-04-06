<?php

namespace app\components\Cache;

Use Yii;

class Cache {

    public static function getDepedencyFile($models) {
        switch (\Yii::$app->params['cache']['dependency_type']) {
            case 'redis':
                return RedisDependency::getDepedencyKeys($models);
            case 'file':
            default:
                return FileDependency::getDepedencyKeys($models);
        }
    }

    public static function getCacheDependency($model, $reusable = false) {
        $fileName = self::getDepedencyFile($model);
        switch (\Yii::$app->params['cache']['dependency_type']) {
            case 'redis':
                $fileName = RedisDependency::getDepedencyKeys($model);
                return new RedisDependency(['fileName' => $fileName, 'reusable' => isset(\Yii::$app->params['reusable']) ? \Yii::$app->params['reusable'] : $reusable
                ]);
            case 'file':
            default:
                $fileName = FileDependency::getDepedencyKeys($model);
                return new FileDependency(['fileName' => $fileName, 'reusable' => isset(\Yii::$app->params['reusable']) ? \Yii::$app->params['reusable'] : $reusable
                ]);
        }
    }

    public static function updateCacheDependency($models) {
        switch (\Yii::$app->params['cache']['dependency_type']) {
            case 'redis':
                return RedisDependency::updateCacheDependency($models);
            case 'file':
            default:
                return FileDependency::updateCacheDependency($models);
        }
    }

    public static function isCacheEnabled() {
        if (!\app\models\ViAccess::isConsole() && isset(\Yii::$app->request->get()[\Yii::$app->params['cache']['queryParam']])) {
            if (\Yii::$app->request->get()[\Yii::$app->params['cache']['queryParam']] == 2) {
                \app\models\subscriber\SubscriberAccount::deleteBouqueListCache();
                Yii::$app->cache->flush();
            }
            return false;
        }
        return \Yii::$app->params['cache']['enabled'];
    }

    public static function getQCD() {
        return \Yii::$app->params['cache']['query_cache_duration'];
    }

    public static function cacheMysqlQueryCountResult($query, $expiryCount) {
        
    }

}
