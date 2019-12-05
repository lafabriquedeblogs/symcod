<?php
/**
 * Plugin Name:     Symcod Documentation
 * Plugin URI:      https://lafabriquedeblogs.com
 * Description:     Gestion de la Documentation des Produits de symcod
 * Author:          La Fabrique de blogs pour Cible
 * Author URI:      YOUR SITE HERE
 * Text Domain:     symcod
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         produits
 */

// Your code starts here.
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'symcod_produits' ) ) {

class symcod_produits {
	/**
	 * Plugin version, match with plugin header
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * Use the function not the variable
	 * @var string
	 */
	public $plugin_url;

	/**
	 * Use the function not the variable
	 * @var string
	 */
	public $plugin_path;

	/**
	 * Do we update the rewrite rules for a custom post type?
	 * @var boolean
	 */
	public $flush_rules = FALSE;
	/**
	 * PLUGIN STARTUP
	 */
	
	public function __construct(){
		// do something when we activate/deactivate the plugin
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		$installed = get_option( __CLASS__ . '_Version', FALSE );

		if( ! $installed or version_compare($installed, $this->version, '!=') ){
			add_action( 'init', array($this, 'activate'), 9 );
			update_option( __CLASS__ . '_Version', $this->version );
		}

		$this->hooks();
	}
	/**
	 * Register the plugin's hooks
	 */
	public function hooks(){
		add_action( 'init', array($this, 'init'), 0 );
		add_action( 'init', array($this,'symcod_documention_rewrite_rules') );
		add_action( 'wp_enqueue_scripts', array($this,'symcod_documentation_scripts') );
		add_action('admin_enqueue_scripts', array($this,'admin_enqueue') );
		add_filter( 'the_content',  array($this,'produits_content_filter') );
		add_filter( 'query_vars', array($this,'myplugin_register_query_vars') );
		
	}
	
	public function symcod_documentation_scripts(){
		wp_enqueue_style( 'symcod-fancy-css', $this->jsURL( 'fancy-box/jquery.fancybox.min.css' ) );
		wp_enqueue_style( 'symcod-documentation-css', $this->cssURL( 'documentation.css' ) );
		wp_enqueue_script( 'symcod-script-fancy', $this->jsURL('fancy-box/jquery.fancybox.min.js'), array('jquery'), null, true );		
		wp_enqueue_script( 'symcod-documentation-script', $this->jsURL('app-documentation.js'), array('jquery'), null, true );
		wp_localize_script( 'symcod-documentation-script', 'Documentation', array(
			'ajaxurl'          => admin_url( 'admin-ajax.php' ),
			'docuNonce' => wp_create_nonce( 'documentation-script-nonce' ),
		));	
	}
	public function admin_enqueue($hook) {
	    // Only add to the edit.php admin page.
	    // See WP docs.

	    if ('post.php' !== $hook) {
	        return;
	    }
	    wp_enqueue_script('my_custom_script', $this->jsURL('app-documentation-admin.js'), array('jquery'), null, true );
	    wp_localize_script( 'my_custom_script', 'Documentation', array(
			'ajaxurl'          => admin_url( 'admin-ajax.php' ),
			'docuNonce' => wp_create_nonce( 'documentation-script-nonce' ),
		));
	}	
	
	public function symcod_documention_rewrite_rules(){
		/* http://symcod.com/?page=***&post_type=produits&version=***&categorie=*** */
		/* http://symcod.test/produit/monicom/version/v1/doc/generale/ */
		
		add_rewrite_tag( '%version%', '([^&]+)' );
		add_rewrite_tag( '%doc%', '([^&]+)' );

		add_rewrite_rule(
			'produit/([^/]+)/version/([^/]+)/doc/([^/]+)/?',
			'index.php?produits=$matches[1]&version=$matches[2]&doc=$matches[3]',
			'top'
		);	
		
		add_rewrite_rule(
			'produit/([^/]+)/version/([^/]+)/?',
			'index.php?produits=$matches[1]&version=$matches[2]',
			'top'
		);
	
		add_rewrite_endpoint('version', EP_PAGES);
		add_rewrite_endpoint('doc', EP_PAGES);
	}
	
	public function myplugin_register_query_vars( $vars ) {
		$vars[] = 'version';
		$vars[] = 'doc';
		return $vars;
	}
	
	/**
	 * Runs on WordPress init hook
	 */
	public function init(){
		$this->post_types();
		$this->flush();
	}
	
	public function post_types(){
		$labels = array(
			'name'                  => _x( 'Produits', 'Post Type General Name', 'symcod' ),
			'singular_name'         => _x( 'Produit', 'Post Type Singular Name', 'symcod' ),
			'menu_name'             => __( 'Produits', 'symcod' ),
			'name_admin_bar'        => __( 'Produit', 'symcod' ),
			'archives'              => __( 'Item Archives', 'symcod' ),
			'attributes'            => __( 'Item Attributes', 'symcod' ),
			'parent_item_colon'     => __( 'Parent Item:', 'symcod' ),
			'all_items'             => __( 'Produits', 'symcod' ),
			'add_new_item'          => __( 'Add New Item', 'symcod' ),
			'add_new'               => __( 'Ajouter', 'symcod' ),
			'new_item'              => __( 'New Item', 'symcod' ),
			'edit_item'             => __( 'Edit Item', 'symcod' ),
			'update_item'           => __( 'Update Item', 'symcod' ),
			'view_item'             => __( 'View Item', 'symcod' ),
			'view_items'            => __( 'View Items', 'symcod' ),
			'search_items'          => __( 'Search Item', 'symcod' ),
			'not_found'             => __( 'Not found', 'symcod' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'symcod' ),
			'featured_image'        => __( 'Featured Image', 'symcod' ),
			'set_featured_image'    => __( 'Set featured image', 'symcod' ),
			'remove_featured_image' => __( 'Remove featured image', 'symcod' ),
			'use_featured_image'    => __( 'Use as featured image', 'symcod' ),
			'insert_into_item'      => __( 'Insert into item', 'symcod' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'symcod' ),
			'items_list'            => __( 'Items list', 'symcod' ),
			'items_list_navigation' => __( 'Items list navigation', 'symcod' ),
			'filter_items_list'     => __( 'Filter items list', 'symcod' ),
		);
		$rewrite = array(
			'slug'                  => 'produit',
			'with_front'            => true,
			'pages'                 => true,
			'feeds'                 => true,
		);
		$args = array(
			'label'                 => __( 'Produit', 'symcod' ),
			'description'           => __( 'Produits', 'symcod' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'custom-fields','excerpt','page-attributes' ),
			'taxonomies'            => array( 'product_cat_symcod' ),
			'hierarchical'          => true,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'rewrite'               => $rewrite,
			'capability_type'       => 'page',
			'show_in_rest'          => true,
		);
		register_post_type( 'produits', $args );	


		$labels_document = array(
			'name'                  => _x( 'Documents', 'Post Type General Name', 'symcod' ),
			'singular_name'         => _x( 'Document', 'Post Type Singular Name', 'symcod' ),
			'menu_name'             => __( 'Documents', 'symcod' ),
			'name_admin_bar'        => __( 'Document', 'symcod' ),
			'archives'              => __( 'Item Archives', 'symcod' ),
			'attributes'            => __( 'Item Attributes', 'symcod' ),
			'parent_item_colon'     => __( 'Parent Item:', 'symcod' ),
			'all_items'             => __( 'Documents', 'symcod' ),
			'add_new_item'          => __( 'Add New Item', 'symcod' ),
			'add_new'               => __( 'Ajouter', 'symcod' ),
			'new_item'              => __( 'New Item', 'symcod' ),
			'edit_item'             => __( 'Edit Item', 'symcod' ),
			'update_item'           => __( 'Update Item', 'symcod' ),
			'view_item'             => __( 'View Item', 'symcod' ),
			'view_items'            => __( 'View Items', 'symcod' ),
			'search_items'          => __( 'Search Item', 'symcod' ),
			'not_found'             => __( 'Not found', 'symcod' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'symcod' ),
			'featured_image'        => __( 'Featured Image', 'symcod' ),
			'set_featured_image'    => __( 'Set featured image', 'symcod' ),
			'remove_featured_image' => __( 'Remove featured image', 'symcod' ),
			'use_featured_image'    => __( 'Use as featured image', 'symcod' ),
			'insert_into_item'      => __( 'Insert into item', 'symcod' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'symcod' ),
			'items_list'            => __( 'Items list', 'symcod' ),
			'items_list_navigation' => __( 'Items list navigation', 'symcod' ),
			'filter_items_list'     => __( 'Filter items list', 'symcod' ),
		);
		$rewrite_document = array(
			'slug'                  => 'document',
			'with_front'            => true,
			'pages'                 => true,
			'feeds'                 => true,
		);
		$args_document = array(
			'label'                 => __( 'Document', 'symcod' ),
			'description'           => __( 'Documents', 'symcod' ),
			'labels'                => $labels_document,
			'supports'              => array( 'title','page-attributes' ),
			'taxonomies'            => array(),
			'hierarchical'          => true,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'rewrite'               => $rewrite_document,
			'capability_type'       => 'page',
			'show_in_rest'          => true,
		);
		register_post_type( 'document', $args_document );

		unset($args);
		unset($labels);
		unset($rewrite);
		
		$labels = array(
			'name'                       => _x( 'Catégories de document', 'Taxonomy General Name', 'symcod' ),
			'singular_name'              => _x( 'Catégorie de document', 'Taxonomy Singular Name', 'symcod' ),
			'menu_name'                  => __( 'Catégories de document', 'symcod' ),
			'all_items'                  => __( 'All Items', 'symcod' ),
			'parent_item'                => __( 'Parent Item', 'symcod' ),
			'parent_item_colon'          => __( 'Parent Item:', 'symcod' ),
			'new_item_name'              => __( 'New Item Name', 'symcod' ),
			'add_new_item'               => __( 'Add New Item', 'symcod' ),
			'edit_item'                  => __( 'Edit Item', 'symcod' ),
			'update_item'                => __( 'Update Item', 'symcod' ),
			'view_item'                  => __( 'View Item', 'symcod' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'symcod' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'symcod' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'symcod' ),
			'popular_items'              => __( 'Popular Items', 'symcod' ),
			'search_items'               => __( 'Search Items', 'symcod' ),
			'not_found'                  => __( 'Not Found', 'symcod' ),
			'no_terms'                   => __( 'No items', 'symcod' ),
			'items_list'                 => __( 'Items list', 'symcod' ),
			'items_list_navigation'      => __( 'Items list navigation', 'symcod' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
			'show_in_rest'               => true,
			'meta_box_cb'				 => 'meta_box_taxdocument',
			'show_in_quick_edit'		 => true,
		);
		register_taxonomy( 'taxdocument', array( 'document' ), $args );
		
	}
	
	public function produits_content_filter($content){
		if ($GLOBALS['post']->post_type === 'produits') {
		    //$content .= 'yeah!';
		}
		// otherwise returns the database content
		return $content;		
	}
	
	/**
	 * Refresh rewrite rules
	 */
	public function flush(){
		if( $this->flush_rules )
			flush_rewrite_rules();
	}

	public function activate(){
		$this->flush_rules = TRUE; // we will need to refresh the rewrite rules for the custom post types
	}

	public function deactivate(){
		$this->flush_rules = TRUE; // refresh the rewrite rules for the custom post types which are no longuer loaded
	}

	public function imgURL( $file ){
		return $this->plugin_url() . "/assets/images/{$file}";
	}

	public function jsURL( $file ){
		return $this->plugin_url() . "/assets/js/{$file}";
	}
	public function jsPATH( $file ){
		return $this->plugin_path() . "/assets/js/{$file}";
	}

	public function cssURL( $file ){
		return $this->plugin_url() . "/assets/css/{$file}";
	}
	public function cssPATH( $file ){
		return $this->plugin_path() . "/assets/css/{$file}";
	}

	/**
	 * Get the plugin url.
	 *
	 * @access public
	 * @return string
	 */
	public function plugin_url() {
		if ( $this->plugin_url ) return $this->plugin_url;
		return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @access public
	 * @return string
	 */
	public function plugin_path() {
		if ( $this->plugin_path ) return $this->plugin_path;
		return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

}
// Init Class and register in global scope
$GLOBALS['symcod_produits'] = new symcod_produits();

} // class_exists check


add_filter('single_template', 'produits_custom_template');

function produits_custom_template( $template ) {

    global $post;

    if ( 'produits' === $post->post_type && locate_template( array( 'single-produits.php' ) ) !== $template ) {
        /*
         * This is a 'movie' post
         * AND a 'single movie template' is not found on
         * theme or child theme directories, so load it
         * from our plugin directory.
         */
        return plugin_dir_path( __FILE__ ) . 'single-produits.php';
    }

    return $template;

}


add_filter( 'acf/fields/svg_icon/file_path/name=type_de_document', 'file_type_acf_svg_icon_file_path');

function file_type_acf_svg_icon_file_path( $file_path ) {
    
    //return get_theme_file_path( __FILE__ ).'assets/images/file-types-sprite.svg';
    return get_theme_file_path( 'assets/img/icons/file-types-sprite.svg' );
}
	
add_filter( 'gform_field_content_4_24', 'my_custom_function', 10, 5 );

function my_custom_function( $content, $field, $value, $lead_id, $form_id ){
	
	$content = '<label class="gfield_label" for="input_4_24">'.__('Produits','symcod').'<span class="gfield_required">*</span></label>
	<div class="ginput_container ginput_container_select">
		<select name="input_24" id="input_4_24" class="medium gfield_select" aria-required="true" aria-invalid="false">';
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


function unique_multidim_array($array, $key) {
    
    $temp_array = array();
    $i = 0;
    $key_array = array();
   
    foreach($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}


include( plugin_dir_path( __FILE__ ) . '/symcod-templates.php');
include( plugin_dir_path( __FILE__ ) . '/includes/documentation-functions.php');
include( plugin_dir_path( __FILE__ ) . '/includes/gravityform-select.php');
include( plugin_dir_path( __FILE__ ) . '/includes/sort_documents_produits.php');
include( plugin_dir_path( __FILE__ ) . '/includes/ajax_functions.php');
