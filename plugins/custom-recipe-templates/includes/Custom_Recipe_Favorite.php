<?php 

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// class Custom_Recipe_Favorite extends WPURP_Template_Block {
class Custom_Recipe_Favorite extends Custom_WPURP_Templates {

    private $class_id='custom-recipe-favorite tooltip-target';
    private $logged_in;

    private $editorField = 'favoriteRecipe';

    private static $FAVLISTS = array();

    // public function __construct( $logged_in, $type = 'recipe-favorite' ) {
    public function __construct() {
        // parent::__construct( $type );
        // self::$_PluginPath = plugin_dir_url( dirname( __FILE__ ) );
                // Ajax
        self::$FAVLISTS=array(
            'favorites' => array(
                'meta' => 'wpurp_favorites',
                'icon' => '<i class="fa fa-heart"></i>',
                'label' => __('<strong>My favorite recipes</strong>','foodiepro'),
                'tooltip-in' => __('In my <a href="%s">favorites</a>','foodiepro'),
            ),
            'wishlist' => array(
                'meta' => 'wpurp_favorites_wishlist',
                'icon' => '<i class="fa fa-thumb-tack"></i>',
                'label' => __('<strong>Recipes wish list</strong>','foodiepro'),
                'tooltip-in' => __('In my <a href="%s">wishlist</a>','foodiepro'),
            ),
            'no' => array(
                'icon' => '✘',
                'label' => __('Remove from favorites','foodiepro'),
                'tooltip-in' => __('Add to my <a href="%s">favorites</a>','foodiepro'),   
            )
        );

        add_action( 'wp_ajax_custom_favorite_recipe', array( $this, 'ajax_favorite_recipe' ) );
        add_action( 'wp_ajax_nopriv_custom_favorite_recipe', array( $this, 'ajax_favorite_recipe' ) );

        add_filter( 'query_vars', array($this, 'add_query_vars_filter') );        
    }
    
    
    public function add_query_vars_filter( $vars ) {
        $vars[] = "list";
        return $vars;
    }

    public function output( $recipe, $args = array() ) {
  
        if( !is_user_logged_in() ) {
            $link_id='id="join-us"';
            $link_url = '/connexion';
            $onclick = "ga('send','event','join-us','click','recipe-favorite', 0);";
            $favorites_link = '/connexion';
        } 
        else {
            $link_id='';
            $link_url = '#';
        	$this->class_id .= ' logged-in';
            $onclick = "";
            $favorites_link = do_shortcode('[permalink slug="favoris-recettes"]');
        }
        
        $isfav = $this->is_favorite_recipe( $recipe->ID() );
        if( $isfav[0] ) {
            $favorites_link .= '/?list=' . $isfav[1];
        }

        $tooltip = $this->get_field( $isfav[1], 'tooltip-in' );
        $tooltip=sprintf( $tooltip, $favorites_link);;
        
        ob_start();
        ?>
            <a href="<?= $link_url;?>" <?= $link_id;?> class="<?= $this->class_id; ?>" data-recipe-id="<?= $recipe->ID(); ?>" onClick="<?= $onclick; ?>" >
            <span class="button-icon" id="favorites"><?= $this->get_toolbar_icon('favorites-' . $isfav[1] ); ?></span>
            <div class="button-caption"><?= __('Cookbook','foodiepro'); ?></div>
            </a>
        <?php 

        // Tooltip::display($tooltip . $tooltip_alt, 'above', 'center');    
        Tooltip::display($tooltip, 'above', 'center');    
        if( is_user_logged_in() ) 
            Tooltip::display($this->add_popup( $recipe->ID() ), 'above', 'center', 'click', false, null, 'form' );    

        $output = ob_get_contents();
        ob_end_clean();

        // return $this->after_output( $output, $recipe, $args );
        return $output;
    }

    public function ajax_favorite_recipe() {
        if( !is_user_logged_in() ) return false;

        // Force strings translation
        $this->__construct();
        
        if (check_ajax_referer( 'custom_favorite_recipe', 'security', false ) ) {
            
            $recipe_id = intval( $_POST['recipe_id'] );
            $user_id = get_current_user_id();
            
            $choice=$_POST['choice'];
            
            foreach ($this->get_lists() as $list) {
                $favorites = get_user_meta( $user_id, $this->get_meta_name($list), true );
                $favorites = is_array( $favorites ) ? $favorites : array();
                
                if ( ($choice=='no' || $choice != $list) && in_array( $recipe_id, $favorites ) ) {
                    $key = array_search( $recipe_id, $favorites );
                    unset( $favorites[$key ] );
                } 
                elseif ( $choice==$list ) {
                    $favorites[] = $recipe_id;
                }
                update_user_meta( $user_id, $this->get_meta_name($list), $favorites );
            }
            $isfav = $this->is_favorite_recipe( $recipe_id );
            $response['text'] = $this->get_field( $isfav[1], 'tooltip-in' );
            $response['icon'] = $this->get_toolbar_icon('favorites-' . $isfav[1] );
            echo json_encode( $response );
            // echo $this->get_toolbar_icon('favorites-' . $isfav[1] );
        }
        die();
    }
    
    public function add_popup( $recipe_id ) {
        
        $fav=$this->is_favorite_recipe( $recipe_id );
        
        ob_start();
        ?>
        <div id="favorite-form">
        <p><?= __('Choose a list :','foodiepro'); ?></p>
        <?php 

        foreach ( $this->get_lists( true ) as $list) {
            $fav=$this->is_favorite_recipe( $recipe_id, $list );
            $isfav=$isfav || $fav[0];
            ?>
            <p>
                <input type="radio" id="<?= $list; ?>" name="favlist" value="<?= $list; ?>" <?= $fav[0]?'checked':'' ?>>
                <label for="<?= $list; ?>"> <?= $this->get_label( $list ) ?></label>
                <span class="favorite-icon"><?= $this->get_icon( $list ) ?></span>
            </p>
            <?php 
        } 
        
        ?>
        <p>
            <button onClick='addToFavoritesUpdate(this)'><?= __('OK','foodiepro'); ?></button>
            <button class="alignright cancel" onClick='addToFavoritesCancel(this);'><?= __('Cancel','foodiepro'); ?></button>
        </p>
        </div>
        <?php
        $html=ob_get_contents();
        ob_end_clean();

        return $html;
    }

    public static function get_lists( $include_no=false ) {
        $favlists = self::$FAVLISTS;
        if ( !$include_no ) {
            unset($favlists['no']);
        }
        return array_keys($favlists);
    }

    public static function get_label( $list ) {
        return self::$FAVLISTS[$list]['label'];
    }

    public static function get_field( $list, $field ) {
        if ( isset(self::$FAVLISTS[$list][$field]) ) 
            $val=self::$FAVLISTS[$list][$field];
        else
            $val=false;
        return $val;
    }    

    public static function get_meta_name( $list ) {
        return self::$FAVLISTS[$list]['meta'];
    }  
    
    public static function get_icon( $list ) {
        return self::$FAVLISTS[$list]['icon'];
    }

    
    public static function get_toolbar_icon( $type ) {
        switch ($type) {
            case 'favorites-no' :
                $html = '<i class="fa fa-book"></i>'; 
                break;
            case 'favorites-favorites' :
                $html = '<span class="fa-narrow fa-stack">';
                $html .= '<i class="fa fa-book fa-stack-1x"></i>';
                $html .= '<i class="fa fa-heart fa-exp fa-stack-1x"></i>';
                $html .= '</span>';                
                break;
            case 'favorites-wishlist' :
                $html = '<span class="fa-narrow fa-stack">';
                $html .= '<i class="fa fa-book fa-stack-1x"></i>';
                $html .= '<i class="fa fa-thumb-tack fa-exp fa-stack-1x"></i>';
                $html .= '</span>';              
                break;         
        }
        return $html;
    }      
    
    public static function is_favorite_recipe( $recipe_id, $lists='all' ) {
        if( !is_user_logged_in() ) return array(false, '');
        
        $user_id = get_current_user_id();
        $is_fav=false;
        $fav_list='no';

        if ($lists=='all') 
            $lists = self::get_lists();
        else
            $lists = array( 0 => $lists);

        foreach ($lists as $list)  {
            $favorites = get_user_meta( $user_id, self::get_meta_name( $list ), true );
            $favorites = is_array( $favorites ) ? $favorites : array();
            if ( in_array( $recipe_id, $favorites ) ) {
                $is_fav= true;
                $fav_list = $list;
            }
        }  

        return array( $is_fav, $fav_list );
    }    


}

new Custom_Recipe_Favorite();