<?php

namespace SmallHadronCollider\SocialLogin\Contracts;

interface StorerInterface
{
    public function store($id, $token);
    public function get($id);
    public function clear($id);
}
