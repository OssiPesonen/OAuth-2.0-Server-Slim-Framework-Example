<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    # Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'auth'                => [
                'private_key'    => getenv('PRIVATE_KEY_PATH'),
                'encryption_key' => getenv('ENCRYPTION_KEY')
            ],
            'displayErrorDetails' => true,
            # Should be set to false in production
            'logger'              => [
                'name'  => 'slim-app',
                'path'  => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'db'                  => [
                'name'     => getenv('DB_NAME'),
                'host'     => getenv('DB_HOST'),
                'username' => getenv('DB_USER'),
                'password' => getenv('DB_PASSWORD'),
                'port'     => getenv('DB_PORT'),
                'driver'   => 'pdo_mysql'
            ],
            'locale' => 'en_GB'
        ],
    ]);
};
