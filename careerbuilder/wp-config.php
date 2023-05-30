<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'jobcareer_careerbuilder' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         'Ql*T=w9Q0:7KR.W9ax.74v0RveEfyfDmXacQH+|ygdv~wr47+dBYz?c`L/Gq=[/L' );
define( 'SECURE_AUTH_KEY',  'wVE;rDIaz0:cH|}D; Q?7NXDpwrR3d?zpV tKM36Qn.y/JJ4Qp)>H&[:Tg$AIW2G' );
define( 'LOGGED_IN_KEY',    'a!P6.Hycequ:D-1b.2l<Mn55.L_:cneRxH42.S0R6&ob{uh*}lX6|#Sc]57p(L6y' );
define( 'NONCE_KEY',        'S&FJ8:cjyadFl$;+HYigd.B~I`tj*@7nY3(!:05os_E`$W`zH(K%i*LB,@kq2JAE' );
define( 'AUTH_SALT',        '/R!|AX4h[}.b9D&-[[W9`I8oZpWAV76f)D=xLan!FqYge=6vi5Zi*rfT1@POO|^/' );
define( 'SECURE_AUTH_SALT', '}A#fxj|[zd_ .pU&Rsz!;oCF165l|-Jyd*RHqQ!:_j&peC&WrL^FA0kQZ&g`q>]o' );
define( 'LOGGED_IN_SALT',   'Qj}<D%P{GLT`zi{; $#,0K@r3+r5)>/dx6!8m-#[5Nwe~)!Ma*AmPPyN4m#Z:[PP' );
define( 'NONCE_SALT',       'YPIvNgb,<}?JdLxG6Gaa=ERb`Xb,| Bd^0-]hSX|b`[NjijETt3Id0T6Rzx2sCgd' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_DEBUG_LOG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
