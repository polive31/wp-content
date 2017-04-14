<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class JCO_Settings {
	
	protected $menu_slug = 'js_css_optimization';
	protected $form_action = 'jco_update_settings';
	protected $nonce = 'wp8756';
	protected $urls_to_request; 
	
	public function __construct() {
		
		// Admin options page
		add_action( 'admin_menu', array($this, 'add_js_css_menu_option'));
		add_action( 'admin_init', array($this, 'jco_settings_init') );
		//add_action( 'admin_post_$this->action', array ( $this, 'jco_update_settings_cb' ) );
		add_action( 'admin_post_' . $this->form_action, array ( $this, 'jco_update_settings_cb' ) );
	
		// load css styles for this page
    add_action( 'admin_enqueue_scripts', array($this,'load_admin_styles') );
		
		$this->urls_to_request = array(
			home_url(), 
			$this->get_permalink_by_slug('bredele'), 
			$this->get_permalink_by_slug('les-myrtilles'), 
		);

		if ( get_option( 'jco_enqueue_recording' ) == 'on' ) {
			add_action( 'wp_head', array($this, 'save_header_scripts') );
			add_action( 'wp_footer', array($this, 'save_footer_scripts') );
		}
		else {
			remove_action( 'wp_head', array($this, 'save_header_scripts') );
			remove_action( 'wp_footer', array($this, 'save_footer_scripts') );
		}		
		
	}

	public function load_admin_styles() {
		PC::debug('In load_admin_styles');
		PC::debug( plugins_url( '/css/jco_options_page.css', __FILE__ ) );
		
  	wp_enqueue_style( 'jco_admin_css', plugins_url( '../css/jco_options_page.css', __FILE__ ) , false, '1.0.0' );
	}  

	public function add_js_css_menu_option() {
		$option_page_id = add_submenu_page(
      'tools.php',
      'JS & CSS Optimization',
      'JS & CSS Optimization',
      'manage_options',
      $this->menu_slug,
      array($this, 'output_options_page')
	    );
	    
		add_action( "load-$option_page_id", array ( $this, 'load_option_page_cb' ) );
	}
	
	
	public function jco_settings_init() {
	    // register settings
	    register_setting('enqueued_list_options', 'jco_enqueued_scripts');
	    register_setting('enqueued_list_options', 'jco_enqueued_styles');
	    register_setting('enqueued_list_options', 'jco_enqueue_recording');
	 
	    // register "general settings" section
	    add_settings_section(
	        'general_settings_section',
	        'General Settings Section',
	        array($this,'output_section_cb'),
	        'js_css_optimization'
	    );

	    // register "enqueued list" section
	    add_settings_section(
	        'enqueued_list_section',
	        'Enqueued Scripts & Styles Section',
	        array($this,'output_section_cb'),
	        'js_css_optimization'
	    );
	 
	    // register new fields in the general settings section
	    add_settings_field(
	        'jco_enqueue_recording',
	        'Activate enqueued scripts & styles recording',
	        array($this,'jco_recording_output'),
	        'js_css_optimization',
	        'general_settings_section'
	    );


	    // register new fields in the enqueued list section
	    add_settings_field(
	        'jco_enqueued_scripts',
	        'Enqueued Scripts',
	        array($this,'jco_scripts_output'),
	        'js_css_optimization',
	        'enqueued_list_section'
	    );
	    
			add_settings_field(
	        'jco_enqueued_styles',
	        'Enqueued Styles',
	        array($this,'jco_styles_output'),
	        'js_css_optimization',
	        'enqueued_list_section'
	    );
	}

	public function output_section_cb( $section ) {
		PC::debug('In section callback');
	  ?>
		<h1><? echo esc_html($section['title']); ?></h1>
		<?php
	}
	
	public function jco_recording_output() {
		PC::debug( array('jco_enqueue_recording'=>get_option( 'jco_enqueue_recording' )) );
		$record = ( get_option( 'jco_enqueue_recording' ) == 'on')?true:false;
		$checked = $record?'checked="checked"':'';
		?>
		<label class="switch">
  	<input type="checkbox" name="jco_recording_checkbox" <?php echo $checked;?> value="on">
  	<div class="slider round"></div>
		</label>
		<?php
	}
	
	
	public function jco_scripts_output() {
		$this->	output_items_list('jco_enqueued_scripts');
	}
	
	public function jco_styles_output() {
		$this->	output_items_list('jco_enqueued_styles');
	}
	
	public function output_items_list( $option) {
	  // get the value of the setting we've registered with register_setting()
    $setting = get_option( $option );
   
    // output the field
    if (! isset ($setting) ) return;?>
    <table col="3">
    	<tr>
    		<th> Handler </th>
    		<th> Dependencies </th>
    		<th> File size </th>
    		<th> Location </th>
    	</tr>
    <?php	
    foreach ($setting as $handle => $script ) {	
    	$filename = $script['filename'];
    	$deps = $script['deps'];
	    $path = parse_url($filename, PHP_URL_PATH);
			//To get the dir, use: dirname($path)
			$path = $_SERVER['DOCUMENT_ROOT'] . $path;
	    $size = size_format( filesize($path) );
	    $location = $script['in_footer']?'footer':'header';  	
    	?>
    	<tr>
	    	<td title="<?php echo $filename;?>"><?php echo $handle;?></td>
	    	<td><?php foreach ($deps as $dep) {echo $dep . '<br>';}?></td>
	    	<td title="<?php echo $path;?>"><?php echo $size;?></td>
	    	<td><?php echo $location;?></td>
    	</tr>
    	<?php
    }?>
    </table>
		<?php
	}

	public function output_options_page() {
	    // check user capabilities
	    if (!current_user_can('manage_options')) {
	        return;
	    }
	    
			$redirect = menu_page_url( $this->menu_slug, FALSE );?>

	    <div class="wrap">
	        <h1><?= esc_html(get_admin_page_title()); ?></h1>
	        <div class="body">
	        </div>
	        <form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
	        		<?php 
	            // output security fields for the registered setting "wporg_options"
	            settings_fields('options');
	            // output setting sections and their fields
	            do_settings_sections('js_css_optimization');
	            
	            ?>
	            <table class="button-table" col="2">
	            <tr>
								<input type="hidden" name="action" value="<?php echo $this->form_action; ?>">
								<?php wp_nonce_field( $this->form_action, $this->nonce, FALSE ); ?>
								<input type="hidden" name="_wp_http_referer" value="<?php echo $redirect; ?>">

	            	<td><?php submit_button( 'Save Settings', 'primary', 'jco_save', true, array('tabindex'=>'1') );?> </td>
	            	<td><?php submit_button( 'Refresh enqueue list', 'secondary', 'jco_refresh', true, array('tabindex'=>'2') );?> </td>
	            	<td><?php submit_button( 'Reset enqueue list', 'delete', 'jco_reset', true, array('tabindex'=>'3') );?> </td>
	          	</tr>
	        </form>
	    </div>
	    <?php
	}

	
//	public function test_object_visibility() {
//	 	if ( is_page('accueil') ) {
//	  	self::$test='Gone through accueil';
//	 	}
//	}
//	
//	public function show_test_obj() {
//		PC::debug(array('Test object' => self::$test));
//	}
//
	
	
	public function jco_update_settings_cb() {
		
		// check user capabilities
    if (!current_user_can('manage_options'))
        return;
		
    if ( ! wp_verify_nonce( $_POST[ $this->nonce ], $this->form_action ) )
        die( 'Invalid nonce.' . var_export( $_POST, true ) );
		//PC::debug('In jco_update_settings_cb function');

		if ( isset ( $_POST[ 'jco_refresh' ] ) ) {
		   	// Scripts & Styles enqueing monitor
		    $this->auto_detect();
		    $msg = 'refresh';
		}
		elseif ( isset ( $_POST[ 'jco_reset' ] ) ) {
		    update_option( 'jco_enqueued_scripts', array() );
		    update_option( 'jco_enqueued_styles', array() );
		    $msg = 'reset';
		}
		else {
				PC::debug( array('jco_enqueue_recording'=>get_option( 'jco_enqueue_recording' )) );
				$recording = $_POST[ 'jco_recording_checkbox' ];
				PC::debug( array('jco_recording_checkbox'=> $recording) );
				update_option( 'jco_enqueue_recording', $recording);
		    $msg = 'save';
		}

		$url = add_query_arg( 'msg', $msg, urldecode( $_POST['_wp_http_referer'] ) );
		if ( ! isset ( $_POST['_wp_http_referer'] ) )
		    die( 'Missing target.' );

		wp_safe_redirect( $url );
		exit;
}
    
  public function load_option_page_cb() {
		//PC::debug('In load_option_page_cb function');
		if (isset ( $_GET['msg'] ) ) 
			add_action( 'admin_notices', array ( $this, 'render_msg' ) );
	}
	
	public function render_msg() {
		?>
		<div class="notice notice-success is-dismissible">
        <p><?php echo 'JCO settings update completed : ' . esc_attr( $_GET['msg'] ) ?></p>
    </div>
		<?php
	}


/* ENQUEUED SCRIPTS & STYLES MONITORING 
-------------------------------------------------------*/

	public function auto_detect() {

		PC::debug('In auto detect !!!');

		foreach ($this->urls_to_request as $url) {
			$request = array(
				'url'  => $url,
				'args' => array(
					'timeout'   => 0.01,
					'blocking'  => false,
					'sslverify' => apply_filters('https_local_ssl_verify', true)
				)
			);

			wp_remote_get($request['url'], $request['args']);
		}
	}
	
	public function get_permalink_by_slug( $slug) {
    $permalink = null;
    $page = get_page_by_path( $slug );
    if( null != $page ) {
        $permalink = get_permalink( $page->ID );
    } 
    return $permalink;
	}
		
	public function save_header_scripts() {
		$this->save_enqueued_scripts( false );
	}
	
	public function save_footer_scripts() {
		//$this->save_enqueued_scripts( true );
	}
	
	public function save_enqueued_scripts( $in_footer ) {
		PC::debug('In save enqueued scripts !!!');
	  //PC::debug( 'In save enqueued scripts' );	
	  $scripts = get_option('jco_enqueued_scripts');
	  //PC::debug(array('scripts before update' => $scripts));
		
		global $wp_scripts;
		//PC::debug( array('WP SCRIPTS'=>$wp_scripts) );
		foreach( $wp_scripts->queue as $handle ) {
	     $obj = $wp_scripts->registered [$handle];
	  	 //PC::debug(array('handle' => $handle));
			 //PC::debug( array('$obj'=>$obj) );
	     $scripts[$handle]=array(
	     	'filename' => $obj->src,
	     	'in_footer' => $in_footer,
	     	'deps' => $obj->deps,
	     );
		}
		
		update_option( 'jco_enqueued_scripts', $scripts, true );
	  //PC::debug(array('scripts after update' => $scripts));
	  	  
	}



}

