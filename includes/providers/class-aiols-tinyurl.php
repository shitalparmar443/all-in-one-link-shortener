<?php
/**
 * TinyURL provider (uses TinyURL API)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AIOLS_Provider_TinyURL implements AIOLS_Provider_Interface {

    public function get_key() { return 'tinyurl'; }
    public function get_label() { return __( 'TinyURL', 'all-in-one-link-shortener' ); }

    public function shorten( $url ) {
        $api_key = defined('AIOLS_TINYURL_KEY') ? AIOLS_TINYURL_KEY : get_option('aiols_tinyurl_api_key','');
        if ( empty( $api_key ) ) {
            throw new Exception( 'TinyURL API key not configured.' );
        }

        $endpoint = 'https://api.tinyurl.com/create';
        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ),
            'body' => wp_json_encode( array( 'url' => esc_url_raw( $url ) ) ),
            'timeout' => 15,
        );

        $res = wp_remote_post( $endpoint, $args );
        if ( is_wp_error( $res ) ) {
            throw new Exception(
                'TinyURL request failed: ' . esc_html( $res->get_error_message() )
            );
        }


        $code = wp_remote_retrieve_response_code( $res );
        $body = json_decode( wp_remote_retrieve_body( $res ), true );

        if ( $code >= 200 && $code < 300 && ! empty( $body['data']['tiny_url'] ) ) {
            return esc_url_raw( $body['data']['tiny_url'] );
        }

        // try fallback fields or show error body for debugging
        if ( ! empty( $body['data']['url'] ) ) return esc_url_raw( $body['data']['url'] );
        throw new Exception( 'TinyURL API error: ' . wp_json_encode( $body ) );
    }
}
