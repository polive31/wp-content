<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM {
	
	/* Plugin attributes */
	const PLUGIN_NAME = 'wpssm';
	const PLUGIN_VERSION = '1.1.0';
	const PLUGIN_SUBMENU = 'tools.php'; // 'options-general.php'
	
	/* Debug */
	const PLUGIN_DBG = on;
	
	/* Post update */
	const FORM_ACTION = 'wpssm_update_settings';
	const NONCE = 'wp8756';	
		
	/* Plugin load atttributes */
	protected $loader;
	protected $user_notification; 

	/* WPSSM general options attributes */
	protected static $opt_general_settings = array(
									'record'=>'off', 
									'optimize'=>'off', 
									'javasync'=>'off', 
									'wpssm_version'=>self::PLUGIN_VERSION);
									

							
						
	public function __construct() {
		$this->plugin_name = self::PLUGIN_NAME;
		$this->plugin_version = self::PLUGIN_VERSION;
		$this->plugin_submenu = self::PLUGIN_SUBMENU;
		
		$this->_load_dependencies();
		$this->_define_debug_hooks();
		$this->_hydrate();
		$this->_define_admin_hooks();
		$this->_define_public_hooks();
	}

	
	private function _load_dependencies() {
		/* The class responsible for orchestrating the actions and filters of the core plugin */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpssm-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'debug/class-dbg.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wpssm-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wpssm-public.php';
		$this->loader = new WPSSM_Loader();
	}
	
	private function _hydrate() {
		WPSSM_Debug::log('In WPSSM hydrate');
		$this->hydrate_opt( self::$opt_general_settings, 'wpssm_general_settings');
	}
	
	private function _define_debug_hooks() {
		if ( !self::PLUGIN_DBG ) return;
		WPSSM_Debug::log('In define_debug_hooks');
		$plugin_debug = new WPSSM_Debug();
		//$this->loader->add_action( 'wp', 																$plugin_debug, 'init_dbg' 																);
		//set_error_handler( 'WPSSM_Debug::error' );	
	}
	
	
	private function _define_admin_hooks() {
		WPSSM_Debug::log('In define_admin_hooks');														
		$plugin_admin = new WPSSM_Admin();
		$this->loader->add_action( 'admin_menu', 												$plugin_admin, 'init_admin' 															);
		//$this->loader->add_action( 'admin_menu', 												$plugin_admin, 'init_settings' 														);
		$this->loader->add_action( 'admin_menu', 												$plugin_admin, 'add_plugin_menu_option_cb' 								);
		$this->loader->add_action( 'admin_enqueue_scripts', 						$plugin_admin, 'enqueue_scripts' 													);
		$this->loader->add_action( 'admin_enqueue_scripts', 						$plugin_admin, 'enqueue_styles' 													);
		$this->loader->add_action( 'admin_post_' . self::FORM_ACTION, 	$plugin_admin, 'update_settings_cb' 											);
	}
	

	private function _define_public_hooks() {
		WPSSM_Debug::log('In define_public_hooks');
		$plugin_public = new WPSSM_Public();
		$this->loader->add_action( 'wp', 																$plugin_public, 'hydrate' 																);
		
		// manage frontend pages recording 
		if ( self::$opt_general_settings['record'] == 'on' ) {
			$this->loader->add_action( 'wp', 															$plugin_public, 'init_recording' 								);
			$this->loader->add_action( 'wp_head', 												$plugin_public, 'record_header_assets_cb' 								);
			$this->loader->add_action( 'wp_print_footer_scripts', 				$plugin_public, 'record_footer_assets_cb' 								);
		}	

		if ( (self::$opt_general_settings['record']=='off') && (self::$opt_general_settings['optimize']=='on') ) {	
			WPSSM_Debug::log(' OPTIMIZE ACTIVE !!!');
			$this->loader->add_action( 'wp_enqueue_scripts',							$plugin_public, 'apply_scripts_mods_cb', 	PHP_INT_MAX 		);
			$this->loader->add_action( 'wp_enqueue_scripts', 							$plugin_public, 'apply_styles_mods_cb', 	PHP_INT_MAX 		);
			$this->loader->add_action( 'get_footer', 											$plugin_public, 'enqueue_footer_styles_cb' 								);
			$this->loader->add_action( 'script_loader_tag', 							$plugin_public, 'add_async_tag_cb', 			PHP_INT_MAX, 3 	);

			if ( (self::$opt_general_settings['javasync']=='on') ) {	
	    	$this->loader->add_action( 'wp_enqueue_scripts', 						$plugin_public, 'enqueue_scripts' , 1 										);
	  	}

		}
	}			
		
	
	/* SHARED FUNCTIONS 
	--------------------------------------------------*/
	public function run() {
		$this->loader->run();
	}
	
	public function get_loader() {
		return $this->loader;
	}
	
	protected function hydrate_opt( &$attribute, $option ) {
		$get_option = get_option( $option );
		if ( $get_option!=false ) {
			if ( is_array($get_option) )
				foreach ($get_option as $key=>$value) {$attribute[$key]=$value;}
			else
				$attribute = $get_option;
		}
		WPSSM_Debug::log('In WPSSM hydrate_opt option=' . $option, $attribute);
	}
	

}