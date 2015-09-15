# Social Login

[![Build Status](https://travis-ci.org/smallhadroncollider/social-login.svg?branch=develop)](https://travis-ci.org/smallhadroncollider/social-login)

[Laravel Socialite](https://github.com/laravel/socialite) is great. But it wasn't created with API-centric (where you have a RESTful API app and a client-side app) PHP apps in mind: it relies on sessions (which a stateless API will lack) and specific `GET` parameters (which may not be desirable).

