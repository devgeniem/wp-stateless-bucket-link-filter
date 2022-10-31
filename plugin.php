<?php
/**
 * Plugin Name: WP Stateless Bucket Link Filter
 * Description: A must-use plugin enabling filtering the WP Stateless bucket link with a PHP constant.
 * Version: 1.2.0
 * Plugin URI: https://github.com/devgeniem/wp-stateless-bucket-name-filter
 * Author: Ville Siltala & Miika Arponen / Geniem Oy
 * Author URI: https://github.com/devgeniem
 * License: GPLv3
 */

namespace Geniem;

/**
 * Activate plugin features if the replacement is set.
 */
if ( defined( 'WP_STATELESS_BUCKET_LINK_REPLACE' ) ) {
    class StatelessBucketLinkFilter {
        public static function init() {
            // Filter the Stateless' Google Storage host.
            add_filter( 'get_gs_host', [ __CLASS__, 'get_gs_host' ], 11, 0 );

            add_filter( 'load_image_to_edit_attachmenturl', [ __CLASS__, 'load_image_to_edit_attachment_url' ], 11, 1 );
        }

        /**
         * Return the constant for the GS host
         *
         * @return string
         */
        public static function get_gs_host() {
            // Ensure no trailing forward slash.
            return rtrim( \WP_STATELESS_BUCKET_LINK_REPLACE, '/' );
        }

        /**
         * Filter URL to point back to Google Storage when loading images for edit.
         *
         * @param string $attachment_url The attachment url.
         * @return string
         */
        public static function load_image_to_edit_attachment_url( $attachment_url ) {
            if ( ! function_exists( 'ud_get_stateless_media' ) ) {
                return $attachment_url;
            }

            $bucket_name = \ud_get_stateless_media()->get( 'sm.bucket' );
            $bucket_url  = 'https://storage.googleapis.com/' . $bucket_name;

            $replaced = str_replace(
                rtrim( \WP_STATELESS_BUCKET_LINK_REPLACE, '/' ),
                $bucket_url,
                $attachment_url
            );

            return $replaced;
        }
    }

    StatelessBucketLinkFilter::init();
}
