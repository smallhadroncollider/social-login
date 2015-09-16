<?php

namespace SmallHadronCollider\SocialLogin\Tests;

use Exception;

trait Routing
{
    protected $routes;

    public function get($uri, $values)
    {
        if (array_key_exists($uri, $this->routes["get"])) {
            $method = $this->routes["get"][$uri];
            return $this->{$method}($values);
        }

        throw new Exception("404");
    }

    public function post($uri, $values)
    {
        if (array_key_exists($uri, $this->routes["post"])) {
            $method = $this->routes["post"][$uri];
            return $this->{$method}($values);
        }

        throw new Exception("404");
    }
}
