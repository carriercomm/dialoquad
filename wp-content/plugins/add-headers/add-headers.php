<?php
/*
Plugin Name: Add Headers
Plugin URI: https://bitbucket.org/gnotaras/wordpress-add-headers
Description: Adds the ETag, Last-Modified, Expires and Cache-Control headers to HTTP responses generated by WordPress to facilitate caching.
Version: 1.0.5
Author: George Notaras
Author URI: http://www.g-loaded.eu/
License: GPLv3
*/

/**
 *  This file is part of the Add-Headers distribution package.
 *
 *  Add-Headers is an extension for the WordPress publishing platform.
 *
 *  Homepage:
 *  - http://wordpress.org/plugins/add-headers/
 *  Documentation:
 *  - http://www.codetrax.org/projects/wp-add-headers/wiki
 *  Development Web Site and Bug Tracker:
 *  - http://www.codetrax.org/projects/wp-add-headers
 *  Main Source Code Repository (Mercurial):
 *  - https://bitbucket.org/gnotaras/wordpress-add-headers
 *  Mirror repository (Git):
 *  - https://github.com/gnotaras/wordpress-add-headers
 *
 *  Licensing Information
 *
 *  Copyright 2013 George Notaras <gnot@g-loaded.eu>, CodeTRAX.org
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


// Store plugin directory
define('ADDH_DIR', dirname(__FILE__));

// Import modules
// require_once( join( DIRECTORY_SEPARATOR, array( ADDH_DIR, 'addh-settings.php' ) ) );


/**
 * Translation Domain
 *
 * Translation files are searched in: wp-content/plugins
 */
//load_plugin_textdomain('add-headers', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');


// Send headers to client
function addh_send_headers( $headers_arr ) {
    foreach ( $headers_arr as $header_data ) {
        $header_data = trim($header_data);
        if ( ! empty($header_data) ) {
            header( $header_data );
        }
    }
}


// ETag
function addh_generate_etag_header( $post, $mtime, $options ) {
    global $wp;
    if ( $options['add_etag_header'] === true ) {
        $to_hash = array( $mtime, $post->post_date_gmt, $post->guid, $post->ID, serialize( $wp->query_vars ) );
        $header_etag_value = sha1( serialize( $to_hash ) );
        // Generate a weak or strong ETag
        if ( $options['generate_weak_etag'] === true ) {
            return sprintf( 'ETag: W/"%s"', $header_etag_value );
        } else {
            return sprintf( 'ETag: "%s"', $header_etag_value );
        }
    }
}


// Last-Modified
function addh_generate_last_modified_header( $post, $mtime, $options ) {
    if ( $options['add_last_modified_header'] === true ) {
        $header_last_modified_value = str_replace( '+0000', 'GMT', gmdate('r', $mtime) );
        return 'Last-Modified: ' . $header_last_modified_value;
    }
}


// Expires (Calculated from client access time, aka current time)
function addh_generate_expires_header( $post, $mtime, $options ) {
    if ( $options['add_expires_header'] === true ) {
        // See also:  $current_time_gmt = (int) gmdate('U');
        $header_expires_value = str_replace( '+0000', 'GMT', gmdate('r', time() + $options['cache_max_age_seconds'] ) );
        return 'Expires: ' . $header_expires_value;
    }
}


// Cache-Control
function addh_generate_cache_control_header( $post, $mtime, $options ) {
    if ( $options['add_cache_control_header'] === true ) {
        if ( intval($options['cache_max_age_seconds']) > 0 ) {
            $default_cache_control_template = 'public, max-age=%s';
            $cache_control_template = apply_filters( 'addh_cache_control_header_format', $default_cache_control_template );
            $header_cache_control_value = sprintf( $cache_control_template, $options['cache_max_age_seconds'] );
            return 'Cache-Control: ' . $header_cache_control_value;
        } else {
            return 'Cache-Control: no-cache, must-revalidate, max-age=0';
        }
    }
}


/**
 * Generates headers in batch
 */
function addh_batch_generate_headers( $post, $mtime, $options ) {
    global $wp;

    $headers_arr = array();

    // ETag
    $headers_arr[] = addh_generate_etag_header( $post, $mtime, $options );
    // Last-Modified
    $headers_arr[] = addh_generate_last_modified_header( $post, $mtime, $options );
    // Expires (Calculated from client access time, aka current time)
    $headers_arr[] = addh_generate_expires_header( $post, $mtime, $options );
    // Cache-Control
    $headers_arr[] = addh_generate_cache_control_header( $post, $mtime, $options );
    // Allow filtering of the generated headers
    $headers_arr = apply_filters( 'addh_headers', $headers_arr );

    // Send headers
    addh_send_headers( $headers_arr );
}



/**
 * Sets headers on post object pages (posts, pages, attachments custom
 * post types).
 *
 * In order to calculate the modified time, two time sources are used:
 *   1) the post object's modified time.
 *   2) the modified time of the most recent comment that is attached to the post object.
 * The most "recent" timestamp of the two is returned.
 */
function addh_set_headers_for_object( $options ) {

    // Get current queried object.
    $post = get_queried_object();
    // Valid post types: post, page, attachment
    if ( ! is_object($post) || ! isset($post->post_type) || ! in_array( get_post_type($post), array('post', 'page', 'attachment') ) ) {
        return;
    }

    // TODO: check for password protected posts

    // Retrieve stored time of post object
    $post_mtime = $post->post_modified_gmt;
    $post_mtime_unix = strtotime( $post_mtime );

    // Initially set the $mtime to the post mtime timestamp
    $mtime = $post_mtime_unix;

    // If there are comments attached to this post object, find the mtime of
    // the most recent comment.
    if ( intval($post->comment_count) > 0 ) {

        // Retrieve the mtime of the most recent comment
        $comments = get_comments( array(
            'status' => 'approve',
            'orderby' => 'comment_date_gmt',
            'number' => '1',
            'post_id' => $post->ID
        ) );
        if ( ! empty($comments) ) {
            $comment = $comments[0];
            $comment_mtime = $comment->comment_date_gmt;
            $comment_mtime_unix = strtotime( $comment_mtime );
            // Compare the two mtimes and keep the most recent (higher) one.
            if ( $comment_mtime_unix > $post_mtime_unix ) {
                $mtime = $comment_mtime_unix;
            }
        }
    }

    addh_batch_generate_headers( $post, $mtime, $options );
}


/**
 * Sets headers on archives
 */
function addh_set_headers_for_archive( $options ) {

    // On archives, the global post object is the first post of the list.
    // So, we use this to set the headers for the archive.
    // There is no need to check for pagination, since every page of the archive
    // has different posts.
    global $post;
    // Valid post types: post
    if ( ! is_object($post) || ! isset($post->post_type) || ! in_array( get_post_type($post), array('post') ) ) {
        return;
    }

    // Retrieve stored time of post object
    $post_mtime = $post->post_modified_gmt;
    $mtime = strtotime( $post_mtime );

    addh_batch_generate_headers( $post, $mtime, $options );
}


/**
 * Sets headers on the main feed and main comments feed.
 *
 * Note: At the time of writing, WordPress 3.8 feeds already have ETag and
 * Last-Modified headers. Here we add Expires and Cache-Control.
 *
 */
function addh_set_headers_for_feed( $options ) {
    $headers_arr = array();

    // Expires (Calculated from client access time, aka current time)
    $headers_arr[] = addh_generate_expires_header( $post, $mtime, $options );
    // Cache-Control
    $headers_arr[] = addh_generate_cache_control_header( $post, $mtime, $options );

    // Allow filtering of the generated headers
    $headers_arr = apply_filters( 'addh_headers_feed', $headers_arr );

    // Send headers
    addh_send_headers( $headers_arr );
}


/**
 * Main function.
 */
function addh_headers( $buffer ){
    
    // Options
    $default_options = array(
        'add_etag_header' => true,
        'generate_weak_etag' => false,
        'add_last_modified_header' => true,
        'add_expires_header' => true,
        'add_cache_control_header' => true,
        'cache_max_age_seconds' => 86400,
        'cache_max_age_seconds_for_search_results' => 0,
    );
    $options = apply_filters( 'addh_options', $default_options );

    // Feeds
    if ( is_feed() ) {
        addh_set_headers_for_feed( $options );
    }

    // Adds headers to:
    // - Post objects (posts, pages, attachments, custom post types)
    // - Static front page
    elseif ( is_singular() ) {
        addh_set_headers_for_object( $options );
    }
    
    // Adds headers to:
    // - Category, tag, author, date based archives
    // - Search results
    // - Default front page displaying the latest posts
    // - Static page displaying the latest posts
    elseif ( is_archive() || is_search() || is_home() ) {
        if ( is_search() ) {
            $options['cache_max_age_seconds'] = $options['cache_max_age_seconds_for_search_results'];
        }
        addh_set_headers_for_archive( $options );
    }

    return $buffer;
}


// See this page for what this workaround is about:
// http://stackoverflow.com/questions/12608881/wordpress-redirect-issue-headers-already-sent
// Possibly related:
// http://wordpress.stackexchange.com/questions/16547/wordpress-plugin-development-headers-already-sent-message
// http://stackoverflow.com/questions/8677901/cannot-modify-header-information-with-mail-and-header-php-with-ob-start
// How WP boots: http://theme.fm/2011/10/wordpress-internals-how-wordpress-boots-up-part-3-2673/
function addh_add_ob_start(){
    ob_start('addh_headers');
}
function addh_flush_ob_end(){
    ob_end_flush();
}
add_action('init', 'addh_add_ob_start');
add_action('wp', 'addh_flush_ob_end');

?>