<?php

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

/**
 *
 * WARNING: Please do not edit this file in any way
 *
 * load the theme function files
 */


// ** install customize widgets ** //

if ( function_exists('register_sidebar') )
  	register_sidebar(array(
    	'name' => '葉展昀模組',
    	'before_widget' => '<div class="widget-title">',
    	'after_widget' => '</div>',
    	'before_title' => '<h6>',
    	'after_title' => '</h6>',
  	)
);

if ( function_exists('register_sidebar') )
  	register_sidebar(array(
    	'name' => '蘇敬博模組',
    	'before_widget' => '<div class="widget-title">',
    	'after_widget' => '</div>',
    	'before_title' => '<h6>',
    	'after_title' => '</h6>',
  	)
);

if ( function_exists('register_sidebar') )
  	register_sidebar(array(
    	'name' => '傅宗玉模組',
    	'before_widget' => '<div class="widget-title">',
    	'after_widget' => '</div>',
    	'before_title' => '<h6>',
    	'after_title' => '</h6>',
  	)
);

if ( function_exists('register_sidebar') )
  	register_sidebar(array(
    	'name' => '蘇煒翔模組',
    	'before_widget' => '<div class="widget-title">',
    	'after_widget' => '</div>',
    	'before_title' => '<h6>',
    	'after_title' => '</h6>',
  	)
);

register_sidebar(array(
	'name' => __('Home Widget 4', 'responsive'),
	'description' => __('Area 12 - sidebar-home.php', 'responsive'),
	'id' => 'home-widget-4',
	'before_title' => '<div id="widget-title-three" class="widget-title-home"><h3>',
	'after_title' => '</h3></div>',
	'before_widget' => '<div id="%1$s" class="widget-wrapper %2$s">',
	'after_widget' => '</div>'
));

// ** Customize different thumbnail crops ** //

if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
    add_image_size( 'allpost-thumb', 100, 100, true ); // Hard Crop Mode2
	add_image_size( 'random-thumb', 369, 294, true ); // Soft Crop Mode3
	add_image_size( 'banner-thumb', 1280, 800, true ); // Unlimited Height Mode
}

// ** Customize excerpt length ** //

function wpe_excerptlength_category( $length ) {
    return 300;
}
function wpe_excerptlength_banner( $length ) {  
    return 120;
}

function wpe_excerptlength_random( $length ) {  
    return 75;
}

function wpe_excerptlength_search( $length ) {  
    return 400;
}

function wpe_excerptmore( $more ) {
    return '...';
}

function wpe_excerpt( $length_callback = '', $more_callback = '' ) {

    if ( function_exists( $length_callback ) ) {
        add_filter( 'excerpt_length', $length_callback);
	}

    if ( function_exists( $more_callback ) ) {
		remove_filter( 'the_content', 'responsive_auto_excerpt_more' );
        add_filter( 'excerpt_more', $more_callback);
	}

    $output = get_the_excerpt();
    $output = apply_filters( 'wptexturize', $output );
    $output = apply_filters( 'convert_chars', $output );
	$output = '<p>' . $output . '</p>'; // maybe wpautop( $foo, $br )
    echo $output;
}

// ** Customize tag cloud for tag fog ** //

function my_widget_tag_cloud_args( $args ) {
	$args['largest'] = 10;
	$args['smallest'] = 2;
	return $args;
}

add_filter( 'widget_tag_cloud_args', 'my_widget_tag_cloud_args' );


function my_wp_generate_tag_cloud( $return, $tags, $args = '' ) {

	extract( $args, EXTR_SKIP );


	// Juggle topic count tooltips:
	if ( isset( $args['topic_count_text'] ) ) {
		// First look for nooped plural support via topic_count_text.
		$translate_nooped_plural = $args['topic_count_text'];
	} elseif ( ! empty( $args['topic_count_text_callback'] ) ) {
		// Look for the alternative callback style. Ignore the previous default.
		if ( $args['topic_count_text_callback'] === 'default_topic_count_text' ) {
			$translate_nooped_plural = _n_noop( '%s topic', '%s topics' );
		} else {
			$translate_nooped_plural = false;
		}
	} elseif ( isset( $args['single_text'] ) && isset( $args['multiple_text'] ) ) {
		// If no callback exists, look for the old-style single_text and multiple_text arguments.
		$translate_nooped_plural = _n_noop( $args['single_text'], $args['multiple_text'] );
	} else {
		// This is the default for when no callback, plural, or argument is passed in.
		$translate_nooped_plural = _n_noop( '%s topic', '%s topics' );
	}
	$counts = array();
	$real_counts = array(); // For the alt tag
	foreach ( (array) $tags as $key => $tag ) {
		$real_counts[ $key ] = $tag->count;
		$counts[ $key ] = $topic_count_scale_callback($tag->count);
	}


	$min_count = min( $counts );
	$spread = max( $counts ) - $min_count;
	if ( $spread <= 0 )
		$spread = 1;
	$font_spread = $largest - $smallest;
	if ( $font_spread < 0 )
		$font_spread = 1;
	$font_step = $font_spread / $spread;


	foreach ( $tags as $key => $tag ) {
		$count = $counts[ $key ];
		$real_count = $real_counts[ $key ];
		$tag_link = '#' != $tag->link ? esc_url( $tag->link ) : '#';
		$tag_id = isset($tags[ $key ]->id) ? $tags[ $key ]->id : $key;
		$tag_name = $tags[ $key ]->name;

		if ( $translate_nooped_plural ) {
			$title_attribute = sprintf( translate_nooped_plural( $translate_nooped_plural, $real_count ), number_format_i18n( $real_count ) );
		} else {
			$title_attribute = call_user_func( $topic_count_text_callback, $real_count, $tag, $args );
		}

		$a[] = "<a href='$tag_link' class='tag-link-$tag_id' title='" . esc_attr( $title_attribute ) . "' style='opacity: " .
			str_replace( ',', '.', ( $smallest + ( ( $count - $min_count ) * $font_step ) ) / $largest )
			. ";'>$tag_name</a>";

	}

	$return = join( $separator, $a );
	return $return;
}


add_filter( 'wp_generate_tag_cloud', 'my_wp_generate_tag_cloud', null , 3 );

require ( get_template_directory() . '/includes/functions.php' );
require ( get_template_directory() . '/includes/theme-options.php' );
require ( get_template_directory() . '/includes/hooks.php' );
require ( get_template_directory() . '/includes/version.php' );
