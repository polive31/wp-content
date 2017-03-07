<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomStarRatingsShortcodes extends CustomStarRatingsMeta {
	
	public function __construct() {
		parent::__construct();
		add_shortcode( 'json-ld-rating', array($this,'display_json_ld_rating') );
		add_shortcode( 'comment-rating-form', array($this,'display_comment_form_with_rating') );
		add_shortcode( 'display-star-rating', array($this,'display_star_rating_shortcode') );
	}


	/* Rating + Votes in string format shorcode 
	-----------------------------------------------*/
	public function display_json_ld_rating($atts) {
		$a = shortcode_atts( array(
			'category' => 'global', //any rating category...
		), $atts );
		
		$post_id = get_the_id();
		
		$ratings = get_post_meta( $post_id , 'user_ratings' );
		$votes = count ($ratings);
			
		$rating = get_post_meta( $post_id , 'user_rating_' . $a['category'], true);	
			
//		$ratings_cat = array_column($ratings, $a['category']);
//		if ( isset($ratings_cat) )
//			$stats = $this->get_rating_stats( $ratings_cat );
		//$stats = implode(' ', $stats);
	
		$stats = $rating . ' ' . $votes;
		return $stats;
    
	}

	/* Comment form with rating input shortcode
	-----------------------------------------------*/
	public function display_comment_form_with_rating() {
		$args = array (
			'title_reply' => '', //Default: __( 'Leave a Reply� )
			'label_submit' => __( 'Send', 'custom-star-rating' ), //default=�Post Comment�
			'comment_field' => $this->output_evaluation_form(), 
			'logged_in_as' => '', //Default: __( 'Leave a Reply to %s� )
			'title_reply_to' => __( 'Reply Title', 'custom-star-rating' ), //Default: __( 'Leave a Reply to %s� )
			'cancel_reply_link' => __( 'Cancel', 'custom-star-rating' ), //Default: __( �Cancel reply� )
			'rating_cats' => 'all',  //Default: "id1 id2..."
			);
		
	  ob_start();
	  
	  //display_rating_form();
	  comment_form($args);
	  
	  $cr_form = ob_get_contents();
	  ob_end_clean();
	  
	  return $cr_form;
	}


	/* Output star rating shortcode 
	---------------------------------------------*/
	public function display_star_rating_shortcode($atts) {
		$a = shortcode_atts( array(
			'source' => 'post', //comment
			'display' => 'normal', //minimal = only stars, normal = category caption + stars, full = with votes
			'category' => 'all',  // "global rating clarity...", global not displayed unless mentioned
		), $atts );
		
		$display_style = $a['display'];
		$comment_rating = ( $a['source'] == 'comment');
		
		//if (!$comment_rating) //$this->dbg('In POST rating display shortcode','');
		//if ($comment_rating) //$this->dbg('In COMMENT rating display shortcode','');

		// Determine table of categories to be displayed
		if ( $a['category']=='all' ) {
			$display_cats=$this->ratingCats;	
		}
		elseif ( $a['category']=='global' ) {
			$display_cats=$this->ratingGlobal;
		}
		else {
			$shortcode_cats=explode(' ', $a['category']);
			foreach ( $this->ratingCats as $id=>$cat ) {
				if ( in_array($cat['id'],$shortcode_cats) ) $display_cats[]=$cat;
			}
		}
		
		if ( $comment_rating ) {
			$comment_id = get_comment_ID();
		}
		else { // Rating in post meta
			$post_id = get_the_id();
			if ($display_style == 'full') { // displays number of votes
				$ratings = get_post_meta( $post_id , 'user_ratings' );
			}
		}

	
		ob_start();
	
		?>
		<table class="ratings-table">
		<?php
		foreach ($display_cats as $id=>$cat) {
	
			if ( $comment_rating ) {
				$rating=$this->get_comment_rating($comment_id,$cat['id']);
			}
			elseif ($display_style == 'full') { // displays number of votes
				$stats=$this->get_post_stats($ratings,$cat['id']);
				$rating=$stats['rating'];
				$votes=$stats['votes'];
			}
			else {
				$ratings = $this->get_post_rating( $post_id , 'user_ratings' );
			}

			$rating=empty($rating)?0:$rating;
			$stars = floor($rating);
			$half = ($rating-$stars) >= 0.5;
			?>
			<tr>
			<?php
			if ( ! ( $comment_rating && $rating==0 ) ) { // Don't show empty ratings in comments 	
				if ( $display_style!='minimal' ) {
				?>
				<td class="rating-category"><?php echo __($cat['title'], 'custom-star-rating')?></td>
				<?php
				}?>
				<td class="rating" title="<?php echo $rating?> : <?php echo $this->rating_caption($rating,$id)?>">
				<?php echo $this->output_stars($stars, $half)?>
				</td>
			<?php
			}
			if ( $display_style=='full' && !empty( $votes ) ) {
				$rating_plural=sprintf(_n('%s review','%s reviews',$votes,'custom-star-rating'), $votes); ?>
				<td class="rating-details">(<?php echo $rating_plural ?>)</td> 
			<?php 
			}?>
			</tr>
			<?php	
		}?>
		</table>
		<?php 
			//else {
				//echo '<div class="rating-details">' . __('Be the first to rate this recipe !','custom-star-rating') . '</div>';
			//}

		$html = ob_get_contents();
	  ob_end_clean();	

		return $html;
	}



	/* Custom Comment Form 
	------------------------------------------------------------ */
	public function output_evaluation_form() {
		
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
		$html.='<label for="rating-input-' . $id . '-5" class="rating-star" title="' . $this->rating_caption(5, $id) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-4" name="rating-' . $id . '" value="4"/>';
		$html.='<label for="rating-input-' . $id . '-4" class="rating-star" title="' . $this->rating_caption(4, $id) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-3" name="rating-' . $id . '" value="3"/>';
		$html.='<label for="rating-input-' . $id . '-3" class="rating-star" title="' . $this->rating_caption(3, $id) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-2" name="rating-' . $id . '" value="2"/>';
		$html.='<label for="rating-input-' . $id . '-2" class="rating-star" title="' . $this->rating_caption(2, $id) . '"></label>';
		$html.='<input type="radio" class="rating-input" id="rating-input-' . $id . '-1" name="rating-' . $id . '" value="1"/>';
		$html.='<label for="rating-input-' . $id . '-1" class="rating-star" title="' . $this->rating_caption(1, $id) . '"></label>';
		$html.='</div>';
	  
	  return $html;
		
	}


}


?>










