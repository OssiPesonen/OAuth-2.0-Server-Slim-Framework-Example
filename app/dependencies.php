<?php
declare(strict_types=1);

use App\Modules\Auth\Services\AuthService;
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Logging\DebugStack;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        # Monolog
        LoggerInterface::class                           => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        # Database connection (DBAL)
        Connection::class                 => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $connectionParams = [
                'dbname'   => $settings['db']['name'],
                'user'     => $settings['db']['username'],
                'password' => $settings['db']['password'],
                'host'     => $settings['db']['host'],
                'driver'   => $settings['db']['driver'],
                'port'     => $settings['db']['port']
            ];

            $connection = DriverManager::getConnection($connectionParams);

            # Logging. Not required. Don't do this in production.
            $connection->getConfiguration()->setSQLLogger(new DebugStack());

            return $connection;
        },
        # OAuth 2.0 server
        AuthorizationServer::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            # Fetch the service from the container
            $authService = $c->get(AuthService::class);

            $privateKey = $settings['auth']['private_key'];
            $encryptionKey = $settings['auth']['encryption_key'];

            $privateKey = new CryptKey($privateKey, null, false);

            # Setup the authorization server
            $server = new AuthorizationServer(
                $authService->getClientRepository(),
                $authService->getAccessTokenRepository(),
                $authService->getScopeRepository(),
                $privateKey,
                $encryptionKey
            );

            # Enable auth code grant
            $grant = new AuthCodeGrant(
                $authService->getAuthCodeRepository(),
                $authService->getRefreshTokenRepository(),
                new \DateInterval('PT10M') // authorization codes will expire after 10 minutes
            );

            # Refresh tokens will expire after 1 month
            $grant->setRefreshTokenTTL(new \DateInterval('P1M'));
            # Access tokens will expire after 1 hour
            $server->enableGrantType($grant, new \DateInterval('PT1H'));

            return $server;
        }
    ]);
};
