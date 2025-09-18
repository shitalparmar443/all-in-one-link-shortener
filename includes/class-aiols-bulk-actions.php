<?php
/**
 * All In One Link Shortener - Bulk Actions
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AIOLS_Bulk_Actions {

    public function __construct() {
        add_filter( 'bulk_actions-edit-post', [ $this, 'register_bulk_action' ] );
        add_filter( 'handle_bulk_actions-edit-post', [ $this, 'handle_bulk_action' ], 10, 3 );
        add_action( 'admin_notices', [ $this, 'admin_notice' ] );
    }

    /**
     * Register bulk action in dropdown
     */
    public function register_bulk_action( $bulk_actions ) {
        $bulk_actions['aiols_generate_shortlinks'] = __( 'Generate Shortlinks', 'all-in-one-link-shortener' );
        return $bulk_actions;
    }

    /**
     * Handle bulk action
     */
    public function handle_bulk_action( $redirect_url, $action, $post_ids ) {
        if ( $action !== 'aiols_generate_shortlinks' ) {
            return $redirect_url;
        }

        $plugin    = AIOLS_Plugin::instance();
        $providers = $plugin->providers;

        $provider_key = get_option( 'aiols_default_provider', 'permalink' );
        if ( ! isset( $providers[ $provider_key ] ) ) {
            set_transient( 'aiols_bulk_notice', [
                'type' => 'error',
                'msg'  => __( 'Provider not found.', 'all-in-one-link-shortener' ),
            ], 30 );
            return $redirect_url;
        }

        $provider  = $providers[ $provider_key ];
        $generated = 0;
        $skipped   = 0;

        foreach ( $post_ids as $post_id ) {
            $existing = get_post_meta( $post_id, '_aiols_shortlink', true );
            if ( $existing ) {
                $skipped++;
                continue;
            }

            try {
                $shortlink = $provider->shorten( get_permalink( $post_id ) );
                update_post_meta( $post_id, '_aiols_shortlink', $shortlink );
                $generated++;
            } catch ( Exception $e ) {
                $skipped++;
            }
        }

        set_transient( 
            'aiols_bulk_notice', 
            [
                'type' => 'success',
                'msg'  => sprintf(
                    /* translators: 1: Number of generated shortlinks, 2: Number of skipped posts */
                    __( 'Generated %1$d shortlinks. Skipped %2$d posts (already had shortlink or failed).', 'all-in-one-link-shortener' ),
                    $generated,
                    $skipped
                ),
            ], 
            30 
        );

        return $redirect_url;
    }


    /**
     * Show admin notice after bulk action
     */
    public function admin_notice() {
        $notice = get_transient( 'aiols_bulk_notice' );

        if ( $notice ) {
            delete_transient( 'aiols_bulk_notice' ); // make sure it shows only once

            $class = $notice['type'] === 'error' ? 'notice-error' : 'notice-success';
            echo '<div class="notice ' . esc_attr( $class ) . ' is-dismissible"><p>' .
                esc_html( $notice['msg'] ) .
                '</p></div>';
        }
    }
}

new AIOLS_Bulk_Actions();
