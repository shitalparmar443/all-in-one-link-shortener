<?php
/**
 * is.gd provider (no API key)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AIOLS_Provider_Isgd implements AIOLS_Provider_Interface {

    public function get_key() { return 'isgd'; }
    public function get_label() { return __( 'is.gd', 'all-in-one-link-shortener' ); }

    public function shorten( $url ) {
        
      $endpoint = 'https://is.gd/create.php?format=json&url=' . rawurlencode( $url );
      $res      = wp_remote_get( $endpoint, array( 'timeout' => 15 ) );

      if ( is_wp_error( $res ) ) {
          throw new Exception(
              'is.gd request failed: ' . esc_html( $res->get_error_message() )
          );
      }

      $body = wp_remote_retrieve_body( $res );

      $data = json_decode( $body, true );

      if( isset( $data['shorturl'] ) && ! empty( $data['shorturl'] ) ){

      	return esc_url_raw( $data['shorturl'] );

      }else{

      	throw new Exception(
          'is.gd API returned an error: ' . esc_html( $data['errormessage'] )
      	);

      }
    }

}
