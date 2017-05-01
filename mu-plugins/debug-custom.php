<?php
/**
 * Plugin Name: PHP Output To Console
 * * Author: Better WP Solutions
 * License:     GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

new DBG();

class DBG {
		
	const ON = true;
	const PHP_CONSOLE = false;
  private static $output = '';
	
	public function __construct() {
		//add_action( 'print_footer_scripts', array($this, 'output_debug_buffer') );
		//add_action( 'admin_print_footer_scripts', array($this, 'output_debug_buffer') );
	}
	
	public function output_debug_buffer() {
		?>
		<script id="DBGlog" type="text/javascript">
			<?php echo self::$output;?>
		</script>
		<?php
		//PC::debug(array('In wp head output_buffer :'=>self::$output));
	}

	public static function log($msg, $var=false ) {
		if (!self::ON) return;	
		if ( self::PHP_CONSOLE && class_exists( 'PC' )) {
			if ($var==false) PC::debug($msg);
			else PC::debug(array($msg=>$var));
		}
		elseif ( !self::PHP_CONSOLE ) {
			if ( defined( 'DOING_AJAX' ) || strstr($_SERVER['REQUEST_URI'], 'admin-post.php') ) return; //Prevents blocking of post submissions/ajax requests
			if ( !is_array($var) ) $var = '"' . $var . '"';
			if ( is_array($var) ) $var = json_encode($var);
			self::$output='console.log("%cDEBUG%c ' . $msg . '", "border-radius:4px;padding:2px 4px;background:blue;color:white", "color:blue");';  
			self::$output.='console.log(' . $var . ');';
			self::output_debug_buffer();
		}
	}
	
}
