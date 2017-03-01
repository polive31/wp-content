<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomStarRatingsShortcodes extends CustomStarRatings {
	
	public function __construct() {
		parent::__construct();
		add_shortcode( 'comment-rating-form', array($this,'display_comment_form_with_rating') );
		add_shortcode( 'display-star-rating', array($this,'display_star_rating_shortcode') );
	}

/* Comment form with rating input shortcode
-----------------------------------------------*/
public function display_comment_form_with_rating() {
	$args = array (
		'title_reply' => '', //Default: __( 'Leave a Reply� )
		'label_submit' => __( 'Send', 'custom-star-rating' ), //default=�Post Comment�
		'comment_field' => $this->output_evaluation_form_html_php(), 
		'logged_in_as' => '', //Default: __( 'Leave a Reply to %s� )
		'title_reply_to' => __( 'Reply Title', 'custom-star-rating' ), //Default: __( 'Leave a Reply to %s� )
		'cancel_reply_link' => __( 'Cancel', 'custom-star-rating' ) //Default: __( �Cancel reply� )
		);
	
  ob_start();
  
  //display_rating_form();
  comment_form($args);
  
  $cr_form = ob_get_contents();
  ob_end_clean();
  
  return $cr_form;
}


	/* Output post rating shortcode 
	---------------------------------------------*/
	public function display_star_rating_shortcode($atts) {
		$a = shortcode_atts( array(
			'source' => 'post', //comment
			'type' => 'stars', //full
		), $atts );
		
		$html='';

		PC::debug('In display-star-rating shortcode');
		$full_display=!($a['type']=='stars');
		$comment_rating = ( $a['source'] == 'comment');
		
		if ( $comment_rating ) {
			$id = get_comment_ID();
			//PC::debug( array('get comment ID'=>$id,) );
			$rating = get_comment_meta($id, 'user_rating', true);
			//PC::debug( array('rating from comment'=>$rating,) );
			$rating = $rating==''?'0':$rating;
			$stars = $rating;
			$half = false;
		}
		
		else { // Rating in post meta
			$id = get_the_id();
			if ($full_display) {
				$ratings = get_post_meta( $id , 'user_ratings' );
				PC::debug(array('$ratings: '=>$ratings));
				$stats = $this->get_rating_stats( $ratings );
				$rating = $stats['rating'];
				$votes = $stats['votes'];
			}
			else {
				$rating = get_post_meta( $id , 'user_rating', true );
			}	
			//PC::debug(array('$rating from shortcode : '=>$rating));
			$stars = floor($rating);
			$half = ($rating-$stars) >= 0.5;
			PC::debug(array('$half : '=>$half));
		}

		//PC:debug(array('votes : '=>$votes,'rating : '=>$rating,'stars : '=>$stars,'half : '=>$half,));	

		if ( ! ( $comment_rating && $rating=='0' ) ) {
			$html .= '<span class="rating" title="' . $rating . ' : ' . $this->rating_caption($rating) . '">';
			$html .= $this->output_stars($stars, $half);
			$html .= '</span>';
		}

		if ( $full_display && $votes!='0') {
			$rating_plural=$votes==1?__('review','foodiepro'):__('reviews','foodiepro'); 
			$html .= '<span class="rating-details">(' . $votes . ' ' . $rating_plural . ')</span>'; //. ' | ' . __('Rate this recipe','foodiepro') . 
		}
			//else {
				//echo '<div class="rating-details">' . __('Be the first to rate this recipe !','foodiepro') . '</div>';
			//}

		return $html;
	}


	/* Custom Comment Form with PHP (called from shortcodes.php)
	------------------------------------------------------------ */
	public function output_evaluation_form_html_php() {
		
		ob_start();?>
		
		<table class="ratings-table">
			
		<?php
		foreach ($this->ratingCats as $id => $cat) {?>
		
		<tr>
		<td class="rating-title"><?php echo __($cat['question'],'custom-star-rating');?></td>
		<td align="left"><?php echo $this -> output_rating_form( $id );?></td>
		</tr>
		
		<?php
		}?>	
		
		</table>
		
		<div class="comment-reply">
		<label for="comment"><?php echo _x( 'Comment', 'noun' );?></label>
		<textarea id="comment" name="comment" cols="50" rows="6" aria-required="true"></textarea>
		</div>

	<?php
		$rating_form = ob_get_contents();
		ob_end_clean();
		
		return $rating_form;

	}

	public function output_rating_form( $id ) {
		
		$html= '<div class="rating-wrapper" id="star-rating-form">';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-5" name="rating-' . $id . '" value="5"/>';
		$html.='<label for="rating-input-' . $id . '-5" class="rating-star" title="' . $this->rating_caption(5) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-4" name="rating-' . $id . '" value="4"/>';
		$html.='<label for="rating-input-' . $id . '-4" class="rating-star" title="' . $this->rating_caption(4) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-3" name="rating-' . $id . '" value="3"/>';
		$html.='<label for="rating-input-' . $id . '-3" class="rating-star" title="' . $this->rating_caption(3) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-2" name="rating-' . $id . '" value="2"/>';
		$html.='<label for="rating-input-' . $id . '-2" class="rating-star" title="' . $this->rating_caption(2) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-1" name="rating-' . $id . '" value="1"/>';
		$html.='<label for="rating-input-' . $id . '-1" class="rating-star" title="' . $this->rating_caption(1) . '"></label>';
		$html.='</div>';
	  
	  return $html;
		
	}


}


?>










