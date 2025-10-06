<?php
/**
 * Default_Permalink provider
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Default Permalink URL Provider
 *
 * This provider returns the original post/page URL using WordPress's built-in
 * `wp_get_shortlink()` function. No external URL shortening service is used.
 *
 * Implements the AIOLS_Provider_Interface.
 *
 * @since 1.0
 */
class AIOLS_Provider_Default_Permalink_URL implements AIOLS_Provider_Interface {

    /**
     * Get the unique provider key.
     *
     * This key is used internally to identify the provider.
     *
     * @since 1.0
     *
     * @return string Unique key for this provider.
     */
    public function get_key() {
        return 'aiols_permalink';
    }

    /**
     * Get the human-readable provider label.
     *
     * This label is displayed in the admin UI when selecting a provider.
     *
     * @since 1.0
     *
     * @return string Translatable label.
     */
    public function get_label() {
        return __( 'Default Permalink (Site page/post URL)', 'all-in-one-link-shortener' );
    }

    /**
     * Shorten the given URL.
     *
     * For this provider, it simply returns the original post/page URL
     * using WordPress's `wp_get_shortlink()` function.
     *
     * @since 1.0
     *
     * @param string $url The original URL to shorten.
     * @return string The shortlink (or original URL if no shortening).
     *
     * @throws Exception If the URL is empty or invalid.
     */
    public function shorten( $url ) {

		    // Validate input
		    if ( empty( $url ) ) {
		        throw new Exception( esc_html__( 'Empty URL provided to Default Permalink provider.', 'all-in-one-link-shortener' ) );
		    }

		    // Try to get the post ID from the URL
		    $post_id = url_to_postid( $url );

		    // Use WordPress built-in shortlink function
		    $shortlink = wp_get_shortlink( $post_id );

		    // Return sanitized URL
		    return esc_url_raw( $shortlink );
		}

}