<?php

namespace App\Modules\Auth\Services\Repositories;

use App\Modules\Auth\Models\AuthModel;
use App\Modules\Auth\Services\Entities\ClientEntity;
use Doctrine\DBAL\DBALException;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
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
     * Fetches the client this OAuth CLIENT_ID is attached to
     *
     * {@inheritdoc}
     * @throws DBALException
     */
    public function getClientEntity($clientIdentifier)
    {
        $return = null;

        $client = $this->model->readClient($clientIdentifier);

        if(!empty($client)) {
            $entity = new ClientEntity(
                $client['client_id'],
                $client['redirect_uri']
            );

            $entity->isConfidential();

            # Mark as confidential if the client is marked as private
            if($client['visibility'] === 'private') {
                $entity->setConfidential();
            }

            $entity->setIdentifier($clientIdentifier);
            $return = $entity;
        }

        return $return;
    }

    /**
     * Validates the CLIENT_ID and SECRET combo with given grant type
     *
     * @inheritDoc
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        return !empty($this->model->validateClient($clientIdentifier, $clientSecret, $grantType));
    }
}
