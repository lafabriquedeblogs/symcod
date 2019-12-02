<?php
	

add_filter( 'gform_pre_render_4', 'populate_choices' );
 
//Note: when changing choice values, we also need to use the gform_pre_validation so that the new values are available when validating the field.
add_filter( 'gform_pre_validation_4', 'populate_choices' );
 
//Note: when changing choice values, we also need to use the gform_admin_pre_render so that the right values are displayed when editing the entry.
add_filter( 'gform_admin_pre_render_4', 'populate_choices' );
 
//Note: this will allow for the labels to be used during the submission process in case values are enabled
add_filter( 'gform_pre_submission_filter_4', 'populate_choices' );

function populate_choices( $form ) {
 
    //only populating drop down for form id 4
    if ( $form['id'] != 4 ) {
       return $form;
    }
	
	
	$terms = get_terms( array(
    	'taxonomy' => 'product_cat_symcod',
    	'hide_empty' => false,
	) );
	
	$products = get_posts( array(
		'post_type' => 'produits',
		'posts_per_page' => -1,
		'post_status' => 'publish'
	));
    
 
    //Add a placeholder to field id 8, is not used with multi-select or radio, will overwrite placeholder set in form editor.
    //Replace 8 with your actual field id.
    $fields = $form['fields'];

    foreach( $form['fields'] as &$field ) {
		//Creating item array.
		$items = array();
		if ( $field->cssClass == 'famille-produit' ) {
	  	foreach ( $terms as $term ) {
			$items[] = array( 'value' => $term->term_id, 'text' => $term->name );
		}
		 $field->choices = $items;	
      }
    }
/*
	$posts_array = get_posts(
	    array(
	        'posts_per_page' => -1,
	        'post_type' => 'produits',
	        'tax_query' => array(
	            array(
	                'taxonomy' => 'product_cat_symcod',
	                'field' => 'term_id',
	                'terms' => $terms[0]->term_id,
	            )
	        )
	    )
	);
 
    foreach( $form['fields'] as &$field ) {
		//Creating item array.
		$items = array();
		if ( $field->cssClass == 'produit-produit' ) {
			foreach( $posts_array as $po ){
				$items[] = array( 'value' => $po->post_title, 'text' => $po->post_title );
			}
			
			$field->choices = $items;	
		}
    }

*/
 
    return $form;
}

function form_produits_select_content(){
	$nonce = $_POST['mynonce'];
	
	if ( ! wp_verify_nonce( $nonce, 'documentation-script-nonce' ) ) {
		die ( 'Busted!');
	}
	
	$term_a = $_POST['term_a'];
	$term_b = $_POST['term_b'];
	
	$response = array();
	$response["term_a"] = $term_a;
	$response["term_b"] = $term_b;
	
	$posts_array = get_posts(
	    array(
	        'posts_per_page' => -1,
	        'post_type' => 'produits',
	        'tax_query' => array(
	            array(
	                'taxonomy' => 'product_cat_symcod',
	                'field' => 'term_id',
	                'terms' => $term_a,
	            )
	        )
	    )
	);	
	
	$options = '';
	foreach( $posts_array as $p ){
		$options .= '<option value="'.$p->post_title.'">'.$p->post_title.'</option>';
	}
	
	$response["options"] = $options;
	// generate the response
	$response = json_encode( $response );
	
	// response output
	header( "Content-Type: application/json" );
	echo $response;
	
	// IMPORTANT: don't forget to "exit"
	exit;	
}
add_action( 'wp_ajax_form_produits_select_content', 'form_produits_select_content' );
add_action( 'wp_ajax_nopriv_form_produits_select_content', 'form_produits_select_content' );