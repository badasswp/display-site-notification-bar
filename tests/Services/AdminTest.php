<?php

namespace SiteNotificationBar\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use SiteNotificationBar\Services\Admin;

/**
 * @covers \SiteNotificationBar\Services\Admin::register
 * @covers \SiteNotificationBar\Services\Admin::register_options_page
 * @covers \SiteNotificationBar\Services\Admin::register_options_cb
 */
class AdminTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_register() {
		$admin = new Admin();

		\WP_Mock::expectActionAdded( 'admin_menu', [ $admin, 'register_options_page' ] );
		\WP_Mock::expectActionAdded( 'admin_init', [ $admin, 'register_options_init' ] );

		$register = $admin->register();

		$this->assertNull( $register );
		$this->assertConditionsMet();
	}

	public function test_register_options_page() {
		$admin = new Admin();

		\WP_Mock::userFunction( '__' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		\WP_Mock::userFunction( 'add_menu_page' )
			->with(
				'Site Notification Bar',
				'Site Notification Bar',
				'manage_options',
				'site-notification-bar',
				[ $admin, 'register_options_cb' ],
				'dashicons-align-wide',
				100
			)
			->andReturn( null );

		$register = $admin->register_options_page();

		$this->assertNull( $register );
		$this->assertConditionsMet();
	}

	public function test_register_options_cb() {
		$admin = new Admin();

		\WP_Mock::userFunction( 'get_option' )
			->with( 'site_notification_bar', [] )
			->andReturn( [] );

		\WP_Mock::userFunction( '_e' )
			->andReturnUsing(
				function ( $arg ) {
					echo $arg;
				}
			);

		\WP_Mock::userFunction( 'settings_fields' )
			->andReturnUsing(
				function ( $arg ) {
					?>
					<section id="<?php echo $arg; ?>"></section>
					<?php
				}
			);

		\WP_Mock::userFunction( 'do_settings_sections' )
			->andReturnUsing(
				function ( $arg ) {
					?>
					<div id="<?php echo $arg; ?>"></div>
					<?php
				}
			);

		\WP_Mock::userFunction( 'submit_button' )
			->andReturnUsing(
				function () {
					?>
					<button type="submit">Save Changes</button>
					<?php
				}
			);

		$register = $admin->register_options_cb();

		$this->expectOutputString(
			'		<div class="wrap">
			<h1>Site Notification Bar</h1>
			<p>Display a notice bar on your WP home page.</p>
			<form method="post" action="options.php">
								<section id="site-notification-bar-group"></section>
										<div id="site-notification-bar"></div>
										<button type="submit">Save Changes</button>
								</form>
		</div>
		'
		);
		$this->assertNull( $register );
		$this->assertConditionsMet();
	}
}
