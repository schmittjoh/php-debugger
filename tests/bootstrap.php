<?php

call_user_func(function() {
    if (!is_file($autoloadFile = __DIR__.'/../vendor/autoload.php')) {
        throw new \RuntimeException('Could not find vendor/autoload.php. Did you run "composer install --dev"?');
    }

    $loader = require $autoloadFile;
    $loader->add('PHPDebugger', __DIR__.'/../src');
    $loader->add('PHPDebugger\\Tests', __DIR__);
});