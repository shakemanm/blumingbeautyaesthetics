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
define( 'DB_NAME', 'wordpress_db' );

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
define( 'AUTH_KEY',         '&G]3L>0`KSTNA}!{e#`Q:>Y,sg^GNus%:o,mUTp@V|K^$WC%izQw0[RN[Lw~Ve=i' );
define( 'SECURE_AUTH_KEY',  '<r$%e*[4B 6EntgDS!lf_?z1gM_VoZl<&:qkLw$l)&B}y3`QKh^. RrsRmD7UN%q' );
define( 'LOGGED_IN_KEY',    'v.2ovDmelk_dl?i$*:!MV5PAyDU#QQHj^SpsvJ}0!)ciuKTo^rEt+ l67o|0yH0^' );
define( 'NONCE_KEY',        'Z64@i4s5N-OSvza+P(-D`ZoDA0m+h<;AD3]pxRBe|v/`~s.9[ti955h++c<?}s!?' );
define( 'AUTH_SALT',        'r~*B7Va3~o|kl3DgH+9mSU5e!HSwzp*X.%w;+9v-8 gC>09:HrB0#O#_qc:949%w' );
define( 'SECURE_AUTH_SALT', '``maSzrIb.:2,W) J*C4H$?*OdhQ,Ju@e!f% 2h%$F`lWio<#zn$`6t`H8mc^+|z' );
define( 'LOGGED_IN_SALT',   'ZgLx0I@Vs<uQ k%O$~knsT:%EyGo-X=er=lP6Ga7%nN3$G6T_hpB[!H)YSI~aT5V' );
define( 'NONCE_SALT',       '~_4,W0g*@QA*h^q!Q``OSwrgM:M4@@%JGEHnuty<c %^Kg,v8G@eh=xjtb-QL*3Z' );

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
