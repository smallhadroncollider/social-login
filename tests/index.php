<?php

namespace SmallHadronCollider\SocialLogin\Tests;

// Load Composer autoload file
include "../vendor/autoload.php";

$platforms = [
    "oauth1" => ["twitter"],
    "oauth2" => ["facebook"],
];

$fakeSessionID = "fakesessionid";

$http = new API();

if (isset($_GET["platform"])) {
    $platform = $_GET["platform"];

    // If OAuth 1 use oauth_token + oauth_verifier
    if (in_array($platform, $platforms["oauth1"])) {
        $code = "{$_GET["oauth_token"]}:{$_GET["oauth_verifier"]}";

    // If OAuth 2 use code + state
    } else {
        $code = "{$_GET["code"]}:{$_GET["state"]}";
    }

    $authDetails = $http->post("/v1/auth/social", [
        "session_id" => $fakeSessionID,
        "code" => $code,
        "platform" => $platform,
    ]);

    if ($authDetails) {
        $auth = $http->post("/v1/auth", [
            "client_id" => "oauthclientid",
            "client_secret" => "blahblahblah",
            "grant_type" => "password",
            "username" => $authDetails->user_id,
            "password" => $authDetails->token,
        ]);
    }

    var_dump($auth);
} else {
    $socialLoginUrls = $http->get("/v1/auth/social/urls", [
        "session_id" => $fakeSessionID,
    ]);
?>

    <h1>Logins</h1>

    <ul>
    <?php foreach ($socialLoginUrls as $platform => $url) { ?>
        <li><a href="<?= $url ?>"><?= $platform ?></a></li>
    <?php } ?>
    </ul>

<?php } ?>
