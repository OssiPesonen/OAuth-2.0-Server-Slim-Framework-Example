<?php

namespace App\Modules\Auth\Services\Entities;

use League\OAuth2\Server\Entities\UserEntityInterface;

class UserEntity implements UserEntityInterface
{
    private $id;
    private $consented;

    public function __construct(string $id, bool $consented = false)
    {
        $this->id = $id;
        $this->consented = $consented;
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

    /**
     * Check if user has previously given consent
     *
     * @return bool
     */
    public function hasUserConsented(): bool {
        return $this->consented;
    }
}
