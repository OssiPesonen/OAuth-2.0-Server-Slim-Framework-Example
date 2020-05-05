<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Models\AuthModel;
use App\Modules\Auth\Services\Repositories\ClientRepository;
use App\Modules\Auth\Services\Repositories\ScopeRepository;
use App\Modules\Auth\Services\Repositories\AccessTokenRepository;
use App\Modules\Auth\Services\Repositories\AuthCodeRepository;
use App\Modules\Auth\Services\Repositories\RefreshTokenRepository;
use App\Modules\Auth\Services\Repositories\UserRepository;

class AuthService {
    private $clientRepository;
    private $scopeRepository;
    private $accessTokenRepository;
    private $authCodeRepository;
    private $refreshTokenRepository;
    private $userRepository;

    /**
     * @var AuthModel
     */
    protected $authModel;

    /**
     * AuthService constructor.
     *
     * Build the required OAuth repositories
     *
     * @param ClientRepository $clientRepository
     * @param ScopeRepository $scopeRepository
     * @param AccessTokenRepository $accessTokenRepository
     * @param AuthCodeRepository $authCodeRepository
     * @param RefreshTokenRepository $refreshTokenRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        ClientRepository $clientRepository,
        ScopeRepository $scopeRepository,
        AccessTokenRepository $accessTokenRepository,
        AuthCodeRepository $authCodeRepository,
        RefreshTokenRepository $refreshTokenRepository,
        UserRepository $userRepository
    )
    {
        $this->clientRepository = $clientRepository;
        $this->scopeRepository = $scopeRepository;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->authCodeRepository = $authCodeRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->userRepository = $userRepository;
    }

    public function getClientRepository() {
        return $this->clientRepository;
    }

    public function getScopeRepository() {
        return $this->scopeRepository;

    }
    public function getAccessTokenRepository() {
        return $this->accessTokenRepository;
    }

    public function getAuthCodeRepository() {
        return $this->authCodeRepository;
    }

    public function getRefreshTokenRepository() {
        return $this->refreshTokenRepository;
    }

    public function getUserRepository() {
        return $this->userRepository;
    }
}
