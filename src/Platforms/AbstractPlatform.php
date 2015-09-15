<?php

namespace SmallHadronCollider\SocialLogin\Platforms;

use SmallHadronCollider\SocialLogin\Contracts\PlatformInterface;
use SmallHadronCollider\SocialLogin\Contracts\StorerInterface;
use SmallHadronCollider\SocialLogin\User;

abstract class AbstractPlatform
{
    protected $storer;
    protected $platform;

    public function setStorer(StorerInterface $storer)
    {
        $this->storer = $storer;
        return $this;
    }
}
