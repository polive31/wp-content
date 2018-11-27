<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomSocialButtons {

	protected static $onClick = 'onclick="javascript:window.open(this.href,\'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=250,width=600\');return false;"';

	protected static $networks = array(
			'facebook', 
			'twitter',
			'googleplus',
			'whatsapp',
			'mailto',
			'whatsapp',
			'linkedin',
			'pinterest',
			'buffer'
		);

	public static $PLUGIN_PATH;
	public static $PLUGIN_URI;	

	const LINEBREAK = '%0D%0A%0D%0A';
	
	public function __construct() {	
		self::$PLUGIN_PATH = plugin_dir_path( dirname( __FILE__ ) );
		self::$PLUGIN_URI = plugin_dir_url( dirname( __FILE__ ) );

		add_action('wp_enqueue_scripts', array($this, 'enqueue_social_buttons_scripts_styles'));
	}

    public function enqueue_social_buttons_scripts_styles() {
		global $post;
		// if ( !has_shortcode( $post->post_content, 'social-sharing-buttons') ) return;

   		$uri = self::$PLUGIN_URI . '/assets/js/';
  		$path = self::$PLUGIN_PATH . '/assets/js/';  
		custom_enqueue_script( 'social-buttons', $uri, $path, 'social_sharing_buttons.js', array( 'jquery' ), CHILD_THEME_VERSION, true );

  		$uri = self::$PLUGIN_URI . '/assets/css/';
  		$path = self::$PLUGIN_PATH . '/assets/css/';
		custom_enqueue_style( 'social-buttons', $uri, $path, 'social_sharing_buttons.css', array(), CHILD_THEME_VERSION );	
	} 	
	
	public function get_sharing_buttons($target, $class, $networks) {
		global $post;

		$html = '<ul class="cssb share-icons">';

		if ($networks['facebook']) 
			$html = self::getFacebookButton($target, $class);
			
		if ($networks['twitter'])
			$html .= self::getTwitterButton($target, $class);

		if ($networks['mailto'])
			$html .= self::getMailButton($target, $class);

		if ($networks['pinterest'])
			$html .= self::getPinterestButton( $target, $class);

		if ($networks['whatsapp'])
			$html .= self::getWhatsappButton($target, $class); 

		if ($networks['linkedin']) {	
			$url = 'https://www.linkedin.com/shareArticle?mini=true&url='.$url.'&amp;title='.$title;
			$html .= '<li class="cssb share-icons ' . $class . '" id="linkedin"><a ' . self::$onClick . ' class="cssb-link cssb-linkedin" href="'.$url.'" target="_blank" title="LinkedIn">&nbsp;</a></li>';
		}

		if ($networks['buffer']) {	
			$url = 'https://bufferapp.com/add?url='.$url.'&amp;text='.$title;
			$html .= '<li class="cssb share-icons ' . $class . '" id="buffer"><a ' . self::$onClick . ' class="cssb-link cssb-buffer" href="'.$url.'" target="_blank" title="Buffer">&nbsp;</a></li>';
		}

		$html .= '</ul>';
		
		return $html;

	}

	// Facebook URL
	public static function facebookURL( $post ) {
		return self::getFacebookURL( get_permalink($post) );
	}

	public static function getFacebookButton( $target, $class ) {
		if ($target=='site') 
			$url=get_site_url(null,'','https');
		else
			$url=get_permalink();

		return '<li class="cssb share-icons ' . $class . '" id="facebook"><a ' . self::$onClick . ' class="cssb-link cssb-facebook" href="'. self::getFacebookURL($url) . '" target="_blank" title="' . __('Share on Facebook','foodiepro') . '"> </a></li>';
	}	

	public static function getFacebookURL( $url ) {
		return 'https://www.facebook.com/sharer/sharer.php?u='.$url;
	}

	// Twitter Functions
	public static function twitterURL( $post ) {
		return self::getTwitterURL( get_permalink($post), $post->post_title );
	}	
	
	public static function getTwitterButton( $target, $class ) {
		if ($target=='site') 
			$url=get_site_url(null,'','https');
		else
			$url=get_permalink();
		// $url=esc_html($url);

		// SEO Friendly current page title
		$title = do_shortcode('[seo-friendly-title]');		
	
		return '<li class="cssb share-icons ' . $class . '" id="twitter"><a ' . self::$onClick . ' class="cssb-link cssb-twitter" href="'. self::getTwitterURL($url,$title) .'" target="_blank" title="' . __('Share on Twitter','foodiepro') . '"> </a></li>';	
	}

	public static function getTwitterURL( $url, $title ) {
		return 'https://twitter.com/intent/tweet/?text='.$title.'&amp;url='.$url; //.'&amp;via=';
	}


	// Pinterest URL
	public static function pinterestURL( $post ) {
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'square-thumbnail' );
		return self::getPinterestURL( get_permalink($post), $post->post_title, $thumb );
	}	

	public static function getPinterestButton($target, $class ) {
		if ($target=='site') 
			$url=get_site_url(null,'','https');
		else
			$url=get_permalink();
		// $url=esc_html($url);

		// SEO Friendly current page title
		$title = do_shortcode('[seo-friendly-title]');

		// Get Post Thumbnail 
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );				

		return '<li class="cssb share-icons ' . $class . '" id="pinterest"><a ' . self::$onClick . ' class="cssb-link cssb-pinterest" href="' . self::getPinterestURL($url,$title,$thumb[0]) . '" data-pin-custom="true" target="_blank" title="' . __('Pin It','foodiepro') . '"> </a></li>';
	}

	public static function getPinterestURL( $url, $title, $thumb ) {
		return 'https://pinterest.com/pin/create/button/?url='.$url.'&amp;media='. $thumb[0] .'&amp;description='. $title;
	}


	// Mail functions

	public static function mailURL( $post, $target ) {
		$fields = self::getPostFields( $post, $target );
		return rawurlencode( htmlspecialchars_decode( self::getMailURL( $fields ) ) );
	}

	public static function getMailButton( $target, $class ) {
		if ($target == 'recipe' || $target == 'post') {
			global $post;
			$fields = self::getPostFields($post, $target, 'find');
		}
		else 
			$fields = self::getSiteFields();

		$url = self::getMailURL( $fields );

		$button = '<li class="cssb share-icons ' . $class . '" id="mailto"><a ' . self::$onClick . ' class="cssb-link cssb-mailto" href="' . $url . '" data-pin-custom="true" target="_blank" title="' . __('Share by email','foodiepro') . '"> </a></li>';

		return $button;
	}

	public static function getMailURL( $fields ) {
		$to = 'remplacez@cetteadresse';
		return 'mailto:' . $to . '?subject=' . $fields['subject'] . '&body=' . $fields['body'];
	}

	// WhatsApp functions

	// From email button => need to precise a post, since current post doesn't work
	public static function whatsappURL( $post, $target ) {
		$fields = self::getPostFields($post, $target);
		return self::getWhatsappURL( $fields['body'] );
	}	
	
	// From shortcode button => current post
	public static function getWhatsappButton( $target, $class ) {
		if ($target=='recipe'||$target=='post') {
			global $post;
			$fields = self::getPostFields($post, $target, 'find');
		}
		else 
			$fields = self::getSiteFields();

		$button =  '<li class="cssb share-icons ' . $class . '" id="whatsapp"><a class="cssb-link cssb-whatsapp" data-body="' . $fields['body'] . '" data-url="' . $fields['post-url'] . '" title="' . __('Share on Whatsapp','foodiepro') . '"> </a></li>';	

		return $button;
	}	


	/* 	GENERAL FUNCTIONS
	-----------------------------------------------------------*/

	public static function getSiteFields() {

		// $break = '\r\n';
		// $break = self::LINEBREAK;

		// $subject = 'Rejoins Goûtu.org !';
		// $body = 'Bonjour,' . $break . 'Je te propose de découvrir Goûtu.org (' . get_site_url(null,'','https') . '), un site de partage autour des thèmes de la Cuisine et de l\'Alimentation.' . $break . 'Tu pourras y découvrir des idéees de recettes, trouver des informations sur les différents ingrédients, et apprendre de nouvelles techniques et tours de main.' . $break . 'Mais Goûtu te permet également de classer tes recettes préférées dans ton carnet personnel, et de publier tes propres recettes et articles. Tu peux ainsi créer un véritable blog culinaire en toute simplicité, et partager ton actualité et tes publications avec le plus grand nombre. Rejoins-nous, l\'inscription est rapide et gratuite.' . $break . 'A bientôt sur la communauté des Gourmets !' . $break;
		// $body .= 'L\'équipe Goûtu.org';

		$url = get_site_url(null,'','https');

		$body = 'Bonjour,

		Je te propose de découvrir Goûtu.org (' . $url . '), un site de partage autour des thèmes de la Cuisine et de l\'Alimentation.
		
		Tu pourras y découvrir des idéees de recettes, trouver des informations sur les différents ingrédients, et apprendre de nouvelles techniques et tours de main.
		
		Mais Goûtu te permet également de classer tes recettes préférées dans ton carnet personnel, et de publier tes propres recettes et articles. Tu peux ainsi créer un véritable blog culinaire en toute simplicité, et partager ton actualité et tes publications avec le plus grand nombre. 
		
		Rejoins-nous, l\'inscription est rapide et gratuite.
		
		A bientôt sur la communauté des Gourmets !
		
		L\'équipe Goûtu.org';

		return array('subject' => $subject, 'body' => $body, 'post-url' => $url);
	}

	public static function getPostFields( $post, $target='recipe', $action='create' ) {
		// $break = '\r\n';
		// $break = self::LINEBREAK;

		$name = ucfirst(get_the_author_meta('display_name', $post->post_author));

		$from = ($target=='recipe')?'Une recette de':'Un article de';
		$thispost = ($target=='recipe')?'cette recette':'cet article';
		$it = ($target=='recipe')?'la':'le';

		$subject = do_shortcode('[seo-friendly-title]') . " - $from " . $name;

		$url  = get_permalink($post);

		if ($action=='create') {
			// $body = 'Bonjour,' . $break . 'J\'ai publié ' . $posttype . ' sur Goûtu.org et voudrais ' . $it . ' partager avec toi : ' . $post->post_title .  ' (' . get_permalink($post) . ').' . $break . 'A bientôt !' . $break . $name . ', blogueur sur Goûtu.org';
			
			$body = 'Bonjour,
				J\'ai publié ' . $thispost . ' sur Goûtu.org et voudrais ' . $it . ' partager avec toi : ' . $post->post_title .  ' (' . $url . ').
				A bientôt !
				' . $name . ', blogueur sur Goûtu.org';
		}
		elseif ($action=='find') {
			$body = 'Bonjour,
				J\'ai trouvé ' . $thispost . ' sur Goûtu.org, et voudrais ' . $it . ' partager avec toi : ' . $post->post_title .  ' (' . $url . ').
				A bientôt !';			
		}

		return array('subject' => $subject, 'body' => $body, 'post-url' => $url);
	}		
}
