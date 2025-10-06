<?php
/**
 * TLY Provider (uses T.LY API)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AIOLS_Provider_Tly implements AIOLS_Provider_Interface {

    public function get_key() {
        return 'aiols_tly';
    }

    public function get_label() {
        return __( 'T.LY', 'all-in-one-link-shortener' );
    }

    public function shorten( $url ) {

        // Get API token from constant or option
        $api_key = defined( 'AIOLS_TLY_KEY' ) ? AIOLS_TLY_KEY : get_option( 'aiols_tly_key', '' );

        if ( empty( $api_key ) ) {
            throw new Exception( 'T.LY API key not configured.' );
        }

        $endpoint = 'https://api.t.ly/api/v1/link/shorten';
        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ),
            'body'    => wp_json_encode( array(
                'long_url' => esc_url_raw( $url ),
                'domain'   => 'https://t.ly/', // optional: replace with your custom domain if available
            ) ),
            'timeout' => 15,
        );

        $res = wp_remote_post( $endpoint, $args );

        if ( is_wp_error( $res ) ) {
            throw new Exception(
                'T.LY request failed: ' . esc_html( $res->get_error_message() )
            );
        }

        $code = wp_remote_retrieve_response_code( $res );
        $body = json_decode( wp_remote_retrieve_body( $res ), true );

        // Success: check for short_url key
        if ( $code >= 200 && $code < 300 && ! empty( $body['short_url'] ) ) {
            return esc_url_raw( $body['short_url'] );
        }

        // fallback or debug
        if ( ! empty( $body['data']['short_url'] ) ) {
            return esc_url_raw( $body['data']['short_url'] );
        }

        throw new Exception( 'T.LY API error: ' . wp_json_encode( $body ) );
    }
}
