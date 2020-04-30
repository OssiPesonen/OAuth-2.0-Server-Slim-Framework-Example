<?php

namespace App\Modules\Auth\Services\Repositories;

use App\Modules\Auth\Models\AuthModel;
use App\Modules\Auth\Services\Entities\RefreshTokenEntity;
use Doctrine\DBAL\DBALException;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
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
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        $this->model->addRefreshToken($refreshTokenEntity);
    }

    /**
     * {@inheritdoc}
     * @throws DBALException
     */
    public function revokeRefreshToken($tokenId)
    {
        $this->model->revokeRefreshToken($tokenId);
    }

    /**
     * {@inheritdoc}
     * @throws DBALException
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        return $this->model->isValidRefreshToken($tokenId) === false;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }
}
