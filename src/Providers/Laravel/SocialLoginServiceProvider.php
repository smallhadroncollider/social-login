<?php

namespace SmallHadronCollider\SocialLogin\Providers\Laravel;

use Config;
use Illuminate\Support\ServiceProvider;
use SmallHadronCollider\SocialLogin\SocialLogin;

class SocialLoginServiceProvider extends ServiceProvider
{
    private $socialLogin;

    public function boot()
    {
        $storer = new CacheStorer("social_login." . $this->app->environment());
        $config = [];

        foreach (SocialLogin::$supported as $platform) {
            $service = Config::get("services.{$platform}");

            if ($service) {
                $config[$platform] = $service;
            }
        }

        $this->socialLogin = new SocialLogin($config, $storer);
    }

    public function register()
    {
        $this->app->bind(SocialLogin::class, function () {
            return $this->socialLogin;
        });
    }
}
