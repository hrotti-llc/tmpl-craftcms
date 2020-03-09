<?php

define('CRAFT_COMPOSER_PATH', realpath(dirname(__DIR__) . '/../www/composer.json'));

define('CRAFT_BASE_PATH', realpath(dirname(__DIR__) . '/../www/craftcms'));
define('CRAFT_VENDOR_PATH', CRAFT_BASE_PATH . '/vendor');

require_once CRAFT_VENDOR_PATH . '/autoload.php';

if (class_exists('Dotenv\Dotenv') && file_exists(CRAFT_BASE_PATH . '/.env')) {

	Dotenv\Dotenv::create(CRAFT_BASE_PATH)->load();

}

define('CRAFT_ENVIRONMENT', getenv('ENVIRONMENT') ?: 'production');

$app = require CRAFT_VENDOR_PATH . '/craftcms/cms/bootstrap/web.php';

$app->run();
