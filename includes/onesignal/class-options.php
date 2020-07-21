<?php
/**
 * Options for AppPresser OneSignal
 *
 * @package  AppPresser OneSignal
 */

namespace AppPresser\OneSignal;

class Options {
	/**
	 * The option name to save settings.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'appp_onesignal';

	/**
	 * Holds the values to be used in the fields callbacks.
	 *
	 * @var array
	 */
	private $options = [];

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_options_page' ] );
		add_action( 'admin_init', [ $this, 'page_init' ] );
	}

	/**
	 * Add options page
	 */
	public function add_options_page() {
		// This page will be under "Settings"
		add_options_page(
			esc_html__( 'AppPresser OneSignal', 'apppresser-onesignal' ),
			esc_html__( 'AppPresser OneSignal', 'apppresser-onesignal' ),
			'manage_options',
			self::OPTION_NAME,
			[
				$this,
				'create_admin_page',
			]
		);
	}

	/**
	 * Options page callback.
	 *
	 * @return void
	 */
	public function create_admin_page() {
		// Set class property
		$this->options = get_option( self::OPTION_NAME );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'AppPresser OneSignal', 'apppresser-onesignal' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				// This prints out all hidden setting fields.
				settings_fields( self::OPTION_NAME );
				do_settings_sections( 'my-setting-admin' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings.
	 *
	 * @return void
	 */
	public function page_init() {
		register_setting(
			self::OPTION_NAME,
			self::OPTION_NAME,
			[
				$this,
				'sanitize',
			]
		);

		add_settings_section(
			self::OPTION_NAME, // ID
			'My Custom Settings', // Title
			[
				$this,
				'print_section_info',
			], // Callback
			'my-setting-admin' // Page
		);  

		add_settings_field(
			'id_number', // ID
			'ID Number', // Title 
			[
				$this,
				'id_number_callback',
			], // Callback
			'my-setting-admin', // Page
			'setting_section_id' // Section           
		);      

		add_settings_field(
			'title',
			'Title',
			[
				$this,
				'title_callback',
			],
			'my-setting-admin',
			'setting_section_id'
		);      
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		$new_input = array();

		if ( isset( $input['id_number'] ) ) {
			$new_input['id_number'] = absint( $input['id_number'] );
		}

		if ( isset( $input['title'] ) ) {
			$new_input['title'] = sanitize_text_field( $input['title'] );
		}

		return $new_input;
	}

	/** 
	 * Print the Section text.
	 *
	 * @return void
	 */
	public function print_section_info() {
		echo 'Enter your settings below:';
	}

	/** 
	 * Get the settings option array and print one of its values.
	 *
	 * @return void
	 */
	public function id_number_callback() {
		printf(
			'<input type="text" id="id_number" name="appp_onesignal[id_number]" value="%s" />',
			isset( $this->options['id_number'] ) ? esc_attr( $this->options['id_number']) : ''
		);
	}

	/** 
	 * Get the settings option array and print one of its values
	 *
	 * @return void
	 */
	public function title_callback() {
		printf(
			'<input type="text" id="title" name="my_option_name[title]" value="%s" />',
			isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
		);
	}
}