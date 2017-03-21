<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_Recipe_Templates {
	
	protected static $_PluginPath;	
	
	public function __construct() {
		
		self::$_PluginPath = plugin_dir_url( dirname( __FILE__ ) );
		
		/* Load javascript styles */
		add_filter ( 'wpurp_assets_js', array($this,'enqueue_wpurp_js'), 15, 1 );
		/* Load stylesheets */
		add_filter ( 'wpurp_assets_css', array($this,'enqueue_wpurp_css'), 15, 1 );

		/* Custom tooltips */
		add_action( 'wp_enqueue_scripts', array($this,'load_tooltips_styles') );
		
		/* Custom menu template */
		//add_filter( 'wpurp_user_menus_form', 'wpurp_custom_menu_template', 10, 2 );


		/* Misc */
		//remove_action ( 'wp_enqueue_scripts', 'WPURP_Assets::enqueue');
		//wp_deregister_script('wpurp_script_minified');
		//wp_enqueue_script( 'wpurp_custom_script', get_stylesheet_directory_uri() . '/assets/js/wpurp_custom.js', array('jquery'), WPURP_VERSION, true );

		add_action( 'genesis_before_content', array($this,'display_debug_info') );

	}
	
	public function load_tooltips_styles() {
		wp_enqueue_style( 'google-font-oswald', '//fonts.googleapis.com/css?family=Oswald', array(), CHILD_THEME_VERSION );
		wp_enqueue_style( 'tooltips-stylesheet', self::$_PluginPath . 'assets/css/tooltips.css', array(), CHILD_THEME_VERSION );
	}

	
	
	/* Output debug information 
	--------------------------------------------------------------*/	
	public function dbg( $msg, $var ) {
			if ( class_exists('PC') ) {
				//PC::debug(array( $msg => $var ) );
			}
	}

	public function display_debug_info() {
				
			//$this->dbg('In WPURP Custom Custom Templates Class', '');
			//$this->dbg('Plugin path', self::$_PluginPath);
	}	
	
	public function enqueue_wpurp_css($css_enqueue) {
				
		//$this->dbg('In Enqueue WPURP CSS', '');
		//$this->dbg('Plugin path', self::$_PluginPath);
			
		if ( is_singular('recipe') ) {
		  $css_enqueue=array(
							array(
		              'url' => WPUltimateRecipe::get()->coreUrl . '/css/admin.css',
		              'admin' => true,
		          ),
							array(
		              'url' => self::$_PluginPath . 'assets/css/custom-recipe.css',
		              'public' => true,
		          ),
							array(
		              'url' => self::$_PluginPath . 'assets/css/tooltips.css',
		              'public' => true,//fonts.googleapis.com/css?family=Oswald'
		          ),
							array(
		              'url' => '//fonts.googleapis.com/css?family=Oswald',
		              'public' => true,
		          ),
			);
		}
		elseif ( is_page( 'menus' ) ) { // Menu page
		  $css_enqueue=array(
							array(
		              'url' => self::$_PluginPath . 'assets/css/custom-menu.css',
		              'public' => true,
		          ),
			);		
		}
		elseif ( is_page( 'nouvelle-recette', 'mes-recettes' ) ) { // Menu page
		  $css_enqueue=array(
				array(
            'url' => self::$_PluginPath . 'assets/css/custom-recipe-submission.css',
            'public' => true,
        ),
				array(
            'url' => WPUltimateRecipe::get()->coreUrl . '/vendor/select2/select2.css',
            'public' => true,
        ),
			);
		}
		else {
			$css_enqueue='';
		}
		return $css_enqueue;
	}


	public function wpurp_custom_menu_template( $form, $menu ) {
		return '';
	}


	public function enqueue_wpurp_js($js_enqueue) {
		
		
			if ( is_singular('recipe') && !is_admin() ) {
				
		    $js_enqueue=array(
		            array(
		                'name' => 'fraction',
		                'url' => WPUltimateRecipe::get()->coreUrl . '/vendor/fraction-js/index.js',
		                'public' => true,
		                'admin' => true,
		            ),
		            /*array(
		                'url' => WPUltimateRecipe::get()->coreUrl . '/vendor/jquery.tools.min.js',
		                'public' => true,
		                'deps' => array(
		                    'jquery',
		                ),
		            ),*/
		            array(
		                'name' => 'print_button',
		                'url' => WPUltimateRecipe::get()->coreUrl . '/js/print_button.js',
		                'public' => true,
		                'deps' => array(
		                    'jquery',
		                ),
		                'data' => array(
		                    'name' => 'wpurp_print',
		                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
		                    'nonce' => wp_create_nonce( 'wpurp_print' ),
		                    'custom_print_css_url' => get_stylesheet_directory_uri() . '/assets/css/custom-recipe-print.css',
		                    'coreUrl' => WPUltimateRecipe::get()->coreUrl,
		                    'premiumUrl' => WPUltimateRecipe::is_premium_active() ? WPUltimateRecipePremium::get()->premiumUrl : false,
		                    'title' => __('Print this Recipe','foodiepro'),
		                    'permalinks' => get_option('permalink_structure'),
		                ),
		            ),
		    	      array(
		                'url' => WPUltimateRecipe::get()->coreUrl . '/js/adjustable_servings.js',
		                'public' => true,
		                'deps' => array(
		                    'jquery',
		                    'fraction',
		                		'print_button',
		                ),
		                'data' => array(
		                    'name' => 'wpurp_servings',
		                    'precision' => 1,
		                    'decimal_character' => ',',
		                ),
		            ),
								array(
		                'url' => self::$_PluginPath . 'assets/js/custom_favorite_recipe.js',
		               	'premium' => true,
		                'public' => true,
		                'setting' => array( 'favorite_recipes_enabled', '1' ),
		                'deps' => array(
		                    'jquery',
		                ),
		                'data' => array(
		                    'name' => 'wpurp_favorite_recipe',
		                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
		                    'nonce' => wp_create_nonce( 'wpurp_favorite_recipe' ),
		                )
		            ),	  
		            array(
		                'url' => self::$_PluginPath . 'assets/js/custom_shopping_list.js',
		                'premium' => true,
		                'public' => true,
		                'deps' => array(
		                    'jquery',
		                ),
		                'data' => array(
		                    'name' => 'wpurp_add_to_shopping_list',
		                    'ajaxurl' => WPUltimateRecipe::get()->helper('ajax')->url(),
		                    'nonce' => wp_create_nonce( 'wpurp_add_to_shopping_list' ),
		                )
		            ),
		    );	
			}
			elseif (is_page( 'nouvelle-recette', 'mes-recettes') ) {
				$js_enqueue=$js_enqueue;
			}
			
			else {
				$js_enqueue='';
			}
		  
		return $js_enqueue;
	}

	
	public static function output_tooltip($content,$position) {
		$path = self::$_PluginPath . 'assets/img/callout_'. $position . '.png';
	
		$html ='<div class="tooltip-content">';
		$html.= '<div class="wrap">';
		$html.=$content;
		$html.='<img class="callout" data-no-lazy="1" src="' . $path . '">';
		$html.='</div>';
		$html.='</div>';
		
		return $html;
	}

	
}

