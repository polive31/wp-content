<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



class WPSSM_Admin {
	
	use Utilities;
	
	/* Local class attributes */
	protected $settings_pages_structure; // Initialized in hydrate_settings
	//protected $active_tab;

	/* File size limits for priority calculation & notifications */
	const SMALL = 1000;
	const LARGE = 1000;
	const MAX = 200000;
	protected $sizes = array('small'=>self::SMALL, 'large'=>self::LARGE, 'max'=>self::MAX );	

 	/* Class arguments */
 	private $args;
 	private $plugin_name;
 	private $plugin_submenu;
 	private $plugin_version;
 	private $form_action;
 	private $nonce;
	private $record;
	private $optimize;
	private $javasync;
		
	/* Objects */ 																									
	protected $Assets;														
	protected $output;														
	protected $update;														


	public function __construct( $args ) {
		WPSSM_Debug::log('*** In WPSSM_Admin __construct ***' );		
  	$this->args = $args;									
  	$this->hydrate_args( $args );		
	}														
														
	public function enqueue_styles_cb() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpssm-admin.css', array(), $this->plugin_version, 'all' );
	}

	public function enqueue_scripts_cb() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpssm-admin.js', array( 'jquery' ), $this->plugin_version, false );
	}
		
	public function init_admin_cb() {
		WPSSM_Debug::log( 'In WPSSM_Admin init_admin_cb()' );								
		if ( !is_admin() ) return;
		require_once plugin_dir_path( dirname(__FILE__) ) . 'assets/class-wpssm-options-assets.php' ;				
		require_once plugin_dir_path( dirname(__FILE__) ) . 'assets/class-wpssm-assets-display.php' ;		
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpssm-admin-output.php' ;	
	
		$this->Assets = 	new WPSSM_Assets_Display( array( 'groupby'=> 'location' ) );
																											
		$this->output = 	new WPSSM_Admin_Output( $this->Assets, 	$this->args);

		// Initialize options settings 
		$this->settings_pages_structure = array(
			'general' => array(
					'slug'=>'general_settings_page',
					'sections'=> array(
							array(
							'slug'=>'general_settings_section', 
							'title'=>'General Settings Section',
							'fields' => array(
										'record' => array(
													'slug' => 'wpssm_record',
													'title' => 'Record enqueued scripts & styles in frontend',
													'callback' => array($this->output,'toggle_switch_recording_cb'),
													),
										'optimize' => array(
													'slug' => 'wpssm_optimize',
													'title' => 'Optimize scripts & styles in frontend',
													'callback' => array($this->output,'toggle_switch_optimize_cb'),
													),	
										'javasync' => array(
													'slug' => 'wpssm_javasync',
													'title' => 'Allow improved asynchronous loading of scripts via javascript',
													'callback' => array($this->output,'toggle_switch_javasync_cb'),
													),	
										),
							),							
							array(
							'slug'=>'general_info_section', 
							'title'=>'General Information',
							'fields' => array(
										'pages' => array(
													'slug' => 'wpssm_recorded_pages',
													'title' => 'Recorded pages',
													'label_for' => 'wpssm-recorded-pages',
													'class' => 'foldable',
													'callback' => array($this->output,'pages_list'),
													),	
										),
							),
					),
			),	
			'scripts' => array(
					'slug'=>'enqueued_scripts_page',
					'sections'=> array(
								array(
								'slug'=>'enqueued_scripts_section', 
								'title'=>'Enqueued Scripts Section',
								'fields' => array(
											'header' => array(
														'slug' => 'wpssm_header_enqueued_scripts',
														'title' => 'Scripts loaded in Header',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-scripts',
														'class' => 'foldable',
														'callback' => array($this->output, 'header_items_list'),
														),
											'footer' => array(
														'slug' => 'wpssm_footer_enqueued_scripts',
														'title' => 'Scripts loaded in Footer',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-scripts',
														'class' => 'foldable',
														'callback' => array($this->output, 'footer_items_list'),
														),
											'async' => array(
														'slug' => 'wpssm_async_enqueued_scripts',
														'title' => 'Scripts loaded Asynchronously',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-scripts',
														'class' => 'foldable',
														'callback' => array($this->output, 'async_items_list'),
														),
											'disabled' => array(
														'slug' => 'wpssm_disabled_scripts',
														'title' => 'Disabed Scripts',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-scripts',
														'class' => 'foldable',
														'callback' => array($this->output, 'disabled_items_list'),
														),											
											)
								)
					),
			),
			'styles' => array(		
					'slug'=>'enqueued_styles_page',
					'sections'=> array(
								array(
								'slug'=>'enqueued_styles_section', 
								'title'=>'Enqueued Styles Section',
								'fields' => array(
											'header' => array(
														'slug' => 'wpssm_header_enqueued_styles',
														'title' => 'Styles loaded in Header',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-styles',
														'class' => 'foldable',
														'callback' => array($this->output, 'header_items_list'),
														),
											'footer' => array(
														'slug' => 'wpssm_footer_enqueued_styles',
														'title' => 'Styles loaded in Footer',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-styles',
														'class' => 'foldable',
														'callback' => array($this->output, 'footer_items_list'),
														),
											'async' => array(
														'slug' => 'wpssm_async_enqueued_styles',
														'title' => 'Styles loaded Asynchronously',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-enqueued-styles',
														'class' => 'foldable',
														'callback' => array($this->output, 'async_items_list'),
														),
											'disabled' => array(
														'slug' => 'wpssm_disabled_styles',
														'title' => 'Disabled Styles',
														'stats' => '(%s files, total size %s)',
														'label_for' => 'wpssm-disabled-styles',
														'class' => 'foldable',
														'callback' => array($this->output, 'disabled_items_list'),
														),											
											),
								),
					),
			),
		);		

		WPSSM_Debug::log('In WPSSM_Admin init_admin(), $this->get_tab()', $this->get_tab() );	

		// Prepare assets to display
		$this->init_settings();						
	}
	

/* MENU OPTION
----------------------------------------------------------*/

	public function add_plugin_menu_option_cb() {
		WPSSM_Debug::log('In add_plugin_menu_option_cb');								
		$page_id = add_submenu_page(
      $this->plugin_submenu,
      'WP Scripts & Styles Manager',
      'Scripts & Styles Manager',
      'manage_options',
      $this->plugin_name,
      array($this, 'output_options_page' )
	    );
	  /* Add hook for admin notice display on page load */  
		add_action( "load-$page_id", array( $this, 'load_option_page_cb' ) );
	}
	
	public function load_option_page_cb() {
		//WPSSM_Debug::log('In load_option_page_cb function');
		if (isset ( $_GET['msg'] ) )
			add_action( 'admin_notices', array ( $this->output, 'admin_notice_cb' ) );
	}



/* SETTINGS INIT FOR OPTION PAGES
----------------------------------------------------------*/
	
	public function init_settings() {
			WPSSM_Debug::log('In WPSSM_Admin init_settings');
			$page = $this->settings_pages_structure[$this->get_tab()];
			WPSSM_Debug::log('=> $this->settings_pages_structure[$this->get_tab()]', $page);
	    // register all settings, sections, and fields
    	foreach ( $page['sections'] as $section ) {
    		WPSSM_Debug::log('register loop - sections', $section );
				add_settings_section(
	        $section['slug'],
	        $section['title'],
	        array($this->output,'section_headline'),
	        $page['slug']
	    	);	
    		foreach ($section['fields'] as $field => $settings) {
    			WPSSM_Debug::log('register loop - fields', array($field => $settings));
    			register_setting($section['slug'], $settings['slug']);
    			if (isset($settings['stats'])) {
    				$count=$this->Assets->get_group_stat($field, 'count');
    				$size=$this->Assets->get_group_stat($field, 'size');
    				$stats=sprintf($settings['stats'],$count,size_format($size));
    			} else $stats='';
    			$label=(isset($settings['label_for']))?$settings['label_for']:'';
    			$class=(isset($settings['class']))?$settings['class']:'';
			    add_settings_field(
			        $settings['slug'],
			        $settings['title'] . ' ' . $stats,
			        $settings['callback'],
			        $page['slug'],
			        $section['slug'],
			        array( 
			        	'label_for' => $label,
			        	'class' => $class)
				  );	    			
		    }      
	    } 	
	}	
	
/* OPTIONS PAGE OUTPUT CALLBACKS
----------------------------------------------------------------*/
	public function output_options_page() {
		$this->output->options_page( $this->settings_pages_structure[$this->get_tab()]['slug'] );
	}



}

