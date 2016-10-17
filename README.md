Redis Cache and Session for Yii2
======================
This extension provides the [redis](http://redis.io/) key-value store support for the [Yii framework 2.0](http://www.yiiframework.com).

It includes a `Cache` and `Session` storage handler in redis.

**<font color="red">Notice: THIS REPO DID NOT SUPPORT ACTIVE RECORD.</font>**

Requirements
------------

- PHP >=5.4.0 
- Redis >= 2.6.12
- ext-redis >=2.2.7
- Yii2 ~2.0.4

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist dcb9/yii2-phpredis
```

or add

```json
"dcb9/yii2-phpredis": "~1.0"
```

to the require section of your composer.json.


Configuration
-------------

To use this extension, you have to configure the Connection class in your application configuration:

```php
return [
    //....
    'components' => [
        'redis' => [
            'class' => 'dcb9\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
        ],
    ]
];
```
