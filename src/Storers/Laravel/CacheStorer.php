<?php

namespace SmallHadronCollider\SocialLogin\Providers\Laravel;

use Cache;
use SmallHadronCollider\SocialLogin\Contracts\StorerInterface;

class CacheStorer implements StorerInterface
{
    public function store($id, $token)
    {
        Cache::put($id, $token, 60);
        return $this;
    }

    public function get($id)
    {
        return Cache::get($id);
    }

    public function clear($id)
    {
        Cache::forget($id);
        return $this;
    }
}
