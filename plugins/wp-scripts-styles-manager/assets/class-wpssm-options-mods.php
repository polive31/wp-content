<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Options_Mods extends WPSSM_Options {

	use Utilities;	

	const OPT_KEY = 'wpssm_mods';
						
	public function __construct( $args ) {
		wpssm_log('*** In WPSSM_Options_Mods __construct ***' );
		$this->hydrate_args( $args );			
		$opt_proto = array(
						'scripts'=>array(
									'footer'=> array(),
									'async'=>array(),
									'group'=>array(),
									'disabled'=>array(),
									'minify'=>array(),
									), 
						'styles'=>array(
									'footer'=> array(),
									'async'=>array(),
									'group'=>array(),
									'disabled'=>array(),
									'minify'=>array(),
									), 						
						);
		parent::__construct( self::OPT_KEY, $opt_proto );
		wpssm_log('In WPSSM_Options_Mods __construct() $this->get() ', $this->get() );
	}

	/* 	Outputs empty array instead of false on missing field
			Since in the case of mods assets all fields are arrays 
	-------------------------------------------------------------*/
	public function get( $type=false, $field=false ) {
		$get = parent::get( $type, $field );
		return ($get==false)?array():$get;
	}

	public function is_mod( $type, $handle, $field ) {
		if ( parent::get( $type, $field ) == false ) return false;
		return in_array( $handle, parent::get( $type, $field ) );
	}
	
	public function is_async( $type, $handle ) {
		if ( parent::get( $type, 'async' ) == false ) return false;
		return in_array( $handle, parent::get( $type,'async' ) );
	}
	
	public function is_footer( $type, $handle ) {
		if ( parent::get( $type, 'footer' ) == false ) return false;
		return in_array( $handle, parent::get( $type,'footer' ) );
	}

	public function is_disabled( $type, $handle ) {
		if ( parent::get( $type, 'disabled' ) == false ) return false;
		return in_array( $handle, parent::get( $type, 'disabled' ));
	}


}

