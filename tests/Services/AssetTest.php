<?php

namespace SiteNotificationBar\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use SiteNotificationBar\Services\Asset;

/**
 * @covers \SiteNotificationBar\Services\Asset::register
 */
class AssetTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_register() {
		$asset = new Asset();

		\WP_Mock::expectActionAdded( 'wp_enqueue_scripts', [ $asset, 'enqueue_frontend_assets' ] );

		$register = $asset->register();

		$this->assertNull( $register );
		$this->assertConditionsMet();
	}

	public function test_enqueue_frontend_assets() {
		$reflection = new \ReflectionClass( Asset::class );

		\WP_Mock::userFunction( 'plugin_dir_url' )
			->once()
			->with( $reflection->getFileName() )
			->andReturn( 'https://example.com/wp-content/plugins/site-notification-bar/inc/Services/' );

		\WP_Mock::userFunction( 'wp_enqueue_style' )
			->once()
			->with(
				'site-notification-bar-styles',
				'https://example.com/wp-content/plugins/site-notification-bar/inc/Services/../../styles.css',
				[],
				'1.0.0'
			)
			->andReturn( null );

		$response = ( new Asset() )->enqueue_frontend_assets();

		$this->assertNull( $response );
		$this->assertConditionsMet();
	}
}
