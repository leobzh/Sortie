<?php

if (file_exists(dirname(__DIR__).'/var/cache/prod/App_KernelProdContainer.preload.php')) {
    require dirname(__DIR__).'/var/cache/prod/App_KernelProdContainer.preload.php';
}


define('GOOGLE_CLIENT_ID', '886269640435-5t3ngf60uvepfopb8i0h9fakt7goi863.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_OAUTH_SCOPE', 'https://www.googleapis.com/auth/calendar');
define('REDIRECT_URI', 'http://localhost:8001/sortie');

$googleOauthURL = 'https://accounts.google.com/o/oauth2/auth.scope='.urlencode(GOOGLE_OAUTH_SCOPE).'&redirect_uri='.REDIRECT_URI.'&response_type=code&client_id='.GOOGLE_CLIENT_ID.'&access_type=offline';

if(!session_id()) {
    session_start();
}