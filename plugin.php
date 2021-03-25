<?php
/**
 * Plugin Name: WP Stateless Bucket Link Filter
 * Description: A must-use plugin enabling filtering the WP Stateless bucket link with a PHP constant.
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
    // Ensure no trailing forward slash.
    $new_bucket_url = rtrim( WP_STATELESS_BUCKET_LINK_REPLACE, '/' );

    // Filter the Stateless' Google Storage host.
    add_filter( 'get_gs_host', function() use ( $new_bucket_url ) {
        return $new_bucket_url;
    }, 11, 0 );

    // Filter URL to point back to Google Storage when loading images for edit.
    add_filter( 'load_image_to_edit_attachmenturl', function( $attachment_url ) use ( $new_bucket_url ) {
        if ( ! function_exists( 'ud_get_stateless_media' ) ) {
            return $attachment_url;
        }

        $bucket_name = \ud_get_stateless_media()->get( 'sm.bucket' );
        $bucket_url  = 'https://storage.googleapis.com/' . $bucket_name;

        $replaced = str_replace( $new_bucket_url, $bucket_url, $attachment_url );

        return $replaced;
    }, 11, 3 );
}
