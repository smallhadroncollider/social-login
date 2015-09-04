<?php

namespace SmallHadronCollider\SocialLogin\Platforms;

use SmallHadronCollider\SocialLogin\Contract\PlatformInterface;
use SmallHadronCollider\SocialLogin\User;

abstract class AbstractPlatform
{
    protected $storer;
    protected $platform;
    protected $sessionID;

    public function __construct($sessionID)
    {
        $this->sessionID = $sessionID;
    }
}
