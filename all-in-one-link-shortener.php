<?php
/**
 * Plugin Name: All In One Link Shortener
 * Plugin URI: https://wordpress.org/plugins/all-in-one-link-shortener/
 * Description: Multi-provider link shortener (TinyURL, Bitly, Rebrandly). Admin UI for keys and default provider.
 * Version: 1.0
 * Author: Shitalben Parmar
 * Contributors: shitalparmar443
 * Author URI: https://profiles.wordpress.org/shitalparmar443/
 * Donate link: https://www.paypal.me/shitalparmar443/
 * Text Domain: all-in-one-link-shortener
 * Requires at least: 6.1
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * Stable tag: 1.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'AIOLS_DIR' ) ) {
    define( 'AIOLS_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'AIOLS_URL' ) ) {
    define( 'AIOLS_URL', plugin_dir_url( __FILE__ ) );
}


/**
 * Load interface first (prevents "interface not found" fatal)
 */
require_once AIOLS_DIR . 'includes/class-aiols-interface.php';

/**
 * Load all provider classes (class-*.php)
 */
foreach ( glob( AIOLS_DIR . 'includes/providers/class-aiols-*.php' ) as $file ) {
    require_once $file;
}

/**
 * Admin settings (loads after providers so UI can show provider labels)
 */
require_once AIOLS_DIR . 'includes/class-aiols-admin-settings.php';

// Load WP-CLI
require_once AIOLS_DIR . 'includes/class-aiols-cli.php';

require_once AIOLS_DIR . 'includes/class-aiols-admin-columns.php';

require_once AIOLS_DIR . 'includes/class-aiols-bulk-actions.php';

/**
 * Add settings link to plugin list
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ) {
    $url = admin_url( 'options-general.php?page=aiols-settings' );
    $settings_link = '<a href="' . esc_url( $url ) . '">' . __( 'Settings', 'all-in-one-link-shortener' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
});

class AIOLS_Plugin {
    private static $instance;
    public $providers = array();

    public static function instance() {
        if ( ! self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', array( $this, 'init_all_in_one_link_shortener' ) );
        add_action( 'admin_init', array( $this, 'all_in_one_link_shortener_register_settings' ) );
        add_action( 'save_post', array( $this, 'all_in_one_link_shortener_generate_on_save' ), 10, 3 );
        add_shortcode( 'aiols_shortlink', array( $this, 'shortcode' ) );
        add_filter( 'post_row_actions', array( $this, 'add_regen_action' ), 10, 2 );
        add_action( 'admin_post_aiols_regenerate', array( $this, 'handle_regen_action' ) );
        
        add_filter('get_sample_permalink_html', array( $this,'all_in_one_link_shortener_add_custom_shortlink_field'), 10, 5);
        
        $this->register_providers();
    }

    public function init_all_in_one_link_shortener() {
        // start
    }

    public function all_in_one_link_shortener_register_settings() {
        // calls run at admin_init
    }
    public function all_in_one_link_shortener_add_custom_shortlink_field($return, $post_id, $new_title, $new_slug, $post) {
        global $post;

        $short_link =  get_post_meta($post->ID, '_aiols_shortlink', true);
        $html  = '';
        if( ! empty( $short_link ) ){
            $html .= '<div class="custom-shortlink" style="margin-top:5px;">';
            $html .= '<label for="short_link_field">Short Link:</label> ';
            $html .= '<input type="text" id="short_link_field" name="short_link_field" value="' . esc_attr($short_link) . '" style="width:300px;opacity: 0.5;"><button type="button" onclick="copyShortlink()">Copy</button>';
            $html .= '</div>';

            // Add JavaScript for the copy to clipboard functionality
            $html .= '<script>
                function copyShortlink() {
                    var copyText = document.getElementById("short_link_field");
                    copyText.select();
                    copyText.setSelectionRange(0, 99999); // For mobile devices
                    document.execCommand("copy");
                    alert("Shortlink copied: " + copyText.value);
                }
            </script>';
        }

        return $return . $html; // append our field after permalink
    }
    
    private function register_providers() {
        // Built-in providers - keys must match provider->get_key()
        $this->providers['permalink']   = new AIOLS_Provider_Default_Permalink_URL();
        $this->providers['tinyurl']   = new AIOLS_Provider_TinyURL();
        $this->providers['bitly']     = new AIOLS_Provider_Bitly();
        $this->providers['rebrandly'] = new AIOLS_Provider_Rebrandly();

        $this->providers = apply_filters( 'aiols_register_providers', $this->providers );
    }

    public function get_provider( $key ) {
        return isset( $this->providers[ $key ] ) ? $this->providers[ $key ] : false;
    }

    public function get_default_provider_key() {
        return get_option( 'aiols_default_provider', 'permalink' );
    }

    public function generate_shortlink_for_url( $url, $provider_key = null ) {
        if ( ! $provider_key ) $provider_key = $this->get_default_provider_key();
        $provider = $this->get_provider( $provider_key );
        if ( ! $provider ) return new WP_Error( 'no_provider', 'Provider not found' );

        try {
            $short = $provider->shorten( $url );
            return $short;
        } catch ( Exception $e ) {
            return new WP_Error( 'provider_error', $e->getMessage() );
        }
    }

    public function all_in_one_link_shortener_generate_on_save( $post_id, $post, $update ) {
        if ( wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) return;

        // Only for published posts
        if ( $post->post_status !== 'publish' ) {
            return;
        }

        $auto = get_option( 'aiols_auto_generate_on_save', 'no' );
        if ( $auto !== 'yes' ) return;

        // Condition: if already generated, don’t generate again
        $existing = get_post_meta( $post_id, '_aiols_shortlink', true );
        if ( ! empty( $existing ) ) {
            return; // shortlink exists => do nothing
        }

        $permalink = get_permalink( $post_id );
        $res = $this->generate_shortlink_for_url( $permalink );
        if ( is_wp_error( $res ) ) {
            update_post_meta( $post_id, '_aiols_last_error', $res->get_error_message() );
        } else {
            update_post_meta( $post_id, '_aiols_shortlink', esc_url_raw( $res ) );
            update_post_meta( $post_id, '_aiols_provider', $this->get_default_provider_key() );
        }
    }

    public function shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'id' => 0,
            'provider' => '',
        ), $atts, 'aiols_shortlink' );

        $id = intval( $atts['id'] );
        if ( ! $id ) return '';
        $provider = $atts['provider'] ? sanitize_text_field( $atts['provider'] ) : get_post_meta( $id, '_aiols_provider', true );
        $link = get_post_meta( $id, '_aiols_shortlink', true );
        if ( $link ) return esc_url( $link );

        $permalink = get_permalink( $id );
        $res = $this->generate_shortlink_for_url( $permalink, $provider );
        if ( is_wp_error( $res ) ) return '';
        return esc_url( $res );
    }

    public function add_regen_action( $actions, $post ) {
        if ( current_user_can( 'edit_post', $post->ID ) ) {
            $url = wp_nonce_url( admin_url( 'admin-post.php?action=aiols_regenerate&post_id=' . $post->ID ), 'aiols_regen_' . $post->ID );
            $actions['aiols_regen'] = '<a href="' . esc_url( $url ) . '">Regenerate shortlink</a>';
        }
        return $actions;
    }

    public function handle_regen_action() {
        if ( ! isset( $_GET['post_id'] ) ) wp_die( 'Missing post' );
        $post_id = intval( $_GET['post_id'] );
        check_admin_referer( 'aiols_regen_' . $post_id );
        if ( ! current_user_can( 'edit_post', $post_id ) ) wp_die( 'No permission' );

        $permalink = get_permalink( $post_id );
        $res = $this->generate_shortlink_for_url( $permalink );
        if ( is_wp_error( $res ) ) {
            wp_redirect( add_query_arg( 'aiols_error', urlencode( $res->get_error_message() ), wp_get_referer() ) );
            exit;
        }

        update_post_meta( $post_id, '_aiols_shortlink', esc_url_raw( $res ) );
        update_post_meta( $post_id, '_aiols_provider', $this->get_default_provider_key() );

        wp_redirect( wp_get_referer() );
        exit;
    }
}

AIOLS_Plugin::instance();