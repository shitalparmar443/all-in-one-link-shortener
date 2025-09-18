<?php
/**
 * Provider interface for All In One Link Shortener
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

interface AIOLS_Provider_Interface {
    /**
     * Machine key for the provider
     * @return string
     */
    public function get_key();

    /**
     * Human-readable label for UI
     * @return string
     */
    public function get_label();

    /**
     * Shorten a URL and return short URL string.
     * Throw Exception on error.
     * @param string $url
     * @return string
     * @throws Exception
     */
    public function shorten( $url );
}
