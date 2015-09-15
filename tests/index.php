<?php

date_default_timezone_set("Europe/London");

// Load Composer autoload file
include "../vendor/autoload.php";

use SmallHadronCollider\SocialLogin\SocialLogin;
use SmallHadronCollider\SocialLogin\Storers\SessionStorer;

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
    switch ($_GET["platform"]) {
        case "facebook":
            $facebook->setAuthCode($_GET["code"]);
            break;

        case "twitter":
            $twitter->setAuthCode("{$_GET["oauth_token"]}:{$_GET["oauth_verifier"]}");
            break;
    }

    header("Location: /");

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
