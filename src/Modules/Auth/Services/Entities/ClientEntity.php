<?php

namespace App\Modules\Auth\Services\Entities;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ClientEntity implements ClientEntityInterface
{
    protected $accountIdentifier;

    /**
     * ClientEntity constructor.
     * @param string $name
     * @param string $redirectUri
     * @param string|null $accountIdentifier Xerberus account ID (site, application, organization)
     */
    public function __construct(string $name, string $redirectUri, string $accountIdentifier = null)
    {
        $this->name = $name;
        $this->redirectUri = $redirectUri;

        if($accountIdentifier) {
            $this->accountIdentifier = $accountIdentifier;
        }
    }

    public function setConfidential() {
        $this->isConfidential = true;
    }

    /**
     * Returns the account id these scopes belong to
     *
     * This is mainly used to identify if the application requesting authorization
     * is Xerberus itself in which case we extend the scopes with admin access.
     *
     * Otherwise this scope will not be granted.
     *
     * @return string
     */
    public function getAccountIdentifier() {
        return $this->accountIdentifier;
    }

    use ClientTrait, EntityTrait;
}
