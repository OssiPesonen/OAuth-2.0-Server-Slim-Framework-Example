<?php
declare(strict_types=1);

use App\Application\Middleware\JsonBodyParserMiddleware;
use App\Application\Middleware\SessionMiddleware;
use Slim\App;

return function (App $app) {
    # Begin a session
    $app->add(SessionMiddleware::class);

    # Parse incoming JSON requests to the 'parsedBody' of a Request
    $app->add(JsonBodyParserMiddleware::class);
};
