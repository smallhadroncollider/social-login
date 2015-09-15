# Social Login

[![Build Status](https://travis-ci.org/smallhadroncollider/social-login.svg?branch=develop)](https://travis-ci.org/smallhadroncollider/social-login)

[Laravel Socialite](https://github.com/laravel/socialite) is great. But it wasn't created with [API-centric](http://code.tutsplus.com/tutorials/creating-an-api-centric-web-application--net-23417) PHP apps in mind: it relies on sessions (which a stateless API will lack) and specific `GET` parameters (which may not be desirable).

The Social Login package is designed to make it easy to add Social Login to apps where you have, for example, an OAuth 2 protected API.


## Typical Usage with an OAuth 2 API

- **Client**: Generate a session and request the authorisation URLs from the API

```php
/*
 * GET https://mysite.com/login
 */

// start a session
session_start();
$sessionID = session_id();

// get the list of supported social login urls
$urls = $http->get("https://api.mysite.com/v1/auth/social/urls", [
    "session_id" => $sessionID,
]);
```

- **API**: Create a new instance of `SocialLogin` using session id

```php
/*
 * GET https://api.mysite.com/v1/auth/social/urls
 */

use SmallHadronCollider\SocialLogin\SocialLogin;
use SmallHadronCollider\SocialLogin\Storers\MemcachedStorer;

$config = [
    "facebook" => [
        "client_id" => "1",
        "client_secret" => "secret",
        "redirect_url" => "https://mysite.com/login/social?platform=facebook",
    ],
];

$storer = new MemcachedStorer();

$sessionID = $_GET["session_id"];
$login = new SocialLogin($config, $storer, $sessionID);

return json_encode($login->getAuthUrls());
```

- **Client**: Send user to the third-party authorisation page

```php
/*
 * GET https://mysite.com/login
 */

<a href="<?= $urls["facebook"] ?>">Login With Facebook</a>
```

- **Client**: Get the auth code that is returned to the redirect

```php
/*
 * GET https://mysite.com/login/social?platform=facebook&code=blahblahblah&state=rhubarbrhubarb
 */

$code = $_GET["code"];
$state = $_GET["state"];

$userDetails = $http->post("https://api.mysite.com/v1/auth/social", [
    "code" => "{$code}:{$state}",
    "platform" => $_GET["platform"],
    "session_id" => $sessionID,
]);
```

- **API**: Use the auth code to request a user

```php
/*
 * POST https://api.mysite.com/v1/auth/social
 */

// Setup SocialLogin as before... (see above)
$login = new SocialLogin($config, $storer, $_POST["session_id"]);

$code = $_POST["code"];
$platform = $_POST["platform"];
$user = $login->platform($platform)->authorizeUser($code);
```

- **API**: Check if user exists in user database
    - If user does not exist, create user

```php
/*
 * POST https://api.mysite.com/v1/auth/social
 */

$email = $user->email;

if (/* user does not exist in database */) {
    // Create a new user from
    // $user->id, $user->name, $user->email
}
```

- **API**: Send back relevant login details (e.g. email)

```php
/*
 * POST https://api.mysite.com/v1/auth/social
 */

return json_encode([
    "user_id" => $user->id,
    "user_email" => $user->email,
]);
```

- **Client**: Authorize user using login details

```php
/*
 * GET https://mysite.com/login/social?platform=facebook&code=blahblahblah&state=rhubarbrhubarb
 */

$loggedIn = $http->post("https://api.mysite.com/v1/auth", [
    "client_id" => "oauthclientid",
    "client_secret" => "blahblahblah",
    "grant_type" => "password",
    "username" => $userDetails->email,
    "password" => $sessionID,
]);
```

- **API**: Check the username against the stored session id

```php
/*
 * POST https://api.mysite.com/v1/auth
 */

function checkUserLoggedIn($username, $password) {
    if (/* $username exists, but using social login */) {
        // Setup SocialLogin as before... (see above)
        $login = new SocialLogin($config, $storer, $password);
    } else {
        // Login normally (e.g. check the password)
    }
}
```
