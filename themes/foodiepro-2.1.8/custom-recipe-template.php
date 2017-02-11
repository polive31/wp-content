<?php

add_filter( 'wpurp_output_recipe', 'wpurp_custom_recipe_template', 10, 2 );

function wpurp_custom_recipe_template( $content, $recipe ) {

	ob_start();
	
	$post_ID = get_the_ID();

	?>

	<div class="recipe wpurp-container">
	<!-- Class .wpurp-container important for adjustable servings javascript -->	

		<div class="recipe-container">
			<?php
			echo $recipe->description();
			?>	
		</div>
			
		<div class="recipe-top">
				<div class="recipe-buttons">
				
				<!-- Recipe Print Button -->
				<div class="recipe-button">
					<a class="wpurp-recipe-print recipe-print-button" href="<?php echo $recipe->link_print(); ?>" target="_blank">
					</a>
					<div class="button-caption"><?php echo __('Print', 'foodiepro'); ?></div>
				</div>
				
				<!-- Recipe Add to Cart Button -->
				<div class="recipe-button">
				<?php
					//echo custom_addtocart_button($recipe);   
					$shopping_list = new WPURP_Custom_Recipe_Add_To_Shopping_List();  
					echo $shopping_list->output( $recipe );?>
					<div class="button-caption"><?php echo __('Add to Shopping List','foodiepro'); ?></div>
				</div>				
				
				<!-- Add To Favorites Button -->
				<div class="recipe-button">
				<?php
					//echo custom_favorite_button($recipe);
					$favorite_recipe = new WPURP_Custom_Recipe_Favorite();
					echo $favorite_recipe->output( $recipe );?>
					<div class="button-caption"><?php echo __('Add to Favorites','foodiepro'); ?></div>
				</div>				
				
				<!-- Recipe Share Button -->
				<div class="recipe-button">
					<a class="recipe-share-button" id="recipe-share">
					</a>
					<div class="button-caption"><?php echo __('Share','foodiepro'); ?></div>
				</div>				
											
				<!-- Recipe Rate Button -->
				<div class="recipe-button">
					<a class="recipe-review-button" id="recipe-review">
					</a>
					<div class="button-caption"><?php echo __('Rate','foodiepro'); ?></div>
				</div>			
													
			</div>
			
		</div>
		
		<div class="recipe-container">
			
			<div class="image-container">
				<div class="clearfix">
				  <a href="<?php echo $recipe->featured_image_url('full');?>">
						<img src="<?php echo $recipe->featured_image_url('horizontal-thumbnail');?>">
					</a>
				</div>
				<div class="clearfix">
					[custom-gallery size="mini-thumbnail" link="file" columns="4" gallery-id="joined-pics"]
				</div>
			</div>
		
			<div class="info-container">
				
				<div class="label-container">
				<?php echo do_shortcode('[display-star-rating type="full"]');?>
				</div>
				
				<?php
					// Origin
				  $test = get_the_term_list( $post_ID, 'cuisine', '', '', '' ); 
					if ($test!='') {
						$html = '<div class="label-container"><div id="tag" class="recipe-label">' . __('Origin','foodiepro') . '</div>' . $test . '</div>';
						echo $html;
					}		
					
					// Difficulty
				  $test = get_the_term_list( $post_ID, 'difficult', '', '', '' ); 
					if ($test!='') {
						$html = '<div class="label-container"><div id="tag" class="recipe-label">' . __('Level','foodiepro') . '</div>' . $test . '</div>';
						echo $html;
					}			
				
					// Servings
					$test = $recipe->servings_normalized();
					if ($test!='') {
						$html = '<div class="label-container"><div id="servings" class="recipe-label">' . __('Serves','foodiepro') . '</div><input type="number" min="1" class="adjust-recipe-servings" data-original="' . $recipe->servings_normalized() . '" data-start-servings="' . $recipe->servings_normalized() . '" value="' . $recipe->servings_normalized() . '"/> ' . $recipe->servings_type() . '</div>';
						echo $html;
					}
					
					// Prep time
					$test = $recipe->prep_time();
					if ($test!='') {
						$html = '<div class="label-container"><div id="prep" class="recipe-label">' . __('Preparation','foodiepro') . '</div>' . $test . ' ' . $recipe->prep_time_text() . '</div>';
						echo $html;
					}
					
					// Prep time
					$test = $recipe->cook_time();
					if ($test!='') {
						$html= '<div class="label-container"><div id="cook" class="recipe-label">' . __('Cooking','foodiepro') . '</div>' . $test . ' ' . $recipe->cook_time_text() . '</div>';
						echo $html;
						}
					
					$test = $recipe->passive_time();
					if ($test!='') {
						$html = '<div class="label-container"><div id="wait" class="recipe-label">' . __('Wait','foodiepro') . '</div>' . $test . ' ' . $recipe->passive_time_text() . '</div>';
						echo $html;					
					}
				?>
				
				
			</div>		
			
		</div>
		
		<div class="recipe-container">
			
			<div class="ingredients-container"> 
				<?php
				// Method "with custom function"
					echo custom_ingredients_list($recipe,'');
				?>
			</div>

			<?php
					echo custom_instructions_list($recipe,'');
			?>
		</div>
		
		<div class="recipe-container">
			<?php
			// Related Posts
			//rp4wp_children();
			?>
		</div>
		
		<div class="recipe-container">
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

function custom_ingredients_list( $recipe, $args ) {
    $out = '';
    $previous_group = '';
    $vocals = array('a','e','i','o','u');
    $exceptions = array('huile');
    
    $out .= '<ul class="wpurp-recipe-ingredients">';
    foreach( $recipe->ingredients() as $ingredient ) {

        if( WPUltimateRecipe::option( 'ignore_ingredient_ids', '' ) != '1' && isset( $ingredient['ingredient_id'] ) ) {
            $term = get_term( $ingredient['ingredient_id'], 'ingredient' );
            if ( $term !== null && !is_wp_error( $term ) ) {
                $ingredient['ingredient'] = $term->name;
            }
        }

        if( $ingredient['group'] != $previous_group ) { //removed isset($ingredient['group'] ) && 
            $out .= '</ul>';
            $out .= '<li class="ingredient-group">' . $ingredient['group'] . '</li>';
            $previous_group = $ingredient['group'];
            $out .= '<ul class="wpurp-recipe-ingredients">';
        }

        $fraction = WPUltimateRecipe::option('recipe_adjustable_servings_fractions', '0') == '1' ? true : false;
        $fraction = strpos($ingredient['amount'], '/') === false ? $fraction : true;

        $meta = WPUltimateRecipe::option( 'recipe_metadata_type', 'json-inline' ) != 'json' && $args['template_type'] == 'recipe' && $args['desktop'] ? ' itemprop="recipeIngredient"' : '';

        $out .= '<li class="wpurp-recipe-ingredient"' . $meta . '>';
        $out .= '<span class="recipe-ingredient-quantity-unit"><span class="wpurp-recipe-ingredient-quantity recipe-ingredient-quantity" data-normalized="'.$ingredient['amount_normalized'].'" data-fraction="'.$fraction.'" data-original="'.$ingredient['amount'].'">'.$ingredient['amount'].'</span> <span class="wpurp-recipe-ingredient-unit recipe-ingredient-unit" data-original="'.$ingredient['unit'].'">'.$ingredient['unit'].'</span></span>';

        $taxonomy = get_term_by('name', $ingredient['ingredient'], 'ingredient');
        $taxonomy_slug = is_object( $taxonomy ) ? $taxonomy->slug : $args['ingredient_name'];

        $plural = WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'plural' );
        $plural = is_array( $plural ) ? false : $plural;
        $plural_data = $plural ? ' data-singular="' . esc_attr( $ingredient['ingredient'] ) . '" data-plural="' . esc_attr( $plural ) . '"' : '';

        $out .= ' <span class="wpurp-recipe-ingredient-name recipe-ingredient-name"' . $plural_data . '>';

				$ingredient_name = remove_accents( $ingredient['ingredient'] );
				$first_letter = $ingredient_name[0];
				$first_word = strtolower( explode(' ', trim($ingredient_name))[0] );
				
				if ( $ingredient['unit']!='' ) {
					if ( in_array($first_letter, $vocals) || in_array( $first_word, $exceptions) )
						$out .= _x('of ','vowel','foodiepro');
					else 
						$out .= _x('of ','consonant','foodiepro');					
				}

        $ingredient_links = WPUltimateRecipe::option('recipe_ingredient_links', 'archive_custom');

        $closing_tag = '';
        if ( !empty( $taxonomy ) && $ingredient_links != 'disabled' ) {

            if( $ingredient_links == 'archive_custom' || $ingredient_links == 'custom' ) {
                $custom_link = WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'link' );
            } else {
                $custom_link = false;
            }

            if( WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy_slug, 'hide_link' ) !== '1' ) {
                if( $custom_link !== false && $custom_link !== '' ) {
                    $nofollow = WPUltimateRecipe::option( 'recipe_ingredient_custom_links_nofollow', '0' ) == '1' ? ' rel="nofollow"' : '';

                    $out .= '<a href="'.$custom_link.'" class="custom-ingredient-link" target="'.WPUltimateRecipe::option( 'recipe_ingredient_custom_links_target', '_blank' ).'"' . $nofollow . '>';
                    $closing_tag = '</a>';
                } else if( $ingredient_links != 'custom' ) {
                    $out .= '<a href="'.get_term_link( $taxonomy_slug, 'ingredient' ).'">';
                    $closing_tag = '</a>';
                }
            }
        }

        $out .= $plural && ($ingredient['unit']!='' || $ingredient['amount_normalized'] > 1) ? $plural : $ingredient['ingredient'];
        $out .= $closing_tag;
        $out .= '</span>';

        if( $ingredient['notes'] != '' ) {
            $out .= ' ';
            $out .= '<span class="wpurp-recipe-ingredient-notes recipe-ingredient-notes">'.$ingredient['notes'].'</span>';
        }

        $out .= '</li>';
    }
    $out .= '</ul>';

    return $out;
}
		
function custom_instructions_list( $recipe, $args ) {
    $out = '';
    $previous_group = '';
    $instructions = $recipe->instructions();
    
    $out .= '<ol class="wpurp-recipe-instruction-container">';
    for( $i = 0; $i < count($instructions); $i++ ) {
        $instruction = $instructions[$i];

				if( $instruction['group'] != $previous_group ) {
            $out .= '</ol>';
            $out .= '<div class="wpurp-recipe-instruction-group recipe-instruction-group">' . $instruction['group'] . '</div>';
            $out .= '<ol class="">';
            $previous_group = $instruction['group'];
        }


        $style = !isset( $instructions[$i+1] ) || $instruction['group'] != $instructions[$i+1]['group'] ? array('li','li-last') : 'li';

        $meta = WPUltimateRecipe::option( 'recipe_metadata_type', 'json-inline' ) != 'json' && $args['template_type'] == 'recipe' && $args['desktop'] ? ' itemprop="recipeInstructions"' : '';

        $out .= '<li class="wpurp-recipe-instruction">';
        $out .= '<div' . $meta . '>'.$instruction['description'].'</div>';

        if( $instruction['image'] != '' ) {
            $thumb = wp_get_attachment_image_src( $instruction['image'], 'thumbnail' );
            $thumb_url = $thumb['0'];

            $full_img = wp_get_attachment_image_src( $instruction['image'], 'full' );
            $full_img_url = $full_img['0'];

            $title_tag = WPUltimateRecipe::option( 'recipe_instruction_images_title', 'attachment' ) == 'attachment' ? esc_attr( get_the_title( $instruction['image'] ) ) : esc_attr( $instruction['description'] );
            $alt_tag = WPUltimateRecipe::option( 'recipe_instruction_images_alt', 'attachment' ) == 'attachment' ? esc_attr( get_post_meta( $instruction['image'], '_wp_attachment_image_alt', true ) ) : esc_attr( $instruction['description'] );

            if( WPUltimateRecipe::option( 'recipe_images_clickable', '0' ) == 1 ) {
                $out .= '<a href="' . $full_img_url . '" rel="lightbox" title="' . $title_tag . '">';
                $out .= '<img src="' . $thumb_url . '" alt="' . $alt_tag . '" title="' . $title_tag . '"' . '/>';
                $out .= '</a>';
            } else {
                $out .= '<img src="' . $thumb_url . '" alt="' . $alt_tag . '" title="' . $title_tag . '"' . '/>';
            }
        }

        $out .= '</li>';
    }

    return $out;
}

function custom_favorite_button( $recipe ) {
    if( !is_user_logged_in() ) return '';

    $current_icon = WPURP_Favorite_Recipes::is_favorite_recipe( $recipe->ID() ) ? 'fa-heart-o' : 'fa-heart';

    $icon = '<i class="fa ' . esc_attr( $current_icon ) . '" data-icon="fa-heart-o" data-icon-alt="fa-heart"></i>';

    $tooltip_text = __('Add to Favorites', 'foodiepro');
    $tooltip_alt_text = __('Remove from Favorites', 'foodiepro');
    if( $tooltip_text && $tooltip_alt_text ) $classes = array( 'recipe-button' );

    if( WPURP_Favorite_Recipes::is_favorite_recipe( $recipe->ID() ) ) {
        $tooltip_text_backup = $tooltip_text;
        $tooltip_text = $tooltip_alt_text;
        $tooltip_alt_text = $tooltip_text_backup;
    }

    ob_start();
		?>
		<a href="#" class="recipe-button wpurp-recipe-favorite" data-recipe-id="<?php echo $recipe->ID(); ?>"><?php echo $icon; ?>
		<?php if( $tooltip_text && $tooltip_alt_text ) { ?>
		    <div class="button-caption">
		        <div class="tooltip-shown"><?php echo $tooltip_text; ?></div>
		        <div class="tooltip-alt"><?php echo $tooltip_alt_text; ?></div>
		    </div>
		<?php } ?>
		</a>			
		<?php
    $output = ob_get_contents();
    ob_end_clean();

    return $output;
}
  

class WPURP_Custom_Recipe_Add_To_Shopping_List extends WPURP_Template_Block {

    public function __construct( $type = 'recipe-add-to-shopping-list' )
    {
        parent::__construct( $type );
    }

    public function output( $recipe, $args = array() )
    {
        if( !$this->output_block( $recipe, $args ) ) return '';
        
        $link_id='';
	      $classes = array();
        
        if( !is_user_logged_in() ) {
        	$link_id='join_us';
        } 

				else {
					$classes[] = 'logged-in';
	        $shopping_list_recipes = array();
	        if( isset( $_COOKIE['WPURP_Shopping_List_Recipes_v2'] ) ) {
	            $shopping_list_recipes = explode( ';', stripslashes( $_COOKIE['WPURP_Shopping_List_Recipes_v2'] ) );
	        }

	        $in_shopping_list = in_array( $recipe->ID(), $shopping_list_recipes );

	        if( $in_shopping_list ) $classes[] = 'in-shopping-list';

	        /*$tooltip_text = __('Add to Shopping List', 'wp-ultimate-recipe');
	        $tooltip_alt_text = __('This recipe is in your Shopping List', 'wp-ultimate-recipe');
	        if( $tooltip_text && $tooltip_alt_text ) $classes[] = 'recipe-tooltip';

	        if( $in_shopping_list ) {
	            $tooltip_text_backup = $tooltip_text;
	            $tooltip_text = $tooltip_alt_text;
	            $tooltip_alt_text = $tooltip_text_backup;
	        }*/

	        $this->classes = $classes;
	        $output = $this->before_output();
				}
        ob_start();
?>
<a href="#"<?php echo $this->style(); ?> id="<?php echo $link_id;?>" data-recipe-id="<?php echo $recipe->ID(); ?>"></a>

<?php
        $output .= ob_get_contents();
        ob_end_clean();

        return $this->after_output( $output, $recipe, $args );
    }
}


class WPURP_Custom_Recipe_Favorite extends WPURP_Template_Block {

    public $editorField = 'favoriteRecipe';

    public function __construct( $type = 'recipe-favorite' )
    {
        parent::__construct( $type );
    }

    public function output( $recipe, $args = array() )
    {
        if( !$this->output_block( $recipe, $args ) ) return '';
        if( !WPUltimateRecipe::is_addon_active( 'favorite-recipes' ) ) return '';
        
        $link_id='';
        
        $classes = array();
        if( !is_user_logged_in() ) {
        	//$icon = '<i class="fa ' . esc_attr( $this->icon ) . '"></i>';
        	$link_id='join_us';
        } 

				else {
					$classes[] = 'logged-in';
	        if (WPURP_Favorite_Recipes::is_favorite_recipe( $recipe->ID() ) ) $classes[] = 'is-favorite';  
	        //$icon = '<i class="fa ' . esc_attr( $current_icon ) . '" data-icon="' . esc_attr( $this->icon ) . '" data-icon-alt="' . esc_attr( $this->iconAlt ) . '"></i>';

	        /*$tooltip_text = __('Add to your Favorite Recipes', 'foodiepro');
	        $tooltip_alt_text = __('This recipe is in your Favorite Recipes', 'foodiepro');
	        if( $tooltip_text && $tooltip_alt_text ) $classes[]='recipe-tooltip';

	        if( WPURP_Favorite_Recipes::is_favorite_recipe( $recipe->ID() ) ) {
	            $tooltip_text_backup = $tooltip_text;
	            $tooltip_text = $tooltip_alt_text;
	            $tooltip_alt_text = $tooltip_text_backup;
	        }*/

					$this->classes = $classes;
	        $output = $this->before_output();
				}
        ob_start();
?>
<a href="#"<?php echo $this->style(); ?> id="<?php echo $link_id;?>" data-recipe-id="<?php echo $recipe->ID(); ?>"></a>

<?php
        $output .= ob_get_contents();
        ob_end_clean();

        return $this->after_output( $output, $recipe, $args );
    }
}
    
?>