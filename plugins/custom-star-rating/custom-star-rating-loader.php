<?php
/*
Plugin Name: Custom Star Ratings
Plugin URI: http://goutu.org/custom-star-rating
Description: Ratings via stars in comments
Version: 1.0
Author: Pascal Olive
Author URI: http://goutu.org
License: GPL
Text Domain: foodiepro
Domain Path: ./lang
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/*************************************************************************/
/************               INITIALIZATION		         *****************/
/*************************************************************************/

require_once 'includes/class-custom-star-rating.php';
require_once 'includes/class-csr-assets.php';

require_once 'public/class-csr-rating.php';
require_once 'public/class-csr-form.php';


/* Chargement du text domain */
// function custom_star_rating_load_textdomain() {
// 	load_plugin_textdomain( 'custom-star-rating', false, 'custom-star-rating/lang/' );
// }
// add_action('plugins_loaded', 'custom_star_rating_load_textdomain');

/* Start plugin */
add_action( 'wp_loaded', 'csr_start_plugin' );
function csr_start_plugin() {
	new Custom_Star_Rating();
}
