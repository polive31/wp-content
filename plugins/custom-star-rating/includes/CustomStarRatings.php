<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomStarRatings {
	
	const RATED_POST_TYPES = array( 'recipe' );
	
	protected $ratedPostTypes;
	protected $ratingCats;

	public function __construct() {
		
		//$this->$ratedPostTypes = array( 'recipe' );
		$this->ratedPostTypes = self::RATED_POST_TYPES;
		$this->ratingCats = array( 
			array (
				'id'=>'rating',
				'weight' => 2,
				'title'=> __('Dish','custom-star-rating'),
				'question'=> __('How did you like this dish ?'),
				'caption' => array(
								__('Really not good','custom-star-rating'),
								__('Not so good','custom-star-rating'),
								__('Rather good','custom-star-rating'),
								__('Tasty','custom-star-rating'),
								__('Delicious','custom-star-rating'),
				)	
			),
			array( 
				'id'=>'clarity',
				'weight' => 1,
				'title'=> __('Clarity','custom-star-rating'),
				'question'=> __('How clear was the recipe ?','custom-star-rating'),
				'caption' => array(
								__('Confusing','custom-star-rating'),
								__('Not so clear','custom-star-rating'),
								__('Rather clear','custom-star-rating'),
								__('Very clear','custom-star-rating'),
								__('Crystal clear even for kitchen dummies','custom-star-rating'),
				)	
			),
		);
		$this->ratingGlobal = array( 
			array (
				'id'=>'global',
				'title'=> __('Overall','custom-star-rating'),
				'caption' => array(
								__('Disappointing recipe','custom-star-rating'),
								__('Not so good','custom-star-rating'),
								__('Rather good','custom-star-rating'),
								__('Excellent recipe','custom-star-rating'),
								__('Outstanding recipe','custom-star-rating'),
				)	
			)
		);

		//add_action( 'genesis_before_content', array($this,'display_debug_info') );
	}
	
	/* Output debug information 
	--------------------------------------------------------------*/	
	protected function dbg( $msg, $var ) {
			if ( class_exists('PC') ) {
				PC::debug(array( $msg => $var ) );
			}
	}


	/* Dump main variables
	--------------------------------------------------------------*/	
	public function display_debug_info() {
		$this->dbg('In Custom Rating Main Class !', '' );
		$this->dbg('Rated types: ', $this->ratedPostTypes );
		$this->dbg('ratingCats : ', $this->ratingCats );
	}	
	
	
	/* Rating caption
	------------------------------------------------------------*/
	public function rating_caption($val,$cat) {
		//$val = intval($rating_val);
		if ($val==0) return __('Not rated','custom-star-rating');
		$val=ceil($val)-1;
		if ( isset( $this->ratingCats[$cat]['caption'] )) {
			return __($this->ratingCats[$cat]['caption'][$val], 'custom-star-rating');
		}
		return '';
	}


	/* Calculate rating stats
	-------------------------------------------------------------*/
	public function get_rating_stats( $cat_ratings ) {
		/* cat_ratings is the list of user ratings for one category */
		$this->dbg('In Get Rating Stats function','');
		$this->dbg('User Ratings array : ',$cat_ratings);
		$votes='';
		$avg_rating='';	
		if ( ! empty($cat_ratings) ) {
			$total = array_sum( $cat_ratings );
			$votes = count( array_filter( $cat_ratings ) );

		  if( $votes !== 0 ) {
		    $avg_rating = $total / $votes; // TODO Just an average for now, implement some more functions later
		    $avg_rating = round( $avg_rating, 1 );
		  } 	
	  }
	  return array(
		  'votes' => $votes,
		  'rating' => $avg_rating,
	  );
	}



	/* Output stars
	-------------------------------------------------------------*/
	public function output_stars($stars, $half) {
		$html = '';
		for ($i = 1; $i <= $stars; $i++) {
			$html .= '<i class="fa fa-star"></i>';
		}
		for ($i = $stars+1; $i <= 5; $i++) {
			if ( ($i == ($stars+1) ) && $half ) {
				$html .= '<i class="fa fa-star-half-o"></i>';
			}
			else {
				$html .= '<i class="fa fa-star-o"></i>';
			}
		}
		return $html;
	}



	
}

