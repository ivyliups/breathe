<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'booloola_breathe' );

/** MySQL database username */
define( 'DB_USER', 'booloola_breathe' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Ue05pJ0T7joM' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         't9eT3Qi5DhPEMCyGPSo9VEjxDw8nLptenXnwjaJsbiB26xcv1T7ICmWD2MNTs61o');
define('SECURE_AUTH_KEY',  'niRubY6vgdxHFhHzzedLhyRT1pjwHuyXsqJkh00YWbn12bX5wnpw1p9j5TdYygGU');
define('LOGGED_IN_KEY',    'WVVzhqFuDJSlbLY3DJ8mlDKPwXrtvEXx7SOL1SQX6Mhdwf84WINrM0Tl5NjSIvp8');
define('NONCE_KEY',        'MJLpW7i5g3nG8cYAn5Dk7gs0zHUO6IMmjgBHHlCfC8O1UnLZTTnkRevfgFgk71mT');
define('AUTH_SALT',        'Qsxqjx96ltHmoDQZpFNZs6NEbey6xnd8lHsD8lcilQTrdSyZxubZIOu7zjfuoibp');
define('SECURE_AUTH_SALT', 'FJEEr1cBFBX6teEqrGO9fxjjzAPDc1Hc9mhuHc26giant1kyshcbJFX35Kxfafoj');
define('LOGGED_IN_SALT',   'uWrmVpDoT1rj1wfMNtkTqNbEoNFpDCueBI8iSGvSX8bNuflRJnI4gaNzGZnEv2Ai');
define('NONCE_SALT',       'fpKyVLKYr09UgGKTlfJPCaAf8OPcN7zKrcQtaYjbyw67PHgMq11956I03AfIFHzq');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');define('FS_CHMOD_DIR',0755);define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');

/**
 * Turn off automatic updates since these are managed upstream.
 */
define('AUTOMATIC_UPDATER_DISABLED', true);


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
