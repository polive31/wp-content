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


		/* Custom menu template */
		//add_filter( 'wpurp_user_menus_form', 'wpurp_custom_menu_template', 10, 2 );

		/* Misc */
		//remove_action ( 'wp_enqueue_scripts', 'WPURP_Assets::enqueue');
		//wp_deregister_script('wpurp_script_minified');
		//wp_enqueue_script( 'wpurp_custom_script', get_stylesheet_directory_uri() . '/assets/js/wpurp_custom.js', array('jquery'), WPURP_VERSION, true );

		//add_action( 'genesis_before_content', array($this,'display_debug_info') );

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
		
		if ( is_admin() ) return $css_enqueue;
			
		if ( is_singular('recipe') ) {
		  $css_enqueue=array(
							array(
		              'url' => self::$_PluginPath . 'assets/css/custom-recipe.css',
		              'public' => true,
		          ),
							/*array(
		              'url' => self::$_PluginPath . 'assets/css/tooltips.css',
		              'public' => true,
		          ),
							array(
		              'url' => '//fonts.googleapis.com/css?family=Oswald|Open+Sans',
		              'public' => true,
		          ),*/				                             
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
		
		elseif ( is_page( ['nouvelle-recette', 'publier-recettes'] ) ) {
			//echo '<pre>' . print_r($css_enqueue,true) . '</pre>';
		  $css_enqueue[]=
							array(
		              'url' => self::$_PluginPath . 'assets/css/custom-recipe-submission.css',
		              'public' => true,
		          ); 
		}
		
		return $css_enqueue;
	}


	public function wpurp_custom_menu_template( $form, $menu ) {
		return '';
	}


	public function enqueue_wpurp_js($js_enqueue) {
		
			if ( is_admin() ) return $js_enqueue;
		
			if ( is_singular('recipe') ) {
				
				$js_enqueue=array();		
				$min_js=self::$_PluginPath . 'assets/js/custom-recipe-tools.min.js';
				
				if ( file_exists( $min_js ) ) {
					$js_enqueue[] = array(
            'name' => 'custom-recipe-tools-minified',
            'url' => $min_js,
            'public' => true,
            'admin' => true,					
					);
				}
				else {
					
				$pause = '<i class="fa fa-pause" aria-hidden="true"></i>';
				$play = '<i class="fa fa-play" aria-hidden="true"></i>';
				$close = '<i class="fa fa-times" aria-hidden="true"></i>';

					
		    $js_enqueue=array(
		            array(
		                'name' => 'fraction',
		                'url' => WPUltimateRecipe::get()->coreUrl . '/vendor/fraction-js/index.js',
		                'public' => true,
		                'admin' => true,
		            ),

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
		                'name' => 'adjustable-servings',
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
		                'name' => 'custom-favorite-recipe',
		                /*'url' => WPUltimateRecipePremium::get()->premiumUrl . '/addons/favorite-recipes/js/favorite-recipes.js',*/
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
		                /*'url' => WPUltimateRecipePremium::get()->premiumUrl . '/js/add-to-shopping-list.js',*/
		                'name' => 'custom-shopping-list',
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
		            array(
		                'name' => 'wpurp-timer',
		                'url' => WPUltimateRecipePremium::get()->premiumUrl . '/addons/timer/js/timer.js',
		                'premium' => true,
		                'public' => true,
		                'deps' => array(
		                    'jquery',
		                ),
		                'data' => array(
		                    'name' => 'wpurp_timer',
		                    'icons' => array(
		                        'pause' => $pause,
		                        'play' => $play,
		                        'close' => $close,
		                    ),
		                )
		            ),		            
		    );	
				}
			}
			
			elseif ( is_page( ['nouvelle-recette', 'publier-recettes'] ) ) {
			 $js_enqueue = $js_enqueue;
			}
			
			else {
				$js_enqueue=array();
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


