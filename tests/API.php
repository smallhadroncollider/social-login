<?php

namespace SmallHadronCollider\SocialLogin\Tests;

class API
{
    use Routing;

    private $login;

    public function __construct()
    {
        $this->routes = include("routes.php");
        $this->login = new SocialLoginProvider();
    }

    public function getUrls($get)
    {
        $login = $this->login->make()->setSessionID($get["session_id"]);
        return $login->getAuthUrls();
    }

    public function socialAuth($get)
    {
        $platform = $get["platform"];
        $code = $get["code"];
        $platform = $this->login->make()->setSessionID($get["session_id"])->platform($platform);

        $token = $platform->getTokenFromCode($code);
        $user = $platform->getUserFromToken($token);

        return (object) [
            "user_id" => $user->id,
            "token" => $platform->addPlatform($token),
        ];
    }

    public function auth($get)
    {
        $userID = $get["username"];
        $token = $get["password"];
        $platform = $this->login->make()->platformFromToken($token);

        $token = $platform->stripPlatform($token);
        $user = $platform->getUserFromToken($token);

        if ($userID === $user->id) {
            return true;
        }

        return false;
    }
}
