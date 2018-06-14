<?php

namespace dcb9\redis;

use Redis;
use Yii;
use yii\base\Configurable;
use RedisException;

/**
 * Class Connection
 * @package dcb9\redis
 */
class Connection extends Redis implements Configurable
{
    /**
     * @var string the hostname or ip address to use for connecting to the redis server. Defaults to 'localhost'.
     * If [[unixSocket]] is specified, hostname and port will be ignored.
     */
    public $hostname = 'localhost';
    /**
     * @var integer the port to use for connecting to the redis server. Default port is 6379.
     * If [[unixSocket]] is specified, hostname and port will be ignored.
     */
    public $port = 6379;
    /**
     * @var string the unix socket path (e.g. `/var/run/redis/redis.sock`) to use for connecting to the redis server.
     * This can be used instead of [[hostname]] and [[port]] to connect to the server using a unix socket.
     * If a unix socket path is specified, [[hostname]] and [[port]] will be ignored.
     */
    public $unixSocket;
    /**
     * @var string the password for establishing DB connection. Defaults to null meaning no AUTH command is send.
     * See http://redis.io/commands/auth
     */
    public $password;
    /**
     * @var integer the redis database to use. This is an integer value starting from 0. Defaults to 0.
     */
    public $database = 0;
    /**
     * @var float value in seconds (optional, default is 0.0 meaning unlimited)
     */
    public $connectionTimeout = 0.0;

    /**
     * Constructor.
     * The default implementation does two things:
     *
     * - Initializes the object with the given configuration `$config`.
     * - Call [[init()]].
     *
     * If this method is overridden in a child class, it is recommended that
     *
     * - the last parameter of the constructor is a configuration array, like `$config` here.
     * - call the parent implementation at the end of the constructor.
     *
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct($config = [])
    {
        if (!empty($config)) {
            Yii::configure($this, $config);
        }
    }

    /**
     * Returns the fully qualified name of this class.
     * @return string the fully qualified name of this class.
     */
    public static function className()
    {
        return get_called_class();
    }

    /**
     * Establishes a DB connection.
     * It does nothing if a DB connection has already been established.
     * @throws RedisException if connection fails
     * @see connect()
     * @param string    $host
     * @param int       $port
     * @param float     $timeout
     * @param int       $retry_interval
     * @return bool
     */
    public function open( $host = null, $port = null, $timeout = null, $retry_interval = 0 )
    {
        if ($this->unixSocket !== null) {
            $isConnected = $this->connect($this->unixSocket);
        } else {
            if(is_null($host)){
                $host = $this->hostname;
            }
            if(is_null($port)){
                $port = $this->port;
            }
            if(is_null($timeout)){
                $timeout = $this->connectionTimeout;
            }
            $isConnected = $this->connect($host, $port, $timeout, null, $retry_interval);
        }

        if ($isConnected === false) {
            throw new RedisException('Connect to redis server error.');
        }

        if ($this->password !== null) {
            $this->auth($this->password);
        }

        if ($this->database !== null) {
            $this->select($this->database);
        }
    }

    /**
     * @return bool
     */
    public function ping()
    {
        return parent::ping() === '+PONG';
    }

    public function flushdb()
    {
        return parent::flushDB();
    }
}
