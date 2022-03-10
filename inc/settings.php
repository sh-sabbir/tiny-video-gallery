<?php

class TVG_Settings {
	private $tvg_settings;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'settings_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'settings_page_init' ) );
	}

	public function settings_add_plugin_page() {
		add_submenu_page(
            'edit.php?post_type=tiny_video_item',
			'Tiny Gallery Settings', // page_title
			'Settings', // menu_title
			'manage_options', // capability
			'settings', // menu_slug
			array( $this, 'tvg_settings_create_admin_page' ) // function
		);
	}

	public function tvg_settings_create_admin_page() {
		$this->tvg_settings = get_option( 'tvg_setting' ); ?>

		<div class="wrap">
			<h2>Tiny Video Gallery Settings</h2>
			<p>Manage Tiny Video Gallery Settings</p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'tvg_settings_option_group' );
					do_settings_sections( 'settings-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function settings_page_init() {
		register_setting(
			'tvg_settings_option_group', // option_group
			'tvg_setting', // option_name
			array( $this, 'settings_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'settings_setting_section', // id
			'', // title
			array( $this, 'settings_section_info' ), // callback
			'settings-admin' // page
		);

		add_settings_field(
			'enable_youtube_auto_sync', // id
			'Enable Youtube Auto Sync', // title
			array( $this, 'enable_youtube_auto_sync_callback' ), // callback
			'settings-admin', // page
			'settings_setting_section' // section
		);

		add_settings_field(
			'youtube_api_key', // id
			'Youtube API Key', // title
			array( $this, 'youtube_api_key_callback' ), // callback
			'settings-admin', // page
			'settings_setting_section' // section
		);

		add_settings_field(
			'data_source', // id
			'Data Source', // title
			array( $this, 'data_source_callback' ), // callback
			'settings-admin', // page
			'settings_setting_section' // section
		);

		add_settings_field(
			'source_id', // id
			'Source ID', // title
			array( $this, 'source_id_callback' ), // callback
			'settings-admin', // page
			'settings_setting_section' // section
		);
	}

	public function settings_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['enable_youtube_auto_sync'] ) ) {
			$sanitary_values['enable_youtube_auto_sync'] = $input['enable_youtube_auto_sync'];
		}

		if ( isset( $input['youtube_api_key'] ) ) {
			$sanitary_values['youtube_api_key'] = sanitize_text_field( $input['youtube_api_key'] );
		}

		if ( isset( $input['data_source'] ) ) {
			$sanitary_values['data_source'] = $input['data_source'];
		}

		if ( isset( $input['source_id'] ) ) {
			$sanitary_values['source_id'] = sanitize_text_field( $input['source_id'] );
		}

		return $sanitary_values;
	}

	public function settings_section_info() {
		echo 'If auto sync is on, Tiny Video Gallery will fetch video from youtube every 4 hours.';
	}

	public function enable_youtube_auto_sync_callback() {
		printf(
			'<input type="checkbox" name="tvg_setting[enable_youtube_auto_sync]" id="enable_youtube_auto_sync" value="1" %s>',
			( isset( $this->tvg_settings['enable_youtube_auto_sync'] ) && $this->tvg_settings['enable_youtube_auto_sync'] === '1' ) ? 'checked' : ''
		);
	}

	public function youtube_api_key_callback() {
		printf(
			'<input class="regular-text" type="text" name="tvg_setting[youtube_api_key]" id="youtube_api_key" value="%s">',
			isset( $this->tvg_settings['youtube_api_key'] ) ? esc_attr( $this->tvg_settings['youtube_api_key']) : ''
		);
	}

	public function data_source_callback() {
		?> <select name="tvg_setting[data_source]" id="data_source">
			<?php $selected = (isset( $this->tvg_settings['data_source'] ) && $this->tvg_settings['data_source'] === 'channel') ? 'selected' : '' ; ?>
			<option value="channel" <?php echo $selected; ?>>Channel</option>
			<?php $selected = (isset( $this->tvg_settings['data_source'] ) && $this->tvg_settings['data_source'] === 'playlist') ? 'selected' : '' ; ?>
			<option value="playlist" <?php echo $selected; ?>>Playlist</option>
		</select> <?php
	}

	public function source_id_callback() {
		printf(
			'<input class="regular-text" type="text" name="tvg_setting[source_id]" id="source_id" value="%s">',
			isset( $this->tvg_settings['source_id'] ) ? esc_attr( $this->tvg_settings['source_id']) : ''
		);
	}

}
if ( is_admin() ){
	$settings = new TVG_Settings();
}

/* 
 * Retrieve this value with:
 * $tvg_settings = get_option( 'tvg_setting' ); // Array of All Options
 * $enable_youtube_auto_sync = $tvg_settings['enable_youtube_auto_sync']; // Enable Youtube Auto Sync
 * $youtube_api_key = $tvg_settings['youtube_api_key']; // Youtube API Key
 * $data_source = $tvg_settings['data_source']; // Data Source
 * $source_id = $tvg_settings['source_id']; // Source ID
 */
