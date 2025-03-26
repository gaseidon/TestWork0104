<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'testwork0104' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'MySQL-8.2' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '0!G(T6C%(4F]a`,QkN5a:vY^%3CkHuZSccMrJuY.>W8y3Fe0m9sj8neDMvF%?x(S' );
define( 'SECURE_AUTH_KEY',  'z]2SjbmclvF]rV-y:.roGgBfU4AR(H=EL;&}@C[!$ok!tHakQA/49}hyp=dI>l;C' );
define( 'LOGGED_IN_KEY',    '%Qj$|w7PJ?0FJn,TudIMpk<hDq<DGc,!Uijyj^e|2I0-A%,KA,VWj?*fDo3Qh*j%' );
define( 'NONCE_KEY',        '[rU^nWvdam|EsY`nEgjvoSzmuZC-R vw@V)gr[e2It{#kU,g_kZK-lnnDx}[MbYf' );
define( 'AUTH_SALT',        'u^ZydtR-A5|*_QZuvv<Ng.%ZFz`fiVJclQk&+bC#JK*CRF;tj{AFtl+A1U!KEv<c' );
define( 'SECURE_AUTH_SALT', '}bC #xcD~d<Vbd Wj1F#rR2!Pf&y|n9{rip3r%Gjh12DzUv7}GIt(D?otsP.!Q[^' );
define( 'LOGGED_IN_SALT',   'Wq#$KdCoiaLF,LAaEPa+g5s[Ub0kTncv:2]M,9(pA-3f&{?qP&;ejj;wQqj{i6{z' );
define( 'NONCE_SALT',       'Jf4Ah<g92I*DA&ex-Y/!10C7S4r)8_Ic%_7uiWjy!~6j}-s?pr1f(}[+-i$Ii%%W' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
