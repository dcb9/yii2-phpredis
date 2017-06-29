<?php

namespace dcb9\redis\tests;

use dcb9\redis\Cache;
use dcb9\redis\Connection;

class CacheTest extends TestCase
{
    private $_cacheInstance = null;

    /**
     * @return Cache
     */
    protected function getCacheInstance()
    {
        $params = self::getParam();
        $connection = new Connection($params);
        $this->mockApplication(['components' => ['redis' => $connection]]);
        if ($this->_cacheInstance === null) {
            $this->_cacheInstance = new Cache();
        }

        return $this->_cacheInstance;
    }

    /**
     * Store a value that is 2 times buffer size big
     * https://github.com/yiisoft/yii2/issues/743
     */
    public function testLargeData()
    {
        $cache = $this->getCacheInstance();
        $data = str_repeat('XX', 8192); // http://www.php.net/manual/en/function.fread.php
        $key = 'bigdata1';
        $this->assertFalse($cache->get($key));
        $cache->set($key, $data);
        $this->assertTrue($cache->get($key) === $data);
        // try with multibyte string
        $data = str_repeat('ЖЫ', 8192); // http://www.php.net/manual/en/function.fread.php
        $key = 'bigdata2';
        $this->assertFalse($cache->get($key));
        $cache->set($key, $data);
        $this->assertTrue($cache->get($key) === $data);
    }

    /**
     * Store a megabyte and see how it goes
     * https://github.com/yiisoft/yii2/issues/6547
     */
    public function testReallyLargeData()
    {
        $cache = $this->getCacheInstance();
        $keys = [];
        for ($i = 1; $i < 16; $i++) {
            $key = 'realbigdata' . $i;
            $data = str_repeat('X', 100 * 1024); // 100 KB
            $keys[$key] = $data;
//            $this->assertTrue($cache->get($key) === false); // do not display 100KB in terminal if this fails :)
            $cache->set($key, $data);
        }
        $values = $cache->mget(array_keys($keys));
        foreach ($keys as $key => $value) {
            $this->assertArrayHasKey($key, $values);
            $this->assertTrue($values[$key] === $value);
        }
    }

    public function testMultiByteGetAndSet()
    {
        $cache = $this->getCacheInstance();
        $data = ['abc' => 'ежик', 2 => 'def'];
        $key = 'data1';
        $this->assertFalse($cache->get($key));
        $cache->set($key, $data);
        $this->assertTrue($cache->get($key) === $data);
    }

    public function testFlushValues()
    {
        $cache = $this->getCacheInstance();
        $key = 'data';
        $cache->set($key, 'val');
        $cache->flush();
        $this->assertFalse($cache->get($key));
    }

    public function testMultiSet()
    {
        $cache = $this->getCacheInstance();
        $items = [
            'k1' => 'v1',
            'k2' => 'v2',
            'k3' => 'v3',
        ];
        $this->assertEquals([], $cache->multiSet($items, 1));
        $this->assertEquals('v1', $cache->get('k1'));
        $this->assertEquals('v2', $cache->get('k2'));
        $this->assertEquals('v3', $cache->get('k3'));
        sleep(1);
        $this->assertFalse($cache->get('k1'));
        $this->assertFalse($cache->get('k2'));
        $this->assertFalse($cache->get('k3'));

        $this->assertFalse($cache->exists('k1'));
        $this->assertFalse($cache->exists('k2'));
        $this->assertFalse($cache->exists('k3'));

        $cache->multiSet($items);
        sleep(2);
        $this->assertEquals('v1', $cache->get('k1'));
        $this->assertEquals('v2', $cache->get('k2'));
        $this->assertEquals('v3', $cache->get('k3'));
    }

    public function testSetGet()
    {
        $cache = $this->getCacheInstance();
        $cache->set('key', 'val', 1);
        $this->assertEquals('val', $cache->get('key'));
        sleep(1);
        $this->assertFalse($cache->get('key'));
        $this->assertFalse($cache->exists('key'));

        $cache->set('key', 'val');
        $this->assertTrue($cache->delete('key'));
        $this->assertFalse($cache->exists('key'));

    }

    public function testMultiAdd()
    {
        $cache = $this->getCacheInstance();
        $items = [
            'k1' => 'v1',
            'k2' => 'v2',
            'k3' => 'v3',
        ];
        $cache->multiSet($items);
        $this->assertEquals(['k2'], $cache->multiAdd([
            'k2' => 'vv22',
            'k4' => 'vv44',
        ]));

        $this->assertEquals('v2', $cache->get('k2'));
        $this->assertEquals('vv44', $cache->get('k4'));

        $this->assertEquals(['k1'], $cache->multiAdd([
            'k5' => 'vv55',
            'k1' => 'v1',
        ], 1));
        $this->assertEquals('vv55', $cache->get('k5'));
        sleep(1);
        $this->assertFalse($cache->exists('k5'));
        $this->assertTrue($cache->exists('k1'));
    }
}
