<?php
/**
 * Genesis Framework.
 *
 * WARNING: This file is part of the core Genesis Framework. DO NOT edit this file under any circumstances.
 * Please do all modifications in the form of a child theme.
 *
 * @package Genesis\Templates
 * @author  StudioPress
 * @license GPL-2.0+
 * @link    http://my.studiopress.com/themes/genesis/
 */

add_action( 'genesis_before_content', 'genesis_do_search_title' );
// Widgeted areas
add_action( 'genesis_before_content', 'add_archives_top_area', 15);
add_action( 'genesis_after_loop', 'add_archives_bottom_area');
add_action( 'genesis_before_content', 'add_search_top_area', 10);

/**
 * Echo the title with the search term.
 *
 * @since 1.9.0
 */
function genesis_do_search_title() {
	echo '<div class="search-description archive-description">';
	echo '<h1 class="search-title archive-title">';
	$title = sprintf(__( 'Search Results for %s', 'foodiepro' ), get_search_query());
	echo apply_filters( 'genesis_search_title_text', $title) . "\n";
	echo '</h1>';
	echo '</div>';
}

function add_archives_top_area()
{
	genesis_widget_area('archives-top', array(
		'before' => '<div class="top archives-top widget-area">',
		'after'  => '</div>',
	));
}

function add_archives_bottom_area()
{
	genesis_widget_area('archives-bottom', array(
		'before' => '<div class="bottom archives-bottom widget-area">',
		'after'  => '</div>',
	));
}

function add_search_top_area() {
	genesis_widget_area( 'search-top', array(
	    'before' => '<div class="top search-top widget-area">',
	    'after'  => '</div>',
	));
}


genesis();
