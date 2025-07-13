<?php
/**
 * Admin Service.
 *
 * This service manages the admin area of the
 * plugin. It registers the plugin option settings.
 *
 * @package SiteNotificationBar
 */

namespace SiteNotificationBar\Services;

use SiteNotificationBar\Abstracts\Service;
use SiteNotificationBar\Interfaces\Kernel;

class Admin extends Service implements Kernel {
	/**
	 * Plugin Options.
	 *
	 * @var array
	 */
	public array $options;

	/**
	 * Plugin Option.
	 *
	 * @var string
	 */
	const PLUGIN_SLUG = 'site-notification-bar';

	/**
	 * Plugin Option.
	 *
	 * @var string
	 */
	const PLUGIN_OPTION = 'site_notification_bar';

	/**
	 * Plugin Group.
	 *
	 * @var string
	 */
	const PLUGIN_GROUP = 'site-notification-bar-group';

	/**
	 * Site Notice Section.
	 *
	 * @var string
	 */
	const SITE_NOTICE_SECTION = 'site_notice_section';

	/**
	 * Site Notice Text.
	 *
	 * @var string
	 */
	const SITE_NOTICE_TEXT = 'site_notice_text';

	/**
	 * Site Notice Background Color.
	 *
	 * @var string
	 */
	const SITE_NOTICE_BACKGROUND_COLOR = 'site_notice_background_color';

	/**
	 * Site Notice Text Color.
	 *
	 * @var string
	 */
	const SITE_NOTICE_TEXT_COLOR = 'site_notice_text_color';

	/**
	 * Site Notice Position.
	 *
	 * @var string
	 */
	const SITE_NOTICE_POSITION = 'site_notice_position';

	/**
	 * Site Notice Visbility.
	 *
	 * @var string
	 */
	const SITE_NOTICE_VISIBILITY = 'site_notice_visibility';

	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_menu', [ $this, 'register_options_page' ] );
		add_action( 'admin_init', [ $this, 'register_options_init' ] );
	}

	/**
	 * Register Options Page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_options_page(): void {
		add_menu_page(
			__( 'Site Notification Bar', 'site-notification-bar' ),
			__( 'Site Notification Bar', 'site-notification-bar' ),
			'manage_options',
			self::PLUGIN_SLUG,
			[ $this, 'register_options_cb' ],
			'dashicons-align-wide',
			100
		);
	}

	/**
	 * Register Options Callback.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_options_cb(): void {
		$this->options = get_option( self::PLUGIN_OPTION, [] );
		?>
		<div class="wrap">
			<h1><?php _e( 'Site Notification Bar', 'site-notification-bar' ); ?></h1>
			<p><?php _e( 'Display a notice bar on your WP home page.', 'site-notification-bar' ); ?></p>
			<form method="post" action="options.php">
			<?php
				settings_fields( self::PLUGIN_GROUP );
				do_settings_sections( self::PLUGIN_SLUG );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register Options Init.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_options_init(): void {
		register_setting(
			self::PLUGIN_GROUP,
			self::PLUGIN_OPTION,
			[ $this, 'sanitize_options' ]
		);

		foreach ( $this->get_sections() as $section ) {
			add_settings_section(
				$section['name'] ?? '',
				$section['label'] ?? '',
				null,
				self::PLUGIN_SLUG
			);
		}

		foreach ( $this->get_options() as $option ) {
			if ( ! isset( $option['name'] ) || ! isset( $option['cb'] ) || ! is_callable( $option['cb'] ) ) {
				continue;
			}

			add_settings_field(
				$option['name'] ?? '',
				$option['label'] ?? '',
				$option['cb'],
				$option['page'] ?? '',
				$option['section'] ?? ''
			);
		}
	}

	/**
	 * Get Form Sections.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	protected function get_sections(): array {
		return [
			[
				'name'  => self::SITE_NOTICE_SECTION,
				'label' => __( 'Notice Bar Settings', 'site-notification-bar' ),
			],
		];
	}

	/**
	 * Get Callback name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Form Control name.
	 * @return string
	 */
	protected function get_callback_name( $name ): string {
		return sprintf( '%s_cb', $name );
	}

	/**
	 * Get Plugin Options.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	protected function get_options(): array {
		$options = [
			[
				'name'    => self::SITE_NOTICE_TEXT,
				'label'   => __( 'Notice Text', 'site-notification-bar' ),
				'cb'      => [ $this, $this->get_callback_name( self::SITE_NOTICE_TEXT ) ],
				'page'    => self::PLUGIN_SLUG,
				'section' => self::SITE_NOTICE_SECTION,
			],
			[
				'name'    => self::SITE_NOTICE_TEXT_COLOR,
				'label'   => __( 'Notice Text Color', 'site-notification-bar' ),
				'cb'      => [ $this, $this->get_callback_name( self::SITE_NOTICE_TEXT_COLOR ) ],
				'page'    => self::PLUGIN_SLUG,
				'section' => self::SITE_NOTICE_SECTION,
			],
			[
				'name'    => self::SITE_NOTICE_BACKGROUND_COLOR,
				'label'   => __( 'Notice Background Color', 'site-notification-bar' ),
				'cb'      => [ $this, $this->get_callback_name( self::SITE_NOTICE_BACKGROUND_COLOR ) ],
				'page'    => self::PLUGIN_SLUG,
				'section' => self::SITE_NOTICE_SECTION,
			],
			[
				'name'    => self::SITE_NOTICE_POSITION,
				'label'   => __( 'Notice Position', 'site-notification-bar' ),
				'cb'      => [ $this, $this->get_callback_name( self::SITE_NOTICE_POSITION ) ],
				'page'    => self::PLUGIN_SLUG,
				'section' => self::SITE_NOTICE_SECTION,
			],
			[
				'name'    => self::SITE_NOTICE_VISIBILITY,
				'label'   => __( 'Notice Visibility', 'site-notification-bar' ),
				'cb'      => [ $this, $this->get_callback_name( self::SITE_NOTICE_VISIBILITY ) ],
				'page'    => self::PLUGIN_SLUG,
				'section' => self::SITE_NOTICE_SECTION,
			],
		];

		/**
		 * Filter Option Fields.
		 *
		 * @since 1.0.0
		 *
		 * @param mixed[] $options Option Fields.
		 * @return mixed[]
		 */
		return apply_filters( 'site_notification_bar_admin_fields', $options );
	}

	/**
	 * Site Notice Textarea Callback.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function site_notice_text_cb(): void {
		printf(
			'<textarea
				id="%2$s"
				name="%1$s[%2$s]"
				rows="5"
				cols="50"
				placeholder="We use cookies on our site..."
			>%3$s</textarea>',
			esc_attr( self::PLUGIN_OPTION ),
			esc_attr( self::SITE_NOTICE_TEXT ),
			esc_attr( $this->options[ self::SITE_NOTICE_TEXT ] ?? '' )
		);
	}

	/**
	 * Site Notice Text Color Callback.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function site_notice_text_color_cb(): void {
		printf(
			'<input
			   type="text"
			   id="%2$s"
			   name="%1$s[%2$s]"
			   placeholder="#FFF"
			   value="%3$s"
		   />',
			esc_attr( self::PLUGIN_OPTION ),
			esc_attr( self::SITE_NOTICE_TEXT_COLOR ),
			esc_attr( $this->options[ self::SITE_NOTICE_TEXT_COLOR ] ?? '' )
		);
	}

	/**
	 * Site Notice Background Color Callback.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function site_notice_background_color_cb(): void {
		printf(
			'<input
				type="text"
				id="%2$s"
				name="%1$s[%2$s]"
				placeholder="#000"
				value="%3$s"
			/>',
			esc_attr( self::PLUGIN_OPTION ),
			esc_attr( self::SITE_NOTICE_BACKGROUND_COLOR ),
			esc_attr( $this->options[ self::SITE_NOTICE_BACKGROUND_COLOR ] ?? '' )
		);
	}

	/**
	 * Site Notice Position Callback.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function site_notice_position_cb(): void {
		$positions = '';

		foreach ( [ 'top', 'bottom' ] as $position ) {
			$selected = '';

			if ( ( $this->options[ self::SITE_NOTICE_POSITION ] ?? '' ) === $position ) {
				$selected = 'selected';
			}

			$positions .= sprintf(
				'<option value="%1$s" %2$s>%1$s</option>',
				esc_attr( $position ),
				esc_attr( $selected ),
			);
		}

		printf(
			'<select
				id="%2$s"
				name="%1$s[%2$s]"
				value="%3$s"
			>%4$s</select>',
			esc_attr( self::PLUGIN_OPTION ),
			esc_attr( self::SITE_NOTICE_POSITION ),
			esc_attr( $this->options[ self::SITE_NOTICE_POSITION ] ?? '' ),
			$positions
		);
	}

	/**
	 * Site Notice Visibility Callback.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function site_notice_visibility_cb(): void {
		$pages = '';

		foreach ( [ 'home', 'all' ] as $page ) {
			$selected = '';

			if ( ( $this->options[ self::SITE_NOTICE_VISIBILITY ] ?? '' ) === $page ) {
				$selected = 'selected';
			}

			$pages .= sprintf(
				'<option value="%1$s" %2$s>%1$s</option>',
				esc_attr( $page ),
				esc_attr( $selected ),
			);
		}

		printf(
			'<select
				id="%2$s"
				name="%1$s[%2$s]"
				value="%3$s"
			>%4$s</select>',
			esc_attr( self::PLUGIN_OPTION ),
			esc_attr( self::SITE_NOTICE_VISIBILITY ),
			esc_attr( $this->options[ self::SITE_NOTICE_VISIBILITY ] ?? '' ),
			$pages
		);
	}

	/**
	 * Sanitize Options.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $input Plugin Options.
	 * @return mixed[]
	 */
	public function sanitize_options( $input ): array {
		$sanitized_options = [];

		if ( isset( $input[ self::SITE_NOTICE_TEXT ] ) ) {
			$input_data = trim( (string) $input[ self::SITE_NOTICE_TEXT ] );

			$sanitized_options[ self::SITE_NOTICE_TEXT ] = sanitize_textarea_field( $input_data );
		}

		if ( isset( $input[ self::SITE_NOTICE_TEXT_COLOR ] ) ) {
			$input_data = trim( (string) $input[ self::SITE_NOTICE_TEXT_COLOR ] );

			$sanitized_options[ self::SITE_NOTICE_TEXT_COLOR ] = sanitize_text_field( $input_data );
		}

		if ( isset( $input[ self::SITE_NOTICE_BACKGROUND_COLOR ] ) ) {
			$input_data = trim( (string) $input[ self::SITE_NOTICE_BACKGROUND_COLOR ] );

			$sanitized_options[ self::SITE_NOTICE_BACKGROUND_COLOR ] = sanitize_text_field( $input_data );
		}

		if ( isset( $input[ self::SITE_NOTICE_POSITION ] ) ) {
			$input_data = trim( (string) $input[ self::SITE_NOTICE_POSITION ] );

			$sanitized_options[ self::SITE_NOTICE_POSITION ] = sanitize_text_field( $input_data );
		}

		if ( isset( $input[ self::SITE_NOTICE_VISIBILITY ] ) ) {
			$input_data = trim( (string) $input[ self::SITE_NOTICE_VISIBILITY ] );

			$sanitized_options[ self::SITE_NOTICE_VISIBILITY ] = sanitize_text_field( $input_data );
		}

		return $sanitized_options;
	}

	/**
	 * Get Settings.
	 *
	 * Ensure graceful fallback for unset values
	 * when plugin is first installed.
	 *
	 * @return mixed[]
	 */
	public static function get_settings(): array {
		$settings = get_option( self::PLUGIN_OPTION, [] );

		if ( empty( $settings[ self::SITE_NOTICE_TEXT ] ) ) {
			$settings[ self::SITE_NOTICE_TEXT ] = '';
		}

		if ( empty( $settings[ self::SITE_NOTICE_TEXT_COLOR ] ) ) {
			$settings[ self::SITE_NOTICE_TEXT_COLOR ] = '#FFF';
		}

		if ( empty( $settings[ self::SITE_NOTICE_BACKGROUND_COLOR ] ) ) {
			$settings[ self::SITE_NOTICE_BACKGROUND_COLOR ] = '#000';
		}

		if ( empty( $settings[ self::SITE_NOTICE_POSITION ] ) ) {
			$settings[ self::SITE_NOTICE_POSITION ] = 'bottom';
		}

		if ( empty( $settings[ self::SITE_NOTICE_VISIBILITY ] ) ) {
			$settings[ self::SITE_NOTICE_VISIBILITY ] = 'home';
		}

		return apply_filters(
			'site_notification_bar_settings',
			[
				self::SITE_NOTICE_TEXT             => $settings[ self::SITE_NOTICE_TEXT ] ?? '',
				self::SITE_NOTICE_TEXT_COLOR       => $settings[ self::SITE_NOTICE_TEXT_COLOR ] ?? '',
				self::SITE_NOTICE_BACKGROUND_COLOR => $settings[ self::SITE_NOTICE_BACKGROUND_COLOR ] ?? '',
				self::SITE_NOTICE_POSITION         => $settings[ self::SITE_NOTICE_POSITION ] ?? '',
				self::SITE_NOTICE_VISIBILITY       => $settings[ self::SITE_NOTICE_VISIBILITY ] ?? '',
			]
		);
	}
}
