<?php

namespace App\Modules\Auth\Services\Repositories;

use App\Modules\Auth\Models\AuthModel;
use App\Modules\Auth\Services\Entities\AccessTokenEntity;
use Doctrine\DBAL\DBALException;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
     /**
     * @var AuthModel
     */
    private $model;

    public function __construct(AuthModel $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     * @throws DBALException
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $this->model->createAccessToken($accessTokenEntity);
    }

    /**
     * {@inheritdoc}
     * @throws DBALException
     */
    public function revokeAccessToken($tokenId)
    {
        $this->model->revokeAccessToken($tokenId);
    }

    /**
     * {@inheritdoc}
     * @throws DBALException
     */
    public function isAccessTokenRevoked($tokenId)
    {
        return $this->model->isValidAccessToken($tokenId) === false;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessTokenEntity();

        $accessToken->setClient($clientEntity);

        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }

        $accessToken->setUserIdentifier($userIdentifier);

        return $accessToken;
    }
}
