<?php
/**
 * Minimal cron handlers migrated from SEOPress PRO.
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! defined( 'SEOPRESS_PRO_VERSION' ) ) {
        add_action( 'plugins_loaded', 'seopress_schedule_migrated_events' );
        add_action( 'seopress_page_speed_insights_cron', 'seopress_page_speed_insights_cron_handler' );
        add_action( 'seopress_404_cron_cleaning', 'seopress_404_cron_cleaning_action' );
}

/**
 * Schedule daily events for migrated features.
 *
 * @return void
 */
function seopress_schedule_migrated_events() {
        if ( function_exists( 'seopress_get_toggle_option' ) ) {
                if ( '1' === seopress_get_toggle_option( 'advanced' ) && ! wp_next_scheduled( 'seopress_page_speed_insights_cron' ) ) {
                        wp_schedule_event( time(), 'daily', 'seopress_page_speed_insights_cron' );
                }

                if ( '1' === seopress_get_toggle_option( '404' ) && ! wp_next_scheduled( 'seopress_404_cron_cleaning' ) ) {
                        wp_schedule_event( time(), 'daily', 'seopress_404_cron_cleaning' );
                }
        }
}

/**
 * Fetch Page Speed data and store it in transients used by the REST endpoint.
 *
 * @return void
 */
function seopress_page_speed_insights_cron_handler() {
        $options   = get_option( 'seopress_pro_option_name', array() );
        $api_key   = ! empty( $options['seopress_ps_api_key'] ) ? sanitize_text_field( $options['seopress_ps_api_key'] ) : 'AIzaSyBqvSx2QrqbEqZovzKX8znGpTosw7KClHQ';
        $site_url  = ! empty( $options['seopress_ps_url'] ) ? esc_url_raw( $options['seopress_ps_url'] ) : get_home_url();
        $language  = function_exists( 'seopress_normalized_locale' ) ? seopress_normalized_locale( get_locale() ) : get_locale();
        $args      = array(
                'timeout' => 120,
        );

        $mobile_request  = sprintf( 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=%s&key=%s&screenshot=true&strategy=mobile&category=performance&category=seo&category=best-practices&locale=%s', rawurlencode( $site_url ), rawurlencode( $api_key ), rawurlencode( $language ) );
        $desktop_request = sprintf( 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=%s&key=%s&screenshot=true&strategy=desktop&category=performance&locale=%s', rawurlencode( $site_url ), rawurlencode( $api_key ), rawurlencode( $language ) );

        $mobile_response = wp_remote_retrieve_body( wp_remote_get( $mobile_request, $args ) );
        $desktop_data    = wp_remote_retrieve_body( wp_remote_get( $desktop_request, $args ) );

        if ( ! empty( $mobile_response ) ) {
                set_transient( 'seopress_results_page_speed', $mobile_response, DAY_IN_SECONDS );
        }

        if ( ! empty( $desktop_data ) ) {
                set_transient( 'seopress_results_page_speed_desktop', $desktop_data, DAY_IN_SECONDS );
        }
}

/**
 * Clean redirection logs older than one month that do not store a redirect type.
 *
 * @return void
 */
function seopress_404_cron_cleaning_action() {
        if ( function_exists( 'seopress_get_toggle_option' ) && '1' !== seopress_get_toggle_option( '404' ) ) {
                return;
        }

        $args = array(
                'date_query'     => array(
                        array(
                                'column' => 'post_date_gmt',
                                'before' => '1 month ago',
                        ),
                ),
                'posts_per_page' => -1,
                'post_type'      => 'seopress_404',
                'meta_key'       => '_seopress_redirections_type',
                'meta_compare'   => 'NOT EXISTS',
                'fields'         => 'ids',
        );

        $old_entries = new WP_Query( $args );
        if ( $old_entries->have_posts() ) {
                foreach ( $old_entries->posts as $post_id ) {
                        wp_delete_post( $post_id, true );
                }
        }
}
