<?php

namespace SmallHadronCollider\SocialLogin\Contracts;

interface PlatformInterface
{
    public function getAuthUrl();
    public function authorizeUser($code);
    public function getUser($userID);
}
