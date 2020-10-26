<?php
return [
    'id' => 'referral-console',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'console\controllers',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=142.91.155.196;port=3306;dbname=test10', // хз какую надо было, взяла последнюю
            'username' => 'tester',
            'password' => 'Jwsq5YwG0aFcJtaK2xdo',
            'enableSchemaCache' => true,
            'schemaCache' => 'fileCache',
        ],
        'fileCache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@console/runtime/cache',
        ],
        'referralManager' => [
            'class' => 'console\components\ReferralManager',
            'cache' => 'fileCache',
        ]
    ],
];
