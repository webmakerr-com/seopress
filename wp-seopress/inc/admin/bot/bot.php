<?php
/**
 * Register the Broken Links Bot custom post type used by SEOPress PRO.
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! defined( 'SEOPRESS_PRO_VERSION' ) ) {
        add_action( 'init', 'seopress_register_bot_cpt', 10 );
        add_filter( 'enter_title_here', 'seopress_bot_title_placeholder' );
}

/**
 * Register SEOPress BOT Custom Post Type.
 *
 * @return void
 */
function seopress_register_bot_cpt() {
        if ( function_exists( 'seopress_get_toggle_option' ) && '1' !== seopress_get_toggle_option( 'bot' ) ) {
                return;
        }

        $labels = array(
                'name'                  => _x( 'Broken links', 'Post Type General Name', 'wp-seopress' ),
                'singular_name'         => _x( 'Broken link', 'Post Type Singular Name', 'wp-seopress' ),
                'menu_name'             => __( 'Broken links', 'wp-seopress' ),
                'name_admin_bar'        => __( 'Broken links', 'wp-seopress' ),
                'all_items'             => __( 'All broken links', 'wp-seopress' ),
                'add_new'               => __( 'Add link', 'wp-seopress' ),
                'add_new_item'          => __( 'Add New link', 'wp-seopress' ),
                'new_item'              => __( 'New link', 'wp-seopress' ),
                'edit_item'             => __( 'Edit link', 'wp-seopress' ),
                'update_item'           => __( 'Update link', 'wp-seopress' ),
                'view_item'             => __( 'View link', 'wp-seopress' ),
                'search_items'          => __( 'Search link', 'wp-seopress' ),
                'not_found'             => __( 'Not found', 'wp-seopress' ),
                'not_found_in_trash'    => __( 'Not found in Trash', 'wp-seopress' ),
        );

        $args = array(
                'label'               => __( 'Broken links', 'wp-seopress' ),
                'description'         => __( 'List of broken links', 'wp-seopress' ),
                'labels'              => $labels,
                'supports'            => array( 'title', 'editor', 'custom-fields' ),
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
                'capability_type'     => 'post',
                'capabilities'        => array(
                        'create_posts' => 'do_not_allow',
                ),
                'map_meta_cap'        => true,
        );

        register_post_type( 'seopress_bot', $args );
}

/**
 * Title placeholder for bot CPT.
 *
 * @param string $title Default placeholder.
 *
 * @return string
 */
function seopress_bot_title_placeholder( $title ) {
        $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
        if ( $screen && 'seopress_bot' === $screen->post_type ) {
                $title = __( 'Enter the broken link URL', 'wp-seopress' );
        }

        return $title;
}
