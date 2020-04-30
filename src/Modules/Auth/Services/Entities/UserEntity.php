<?php

namespace App\Modules\Auth\Services\Entities;

use League\OAuth2\Server\Entities\UserEntityInterface;

class UserEntity implements UserEntityInterface
{
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Return the user's identifier.
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->id;
    }
}
