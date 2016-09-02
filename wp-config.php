<?php

define('WP_MAX_MEMORY_LIMIT','128M');
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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'insight');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

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
define('AUTH_KEY',         'wu2x{BI2@1$[MwI[X$-?9f&{dr_._W:V{ZiN)zzAv&*o|qRQ0CKvupq)*(u+]fxB');
define('SECURE_AUTH_KEY',  'MRXEpmjAJ[r}<u$Fl#zR,oBR)Js-Se#|m)qAX-CTc+#AHe3<sfdq{KL&qq`k~Hz(');
define('LOGGED_IN_KEY',    'WZX_-$k0hv!?:QF~QY>dIn?Rm6{ZzI>Z MMO?`pn5a<Y4-0AiGGz)3}mjV%w8?=[');
define('NONCE_KEY',        'v0<KmG7.huwvFu?[s64+ba.g_fP4w!<E!I28R_jF)wiD#CS^88C-zm#P^>ynTXh+');
define('AUTH_SALT',        'czzit8rHk|jNp7jxuYCqb@8> OE+Oh,w`(t:$Y.][:DGH>M<GYiO@SP#gk8xgYkG');
define('SECURE_AUTH_SALT', 'aY/i+ig_)0kP~C3&3oWeZ$Bz+F|1n6[Dk}O|^PM@f6Vle[X+XSC]>61!I]P~0abj');
define('LOGGED_IN_SALT',   'ma+[?$BLeBlp|L+^;_* G }!YGke,JW4Eg4+{K$)MKO36n$CpXE0XdzEi+%x6qvf');
define('NONCE_SALT',       '4|licXwnTN7Y@R%RXN;<3mD&w<XQ )lu7)?t09&[n_FW?Ycir]%MAn6Y*T}i:$T]');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'ip_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

define('FS_METHOD','direct');
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
