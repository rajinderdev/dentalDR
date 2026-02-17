<?php
return [
    'hosts' => [
        env('ELASTICSEARCH_HOSTS', 'localhost:9200')
    ],
    'retries' => 1,
    'timeout' => 5,
    'connection_pool' => '\\Elasticsearch\\ConnectionPool\\StaticNoPingConnectionPool',
    'ssl_verification' => false
];
