<?php
class Bookwize_Integrated_Settings {

	// A config array containing the required options for connection
	public $settings = [ ];

	// Add Wp Action Hooks
	public function __construct() {

		$bookwize_languages = new Bookwize_Integrated_Languages();
		$languages          = $bookwize_languages->get_languages();

		$extra_fields = [
			[
				'bw_theme', 'Default Theme','select', 'eg. c', 
				[
					'options' => [
						'a' => 'a',
						'b' => 'b',
						'c' => 'c',
						'f' => 'f'
					],
					'default' => 'c'
				]
			],
			['bw_currency', 'Default Currency', 'text', 'eg. EUR' ],
			['bw_debug', 'Debug', 'checkbox', '<b>Caution:</b> In DEBUG mode Adwords Tracking is disabled.'],
			['bw_preload', 'Preload scripts', 'checkbox', 'Preload critical scripts IBE/vendors'],					
			['bw_header', 'Display Header?', 'checkbox', ''],
			['bw_group_text', 'Display Group Text?', 'checkbox', '<b>Group Text</b> is displayed above checkin & checkout. '],
			['bw_primary_color', 'Primary Color ', 'color'],
			['bw_secondary_color', 'Secondary Color ', 'color'],
			['bw_header_color', 'Header Color ', 'color'],
			['bw_enable_jcc', 'Enable Jcc', 'checkbox'],
		];

		

		$is_jcc_active = get_option('bw_enable_jcc') === "1";

		if($is_jcc_active) {
			$extra_fields[] = ['bw_jcc_password', 'JCC password ', 'password', 'JCC password'];
			$extra_fields[] = ['bw_jcc_merchant', 'JCC merchant ', 'password', 'JCC merchant'];
			$extra_fields[] = ['bw_jcc_acquirer', 'JCC acquirer ', 'password', 'JCC acquirer'];
		}
		
		$this->settings = [
			[
				'name'     => 'auth',
				'section'  => [ 'auth_section', 'Authentication', 'Credentials for Bookwize' ],
				'settings' => [
					[ 'bw_hotelId', 'Hotel ID', 'text', 'Hotel Id' ],
					[ 'bw_apiKey', 'API KEY', 'text', 'API Key' ]
				]
			],
			[
				'name'     => 'frontend',
				'section'  => [ 'frontend_section', 'Front End Configuration' ],
				'settings' => $extra_fields,
			]
		];


	}

	// Creates Bookwize Settings Page
	public function admin_menu() {

		add_menu_page(
			'Settings',
			'Bookwize Cinnamon',
			'manage_options',
			'bookwize-integrated-cinnamon',
			[ &$this, 'render_options_page' ]
		);
	}

	// Sets Social Media Connection Settings
	public function admin_init() {

		$this->set_settings( $this->settings );

		flush_rewrite_rules();
	}

	public function set_settings( $settings ) {

		foreach ( $settings as $setting ) {
			$section = $setting['section'];
			add_settings_section( $section[0], $section[1], [ &$this, 'render_settings_section' ], 'bookwize-integrated-cinnamon' );

			foreach ( $setting['settings'] as $option ) {

				add_settings_field(
					$option[0], $option[1], [ &$this, 'render_settings_fields' ],
					'bookwize-integrated-cinnamon',
					$section[0],
					$option
				);

				register_setting( 'bookwize-integrated-cinnamon', $option[0] );
			}
		}
	}

	// Render Content for Bookwize IntegratedSettings Page
	public function render_options_page() {

		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/bookwize-integrated-cinnamon-admin-settings.php';
	}

	// Render input fields foreach registered setting
	public function render_settings_fields( $field ) {
		switch ( $field[2] ) {
			case 'checkbox':
				echo '<input name="' . $field[0] . '" id="' . $field[0] . '" type="checkbox" value="1" class="code" ' . checked( 1, get_option( $field[0] ), false );
				$this->_print_attrs( $field );
				echo '/>';
				break;
			case 'select':
				echo '<select  name="' . $field[0] . '" id="' . $field[0] . '" class="code">';
				if ( isset( $field[4]['options'] ) ) {
					$default =  isset($field[4]['default']) && !empty($field[4]['default']) ? $field[4]['default'] : false; 
					foreach ( $field[4]['options'] as $value => $option ) {
						echo '<option value="' . $value . '"' . selected( $value, get_option( $field[0] ,$default ) ) . '>' . $option . '</option>';
					}
				}
				echo '</select>';
				break;
			case 'textarea' :
				echo '<textarea class="regular-text code" cols="40" rows="7" name="' . $field[0] . '" id="' . $field[0] . '" ' . $this->_print_attrs( $field ) . '>'
				     . get_option( $field[0] )
				     . '</textarea>';
				break;
			case 'password' :
				
				echo '<input class="regular-text code" name="' . $field[0] . '" id="' . $field[0] . '" type="password" value="' . get_option( $field[0] ) . '"';
				$this->_print_attrs( $field );
				echo '/>';
				break;
			default :
				echo '<input class="regular-text code" name="' . $field[0] . '" id="' . $field[0] . '" type="text" value="' . get_option( $field[0] ) . '"';
				$this->_print_attrs( $field );
				echo '/>';
				break;
		}

		// display extra info texts
		foreach ( $this->settings as $settings ) {
			foreach ( $settings['settings'] as $option ) {
				if ( $option[0] == $field[0] && isset( $option[3] ) ) {
					echo '<p class="description">' . $option[3] . '</p>';
				}
			}
		}
	}

	public function render_settings_section( $field ) {
		// display extra info texts
		foreach ( $this->settings as $settings ) {
			if ( $settings['section'][0] == $field['id'] && isset( $settings['section'][2] ) ) {
				echo '<p class="description">' . $settings['section'][2] . '</p>';
			}
		}
	}

	public function show_flash_message( $message = 'Updated' ) {
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/bookwize-integrated-admin-flash-message.php';
	}

	public function render_options_subpage_settings() {
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/bookwize-integrated-cinnamon-admin-settings.php';
	}

	public function render_options_subpage_localization() {

		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/bookwize-admin-localization.php';
	}

	protected function _print_attrs( $field ) {
		if ( isset( $field[4] ) && is_array( $field[4] ) ) {
			foreach ( $field[4] as $key => $value ) {
				echo ' ' . $key . '="' . esc_attr( $value ) . '"';
			}
		}
	}
}
