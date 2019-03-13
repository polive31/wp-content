<?php
/**
 * This file adds the Landing template to the Foodie Pro Theme.
 *
 * @author StudioPress
 * @package Foodie Pro
 * @subpackage Customizations
 */

/*
Template Name: Landing
*/

//* Force full width content layout
add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );

//* Remove top ad widget area
remove_action( 'genesis_before', 'foodie_pro_before_header' );


//* Remove site header elements
remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
remove_action( 'genesis_header', 'genesis_do_header' );
remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );

remove_action( 'genesis_before', 'custom_header_markup_open', 5 );
remove_action( 'genesis_before', 'genesis_do_header' );
remove_action( 'genesis_before', 'custom_header_markup_close', 15 );	

//* Remove navigation
remove_action( 'genesis_after_header', 'genesis_do_nav' );
remove_action( 'genesis_after_header', 'genesis_do_subnav' );

//* Remove breadcrumbs
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );

//* Remove site footer widgets
remove_action( 'genesis_before_footer', 'genesis_footer_widget_areas' );

//* Remove site footer elements
remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
remove_action( 'genesis_footer', 'genesis_do_footer' );
remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );

//* Run the Genesis loop
genesis();

/**
 * This file adds the Custom Landing template to the Metro Theme.
 *
 * @author Brad Dalton
 * @package Generate
 * @subpackage Customizations
 */

/*
Template Name: Custom
*/


