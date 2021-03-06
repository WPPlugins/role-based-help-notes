<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * RBHN_TAX class.
 */
class RBHN_TAX {
    

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */	 
	function __construct( $args = array( ) ) {

		// Parse default and new args.
		$defaults = array(
							'post_type' => 'h_administrator', 
							'taxonomy' => 'topics', 
							'page_title' => 'New Topics', 
							'menu_title' => 'Topics',
							//'hierarchical' => true,
                                                        'manage_capability' => 'manage_categories',
							'labels' => array( 
                                                                            'name' => _x( 'Topics', 'taxonomy plural name', 'role-based-help-notes' ),
                                                                            'singular_name' => _x( 'Topic', 'taxonomy singular name', 'role-based-help-notes' ),
                                                                            'search_items' =>  __( 'Search Topics', 'role-based-help-notes' ),
                                                                            'all_items' => __( 'All Topics', 'role-based-help-notes' ),
                                                                            'parent_item' => __( 'Parent Topic', 'role-based-help-notes' ),
                                                                            'parent_item_colon' => __( 'Parent Topic:', 'role-based-help-notes' ),
                                                                            'edit_item' => __( 'Edit Topic', 'role-based-help-notes' ),
                                                                            'update_item' => __( 'Update Topic', 'role-based-help-notes' ),
                                                                            'add_new_item' => __( 'Add New Topic', 'role-based-help-notes' ),
                                                                            'new_item_name' => __( 'New Topic Name', 'role-based-help-notes' ),
                                                                            'menu_name' => _x( 'Topics', 'taxonomy menu name', 'role-based-help-notes' ),
                                                                        ) 
							);
							
		$this->args     = wp_parse_args( $args, $defaults );
                
		//Add custom taxonomy to Role Based Help Notes
		//hook into the init action and call create_book_taxonomies when it fires
		add_action( 'init', array( $this, 'create_hierarchical_taxonomy' ) );
		
		add_action( 'restrict_manage_posts', array( $this, 'restrict_posttype_by_taxonomy' ) );

		add_filter( 'parse_query', array( $this, 'convert_id_to_term_in_query' ) );
	
	}

	public function create_hierarchical_taxonomy( ) {

		// register the taxonomy
		register_taxonomy(	$this->args['taxonomy'], 
							array( $this->args['post_type'] ), 
							apply_filters( 'rbhn_taxonomy_args', array(
                                                                                                    'hierarchical' => true,
                                                                                                    'labels' => $this->args['labels'],
                                                                                                    'show_ui' => true,
                                                                                                    'show_in_nav_menus' => false,
                                                                                                    'show_admin_column' => true,
                                                                                                    'query_var' => true,
                                                                                                    'rewrite' => array( 'slug' => $this->args['taxonomy'] ),
                                                                                                    'capabilities' => array( 'manage_terms' => $this->args['manage_capability'],
                                                                                                                             'edit_terms' => $this->args['manage_capability'],
                                                                                                                             'delete_terms' => $this->args['manage_capability'],
                                                                                                                             'assign_terms' => 'edit_' . $this->args['post_type'] . 's',
                                                                                                                            ),
                                                                                                    )
										)
						);
	}
	
	public function restrict_posttype_by_taxonomy( ) {

			global $typenow;		
			if ( $typenow == $this->args['post_type'] ) {
				$selected = isset( $_GET[$this->args['taxonomy']] ) ? $_GET[$this->args['taxonomy']] : '';
				$info_taxonomy = get_taxonomy( $this->args['taxonomy'] );
				if ( $info_taxonomy ) { 
                                    wp_dropdown_categories( array(
                                                                'show_option_all' => __( "Show All {$info_taxonomy->label}" ),
                                                                'taxonomy' => $this->args['taxonomy'],
                                                                'name' => $this->args['taxonomy'],
                                                                'orderby' => 'name',
                                                                'selected' => $selected,
                                                                'show_count' => true,
                                                                'hide_empty' => true,
                                                                )
                                                            );
                                }
			}
		}


	public function convert_id_to_term_in_query( $query ) {
	
		global $pagenow;	
		$q_vars = &$query->query_vars;
	
		if ( $pagenow == 'edit.php' && isset( $q_vars['post_type'] ) && $q_vars['post_type'] == $this->args['post_type'] && isset( $q_vars[$this->args['taxonomy']] ) && is_numeric( $q_vars[$this->args['taxonomy']] ) && $q_vars[$this->args['taxonomy']] != 0 ) {	
			$term = get_term_by( 'id', $q_vars[$this->args['taxonomy']], $this->args['taxonomy'] );
			$q_vars[$this->args['taxonomy']] = $term->slug;
		}
	}
}

//Add the new Taxonomies for the Help Notes.


// Configure the Taxonomy
/*
$args = array(
                        'post_type' => 'h_administrator', 
                        'taxonomy' => 'h_tax_topics', 
                        'page_title' => 'New Topics', 
                        'menu_title' => 'Topics',
                        'labels' => array( 
                                                                'name' => _x( 'Topics', 'taxonomy plural name', 'role-based-help-notes' ),
                                                                'singular_name' => _x( 'Topic', 'taxonomy singular name', 'role-based-help-notes' ),
                                                                'search_items' =>  __( 'Search Topics', 'role-based-help-notes' ),
                                                                'all_items' => __( 'All Topics', 'role-based-help-notes' ),
                                                                'parent_item' => __( 'Parent Topic', 'role-based-help-notes' ),
                                                                'parent_item_colon' => __( 'Parent Topic:', 'role-based-help-notes' ),
                                                                'edit_item' => __( 'Edit Topic', 'role-based-help-notes' ),
                                                                'update_item' => __( 'Update Topic', 'role-based-help-notes' ),
                                                                'add_new_item' => __( 'Add New Topic', 'role-based-help-notes' ),
                                                                'new_item_name' => __( 'New Topic Name', 'role-based-help-notes' ),
                                                                'menu_name' => _x( 'Topics', 'taxonomy menu name', 'role-based-help-notes' ),
                                                        ) 
                        );
*/
$post_types_array       = array_filter( ( array ) get_option( 'rbhn_post_types' ) );
$role_based_help_notes  = RBHN_Role_Based_Help_Notes::get_instance( );

//  loop through the site roles and create a topics taxonomy for each
global $wp_roles;

// Load roles if not set
if ( ! isset( $wp_roles ) ) {
        $wp_roles = new WP_Roles( );
}

$roles = $wp_roles->get_names( );
unset( $wp_roles );

if ( ! empty( $post_types_array ) ) {

        foreach( $post_types_array as $array ) {	
                foreach( $array as $active_role=>$active_posttype ) {
                        if ( array_key_exists ( $active_role, $roles ) ) {
                                if ( $role_based_help_notes->help_notes_current_user_has_role( $active_role ) ) {

                                        $tax_args = array(								
                                                                        'post_type' => $active_posttype, 
                                                                        'taxonomy' => $active_posttype . 'topics',
                                                                        'manage_capability' => 'manage_categories_' . $active_posttype,
                                                                        'page_title' => _x( 'New Topics', 'Title of the New Taxonomy Page', 'role-based-help-notes' ),
                                                                        //'menu_title' => sprintf( __( '%1$s Topics', 'role-based-help-notes' ), $role_name ),
                                                                        'labels' => array( 
                                                                                                                //'name' => _x( 'Topics', 'taxonomy plural name for title', 'role-based-help-notes' ),
                                                                                                                'name' => sprintf( __( 'Topics for the %1$s role', 'role-based-help-notes' ), $roles[$active_role] ),
                                                                                                                'singular_name' => _x( 'Topic', 'taxonomy singular name', 'role-based-help-notes' ),
                                                                                                                'search_items' =>  __( 'Search Topics', 'role-based-help-notes' ),
                                                                                                                'all_items' => __( 'All Topics', 'role-based-help-notes' ),
                                                                                                                'parent_item' => __( 'Parent Topic', 'role-based-help-notes' ),
                                                                                                                'parent_item_colon' => __( 'Parent Topic:', 'role-based-help-notes' ),
                                                                                                                'edit_item' => __( 'Edit Topic', 'role-based-help-notes' ),
                                                                                                                'update_item' => __( 'Update Topic', 'role-based-help-notes' ),
                                                                                                                'add_new_item' => __( 'Add New Topic', 'role-based-help-notes' ),
                                                                                                                'new_item_name' => __( 'New Topic Name', 'role-based-help-notes' ),
                                                                                                                'menu_name' => sprintf( __( '%1$s Topics', 'role-based-help-notes' ), $roles[$active_role] ),
                                                                                                        ) 
                                                                        );

                                        new RBHN_TAX( $tax_args );


                                }
                        } 
                }
        }
}