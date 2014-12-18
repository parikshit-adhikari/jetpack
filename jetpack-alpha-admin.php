<?php

class Jp_Alpha_Admin {

	function __construct() {
		add_action( 'admin_init', array( $this, 'register_assets' ) );
		add_action( 'jetpack_admin_menu', array( $this, 'jp_alpha_register_admin_page' ), 12 );
		add_action( 'admin_init', array( $this, 'jp_alpha_save_settings' )  );
	}

	function register_assets() {

		wp_register_style( 'jpalpha-css', JPALPHA__PLUGIN_FILE . 'css/style.css' );

	}

	function enqueue_assets() {

		wp_enqueue_style( 'jpalpha-css' );

	}

	function jp_alpha_register_admin_page() {
		$parent_slug        = 'jetpack';
		$jpalpha_page_title = 'Jetpack Alpha';
		$jpalpha_menu_title = 'Jetpack Alpha';
		$jpalpha_capability = 'manage_options';
		$jpalpha_menu_slug = 'jetpack-alpha';
		$jpalpha_function = array( $this, 'render_jpalpha_main_page' );

		return add_submenu_page(
			$parent_slug,
			$jpalpha_page_title,
			$jpalpha_menu_title,
			$jpalpha_capability,
			$jpalpha_menu_slug,
			$jpalpha_function
		);

	}

	function render_jpalpha_main_page() {
		include JPALPHA__DIR . 'admin/jpalpha-admin-main.php';
	}

	/**
	 * Save our JPS settings options
	 */
	function jp_alpha_save_settings() {

	}

	function updated_success_message() {
		echo '<div id="message" class="updated below-h2"><p>Settings Updated!</p></div>';
	}

}
