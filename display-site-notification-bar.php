<?php
/**
 * Plugin Name: Display Site Notification Bar
 * Plugin URI:  https://github.com/badasswp/display-site-notification-bar
 * Description: Display a notice bar on your WP home page.
 * Version:     1.0.5
 * Author:      badasswp
 * Author URI:  https://github.com/badasswp
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: display-site-notification-bar
 * Domain Path: /languages
 *
 * @package DisplaySiteNotificationBar
 */

namespace badasswp\DisplaySiteNotificationBar;

if ( ! defined( 'WPINC' ) ) {
	exit;
}

define( 'DISPLAY_SITE_NOTIFICATION_BAR_AUTOLOAD', __DIR__ . '/vendor/autoload.php' );

// Composer Check.
if ( ! file_exists( DISPLAY_SITE_NOTIFICATION_BAR_AUTOLOAD ) ) {
	add_action(
		'admin_notices',
		function () {
			vprintf(
				/* translators: Plugin directory path. */
				esc_html__( 'Fatal Error: Composer not setup in %s', 'display-site-notification-bar' ),
				[ __DIR__ ]
			);
		}
	);

	return;
}

// Run Plugin.
require_once DISPLAY_SITE_NOTIFICATION_BAR_AUTOLOAD;
( \DisplaySiteNotificationBar\Plugin::get_instance() )->run();
