<?php

namespace dcb9\redis\tests;

class ConnectionTest extends TestCase
{
    /**
     * test connection to redis and selection of db
     */
    public function testConnect()
    {
        $db = $this->getConnection(false);
        $database = $db->database;
        $db->open();
        $this->assertTrue($db->ping());
        $db->set('YIITESTKEY', 'YIITESTVALUE');
        $db->close();

        $db = $this->getConnection(false);
        $db->database = $database;
        $db->open();
        $this->assertEquals('YIITESTVALUE', $db->get('YIITESTKEY'));
        $db->close();

        $db = $this->getConnection(false);
        $db->database = 1;
        $db->open();
        $this->assertFalse($db->get('YIITESTKEY'));
        $db->close();
    }

    /**
     * tests whether close cleans up correctly so that a new connect works
     */
    public function testReConnect()
    {
        $db = $this->getConnection(false);
        $db->open();
        $this->assertTrue($db->ping());
        $db->close();
        $db->open();
        $this->assertTrue($db->ping());
        $db->close();
    }

    public function keyValueData()
    {
        return [
            [123],
            [-123],
            [0],
            ['test'],
            ["test\r\ntest"],
            [''],
        ];
    }

    /**
     * @dataProvider keyValueData
     */
    public function testStoreGet($data)
    {
        $db = $this->getConnection(true);
        $db->set('hi', $data);
        $this->assertEquals($data, $db->get('hi'));
    }
}
