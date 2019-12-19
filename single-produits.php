<?php
		get_header();

		$post_id = get_the_id();
		$permalink = get_permalink( $post_id );
		
		$version = get_query_var('version');
		$categorie = get_query_var('doc');
		
		
		$versions_produit_disponibles_raw =  str_replace("<br />", ",", get_field('versions_produit'));
		$versions_produit_disponibles = preg_replace('/\s/', '', $versions_produit_disponibles_raw);
		$versions_produit_array = explode(",", $versions_produit_disponibles);
		
		$cats_produit_disponibles_raw =  str_replace("<br />", ",", get_field('categories_de_document'));
		$cats_produit_array = explode(",", $cats_produit_disponibles_raw);
				
?>

	<div id="primary" class="content-area">

			<header class="entry-header">
				<div class="big-max-width">
					<?php //symcod_archive_thumbnail(1); ?>
					<div class="max-width">
						<div class="entry-title-div">
							<h1 class="entry-title"><?php _e('Téléchargements','symcod'); ?></h1>
						</div><!-- entry-title-div -->
					</div><!-- max-width -->
				</div>
			</header><!-- .page-header -->	
		<?php
		
		//while ( have_posts() ) :
		
			//the_post();
			//$etape_1_permalink = get_permalink( apply_filters( 'wpml_object_id', 55, '', TRUE  ));
			
			$product_permalink = get_permalink(get_the_id());
			
			$documentation_permalink = get_permalink( apply_filters( 'wpml_object_id', 55, 'page', TRUE  ));
			
		?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						
				<div class="entry-content">
					<?php the_content(); ?>

					<section id="section-docu-search" class="section-page">
						<a id="search-anchor" style="position:absolute;top:-187px;"></a>
						<div id="max-width-docu-search" class="max-width">
							<div id="section-body-docu-search" class="section-body">
					
								<h2><?php _e('Chercher un document','symcod'); ?></h2>
								
								<div id="entry-header-produit">
								<div>
									<h3 id="produit-title" class="entry-title"><a href="<?php echo $documentation_permalink;?>" rel="bookmark"><span class="etape"><?php _e('Étape','symcod'); ?> 1</span></a><span class="serach-step-header"><a href="<?php echo $documentation_permalink;?>/" rel="bookmark"><?php _e('Produit','symcod'); ?>:</a></span> <?php the_title();?></h3>	
								</div>	
								

									<?php
										
										/*
										 *
										 *  VERSION ALL
										 *	
										*/
										
										if( !empty( $version) && $version == 'all' && empty($categorie) ):  ?>
											
											<h4 id="choisir-version"><a href="<?php echo $product_permalink;?>version/all/"><span class="etape bordered"><?php _e('Étape','symcod'); ?> 2</span></a><span><?php _e('Choisir une version ?','symcod'); ?></span></h4>
											
											</div><!-- #entry-header-produit -->
											
											<ul id="documentation-search-results">
												
												<?php
													if( have_rows('ajouter_un_produit_version') ):
														
														$result = '';
														
													    while ( have_rows('ajouter_un_produit_version') ) : the_row();
															
															$version_prod = get_sub_field( 'version' );
															$description_prod =  get_sub_field( 'description' );
															$image_prod_data =  get_sub_field( 'image' );				
															$image_prod = $image_prod_data['sizes']['thumbnail'];
															$image_prod_full = $image_prod_data['sizes']['large'];
														?>
																<li id="result-item-<?php echo  get_the_id();?>" class="document-list-result-item categorie-document-list-result-item">
																	<header>
																		<figure>
																			<a href="<?php echo $permalink."version/".$version_prod;?>/doc/all/"><img src="<?php echo $image_prod;?>" alt="" width="150" height="150" /></a>
																			<p>
																				<a href="<?php echo $image_prod_full;?>" class="loupe-produit-version" data-fancybox>
																					<svg id="loupe" x="0px" y="0px" viewBox="0 0 21 21"><path d="M20.6,19.1l-6.5-6.5c1-1.3,1.6-3,1.6-4.8c0-4.3-3.5-7.9-7.9-7.9S0,3.5,0,7.9s3.5,7.9,7.9,7.9c1.8,0,3.4-0.6,4.7-1.6l6.5,6.5 c0.2,0.2,0.5,0.3,0.8,0.3c0.3,0,0.6-0.1,0.8-0.3C21.1,20.3,21.1,19.6,20.6,19.1z M2.2,7.9c0-3.1,2.6-5.7,5.7-5.7s5.7,2.6,5.7,5.7 s-2.6,5.7-5.7,5.7S2.2,11,2.2,7.9z"/></svg>
																				</a>
																			</p>
																		</figure>
																	</header>
																	<div class="docuemnt-entry">
																		<p class="docuemnt-meta">
																			<span class="version"><?php _e('Version','symcod'); ?>: <a href="<?php echo $permalink."version/".$version_prod;?>/doc/all/"><strong><?php echo $version_prod;?></strong></a></span>
																		</p>
																		<p class="description"><?php echo $description_prod;?></p>
																	</div>
																</li>
														<?php
													    endwhile;
													else :
													
													endif;
												?>
											</ul>
										<?php endif;?>

										<?php
										
										/*
										 *
										 *  DOC ALL
										 *	&& $categorie == 'all'
										*/

										if(  !empty( $version) && $version != 'all' && !empty($categorie) ):  ?>
											
											<div class="serach-step-header">
												<h3 id="version-product-title"><a href="<?php echo $product_permalink;?>version/all/"><span class="etape"><?php _e('Étape','symcod'); ?> 2</span></a><span><a href="<?php echo $product_permalink;?>version/all/">Version: </a></span><?php echo $version;?></h3>
												
											</div>
											</div><!-- #entry-header-produit -->
											
											<!-- <h3 id="categorie-title" class="inactif"><?php _e('Tous les documents','symcod'); ?></h3> -->
											
											<?php
												$all_rows = get_field( 'ajouter_un_produit_version' );
														
												$product_by_version =  array();
												$tous_les_documents = array();
												$liste_des_categories_select = array();
												$document_ids = array();
												
												// Récupération des documents appartement à la version choisie du produit																								
												foreach( $all_rows as $row ){
													if( $row['version'] ==  $version ){
														$product_by_version[] = $row;
													}
												}
												
												$tous_les_documents = $product_by_version[0]['ajouter_un_document'];
/*
												echo '<pre>';
													var_dump($tous_les_documents);
												echo '</pre>';
												// Extraction et Ajout des document dans un tableau
												foreach( $product_by_version[0] as $doc ){
													$tous_les_documents = $doc['ajouter_un_document'];
												}	
*/
												
												
												foreach( $tous_les_documents as $doc ){

													$terms = get_the_terms( $doc['document']->ID, 'taxdocument' );
													$term = $terms[0];

													
													$document_ids[] = array( 'ID' => $doc['document']->ID, "cat" => $term->name, "menu_order" => $term->menu_order);	
																							
													if( ! in_array($term->name, $liste_des_categories_select ) ){
														$liste_des_categories_select[] = $doc['categorie_du_document'];
													}
												}
												
												$categories_array = array();
												$categorie_display. array();
												
												foreach( $document_ids as $doc ){
													
													if( !in_array( $doc['cat'], $categorie_display )){
														
														$categorie_display[] = $doc['cat'];
													
														$category_order = get_term_by('name',$doc['cat'] ,'taxdocument' );
														$category_id_order = intval( get_field('id_ordre',$category_order));
														
														if( empty($category_id_order)) $category_id_order = 0;
														
														//$categories_array[] = array($doc['menu_order'],$doc['cat']);
														$categories_array[] = array($category_id_order,$doc['cat']);
													}
												}

												sort($categories_array);

																		
												$all_docs = array();
												
												foreach( $categories_array as $a ){
													$dd = array();
													
													foreach( $tous_les_documents as $doc ){
														
														$terms = get_the_terms( $doc['document']->ID, 'taxdocument' );
														$term = $terms[0];
														
														if( $term->name == $a[1]){
															$dd[] = array($doc['document']->menu_order,$doc['document']->post_title,$doc['document']->ID);
														}
													}
													sort($dd);
													$all_docs[$a[0].'-'.$a[1]] = $dd;
												}
												
											?>
											
											<form id="form-categorie-document">
												<a href="" rel="bookmark"><span class="etape bordered"><?php _e('Étape','symcod'); ?> 3</span></a>
												<select id="categorie-document" name="categorie-document">
													<option value="<?php echo $product_permalink;?>version/<?php echo $version;?>/doc/all/" ><?php _e('Tous les documents','symcod'); ?></option>
													
													<?php
																																					
														//foreach( $categories_array as $cat ){
														foreach($all_docs as $cat => $value ){
															//$slug_cat = sanitize_title( $cat[1] );
															//$selected = ( $slug_cat == $categorie ) ? 'selected' : '';
															$slug_cat = sanitize_title( $cat );
															$selected = ( $slug_cat == $categorie ) ? 'selected' : '';
													?>
															
															<!-- <option value="<?php echo $product_permalink;?>version/<?php echo $version;?>/doc/<?php echo sanitize_title( $cat[1] );?>/" <?php echo $selected;?>><?php echo $cat[1];?></option> -->
															<option value="<?php echo $product_permalink;?>version/<?php echo $version;?>/doc/<?php echo sanitize_title( $cat );?>/" <?php echo $selected;?>><?php echo $cat;?></option>
													<?php } ?>												
												</select>
												
												<input type="hidden" id="version-produit" value="<?php echo $version;?>" />
												<input type="hidden" id="input_product_id" value="<?php echo get_the_id();?>" />
											</form>

																						
											<ul id="documentation-search-results">
												
												<?php
											
																			
													foreach( $all_docs as $doc_cat => $doc ){															
														
															
															echo '<li data-categorie="'.$doc_cat.'" class="document-list-result-item categorie-document-list-result-item document-list-result-item-title-li"><h4>'.$doc_cat.'</h4></li>';
										
															
															foreach( $doc as $d ){
																
																$document = $d[2];
																
																
																$image_data = get_field('image',$document);
																
																$nom_du_document = get_field('nom_du_document',$document) ? get_field('nom_du_document',$document) :  get_the_title( $document );
																
																$type_de_document = get_field('type_de_document',$document);
																$version_du_document = get_field('version_du_document',$document);
																$telecharger_un_document = get_field('telecharger_un_document',$document);
																$url_du_document = get_field('url_du_document',$document);
																$description_courte = get_field('description_courte',$document);
																
																$menu_order = get_post_field('menu_order', $document);
																
																if($telecharger_un_document){
																	$url_du_document = $telecharger_un_document;
																}
																
																$image = $image_data["sizes"]['thumbnail'];
																$image_prod_full = $image_data["sizes"]['large'];																
																												
														?>
															<li id="result-item-<?php echo  $document;?>" data-categorie="<?php echo $doc_cat;?>" class="document-list-result-item categorie-document-list-result-item document--item">
																	
																	<?php if($image == "Hello dude!"): ?>
																	<header>
																		<figure>
																			<img src="<?php echo $image;?>" alt="" width="150" height="150" />
																			
																		</figure>
																		<div class="icon-download">
																			<a  href="<?php echo $image_prod_full;?>" class="loupe-produit-version" data-fancybox>
																				<svg id="loupe" x="0px" y="0px" viewBox="0 0 21 21"><path d="M20.6,19.1l-6.5-6.5c1-1.3,1.6-3,1.6-4.8c0-4.3-3.5-7.9-7.9-7.9S0,3.5,0,7.9s3.5,7.9,7.9,7.9c1.8,0,3.4-0.6,4.7-1.6l6.5,6.5 c0.2,0.2,0.5,0.3,0.8,0.3c0.3,0,0.6-0.1,0.8-0.3C21.1,20.3,21.1,19.6,20.6,19.1z M2.2,7.9c0-3.1,2.6-5.7,5.7-5.7s5.7,2.6,5.7,5.7 s-2.6,5.7-5.7,5.7S2.2,11,2.2,7.9z"/></svg>
																			</a>
																		</div>
																	</header>
																	<?php endif; ?>
																	
																	<div class="docuemnt-entry">
																		<h3 data-menu-order="<?php echo $menu_order;?>"><?php echo $menu_order;?> - <?php echo $nom_du_document;?>
																		
																		<?php if( !empty( $version_du_document )): ?>
																		 <span class="version"><?php _e('Ver.','symcod'); ?>: <strong><?php echo $version_du_document;?></strong></span>
																		<?php endif; ?>
																		
																		</h3>

																		<p class="description"><?php echo $description_courte;?></p>
																		<?php
																		/*
																		<p class="docuemnt-meta">
																			<span class="type"><?php _e('Type de document','symcod'); ?>: <strong><?php echo strtoupper( $type_de_document["ID"]);?></strong></span>
																		</p>
																		*/
																		?>
																		<a href="<?php echo $url_du_document; ?>" target="_blank" class="download">
																			<svg class="icon-type-download" width="64" height="64">
																			    <use xlink:href="<?php echo esc_url( "{$type_de_document['_file_url']}#{$type_de_document['ID']}" ); ?>"></use>
																			</svg>
																			<span><?php _e('Télécharger le document','symcod'); ?></span>
																		</a>
																	</div>
															</li>														
												<?php 
														}
													} 
													
												?>
											</ul>
											<script>
												jQuery(document).ready( function($){
													
													var cat_title = $("option:selected", "#categorie-document").text();
													console.log(cat_title);
													//$("#categorie-title").html( cat_title );
													$(".categorie-document-list-result-item").each( function(){
														if( $(this).attr("data-categorie") != cat_title && cat_title != Documentation.tous_les_documents){
															$(this).hide();
														}else {
															$(this).show();
														}
													});
												});
											</script>
											<div id="selected-final-result"></div>
											
										<?php endif;?>
																										
							</div><!-- #section-body-docu-search -->
						</div><!-- #max-width-docu-search -->
					</section>


				</div><!-- .entry-content -->
			
			
			</article><!-- #post-<?php the_ID(); ?> -->		
		<?php
		///endwhile; // End of the loop.
		?>	
	</div><!-- #primary -->
 
	<?php
		
		$term = get_term( 1, 'category');
		
	?>
			<section id="section-page-archives" class="section-page information">
				<div id="max-width" class="max-width">
					<div id="section-body" class="section-body">
						<div class="wp-block-columns has-2-columns">
							<div class="wp-block-column colonne-titre">
								<h2><?php echo get_field('titre',$term);?></h2>
							</div>
							<div class="wp-block-column colonne-cta">
								<div class="wp-block-button"><a class="bouton" href="<?php echo get_field('lien_bouton',$term);?>" target="<?php echo get_field('cible_buton',$term);?>"><?php echo get_field('texte_bouton',$term);?></a></div>
							</div>
						</div>
					</div>
				</div>
			</section>
<?php
	
get_footer();
