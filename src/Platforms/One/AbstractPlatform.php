<?php

namespace SmallHadronCollider\SocialLogin\Platforms\One;

use League\OAuth1\Client\Server\Server;
use SmallHadronCollider\SocialLogin\Platforms\AbstractPlatform as Platform;
use SmallHadronCollider\SocialLogin\Contracts\PlatformInterface;
use SmallHadronCollider\SocialLogin\User;

abstract class AbstractPlatform extends Platform implements PlatformInterface
{
    protected $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function getAuthUrl()
    {
        $temporaryIdentifier = $this->server->getTemporaryCredentials();
        $this->storer->store("{$this->platform}.{$this->sessionID}.temporary", serialize($temporaryIdentifier));
        return $this->server->getAuthorizationUrl($temporaryIdentifier);
    }

    public function setAuthCode($code)
    {
        list($token, $verifier) = explode(":", $code);
        $temporaryCredentials = unserialize($this->storer->get("{$this->platform}.{$this->sessionID}.temporary"));
        $tokenCredentials = $this->server->getTokenCredentials($temporaryCredentials, $token, $verifier);

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
        $this->storer->store("{$this->platform}.{$userID}", serialize($tokenCredentials));
    }
}
