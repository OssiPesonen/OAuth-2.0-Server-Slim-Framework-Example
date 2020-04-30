<?php

namespace App\Modules\Auth\Controllers;

use App\Application\Utilities\ExceptionUtility;
use App\Modules\Auth\Services\Entities\UserEntity;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class AuthController
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AuthorizationServer
     */
    protected $server;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->server = $this->container->get(AuthorizationServer::class);
        $this->logger = $this->container->get(LoggerInterface::class);
    }

    /**
     * This combines the following features, but you can split each to their own
     * if you store the $authRequest by serializing it to a session, for example:
     *
     * - Authorizing the request (code)
     * - Authenticating the user (login)
     * - Approving the application (consent)
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function authorize(Request $request, Response $response, array $args)
    {
        try {
            # Validate the HTTP request and return an AuthorizationRequest object.
            $authRequest = $this->server->validateAuthorizationRequest($request);

            # The auth request object can be serialized and saved into a user's session.
            # You will probably want to redirect the user at this point to a login endpoint.

            # Once the user has logged in set the user on the AuthorizationRequest
            # For this example we simply set the ID to '1'
            $authRequest->setUser(new UserEntity(1));

            # At this point you should redirect the user to an authorization page.
            # This form will ask the user to approve the client and the scopes requested;
            $authRequest->setAuthorizationApproved(true);

            return $this->server->completeAuthorizationRequest($authRequest, $response);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            $this->logger->error("Something went wrong when attempting to issue an access token", ExceptionUtility::toArray($exception));

            $writableBodyStream = $response->getBody();
            $writableBodyStream->write(json_encode(['error' => 'Something has gone wrong on our end']));
            $httpResponseStatus = 500;

            return $response->withBody($writableBodyStream)->withStatus($httpResponseStatus);
        }
    }

    /**
     * Receives the 'code' and returns an access_token
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function accessToken(Request $request, Response $response, array $args)
    {
        try {
            // Try to respond to the request
            return $this->server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            // All instances of OAuthServerException can be formatted into a HTTP response
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            $this->logger->error("Something went wrong when attempting to issue an access token", ExceptionUtility::toArray($exception));
            // Unknown exception
            $writableBodyStream = $response->getBody();
            $writableBodyStream->write(json_encode(['error' => 'Something has gone wrong on our end']));
            $httpResponseStatus = 500;
            return $response->withBody($writableBodyStream)->withStatus($httpResponseStatus);

        }
    }
}
