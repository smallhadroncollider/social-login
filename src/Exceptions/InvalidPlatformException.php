<?php

namespace SmallHadronCollider\SocialLogin\Exceptions;

use Exception;

class InvalidPlatformException extends Exception
{
    public function __construct($platform)
    {
        $this->message = "Invalid SocialLogin Platform: {$platform}";
    }
}
