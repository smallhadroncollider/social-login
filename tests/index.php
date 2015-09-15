<?php

namespace SmallHadronCollider\SocialLogin\Tests;

// Load Composer autoload file
include "../vendor/autoload.php";

use SmallHadronCollider\SocialLogin\SocialLogin;

// Create new SocialLogin object
$login = new SocialLogin([
    "facebook" => [
        "client_id" => "1491252651198059",
        "client_secret" => "165012335278745064b8c7ea15fda3de",
        "redirect_url" => "http://localhost:8080/?platform=facebook",
    ],
    "twitter" => [
        "client_id" => "b58GoEiVnLPIzymwUsyNFYy2l",
        "client_secret" => "WzjqBn0ZWcpNbReaYLxR3OmkM5QP9Dsu64s4rfbjYBkVK5RbVc",
        "redirect_url" => "http://localhost:8080/?platform=twitter",
    ],
], new SessionStorer, session_id());

$facebook = $login->platform("facebook");
$twitter = $login->platform("twitter");

if (isset($_GET["platform"])) {
    $platform = $_GET["platform"];

    if (!isset($_GET["user"])) {
        switch ($platform) {
            case "facebook":
                $user = $facebook->authorizeUser("{$_GET["code"]}:{$_GET["state"]}");
                break;

            case "twitter":
                $user = $twitter->authorizeUser("{$_GET["oauth_token"]}:{$_GET["oauth_verifier"]}");
                break;
        }

        header("Location: /?platform={$platform}&user={$user->id}");
    } else {
        $userID = $_GET["user"];
        $user = $login->platform($_GET["platform"])->getUser($userID);

        echo $user->name;
    }

} else {

$socialLoginUrls = $login->getAuthUrls();

?>

<h1>Logins</h1>

<ul>
<?php foreach ($socialLoginUrls as $platform => $url) { ?>
    <li><a href="<?= $url ?>"><?= $platform ?></a></li>
<?php } ?>
</ul>
<?php } ?>
