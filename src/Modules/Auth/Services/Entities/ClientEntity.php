<?php

namespace App\Modules\Auth\Services\Entities;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ClientEntity implements ClientEntityInterface
{
    public function __construct(string $name, string $redirectUri)
    {
        $this->name = $name;
        $this->redirectUri = $redirectUri;
    }

    public function setConfidential() {
        $this->isConfidential = true;
    }

    use ClientTrait, EntityTrait;
}
