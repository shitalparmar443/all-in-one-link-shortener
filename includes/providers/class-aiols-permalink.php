<?php
/**
 * Default_Permalink provider
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AIOLS_Provider_Default_Permalink_URL implements AIOLS_Provider_Interface {

    public function get_key() { return 'permalink'; }
    public function get_label() { return __( 'Default Permalink (Site page/post URL)', 'all-in-one-link-shortener' ); }
    
    /**
     * Return the original URL (no shortening).
     *
     * @param string $url
     * @return string
     * @throws Exception
     */
    public function shorten( $url ) {
        if ( empty( $url ) ) {
            throw new Exception( 'Empty URL' );
        }

        // Make sure it's a valid URL and return raw (the plugin stores esc_url_raw elsewhere).
        return esc_url_raw( $url );
    }
}
