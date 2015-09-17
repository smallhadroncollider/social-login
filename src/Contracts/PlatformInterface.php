<?php

namespace SmallHadronCollider\SocialLogin\Contracts;

interface PlatformInterface
{
    public function getAuthUrl();
    public function getTokenFromCode($code);
    public function getUserFromToken($token);
}
