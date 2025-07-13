<?php

namespace SiteNotificationBar\Tests\Services;

use Mockery;
use WP_Mock\Tools\TestCase;
use SiteNotificationBar\Services\NoticeBar;
use SiteNotificationBar\Services\Admin;

/**
 * @covers \SiteNotificationBar\Services\NoticeBar::register
 * @covers \SiteNotificationBar\Services\NoticeBar::display_notice_bar
 */
class NoticeBarTest extends TestCase {
	public function setUp(): void {
		\WP_Mock::setUp();
	}

	public function tearDown(): void {
		\WP_Mock::tearDown();
	}

	public function test_register() {
		$notice_bar = new NoticeBar();

		\WP_Mock::expectActionAdded( 'wp_head', [ $notice_bar, 'display_notice_bar' ] );

		$register = $notice_bar->register();

		$this->assertNull( $register );
		$this->assertConditionsMet();
	}

	public function test_display_notice_bar_uses_settings_values() {
		$notice_bar = new NoticeBar();

		$settings = [
			'site_notice_text'             => 'Hello World!',
			'site_notice_text_color'       => '#FFF',
			'site_notice_background_color' => '#F00',
			'site_notice_position'         => 'top',
			'site_notice_visibility'       => 'home',
		];

		\WP_Mock::userFunction( 'get_option' )
			->once()
			->with( 'site_notification_bar', [] )
			->andReturn( $settings );

		\WP_Mock::expectFilter( 'site_notification_bar_settings', $settings );

		\WP_Mock::userFunction( 'is_home' )
			->andReturn( true );

		\WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing(
				function( $arg ) {
					return $arg;
				}
			);

		\WP_Mock::userFunction( 'esc_attr' )
			->andReturnUsing(
				function( $arg ) {
					return $arg;
				}
			);

		$register = $notice_bar->display_notice_bar();

		$this->expectOutputString(
			'<section class="site-notification-bar" style="top: 0; background: #F00;">
				<span style="color: #FFF;">Hello World!</span>
			</section>'
		);
		$this->assertNull( $register );
		$this->assertConditionsMet();
	}

	public function test_display_notice_bar_uses_default_values_if_not_set_by_user() {
		$notice_bar = new NoticeBar();

		\WP_Mock::userFunction( 'get_option' )
			->once()
			->with( 'site_notification_bar', [] )
			->andReturn( [] );

		\WP_Mock::expectFilter(
			'site_notification_bar_settings',
			[
				'site_notice_text'             => '',
				'site_notice_text_color'       => '#FFF',
				'site_notice_background_color' => '#000',
				'site_notice_position'         => 'bottom',
				'site_notice_visibility'       => 'home',
			]
		);

		\WP_Mock::userFunction( 'is_home' )
			->andReturn( true );

		\WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing(
				function( $arg ) {
					return $arg;
				}
			);

		\WP_Mock::userFunction( 'esc_attr' )
			->andReturnUsing(
				function( $arg ) {
					return $arg;
				}
			);

		$register = $notice_bar->display_notice_bar();

		$this->expectOutputString(
			'<section class="site-notification-bar" style="bottom: 0; background: #000;">
				<span style="color: #FFF;"></span>
			</section>'
		);
		$this->assertNull( $register );
		$this->assertConditionsMet();
	}

	public function test_display_notice_bar_uses_filtered_values() {
		$notice_bar = new NoticeBar();

		\WP_Mock::userFunction( 'get_option' )
			->once()
			->with( 'site_notification_bar', [] )
			->andReturn( [] );

		\WP_Mock::onFilter( 'site_notification_bar_settings' )
			->with(
				[
					'site_notice_text'             => '',
					'site_notice_text_color'       => '#FFF',
					'site_notice_background_color' => '#000',
					'site_notice_position'         => 'bottom',
					'site_notice_visibility'       => 'home',
				]
			)
			->reply(
				[
					'site_notice_text'             => 'Filtered Text',
					'site_notice_text_color'       => '#FF0',
					'site_notice_background_color' => '#F00',
					'site_notice_position'         => 'bottom',
					'site_notice_visibility'       => 'home',
				]
			);

		\WP_Mock::userFunction( 'is_home' )
			->andReturn( true );

		\WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing(
				function( $arg ) {
					return $arg;
				}
			);

		\WP_Mock::userFunction( 'esc_attr' )
			->andReturnUsing(
				function( $arg ) {
					return $arg;
				}
			);

		$register = $notice_bar->display_notice_bar();

		$this->expectOutputString(
			'<section class="site-notification-bar" style="bottom: 0; background: #F00;">
				<span style="color: #FF0;">Filtered Text</span>
			</section>'
		);
		$this->assertNull( $register );
		$this->assertConditionsMet();
	}

	public function test_display_notice_bar_does_not_display_on_all_pages_by_default() {
		$notice_bar = new NoticeBar();

		\WP_Mock::userFunction( 'get_option' )
			->once()
			->with( 'site_notification_bar', [] )
			->andReturn( [] );

		\WP_Mock::expectFilter(
			'site_notification_bar_settings',
			[
				'site_notice_text'             => '',
				'site_notice_text_color'       => '#FFF',
				'site_notice_background_color' => '#000',
				'site_notice_position'         => 'bottom',
				'site_notice_visibility'       => 'home',
			]
		);

		\WP_Mock::userFunction( 'is_home' )
			->andReturn( false );

		\WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing(
				function( $arg ) {
					return $arg;
				}
			);

		\WP_Mock::userFunction( 'esc_attr' )
			->andReturnUsing(
				function( $arg ) {
					return $arg;
				}
			);

		$register = $notice_bar->display_notice_bar();

		$this->expectOutputString( '' );
		$this->assertNull( $register );
		$this->assertConditionsMet();
	}
}
