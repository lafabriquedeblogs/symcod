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



// Select Produits FOrmulaire de soumission
add_filter( 'gform_field_content_4_24', 'my_custom_function', 10, 5 );
add_filter( 'gform_field_content_4_31', 'my_custom_function', 10, 5 );
add_filter( 'gform_field_content_4_37', 'my_custom_function', 10, 5 );
add_filter( 'gform_field_content_4_43', 'my_custom_function', 10, 5 );
add_filter( 'gform_field_content_4_49', 'my_custom_function', 10, 5 );
add_filter( 'gform_field_content_4_55', 'my_custom_function', 10, 5 );
add_filter( 'gform_field_content_4_61', 'my_custom_function', 10, 5 );
add_filter( 'gform_field_content_4_67', 'my_custom_function', 10, 5 );
add_filter( 'gform_field_content_4_69', 'my_custom_function', 10, 5 );
add_filter( 'gform_field_content_4_73', 'my_custom_function', 10, 5 );
add_filter( 'gform_field_content_4_85', 'my_custom_function', 10, 5 );
function my_custom_function( $content, $field, $value, $lead_id, $form_id ){
	
		$content = '<label class="gfield_label" for="input_4_'.$field->id.'">'.__('Produits','symcod').'<span class="gfield_required">*</span></label>
			<div class="ginput_container ginput_container_select">
				<select name="input_'.$field->id.'" id="input_4_'.$field->id.'" class="medium gfield_select" aria-required="true" aria-invalid="false">';
						$products_args = array(
							'post_type' => array('produits'),
							'posts_per_page' => -1,
							'post_status' => 'publish',
							'orderby' => 'title',
						);
						$products_query = new WP_Query( $products_args );
						 while( $products_query->have_posts()){
							 $products_query->the_post();
							 $nom_du_produit = get_the_title();
							 $options = '';
							 
							 if( have_rows('ajouter_un_produit_version')){
								 while( have_rows('ajouter_un_produit_version') ){
									 the_row();
									 $version_name = get_sub_field('nom_du_produit_v');
									 $options .= '<option value="'.$version_name.'">'.$version_name.'</option>';
								 }
								$content .= '<optgroup label="'.$nom_du_produit.'">'.$options.'</optgroup>';	 
							 } else {
								 $content .= '<option value="'.$nom_du_produit.'">'.$nom_du_produit.'</option>';
							 }
							 
							 
						 }			
			
			$content .= '</select>
		</div>';
	return $content;
}


add_filter( 'gform_field_content_4_23', 'my_custom_function_tion', 10, 5 );
add_filter( 'gform_field_content_4_90', 'my_custom_function_tion', 10, 5 );
add_filter( 'gform_field_content_4_32', 'my_custom_function_tion', 10, 5 );
add_filter( 'gform_field_content_4_38', 'my_custom_function_tion', 10, 5 );
add_filter( 'gform_field_content_4_44', 'my_custom_function_tion', 10, 5 );
add_filter( 'gform_field_content_4_50', 'my_custom_function_tion', 10, 5 );
add_filter( 'gform_field_content_4_56', 'my_custom_function_tion', 10, 5 );
add_filter( 'gform_field_content_4_62', 'my_custom_function_tion', 10, 5 );
add_filter( 'gform_field_content_4_68', 'my_custom_function_tion', 10, 5 );
add_filter( 'gform_field_content_4_74', 'my_custom_function_tion', 10, 5 );
add_filter( 'gform_field_content_4_80', 'my_custom_function_tion', 10, 5 );
add_filter( 'gform_field_content_4_86', 'my_custom_function_tion', 10, 5 );
function my_custom_function_tion( $content, $field, $value, $lead_id, $form_id ){
	
	$content = '<label class="gfield_label" for="input_4_'.$field->id.'">'.__('Catégorie de produits','symcod').'<span class="gfield_required">*</span></label>
	<div class="ginput_container ginput_container_select">
		<select name="input_'.$field->id.'" id="input_4_'.$field->id.'" class="medium gfield_select" aria-required="true" aria-invalid="false">';
				$terms = get_terms( 'catprods', array(
					'hide_empty' => false,
				) );
				foreach( $terms as $term ){
					$content .= '<option value="'.$term->term_id.'">'.$term->name.'</option>';
				}

	$content .= '</select>
	</div>';
	return $content;
}

