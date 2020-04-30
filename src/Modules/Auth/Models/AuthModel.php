<?php

namespace App\Modules\Auth\Models;

use App\Persistence\BaseModel;
use Doctrine\DBAL\Query\QueryBuilder;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\TokenInterface;

class AuthModel extends BaseModel
{
    public function listScopes()
    {
        return $this->connection->fetchAll("SELECT * FROM oauth_scopes");
    }

    /**
     * Return all clients
     *
     * @return mixed[]
     */
    public function listClients()
    {
        return $this->connection->fetchAll("SELECT * FROM oauth_clients");
    }

    /**
     * Return single auth client information
     *
     * @param string $id
     * @return false|mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function readClient(string $id)
    {
        return $this->connection->fetchAssoc("SELECT * FROM oauth_clients WHERE client_id = :client_id LIMIT 1", [
            'client_id' => $id
        ]);
    }

    /**
     * Validate Client ID and Secret
     *
     * @param string $id
     * @param string $secret
     * @param string $grantType
     * @return false|mixed
     */
    public function validateClient(string $id, string $secret, string $grantType)
    {
        $qb = new QueryBuilder($this->connection);

        $qb->select('client_id')
            ->from('oauth_clients')
            ->where('client_id = :client_id')
            ->setParameter('client_id', $id)
            ->andWhere('client_secret = :client_secret')
            ->setParameter('client_secret', $secret)
            ->andWhere(
                $qb->expr()->like('grant_types', "'%" . $grantType . "%'")
            );

        $client = $qb->execute()->fetch();

        return !empty($client['client_id']);
    }

    /**
     * Store an access token
     *
     * @param AccessTokenEntityInterface $entity
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createAccessToken(AccessTokenEntityInterface $entity)
    {
        return $this->connection->insert('oauth_access_tokens', [
            'access_token' => $entity->__toString(),
            'client_id'    => $entity->getClient()->getIdentifier(),
            'user_id'      => $entity->getUserIdentifier(),
            'scope'        => $this->scopesToStr($entity),
            'expires'      => $entity->getExpiryDateTime()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Revoke an access token by updating it's timestamp to 1
     *
     * @param string $tokenId
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function revokeAccessToken(string $tokenId)
    {
        return $this->connection->update('oauth_access_tokens', [
            'expires' => date('Y-m-d H:i:s', strtotime('-1 year')),
        ], ['access_token' => $tokenId]);
    }

    /**
     * Checks if an access token is still valid
     *
     * @param string $tokenId
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function isValidAccessToken(string $tokenId)
    {
        return !empty($this->connection->executeQuery(
            "SELECT access_token FROM oauth_access_tokens WHERE access_token = :token AND expires> :date", [
                'token' => $tokenId,
                'date' => date('Y-m-d H:i:s')
            ]
        )->fetch());
    }

    /**
     * Store authorization code
     *
     * @param AuthCodeEntityInterface $entity
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function addCode(AuthCodeEntityInterface $entity)
    {
        return $this->connection->insert('oauth_authorization_codes', [
            'authorization_code' => $entity->getIdentifier(),
            'client_id'          => $entity->getClient()->getIdentifier(),
            'user_id'            => $entity->getUserIdentifier(),
            'redirect_uri'       => $entity->getRedirectUri(),
            'expires'            => $entity->getExpiryDateTime()->format('Y-m-d H:i:s'),
            'scope'              => $this->scopesToStr($entity)
        ]);
    }

    /**
     * Revoke an access code by updating it's timestamp to 1
     *
     * @param string $codeId
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function revokeCode(string $codeId)
    {
        return $this->connection->update('oauth_authorization_codes', [
            'expires' => date('Y-m-d H:i:s', strtotime('-1 year')),
        ], ['authorization_code' => $codeId]);
    }

    public function isValidAuthCode(string $codeId)
    {
        return !empty($this->connection->executeQuery(
            "SELECT authorization_code FROM oauth_authorization_codes WHERE authorization_code = :code AND expires > :date", [
                'code' => $codeId,
                'date' => date('Y-m-d H:i:s')
            ]
        )->fetch());
    }


    /**
     * Store authorization code
     *
     * @param AuthCodeEntityInterface $entity
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function addRefreshToken(RefreshTokenEntityInterface $entity)
    {
        return $this->connection->insert('oauth_refresh_tokens', [
            'refresh_token'      => $entity->getIdentifier(),
            'client_id'          => $entity->getAccessToken()->getClient()->getIdentifier(),
            'user_id'            => $entity->getAccessToken()->getUserIdentifier(),
            'expires'            => $entity->getExpiryDateTime()->format('Y-m-d H:i:s'),
            'scope'              => $this->scopesToStr($entity->getAccessToken())
        ]);
    }

    /**
     * Revoke a refresh token by updating it's timestamp to 1
     *
     * @param string $token
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function revokeRefreshToken(string $token)
    {
        return $this->connection->delete('oauth_refresh_tokens', ['refresh_token' => $token]);
    }

    /**
     * Checks if a given refresh token is valid
     *
     * @param string $token
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function isValidRefreshToken(string $token)
    {
        return !empty($this->connection->executeQuery(
            "SELECT refresh_token FROM oauth_refresh_tokens WHERE refresh_token = :token AND expires > :time", [
                'token' => $token,
                'time' => date('Y-m-d H:i:s')
            ]
        )->fetch());
    }

    private function scopesToStr(TokenInterface $entity) {
        $str = '';
        foreach ($entity->getScopes() as $scope) {
            if(!empty($str)) $str .= ' ';
            $str .= $scope->getIdentifier();
        }
        return $str;
    }
}
