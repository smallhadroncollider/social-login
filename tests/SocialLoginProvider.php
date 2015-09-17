<?php

namespace SmallHadronCollider\SocialLogin\Tests;

use SmallHadronCollider\SocialLogin\SocialLogin;

class SocialLoginProvider
{
    private $config;
    private $storer;

    public function __construct()
    {
        $this->config = include("config.php");
        $this->storer = new SessionStorer();
    }

    public function make()
    {
        return new SocialLogin($this->config, $this->storer);
    }
}
