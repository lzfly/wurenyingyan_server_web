<?php
/**
 * Global settings
 */
require_once 'global.defines.php';

/**
 * Session settings
 */
require_once 'global.session.php';

/**
 * Datamap settings
 */
require_once 'global.datamap.php';

/**
 * Message settings
 */
require_once 'global.message.php';

/**
 * Database settings
 */
require_once 'database.mysql.php';

/**
 * App definitions
 */
define('__APP_NAME', 'Qianliyan');
define('__APP_VERSION', '1.0');

/**
 * URL relative constants
 */
define('__HOST_SERVER', 'http://182.92.156.183:8007');
define('__HOST_WEBSITE', 'http://182.92.156.183:8008');

/**
 * MVC url mapping ini file
 */
define('__MAP_INI_FILE', realpath(__ETC . '/app.mapping.ini'));

/**
 * Logic libraries
 */
define('__LIB_PATH_CLI', realpath(__LIB_DIR . '/Qianliyan/Cli'));
define('__LIB_PATH_SERVER', realpath(__LIB_DIR . '/Qianliyan/App/Server'));
define('__LIB_PATH_WEBSITE', realpath(__LIB_DIR . '/Qianliyan/App/Website'));

/**
 * Data dir settings
 */
define('__DAT_LOG_DIR', realpath(__DAT_DIR . '/log'));
define('__DAT_CACHE_DIR', realpath(__DAT_DIR . '/cache'));

/**
 * Picture dir
 */
define('__WWW_PICTURES', realpath(__WWW_DIR . '/website/pictures'));
define('__WWW_5LIANPAI', realpath(__WWW_DIR . '/website/5lianpai'));

/**
 * memcache config
 */
define('__MEMCACHE_IP', '182.92.156.183');
define('__MEMCACHE_PORT', 11211);


