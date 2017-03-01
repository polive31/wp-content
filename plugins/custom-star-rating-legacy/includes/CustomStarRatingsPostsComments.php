<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class CustomStarRatingsPostsComments extends CustomStarRatings {
	
	public function __construct() {
		parent::__construct();
		add_action( 'genesis_before_content', array($this,'display_debug_info') );
		add_action( 'comment_post',array($this,'update_comment_post_meta_php',10,3) );
		add_action( 'save_post', array($this,'wpurp_add_default_rating', 10, 2 ) );
	}
	
		/* Output debug information 
		--------------------------------------------------------------*/	
	public function display_debug_info() {
		//if ( is_single() ) {	
			//echo '<pre>' . print_r( $this->get_cats(), false ) . '</pre>';	
			PC::debug(array('In Custom Rating Post Comments !' ) );
			PC::debug(array('get_cats() : '=> $this->get_cats('') ) );
			PC::debug(array('ratingCats : '=> $this->ratingCats ) );
		//}
	}


		/* Add field 'rate' to the comments meta on submission using PHP
		------------------------------------------------------------ */

	public function update_comment_post_meta_php($comment_id,$comment_approved,$comment) {
		PC::debug('In comment post !');
		
		$rating = '';
		foreach ($this->ratingCats as $id->$cat) {
			if ( isset( $_POST[ 'rating-' . $id ] ) ) 
				$rating[$cat['name']] = $_POST[ 'rating-' . $id ];
				//otherwise let the cell empty, important for stats function
		}
		PC::debug(array('Rating :'=>$rating));
		add_comment_meta($comment_id, 'user_rating', $rating);

		/* POST META UPDATE
		------------------------------------------------------*/
		$post_id = $comment['comment_post_ID'];
		$this->update_post_meta_user_ratings( $post_id, $rating );

	}

		/* Add ratings default value on post save 
		-------------------------------------------------------------*/ 

	public function wpurp_add_default_rating( $id, $post ) {
	 	if ( ! wp_is_post_revision($post->ID) ) {
	 		//PC:debug('Default rating add');
			update_post_meta($post->ID, 'user_rating', '0');
	 	}
	}


	public function update_post_meta( $post_id, $new_rating ) {

		$user_ratings = get_post_meta( $post_id, 'user_ratings' );
		PC::debug(array('User Ratings Table :'=>$user_ratings));

		$user_id = ( is_user_logged_in() )?get_current_user_id():0;
		$user_ip = $this->get_user_ip();
		PC::debug(array('User IP :'=>$user_ip));

		/* Search and delete previous rating from same user */
		foreach ( $user_ratings as $id => $user_rating ) {
			if ( ( $user_id!=0 && $user_rating['user']==$user_id ) || ( $user_id==0 && $user_rating['ip']==$user_ip ) )  {
				//PC::debug(array('Previous rating from same user '=>$id));
				delete_post_meta($post_id, 'user_ratings', $user_rating);
				unset( $user_ratings[$id] );
			}
			
		}

		$new_rating['user'] = $user_id;
		$new_rating['ip'] = $user_ip;
		PC::debug(array('New User Rating :'=>$new_rating ) );
		
		add_post_meta($post_id, 'user_ratings', $new_rating);

		$user_ratings[]=$rating;
		$this->update_post_meta_user_rating( $post_id, $user_ratings );

	}
	
	public function update_post_meta_user_rating( $post_id, $user_ratings ) {
		
		foreach ( $user_ratings as $id => $user_rating ) {
			$stats = $this->get_rating_stats( $user_ratings );
		//PC:debug(array('Stats :'=>$stats) );
			update_post_meta($post_id, 'user_rating', $stats['rating']);
		}
		
	}

}