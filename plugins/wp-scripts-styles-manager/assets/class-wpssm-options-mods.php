<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WPSSM_Options_Mods extends WPSSM_Options {

	use Utilities;	

	const OPT_KEY = 'wpssm_mods';
						
	public function __construct( $args ) {
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
		WPSSM_Debug::log('In WPSSM_Options_Mods __construct() $this->get() ', $this->get() );
	}

	/* 	Outputs empty array instead of false on missing field
			Since in the case of mods assets all fields are arrays 
	-------------------------------------------------------------*/
	public function get( $type, $field ) {
		$get = parent::get( $type, $field );
		return ($get==false):array():$get;
	}

	public function is_mod( $type, $handle, $field ) {
		if ( isset( $this->get( $type, $field ))) {
			return in_array( $handle, $this->get( $type, $field) );
		}
	}
	
	public function is_async( $type, $handle ) {
		if ( ! isset( $this->get('scripts','async' ))) return false;
	}
	
	public function is_footer( $type, $handle ) {
		if ( ! isset( $this->get( $type, 'footer' ))) return false;
		return in_array( $handle, $this->et($type,'footer') );
	}

	public function is_disabled( $type, $handle ) {
		if ( ! isset( $this->get( $type,'disabled' ))) return false;
		return in_array( $handle, $this->get( $type, 'disabled' ));
	}


}

