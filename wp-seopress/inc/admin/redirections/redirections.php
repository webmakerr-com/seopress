<?php
/**
 * Register the Redirections custom post type used by SEOPress PRO.
 *
 * This file ports the minimal registration logic so the CPT exists even when
 * the PRO plugin is not loaded.
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

// Only register the CPT when the PRO plugin is not active.
if ( ! defined( 'SEOPRESS_PRO_VERSION' ) ) {
        add_action( 'init', 'seopress_register_redirections_cpt', 10 );
        add_filter( 'map_meta_cap', 'seopress_redirections_map_meta_cap', 10, 4 );
        add_action( 'init', 'seopress_register_redirections_taxonomy', 10 );
        add_filter( 'enter_title_here', 'seopress_redirections_title_placeholder' );
}

/**
 * Register SEOPress Redirections Custom Post Type.
 *
 * @return void
 */
function seopress_register_redirections_cpt() {
        if ( function_exists( 'seopress_get_toggle_option' ) && '1' !== seopress_get_toggle_option( '404' ) ) {
                return;
        }

        $labels = array(
                'name'                  => _x( 'Redirections', 'Post Type General Name', 'wp-seopress' ),
                'singular_name'         => _x( 'Redirection', 'Post Type Singular Name', 'wp-seopress' ),
                'menu_name'             => __( 'Redirections', 'wp-seopress' ),
                'name_admin_bar'        => __( 'Redirections', 'wp-seopress' ),
                'all_items'             => __( 'All redirections', 'wp-seopress' ),
                'add_new'               => __( 'Add redirection', 'wp-seopress' ),
                'add_new_item'          => __( 'Add New redirection', 'wp-seopress' ),
                'new_item'              => __( 'New redirection', 'wp-seopress' ),
                'edit_item'             => __( 'Edit redirection', 'wp-seopress' ),
                'update_item'           => __( 'Update redirection', 'wp-seopress' ),
                'view_item'             => __( 'View redirection', 'wp-seopress' ),
                'search_items'          => __( 'Search redirection', 'wp-seopress' ),
                'not_found'             => __( 'Not found', 'wp-seopress' ),
                'not_found_in_trash'    => __( 'Not found in Trash', 'wp-seopress' ),
                'items_list'            => __( 'Redirections list', 'wp-seopress' ),
                'items_list_navigation' => __( 'Redirections list navigation', 'wp-seopress' ),
        );

        $args = array(
                'label'               => __( 'Redirections', 'wp-seopress' ),
                'description'         => __( 'Redirections and 404 monitoring', 'wp-seopress' ),
                'labels'              => $labels,
                'supports'            => array( 'title' ),
                'hierarchical'        => false,
                'public'              => false,
                'show_ui'             => true,
                'show_in_menu'        => false,
                'show_in_admin_bar'   => false,
                'show_in_nav_menus'   => false,
                'can_export'          => true,
                'has_archive'         => false,
                'exclude_from_search' => true,
                'publicly_queryable'  => false,
                'capability_type'     => 'redirection',
                'capabilities'        => array(
                        'edit_post'              => 'edit_redirection',
                        'edit_posts'             => 'edit_redirections',
                        'edit_others_posts'      => 'edit_others_redirections',
                        'publish_posts'          => 'publish_redirections',
                        'read_post'              => 'read_redirection',
                        'read_private_posts'     => 'read_private_redirections',
                        'delete_post'            => 'delete_redirection',
                        'delete_others_posts'    => 'delete_others_redirections',
                        'delete_published_posts' => 'delete_published_redirections',
                ),
        );

        register_post_type( 'seopress_404', $args );
}

/**
 * Map SEOPress 404 capabilities.
 *
 * @param array  $caps    Capabilities for meta capability.
 * @param string $cap     Capability.
 * @param int    $user_id User ID.
 * @param array  $args    Arguments.
 *
 * @return array
 */
function seopress_redirections_map_meta_cap( $caps, $cap, $user_id, $args ) {
	if ( 'edit_redirection' === $cap || 'delete_redirection' === $cap || 'read_redirection' === $cap ) {
		$post_id = isset( $args[0] ) ? (int) $args[0] : 0;

		if ( ! $post_id ) {
			return array( 'do_not_allow' );
		}

		$post      = get_post( $post_id );
		$post_type = $post ? get_post_type_object( $post->post_type ) : null;

		if ( ! $post || ! $post_type ) {
			return array( 'do_not_allow' );
		}
		$caps      = array();
	}

        if ( 'edit_redirection' === $cap ) {
                $caps[] = ( $user_id === (int) $post->post_author ) ? $post_type->cap->edit_posts : $post_type->cap->edit_others_posts;
        } elseif ( 'delete_redirection' === $cap ) {
                $caps[] = ( $user_id === (int) $post->post_author ) ? $post_type->cap->delete_published_posts : $post_type->cap->delete_others_posts;
        } elseif ( 'read_redirection' === $cap ) {
                if ( 'private' !== $post->post_status || $user_id === (int) $post->post_author ) {
                        $caps[] = 'read';
                } else {
                        $caps[] = $post_type->cap->read_private_posts;
                }
        }

        return $caps;
}

/**
 * Register categories taxonomy for redirections.
 *
 * @return void
 */
function seopress_register_redirections_taxonomy() {
        if ( function_exists( 'seopress_get_toggle_option' ) && '1' !== seopress_get_toggle_option( '404' ) ) {
                return;
        }

        $labels = array(
                'name'          => _x( 'Categories', 'Taxonomy General Name', 'wp-seopress' ),
                'singular_name' => _x( 'Category', 'Taxonomy Singular Name', 'wp-seopress' ),
                'menu_name'     => __( 'Categories', 'wp-seopress' ),
                'all_items'     => __( 'All Categories', 'wp-seopress' ),
                'new_item_name' => __( 'New Category Name', 'wp-seopress' ),
                'add_new_item'  => __( 'Add New Category', 'wp-seopress' ),
                'edit_item'     => __( 'Edit Category', 'wp-seopress' ),
                'update_item'   => __( 'Update Category', 'wp-seopress' ),
                'search_items'  => __( 'Search Category', 'wp-seopress' ),
        );

        $args = array(
                'labels'            => $labels,
                'hierarchical'      => true,
                'public'            => false,
                'show_ui'           => true,
                'show_admin_column' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud'     => false,
        );

        register_taxonomy( 'seopress_404_cat', array( 'seopress_404' ), $args );
}

/**
 * Set the title placeholder for the redirections CPT.
 *
 * @param string $title Default title placeholder.
 *
 * @return string
 */
function seopress_redirections_title_placeholder( $title ) {
        $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
        if ( $screen && 'seopress_404' === $screen->post_type ) {
                $title = __( 'Enter the request URI (without the domain)', 'wp-seopress' );
        }

        return $title;
}
