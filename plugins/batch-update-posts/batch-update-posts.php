<?php
/*
Plugin Name: Batch Update Posts
Plugin URI: http://goutu.org/
Description: Shortcodes for post & comments batch processing
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
*/


// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


require 'functions/delete_comments_ajax.php';
require 'functions/update_posts_meta_ajax.php';

add_action( 'wp_enqueue_scripts', 'bupm_init_scripts' );
function bupm_init_scripts() {
	//wp_enqueue_script( 'jquery' );
	//wp_register_script( 'ajax_call_meta_update', plugins_url( 'js/ajax_call_on_button_press.js', __FILE__ ) , array( 'jquery' ), '1.0', true );
	//wp_register_script( 'ajax_call_comment_delete', plugins_url( 'js/ajax_call_on_button_press.js', __FILE__ ) , array( 'jquery' ), '1.0', true );
	wp_register_script( 'ajax_call_batch_update', plugins_url( 'js/ajax_call_on_button_press.js', __FILE__ ) , array( 'jquery' ), '1.0', true );
}

add_action("wp_ajax_CommentDelete", "ajax_batch_delete_comments");
add_action("wp_ajax_nopriv_CommentDelete", "ajax_batch_delete_comments");

add_action("wp_ajax_MetaUpdate", "ajax_batch_update_meta");
add_action("wp_ajax_nopriv_MetaUpdate", "ajax_batch_update_meta");

/* =================================================================*/
/* =               BATCH UPDATE POST META
/* =================================================================*/

add_shortcode('batch-update-meta', 'batch_update_meta');

/* Batch update user_ratings_ratings custom field */
function batch_update_meta($atts) {
	$a = shortcode_atts( array(
		'post-type' => 'recipe',
		'include' => '',
		'key' => 'user_rating',
		'new-key' => '',
		'value' => '0',//can be scalar or array of space-separated $key/$value pairs
		'cmd' => 'add',//replace, delete, rename
	), $atts );
	
	static $script_id; // allows several shortcodes on the same page
	++$script_id;

	echo "<h3>BATCH UPDATE META SHORTCODE#" . $script_id . "</h3>";
	$ajson = json_encode($a);
	echo $ajson;
		
	// Localize and enqueue the script with new data
	$jsargs = array(
		'url' => admin_url( 'admin-ajax.php' ),
		'data' => $ajson,
	);
	wp_enqueue_script( 'ajax_call_batch_update' );	
	wp_localize_script( 'ajax_call_batch_update', 'scriptMetaUpdate' . $script_id , $jsargs );
	
	$style='';
	$cmd=$a['cmd'];
	if ($cmd=='delete') $style='background-color:red';
	if ($cmd=='update') $style='background-color:brown';
	
	ob_start();?>
	
	<div id = "center">
	<input style="<?php echo $style;?>" type="submit" id="button" data-name="MetaUpdate" data-instance="<?php echo $script_id;?>" name="Submit_<?php echo $script_id;?>" value="<?php echo $cmd;?>">
	</div>
	<div id="respCommentDelete<?php echo $script_id;?>"></div>
	<br>

	<?php
	$form=ob_get_contents();
	ob_end_clean();
	
	echo $form;
	
}



/* =================================================================*/
/* =               BATCH DELETE COMMENTS
/* =================================================================*/

add_shortcode('batch-delete-comments', 'batch_delete_comments');

/* Batch update user_ratings_ratings custom field */
function batch_delete_comments($atts) {
	$a = shortcode_atts( array(
		'post-type' => 'recipe',
		'include' => '', // Post ids list, separated by commas
	), $atts );
	

	static $script_id; // allows several shortcodes on the same page
	++$script_id;
	
	echo "<h3>BATCH DELETE COMMENTS SHORTCODE#" . $script_id . "</h3>";
	$ajson = json_encode($a);
	echo $ajson;
	

	// Localize the script with new data
	$jsargs = array(
		'url' => admin_url( 'admin-ajax.php' ),
		'data' => $ajson,
	);
	
	wp_enqueue_script( 'ajax_call_batch_update' );	
	wp_localize_script( 'ajax_call_batch_update', 'scriptCommentDelete' . $script_id, $jsargs );

	
	$cmd='delete';
	$style='background-color:red';
	
	ob_start();?>
	
	<div id = "center">
	<input style="<?php echo $style;?>" type="submit" id="button" data-name="CommentDelete" data-instance="<?php echo $script_id;?>" name="Submit_<?php echo $script_id;?>" value="<?php echo $cmd;?>">
	</div>
	<div id="respCommentDelete<?php echo $script_id;?>"></div>
	<br>

	<?php
	$form=ob_get_contents();
	ob_end_clean();
	
	echo $form;

	
}

?>


