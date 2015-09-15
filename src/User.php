<?php

namespace SmallHadronCollider\SocialLogin;

use OutOfBoundsException;

class User
{
    private $key = ["id", "email", "name"];
    private $id;
    private $email;
    private $name;

    private $properties;

    public function setID($id)
    {
        $this->id = $id;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }

    public function __get($property)
    {
        if (in_array($property, $this->key)) {
            return $this->{$property};
        }

        if (array_key_exists($property, $this->properties)) {
            return $this->properties[$property];
        }

        throw new OutOfBoundsException();
    }
}
