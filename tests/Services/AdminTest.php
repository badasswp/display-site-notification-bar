<?php

namespace DisplaySiteNotificationBar\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use DisplaySiteNotificationBar\Services\Admin;

/**
 * @covers \DisplaySiteNotificationBar\Services\Admin::register
 * @covers \DisplaySiteNotificationBar\Services\Admin::register_options_page
 * @covers \DisplaySiteNotificationBar\Services\Admin::register_options_cb
 * @covers \DisplaySiteNotificationBar\Services\Admin::register_options_init
 * @covers \DisplaySiteNotificationBar\Services\Admin::get_sections
 * @covers \DisplaySiteNotificationBar\Services\Admin::get_callback_name
 * @covers \DisplaySiteNotificationBar\Services\Admin::get_options
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
			->with( 'display_site_notification_bar', [] )
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

	public function test_register_options_init() {
		$admin = new Admin();

		\WP_Mock::userFunction( '__' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		\WP_Mock::userFunction( 'register_setting' )
			->with(
				'site-notification-bar-group',
				'display_site_notification_bar',
				[ $admin, 'sanitize_options' ]
			)
			->andReturn( null );

		\WP_Mock::userFunction( 'add_settings_section' )
			->once()
			->with(
				'site-notice-section',
				'Notice Bar Settings',
				null,
				'site-notification-bar'
			)
			->andReturn( null );

		\WP_Mock::expectFilter(
			'display_site_notification_bar_admin_fields',
			[
				[
					'name'    => 'text',
					'label'   => 'Notice Text',
					'cb'      => [ $admin, 'text_cb' ],
					'page'    => 'site-notification-bar',
					'section' => 'site-notice-section',
				],
				[
					'name'    => 'text_color',
					'label'   => 'Notice Text Color',
					'cb'      => [ $admin, 'text_color_cb' ],
					'page'    => 'site-notification-bar',
					'section' => 'site-notice-section',
				],
				[
					'name'    => 'background_color',
					'label'   => 'Notice Background Color',
					'cb'      => [ $admin, 'background_color_cb' ],
					'page'    => 'site-notification-bar',
					'section' => 'site-notice-section',
				],
				[
					'name'    => 'position',
					'label'   => 'Notice Position',
					'cb'      => [ $admin, 'position_cb' ],
					'page'    => 'site-notification-bar',
					'section' => 'site-notice-section',
				],
				[
					'name'    => 'visibility',
					'label'   => 'Notice Visibility',
					'cb'      => [ $admin, 'visibility_cb' ],
					'page'    => 'site-notification-bar',
					'section' => 'site-notice-section',
				],
			]
		);

		\WP_Mock::userFunction( 'add_settings_field' )
			->times( 5 );

		$register = $admin->register_options_init();

		$this->assertNull( $register );
		$this->assertConditionsMet();
	}

	public function test_get_sections() {
		$admin = Mockery::mock( Admin::class )->makePartial();
		$admin->shouldAllowMockingProtectedMethods();

		\WP_Mock::userFunction( '__' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		$sections = $admin->get_sections();

		$this->assertSame(
			$sections,
			[
				[
					'name'  => 'site-notice-section',
					'label' => 'Notice Bar Settings',
				],
			]
		);
	}

	public function test_get_callback_name() {
		$admin = Mockery::mock( Admin::class )->makePartial();
		$admin->shouldAllowMockingProtectedMethods();

		$this->assertSame(
			'name-of-control_cb',
			$admin->get_callback_name( 'name-of-control' )
		);
	}

	public function test_get_options() {
		$admin = Mockery::mock( Admin::class )->makePartial();
		$admin->shouldAllowMockingProtectedMethods();

		$options = [
			[
				'name'    => 'text',
				'label'   => 'Notice Text',
				'cb'      => [ $admin, 'text_cb' ],
				'page'    => 'site-notification-bar',
				'section' => 'site-notice-section',
			],
			[
				'name'    => 'text_color',
				'label'   => 'Notice Text Color',
				'cb'      => [ $admin, 'text_color_cb' ],
				'page'    => 'site-notification-bar',
				'section' => 'site-notice-section',
			],
			[
				'name'    => 'background_color',
				'label'   => 'Notice Background Color',
				'cb'      => [ $admin, 'background_color_cb' ],
				'page'    => 'site-notification-bar',
				'section' => 'site-notice-section',
			],
			[
				'name'    => 'position',
				'label'   => 'Notice Position',
				'cb'      => [ $admin, 'position_cb' ],
				'page'    => 'site-notification-bar',
				'section' => 'site-notice-section',
			],
			[
				'name'    => 'visibility',
				'label'   => 'Notice Visibility',
				'cb'      => [ $admin, 'visibility_cb' ],
				'page'    => 'site-notification-bar',
				'section' => 'site-notice-section',
			],
		];

		\WP_Mock::userFunction( '__' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		\WP_Mock::expectFilter(
			'display_site_notification_bar_admin_fields',
			$options
		);

		$this->assertSame( $options, $admin->get_options() );
		$this->assertConditionsMet();
	}

	public function test_text_cb() {
		\WP_Mock::userFunction( 'esc_attr' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		$response = ( new Admin() )->text_cb();

		$this->expectOutputString(
			'<textarea
				id="text"
				name="display_site_notification_bar[text]"
				rows="5"
				cols="50"
				placeholder="We use cookies on our site..."
			></textarea>'
		);
		$this->assertNull( $response );
		$this->assertConditionsMet();
	}
}
