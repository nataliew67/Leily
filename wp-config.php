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
define( 'DB_NAME', '2713-21_uhtxeibrjdf' );

/** Database username */
define( 'DB_USER', '2713-21_grechushnikovaa' );

/** Database password */
define( 'DB_PASSWORD', 'c3906fe0392a1fd6591f' );

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
define( 'AUTH_KEY',         'X(kN>s]fIKk[}D@VuIu:!Lf7,W4(+[j1.X2j;Sb%=1k#EY.8SIXbOy9PUd4M:m{E' );
define( 'SECURE_AUTH_KEY',  '^S*6ClQrK:et^,pdwa2g^G={T>^ZDJ;hbwi=GXGy{]*o jUHmtZAnxChu)5bW+!E' );
define( 'LOGGED_IN_KEY',    'Slz:&W&uvJ#/eF05=9+-/pQ6:_B|z.zGNKg9k&O0])RF%%jt}~5%$%Snv}T5:Oob' );
define( 'NONCE_KEY',        ';H|[M)+_:m}. }%B%lX<I%8=eDBOeWB7A0xS8kKm@L17Idg6QRAnmhO@GZTP25(w' );
define( 'AUTH_SALT',        'Bf4,b>c!A|iP)EhORZ25pL+RUP@KaCc!O?^oHHx-umO:/KE>j^# 4/fO(@ma+_dy' );
define( 'SECURE_AUTH_SALT', '*p|HB2+2&2a*y24apDm$1V*Uhh8wP^uLLQOKo.uP*a Y;YF52:lZ652v)#{RW%Wk' );
define( 'LOGGED_IN_SALT',   'myb=[-LzPM#uIl>1IyDIHIhj=tA+_GcT C1~`,[ 9xuY$|K?JVW~L IglU7V&.dn' );
define( 'NONCE_SALT',       '#Xt+TCM<NhQYD{36Vr!*jf}n_g}Bri?oPj-<p(Vuqh`/)E=V^.a.fMXIA0M@|Lt ' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'cFkmz_';


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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';