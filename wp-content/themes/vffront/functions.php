<?php
/**
 * VFfront engine room
 *
 * @package vffront
 */

/**
 * Assign the VFfront version to a var
 */
$theme              = wp_get_theme( 'vffront' );
$vffront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$vffront = (object) array(
	'version' => $vffront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-vffront.php',
	'customizer' => require 'inc/customizer/class-vffront-customizer.php',
);

require 'inc/vffront-functions.php';
require 'inc/vffront-template-hooks.php';
require 'inc/vffront-template-functions.php';

// if ( class_exists( 'Jetpack' ) ) {
// 	$vffront->jetpack = require 'inc/jetpack/class-vffront-jetpack.php';
// }

if ( vffront_is_woocommerce_activated() ) {
	$vffront->woocommerce = require 'inc/woocommerce/class-vffront-woocommerce.php';

	require 'inc/woocommerce/vffront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/vffront-woocommerce-template-functions.php';
}

// if ( is_admin() ) {
// 	$vffront->admin = require 'inc/admin/class-vffront-admin.php';
// 	require 'inc/admin/class-vffront-plugin-install.php';
// }

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
// if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
// 	require 'inc/nux/class-vffront-nux-admin.php';
// 	require 'inc/nux/class-vffront-nux-guided-tour.php';
//
// 	if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
// 		require 'inc/nux/class-vffront-nux-starter-content.php';
// 	}
// }

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */
