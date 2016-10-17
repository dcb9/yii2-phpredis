<?php

namespace dcb9\redis\tests;

use yii\di\Container;
use yii\helpers\ArrayHelper;
use Yii;
use dcb9\redis\Connection;

/**
 * This is the base class for all yii framework unit tests.
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    public static $params;

    /**
     * Returns a test configuration param from config.php or config-local.php
     * @return array  the value of the configuration param
     */
    public static function getParam()
    {
        if (static::$params === null) {
            if (file_exists(__DIR__ . '/config-local.php')) {
                static::$params = include(__DIR__ . '/config-local.php');
            } else {
                static::$params = include(__DIR__ . '/config.php');
            }
        }

        return static::$params;
    }

    /**
     * Clean up after test.
     * By default the application created with [[mockApplication]] will be destroyed.
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->destroyApplication();
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected function mockApplication($config = [], $appClass = '\yii\console\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => dirname(__DIR__) . '/vendor',
        ], $config));
    }

    protected function mockWebApplication($config = [], $appClass = '\yii\web\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => dirname(__DIR__) . '/vendor',
            'components' => [
                'request' => [
                    'cookieValidationKey' => 'wefJDF8sfdsfSDefwqdxj9oq',
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => '/index.php',
                ],
            ]
        ], $config));
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected function destroyApplication()
    {
        Yii::$app = null;
        Yii::$container = new Container();
    }

    protected function setUp()
    {
        $params = self::getParam();
        if ($params === null) {
            $this->markTestSkipped('No redis server connection configured.');
        }
        $connection = new Connection($params);
        $this->mockApplication(['components' => ['redis' => $connection]]);

        parent::setUp();
    }

    /**
     * @param  boolean $reset whether to clean up the test database
     * @return Connection
     */
    public function getConnection($reset = true)
    {
        $params = self::getParam();
        $db = new Connection($params);
        if ($reset) {
            $db->open();
            $db->flushdb();
        }

        return $db;
    }
}
