<?php

return [
    "get" => [
        "/v1/auth/social/urls" => "getUrls",
    ],
    "post" => [
        "/v1/auth/social" => "socialAuth",
        "/v1/auth" => "auth",
    ],
];
