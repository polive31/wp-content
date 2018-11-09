<?php


// class Custom_Recipe_Template  {
class Custom_Recipe_Template extends Custom_WPURP_Templates {

	private $post_ID;
	private $post_content;
	
	public function __construct() {
		parent::__construct();
		/* Custom recipe template */
		add_filter('wpurp_output_recipe', array($this,'wpurp_custom_recipe_template'), 10, 2 );
		add_filter('wp_dropdown_cats', array($this, 'add_lang_to_select'));
		add_action('wp_head',array($this,'add_custom_js'));

		$this->hydrate();
	}

	public function hydrate() {
		$this->post_ID = get_the_ID();
		$post = get_post( $this->post_ID );
		if ($post) {
	        $content = $post->post_content;
	        $this->post_content = trim(preg_replace("/\[wpurp-searchable-recipe\][^\[]*\[\/wpurp-searchable-recipe\]/", "", $content));
		}
		else {
			$this->post_content = '';	
		}
	}

	public function add_custom_js(){
	?>
	<script>
		jQuery(document).ready(function() {
			jQuery('li.wpurp-recipe-ingredient').on('click', function () {
				console.log('click detected on ingredient');
        		jQuery(this).toggleClass('clicked');
    		});
		});
	</script>
	<?php
	}

	public function add_lang_to_select($output){
  		return str_replace('<select','<select lang="fr"',$output);
	}


	public function wpurp_custom_recipe_template( $content, $recipe ) {

		$this->post_ID = get_the_ID();

		$imgID = $recipe->featured_image();

 		$imgAlt = get_post_meta($imgID,'_wp_attachment_image_alt', true);
 		if (empty($imgAlt))
 			// $imgAlt=sprintf(__('Recipe of %s', 'foodiepro'), $recipe->title());
 			$imgAlt=$recipe->title();

		ob_start();
		
		// Debug
			//echo '<pre>' . print_r(get_post_meta($this->post_ID), true) . '</pre>';
		
		// Output JSON+LD metadata & rich snippets
			echo $this->json_ld_meta_output($recipe,'');
		?>
		
		<div id="share-buttons"><?php //echo do_shortcode('[mashshare text=""]'); ?></div>

		<!-- Class .wpurp-container important for adjustable servings javascript -->	
		<div class="recipe wpurp-container" id="wpurp-container-recipe-<?php echo $recipe->ID(); ?>" data-id="<?php echo $recipe->ID(); ?>" data-permalink="<?php echo $recipe->link(); ?>" data-servings-original="<?php echo $recipe->servings_normalized(); ?>">
			<!-- Recipe description -->
			<div class="recipe-container" id="intro">
				<?php

				if (empty($this->post_content)) {
					echo $recipe->description();
				}
				?>	
			</div>
				
			<!-- Function buttons  -->
			<div class="recipe-top">
					<div class="recipe-buttons">

					<!-- Recipe Rate Button -->
					<div class="recipe-button tooltip tooltip-above tooltip-left <?php echo self::$logged_in?'':'disabled';?>" id="rate">
						<a href="<?php echo self::$logged_in?'#':'/connexion';?>" class="recipe-review-button" id="<?php echo self::$logged_in?'recipe-review':'join-us';?>">
						<div class="button-caption"><?php echo __('Rate','foodiepro'); ?></div>
						</a>
						[tooltip text="<?php echo __('Comment and rate this recipe','foodiepro'); ?>" pos="top"]   
					</div>	
					
					<!-- Recipe Add to Cart Button -->
	<!-- 				<div class="recipe-button tooltip tooltip-above tooltip-left" id="shopping">
					<?php 
						$shopping_list = new Custom_Recipe_Add_To_Shopping_List( self::$logged_in );  
						echo $shopping_list->output( $recipe );?>
					</div>	 -->			
					
					<!-- Add To Favorites Button -->
					<div class="recipe-button tooltip tooltip-above tooltip-left <?php echo self::$logged_in?'':'disabled';?>" id="favorite">
					<?php
						$favorite_recipe = new Custom_Recipe_Favorite( self::$logged_in );
						echo $favorite_recipe->output( $recipe );?>
					</div>			

					<!-- Like Button -->
					<div class="recipe-button tooltip tooltip-above tooltip-left" id="like">
					<?php
						$recipe_like = new Custom_Social_Like_Post( 'recipe' );
						echo $recipe_like->display();?>
					</div>		

					<!-- Recipe Print Button -->
					<div class="recipe-button tooltip tooltip-above tooltip-right" id="print">
						<a class="wpurp-recipe-print recipe-print-button" href="<?php echo $recipe->link_print(); ?>" target="_blank">
						<div class="button-caption"><?php echo __('Print', 'foodiepro'); ?></div>
						</a>
						[tooltip text="<?php echo __('Print this recipe','foodiepro'); ?>" pos="top"]   
					</div>	
										
					<!-- Recipe Share Button -->
					<div class="recipe-button tooltip tooltip-above" id="share">
						<a class="recipe-share-button" id="recipe-share" cursor-style="pointer">
						<div class="button-caption"><?php echo __('Share','foodiepro'); ?></div>
						</a> 
						<?php //echo Custom_WPURP_Templates::output_tooltip(__('Share this recipe','foodiepro'),'top');
							$share = do_shortcode('[mashshare]');
						?>  
						[tooltip text='<?php echo $share;?>' pos="top"] 
					</div>				
<!-- 					<script type="text/javascript">
						jQuery( "#recipe-share" ).click(function() {
					    	jQuery( "#share-buttons" ).toggle();
						});
					</script> -->
														
				</div>
				
			</div>
			
			<!-- Image + recipe info -->
			<div class="recipe-container"  id="image">
				
				<div class="image-container">
					<div class="clearfix">
					  	<a href="<?php echo $recipe->featured_image_url('full');?>">
							<img src="<?php echo $recipe->featured_image_url('vertical-thumbnail');?>" alt="<?php echo $imgAlt;?>">
						</a>
					</div>
					<div class="clearfix">
						[custom-gallery size="mini-thumbnail" link="file" columns="4" gallery-id="joined-pics"]
					</div>
				</div>
			
				<div class="info-container">
					
					<div class="label-container">
						[display-star-rating display="full"]
					</div>

					<?php
						// Origin
					  $terms = get_the_term_list( $this->post_ID, 'cuisine', '', ', ', '' ); 
						if ($terms!='') {
							$html = '<div class="label-container" id="tag"><div class="recipe-label">' . __('Origin','foodiepro') . '</div>' . $terms . '</div>';
							echo $html;
						}		
						
						// Diet
					  $terms = get_the_term_list( $this->post_ID, 'diet', '', ', ', '' ); 
						if ($terms!='') {
							$html = '<div class="label-container" id="tag"><div class="recipe-label">' . __('Diet','foodiepro') . '</div>' . $terms . '</div>';
							echo $html;
						}	
						
						// Difficulty
					  $terms = get_the_term_list( $this->post_ID, 'difficult', '', '', '' ); 
						if ($terms!='') {
							$html = '<div class="label-container" id="tag"><div class="recipe-label">' . __('Level','foodiepro') . '</div>' . $terms . '</div>';
							echo $html;
						}			
					
						// Servings
						$terms = $recipe->servings_normalized();
						if ($terms!='') {
							$html = '<div class="label-container" id="servings">';
							$html .= '<div class="recipe-label">' . __('Serves','foodiepro') . '</div>';
							$html .= '<div class="recipe-input">';
							$html .= '<i id="dec" class="fa fa-minus-circle"></i>';
							$html .= '<input type="number" min="1" class="adjust-recipe-servings" data-original="' . $recipe->servings_normalized() . '" data-start-servings="' . $recipe->servings_normalized() . '" value="' . $recipe->servings_normalized() . '"/>';
							$html .= '<i id="inc" class="fa fa-plus-circle"></i>';
							$html .= ' ' . $recipe->servings_type();
							$html .= '</div>';
							$html .= '</div>';
							echo $html;
						}
						
						?>
	<script>
		jQuery(".recipe-input i").on("click", function() {
			//console.log("Button Click !!!");
		  var $button = jQuery(this);
		  var $input= $button.parent().find("input");
		  var oldValue = $input.val();
		  //console.log("Old value : " + oldValue );
		  //console.log( "button id " + $button.attr('id') );
		  if ($button.attr('id') == "inc") {
			//console.log("INC Click !!!");
			  var newVal = parseFloat(oldValue) + 1;
			} else {
			//console.log("DEC Click !!!");
		    if (oldValue > 1) {
		      var newVal = parseFloat(oldValue) - 1;
		    } else {
		      newVal = 1;
		    }
		  }
		  $input.val(newVal);
		  $input.trigger("change");
		});
	</script>
	
<?php					
						// Prep time
						$test = $recipe->prep_time();
						if ($test!='') {
							$html = '<div class="label-container" id="prep"><div class="recipe-label">' . __('Preparation','foodiepro') . '</div>' . $test . ' ' . $recipe->prep_time_text() . '</div>';
							echo $html;
						}
						
						// Prep time
						$test = $recipe->cook_time();
						if ($test!='') {
							$html= '<div class="label-container" id="cook"><div class="recipe-label">' . __('Cooking','foodiepro') . '</div>' . $test . ' ' . $recipe->cook_time_text() . '</div>';
							echo $html;
							}
						
						$test = $recipe->passive_time();
						if ($test!='') {
							$html = '<div class="label-container" id="wait"><div class="recipe-label">' . __('Wait','foodiepro') . '</div>' . $test . ' ' . $recipe->passive_time_text() . '</div>';
							echo $html;					
						}
					?>
					
					
				</div>		
				
			</div>
			
			<!-- Ingredients + Instructions -->
			<div class="recipe-container" id="main">
				
				<div class="ingredients-container"> 
					<?php
					// Method "with custom function"
						echo $this->custom_ingredients_list($recipe,'');
					?>
				</div>

				<?php
						echo $this->custom_instructions_list($recipe,'');
				?>
			</div>
			
			<div class="recipe-container"  id="general">
				<?php
				$test = $recipe->notes();
				if ($test!='') {
					$html= '<h3>' . __('Notes','foodiepro') . '</h3>';
					$html.= '<div class="label-container">' . $test . '</div>';
					echo $html;
					}
				?>
			</div>
			
		</div>

		<?php
	    $output = ob_get_contents();
	    ob_end_clean();

		return $output;
	}


	public function json_ld_meta_output( $recipe, $args ) {
		
		$Custom_Metadata = new Custom_Recipe_Metadata;
		// $metadata = in_array( WPUltimateRecipe::option( 'recipe_metadata_type', 'json-inline' ), array( 'json', 'json-inline' ) ) ? $Custom_Metadata->get_metadata( $recipe ) : '';
		$metadata = $Custom_Metadata->get_metadata( $recipe );

		ob_start();?>

		<?php
		echo $metadata;

		$output = ob_get_contents();
	  ob_end_clean();

		return $output;
	}

	public function custom_ingredients_list( $recipe, $args ) {
	    $out = '';
	    $previous_group = '';	    
	    $first_group = true;
	    //$out .= '<ul class="wpurp-recipe-ingredients">';
	    
	    foreach( $recipe->ingredients() as $ingredient ) {

	        if( WPUltimateRecipe::option( 'ignore_ingredient_ids', '' ) != '1' && isset( $ingredient['ingredient_id'] ) ) {
	            $term = get_term( $ingredient['ingredient_id'], 'ingredient' );
	            if ( $term !== null && !is_wp_error( $term ) ) {
	                $ingredient['ingredient'] = $term->name;
	            }
	        }

	        if( $ingredient['group'] != $previous_group || $first_group ) { //removed isset($ingredient['group'] ) && 
	            $out .= $first_group ? '' : '</ul>';
	            $out .= '<ul class="wpurp-recipe-ingredients">';
	            $out .= '<li class="ingredient-group">' . $ingredient['group'] . '</li>';
	            $previous_group = $ingredient['group'];
				$first_group = false;
	        }

	        $meta = WPUltimateRecipe::option( 'recipe_metadata_type', 'json-inline' ) != 'json' && $args['template_type'] == 'recipe' && $args['desktop'] ? ' itemprop="recipeIngredient"' : '';

	        $out .= '<li class="wpurp-recipe-ingredient"' . $meta . '>';

	        $out .= Custom_WPURP_Ingredient::display( $ingredient );

	        $out .= '</li>';
	    }
	    //$out .= '</ul>';

	    return $out;
	}
			
	public function custom_instructions_list( $recipe, $args ) {
	    $out = '';
	    $previous_group = '';
	    $instructions = $recipe->instructions();
	    
	    $out .= '<ol class="wpurp-recipe-instruction-container">';
	    $first_group = true;
	    
	    for( $i = 0; $i < count($instructions); $i++ ) {
					
	        $instruction = $instructions[$i];
					$first_inst = false;
					
					if( $instruction['group'] != $previous_group ) { /* Entering new instruction group */
							$first_inst = true;
	            $out .= $first_group ? '' : '</ol>';
	            $out .= '<div class="wpurp-recipe-instruction-group recipe-instruction-group">' . $instruction['group'] . '</div>';
	            $out .= '<ol class="wpurp-recipe-instructions">';
	            $previous_group = $instruction['group'];
	    				$first_group = false;
	        }

	        $style = $first_inst ? ' li-first' : '';
	        $style .= !isset( $instructions[$i+1] ) || $instruction['group'] != $instructions[$i+1]['group'] ? ' li-last' : '';

	        $meta = WPUltimateRecipe::option( 'recipe_metadata_type', 'json-inline' ) != 'json' && $args['template_type'] == 'recipe' && $args['desktop'] ? ' itemprop="recipeInstructions"' : '';

	        $out .= '<li class="wpurp-recipe-instruction ' . $style . '">';
	        //$out .= '<div' . $meta . '>'.$instruction['description'].'</div>';
	        $out .= '<span>' . $instruction['description'] . '</span>';

	        if( !empty($instruction['image']) ) {
	            $thumb = wp_get_attachment_image_src( $instruction['image'], 'thumbnail' );
	            $thumb_url = $thumb['0'];

	            $full_img = wp_get_attachment_image_src( $instruction['image'], 'full' );
	            $full_img_url = $full_img['0'];

	            $title_tag = WPUltimateRecipe::option( 'recipe_instruction_images_title', 'attachment' ) == 'attachment' ? esc_attr( get_the_title( $instruction['image'] ) ) : esc_attr( $instruction['description'] );
	            $alt_tag = WPUltimateRecipe::option( 'recipe_instruction_images_alt', 'attachment' ) == 'attachment' ? esc_attr( get_post_meta( $instruction['image'], '_wp_attachment_image_alt', true ) ) : esc_attr( $instruction['description'] );

	            if( WPUltimateRecipe::option( 'recipe_images_clickable', '0' ) == 1 ) {
	                $out .= '<div class="instruction-step-image"><a href="' . $full_img_url . '" rel="lightbox" title="' . $title_tag . '">';
	                $out .= '<img src="' . $thumb_url . '" alt="' . $alt_tag . '" title="' . $title_tag . '"' . '/>';
	                $out .= '</a></div>';
	            } else {
	                $out .= '<div class="instruction-step-image"><img src="' . $thumb_url . '" alt="' . $alt_tag . '" title="' . $title_tag . '"' . '/></div>';
	            }
	        }

	        $out .= '</li>';
	    }
			$out .= '</ol>';

	    return $out;
	}

	
}