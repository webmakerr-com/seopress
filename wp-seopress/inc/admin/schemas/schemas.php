<?php
/**
 * Register the Schemas custom post type used by SEOPress PRO.
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! defined( 'SEOPRESS_PRO_VERSION' ) ) {
        add_action( 'admin_init', 'seopress_register_schemas_cpt', 10 );
        add_filter( 'map_meta_cap', 'seopress_schemas_map_meta_cap', 10, 4 );
        add_filter( 'enter_title_here', 'seopress_schemas_title_placeholder' );
}

/**
 * Register SEOPress Schemas Custom Post Type.
 *
 * @return void
 */
function seopress_register_schemas_cpt() {
        if ( function_exists( 'seopress_get_toggle_option' ) && '1' !== seopress_get_toggle_option( 'rich-snippets' ) ) {
                return;
        }

        $labels = array(
                'name'                  => _x( 'Schemas', 'Post Type General Name', 'wp-seopress' ),
                'singular_name'         => _x( 'Schema', 'Post Type Singular Name', 'wp-seopress' ),
                'menu_name'             => __( 'Schemas', 'wp-seopress' ),
                'name_admin_bar'        => __( 'Schemas', 'wp-seopress' ),
                'all_items'             => __( 'All schemas', 'wp-seopress' ),
                'add_new'               => __( 'Add schema', 'wp-seopress' ),
                'add_new_item'          => __( 'Add New schema', 'wp-seopress' ),
                'new_item'              => __( 'New schema', 'wp-seopress' ),
                'edit_item'             => __( 'Edit schema', 'wp-seopress' ),
                'update_item'           => __( 'Update schema', 'wp-seopress' ),
                'view_item'             => __( 'View schema', 'wp-seopress' ),
                'search_items'          => __( 'Search schema', 'wp-seopress' ),
                'not_found'             => __( 'Not found', 'wp-seopress' ),
                'not_found_in_trash'    => __( 'Not found in Trash', 'wp-seopress' ),
                'items_list'            => __( 'Schemas list', 'wp-seopress' ),
                'items_list_navigation' => __( 'Schemas list navigation', 'wp-seopress' ),
        );

        $args = array(
                'label'               => __( 'Schemas', 'wp-seopress' ),
                'description'         => __( 'List of schemas', 'wp-seopress' ),
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
                'capability_type'     => 'schema',
                'capabilities'        => array(
                        'edit_post'              => 'edit_schema',
                        'edit_posts'             => 'edit_schemas',
                        'edit_others_posts'      => 'edit_others_schemas',
                        'publish_posts'          => 'publish_schemas',
                        'read_post'              => 'read_schema',
                        'read_private_posts'     => 'read_private_schemas',
                        'delete_post'            => 'delete_schema',
                        'delete_others_posts'    => 'delete_others_schemas',
                        'delete_published_posts' => 'delete_published_schemas',
                ),
        );

        register_post_type( 'seopress_schemas', $args );
}

/**
 * Map SEOPress schema capabilities.
 *
 * @param array  $caps    Capabilities.
 * @param string $cap     Capability.
 * @param int    $user_id User ID.
 * @param array  $args    Arguments.
 *
 * @return array
 */
function seopress_schemas_map_meta_cap( $caps, $cap, $user_id, $args ) {
        if ( 'edit_schema' === $cap || 'delete_schema' === $cap || 'read_schema' === $cap ) {
                $post      = get_post( $args[0] );
                $post_type = get_post_type_object( $post->post_type );
                $caps      = array();
        }

        if ( 'edit_schema' === $cap ) {
                $caps[] = ( $user_id === (int) $post->post_author ) ? $post_type->cap->edit_posts : $post_type->cap->edit_others_posts;
        } elseif ( 'delete_schema' === $cap ) {
                $caps[] = ( $user_id === (int) $post->post_author ) ? $post_type->cap->delete_published_posts : $post_type->cap->delete_others_posts;
        } elseif ( 'read_schema' === $cap ) {
                if ( 'private' !== $post->post_status || $user_id === (int) $post->post_author ) {
                        $caps[] = 'read';
                } else {
                        $caps[] = $post_type->cap->read_private_posts;
                }
        }

        return $caps;
}

/**
 * Title placeholder for schemas CPT.
 *
 * @param string $title Default placeholder.
 *
 * @return string
 */
function seopress_schemas_title_placeholder( $title ) {
        $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
        if ( $screen && 'seopress_schemas' === $screen->post_type ) {
                $title = __( 'Enter the name of your schema', 'wp-seopress' );
        }

        return $title;
}
