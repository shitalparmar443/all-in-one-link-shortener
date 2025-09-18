<?php
/**
 * Rebrandly provider
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AIOLS_Provider_Rebrandly implements AIOLS_Provider_Interface {

    public function get_key() { return 'rebrandly'; }
    public function get_label() { return __( 'Rebrandly', 'all-in-one-link-shortener' ); }

    public function shorten( $url ) {
        $key = defined('AIOLS_REBRANDLY_KEY') ? AIOLS_REBRANDLY_KEY : get_option('aiols_rebrandly_key','');
        if ( empty( $key ) ) throw new Exception( 'Rebrandly API key not configured.' );

        $endpoint = 'https://api.rebrandly.com/v1/links';
        $body = array( 'destination' => esc_url_raw( $url ) );

        $args = array(
            'headers' => array(
                'apikey' => $key,
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode( $body ),
            'timeout' => 15,
        );

        $res = wp_remote_post( $endpoint, $args );
        if ( is_wp_error( $res ) ) {
            throw new Exception(
                'Rebrandly request failed: ' . esc_html( $res->get_error_message() )
            );
        }

        $code = wp_remote_retrieve_response_code( $res );
        $body = json_decode( wp_remote_retrieve_body( $res ), true );

        if ( $code >= 200 && $code < 300 && ! empty( $body['shortUrl'] ) ) {
            return esc_url_raw( 'https://' . $body['shortUrl'] );
        }

        throw new Exception( 'Rebrandly API error: ' . wp_json_encode( $body ) );
    }
}
