<?php

namespace SmallHadronCollider\SocialLogin\Platforms\Two;

use League\OAuth2\Client\Provider\Facebook;
use SmallHadronCollider\SocialLogin\Contracts\PlatformInterface;

class FacebookPlatform extends AbstractPlatform implements PlatformInterface
{
    protected $platform = "facebook";

    public function __construct(array $config)
    {
        $provider = new Facebook([
            "clientId" => $config["client_id"],
            "clientSecret" => $config["client_secret"],
            "redirectUri" => $config["redirect_url"],
            "graphApiVersion" => "v2.4",
        ]);

        parent::__construct($provider);
    }

    protected function getUserID($resourceOwner)
    {
        return $resourceOwner->getId();
    }

    protected function getUserName($resourceOwner)
    {
        return $resourceOwner->getName();
    }

    protected function getUserEmail($resourceOwner)
    {
        return $resourceOwner->getEmail();
    }
}
