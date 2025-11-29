<?php // phpcs:ignore

namespace SEOPress\Actions\Api;

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

class Redirections implements ExecuteHooks {
        private $current_user;

        public function hooks() {
                if ( ! $this->shouldRegister() ) {
                        return;
                }

                $this->current_user = wp_get_current_user()->ID;
                add_action( 'rest_api_init', array( $this, 'register' ) );
        }

        public function register() {
                register_rest_route(
                        'seopress/v1',
                        '/redirections',
                        array(
                                'methods'             => 'GET',
                                'callback'            => array( $this, 'processGetAll' ),
                                'args'                => array(
                                        'id'      => array(
                                                'validate_callback' => function ( $param ) {
                                                        return is_numeric( $param );
                                                },
                                        ),
                                        'enabled' => array(
                                                'validate_callback' => function ( $param ) {
                                                        return in_array( $param, array( 'yes', 'no' ), true );
                                                },
                                        ),
                                        'type'    => array(
                                                'validate_callback' => function ( $param ) {
                                                        $types = array( '301', '302', '307', '404', '410', '451' );

                                                        if ( is_array( $param ) ) {
                                                                return array_intersect( $param, $types ) === $param;
                                                        }

                                                        return in_array( $param, $types, true );
                                                },
                                        ),
                                ),
                                'permission_callback' => function () {
                                        $current_user = $this->current_user ? $this->current_user : wp_get_current_user()->ID;

                                        return user_can( $current_user, 'read_redirection' );
                                },
                        )
                );
        }

        public function processGetAll( \WP_REST_Request $request ) {
                $id      = $request->get_param( 'id' );
                $enabled = $request->get_param( 'enabled' );
                $type    = $request->get_param( 'type' );

                $args = array(
                        'post_type'      => 'seopress_404',
                        'posts_per_page' => '-1',
                );

                if ( ! empty( $id ) ) {
                        $args['p'] = $id;
                }

                if ( ! empty( $enabled ) ) {
                        if ( 'yes' === $enabled ) {
                                $args['meta_query'] = array(
                                        array(
                                                'key'     => '_seopress_redirections_enabled',
                                                'value'   => 'yes',
                                                'compare' => '=',
                                        ),
                                );
                        } elseif ( 'no' === $enabled ) {
                                $args['meta_query'] = array(
                                        'relation' => 'OR',
                                        array(
                                                'key'     => '_seopress_redirections_enabled',
                                                'value'   => 'no',
                                                'compare' => '=',
                                        ),
                                        array(
                                                'key'     => '_seopress_redirections_enabled',
                                                'compare' => 'NOT EXISTS',
                                        ),
                                );
                        }
                }

                if ( ! empty( $type ) ) {
                        if ( is_array( $type ) ) {
                                $args['meta_query'][] = array(
                                        'key'     => '_seopress_redirections_type',
                                        'value'   => $type,
                                        'compare' => 'IN',
                                );
                        } else {
                                $args['meta_query'][] = array(
                                        'key'     => '_seopress_redirections_type',
                                        'value'   => $type,
                                        'compare' => '=',
                                );
                        }
                }

                $seopress_redirects_query = new \WP_Query( $args );
                $posts                    = $seopress_redirects_query->posts;
                $response                 = array();

                foreach ( $posts as $post ) {
                        $response[ $post->ID ] = array(
                                'origin'               => $post->post_title,
                                'destination'          => get_post_meta( $post->ID, '_seopress_redirections_value', true ),
                                'enabled'              => get_post_meta( $post->ID, '_seopress_redirections_enabled', true ),
                                'type'                 => get_post_meta( $post->ID, '_seopress_redirections_type', true ),
                                'param'                => get_post_meta( $post->ID, '_seopress_redirections_param', true ),
                                'enabled_regex'        => get_post_meta( $post->ID, '_seopress_redirections_enabled_regex', true ),
                                'logged_status'        => get_post_meta( $post->ID, '_seopress_redirections_logged_status', true ),
                                'ip'                   => get_post_meta( $post->ID, '_seopress_redirections_ip', true ),
                                'ua'                   => get_post_meta( $post->ID, 'seopress_redirections_ua', true ),
                                'full_origin'          => get_post_meta( $post->ID, 'seopress_redirections_referer', true ),
                                'date_request'         => get_post_meta( $post->ID, '_seopress_redirections_date_request', true ),
                                'count'                => get_post_meta( $post->ID, 'seopress_404_count', true ),
                                'redirect_date_request' => get_post_meta( $post->ID, '_seopress_404_redirect_date_request', true ),
                        );
                }

                wp_send_json_success( $response );
        }

        /**
         * Determine if routes should be registered.
         *
         * @return bool
         */
        protected function shouldRegister() {
                return ! defined( 'SEOPRESS_PRO_VERSION' );
        }
}
