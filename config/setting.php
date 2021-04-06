<?php
defined('LOGIN_EXPIRY_TIME') or define('LOGIN_EXPIRY_TIME', 24 * 60 * 60);



function loadConfig($prefix, $dir, $extraConfig = []) {
    $fileList = \yii\helpers\FileHelper::findFiles($dir, ['only' => ['*' . $prefix . '.php']]);
    $config = $extraConfig;

    if (!empty($fileList)) {
        $i = 0;
        foreach ($fileList as $filename) {
            $result = require($filename);
            foreach ($result as $key => $values) {
                if (\yii\helpers\ArrayHelper::keyExists($key, $config)) {
                    $config[$key] = \yii\helpers\ArrayHelper::merge($values, $config[$key]);
                } else {
                    $config[$key] = $values;
                }
            }

            $i++;
        }
    }
    return $config;
}
