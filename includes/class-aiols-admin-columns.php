<?php
/**
 * All In One Link Shortener - Admin Columns with Generate button
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class AIOLS_Admin_Columns {

    public function __construct() {
        add_filter( 'manage_post_posts_columns', [ $this, 'add_column' ] );
        add_action( 'manage_post_posts_custom_column', [ $this, 'render_column' ], 10, 2 );

        // Handle AJAX
        add_action( 'wp_ajax_aiols_generate_shortlink', [ $this, 'ajax_generate' ] );

        // JS for copy + generate
        add_action( 'admin_footer-edit.php', [ $this, 'column_js' ] );
    }

    /**
     * Add the Shortlink column
     */
    public function add_column( $columns ) {
        $columns['aiols_shortlink'] = __( 'Shortlink', 'all-in-one-link-shortener' );
        return $columns;
    }

    /**
     * Render Shortlink column content
     */
    public function render_column( $column, $post_id ) {
        if ( $column !== 'aiols_shortlink' ) {
            return;
        }

        $shortlink = get_post_meta( $post_id, '_aiols_shortlink', true );

        if ( $shortlink ) {
            $shortlink_esc = esc_url( $shortlink );

            printf(
                '<a href="%1$s" target="_blank">%1$s</a><br>',
                esc_url($shortlink_esc)
            );

            printf(
                ' <button type="button" class="button button-small aiols-copy" data-link="%1$s">%2$s</button>',
                esc_attr( $shortlink ),
                esc_html__( 'Copy', 'all-in-one-link-shortener' )
            );

        } else {
            printf(
                '<button type="button" class="button button-small aiols-generate" data-post="%1$d">%2$s</button>',
                (int) $post_id,
                esc_html__( 'Generate', 'all-in-one-link-shortener' )
            );
        }

    }

    /**
     * AJAX callback: generate shortlink
     */
    public function ajax_generate() {
        check_ajax_referer( 'aiols_generate_shortlink', 'nonce' );

        $post_id = absint( $_POST['post_id'] ?? 0 );
        if ( ! $post_id ) {
            wp_send_json_error( 'Invalid post ID.' );
        }

        $plugin    = AIOLS_Plugin::instance();
        $providers = $plugin->providers;

        $provider_key = get_option( 'aiols_default_provider', 'permalink' );
        if ( ! isset( $providers[ $provider_key ] ) ) {
            wp_send_json_error( 'Provider not found.' );
        }

        $provider = $providers[ $provider_key ];
        try {
            $shortlink = $provider->shorten( get_permalink( $post_id ) );
            update_post_meta( $post_id, '_aiols_shortlink', $shortlink );
            wp_send_json_success( [ 'shortlink' => $shortlink ] );
        } catch ( Exception $e ) {
            wp_send_json_error( $e->getMessage() );
        }
    }

    /**
     * JavaScript for copy and generate buttons
     */
    public function column_js() {
        $nonce = wp_create_nonce( 'aiols_generate_shortlink' );
        ?>
        <script>
        document.addEventListener('click', function(e) {
            // Copy button
            if (e.target && e.target.classList.contains('aiols-copy')) {
                e.preventDefault();
                const link = e.target.getAttribute('data-link');
                navigator.clipboard.writeText(link).then(() => {
                    e.target.textContent = 'Copied!';
                    setTimeout(() => { e.target.textContent = 'Copy'; }, 1500);
                });
            }

            // Generate button
            if (e.target && e.target.classList.contains('aiols-generate')) {
                e.preventDefault();
                const postId = e.target.getAttribute('data-post');
                const button = e.target;
                button.disabled = true;
                button.textContent = 'Generating...';

                fetch(ajaxurl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=aiols_generate_shortlink&nonce=<?php echo esc_js( $nonce ); ?>&post_id=' + postId
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const link = data.data.shortlink;
                        button.outerHTML = '<a href="'+link+'" target="_blank">'+link+'</a> <button type="button" class="button button-small aiols-copy" data-link="'+link+'">Copy</button>';
                    } else {
                        button.textContent = 'Error';
                        alert('Error: ' + data.data);
                    }
                })
                .catch(err => {
                    button.textContent = 'Error';
                    alert('Request failed: ' + err);
                });
            }
        });
        </script>
        <?php
    }
}

new AIOLS_Admin_Columns();
