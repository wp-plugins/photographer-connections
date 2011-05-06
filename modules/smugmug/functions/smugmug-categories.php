<?php

	/**
	 * All functions that deal with categories
	 *
	 */		

	/**
	 * Gets all categories
	 *
	 * @return array	
	 */		
	function blogsite_connect_get_smugmug_cats() { 
		$smug = blogsite_connect_smugmug_connection();
		if ( is_object( $smug ) ) {
			try {
				$cats = $smug->categories_get();
			}	
			catch( Exception $e ) {
				echo "{$e->getMessage()} (Error Code: {$e->getCode()})";
		 	}
		} else {
			$cats = array();
		}
		return $cats;
	};

	function blogsite_connect_get_smugmug_cats_create( $name = '' ) { 
		$smug = blogsite_connect_smugmug_connection();
		
		$args = array (
			'Name' => $name,
		);
		
		if ( is_object( $smug ) ) {
			try {
				$smug->categories_create( $args );
			}	
			catch( Exception $e ) {
				echo "{$e->getMessage()} (Error Code: {$e->getCode()})";
		 	}
		} 
	};

?>