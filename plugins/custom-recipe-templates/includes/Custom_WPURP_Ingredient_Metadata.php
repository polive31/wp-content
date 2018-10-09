<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Custom_WPURP_Ingredient_Metadata {

	protected static $MONTHS;

	public function __construct() {
		// parent::__construct();
		add_action( 'wp', array($this, 'hydrate' ));
		add_action( 'ingredient_add_form_fields', array($this, 'taxonomy_add_months_field'), 10, 2 );
		add_action( 'ingredient_edit_form_fields', array($this, 'taxonomy_edit_months_field'), 10, 2 );
		add_action( 'edited_ingredient', array($this, 'save_ingredient_custom_meta'), 10, 2 );  
		add_action( 'create_ingredient', array($this, 'save_ingredient_custom_meta'), 10, 2 );

	}

	public function hydrate() {
		self::$MONTHS = array(
			__('January','foodiepro'),
			__('February','foodiepro'),
			__('March','foodiepro'),
			__('April','foodiepro'),
			__('May','foodiepro'),
			__('June','foodiepro'),
			__('July','foodiepro'),
			__('August','foodiepro'),
			__('September','foodiepro'),
			__('October','foodiepro'),
			__('November','foodiepro'),
			__('December','foodiepro')
		);
	}

	// Add term page
	public function taxonomy_add_months_field() {
		// this will add the custom meta field to the add new term page
		?>
		<label for="wpurp_ctm_ingredient_month"><?php echo __('Months','foodiepro');?></label>
		<!-- <table> -->
		<!-- <tr> -->
		<?php
		$i=1;
		foreach (self::$MONTHS as $month) {	
		// echo '<pre>' . print_r(self::$MONTHS) . '</pre>';
			// echo '<pre>' . print_r($month) . '<br></pre>';
			?>
			<!-- <td> -->
			<div class="form-ingredient-month">
				<label for="wpurp_taxonomy_metadata_ingredient[month][<?php echo $i;?>]" title="<?php echo $month;?>"><?php echo $month[0]; ?></label>
				<input type="checkbox" name="wpurp_taxonomy_metadata_ingredient[month][<?php echo $i;?>]" id="wpurp_taxonomy_metadata_ingredientmonth<?php echo $i;?>" value="available" title="<?php echo $month;?>" >
			</div>
			<!-- </td> -->
			<?php
			$i++;
		}?>
		<!-- </tr> -->
		<!-- </table> -->
		<p class="description"><?php _e( 'Check the months when this ingredient is available','foodiepro' ); ?></p>
		<?php
	}


	// Edit term page
	public function taxonomy_edit_months_field($term) {
		// put the term ID into a variable
		$t_id = $term->term_id;
		// retrieve the existing value(s) for this meta field. This returns an array
		$ingredient_meta = get_option( "taxonomy_$t_id" ); 
		// echo '<pre>' . print_r($ingredient_meta) . '<br></pre>';
	 	?>
	 	<tr class="form-field">
		<th scope="row" valign="top">
		<label for="wpurp_taxonomy_metadata_ingredient_months"><?php echo __('Months','foodiepro');?></label>
		<td>
			<table>
				<tr>		
				<?php
				$i=1;
				foreach (self::$MONTHS as $month) {	
				// echo '<pre>' . print_r(self::$MONTHS) . '</pre>';
					// echo '<pre>' . print_r($month) . '<br></pre>';
					$checked = isset($ingredient_meta['month'][$i]);
					?>
					<td>
					<div class="form-field">
						<label for="wpurp_taxonomy_metadata_ingredient[month][<?php echo $i;?>]" title="<?php echo $month;?>"><?php echo $month[0]; ?></label>
						<input type="checkbox" name="wpurp_taxonomy_metadata_ingredient[month][<?php echo $i;?>]" id="wpurp_taxonomy_metadata_ingredientmonth<?php echo $i;?>" title="<?php echo $month;?>" <?php echo $checked?"checked":"";?>  >
					</div>
					</td>
					<?php
					$i++;
				}?>
				</tr>
			</table>
		</td>
		</th>
		</tr>
		<p class="description"><?php _e( 'Check the months when this ingredient is available','foodiepro' ); ?></p>

		<?php
	}


	// Save extra taxonomy fields callback function.
	public function save_ingredient_custom_meta( $term_id ) {
		if ( isset( $_POST['wpurp_taxonomy_metadata_ingredient'] ) ) {
			// $t_id = $term_id;
			$ingredient_meta = get_option( "taxonomy_$term_id" );
			$i=1;
			foreach ( self::$MONTHS as $month ) {
				if ( isset ( $_POST['wpurp_taxonomy_metadata_ingredient']['month'][$i] ) ) 
					$ingredient_meta['month'][$i] = $_POST['wpurp_taxonomy_metadata_ingredient']['month'][$i];
				elseif ( isset($ingredient_meta['month'][$i]) )
					unset($ingredient_meta['month'][$i]);
				$i++;
			}
			// Save the option array.
			update_option( "taxonomy_$term_id", $ingredient_meta );
		}
	}  



}