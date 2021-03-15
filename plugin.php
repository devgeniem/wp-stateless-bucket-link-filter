<?php
/**
 * Plugin Name: WP Stateless Bucket Link Filter
 * Description: Plugin which enables filtering the WP Stateless bucket link with a PHP constant.
 * Version: 1.0.0
 * Plugin URI: https://github.com/devgeniem/wp-stateless-bucket-name-filter
 * Author: Ville Siltala / Geniem Oy
 * Author URI: https://github.com/devgeniem
 * License: GPLv3
 */

namespace Geniem;

/**
 * Activate plugin features if the replacement is set.
 */
if ( defined( 'WP_STATELESS_BUCKET_LINK_REPLACE' ) ) {

    // Remove the image size threshold. This feature breaks image processing in WP Stateless.
    \add_filter( 'big_image_size_threshold', '__return_false' );

    // Make the bucket links point to the the defined URL.
    add_filter( 'wp_stateless_bucket_link', function( $link ) {
        if (
            function_exists( 'ud_get_stateless_media' )
            && function_exists( 'is_admin' )
            && !\is_admin()
            && $_SERVER['REQUEST_METHOD'] === 'GET'
        ) {
            // Get the bucket name.
            $bucket_name = \ud_get_stateless_media()->get( 'sm.bucket' );
            // This is the default link.
            $default = 'https://storage.googleapis.com/' . $bucket_name;
            // Get the constant and remove an unwanted trailing slash.
            $replace = rtrim( WP_STATELESS_BUCKET_LINK_REPLACE, '/' );
            return str_replace( $default, $replace, $link );
        }
        return $link;
    } );

    // While on admin side, filter all attachment URLs to point back to the bucket.
    if ( is_admin() ) {
        add_filter( 'wp_get_attachment_url', function( $url ) {
            // If filtered URL is set when retrieving images to edit, replace back to bucket url.
            if (
                function_exists( 'ud_get_stateless_media' )
                && strpos( $url, WP_STATELESS_BUCKET_LINK_REPLACE ) !== false )
            {
                // Get the bucket name.
                $bucket_name = \ud_get_stateless_media()->get( 'sm.bucket' );
                // This is the bucket's url.
                $bucket_url = 'https://storage.googleapis.com/' . $bucket_name;
                // Image urls will be filtered to point to this url.
                $filtered = rtrim( WP_STATELESS_BUCKET_LINK_REPLACE, '/' );

                // Replace the filtered url with the bucket url.
                return str_replace( $filtered, $bucket_url, $url );
            }

            return $url;
        }, 1, 1 );
    }
}
