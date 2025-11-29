<?php // phpcs:ignore

namespace SEOPress\Actions\Api\PageSpeed;

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

use SEOPress\Core\Hooks\ExecuteHooks;

class GetPageSpeedReport implements ExecuteHooks {
        public function hooks() {
                if ( ! $this->shouldRegister() ) {
                        return;
                }

                add_action( 'rest_api_init', array( $this, 'register' ) );
        }

        public function register() {
                register_rest_route(
                        'seopress/v1',
                        '/page-speed',
                        array(
                                'methods'             => 'GET',
                                'callback'            => array( $this, 'processGet' ),
                                'args'                => array(
                                        'device' => array(
                                                'validate_callback' => function ( $param ) {
                                                        return in_array( $param, array( 'desktop', 'mobile' ), true );
                                                },
                                        ),
                                ),
                                'permission_callback' => function () {
                                        return current_user_can( 'manage_options' );
                                },
                        )
                );
        }

        public function processGet( \WP_REST_Request $request ) {
                $device = $request->get_param( 'device' ) ?? 'mobile';

                if ( ! in_array( $device, array( 'desktop', 'mobile' ), true ) ) {
                        return new \WP_REST_Response( __( 'Invalid device', 'wp-seopress' ) );
                }

                $data = ( 'desktop' === $device ) ? get_transient( 'seopress_results_page_speed_desktop' ) : get_transient( 'seopress_results_page_speed' );

                if ( is_string( $data ) ) {
                        $data = json_decode( $data );
                }

                if ( empty( $data ) ) {
                        return new \WP_REST_Response( __( 'No data found', 'wp-seopress' ) );
                }

                return new \WP_REST_Response( $data );
        }

        /**
         * Determine if the route should be registered.
         *
         * @return bool
         */
        protected function shouldRegister() {
                return ! defined( 'SEOPRESS_PRO_VERSION' );
        }
}
