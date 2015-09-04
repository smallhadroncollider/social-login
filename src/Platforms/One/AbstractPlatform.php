<?php

namespace SmallHadronCollider\SocialLogin\Platforms\One;

use SmallHadronCollider\SocialLogin\Contract\PlatformInterface;
use SmallHadronCollider\SocialLogin\User;

abstract class AbstractPlatform implements PlatformInterface
{
    protected $server;

    public function getAuthUrl()
    {
        $temporaryIdentifier = $this->server->getTemporaryCredentials();
        $this->storer->store("{$this->platform}.{$this->sessionID}.temporary", serialize($temporaryIdentifier));
        return $this->server->getAuthorizationUrl($temporaryIdentifier);
    }

    public function setAuthCode($code)
    {
        $tokens = explode(":", $code);
        $temporaryCredentials = unserialize($this->storer->get("{$this->platform}.{$this->sessionID}.temporary"));
        $tokenCredentials = $this->server->getTokenCredentials($temporaryCredentials, $tokens[0], $tokens[1]);

        $this->storeTokenCredentials($tokenCredentials);
    }

    public function getUser($userID)
    {
        $tokenCredentials = unserialize($this->storer->getToken("{$this->platform}.{$userID}"));

        $user = new User();
        $user->setID($this->server->getUserUid($tokenCredentials));
        $user->setName($this->server->getUserScreenName($tokenCredentials));
        $user->setEmail($this->server->getUserEmail($tokenCredentials));
        $user->setProperties($this->server->getUserDetails($tokenCredentials)->extra);

        return $user;
    }

    protected function storeTokenCredentials($tokenCredentials)
    {
        $userID = $this->server->getUserUid($tokenCredentials);
        $this->tokenStorer->storeToken("{$this->platform}.{$userID}", serialize($tokenCredentials));
    }
}
