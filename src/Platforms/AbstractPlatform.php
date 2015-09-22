<?php

namespace SmallHadronCollider\SocialLogin\Platforms;

use SmallHadronCollider\SocialLogin\Contracts\PlatformInterface;
use SmallHadronCollider\SocialLogin\Contracts\StorerInterface;
use SmallHadronCollider\SocialLogin\Exceptions\SessionIDNotSetException;
use SmallHadronCollider\SocialLogin\Exceptions\InvalidTokenException;

abstract class AbstractPlatform
{
    protected $storer;
    protected $platform;
    protected $sessionID;

    public function setStorer(StorerInterface $storer)
    {
        $this->storer = $storer;
        return $this;
    }

    public function setSessionID($sessionID)
    {
        $this->sessionID = $sessionID;
        return $this;
    }

    public function name()
    {
        return $this->platform;
    }

    public function addPlatform($token)
    {
        return "{$this->platform}:{$token}";
    }

    public function stripPlatform($token)
    {
        if (!preg_match("/^{$this->platform}:/", $token)) {
            throw new InvalidTokenException();
        }

        return preg_replace("/^{$this->platform}:/", "", $token);
    }

    protected function checkSessionID()
    {
        if (!$this->sessionID) {
            throw new SessionIDNotSetException();
        }
    }
}
