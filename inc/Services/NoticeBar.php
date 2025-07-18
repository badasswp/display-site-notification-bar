<?php
/**
 * NoticeBar Service.
 *
 * This service manages the registration and
 * binding of the NoticeBar service.
 *
 * @package DisplaySiteNotificationBar
 */

namespace DisplaySiteNotificationBar\Services;

use DisplaySiteNotificationBar\Services\Admin;
use DisplaySiteNotificationBar\Abstracts\Service;
use DisplaySiteNotificationBar\Interfaces\Kernel;

class NoticeBar extends Service implements Kernel {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'wp_head', [ $this, 'display_notice_bar' ] );
	}

	/**
	 * Display Notice Bar.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function display_notice_bar(): void {
		$settings = Admin::get_settings();

		$text             = $settings['text'] ?? '';
		$text_color       = $settings['text_color'] ?? '';
		$background_color = $settings['background_color'] ?? '';
		$position         = $settings['position'] ?? '';
		$visibility       = $settings['visibility'] ?? '';

		if ( 'home' === $visibility && ! is_home() ) {
			return;
		}

		printf(
			'<section class="site-notification-bar" style="%4$s: 0; background: %3$s;">
				<span style="color: %2$s;">%1$s</span>
			</section>',
			esc_html( $text ),
			esc_attr( $text_color ),
			esc_attr( $background_color ),
			esc_attr( $position ),
		);
	}
}
