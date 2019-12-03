<?php
//taxdocument

function create_taxdocument_column_for_taxdocument( $taxdocument_columns ) {
  $taxdocument_columns['trie_id'] = 'Trier par ID';
  return $taxdocument_columns;
}


function populate_ordre_column_for_taxdocuments( $value, $column_name, $term_id ) {
  
  $taxdocument = get_term($term_id, 'taxdocument');
  
  $ordre = get_field('id_ordre',$taxdocument);
  
  switch($column_name) {
    case 'trie_id': 
      $value = intval($ordre);
    break;
    default:
    break;
  }
  return $value;    
}

function register_date_column_for_issues_sortable($columns) {
  $columns['trie_id'] = 'id_ordre';
  return $columns;
}



add_filter( 'terms_clauses', function( $pieces, $taxonomies, $args ) {
	global $pagenow, $wpdb;
	$custom_sort_term = 'id_ordre';
	$custom_taxonomy = 'taxdocument';
	$orderby = ( isset( $_GET[ 'orderby' ] ) ) ? trim( sanitize_text_field( $_GET[ 'orderby' ] ) ) : '';
	if ( empty( $orderby ) ) { return $pieces; }
	$taxonomy = $taxonomies[ 0 ];
	if ( ! is_admin() || 'edit-tags.php' !== $pagenow || ! in_array( $taxonomy, [ $custom_taxonomy ] ) ) {
		return $pieces;
	}

	
	if ( $custom_sort_term ===  $orderby ) {
		$pieces[ 'join' ] .= ' INNER JOIN ' . $wpdb->termmeta . ' AS tm ON t.term_id = tm.term_id ';
		$pieces[ 'orderby' ]  = ' ORDER BY tm.meta_value ';
		$pieces[ 'where' ] .= ' AND tm.meta_key = "id_ordre"';
	}
	return $pieces;
}, 10, 3 );

add_filter('manage_edit-taxdocument_columns', 'create_taxdocument_column_for_taxdocument');
add_filter('manage_taxdocument_custom_column', 'populate_ordre_column_for_taxdocuments', 10, 3);
add_filter('manage_edit-taxdocument_sortable_columns', 'register_date_column_for_issues_sortable');

add_filter( 'get_terms_args', 'wpse_53094_sort_get_terms_args', 10, 2 );


function wpse_53094_sort_get_terms_args( $args, $taxonomies ) 
{
    global $pagenow;
    global $post;
    
    if( !is_admin() || ('post.php' != $pagenow && 'post-new.php' != $pagenow) ) 
        return $args;
		
    $args['orderby'] = 'id_ordre';
    $args['order'] = 'desc';

    return $args;
}

/*
 *
 *
 *
 *
*/


function add_new_documen_text_column($header_text_columns) {
  $header_text_columns['menu_order'] = "Order";
  return $header_text_columns;
}
add_action('manage_edit-document_columns', 'add_new_documen_text_column');


function show_document_order_column($name){
  global $post;

  switch ($name) {
    case 'menu_order':
      $order = $post->menu_order;
      echo $order;
      break;
   default:
      break;
   }
}
add_action('manage_document_posts_custom_column','show_document_order_column');

/**
* make column sortable
*/
function order_column_document_register_sortable($columns){
  $columns['menu_order'] = 'menu_order';
  return $columns;
}
add_filter('manage_edit-document_sortable_columns','order_column_document_register_sortable');

/*
 *
 *
 *
 *
*/


function add_new_produits_column($header_text_columns) {
  $header_text_columns['menu_order'] = "Order";
  return $header_text_columns;
}
add_action('manage_edit-produits_columns', 'add_new_produits_column');


function show_produits_order_column($name){
  global $post;

  switch ($name) {
    case 'menu_order':
      $order = $post->menu_order;
      echo $order;
      break;
   default:
      break;
   }
}
add_action('manage_produits_posts_custom_column','show_produits_order_column');

/**
* make column sortable
*/
function order_column_produits_register_sortable($columns){
  $columns['menu_order'] = 'menu_order';
  return $columns;
}
add_filter('manage_edit-produits_sortable_columns','order_column_produits_register_sortable');

/*
 *
 *
 *
 *
*/


// remove the old box
function remove_default_categories_box() {
    remove_meta_box('taxdocumentdiv', 'document', 'side');
}
add_action( 'admin_head', 'remove_default_categories_box' );

// add the new box
function add_custom_categories_box() {
    add_meta_box('customtaxdocumentdiv', 'Catégories des documents', 'custom_post_taxdocument_meta_box', 'document', 'side', 'low', array( 'taxonomy' => 'taxdocument' ));
}
add_action('admin_menu', 'add_custom_categories_box');

/**
 * Display CUSTOM post categories form fields.
 *
 * @since 2.6.0
 *
 * @param object $post
 */
function custom_post_taxdocument_meta_box( $post, $box ) {
    
    $defaults = array('taxonomy' => 'taxdocument');
    
    if ( !isset($box['args']) || !is_array($box['args']) )
        $args = array();
    else
        $args = $box['args'];
    
    extract( wp_parse_args($args, $defaults), EXTR_SKIP );
    
    $tax = get_taxonomy($taxonomy);
	
    ?>
    <div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
        <ul id="<?php echo $taxonomy; ?>-tabs" class="category-tabs">
            <li class="tabs"><a href="#<?php echo $taxonomy; ?>-all" tabindex="3"><?php echo $tax->labels->all_items; ?></a></li>
            <li class="hide-if-no-js"><a href="#<?php echo $taxonomy; ?>-ido" tabindex="3"><?php _e( 'trier par id' ); ?></a></li>
            <li class="hide-if-no-js"><a href="#<?php echo $taxonomy; ?>-pop" tabindex="3"><?php _e( 'Most Used' ); ?></a></li>
            
        </ul>

        <div id="<?php echo $taxonomy; ?>-pop" class="tabs-panel" style="display: none;">
            <ul id="<?php echo $taxonomy; ?>checklist-pop" class="categorychecklist form-no-clear" >
                <?php $popular_ids = wp_popular_terms_checklist($taxonomy,0,-1); ?>
            </ul>
        </div>
        
        <div id="<?php echo $taxonomy; ?>-ido" class="tabs-panel">
            <?php
            $name = ( $taxonomy == 'taxdocument' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
            
            echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
            ?>
            <ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
                <?php 
                /**
                 * This is the one line we had to change in the original function
                 * Notice that "checked_ontop" is now set to FALSE
                 */
                //$list = wp_terms_checklist($post->ID, array( 'taxonomy' => $taxonomy, 'popular_cats' => $popular_ids, 'checked_ontop' => FALSE , 'echo'=> false ) );
                
                $terms = get_the_terms( $post->ID, $taxonomy );
                $checked_terms = array();
                foreach( $terms as $term ){
	                $checked_terms[] = $term->term_id;
                }
                $all_terms = get_terms( array(
                	'taxonomy' => $taxonomy,
                	'hide_empty' => false,
                	'meta_key' => 'id_ordre',
                	'orderby' => 'meta_value_num',
                	'order' => 'ASC'
				) );
				
				foreach( $all_terms as $term ){
					if( !in_array( $term->term_id, $checked_terms ) ):
					?>
						<li id="taxdocument-<?php echo $term->term_id;?>" class="popular-category wpseo-term-unchecked"><label class="selectit"><input value="<?php echo  $term->term_id;?>" type="checkbox" name="tax_input[taxdocument][]" id="in-taxdocument-<?php echo $term->term_id;?>"> <?php echo $term->name;?></label></li>
					<?php
						else:
					?>
						<li id="taxdocument-<?php echo $term->term_id;?>" class="popular-category wpseo-non-primary-term"><label class="selectit"><input value="<?php echo $term->term_id;?>" type="checkbox" name="tax_input[taxdocument][]" id="in-taxdocument-<?php echo $term->term_id;?>" checked="checked"> <?php echo $term->name;?></label></li>					
					<?php
						endif;
				}
                ?>
            </ul>
        </div>
        
        <div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
            <?php
            $name = ( $taxonomy == 'taxdocument' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
            echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
            ?>
            <ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
                <?php 
                /**
                 * This is the one line we had to change in the original function
                 * Notice that "checked_ontop" is now set to FALSE
                 */
                wp_terms_checklist($post->ID, array( 'taxonomy' => $taxonomy, 'popular_cats' => $popular_ids, 'checked_ontop' => FALSE ) ) ?>
            </ul>
        </div>
    
    <?php if ( !current_user_can($tax->cap->assign_terms) ) : ?>
    <p><em><?php _e('You cannot modify this taxonomy.'); ?></em></p>
    <?php endif; ?>
    
    <?php if ( current_user_can($tax->cap->edit_terms) ) : ?>
            <div id="<?php echo $taxonomy; ?>-adder" class="wp-hidden-children">
                <h4>
                    <a id="<?php echo $taxonomy; ?>-add-toggle" href="#<?php echo $taxonomy; ?>-add" class="hide-if-no-js" tabindex="3">
                        <?php
                            /* translators: %s: add new taxonomy label */
                            printf( __( '+ %s' ), $tax->labels->add_new_item );
                        ?>
                    </a>
                </h4>
                <p id="<?php echo $taxonomy; ?>-add" class="category-add wp-hidden-child">
                    <label class="screen-reader-text" for="new<?php echo $taxonomy; ?>"><?php echo $tax->labels->add_new_item; ?></label>
                    <input type="text" name="new<?php echo $taxonomy; ?>" id="new<?php echo $taxonomy; ?>" class="form-required form-input-tip" value="<?php echo esc_attr( $tax->labels->new_item_name ); ?>" tabindex="3" aria-required="true"/>
                    <label class="screen-reader-text" for="new<?php echo $taxonomy; ?>_parent">
                        <?php echo $tax->labels->parent_item_colon; ?>
                    </label>
                    <?php wp_dropdown_categories( array( 'taxonomy' => $taxonomy, 'hide_empty' => 0, 'name' => 'new'.$taxonomy.'_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => '&mdash; ' . $tax->labels->parent_item . ' &mdash;', 'tab_index' => 3 ) ); ?>
                    <input type="button" id="<?php echo $taxonomy; ?>-add-submit" class="add:<?php echo $taxonomy ?>checklist:<?php echo $taxonomy ?>-add button category-add-sumbit" value="<?php echo esc_attr( $tax->labels->add_new_item ); ?>" tabindex="3" />
                    <?php wp_nonce_field( 'add-'.$taxonomy, '_ajax_nonce-add-'.$taxonomy, false ); ?>
                    <span id="<?php echo $taxonomy; ?>-ajax-response"></span>
                </p>
            </div>
        <?php endif; ?>
    </div>
    <?php
}