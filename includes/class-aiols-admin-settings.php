<?php
/**
 * Admin settings for All In One Link Shortener
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AIOLS_Admin_Settings {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function add_menu() {
        add_menu_page(
            __( 'All In One Link Shortener', 'all-in-one-link-shortener' ), // Page title
            __( 'All In One Link Shortener', 'all-in-one-link-shortener' ), // Menu title
            'manage_options', // Capability
            'aiols-settings', // Slug
            [ $this, 'settings_page' ], // Callback
            'dashicons-admin-links', // Icon
            81,  // Position (after Settings)
        );
    }


    public function register_settings() {
        register_setting( 'aiols_options', 'aiols_default_provider', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'aiols_options', 'aiols_auto_generate_on_save', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        $keys = array(
            'aiols_tinyurl_api_key',
            'aiols_bitly_token',
            'aiols_rebrandly_key',
            'aiols_cuttly_key',
            'aiols_tly_key',
        );
        foreach ( $keys as $k ) register_setting( 'aiols_options', $k, array( 'sanitize_callback' => 'sanitize_text_field' ) );
    }

    public function settings_page() {
        $providers = AIOLS_Plugin::instance()->providers;
        ?>
        <div class="wrap">
        		<?php settings_errors(); // Display success/error messages ?>
            <h1><?php esc_html_e( 'All In One Link Shortener Settings', 'all-in-one-link-shortener' ); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields( 'aiols_options' ); do_settings_sections( 'aiols-settings' ); ?>
                <table class="form-table" role="presentation">

                    <tr>
                        <th scope="row">
                            <label for="aiols_default_provider"><?php esc_html_e( 'Default Provider', 'all-in-one-link-shortener' ); ?></label>
                        </th>
                        <td>
                            <select name="aiols_default_provider" id="aiols_default_provider">
                                <?php foreach ( $providers as $key => $provider ) : ?>
                                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( get_option( 'aiols_default_provider', 'aiols_permalink' ), $key ); ?>>
                                        <?php echo esc_html( $provider->get_label() ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php esc_html_e( 'Choose provider used by default.', 'all-in-one-link-shortener' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e( 'Auto-generate on Save', 'all-in-one-link-shortener' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="aiols_auto_generate_on_save" value="yes" <?php checked( get_option( 'aiols_auto_generate_on_save', 'no' ), 'yes' ); ?> />
                                <?php esc_html_e( 'Automatically generate shortlink when saving posts', 'all-in-one-link-shortener' ); ?>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="aiols_tinyurl_api_key"><?php esc_html_e( 'TinyURL API Key', 'all-in-one-link-shortener' ); ?></label></th>
                        <td>
                            <input type="text" name="aiols_tinyurl_api_key" id="aiols_tinyurl_api_key" value="<?php echo esc_attr( get_option( 'aiols_tinyurl_api_key', '' ) ); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e( 'From https://tinyurl.com/app/dev or define AIOLS_TINYURL_KEY in wp-config.php', 'all-in-one-link-shortener' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="aiols_bitly_token"><?php esc_html_e( 'Bitly Access Token', 'all-in-one-link-shortener' ); ?></label></th>
                        <td>
                            <input type="text" name="aiols_bitly_token" id="aiols_bitly_token" value="<?php echo esc_attr( get_option( 'aiols_bitly_token', '' ) ); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e( 'You can define AIOLS_BITLY_TOKEN in wp-config.php or paste token here.', 'all-in-one-link-shortener' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="aiols_rebrandly_key"><?php esc_html_e( 'Rebrandly API Key', 'all-in-one-link-shortener' ); ?></label></th>
                        <td>
                            <input type="text" name="aiols_rebrandly_key" id="aiols_rebrandly_key" value="<?php echo esc_attr( get_option( 'aiols_rebrandly_key', '' ) ); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e( 'You can define AIOLS_REBRANDLY_KEY in wp-config.php or paste token here.', 'all-in-one-link-shortener' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="aiols_cuttly_key"><?php esc_html_e( 'Cutt.ly API Key', 'all-in-one-link-shortener' ); ?></label></th>
                        <td>
                        	<input type="text" name="aiols_cuttly_key" id="aiols_cuttly_key" value="<?php echo esc_attr( get_option( 'aiols_cuttly_key', '' ) ); ?>" class="regular-text" />
                        	<p class="description"><?php esc_html_e( 'You can define AIOLS_CUTTLY in wp-config.php or paste token here.', 'all-in-one-link-shortener' ); ?></p>
                        </td>
                    </tr>
                    <tr>
									    <th scope="row">
									        <label for="aiols_isgd_key"><?php esc_html_e( 'is.gd', 'all-in-one-link-shortener' ); ?></label>
									    </th>
									    <td>
									        <p class="description">
									            <?php
									            printf(
									                /* translators: %s: is.gd API documentation URL */
									                esc_html__( 'is.gd does not require an API key. For more information, visit: %s', 'all-in-one-link-shortener' ),
									                '<a href="https://is.gd/apishorteningreference.php" target="_blank" rel="noopener noreferrer">https://is.gd/apishorteningreference.php</a>'
									            );
									            ?>
									        </p>
									        <p class="description">
									            <?php
									            printf(
									                /* translators: %s: is.gd JSON API URL */
									                esc_html__( 'This plugin uses the JSON method for URL shortening: %s', 'all-in-one-link-shortener' ),
									                '<a href="https://is.gd/create.php?format=json&amp;url=longurl" target="_blank" rel="noopener noreferrer">https://is.gd/create.php?format=json&amp;url=longurl</a>'
									            );
									            ?>
									        </p>
									    </td>
									</tr>
                  <tr>
									    <th scope="row">
									        <label for="aiols_tly_key"><?php esc_html_e( 't.ly', 'all-in-one-link-shortener' ); ?></label>
									    </th>
									    <td>
									    		<input type="text" name="aiols_tly_key" id="aiols_tly_key" value="<?php echo esc_attr( get_option( 'aiols_tly_key', '' ) ); ?>" class="regular-text" />
									        <p class="description"><?php esc_html_e( 'You can define AIOLS_TLY in wp-config.php or paste token here.', 'all-in-one-link-shortener' ); ?></p>
									    </td>
									</tr>
                    
                </table>

                <?php submit_button(); ?>
            </form>

        </div>
        <?php
    }
}

new AIOLS_Admin_Settings();