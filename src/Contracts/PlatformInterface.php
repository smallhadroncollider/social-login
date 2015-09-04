<?php

namespace SmallHadronCollider\SocialLogin\Contracts;

interface PlatformInterface
{
    public function getAuthUrl();
    public function setAuthCode($code);
    public function getUser();
}
