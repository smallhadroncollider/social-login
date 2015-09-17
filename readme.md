# Social Login

[![Version](https://img.shields.io/packagist/v/smallhadroncollider/social-login.png?style=flat)](https://packagist.org/packages/smallhadroncollider/social-login) [![Build Status](https://img.shields.io/travis/smallhadroncollider/social-login.png?style=flat)](https://travis-ci.org/smallhadroncollider/social-login)

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

$login = new SocialLogin($config, $storer);
$login->setSessionID($_GET["session_id"]);

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
$login = (new SocialLogin($config, $storer))->setSessionID($_POST["session_id"]);

$code = $_POST["code"];

$platform = $login->platform($_POST["platform"]);
$token = $platform->getTokenFromCode($code);
$user = $platform->getTokenFromCode($code);
```

- **API**: Check if user exists in user database
    - If user does not exist, create user

```php
/*
 * POST https://api.mysite.com/v1/auth/social
 */

if (/* user does not exist in database */) {
    // Create a new user from
    // $user->id, $user->name, $user->email
}

if (/* user in database has different social id to logged in user */) {
    // return a 401 page
}
```

- **API**: Send back user id and token with platform prepended

```php
/*
 * POST https://api.mysite.com/v1/auth/social
 */

return json_encode([
    "user_id" => $user->id,
    "token" => $platform->addPlatform($token),
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
    "username" => $userDetails->user_id,
    "password" => $userDetails->token,
]);
```

- **API**: Check the username against the stored session id

```php
/*
 * POST https://api.mysite.com/v1/auth
 */

function checkUserLoggedIn($username, $password) {
    if (/* $user using social login */) {
        $login = new SocialLogin($config, $storer);
        $platform = $login->platformFromToken($token);
        $token = $platform->stripPlatform($token);
        $user = $platform->getUserFromToken($token);

        if ($username === $user->id) {
            return true;
        }

        return false;
    } else {
        // Login normally (e.g. check the password)
    }
}
```

## License

The MIT License (MIT)

Copyright (c) 2015 Small Hadron Collider

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
