<?php

namespace dcb9\redis\tests;

use dcb9\redis\Connection;
use Yii;

class SessionTest extends TestCase
{
    public function testSession()
    {
        $params = self::getParam();
        $params['class'] = Connection::className();
        $this->mockApplication([
            'components' => [
                'redis' => $params,
                'session' => 'dcb9\\redis\\Session',
            ]
        ]);

        $sessionId = 'sessionId';
        $session = Yii::$app->session;
        $session->setTimeout(1);
        $sessionData = json_encode([
            'sessionId' => $sessionId,
            'username' => 'bob',
        ]);
        $session->writeSession($sessionId, $sessionData);
        $this->assertEquals($sessionData, $session->readSession($sessionId));
        $this->assertTrue($session->destroySession($sessionId));
        $this->assertEquals('', $session->readSession($sessionId));
    }
}
