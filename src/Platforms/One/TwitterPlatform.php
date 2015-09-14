<?php

namespace SmallHadronCollider\SocialLogin\Platforms\One;

use League\OAuth1\Client\Server\Twitter;
use SmallHadronCollider\SocialLogin\Contracts\PlatformInterface;

class TwitterPlatform extends AbstractPlatform implements PlatformInterface
{
    protected $platform = "twitter";

    public function __construct(array $config)
    {
        $server = new Twitter([
            "identifier" => $config["client_id"],
            "secret" => $config["client_secret"],
            "callback_uri" => $config["redirect_url"],
        ]);

        parent::__construct($server);
    }
}
