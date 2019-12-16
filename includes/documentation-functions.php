<?php
	
	/*
	 *
	 *	Recherches produits
	 *	return - Versions disponibles
	*/
	function docu_search_nom_produits(){
		
		add_filter('posts_join', 'cf_search_join' );
		add_filter('posts_where', 'my_posts_where',10,2);
		add_filter( 'posts_distinct', 'cf_search_distinct' );
		
		$nonce = $_POST['mynonce'];
		
		if ( ! wp_verify_nonce( $nonce, 'documentation-script-nonce' ) ) {
			die ( 'Busted!');
		}
		
		$search_term = $_POST['term'];
		
		$response_array = array();
		
		
		$desc = 'description';
		
		$args = array(
			'post_type' => array('produits'),
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'suppress_filters' => false,
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'search_prod_title' => $search_term,
		);
		
		$products_list = get_product_list($args);
		

		if( !$products_list ){
			$response_array['no_post'] = __("Votre recherche n'a donné aucun resultat");
		} else {
			$response_array['les_posts'] = $products_list;
		}
				
		// generate the response
		$response = json_encode( $response_array );
		
		// response output
		header( "Content-Type: application/json" );
		echo $response;
		
		remove_filter('posts_join', 'cf_search_join' );
		remove_filter('posts_where', 'my_posts_where',10,2);
		remove_filter( 'posts_distinct', 'cf_search_distinct' );
		
		// IMPORTANT: don't forget to "exit"
		exit;
	}
	
	add_action( 'wp_ajax_docu_search_nom_produits', 'docu_search_nom_produits' );
	add_action( 'wp_ajax_nopriv_docu_search_nom_produits', 'docu_search_nom_produits' );

	
	function get_product_list( $args){
		
		$product_query = new WP_Query($args);
		
		$result = '';
					
		if( $product_query->have_posts() ):
			
			$count = $product_query->post_count;
			$response_array['post_count'] = $count;
			
			while( $product_query->have_posts() ){

				$product_query->the_post();
				
				$versions_produit = str_replace("<br />", ",", get_field('versions_produit'));
				$categorie_de_document = str_replace("<br />", ",", get_field('categories_de_document'));
				
				$the_post["id"] = get_the_id();
				$the_post["title"] = get_the_title();
				$the_post["versions_produit"] = explode(",",$versions_produit);
				$the_post["categorie_de_document"] =  explode(",",$categorie_de_document);
				
				$thumbnail = get_the_post_thumbnail(  get_the_id(), 'thumbnail', array() );
				$image_prod_full =   get_the_post_thumbnail_url( get_the_id(),'large' );
				$permalien = get_permalink( get_the_id());

				ob_start();
			?>
					<li id="result-item-<?php echo  get_the_id();?>" class="document-list-result-item categorie-document-list-result-item">
						<header>
							<figure>
								<a href="<?php echo $permalien;?>version/all/#search-anchor" class="produit-document-list-result-item" data-product="<?php echo  get_the_id();?>" data-versions="<?php echo $versions_produit;?>" data-categories="<?php echo $categorie_de_document;?>">
									<?php echo  $thumbnail;?>
								</a>
								<p>
									<a href="<?php echo $image_prod_full;?>" class="loupe-produit-version" data-fancybox>
										<svg id="loupe" x="0px" y="0px" viewBox="0 0 21 21"><path d="M20.6,19.1l-6.5-6.5c1-1.3,1.6-3,1.6-4.8c0-4.3-3.5-7.9-7.9-7.9S0,3.5,0,7.9s3.5,7.9,7.9,7.9c1.8,0,3.4-0.6,4.7-1.6l6.5,6.5 c0.2,0.2,0.5,0.3,0.8,0.3c0.3,0,0.6-0.1,0.8-0.3C21.1,20.3,21.1,19.6,20.6,19.1z M2.2,7.9c0-3.1,2.6-5.7,5.7-5.7s5.7,2.6,5.7,5.7 s-2.6,5.7-5.7,5.7S2.2,11,2.2,7.9z"/></svg>
									</a>
								</p>
							</figure>
						</header>
						<div class="docuemnt-entry">
							<a href="<?php echo $permalien;?>version/all/#search-anchor" class="produit-document-list-result-item" data-product="<?php echo  get_the_id();?>" data-versions="<?php echo $versions_produit;?>" data-categories="<?php echo $categorie_de_document;?>">
								<h3><?php echo get_the_title();?></h3>
							</a>
							<p class="description"><?php echo get_the_excerpt();?></p>
						</div>
					</li>



			<?php
				$result .= ob_get_clean();	
				
			}				
			
			return $result;
			
		else:
			return false;
		endif;		
	}
	
	function cf_search_join( $join ) {
	    global $wpdb;
        $join .=' LEFT JOIN '.$wpdb->postmeta. ' cfmeta ON '. $wpdb->posts . '.ID = cfmeta.post_id ';
	
	    return $join;
	}
	
	
	function my_posts_where( $where, &$wp_query ) {
		global $wpdb;
	
		if ( $search_term = $wp_query->get( 'search_prod_title' ) ){
			$where .= ' AND ('. $wpdb->posts . '.post_title LIKE "%' .  $wpdb->esc_like( $search_term ) . '%" OR '. $wpdb->posts . '.post_excerpt LIKE "%' . $wpdb->esc_like( $search_term ) . '%")';
			//$where .= ' OR '. $wpdb->posts . '.post_excerpt LIKE "%' . $wpdb->esc_like( $search_term ) . '%"';
			$where .= ' OR ( cfmeta.meta_key LIKE "ajouter_un_produit_version_%" AND cfmeta.meta_value LIKE "%' . $wpdb->esc_like( $search_term ) . '%")';
			$where .= ' AND (' . $wpdb->posts . '.post_status = "publish" )';
		}

		
		return $where;
	}
	
	
	function cf_search_distinct( $where ) {
	    global $wpdb;
	    return "DISTINCT";
	}
	
	function docu_search_categorie_document(){
		$nonce = $_POST['mynonce'];
		
		if ( ! wp_verify_nonce( $nonce, 'documentation-script-nonce' ) ) {
			die ( 'Busted!');
		}
		
		$version = $_POST['version'] ;
		$product_id = $_POST['product_id'];
		$input_cat =  preg_replace('/\n/', '', $_POST['input_cat']);
		
		$all_rows = get_field( 'ajouter_un_produit_version' , $product_id);
		
		$product_by_version =  array();
		$tous_les_documents = array();
		$liste_des_categories_select = array();
		$document_ids = array();
		$documents_finaux = array();
															
		foreach( $all_rows as $row ){
			if( $row['version'] ==  $version ){
				$product_by_version[] = $row;
			}
		}
		
		foreach( $product_by_version as $doc ){
			$tous_les_documents[] = $doc['ajouter_un_document'];
		}
		
		foreach( $tous_les_documents[0] as $doc ){
			
			if( $doc['categorie_du_document'] == $input_cat ){
				$documents_finaux[] = $doc['document'];
			}

		}
		
		$response_array = array();
		$response_array["product_id"] = $product_id;
		$response_array["version"] = $documents_finaux;
		$response_array["categorie"] = preg_replace('/\n/', '', $input_cat); ;
		
		$response = json_encode( $response_array );
		
				
		// response output
		header( "Content-Type: application/json" );
		echo $response;
		
		// IMPORTANT: don't forget to "exit"
		exit;
		
	}

	add_action( 'wp_ajax_docu_search_categorie_document', 'docu_search_categorie_document' );
	add_action( 'wp_ajax_nopriv_docu_search_categorie_document', 'docu_search_categorie_document' );

	
	function get_document_by_versions( $product_id, $version ){
			
			$the_rows = get_field('document',$product_id);
			
			
			$filtered_version = array_filter($the_rows, function ($item) use ($version) {
			    if (stripos($item['version_produit'], $version) !== false) {
			        return true;
			    }
			    return false;
			});	
			
			return 	$filtered_version;	
	}
	
	function get_document_cat_by_version( $product_id, $version, $input_cat ){
			
			$filtered_version = get_document_by_versions($product_id, $version);			

			$filtered_cat = array_filter($filtered_version, function ($item) use ($input_cat) {
			    if (stripos($item['categorie_de_document'], $input_cat) !== false) {
			        return true;
			    }
			    return false;
			});
			
			return $filtered_cat;
			
				
	}
	
	
	//field_5cfa95fd19fd4
	function my_acf_load_field( $field ) {
		//global $post;
		
		$post_id = empty($_GET['post']) ? 0 : $_GET['post'];
	    
	    $choices = get_field('versions_produit',$post_id,false);
		$choices = explode("\n", $choices);	
		// remove any unwanted white space
		$choices = array_map('trim', $choices);
		foreach( $choices as $choice ) {
			//$field['value'] =  sanitize_title( $choice );
			$field['choices'][$choice] = $choice;
		}
	
	    return $field;
	    
	}
	add_filter('acf/load_field/key=field_5cfa95fd19fd4', 'my_acf_load_field');
	
	
	
	// choix de la version de produit fr
	//field_5da61f7097ecc
	function my_acf_load_field_too( $field ) {
		//global $post;
		
		$post_id = empty($_GET['post']) ? 0 : $_GET['post'];
	    
	    $choices = get_field('versions_produit',$post_id,false);
		$choices = explode("\n", $choices);	
		// remove any unwanted white space
		$choices = array_map('trim', $choices);
		foreach( $choices as $choice ) {
			//$field['value'] =  sanitize_title( $choice );
			$field['choices'][$choice] = $choice;
		}
	
	    return $field;
	    
	}
	add_filter('acf/load_field/key=field_5da61f7097ecc', 'my_acf_load_field_too');	
	
	// choix de la version de produit en
	//field_5dea61e89ed39
	//acf-field-5df7b53a66530
	function my_acf_load_field_tooo( $field ) {
		//global $post;
		
		$post_id = empty($_GET['post']) ? 0 : $_GET['post'];
	    
	    $choices = get_field('versions_produit',$post_id,false);
		$choices = explode("\n", $choices);	
		// remove any unwanted white space
		$choices = array_map('trim', $choices);
		foreach( $choices as $choice ) {
			//$field['value'] =  sanitize_title( $choice );
			$field['choices'][$choice] = $choice;
		}
	
	    return $field;
	    
	}
	add_filter('acf/load_field/key=field_5df7b53a66530', 'my_acf_load_field_tooo');	
	
	//field_5cfa9ad184fe0
	function my_acf_load_field_categories_de_document( $field ) {
		
		//global $post;
		
		$post_id = empty($_GET['post']) ? 0 : $_GET['post'];

	    $choices = get_field('categories_de_document',$post_id,false);
		$choices = explode("\n", $choices);	
		// remove any unwanted white space
		$choices = array_map('trim', $choices);
		foreach( $choices as $choice ) {
			//$field['value'] =  sanitize_title( $choice );
			$field['choices'][$choice] = $choice;	
		}
	
	    return $field;
	    
	}
	add_filter('acf/load_field/key=field_5db0a5d5201ad', 'my_acf_load_field_categories_de_document');
	