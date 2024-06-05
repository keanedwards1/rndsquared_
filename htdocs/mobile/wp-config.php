<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
 
define('WP_HOME','http://rndsquared.com/mobile');
define('WP_SITEURL','http://rndsquared.com/mobile');

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'mydomains_rndsquaredwp');

/** MySQL database username */
define('DB_USER', 'rndsquareduser');

/** MySQL database password */
define('DB_PASSWORD', 'n00dl3sn00dl3s!');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'u;;c%]u1v 9g{ZydzM6,TSsmb&H,LzU`yZF6eI|>|_Gj:;=j->1Z@C?0 5?|X9N_');
define('SECURE_AUTH_KEY',  'ohbc)0k,m(^M {OdYKb8Ri]/Y6+KR-{JcyA*:8iW[0zlT_=w5Rt^+(M?gvK@LN^h');
define('LOGGED_IN_KEY',    'OSO-T-u#Kd`Uk]chfOP6]nh(aZ`QK|CerfMF<7~5`F!u/J{_T%||p.~(n2[[s/==');
define('NONCE_KEY',        'A(A.]Ss}sL!KEdsY89tMNu-T+V;OxC_6U;@<k#QmiwWyIo^F/<:Hox-aCv[Q;N-8');
define('AUTH_SALT',        ':44iUJ-ODj:e_0)*D;k=@IT)B+zty6XX}AFka%</r&/caOQdi9ZiBd|)_-z{V;er');
define('SECURE_AUTH_SALT', '~Lem9a%-R7-r~TAzU.5}_#x->xR$foQ,+oH&_g]J[rlotuCoU<ch+2<^sW15C}dc');
define('LOGGED_IN_SALT',   'RX=1)#J-p%?z:![ c(Z$S #@$@,yxafq;D_ot:jA_&X.TxVR}9i*-NmL^7JIOM-^');
define('NONCE_SALT',       'dSC?T~XQ]Z>};IHwD4`@q`AN&lPa=H N8VX*c~_5?Y:0}F)|S}Fc`_&?qk `K$:7');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'rndwptbl_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress.  A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de.mo to wp-content/languages and set WPLANG to 'de' to enable German
 * language support.
 */
define ('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */

//define('WP_SITEURL', 'http://archive.rndsquared.com/mobile/');
//define('WP_HOME', 'http://archive.rndsquared.com/mobile/');

define('WP_DEBUG', false);
define ('WP_CACHE', false);

define('WP_POST_REVISIONS', false);
define( 'AUTOSAVE_INTERVAL', 99999 );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
