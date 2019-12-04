<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package symcod
 */

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
			
			$product_permalink = get_permalink(get_the_id());
			
			$documentation_permalink = get_permalink( apply_filters( 'wpml_object_id', 55, 'produits', TRUE  ));
			
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
									<h3 id="produit-title" class="entry-title"><span class="etape"><a href="<?php echo $documentation_permalink;?>/#search-anchor" rel="bookmark"><?php _e('Étape','symcod'); ?> 1</a></span><span class="serach-step-header"><a href="<?php echo $documentation_permalink;?>/#search-anchor" rel="bookmark"><?php _e('Produit','symcod'); ?>:</a></span> <?php the_title();?></h3>	
								</div>	
								

									<?php
										
										/*
										 *
										 *  VERSION ALL
										 *	
										*/
										
										if( !empty( $version) && $version == 'all' && empty($categorie) ):  ?>
											
											<h4 id="choisir-version"><span class="etape bordered"><a href="<?php echo $product_permalink;?>version/all/#search-anchor"><?php _e('Étape','symcod'); ?> 2</a></span><span><?php _e('Choisir une version ?','symcod'); ?></span></h4>
											
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
																			<a href="<?php echo $permalink."version/".$version_prod;?>/doc/all/#search-anchor"><img src="<?php echo $image_prod;?>" alt="" width="150" height="150" /></a>
																			<p>
																				<a href="<?php echo $image_prod_full;?>" class="loupe-produit-version" data-fancybox>
																					<svg id="loupe" x="0px" y="0px" viewBox="0 0 21 21"><path d="M20.6,19.1l-6.5-6.5c1-1.3,1.6-3,1.6-4.8c0-4.3-3.5-7.9-7.9-7.9S0,3.5,0,7.9s3.5,7.9,7.9,7.9c1.8,0,3.4-0.6,4.7-1.6l6.5,6.5 c0.2,0.2,0.5,0.3,0.8,0.3c0.3,0,0.6-0.1,0.8-0.3C21.1,20.3,21.1,19.6,20.6,19.1z M2.2,7.9c0-3.1,2.6-5.7,5.7-5.7s5.7,2.6,5.7,5.7 s-2.6,5.7-5.7,5.7S2.2,11,2.2,7.9z"/></svg>
																				</a>
																			</p>
																		</figure>
																	</header>
																	<div class="docuemnt-entry">
																		<p class="docuemnt-meta">
																			<span class="version"><?php _e('Version','symcod'); ?>: <a href="<?php echo $permalink."version/".$version_prod;?>/doc/all/#search-anchor"><strong><?php echo $version_prod;?></strong></a></span>
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
												<h3 id="version-product-title"><span class="etape"><a href="<?php echo $product_permalink;?>version/all/#search-anchor"><?php _e('Étape','symcod'); ?> 2</a></span><span><a href="<?php echo $product_permalink;?>version/all/#search-anchor">Version: </a></span><?php echo $version;?></h3>
												
											</div>
											</div><!-- #entry-header-produit -->
											
											<!-- <h3 id="categorie-title" class="inactif"><?php _e('Tous les documents','symcod'); ?></h3> -->
											
											<?php
												$all_rows = get_field( 'ajouter_un_produit_version' );
														
												$product_by_version =  array();
												$tous_les_documents = array();
												$liste_des_categories_select = array();
												$document_ids = array();
																																						
												foreach( $all_rows as $row ){
													if( $row['version'] ==  $version ){
														$product_by_version[] = $row;
													}
												}
												
												foreach( $product_by_version as $doc ){
													$tous_les_documents[] = $doc['ajouter_un_document'];
												}
												
												foreach( $tous_les_documents[0] as $doc ){
													
													$category = wp_get_post_terms( $doc['document'], 'taxdocument', array('fields' => 'all') );
													
													
													$document_ids[] = array( $doc['document'], $category[0]->name);
													
													if( ! in_array( $category[0]->name, $liste_des_categories_select ) ){
														
														$liste_des_categories_select[] = $category[0]->name;
													}
												}
												
											?>
											
											<form id="form-categorie-document">
												<span class="etape bordered"><a href="<?php echo $documentation_permalink;?>/#search-anchor" rel="bookmark"><?php _e('Étape','symcod'); ?> 3</a></span>
												<select id="categorie-document" name="categorie-document">
													<option value="<?php echo $product_permalink;?>version/<?php echo $version;?>/doc/all/#search-anchor" ><?php _e('Tous les documents','symcod'); ?></option>
													
													<?php
																																					
														foreach( $liste_des_categories_select as $cat ){
															$slug_cat = sanitize_title( $cat );
															$selected = ( $slug_cat == $categorie ) ? 'selected' : '';
													?>
															
															<option value="<?php echo $product_permalink;?>version/<?php echo $version;?>/doc/<?php echo sanitize_title( $cat );?>/#search-anchor" <?php echo $selected;?>><?php echo $cat;?></option>
													<?php } ?>												
												</select>
												
												<input type="hidden" id="version-produit" value="<?php echo $version;?>" />
												<input type="hidden" id="input_product_id" value="<?php echo get_the_id();?>" />
											</form>

											<?php /* <h2>Yep yep!</h2> */ 
											
														//tableau de sortie
														$full_array = array();
											?>
											
											<ul id="documentation-search-results">
												
												<?php
													
													// tableau des categories avec les documents
													$categories_array = array();
													$categorie_display. array();
													
													foreach( $document_ids as $doc ){
														
														$categorie = $doc[1];
														
														if( !in_array( $categorie, $categorie_display )){
															
															$categorie_display[] = $categorie;
														
															$category_order = get_term_by('name',$categorie ,'taxdocument' );
															$category_id_order = get_field('id_ordre',$category_order);
															
															$categories_array[$category_id_order][] = $categorie;
															//$categories_array[$category_id_order][] = '<li data-categorie="'.$categorie.'" class="document-list-result-item categorie-document-list-result-item document-list-result-item-title-li"><h4>'.$category_id_order.' - '.$categorie.'</h4></li>';
														}
													}
													echo '<pre>';
														var_dump($categories_array);
													echo '</pre>';
												
													foreach( $document_ids as $doc ){
																												
														$document = $doc[0];
														
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
																												
														$categorie = $doc[1];
														
														foreach($categories_array as $k => $cat){
															
															
															$category_order = get_term_by('name',$cat[0] ,'taxdocument' );
															$category_id_order = get_field('id_ordre',$category_order);
															
															
															
															if( has_term( $cat[0],'taxdocument',$document ) ){
																
																$documents_array_wrap = array();
																$documents_array = array();
																
																$documents_array['menu_order'] = $menu_order;
																
																ob_start();
													
																
															?>
																	<li id="result-item-<?php echo  $document;?>" data-categorie="<?php echo $categorie;?>" class="document-list-result-item categorie-document-list-result-item document--item">
																			
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
																				<h3><?php echo $menu_order;?> - <?php echo $nom_du_document;?>
																				
																				<?php if( !empty( $version_du_document )): ?>
																				 <span class="version"><?php _e('Ver.','symcod'); ?>: <strong><?php echo $version_du_document;?></strong></span>
																				<?php endif; ?>
																				
																				</h3>
																	
																				<p class="description"><?php echo $description_courte;?></p>
																				<a href="<?php echo $url_du_document; ?>" target="_blank" class="download">
																					<svg class="icon-type-download" width="64" height="64">
																					    <use xlink:href="<?php echo esc_url( "{$type_de_document['_file_url']}#{$type_de_document['ID']}" ); ?>"></use>
																					</svg>
																					<span><?php _e('Télécharger le document','symcod'); ?></span>
																				</a>
																			</div>
																	</li>
																										
															<?php 																
																
																$content = ob_get_clean();
																$documents_array['document'] = $nom_du_document;//;
																
																
																
																$categories_array[$k][] = $documents_array;
																
															}
															sort($categories_array[$k]);
															/*
															usort($categories_array[$k][],function($a,$b){
																return $a['menu_order'] - $b['menu_order'];
															});
*/
														}

													
														} 
													ksort($categories_array);	
													echo '<pre>';
													   var_dump(json_encode($categories_array,JSON_PRETTY_PRINT));
													   //var_dump($categories_array);
													echo '</pre>';
													
														
												?>
											</ul>
											<script>
												jQuery(document).ready( function($){
													
													var cat_title = $("option:selected", "#categorie-document").text();
													$("#categorie-title").html( cat_title );

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
