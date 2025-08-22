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
 * @covers \DisplaySiteNotificationBar\Services\Admin::text_cb
 * @covers \DisplaySiteNotificationBar\Services\Admin::text_color_cb
 * @covers \DisplaySiteNotificationBar\Services\Admin::background_color_cb
 * @covers \DisplaySiteNotificationBar\Services\Admin::position_cb
 * @covers \DisplaySiteNotificationBar\Services\Admin::visibility_cb
 * @covers \DisplaySiteNotificationBar\Services\Admin::sanitize_options
 * @covers \DisplaySiteNotificationBar\Services\Admin::get_settings
 */
class AdminTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();

		\WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		\WP_Mock::userFunction( 'esc_html__' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		\WP_Mock::userFunction( 'esc_html_e' )
			->andReturnUsing(
				function ( $arg ) {
					echo $arg;
				}
			);

		\WP_Mock::userFunction( 'esc_attr' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);
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

		\WP_Mock::userFunction( 'add_menu_page' )
			->with(
				'Display Site Notification Bar',
				'Display Site Notification Bar',
				'manage_options',
				'display-site-notification-bar',
				[ $admin, 'register_options_cb' ],
				'dashicons-align-center',
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
			<h1>Display Site Notification Bar</h1>
			<p>Display a notice bar on your WP home page.</p>
			<form method="post" action="options.php">
								<section id="display-site-notification-bar-group"></section>
										<div id="display-site-notification-bar"></div>
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

		\WP_Mock::userFunction( 'register_setting' )
			->with(
				'display-site-notification-bar-group',
				'display_site_notification_bar',
				[ $admin, 'sanitize_options' ]
			)
			->andReturn( null );

		\WP_Mock::userFunction( 'add_settings_section' )
			->once()
			->with(
				'display-site-notice-section',
				'Notice Bar Settings',
				null,
				'display-site-notification-bar'
			)
			->andReturn( null );

		\WP_Mock::expectFilter(
			'display_site_notification_bar_admin_fields',
			[
				[
					'name'    => 'text',
					'label'   => 'Notice Text',
					'cb'      => [ $admin, 'text_cb' ],
					'page'    => 'display-site-notification-bar',
					'section' => 'display-site-notice-section',
				],
				[
					'name'    => 'text_color',
					'label'   => 'Notice Text Color',
					'cb'      => [ $admin, 'text_color_cb' ],
					'page'    => 'display-site-notification-bar',
					'section' => 'display-site-notice-section',
				],
				[
					'name'    => 'background_color',
					'label'   => 'Notice Background Color',
					'cb'      => [ $admin, 'background_color_cb' ],
					'page'    => 'display-site-notification-bar',
					'section' => 'display-site-notice-section',
				],
				[
					'name'    => 'position',
					'label'   => 'Notice Position',
					'cb'      => [ $admin, 'position_cb' ],
					'page'    => 'display-site-notification-bar',
					'section' => 'display-site-notice-section',
				],
				[
					'name'    => 'visibility',
					'label'   => 'Notice Visibility',
					'cb'      => [ $admin, 'visibility_cb' ],
					'page'    => 'display-site-notification-bar',
					'section' => 'display-site-notice-section',
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

		$sections = $admin->get_sections();

		$this->assertSame(
			$sections,
			[
				[
					'name'  => 'display-site-notice-section',
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
				'page'    => 'display-site-notification-bar',
				'section' => 'display-site-notice-section',
			],
			[
				'name'    => 'text_color',
				'label'   => 'Notice Text Color',
				'cb'      => [ $admin, 'text_color_cb' ],
				'page'    => 'display-site-notification-bar',
				'section' => 'display-site-notice-section',
			],
			[
				'name'    => 'background_color',
				'label'   => 'Notice Background Color',
				'cb'      => [ $admin, 'background_color_cb' ],
				'page'    => 'display-site-notification-bar',
				'section' => 'display-site-notice-section',
			],
			[
				'name'    => 'position',
				'label'   => 'Notice Position',
				'cb'      => [ $admin, 'position_cb' ],
				'page'    => 'display-site-notification-bar',
				'section' => 'display-site-notice-section',
			],
			[
				'name'    => 'visibility',
				'label'   => 'Notice Visibility',
				'cb'      => [ $admin, 'visibility_cb' ],
				'page'    => 'display-site-notification-bar',
				'section' => 'display-site-notice-section',
			],
		];

		\WP_Mock::userFunction( 'esc_html__' )
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

	public function test_text_color_cb() {
		\WP_Mock::userFunction( 'esc_attr' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		$response = ( new Admin() )->text_color_cb();

		$this->expectOutputString(
			'<input
			   type="text"
			   id="text_color"
			   name="display_site_notification_bar[text_color]"
			   placeholder="#FFF"
			   value=""
		   />'
		);
		$this->assertNull( $response );
		$this->assertConditionsMet();
	}

	public function test_background_color_cb() {
		\WP_Mock::userFunction( 'esc_attr' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		$response = ( new Admin() )->background_color_cb();

		$this->expectOutputString(
			'<input
				type="text"
				id="background_color"
				name="display_site_notification_bar[background_color]"
				placeholder="#000"
				value=""
			/>'
		);
		$this->assertNull( $response );
		$this->assertConditionsMet();
	}

	public function test_position_cb() {
		\WP_Mock::userFunction( 'esc_attr' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		$response = ( new Admin() )->position_cb();

		$this->expectOutputString(
			'<select
				id="position"
				name="display_site_notification_bar[position]"
				value=""
			><option value="top" >top</option><option value="bottom" >bottom</option></select>'
		);
		$this->assertNull( $response );
		$this->assertConditionsMet();
	}

	public function test_visibility_cb() {
		$response = ( new Admin() )->visibility_cb();

		$this->expectOutputString(
			'<select
				id="visibility"
				name="display_site_notification_bar[visibility]"
				value=""
			><option value="home" >home</option><option value="all" >all</option></select>'
		);
		$this->assertNull( $response );
		$this->assertConditionsMet();
	}

	public function test_get_settings_uses_default_values_if_plugin_options_not_set() {
		\WP_Mock::userFunction( 'get_option' )
			->with( 'display_site_notification_bar', [] )
			->andReturn( [] );

		\WP_Mock::expectFilter(
			'display_site_notification_bar_settings',
			[
				'text'             => '',
				'text_color'       => '#FFF',
				'background_color' => '#000',
				'position'         => 'bottom',
				'visibility'       => 'home',
			]
		);

		$response = Admin::get_settings();

		$this->assertSame(
			$response,
			[
				'text'             => '',
				'text_color'       => '#FFF',
				'background_color' => '#000',
				'position'         => 'bottom',
				'visibility'       => 'home',
			]
		);
	}

	public function test_get_settings_returns_plugin_options_if_set() {
		\WP_Mock::userFunction( 'get_option' )
			->with( 'display_site_notification_bar', [] )
			->andReturn(
				[
					'text'             => '',
					'background_color' => '#F00',
					'position'         => 'top',
					'visibility'       => 'all',
				]
			);

		\WP_Mock::expectFilter(
			'display_site_notification_bar_settings',
			[
				'text'             => '',
				'text_color'       => '#FFF',
				'background_color' => '#F00',
				'position'         => 'top',
				'visibility'       => 'all',
			]
		);

		$response = Admin::get_settings();

		$this->assertSame(
			$response,
			[
				'text'             => '',
				'text_color'       => '#FFF',
				'background_color' => '#F00',
				'position'         => 'top',
				'visibility'       => 'all',
			]
		);
	}

	public function test_sanitize_options_does_not_sanitize_any_control_if_not_set() {
		$sanitized_options = ( new Admin() )->sanitize_options( [] );

		$this->assertSame( $sanitized_options, [] );
		$this->assertConditionsMet();
	}

	public function test_sanitize_options_sanitizes_only_controls_that_are_set() {
		\WP_Mock::userFunction( 'sanitize_textarea_field' )
			->andReturnUsing(
				function ( $arg ) {
					return $arg;
				}
			);

		$sanitized_options = ( new Admin() )->sanitize_options(
			[
				'text' => 'Lorem ipsum dolor sit amet...',
			]
		);

		$this->assertSame(
			$sanitized_options,
			[
				'text' => 'Lorem ipsum dolor sit amet...',
			]
		);
		$this->assertConditionsMet();
	}
}
