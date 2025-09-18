<?php
/**
 * All In One Link Shortener - WP-CLI Commands
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {

    class AIOLS_CLI {

        /**
         * Bulk-generate shortlinks for posts
         *
         * ## OPTIONS
         *
         * [--provider=<provider>]
         * : Provider key (default: option aiols_default_provider)
         *
         * [--post_type=<type>]
         * : Post type (default: post)
         *
         * ## EXAMPLES
         *
         *     wp aiols generate --provider=bitly --post_type=page
         *
         * @when after_wp_load
         */
        public function generate( $args, $assoc_args ) {
            $provider_key = $assoc_args['provider'] ?? get_option( 'aiols_default_provider', 'permalink' );
            $post_type    = $assoc_args['post_type'] ?? 'post';

            $plugin    = AIOLS_Plugin::instance();
            $providers = $plugin->providers;

            if ( ! isset( $providers[ $provider_key ] ) ) {
                WP_CLI::error( "Provider {$provider_key} not found." );
            }

            $provider = $providers[ $provider_key ];

            $query = new WP_Query( [
                'post_type'      => $post_type,
                'post_status'    => 'publish',
                'posts_per_page' => -1,
            ] );

            WP_CLI::log( "Found {$query->found_posts} posts. Generating shortlinks using {$provider_key}..." );

            $count = 0;
            foreach ( $query->posts as $post ) {
                try {
                    $shortlink = $provider->shorten( get_permalink( $post ) );
                    update_post_meta( $post->ID, '_aiols_shortlink', $shortlink );
                    $count++;
                    WP_CLI::log( "✔ {$post->post_title} → {$shortlink}" );
                } catch ( Exception $e ) {
                    WP_CLI::warning( "Failed for post {$post->ID}: " . $e->getMessage() );
                }
            }

            WP_CLI::success( "Generated {$count} shortlinks." );
        }
    }

    WP_CLI::add_command( 'aiols generate', [ 'AIOLS_CLI', 'generate' ] );
}
