<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPURP_Custom_Metadata {

    public function get_metadata( $recipe )
    {
        if( is_feed() ) {
            return '';
        }
        
        $metadata = $this->get_metadata_array( $recipe );
        $metadata = $this->sanitize_metadata( $metadata );
        return '<script type="application/ld+json">' . json_encode( $metadata ) . '</script>';
    }

    private function sanitize_metadata( $metadata ) {
		$sanitized = array();
		if ( is_array( $metadata ) ) {
			foreach ( $metadata as $key => $value ) {
				$sanitized[ $key ] = $this->sanitize_metadata( $value );
			}
		} else {
			$sanitized = strip_shortcodes( wp_strip_all_tags( $metadata ) );
		}
		return $sanitized;
	}

    private function get_metadata_array( $recipe )
    {
        $recipe = is_null( $recipe ) ? new WPURP_Recipe(0) : $recipe;
        $post_id = get_the_id();

        // Essentials
        $metadata = array(
            '@context' => 'http://schema.org/',
            '@type' => 'Recipe',
            'name' => $recipe->title(),
            'author' => array(
                '@type' => 'Person',
                'name' => $recipe->author(),
            ),
            'datePublished' => $recipe->date(),
            'image' => $recipe->image_url( 'full' ),
            'description' => $recipe->description(),
        );


        // Yield
        if( $recipe->servings() ) $metadata['recipeYield'] = $recipe->servings() . ' ' . $recipe->servings_type();


        // Rating
				//$metadata['aggregateRating'] = do_shortcode('[json-ld-rating]');
				$rating_stats = explode(' ', do_shortcode('[json-ld-rating]') );
        
        if ( isset($rating_stats) && $rating_stats[1]!=0) {
	        $metadata['aggregateRating'] = array(
	            '@type' => 'AggregateRating',
	            'ratingValue' => $rating_stats[0],
	            'ratingCount' => $rating_stats[1],
	        );
        }

        // Times
        if( $recipe->prep_time_meta() && $recipe->cook_time_meta() ) {
            // Only use separate ones when we have both
            $metadata['prepTime'] = $recipe->prep_time_meta();
            $metadata['cookTime'] = $recipe->cook_time_meta();
        } else {
            // Otherwise use total time
            if( $recipe->prep_time_meta() ) $metadata['totalTime'] = $recipe->prep_time_meta();
            if( $recipe->cook_time_meta() ) $metadata['totalTime'] = $recipe->cook_time_meta();
        }

        // Nutrition
        if( WPUltimateRecipe::is_addon_active( 'nutritional-information' ) ) {
            $nutritional = $recipe->nutritional();
            $nutritional_units = WPUltimateRecipe::addon( 'nutritional-information' )->fields;
            $nutritional_units['unsaturated_fat'] = 'g';

            $mapping = array(
                'calories' => 'calories',
                'fat' => 'fatContent',
                'saturated_fat' => 'saturatedFatContent',
                'unsaturated_fat' => 'unsaturatedFatContent',
                'trans_fat' => 'transFatContent',
                'carbohydrate' => 'carbohydrateContent',
                'sugar' => 'sugarContent',
                'fiber' => 'fiberContent',
                'protein' => 'proteinContent',
                'cholesterol' => 'cholesterolContent',
                'sodium' => 'sodiumContent',
            );

            // Unsaturated Fat = mono + poly
            if( isset( $nutritional['monounsaturated_fat'] ) && $nutritional['monounsaturated_fat'] !== '' ) {
                $nutritional['unsaturated_fat'] = floatval( $nutritional['monounsaturated_fat'] );
            }

            if( isset( $nutritional['polyunsaturated_fat'] ) && $nutritional['polyunsaturated_fat'] !== '' ) {
                $mono = isset( $nutritional['unsaturated_fat'] ) ? $nutritional['unsaturated_fat'] : 0;
                $nutritional['unsaturated_fat'] = $mono + floatval( $nutritional['polyunsaturated_fat'] );
            }

            // Get metadata
            $metadata_nutrition = array(
                '@type' => 'NutritionInformation',
                'servingSize' => '1 serving',
            );

            foreach( $mapping as $field => $meta_field ) {
                if( isset( $nutritional[$field] ) && $nutritional[$field] !== '' ) {
                    $metadata_nutrition[$meta_field] = floatval( $nutritional[$field] ) . ' ' . $nutritional_units[$field];
                }
            }

            if( count( $metadata_nutrition ) > 2 ) {
                $metadata['nutrition'] = $metadata_nutrition;
            }
        }


        // Ingredients
        if( $recipe->has_ingredients() ) {
            $metadata_ingredients = array();

            foreach( $recipe->ingredients() as $ingredient ) {
                $metadata_ingredient = $ingredient['amount'] . ' ' . $ingredient['unit'] . ' ' . $ingredient['ingredient'];
                if( trim( $ingredient['notes'] ) !== '' ) {
                    $metadata_ingredient .= ' (' . $ingredient['notes'] . ')';
                }

                $metadata_ingredients[] = $metadata_ingredient;
            }

            $metadata['recipeIngredient'] = $metadata_ingredients;
        }


        // Instructions
        if( $recipe->has_instructions() ) {
            $metadata_instructions = array();

            foreach( $recipe->instructions() as $instruction ) {
                $metadata_instructions[] = $instruction['description'];
            }

            $metadata['recipeInstructions'] = $metadata_instructions;
        }


        // Category & Cuisine
        $courses = wp_get_post_terms( $recipe->ID(), 'course', array( 'fields' => 'names' ) );
        if( !is_wp_error( $courses ) && isset( $courses[0] ) ) {
            $metadata['recipeCategory'] = $courses[0];
        }

        $cuisines = wp_get_post_terms( $recipe->ID(), 'cuisine', array( 'fields' => 'names' ) );
        if( !is_wp_error( $cuisines ) && isset( $cuisines[0] ) ) {
            $metadata['recipeCuisine'] = $cuisines[0];
        }
        
				$diets = wp_get_post_terms( $recipe->ID(), 'diet', array( 'fields' => 'names' ) );
        if( !is_wp_error( $diets ) && isset( $diets[0] ) ) {
            $metadata['suitableForDiet'] = $diets[0];
        }

        // Allow external filtering of metadata
        return apply_filters( 'wpurp_custom_recipe_metadata', $metadata, $recipe );
    } 
    
}