<?php

class WPSSM_Admin_Output {
	
	const SMALL = 1000;
	const LARGE = 1000;
	const MAX = 200000;
 	
 	private $type;
 	private $size_small;
 	private $size_large;
 	private $size_max;
 	
 	private $asset_notice;

 	/* Class arguments */
 	private $plugin_name;
 	
 	/* Objects */
 	private $assets;
  
  public function __construct( $assets, $args ) {
  	$this->type = $assets->get_display_attr('type');
  	$this->size_small = self::SMALL;
  	$this->size_large = self::LARGE;
  	$this->size_max = self::MAX;
  	$this->assets = $assets;
  	foreach ($args as $key=>$value) {
  		$this->$key = $value;
  	}
  }


  
/* COMMON 
--------------------------------------------------------------------*/
	public function section_headline( $section ) {
		//WPSSM_Debug::log('In section callback');
	}
    

/* GENERAL SETTINGS PAGE
--------------------------------------------------------------------*/
	public function pages_list() {
		if ( $this->type != 'general') return false;
		WPSSM_Debug::log('In WPSSM_Output pages_list(), $this->assets ', $this->assets->get('pages') );
		foreach ($this->assets->get_displayed_assets() as $page) {
			echo '<p>' . $page[0] . ' on ' . $page[1] . '</p>';
		}
	}
	

	public function toggle_switch( $input_name, $value ) {
		WPSSM_Debug::log( 'in output toggle switch for ' . $input_name , $value);
		$checked = ( $value == 'on')?'checked="checked"':'';
		?>
		<label class="switch">
  	<input type="checkbox" name="<?php echo $input_name;?>_checkbox" <?php echo $checked;?> value="on">
  	<div class="slider round"></div>
		</label>
		<?php
	}
	

/* SCRIPTS AND STYLES PAGES
--------------------------------------------------------------------*/  

	public function header_items_list() {
		$this->items_list( $this->assets->sort( 'header' ), 'header' );
	}
	
	public function footer_items_list() {
		$this->items_list( $this->assets->sort( 'footer' ), 'footer' );
	}
	
	public function async_items_list() {
		$this->items_list( $this->assets->sort( 'async' ), 'async' );
	}
	
	public function disabled_items_list() {
		$this->items_list( $this->assets->sort( 'disabled' ), 'disabled' );
	}

	public function items_list( $sorted_list, $location ) {
		WPSSM_Debug::log('In WPSSM_Output items_list() $sorted_list : ', $sorted_list);			
		?><table class="enqueued-assets"><?php
		$this->item_headline();
    foreach ($sorted_list as $handle => $priority ) {
			WPSSM_Debug::log('Asset in WPSSM_Output->items_list() loop for ' . $location . ' : ', $handle );			
			$this->item_content( $this->assets->get($this->type, $handle) );  
    }
    ?></table><?php
	}
	

	public function item_headline() {
		?>
    	<tr>
    		<th> handle </th>
    		<th> priority </th>
    		<!--<th> Dependencies </th>-->
    		<th> Dependents </th> 
    		<th> File size </th>
    		<th> Location </th>
    		<th> Minify </th>
    	</tr>	
		<?php
	}

	public function item_content( $asset ) {
    	$handle = $asset['handle'];
    	$filename = $asset['filename'];
    	$dependencies = $asset['dependencies'];
    	$dependents = $asset['dependents'];
    	$priority = $asset['priority'];
    	$location = $this->assets->get_field_value( $asset, 'location');
	    $minify = $this->assets->get_field_value( $asset, 'minify');
	    $size = $this->assets->get_field_value( $asset, 'size');
	    	
	    $asset_is_minified = ( $minify == 'yes')?true:false; 
	    $already_minified_msg = __('This file is already minimized within its plugin', 'jco');
	    
	    
		?>
		   	<tr class="enqueued-asset <?php echo $this->type;?>" id="<?php echo $handle;?>">
	    	<td class="handle" title="<?php echo $filename;?>"><?php echo $handle;?><?php $this->asset_notice( $asset );?></td>
	    	
	    	<td><?php echo $priority;?></td>
	    	
	    	<!-- <td class="dependencies"><?php foreach ($dependencies as $dep) {echo $dep . '<br>';}?></td> -->
	    	<td class="dependents"><?php foreach ($dependents as $dep) {echo $dep . '<br>';}?></td>
	    	
	    	<td class="size" title="<?php echo $filename;?>"><?php echo size_format( $size );?></td>
	    	
	    	<td class="location <?php echo $this->assets->is_modified( $asset, 'location');?>">
	    		<select data-dependencies='<?php echo json_encode($dependencies);?>' data-dependents='<?php echo json_encode($dependents);?>' id="<?php echo $handle;?>" class="asset-setting location <?php echo $this->type;?>" name="<?php echo $location;?>">
  					<option value="header" <?php echo ($location=='header')?'selected':'';?> >header</option>
  					<option value="footer" <?php echo ($location=='footer')?'selected':'';?> >footer</option>
  					<option value="async" <?php echo ($location=='async')?'selected':'';?> >asynchronous</option>
  					<option value="disabled" <?php echo ($location=='disabled')?'selected':'';?>>disabled</option>
					</select>
				</td>
				
				<td class="minify <?php echo $this->assets->is_modified( $asset, 'minify');?>">
	    		<select id="<?php echo $handle;?>" class="asset-setting minify <?php echo $this->type;?>" <?php echo ($asset_is_minified)?'disabled':'';?> title="<?php echo ($asset_is_minified)?$already_minified_msg:'';?>" name="<?php echo $minify;?>">
  					<option value="no" <?php echo ($minify=='no')?'selected':'';?>  >no</option>
  					<option value="yes" <?php echo ($minify=='yes')?'selected':'';?> >yes</option>
					</select>
				</td>
    	
    	</tr>
		<?php
	}



/* ASSET WARNING/ADVICE NOTICES
--------------------------------------------------------------*/		
	
	private function asset_notice( $asset ) {
		
		$size= $asset['size'];
		//WPSSM_Debug::log(array('size : '=>$size));
		$is_minified = $this->assets->get_field_value( $asset, 'minify') == 'yes';
		//WPSSM_Debug::log(array('is_minified: '=>$is_minified));
		$in_footer = ( $this->assets->get_field_value( $asset, 'location') == 'footer');
		
		$this->reset_asset_notice();
		if (!$is_minified) {
			if ( $size > $this->size_large ) {
				$level = 'issue';
				$msg = __('This file is large and not minified : minification highly recommended', 'jco');	
				$this->enqueue_asset_notice( $msg, $level);
			}
			elseif ( $size != 0 ) {
				$level = 'warning';
				$msg = __('This file is not minified : minification recommended', 'jco');	
				$this->enqueue_asset_notice( $msg, $level);
			}
		}

		if ( ( $size > $this->size_large ) && ( !$in_footer ) ) {
			$level = 'issue';
			$msg = __('Large files loaded in the header will slow down page display : make asynchronous, loading in footer or at least conditional enqueue recommended', 'jco');			
			$this->enqueue_asset_notice( $msg, $level);
		}	
		
		if ( ( $size < $this->size_small ) && (!isset( $asset['in_group']) ) ) {
			$level = 'warning';
			$msg = __('This file is small and requires a specific http request : it is recommended to inline it, or to group it with other files', 'jco');			
			$this->enqueue_asset_notice( $msg, $level);
		}	
		echo $this->asset_notice;		
	}

	private function reset_asset_notice() {
		$this->asset_notice='';
	}
	
	private function enqueue_asset_notice( $msg, $level) {
		if ($msg != '') {
			$this->asset_notice .= '<i class="user-notification" id="' . $level . '" title="' . $msg . '"></i>';
		}		
	}
	
	
}