<?php

/* CustomArchive class
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Custom_Navigation_Helpers {

	public function __construct() {

		$Assets = new CNH_Assets();
		add_action( 'wp_enqueue_scripts', 				array($Assets, 'enqueue_masonry_scripts'));

		$Entries = new CNH_Archive_Entries();
		add_filter( 'genesis_post_title_output', 		array($Entries, 'archive_rating' ), 1 );
		/* Customize archive pages */
		add_filter( 'post_class', 						array($Entries, 'set_grid_columns'));
		add_filter( 'genesis_before_entry', 			array($Entries, 'reorganize_entry_content') );

		$Tags = new CNH_Tags_Overlay();
		add_filter( 'rpwe_in_thumbnail', 				array($Tags, 'rpwe_add_overlay'), 10, 2 );
		add_action( 'genesis_entry_header', 			array($Tags, 'do_post_title_before'), 1 );
		add_action( 'genesis_entry_header', 			array($Tags, 'do_post_title_after') );

		$Headline = new CNH_Archive_Headline();
		// Headline text
		add_filter( 'genesis_archive_title_text', 		array($Headline,'custom_archive_title') );
		add_filter( 'genesis_search_title_text', 		array($Headline,'custom_search_title_text') );
		// Intro text
		add_filter( 'genesis_term_intro_text_output', 	'wpautop' );
		add_filter( 'genesis_archive_description_text', array($Headline,'custom_archive_description') );
		// add_filter( 'genesis_term_intro_text_output', 'wpautop' );
		// Page title text
		// remove_filter('wp_title','genesis_default_title', 10, 3);
		// add_filter('wp_title', 'custom_archive_title', 10, 3);
		// Shortcode
		add_shortcode('seo-friendly-title', 			array($Headline,'get_seo_friendly_page_title'));

		$RPWE = new CNH_RPWE_Customizations();
		// Add post author to RPWE  widget
		add_filter('rpwe_post_title_meta', 				array($RPWE, 'rpwe_add_author'), 10, 2);
		/* Modify WP Recent Posts extended output, depending on the css ID field value */
		add_filter('rpwe_after_thumbnail', 				array($RPWE, 'wprpe_add_avatar'), 20, 2);
		/* Modify WPRPE output, displaying posts from current logged-in user */
		add_filter( 'rpwe_default_query_arguments', 	array($RPWE, 'wprpe_query_displayed_user_posts') );
		/* Workaround for shortcodes in rpwe "after" html not executing */
		// add_filter( 'rpwe_markup', 					array($this, 'add_more_from_author_link'),15, 2 );
		/* Prevent redundant posts when several rpwe instances are called on the same page */
		add_action('rpwe_loop', 						array($RPWE, 'rpwe_get_queried_posts') );
		/* ??? */
		add_filter('rpwe_default_query_arguments', 		array($RPWE, 'rpwe_exclude_posts') );
		/* Add user rating to RPWE widget */
		add_filter('rpwe_post_title', 					array($RPWE, 'rpwe_add_rating'), 10, 2 );
		/* Modify WP Recent Posts ordering, depending on the orderby field value */
		add_filter( 'rpwe_default_query_arguments', 	array($RPWE, 'wprpe_orderby_rating' ) );

		$Shortcodes = new CNH_Shortcodes();
		// Navigation shortcodes
		// add_shortcode('index-link', 					array($this,'add_index_link'));
		// add_shortcode('tooltip', 					array($this,'output_tooltip'));
		add_shortcode('ct-terms-menu', 					array($Shortcodes, 'list_taxonomy_terms'));
		add_shortcode('tags-menu', 						array($Shortcodes, 'list_tags'));
		add_shortcode('ct-terms', 						array($Shortcodes, 'list_terms_taxonomy'));
		// add_shortcode('ct-dropdown', 				array($this,'custom_categories_dropdown_shortcode'));
		add_shortcode('share-title', 					array($Shortcodes, 'display_share_title'));
		add_shortcode('wp-page-link', 					array($Shortcodes, 'display_wordpress_page_link') );
		add_shortcode('taxonomy-terms', 				array($Shortcodes, 'simple_list_taxonomy_terms'));

		// Admin shortcodes
		add_shortcode('post-count', 					array($Shortcodes, 'get_post_count'));

		// Add link shortcodes
		add_shortcode('permalink', 						array($Shortcodes, 'get_permalink'));
		add_shortcode('glossary', 						array($Shortcodes, 'search_glossary') );
		add_shortcode('search', 						array($Shortcodes, 'search_posts') );
		add_shortcode('registration', 					array($Shortcodes, 'get_registration_page'));

		// Social shortcodes
		add_shortcode('site-logo', 						array($Shortcodes, 'get_site_logo_path'));

		// Misc
		add_shortcode('if', 							array($Shortcodes, 'display_conditionnally') );
		add_shortcode('debug', 							array($Shortcodes, 'show_debug_html') );

	}

}