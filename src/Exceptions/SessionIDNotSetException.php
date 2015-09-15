<?php

namespace SmallHadronCollider\SocialLogin\Exceptions;

use Exception;

class SessionIDNotSetException extends Exception
{
    protected $message = "A session ID has not been set";
}
