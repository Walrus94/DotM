<?php

// Copy these variables to your override.config.php in a development environment.
// Change as required.

define('SITE_URL', 'http://localhost:7001');

define('DISABLE_TRACKER', true);
define('DISABLE_IRC', true);

define('DEBUG_EMAIL', true);
define('DEBUG_MODE', true);
define('DEBUG_WARNINGS', true);
define('DEBUG_UPLOAD_NOTIFICATION', true);

define('OPEN_REGISTRATION', true);

define('MEMCACHE_HOST_LIST', [['host' => 'memcached', 'port' => 11211, 'buckets' => 1]]);

define('ENCKEY',       "");
define('AUTHKEY',      "");
define('RSS_HASH',     "");

define('SEEDBOX_SALT', "");
define('AVATAR_SALT',  "");

// Docker setup runs the scheduler only once every 15 minutes
define('SCHEDULER_DELAY', 1200);

// Migrations use the same credentials as the application by default.
// To override, define SQL_PHINX_USER and SQL_PHINX_PASS here.
