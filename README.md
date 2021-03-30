1. Create following config file in config file
    a. cache.local.php
        return [
            'components' => [
                'cache' => [
                    'class' => 'yii\redis\Cache',
                    'redis' => [
                        'hostname' => 'localhost',
                        'port' => 6379,
                        'database' => 0,
                    ]
                ],
                'redis' => [
                    'class' => 'yii\redis\Connection',
                    'hostname' => 'localhost',
                    'port' => 6379,
                    'database' => 0,
                ],
            ]
        ];
  
    b. db.local.php
       return [
            'components' => [
                'db' => [
                    'class' => 'yii\db\Connection',
                    'driverName' => 'sqlsrv',
                    'dsn' => 'sqlsrv:Server=localhost;Database=rvision',
                    'username' => 'SA',
                    'password' => 'Chandrap@123',
                    'charset' => 'utf8'
                ],
            ]
        ];
    
