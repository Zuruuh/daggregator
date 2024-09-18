<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\ErrorHandler;

require dirname(__DIR__) . '/vendor/autoload.php';

// see https://github.com/symfony/symfony/issues/53812#issuecomment-1962311843
ErrorHandler::register(null, false);
(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
