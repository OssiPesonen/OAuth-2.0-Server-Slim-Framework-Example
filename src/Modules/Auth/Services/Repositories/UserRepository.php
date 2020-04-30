<?php


namespace App\Modules\Auth\Services\Repositories;

use App\Modules\Auth\Services\Entities\UserEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {
        // Todo: Check the username, passwword from users model and return the correct dingdong ding
        $id = 1;
        return new UserEntity($id);
    }
}
