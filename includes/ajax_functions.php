<?php 
	
	function get_document_categorie_name(){
		$nonce = $_POST['mynonce'];
		
		
		if ( ! wp_verify_nonce( $nonce, 'documentation-script-nonce' ) ) {
			die ( 'Busted!');
		}
		
		$document = $_POST['document'];
		$title = get_the_title($document);
		
		$terms_list = wp_get_post_terms( $document, 'taxdocument', array('fields' => 'names') );
		
		$response_array = array( 'nom' => $terms_list[0] );
		$response = json_encode( $response_array );
		
		// response output
		header( "Content-Type: application/json" );
		echo $response;
		// IMPORTANT: don't forget to "exit"
		exit;
	}
	
	add_action( 'wp_ajax_document_categorie_name', 'get_document_categorie_name' );
	add_action( 'wp_ajax_nopriv_document_categorie_name', 'get_document_categorie_name' );