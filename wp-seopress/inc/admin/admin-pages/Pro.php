<?php
/**
 * Lightweight PRO overview when the add-on is not present.
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( function_exists( 'seopress_admin_header' ) ) {
        echo seopress_admin_header(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
?>

<div class="wrap seopress-option">
        <?php echo $this->feature_title( null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <div id="seopress-tabs" class="wrap full-width">
                <div class="seopress-tab active">
                        <p><?php esc_html_e( 'Access the built-in SEOPress PRO settings directly from the core plugin.', 'wp-seopress' ); ?></p>

                        <ul class="ul-disc">
                                <li><a href="<?php echo esc_url( admin_url( 'admin.php?page=seopress-license' ) ); ?>"><?php esc_html_e( 'Manage your license', 'wp-seopress' ); ?></a></li>
                                <li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=seopress_schemas' ) ); ?>"><?php esc_html_e( 'Structured data types', 'wp-seopress' ); ?></a></li>
                                <li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=seopress_404' ) ); ?>"><?php esc_html_e( 'Redirections / 404 monitoring', 'wp-seopress' ); ?></a></li>
                                <li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=seopress_bot' ) ); ?>"><?php esc_html_e( 'Broken links', 'wp-seopress' ); ?></a></li>
                        </ul>

                        <p class="description"><?php esc_html_e( 'Need the full experience? Install the PRO add-on to enable advanced tabs such as Local Business, Breadcrumbs, PageSpeed, and more.', 'wp-seopress' ); ?></p>
                </div>
        </div>
</div>
