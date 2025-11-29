<?php
/**
 * Standalone license screen for SEOPress when the PRO add-on is not active.
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

$license = defined( 'SEOPRESS_LICENSE_KEY' ) && is_string( SEOPRESS_LICENSE_KEY ) ? SEOPRESS_LICENSE_KEY : get_option( 'seopress_pro_license_key' );
$status  = get_option( 'seopress_pro_license_status' );

if ( function_exists( 'seopress_admin_header' ) ) {
        echo seopress_admin_header(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
?>

<form class="seopress-option" method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
        <?php settings_fields( 'seopress_license' ); ?>
        <?php echo $this->feature_title( null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div id="seopress-tabs" class="wrap full-width">
                <div class="seopress-tab active">
                        <p><?php esc_html_e( 'Enter your license key to unlock updates and support.', 'wp-seopress' ); ?></p>

                        <table class="form-table" role="presentation">
                                <tbody>
                                        <tr valign="top">
                                                <th scope="row"><?php esc_html_e( 'License Key', 'wp-seopress' ); ?></th>
                                                <td valign="top">
                                                        <input id="seopress_pro_license_key" name="seopress_pro_license_key" type="text" autocomplete="off" spellcheck="false" autocorrect="off" autocapitalize="off" class="regular-text" value="<?php echo esc_attr( $license ? '********************************' : '' ); ?>" />
                                                        <p class="description"><?php esc_html_e( 'Save your key to keep it available even if the PRO plugin is not installed.', 'wp-seopress' ); ?></p>
                                                </td>
                                        </tr>
                                        <tr valign="top">
                                                <th scope="row"><?php esc_html_e( 'Status', 'wp-seopress' ); ?></th>
                                                <td valign="top">
                                                        <?php if ( $status ) : ?>
                                                                <span class="seopress-notice <?php echo 'valid' === $status ? 'is-success' : 'is-warning'; ?>">
                                                                        <?php echo esc_html( $status ); ?>
                                                                </span>
                                                        <?php else : ?>
                                                                <span class="description"><?php esc_html_e( 'No activation status stored yet.', 'wp-seopress' ); ?></span>
                                                        <?php endif; ?>
                                                </td>
                                        </tr>
                                </tbody>
                        </table>

                        <?php echo $this->feature_save(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <?php submit_button( __( 'Save changes', 'wp-seopress' ) ); ?>
                </div>
        </div>
</form>
