<?php
/**
 * Asset Service.
 *
 * This service manages the asset service
 * that loads styles and scripts.
 *
 * @package DisplaySiteNotificationBar
 */

namespace DisplaySiteNotificationBar\Services;

use DisplaySiteNotificationBar\Abstracts\Service;
use DisplaySiteNotificationBar\Interfaces\Kernel;

class Asset extends Service implements Kernel {
	/**
	 * Asset name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected static $name = 'display-site-notification-bar';

	/**
	 * Register Asset.
	 *
	 * Register implementation for the front-end
	 * assets here.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
	}

	/**
	 * Enqueue Frontend assets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_frontend_assets(): void {
		wp_enqueue_style(
			sprintf( '%s-styles', static::$name ),
			plugin_dir_url( __FILE__ ) . '../../styles.css',
			[],
			'1.0.0'
		);
	}
}
