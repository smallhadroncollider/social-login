<?php

namespace SmallHadronCollider\SocialLogin\Platforms\Two;

use League\OAuth2\Client\Provider\LinkedIn;
use SmallHadronCollider\SocialLogin\Contracts\PlatformInterface;

class LinkedInPlatform extends AbstractPlatform implements PlatformInterface
{
    protected $platform = "linkedin";
    protected $authUrlOptions = [
        "scope" => [
            "r_basicprofile",
            "r_emailaddress"
        ]
    ];

    public function __construct(array $config)
    {
        $provider = new LinkedIn([
            "clientId" => $config["client_id"],
            "clientSecret" => $config["client_secret"],
            "redirectUri" => $config["redirect_url"],
        ]);

        parent::__construct($provider);
    }

    protected function getUserName($resourceOwner)
    {
        return $resourceOwner->getFirstName() . " " . $resourceOwner->getLastName();
    }
}
