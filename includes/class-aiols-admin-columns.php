<?php
/**
 * All In One Link Shortener - Admin Columns with Generate button
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AIOLS_Admin_Columns {

    public function __construct() {
        add_filter( 'manage_post_posts_columns', [ $this, 'add_column' ] );
        add_action( 'manage_post_posts_custom_column', [ $this, 'render_column' ], 10, 2 );

        // Handle AJAX
        add_action( 'wp_ajax_aiols_generate_shortlink', [ $this, 'ajax_generate' ] );
    }

    /**
     * Add the Shortlink column
     */
    public function add_column( $columns ) {
        $columns['aiols_shortlink'] = __( 'Shortlink', 'all-in-one-link-shortener' );
        return $columns;
    }

    /**
     * Render Shortlink column content
     */
    public function render_column( $column, $post_id ) {
        if ( $column !== 'aiols_shortlink' ) {
            return;
        }

        $shortlink = get_post_meta( $post_id, '_aiols_shortlink', true );

        if ( $shortlink ) {
            $shortlink_esc = esc_url( $shortlink );

            printf(
                '<a href="%1$s" target="_blank">%1$s</a><br>',
                esc_url($shortlink_esc)
            );

            printf(
                ' <button type="button" class="button button-small aiols-copy" data-link="%1$s">%2$s</button>',
                esc_attr( $shortlink ),
                esc_html__( 'Copy', 'all-in-one-link-shortener' )
            );

        } else {
            printf(
                '<button type="button" class="button button-small aiols-generate" data-post="%1$d">%2$s</button>',
                (int) $post_id,
                esc_html__( 'Generate', 'all-in-one-link-shortener' )
            );
        }

    }

    /**
     * AJAX callback: generate shortlink
     */
    public function ajax_generate() {
        check_ajax_referer( 'aiols_generate_shortlink', 'nonce' );

        $post_id = absint( $_POST['post_id'] ?? 0 );
        if ( ! $post_id ) {
            wp_send_json_error( 'Invalid post ID.' );
        }

        $plugin    = AIOLS_Plugin::instance();
        $providers = $plugin->providers;

        $provider_key = get_option( 'aiols_default_provider', 'permalink' );
        if ( ! isset( $providers[ $provider_key ] ) ) {
            wp_send_json_error( 'Provider not found.' );
        }

        $provider = $providers[ $provider_key ];
        try {
            $shortlink = $provider->shorten( get_permalink( $post_id ) );
            update_post_meta( $post_id, '_aiols_shortlink', $shortlink );
            wp_send_json_success( [ 'shortlink' => $shortlink ] );
        } catch ( Exception $e ) {
            wp_send_json_error( $e->getMessage() );
        }
    }

}

new AIOLS_Admin_Columns();
