<?php
/**
 * Bitly provider (uses v4 shorten endpoint with access token)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AIOLS_Provider_Bitly implements AIOLS_Provider_Interface {

    public function get_key() { return 'bitly'; }
    public function get_label() { return __( 'Bitly', 'all-in-one-link-shortener' ); }

    public function shorten( $url ) {
        $token = defined('AIOLS_BITLY_TOKEN') ? AIOLS_BITLY_TOKEN : get_option('aiols_bitly_token','');
        if ( empty( $token ) ) {
            throw new Exception( 'Bitly token not configured.' );
        }

        $domain = get_option( 'aiols_bitly_domain', '' ); // optional branded domain
        $body = array( 'long_url' => esc_url_raw( $url ) );
        if ( ! empty( $domain ) ) $body['domain'] = sanitize_text_field( $domain );

        $endpoint = 'https://api-ssl.bitly.com/v4/shorten';
        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ),
            'body' => wp_json_encode( $body ),
            'timeout' => 15,
        );

        $res = wp_remote_post( $endpoint, $args );
        if ( is_wp_error( $res ) ) {
            throw new Exception(
                'Bitly request failed: ' . esc_html( $res->get_error_message() )
            );
        }

        $code = wp_remote_retrieve_response_code( $res );
        $body = json_decode( wp_remote_retrieve_body( $res ), true );

        if ( $code >= 200 && $code < 300 && ! empty( $body['link'] ) ) {
            return esc_url_raw( $body['link'] );
        }

        throw new Exception( 'Bitly API error: ' . wp_json_encode( $body ) );
    }
}
