<?php


namespace App\Modules\Auth\Services\Repositories;

use App\Modules\Auth\Models\AuthModel;
use App\Modules\Auth\Services\Entities\UserEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var AuthModel
     */
    protected $authModel;

    public function __construct(AuthModel $model)
    {
        $this->authModel = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    )
    {
        // Todo: Check the username, passwword from users model and return the correct dingdong ding
        $id = 1;
        $entity = null;

        if($id) {
            $consented = $this->hasUserConsented($id, $clientEntity->getIdentifier());
            $entity = new UserEntity($id, $consented);
        }

        return $entity;
    }

    public function hasUserConsented(int $userId, string $clientId) {
        return $this->authModel->hasUserConsented($userId, $clientId);
    }

    public function addClientAuthorization(int $userId, string $clientId) {
        return $this->authModel->addClientAuthorization($userId, $clientId);
    }
}
