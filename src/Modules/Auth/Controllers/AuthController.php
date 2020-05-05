<?php

namespace App\Modules\Auth\Controllers;

use App\Modules\Auth\Services\AuthService;
use App\Modules\Auth\Services\Entities\UserEntity;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class AuthController
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var AuthorizationServer
     */
    protected $server;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->server = $this->container->get(AuthorizationServer::class);
    }

    /**
     * Authorize the request
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public function authorize(Request $request, Response $response, array $args)
    {
        try {
            # Validate the HTTP request and return an AuthorizationRequest object
            $authRequest = $this->server->validateAuthorizationRequest($request);

            # Serialize the authRequest object to session so we can use it on the next steps: login and consent
            $_SESSION['authRequest'] = serialize($authRequest);

            # Return a 302 Found with redirect
            $response = $response->withStatus(302)->withHeader('Location', getenv('TENANT_URL') . getenv('TENANT_SIGNIN_PATH'));
        } catch (OAuthServerException $exception) {
            $response = $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            $response = $response->withStatus(500);
        }

        return $response;
    }

    /**
     * Sign in the user and either redirect him to consent form,
     * or complete the request and redirect back to redirect_uri with authorization code
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\MessageInterface|\Psr\Http\Message\ResponseInterface|\Slim\Psr7\Message|Response
     */
    public function signIn(Request $request, Response $response, array $args)
    {
        try {
            if (empty($_SESSION['authRequest'])) {
                throw OAuthServerException::serverError("We were unable to find your authorization session. Please retry your request.");
            }

            /** @var AuthorizationRequest $authRequest */
            $authRequest = unserialize($_SESSION['authRequest']);

            if (!$authRequest instanceof AuthorizationRequest) {
                throw OAuthServerException::serverError("We were unable to verify your authorization session. Please retry your request.");
            }

            # In here, authenticate the users request (username & password)
            # UserService::isValid(...);

            # Once the user has logged in set the user on the AuthorizationRequest
            $authRequest->setUser(new UserEntity(1));

            # Check if use has given consent
            $consent = false;

            # Allow first party to override consent
            if ((int)$authRequest->getClient()->getAccountIdentifier() === 1) {
                 $consent = true;
            }

            # Not first party application, check if the user has consented earlier
            # It would be beneficial for the API to return the requested scopes and their permissions in the returned response
            # instead of a redirect
            if (!$consent && $authRequest->getUser()->hasUserConsented() === false) {
                $response = $response->withStatus(302)->withHeader('Location', getenv('TENANT_URL') . getenv('TENANT_CONSENT_PATH'));
            } else {
                # Already consented earlier, approve
                $authRequest->setAuthorizationApproved(true);

                # Complete the request, return back to redirect URI
                $response = $this->server->completeAuthorizationRequest($authRequest, $response);

                # With XmlHttpRequest we just return the redirectUri in the body and not redirect
                if (in_array('XmlHttpRequest', $request->getHeader('X-Requested-With'))) {
                    $redirectUri = $response->getHeader('Location')[0];
                    $response = $this->setRedirectBody($response, $redirectUri);
                }
            }
        } catch (OAuthServerException $exception) {
            $response = $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            $response = $response->withStatus(500);
        }

        return $response;
    }

    /**
     * Approve the application to gain access to requested scopes
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @throws OAuthServerException
     */
    public function approveApp(Request $request, Response $response, array $args)
    {
        try {
            # Attempt to locate authRequest object from the SESSION
            if (empty($_SESSION['authRequest'])) {
                throw OAuthServerException::serverError("We were unable to find your authorization session. Please retry your request.");
            }

            /** @var AuthorizationRequest $authRequest */
            $authRequest = unserialize($_SESSION['authRequest']);

            # Verify the instance type
            if (!$authRequest instanceof AuthorizationRequest) {
                throw OAuthServerException::serverError("We were unable to find your authorization session. Please retry your request.");
            }

            /** @var AuthService $authService */
            $authService = $this->container->get(AuthService::class);

            # Store authorization information for user and app
            $authService->getUserRepository()->addClientAuthorization(
                $authRequest->getUser()->getIdentifier(),
                $authRequest->getClient()->getIdentifier()
            );

            # Approve the authorization request
            $authRequest->setAuthorizationApproved(true);

            $response = $this->server->completeAuthorizationRequest($authRequest, $response);
        } catch (OAuthServerException $exception) {
            # All instances of OAuthServerException can be formatted into a HTTP response
            $response = $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            $response = $response->withStatus(500);
        }

        return $response;
    }

    /**
     * Respond to access token request
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function accessToken(Request $request, Response $response, array $args)
    {
        try {
            # Try to respond to the request
            $response = $this->server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            $response = $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            $response = $response->withStatus(500);
        }

        return $response;
    }

    /**
     * Sets the given $redirectUri in the response body, and convert
     * the response to 200 OK with application/json content-type
     *
     * @param ResponseInterface $response
     * @param string $redirectUri Redirect URL
     * @param array $payload Additional content added to the body
     * @return ResponseInterface
     */
    private function setRedirectBody(ResponseInterface $response, string $redirectUri, array $payload = [])
    {
        $writableBodyStream = $response->getBody();

        $body = ['redirectUri' => $redirectUri];

        if (!empty($payload)) {
            $body = array_merge($body, $payload);
        }

        $writableBodyStream->write(json_encode($body));

        $response = $response
            ->withStatus(202)
            ->withBody($writableBodyStream)
            ->withoutHeader('Location')
            ->withHeader('Content-type', 'application/json');

        return $response;
    }
}