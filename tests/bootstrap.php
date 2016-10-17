<?php

// ensure we get report on all possible php errors
error_reporting(-1);

if (!defined('YII_ENABLE_ERROR_HANDLER')) {
  define('YII_ENABLE_ERROR_HANDLER', false);
}

if (!defined('YII_DEBUG')) {
    define('YII_DEBUG', true);
}

$_SERVER['SCRIPT_NAME'] = '/' . __DIR__;
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
