<?php

/* CustomPostTemplates class
--------------------------------------------*/


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CPM_Assets {

	const ATTACHMENT_FORMATS = array('jpg','jpeg','png');
	const MAX_ATTACHMENT_SIZE_KB = 500;

	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;

	private static $slugs;
	private static $labels;
	private static $required;
	private static $taxonomies;
	private static $enqueued_styles;
	private static $enqueued_scripts;
	private static $statuses;
	private static $fallback;

	public function __construct() {
		// __construct is empty due to this class being used in a static way
		// However the hydrate function is called within constructor to allow constants to be populated
		// in the case of ajax calls & post submissions where the main plugin class is not created
		self::hydrate();
	}

	public static function hydrate() {

		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );

		$default_slugs = array(
			'post'	=> array(
				'post_list'         => 'publier-articles',
				'post_form'         => 'saisie-article',
			)
        );
		self::$slugs = apply_filters( 'cpm_page_slugs', $default_slugs );

		/* Location can be either a key of the self::$slugs array, or a post type */
		$default_enqueued_styles = array(
			'cpm-list'	=> array(
				'file'				=> 'assets/css/custom_posts_list.css',
				'uri'				=> self::$PLUGIN_URI,
				'dir'				=> self::$PLUGIN_PATH,
				'location'			=> array('post_list')
			),
			// 'cpm-post'	=> array(
			// 	'file'				=> 'assets/css/custom-submission-form.css',
			// 	'uri'				=> self::$PLUGIN_URI,
			// 	'dir'				=> self::$PLUGIN_PATH,
			// 	'deps'				=> array('post-font'),
			// 	'location'			=> array('post')
			// ),
			'cpm-submission-form'	=> array(
				'file'				=> 'assets/css/custom-submission-form.css',
				'uri'				=> self::$PLUGIN_URI,
				'dir'				=> self::$PLUGIN_PATH,
				'deps'				=> array('cpm-select2'),
				'location'			=> array('post_form')
			),
			'cpm-select2'	=> array(
				'file'				=> 'vendor/select2-4.0.13/css/select2.min.css',
				'uri'				=> self::$PLUGIN_URI,
				'dir'				=> self::$PLUGIN_PATH,
				'location'			=> array('post_form')
			),
			'post-font'		=> array(
				'handle'			=> 'post-font',
				'uri'				=> '//fonts.googleapis.com/css?family=Cabin',
				'location'			=> array('post')
				)
			);
			self::$enqueued_styles = apply_filters( 'cpm_enqueued_styles', $default_enqueued_styles );

			$default_enqueued_scripts = array(
			'cpm-list'		=> array(
				'file'				=> 'assets/js/custom_posts_list.js',
				'uri'				=> self::$PLUGIN_URI,
				'dir'				=> self::$PLUGIN_PATH,
				'deps'				=> array( 'jquery' ),
				'footer'			=> true,
				'location'			=> array('post_list'),
				'data' 				=> array(
					'name'				=> 'custom_posts_list',
					'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
					'nonce' 			=> wp_create_nonce( 'custom_posts_list' ),
					'confirm_message' 	=> _x( 'Are you sure you want to delete this post :', 'post', 'foodiepro' )
					)
				),
			'cpm-autosave'		=> array(
				'file'				=> 'assets/js/custom_post_autosave.js',
				'uri'				=> self::$PLUGIN_URI,
				'dir'				=> self::$PLUGIN_PATH,
				'deps'				=> array('jquery'),
				'footer'			=> true,
				'data'				=> array(
					'name'				=> 'custom_post_autosave',
					'ajaxurl' 			=> admin_url('admin-ajax.php'),
					'post_type'			=> 'post',
					'nonce' 			=> wp_create_nonce('custom_post_autosave'),
				),
				'location'			=> array('post_form'),
			),
			'cpm-submission'		=> array(
				'file'				=> 'assets/js/custom_post_submission.js',
				'uri'				=> self::$PLUGIN_URI,
				'dir'				=> self::$PLUGIN_PATH,
				'deps'				=> array('jquery' ),
				'footer'			=> true,
				'location'			=> array('post_form'),
				'data'				=> array(
					'name'			=> 'cpm_submission',
					'ajaxurl' 			=> admin_url('admin-ajax.php'),
					'nonce' 			=> wp_create_nonce('custom_post_submission_form'),
					'fileTypes' 		=> self::ATTACHMENT_FORMATS,
					'wrongFileType' 	=> sprintf(__('Authorized file formats are : %s', 'foodiepro'), implode(', ', self::ATTACHMENT_FORMATS)),
					'maxFileSize' 		=> self::MAX_ATTACHMENT_SIZE_KB,
					'fileTooBig' 		=> sprintf(__('The maximum file size is %s kB', 'foodiepro'), self::MAX_ATTACHMENT_SIZE_KB),
					'deleteImage' 		=> __('Do you really want to delete this image ?', 'foodiepro'),
				),
			),
			'cpm-editor'		=> array(
				'file'				=> 'assets/js/custom_post_editor.js',
				'uri'				=> self::$PLUGIN_URI,
				'dir'				=> self::$PLUGIN_PATH,
				'deps'				=> array('cpm-tinymce', 'jquery'),
				'footer'			=> true,
				'location'			=> array('post_form'),
				'data'				=> array(
					'name'			=> 'cpm_editor',
					'ajaxurl' 			=> admin_url('admin-ajax.php'),
					'nonce' 			=> wp_create_nonce('custom_post_editor'),
				),
			),
			'cpm-select2-taxonomies'=> array(
				'file'				=> 'assets/js/select2_taxonomies.js',
				'uri'				=> self::$PLUGIN_URI,
				'dir'				=> self::$PLUGIN_PATH,
				'deps'				=> array( 'jquery', 'cpm-select2' ),
				'footer'			=> true,
				'location'			=> array('post_form'),
			),
			'cpm-select2'		=> array(
				'file'				=> 'vendor/select2-4.0.13/js/select2.min.js',
				'uri'				=> self::$PLUGIN_URI,
				'dir'				=> self::$PLUGIN_PATH,
				'deps'				=> array( 'jquery' ),
				'footer'			=> true,
				'location'			=> array('post_form'),
			),
			'cpm-select2-fr'	=> array(
				'file'				=> 'vendor/select2-4.0.13/js/i18n/fr.js',
				'uri'				=> self::$PLUGIN_URI,
				'dir'				=> self::$PLUGIN_PATH,
				'deps'				=> array( 'cpm-select2' ),
				'footer'			=> true,
				'location'			=> array('post_form'),
			),
			/* LOCAL VERSION OF TINYMCE */
			'cpm-tinymce'		=> array(
				'file'				=> 'vendor/tinymce/tinymce.min.js',
				'uri'				=> self::$PLUGIN_URI,
				'dir'				=> self::$PLUGIN_PATH,
				'deps'				=> array( 'jquery' ),
				'footer'			=> true,
				'location'			=> array('post_form'),
			),
			/* CLOUD-BASED VERSION OF TINYMCE */
			// 'cpm-tinymce'		=> array(
			// 	'file'				=> 'https://cdn.tiny.cloud/1/b5likm29r9k8p1nipizqykdpy6qpiw6li1wc3tqmog5bx9it/tinymce/5/tinymce.min.js',
			// 	'uri'				=> '',
			// 	'deps'				=> array(),
			// 	'footer'			=> true,
			// 	'location'			=> array('post_form'),
			// ),
		);
		self::$enqueued_scripts = apply_filters( 'cpm_enqueued_scripts', $default_enqueued_scripts );

		$default_labels = array(
			'post' => array(
				'title'						=> _x( 'Post Title', 'post', 'foodiepro' ),
				'edit_button'				=> _x( 'Edit Post', 'post', 'foodiepro' ),
				'delete_button'				=> _x( 'Delete Post', 'post', 'foodiepro' ),
				'new_button'				=> _x( 'New Post', 'post', 'foodiepro' ),
				'featured_image'			=> _x( 'Add here your best picture for this post !', 'post', 'foodiepro'),
				'new1'						=> _x( 'Write your new post on this page.', 'post', 'foodiepro' ),
                'new2'						=> _x( 'You can then choose to save it as draft, or to publish it. Once approved, it will be visible to others according to your visibility preferences.', 'post', 'foodiepro' ),
				'edit1'						=> _x( 'Edit your existing post on this page.', 'post', 'foodiepro' ),
                'edit2' 					=> _x( 'You can then choose to save it as draft, or to publish it. Once approved, it wil be visible to others according to your visibility preferences.', 'post', 'foodiepro' ),
				'draft1'					=> _x( 'Post saved as <a href="%s">a draft</a>.','post', 'foodiepro'),
				'draft2'					=> _x( 'It will not be visible on the site, but you can edit it at any time and submit it later.','post','foodiepro'),
				'back'						=> _x( 'Back to <a href="%s">my posts</a>.', 'post', 'foodiepro'),
				'publish-admin'				=> _x( 'Dear administrator, this post is now <a href="%s">published</a>.', 'post', 'foodiepro' ),
				'publish-user'				=> _x( 'Post submitted! Thank you, your post is now awaiting moderation.', 'post', 'foodiepro' ),
				'required'					=> _x( 'In order for your post to be published, please fill-in those required fields:', 'post', 'foodiepro' ),
				'noposts'					=> _x( 'You have no posts yet.', 'post', 'foodiepro'),
		    	'post_publish_title'		=> _x( 'Your post just got published !', 'post', 'foodiepro'),
		    	'post_publish_content'		=> _x( 'Greetings, your post <a href="%s">%s</a> just got published !', 'post', 'foodiepro'),
				'post_publish_content1' 	=> _x( 'It is visible on the website, and appears on <a href="%s">your blog</a>.', 'post','foodiepro'),
		    	'comment_publish_title'		=> _x( '%s commented one of your posts', 'post', 'foodiepro'),
		    	'comment_publish_content'	=> _x( '%s added a comment to your post <a href="%s">%s</a> :', 'post', 'foodiepro'),
		    	'comment_publish_content'	=> _x( '%s added a comment to your post <a href="%s">%s</a> :', 'post', 'foodiepro'),
		    	'not_like'					=> _x( 'liked your post %s', 'post', 'foodiepro'),
		    	'not_comment'				=> _x( 'commented your post %s', 'post', 'foodiepro'),
		    	'not_comment_respond'		=> _x( 'answered your comment on post %s', 'post', 'foodiepro'),
			),
		);
		self::$labels = apply_filters( 'cpm_labels', $default_labels );

        $default_taxonomies=array(
			'post'	=> array(
				'category' => array(
					'multiselect' 	=> false,
					'child_of'		=> 9987,
					'exclude' 		=> array(),
					'exclude_tree' 	=> array(),
					'orderby' 		=> 'description',
					'labels'		=> array(
						'singular_name'=>__( 'Category', 'foodiepro' ),
					),
				),
				'post_tag' => array(
					'multiselect' => true,
					'exclude' => '',
					'orderby' => 'name',
					'labels'=>array(
						'singular_name'=>__( 'Keywords', 'foodiepro' ),
					),
				),
			),
        );
		self::$taxonomies = apply_filters( 'cpm_taxonomies', $default_taxonomies );

        $default_required = array(
			'post'	=> array(
				'post_title'  				=> __('Post Title','foodiepro'),
				'post_content'      		=> __('Post Content', 'foodiepro'),
				'post_category'				=> __('Post Category', 'foodiepro'),// IMPORTANT for taxonomies, name here must be <post_type>_<taxonomy>
				'post_image_attachment'		=> __('Post Featured Image.', 'foodiepro'),
				// 'post_post_tag'		=> __('Post Tag.', 'foodiepro'),//
			),
        );
		self::$required = apply_filters( 'cpm_required', $default_required );

		// Post statuses to be displayed in the user's own posts lists
		$default_statuses = array(
			'post'	=> array(
				'all'		=> array(
					'label'			=> _x('All my posts', 'post', 'foodiepro'),
					'description'	=> '',
				),
				'restored'	=> array(
					'label'			=> _x('Restored', 'post', 'foodiepro'),
					'description'	=> _x('Those posts have been automatically kept but not saved as drafts. You can delete them if you don\'t need them.', 'post', 'foodiepro'),
				),
				'pending'	=> array(
					'label'			=> _x('Pending', 'post', 'foodiepro'),
					'description'	=> _x('Those posts have been submitted and pending administrator\'s approval.', 'post', 'foodiepro'),
				),
				'draft'		=> array(
					'label'			=> _x('Draft', 'post', 'foodiepro'),
					'description'	=> _x('Those posts are in preparation, and not yet submitted.', 'post', 'foodiepro'),
				),
				'publish'	=> array(
					'label'			=> _x('Public', 'post', 'foodiepro'),
					'description'	=> _x('Those posts have been approved by the administrator. They are visible on the website, according to your visibility preferences.', 'post', 'foodiepro'),
				),
			)
		);
		self::$statuses = apply_filters('cpm_post_status', $default_statuses);

		$default_fallback = array(
			'post'	=> trailingslashit( self::$PLUGIN_URI ) . 'assets/img/fallback.jpg',
		);
		self::$fallback = apply_filters('cpm_fallback_image', $default_fallback);
	}

	public static function get_fallback_img_url($post_type, $size = '')
	{
		if (!isset(self::$fallback[$post_type])) return false;
		$url = self::$fallback[$post_type];
		if (!empty($size)) {
			$filename = basename($url, '.jpg');
			$url = str_replace($filename, $filename . '-' . $size, $url);
		}
		return $url ;
	}

	public static function get_fallback_img( $post_type, $size='' ) {
		$url = self::get_fallback_img_url($post_type, $size);
		$html = '<img src="' . $url . '"/>';
		return $html;
	}


	public static function scripts_styles_enqueue() {
		foreach (self::$enqueued_styles as $handle => $style) {
			$enqueue = false;
			foreach ($style['location'] as $location) {
				$singular = is_singular($location);
				$slug = self::get_slug(false, $location);
				$page = empty($slug)?false:is_page( $slug );
				if ( $page || $singular ) {
					$enqueue=true;
					break;
				}
			}
			if ($enqueue) {
				$args=$style;
				$args['handle']=$handle;
				foodiepro_enqueue_style( $args );
			}
		}

		foreach (self::$enqueued_scripts as $handle => $script) {
			$enqueue = false;
			foreach ($script['location'] as $location) {
				$singular = is_singular($location);
				$slug = self::get_slug(false, $location);
				$page = empty($slug)?false:is_page( $slug );
				if ( $page || $singular ) {
					$enqueue=true;
					break;
				}
			}
			if ($enqueue) {
				$args=$script;
				$args['handle']=$handle;
				foodiepro_enqueue_script( $args );
			}
		}
	}


	/********************************************************************************
	*********************         GETTERS / SETTERS       ***************************
	********************************************************************************/
    public static function get_slug( $post_type, $action ) {
		if ($post_type) {
			if (isset(self::$slugs[$post_type][$action] ))
				return self::$slugs[$post_type][$action];
		}
		else {
		// No post type provided, searching through all slugs elements
			foreach (self::$slugs as $post_type=>$actions ) {
				if ( isset($actions[$action]) )
					return $actions[$action];
			}
		}
		return false;
	}

	public static function get_type_from_slug($slug)
	{
		foreach (self::$slugs as $post_type => $actions) {
			if (in_array($slug, $actions))
				return $post_type;
		}
		return false;
	}

    public static function get_label( $post_type, $label_slug ) {
		if ( !isset(self::$labels[$post_type][$label_slug]) ) return false;
        return self::$labels[$post_type][$label_slug];
	}

    public static function get_required( $post_type ) {
		if ( !isset(self::$required[$post_type]) ) return false;
        return self::$required[$post_type];
	}

	/**
	 * Returns an array of post status & their labels
	 *
	 * @param string $post_type
	 *      Post type
	 *
	 * @param string $restrict
	 *      'registered' only retrieves registered statuses for this post type,
	 *      <status_slug> only retrieves status & label for the selected status
	 * @param string $all
	 *      Returns 'all' status & label
	 * @return string
	 *      array of status => label
	 **/
	public static function get_statuses( $post_type, $restrict=false, $all=true )
	{
		if ( !isset(self::$statuses[$post_type]) ) return false;
		if ($restrict) {
			$restrict_array=$all?array('all'=>''):array();
			if ($restrict=='registered')
				$restrict_array = array_merge($restrict_array, get_post_stati());
			else
				$restrict_array[$restrict] = '';
			$statuses = array_intersect_key(self::$statuses[$post_type], $restrict_array);
		}
		else
			$statuses= self::$statuses[$post_type];
		return $statuses;
	}

    public static function get_taxonomies( $post_type, $taxonomy=false, $field=false ) {
		if ( !isset(self::$taxonomies[$post_type]) ) return '';
		$tax = self::$taxonomies[$post_type];
		if ($taxonomy)
		$tax = isset($tax[$taxonomy])?$tax[$taxonomy]:false;
		if ($field)
		$tax = isset($tax[$field])?$tax[$field]:false;
		return $tax;
	}

    public static function get_post_types() {
		if ( empty(self::$taxonomies) ) return array('post');
		$types = array_keys( self::$taxonomies );
		return $types;
	}

    public static function get_edit_button( $post, $post_type, $class='edit-button' ) {
		$out='';
		$current_user = wp_get_current_user();
		if ($post->post_author == $current_user->ID || current_user_can('administrator')) {
			$post_url =  do_shortcode('[permalink slug="' . self::get_slug( $post_type, $post_type . '_form' ) . '"]');
			$edit_url = $post_url . '?edit-' . $post_type . '=' . $post->ID;
			$edit_title = self::get_label( $post_type, 'edit_button');
			// $out = '<span class="' . $class . '"><a href="' . $edit_url . '">' . foodiepro_get_icon('edit', '', $class, $edit_title) . '</a></span>';
			$out = foodiepro_get_icon_link($edit_url, 'edit', '', $class, $edit_title);
		}
		return $out;
	}

	public static function get_delete_button($post, $post_type, $class = 'delete-button')
	{
		$out = '<span class="'. $class . '">';
		// $out .= '<a href="#">';
		$data=array(
			'id'	=> $post->ID,
			'title'	=> esc_attr($post->post_title)
		);
		$out .= foodiepro_get_icon_link('#', 'delete', '', 'cpm-delete-post nodisplay', self::get_label($post_type, 'delete_button'), $data);
		// $out .= '<i class="far fa-trash-alt " data-id="' . $post->ID . '" data-title="' . esc_attr($post->post_title) . '"></i>';
		// $out .= '</a>';
		$out .= '</span>';
		return $out;
	}


	/* TEMPLATES
	---------------------------------------------------------------------- */
	public static function echo_template_part($slug, $name = false, $args = array())
	{
		extract($args);

		$templates_path = trailingslashit(self::$PLUGIN_PATH) . 'templates/';
		$template = 'template-' . $slug;
		$template .= $name ? '-' . $name : '';
		$template .= '.php';
		include($templates_path . $template);
	}

	public static function get_template_part($slug, $name = false, $args = array())
	{
		ob_start();
		self::echo_template_part($slug, $name, $args);
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}



}
