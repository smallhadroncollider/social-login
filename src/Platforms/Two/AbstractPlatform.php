<?php

namespace SmallHadronCollider\SocialLogin\Platforms\Two;

use League\OAuth2\Client\Provider\AbstractProvider;

use SmallHadronCollider\SocialLogin\Platforms\AbstractPlatform as Platform;
use SmallHadronCollider\SocialLogin\Contracts\PlatformInterface;
use SmallHadronCollider\SocialLogin\Exceptions\InvalidAuthCodeException;
use SmallHadronCollider\SocialLogin\User;

abstract class AbstractPlatform extends Platform implements PlatformInterface
{
    public $provider;

    public function __construct(AbstractProvider $provider)
    {
        $this->provider = $provider;
    }

    public function getAuthUrl()
    {
        $this->checkSessionID();

        $authURL = $this->provider->getAuthorizationUrl();
        $this->storer->store("{$this->platform}.{$this->sessionID}.temporary", $this->provider->getState());
        return $authURL;
    }

    public function setAuthCode($code)
    {
        $this->checkSessionID();

        list($code, $state) = explode(":", $code);

        $cachedState = $this->storer->get("{$this->platform}.{$this->sessionID}.temporary");

        if ($cachedState !== $state) {
            throw new InvalidAuthCodeException();
        }

        $accessToken = $this->provider->getAccessToken("authorization_code", [
            "code" => $code,
        ]);

        $this->storeAccessToken($accessToken);
    }

    public function getUser($userID)
    {
        $accessToken = unserialize($this->storer->get("{$this->platform}.{$userID}"));
        $resourceOwner = $this->provider->getResourceOwner($accessToken);

        $user = new User();
        $user->setID($this->getUserID($resourceOwner));
        $user->setName($this->getUserName($resourceOwner));
        $user->setEmail($this->getUserEmail($resourceOwner));
        $user->setProperties($resourceOwner->toArray());

        return $user;
    }

    protected function storeAccessToken($accessToken)
    {
        $resourceOwner = $this->provider->getResourceOwner($accessToken);
        $userID = $this->getUserID($resourceOwner);
        $this->storer->store("{$this->platform}.{$userID}", serialize($accessToken));
    }

    abstract protected function getUserID($resourceOwner);
    abstract protected function getUserName($resourceOwner);
    abstract protected function getUserEmail($resourceOwner);
}
