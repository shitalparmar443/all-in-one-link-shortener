<?php
/**
 * Cutt.ly provider for Link Shortener Multi
 *
 * File: includes/providers/class-cuttly.php
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AIOLS_Provider_Cuttly implements AIOLS_Provider_Interface {

    /**
     * Machine key for the provider (must match registration key)
     * @return string
     */
    public function get_key() {
        return 'aiols_cuttly';
    }

    /**
     * Human readable label used in settings UI
     * @return string
     */
    public function get_label() {
        return __( 'Cutt.ly', 'all-in-one-link-shortener' );
    }

    /**
     * Shorten a URL using Cutt.ly API
     *
     * Uses a constant AIOLS_CUTTLY if defined, otherwise option 'aiols_cuttly_key'
     *
     * @param string $url
     * @return string
     * @throws Exception on failure
     */
    public function shorten( $url ) {
        $key = defined( 'AIOLS_CUTTLY' ) ? AIOLS_CUTTLY : get_option( 'aiols_cuttly_key', '' );

        if ( empty( $key ) ) {
            throw new Exception( 'Cutt.ly API key not configured.' );
        }
        
        $endpoint = 'https://cutt.ly/api/api.php?key=' . rawurlencode( $key ) . '&short=' . rawurlencode( $url );

        $res = wp_remote_get( $endpoint, array( 'timeout' => 15 ) );

        if ( is_wp_error( $res ) ) {
            throw new Exception(
                'Cutt.ly request failed: ' . esc_html( $res->get_error_message() )
            );
        }


        $body_json = wp_remote_retrieve_body( $res );
        $body      = json_decode( $body_json, true );

        // Basic response validation
        if ( is_array( $body ) && ! empty( $body['url']['shortLink'] ) ) {
            return esc_url_raw( $body['url']['shortLink'] );
        }

        // If API returns an error description, include it for debugging
        if ( is_array( $body ) && ! empty( $body['url']['status'] ) ) {
            throw new Exception( 'Cutt.ly API error. Status: ' . esc_html( $body['url']['status'] ) . ' Response: ' . wp_json_encode( $body ) );
        }

        // Fallback generic error
        throw new Exception( 'Cutt.ly: unexpected API response: ' . wp_json_encode( $body ) );
    }
}
