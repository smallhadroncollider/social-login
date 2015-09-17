<?php

namespace SmallHadronCollider\SocialLogin\Contracts;

interface StorerInterface
{
    /**
     * Store the token
     *
     * @param string $id A unique id. Should use a VARCHAR if using SQL
     * @param string @token The token. Often this will be a serialized object. Should use TEXT if using SQL
     */
    public function store($id, $token);

    /**
     * Retrieve the token
     * Should retrieve the previously stored token with the given ID
     *
     * @param string $id The unique id
     */
    public function get($id);

    /**
     * Clear the token
     * Should clear the previously stored token (with the given ID) from storage
     *
     * @param string $id The unique id
     */
    public function clear($id);
}
