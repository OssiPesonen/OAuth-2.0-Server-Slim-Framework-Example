<?php

namespace App\Modules\Auth\Services\Repositories;

use App\Modules\Auth\Services\Entities\ScopeEntity;
use App\Modules\Auth\Models\AuthModel;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class ScopeRepository implements ScopeRepositoryInterface
{
    private $model;

    public function __construct(AuthModel $model)
    {
        $this->model = $model;
    }

    /**
     * Checks that the scope is a valid one
     *
     * {@inheritdoc}
     */
    public function getScopeEntityByIdentifier($scopeIdentifier)
    {
        # By default return null if no scopes are allowed
        $return = null;

        # Fetch scopes
        $scopes = $this->model->listScopes();

        if (array_key_exists($scopeIdentifier, $scopes) === false) {
            $scope = new ScopeEntity();
            $scope->setIdentifier($scopeIdentifier);
            $return = $scope;
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        /**
         * If $userIdentifier is provided, we fetch the users permissions and add to the 'scopes' permission values
         *
         */

        return $scopes;
    }
}
