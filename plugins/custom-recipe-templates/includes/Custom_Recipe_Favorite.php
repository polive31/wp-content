<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Custom_Recipe_Favorite extends WPURP_Template_Block {

    public $class_id='wpurp-recipe-favorite';
    public $link_id='';
    public $editorField = 'favoriteRecipe';

    public function __construct( $type = 'recipe-favorite' )
    {
        parent::__construct( $type );
    }

    public function output( $recipe, $args = array() )
    {
        if( !$this->output_block( $recipe, $args ) ) return '';
        
        if( !is_user_logged_in() ) {
        	$this->link_id='join_us';
        } 
        else {
        	$this->class_id .= ' logged-in';
        }
        
        $title_in =__('In my favorites','foodiepro');
				$title_add =__('Add to my favorites','foodiepro');
				
        if( WPURP_Favorite_Recipes::is_favorite_recipe( $recipe->ID() ) ) {
        	$this->class_id .= ' is-favorite';
        	$title=$title_in;
        	$title_alt=$title_add;
        }
				else {
        	$title=$title_add;
        	$title_alt=$title_in;
				}
				
        $output = $this->before_output();
        ob_start();
?>
				<a href="#" id="<?php echo $this->link_id;?>" class="<?php echo $this->class_id; ?>" title="<?php echo $title?>" data-title-alt="<?php echo $title_alt; ?>"" data-recipe-id="<?php echo $recipe->ID(); ?>">
				<div class="button-caption"><?php echo __('Favorites','foodiepro'); ?></div>
				</a>

<?php
        $output .= ob_get_contents();
        ob_end_clean();

        return $this->after_output( $output, $recipe, $args );
    }
}