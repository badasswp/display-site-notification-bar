<?php
/**
 * Plugin Name: Display Site Notification Bar
 * Plugin URI:  https://github.com/badasswp/site-notification-bar
 * Description: Display a notice bar on your WP home page.
 * Version:     1.0.0
 * Author:      badasswp
 * Author URI:  https://github.com/badasswp
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: site-notification-bar
 * Domain Path: /languages
 *
 * @package SiteNotificationBar
 */

namespace badasswp\SiteNotificationBar;

if ( ! defined( 'WPINC' ) ) {
	exit;
}

define( 'SITE_NOTIFICATION_BAR_AUTOLOAD', __DIR__ . '/vendor/autoload.php' );

// Composer Check.
if ( ! file_exists( SITE_NOTIFICATION_BAR_AUTOLOAD ) ) {
	add_action(
		'admin_notices',
		function () {
			vprintf(
				/* translators: Plugin directory path. */
				esc_html__( 'Fatal Error: Composer not setup in %s', 'site-notification-bar' ),
				[ __DIR__ ]
			);
		}
	);

	return;
}

// Run Plugin.
require_once SITE_NOTIFICATION_BAR_AUTOLOAD;
( \SiteNotificationBar\Plugin::get_instance() )->run();
