<?php
declare(strict_types=1);

use App\Modules\Auth\Controllers\AuthController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    # CORS Pre-Flight OPTIONS Request Handler
    $app->options('/{routes:.+}', function (Request $request, Response $response) {
        return $response;
    });

    // Authentication
    $app->post('/access_token', AuthController::class . ':accessToken');
    $app->get('/authorize', AuthController::class . ':authorize');

};
