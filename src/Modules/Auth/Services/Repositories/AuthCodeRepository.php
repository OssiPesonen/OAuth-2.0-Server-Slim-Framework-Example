<?php

namespace App\Modules\Auth\Services\Repositories;

use App\Modules\Auth\Models\AuthModel;
use App\Modules\Auth\Services\Entities\AuthCodeEntity;
use Doctrine\DBAL\DBALException;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository implements AuthCodeRepositoryInterface
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
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        $this->model->addCode($authCodeEntity);
    }

    /**
     * {@inheritdoc}
     * @throws DBALException
     */
    public function revokeAuthCode($codeId)
    {
        $this->model->revokeCode($codeId);
    }

    /**
     * {@inheritdoc}
     * @throws DBALException
     */
    public function isAuthCodeRevoked($codeId)
    {
        return $this->model->isValidAuthCode($codeId) === false;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewAuthCode()
    {
        return new AuthCodeEntity();
    }
}
