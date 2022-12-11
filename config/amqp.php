<?php

return [

    'use' => 'production',

    'properties' => [

        'production' => [
            'host'                => env('RABBITMQ_HOST', 'localhost'),
            'port'                => env('RABBITMQ_PORT', 5672),
            'username'            => env('RABBITMQ_LOGIN', 'guest'),
            'password'            => env('RABBITMQ_PASSWORD', 'guest'),
            'vhost'               => '/',
            'exchange'            => env('RABBITMQ_EXCHANGE_NAME', 'amq.direct'),
            'exchange_type'       => env('RABBITMQ_EXCHANGE_TYPE', 'direct'),
            'exchange_durable'    => env('RABBITMQ_EXCHANGE_DURABLE', true),
            'exchange_properties' => [],
            'consumer_tag'        => 'consumer',
            'ssl_options'         => [], // See https://secure.php.net/manual/en/context.ssl.php
            'connect_options'     => [], // See https://github.com/php-amqplib/php-amqplib/blob/master/PhpAmqpLib/Connection/AMQPSSLConnection.php
            'queue_durable'       => env('RABBITMQ_QUEUE_DURABLE', true),
            'queue_properties'    => ['x-ha-policy' => ['S', 'all']],
            'timeout'             => 0
        ],

    ],

];
