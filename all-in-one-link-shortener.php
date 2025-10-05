<?php
/**
 * Plugin Name: All In One Link Shortener
 * Plugin URI: https://wordpress.org/plugins/all-in-one-link-shortener/
 * Description: Multi-provider link shortener (TinyURL, Bitly, Rebrandly). Admin UI for keys and default provider.
 * Version: 1.0.2
 * Author: Shitalben Parmar
 * Contributors: shitalparmar443
 * Author URI: https://profiles.wordpress.org/shitalparmar443/
 * Donate link: https://www.paypal.me/shitalparmar443/
 * Text Domain: all-in-one-link-shortener
 * Requires at least: 6.1
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * Stable tag: 1.0.2
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
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
    $url = admin_url( 'admin.php?page=aiols-settings' );
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
        
        add_action( 'admin_enqueue_scripts', array( $this, 'all_in_one_link_shortener_enqueue_admin_scripts' ) );

        $this->register_providers();
    }

    public function init_all_in_one_link_shortener() {
        // start
    }

    public function all_in_one_link_shortener_register_settings() {
        // calls run at admin_init
    }

	  /**
		 * Enqueue admin JavaScript for the custom shortlink copy button.
		 *
		 * This script is only loaded on the post editing screens (post.php and post-new.php).
		 * It powers the "Copy Shortlink" button that copies the generated shortlink
		 * to the clipboard and displays a confirmation message.
		 *
		 * @since 1.0
		 *
		 * @param string $hook The current admin page hook suffix.
		 */
		public function all_in_one_link_shortener_enqueue_admin_scripts( $hook ) {

		    // Only load script on the add/edit post screens.
		    if ( ! in_array( $hook, array( 'post.php', 'post-new.php', 'edit.php' ), true ) ) {
		        return;
		    }

		    // Enqueue the custom admin script for handling shortlink copy.
		    wp_enqueue_script(
		        'aiols-admin-shortlink', // Unique script handle.
		        plugin_dir_url( __FILE__ ) . 'assets/js/admin-aiols-shortlink.js',
		        array(),
		        '1.0.0',
		        true
		    );

		    // Localize script to make strings translatable in JS.
		    wp_localize_script(
		        'aiols-admin-shortlink',
		        'aiols_js',
		        array(
		        		'ajax_url'     => admin_url( 'admin-ajax.php' ),
		        		'nonce'        => wp_create_nonce( 'aiols_generate_shortlink' ),
		        		'generate_text'=> __( 'Generating...', 'all-in-one-link-shortener' ),
		        		'error_text'   => __( 'Error', 'all-in-one-link-shortener' ),
		        		'error_message'=> __( 'Request failed', 'all-in-one-link-shortener' ),
		            'message'      => __( 'Shortlink copied:', 'all-in-one-link-shortener' ),
		        )
		    );
		}

    /**
		 * Add custom shortlink field to the post edit screen.
		 *
		 * @param string  $return    The existing HTML for the permalink.
		 * @param int     $post_id   The current post ID.
		 * @param string  $new_title The new post title.
		 * @param string  $new_slug  The new post slug.
		 * @param WP_Post $post      The post object.
		 *
		 * @return string Modified HTML with custom shortlink field.
		 */
		public function all_in_one_link_shortener_add_custom_shortlink_field( $return, $post_id, $new_title, $new_slug, $post ) {
		    $short_link = get_post_meta( $post_id, '_aiols_shortlink', true );

		    if ( empty( $short_link ) ) {
		        return $return;
		    }

		    ob_start();
		    ?>
		    <div class="custom-shortlink">
		        <button type="button" class="button button-secondary aiols-copy-shortlink" data-shortlink="<?php echo esc_attr( $short_link ); ?>" >
		            <?php esc_html_e( 'Copy Shortlink', 'all-in-one-link-shortener' ); ?>
		        </button>
		    </div>
		    <?php
		    return $return . ob_get_clean();
		}
    
    /**
		 * Register the available shortlink providers.
		 *
		 * This method initializes the built-in shortlink providers
		 * (Permalink, TinyURL, Bitly, and Rebrandly) and allows
		 * third-party developers to add or override providers
		 * via the `aiols_register_providers` filter.
		 *
		 * Each provider must implement a `get_key()` method, and the
		 * array keys here should match those provider keys.
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function register_providers() {
		    // Built-in providers — keys must match the provider's get_key() value.
		    $this->providers['permalink']   = new AIOLS_Provider_Default_Permalink_URL();
		    $this->providers['tinyurl']     = new AIOLS_Provider_TinyURL();
		    $this->providers['bitly']       = new AIOLS_Provider_Bitly();
		    $this->providers['rebrandly']   = new AIOLS_Provider_Rebrandly();
		    $this->providers['cuttly']    = new AIOLS_Provider_Cuttly();
		    $this->providers['isgd']      = new AIOLS_Provider_Isgd();
		    /**
		     * Filter the list of registered shortlink providers.
		     *
		     * Developers can use this filter to add custom providers or
		     * modify/remove existing ones. Each provider should be an object
		     * implementing the required interface or methods expected by the plugin.
		     *
		     * @since 1.0
		     *
		     * @param array $this->providers An associative array of provider objects, keyed by provider slug.
		     */
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

    /**
		 * Add a "Regenerate shortlink" action to the post row actions.
		 *
		 * This adds a custom link under each post in the post list table,
		 * allowing users with the 'edit_post' capability to regenerate the shortlink.
		 *
		 * @since 1.0
		 *
		 * @param array   $actions Existing row actions.
		 * @param WP_Post $post    Post object.
		 * @return array Modified row actions including "Regenerate shortlink" link.
		 */
		public function add_regen_action( $actions, $post ) {

		    // Check if the current user can edit this post.
		    if ( current_user_can( 'edit_post', $post->ID ) ) {

		        // Generate a secure nonce URL for regenerating the shortlink.
		        $url = wp_nonce_url(
		            admin_url( 'admin-post.php?action=aiols_regenerate&post_id=' . $post->ID ),
		            'aiols_regen_' . $post->ID
		        );

		        // Add the custom action link.
		        $actions['aiols_regen'] = sprintf(
		            '<a href="%s">%s</a>',
		            esc_url( $url ),
		            esc_html__( 'Regenerate shortlink', 'all-in-one-link-shortener' )
		        );
		    }

		    return $actions;
		}

    public function handle_regen_action() {

	    if ( ! isset( $_GET['post_id'] ) ) {
	        wp_die( esc_html__( 'Missing post ID.', 'all-in-one-link-shortener' ) );
	    }

	    $post_id = intval( $_GET['post_id'] );

	    check_admin_referer( 'aiols_regen_' . $post_id );

	    if ( ! current_user_can( 'edit_post', $post_id ) ) {
	        wp_die( esc_html__( 'You do not have permission to edit this post.', 'all-in-one-link-shortener' ) );
	    }

	    $permalink = get_permalink( $post_id );
	    $res       = $this->generate_shortlink_for_url( $permalink );

	    $redirect_url = wp_get_referer();

	    if ( is_wp_error( $res ) ) {
	        // Store error in transient for display after redirect.
	        set_transient(
	            'aiols_bulk_notice',
	            [
	                'type' => 'error',
	                'msg'  => $res->get_error_message(),
	            ],
	            30 // 30 seconds expiration
	        );

	        wp_redirect( $redirect_url );
	        exit;
	    }

	    // Update post meta
	    update_post_meta( $post_id, '_aiols_shortlink', esc_url_raw( $res ) );
	    update_post_meta( $post_id, '_aiols_provider', $this->get_default_provider_key() );

	    // Store success message in transient
			set_transient(
			    'aiols_bulk_notice',
			    [
			        'type' => 'success',
			        'msg'  => sprintf(
			            /* translators: %d is the post ID for which the shortlink was regenerated. */
			            __( 'Shortlink successfully regenerated for post ID %d', 'all-in-one-link-shortener' ),
			            $post_id
			        ),
			    ],
			    30 // 30 seconds expiration
			);

	    wp_redirect( $redirect_url );
	    exit;
	}
}

AIOLS_Plugin::instance();