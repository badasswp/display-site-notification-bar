<?php
/**
 * NoticeBar Service.
 *
 * This service manages the registration and
 * binding of the NoticeBar service.
 *
 * @package SiteNotificationBar
 */

namespace SiteNotificationBar\Services;

use SiteNotificationBar\Services\Admin;
use SiteNotificationBar\Abstracts\Service;
use SiteNotificationBar\Interfaces\Kernel;

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

		$site_notice_text             = $settings['site_notice_text'] ?? '';
		$site_notice_text_color       = $settings['site_notice_text_color'] ?? '';
		$site_notice_background_color = $settings['site_notice_background_color'] ?? '';
		$site_notice_position         = $settings['site_notice_position'] ?? '';

		printf(
			'%5$s
			<section class="site-notification-bar" style="%4$s: 0; background: %3$s;">
				<span style="color: %2$s;">%1$s</span>
			</section>',
			esc_html( $site_notice_text ),
			esc_attr( $site_notice_text_color ),
			esc_attr( $site_notice_background_color ),
			esc_attr( $site_notice_position ),
			$this->get_notice_bar_styles()
		);
	}

	/**
	 * Get Notice Bar styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function get_notice_bar_styles(): string {
		return sprintf(
			'<style type="text/css">%s</style>',
			'.site-notification-bar {
				padding: 35px;
				z-index: 999;
				position: fixed;
				text-align: center;
				width: 100vw;
			}'
		);
	}
}
