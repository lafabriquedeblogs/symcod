<?php
/**
 *
 *	Template name: Documentation - Ajax
 */

get_header();
?>

	<div id="primary" class="content-area">
			<header class="entry-header single-produits">
				<div class="big-max-width">
					<div class="max-width">
						<div class="entry-title-div">
							<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
						</div><!-- entry-title-div -->
					</div><!-- max-width -->
				</div>
			</header><!-- .page-header -->

		
		<section id="section-docu-search" class="section-page">
			
			<div id="max-width-docu-search" class="max-width">
				<div id="section-body-docu-search" class="section-body">
					<a id="search-anchor" style="position:absolute;top:-187px;"></a>
					<h2><?php _e('Chercher un document','symcod'); ?></h2>
					<h4 id="version-product-title"><a href="" rel="bookmark"><span class="etape bordered"><?php _e('Étape','symcod'); ?> 1</span></a> <?php _e('Choisir un produit ?','symcod'); ?></h4>
						<div id="docu-search-fields-wrapper">
							<div id="div-nom-produit">
								<form id="form-nom-produit">
									<div class="form-nom-produit-champ" id="champ-text">
										<!-- <label class="input-label" for="nom-produit"><?php _e('Tous les produits','symcod'); ?><?php /*_e('Saisir le nom du produit','symcod');*/ ?></label> -->
										<input type="text" id="nom-produit" name="nom-produit" placeholder="<?php _e('Tous les produits','symcod'); ?>"/>
									</div>
									<div class="form-nom-produit-champ" id="champ-submit">
										<div class="wp-block-button">
											<input id="submiter-nom-produit" class="bouton"  type="submit" value="<?php _e('Chercher','symcod'); ?>"/>
										</div>
									</div>				


								</form>
							</div>

						</div><!-- #docu-search-fields-wrapper -->
						<ul id="documentation-search-results">
						<?php
							$args = array(
								'post_type' => array('produits'),
								'posts_per_page' => -1,
								'post_status' => 'publish',
								'orderby' => 'menu_order',
								'order' => 'ASC'
							);
							
							$products_list = get_product_list($args);
							
							echo $products_list;
							
						?>							
						</ul>		
				</div><!-- #section-body-docu-search -->
			</div><!-- #max-width-docu-search -->
		</section>

		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', 'page' );
		endwhile; // End of the loop.
		?>

	</div><!-- #primary -->

<?php
get_footer();
