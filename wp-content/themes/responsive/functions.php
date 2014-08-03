<?php

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

/**
 *
 * WARNING: Please do not edit this file in any way
 *
 * load the theme function files
 */



// ** Add short code to show all avatars ** //

function dq_all_avatar(){
  	global $wpua_functions;
	$blogusers = get_users( array('orderby' => 'post_count', 'order' => 'DSC', 'role' => 'author') );
	$output = '<i class="nav previous icon-arrow-left-2"></i>';
	foreach ( $blogusers as $index=> $user ) {
  		$output.= '<div class="author-avatar"><div class="author-img">' . $wpua_functions->get_wp_user_avatar($user->user_email, '225');
		$user_info = '<div id="author-links">';
 	    $user_info .= '<div class="widget-title-home"><h3><a href="' . esc_url(get_bloginfo('url') . '/?author=' . $user->ID) . '">所有文章';	
		$user_info .= '<span>(' . count_user_posts( $user->ID ) . ' Articles)</span></a></h3></div>';
 	    $user_info .= '<div class="widget-title-home"><h3><a href="' . esc_url(get_bloginfo('url') . '/作者群/' . $user->data->user_nicename) . '">Prologue</a></h3></div>';
		
		$menu = wp_nav_menu(array(
			'container'       => '',
			'fallback_cb'	  =>  false,
			'menu_class'      => 'category-menu',
			'theme_location'  => 'category-menu',
			'author_id' => $user->ID,
			'echo' => false)
		);
		$menu = preg_replace('/<ul.*?><\/ul>/','',$menu);
		$tags = dq_the_tags(array('author_id' => $user->ID));

		$output .= '</div><div class="author-info">' . $user_info;
		$output .= '<div id="category-menu-' . $index . '">' . '<div class="widget-title-home"><h3>' . $user->data->display_name . '的分類文章</h3></div>' . $menu . '</div></div>';
		$output .= '<div id="author-tagcloud" class="tagcloud">' . '<div class="widget-title-home"><h3>' . $user->data->display_name . '的標籤</h3></div>' . $tags . '</div>';
		$output .= '</div></div>';
	}
	$output .= '<i class="nav next icon-arrow-right-2"></i>';
	return $output;
}

function author_get_terms($taxonomy, $r){
	global $wpdb;

	return $wpdb->get_results(
		"SELECT posts.ID, taxonomy.term_id, terms.name,terms.slug, terms.term_group, relationships.term_taxonomy_id,  taxonomy.taxonomy,  taxonomy.description, COUNT(terms.name) AS count

		FROM {$wpdb->prefix}posts AS posts
		JOIN {$wpdb->prefix}term_relationships AS relationships
		JOIN {$wpdb->prefix}term_taxonomy AS taxonomy
		JOIN {$wpdb->prefix}terms AS terms

		WHERE posts.post_author = " . $r['author_id'] . "
		AND posts.post_status = 'publish'
		AND posts.post_type = 'post'
		AND relationships.object_id = posts.ID
		AND relationships.term_taxonomy_id = taxonomy.term_taxonomy_id
		AND taxonomy.taxonomy = '{$taxonomy}'

		AND terms.term_id = taxonomy.term_id
		GROUP BY terms.name
		ORDER BY count DESC"
    );
}

add_shortcode('all_avatar','dq_all_avatar');
add_filter('widget_text', 'do_shortcode');

if ( function_exists('register_sidebar') )
  	register_sidebar(array(
    	'name' => 'Avatar Widgets',
    	'before_widget' => '<div class="widget-avatar">',
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

// ** Set Version for Development css ** //

function css_version( $args ) {
	$args .= '?';
	$args .= date('l jS \of F Y h:i:s A'); 
	return $args;
}

add_filter( 'stylesheet_uri', 'css_version' );


// ** Customize category icons ** //

function dq_category( $args ) {
	$name = $args[0]-> name;
	$category_id = get_cat_ID( $name );
	$category_link = get_category_link( $category_id );
	$icons = array(
		"On the road" => "icon-compass",
		"絕對位置，相對位置" => "icon-location",
		"關於廣告行銷，我只是初學" => "icon-tv",
		"少年Po的奇怪漂流" => "icon-air",
		"小宇宙大爆炸" => "icon-lab",
		"美好生活的簡單提案" => "icon-sun",
		"我家有隻蠹書蟲" => "icon-book",
		"我無意成為影痴，但是" => "icon-camera-3",
		"故事都未完成" => "icon-puzzle",
		"字型甚至是個學問" => "icon-type",
		"客座徵文" => "icon-comments-5"
	);

	return '<a ' . (!is_category() ? 'href="'.$category_link.'"':'')  . 'class="' . $icons[$name] . ' caticon" ' . 'title="' . $name .'">' . '</a>';
}

add_filter('get_the_categories','dq_category');

// ** Customize top-menu navigation bar ** //

class regex_parser {

	private $i, $tag, $pattern, $delimeter;

	public function __construct($pattern = '', $tag = array(), $delimeter = '') {
		$this->pattern = $pattern;
		$this->tag = $tag;
		$this->delimeter = $delimeter;
	}

	public function parse($source) {
		$this->i = 0;
		return preg_replace_callback($this->pattern, array($this, 'on_match'), $source);
	}

	private function on_match($m) {
		// Return what you want the replacement to be.
		$result = $m[1] . $this->delimeter[0] . $this->tag[$this->i] . $this->delimeter[1] . $m[2] . $this->delimeter[2];
		$this->i++;
		return $result;
	}
}

function dq_nav_menu( $items, $args ) {
	if( $args-> menu_class == 'top-menu'){
		$pattern = '/(<a.*?)(>.*<\/a>)/';
		$tag = array('icon-grid', 'icon-list', 'icon-user-2', '', '', '', '', '', 'icon-info', 'icon-search');
		$delimeter = array(' class="','"','');
		$parser = new regex_parser($pattern, $tag, $delimeter);
		return $parser-> parse($items);
	}elseif($args-> menu_class == 'menu-widget'){
		$pattern = '/(<li.*?>)(<a.*?>.*<\/a>)/';
		$tag = array('friends-youwen', 'friends-hun');
		$delimeter = array('<div class="friends" id="','">', '</div>');
		$parser = new regex_parser($pattern, $tag, $delimeter);
		$items = '<div id="friends-container">' . $parser-> parse($items) . '</div>';
		return $items;
	}else{
		return $items;
	}
	}

	add_filter( 'wp_nav_menu', 'dq_nav_menu', null, 2);


	// ** Customize search box ** //

	function dq_search_form( $args ) {
		$args = '<div class="input-control text" id="searchbox">' . $args . '</div>';
		$args = preg_replace('/class="field" /','style="height:32px;"',$args);
		$args = preg_replace('/<input type="submit"(.*) \/>/','<button type="submit"${1}></button>',$args);
		$args = preg_replace('/class="submit"/','class="btn-search"',$args);
		return $args;
	}

	add_filter( 'get_search_form', 'dq_search_form');

	// ** Customize subscribe box ** //

	function dq_subscribe_form( $args ) {
		$args = '<div class="metro">' . $args . '</div>' . '</div>';
		$args = preg_replace('/(Your email:<\/label>)<br \/>/','${1}<div class="input-control text" id="subscribebox">',$args);
		$args = preg_replace('/Enter email address.../','Email address...',$args);
		$args = preg_replace('/onfocus.*}"/','placeholder="Email address..."',$args);
		$args = preg_replace('/(<p>|<\/p>)/','',$args);
		$args = preg_replace('/value="Email address..."/','style="width:74%;"',$args);
		$args = preg_replace('/<input type="submit"(.*?)name="([a-z]*)"(.*?)\/>/','<button type="submit"${1}name="${2}" class="btn-${2}" title="${2}"${3}></button>',$args);
		$args = preg_replace('/btn-subscribe/','${0} icon-feed" style="right:24px',$args);
		$args = preg_replace('/btn-unsubscribe/','${0} icon-stop-2 style="right:2px',$args);
		return $args;
	}

	add_filter('s2_form', 'dq_subscribe_form');

	// ** Customize excerpt length ** //

	function wpe_excerptlength_category( $length ) {
    	return 300;
	}
	function wpe_excerptlength_banner( $length ) {  
    	return 120;
	}

	function wpe_excerptlength_random( $length ) {  
    	return 125;
	}

	function wpe_excerptlength_search( $length ) {  
    	return 300;
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
		$args['number'] = 55;
		if($args['single'] == null)
			$args['order'] = 'RAND';
		return $args;
	}

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

	add_filter( 'widget_tag_cloud_args', 'my_widget_tag_cloud_args' );
	add_filter( 'wp_generate_tag_cloud', 'my_wp_generate_tag_cloud', null , 3 );

	// ** Relate Post Query Generated Function **//

	function relate_query(){
   		global $yarpp, $wp_query, $cache_status;
		$args = array();
		if ($yarpp->get_option('cross_relate')) {
			$args['post_type'] = $yarpp->get_post_types();
		} else {
			$args['post_type'] = array('post');
		}

		if (is_numeric($reference_ID)) {
    		$reference_ID = (int) $reference_ID;
		} else {
    		$reference_ID = get_the_ID();
		}

		if ($the_post = wp_is_post_revision($reference_ID)) $reference_ID = $the_post;

		$yarpp->setup_active_cache($args);

		$options = array(
    		'domain',
    		'limit',
    		'template',
    		'order',
    		'promote_yarpp',
    		'optin'
		);

		extract($yarpp->parse_args($args, $options));

		$cache_status = $yarpp->active_cache->enforce($reference_ID);
		if ($cache_status === YARPP_DONT_RUN) return;
		if ($cache_status !== YARPP_NO_RELATED) $yarpp->active_cache->begin_yarpp_time($reference_ID, $args);

		$yarpp->save_post_context();

		$wp_query = new WP_Query();
		$relatePosts = $wp_query;

		if ($cache_status !== YARPP_NO_RELATED) {
    		$orders = explode(' ', $order);
    		$relatePosts->query(
        		array(
            		'p'         => $reference_ID,
            		'orderby'   => $orders[0],
            		'order'     => $orders[1],
            		'showposts' => $limit,
            		'post_type' => (isset($args['post_type']) ? $args['post_type'] : $yarpp->get_post_types())
        		)
    		);
		}

    	$yarpp->prep_query($yarpp->current_query->is_feed);

    	$relatePosts->posts = apply_filters('yarpp_results', $relatePosts->posts, array(
        	'function'      => 'display_related',
        	'args'          => $args,
        	'related_ID'    => $reference_ID)
    	);

		return $relatePosts;
	}

	function restore_related_resource(){
		global $yarpp, $wp_query, $cache_status;
		unset($wp_query);
 		if ($cache_status === YARPP_NO_RELATED) {
        	// Uh, do nothing. Stay very still.
    	} else {
        	$yarpp->active_cache->end_yarpp_time();
    	}
 		$yarpp->restore_post_context();
	}

	// ** Subscribe2 customization ** //

	function wp_mail_to_smtp(&$phpmailer) {
		$phpmailer->Mailer = 'smtp';
		$phpmailer->SMTPAuth = true;
		$phpmailer->Host = 'smtp.mailgun.org';
		$phpmailer->Port = '25';
		$phpmailer->Username = 'postmaster@dialoquad.net';
		$phpmailer->Password = 'trioplusone';
	}

	if (getenv('OPENSHIFT_APP_NAME') != "") {
		add_action('phpmailer_init', 'wp_mail_to_smtp');
	}

	// ** Customize author post page **//

	function dq_remove_metaboxes(){
		$role = wp_get_current_user()-> roles;
		$target = array('administrator', 'editor');
		if( count(array_intersect($role, $target)) <= 0 ) {
			remove_meta_box('yarpp_relatedposts', 'post', 'normal');
			remove_meta_box('yarpp_relatedposts', 'page', 'normal');
		}
	}

	add_action( 'add_meta_boxes', 'dq_remove_metaboxes', 11 );

	// ** Remove auto-generated password warning ** //

	function remove_default_password_nag() {
		global $user_ID;
		delete_user_setting('default_password_nag', $user_ID);
		update_user_option($user_ID, 'default_password_nag', false, true);
	}
	add_action('admin_init', 'remove_default_password_nag');

	// ** Icons DQ Navigation ** //

	function insert_dq_nav_icons($htmlString){
  		$dom = new DOMDocument;
		$dom->loadHTML(mb_convert_encoding($htmlString, 'HTML-ENTITIES', 'UTF-8'));
		$xPath = new DOMXPath($dom);
		$icons = array(
			"On the road" => "icon-compass",
			"絕對位置，相對位置" => "icon-location",
			"關於廣告行銷，我只是初學" => "icon-tv",
			"少年Po的奇怪漂流" => "icon-air",
			"小宇宙大爆炸" => "icon-lab",
			"美好生活的簡單提案" => "icon-sun",
			"我家有隻蠹書蟲" => "icon-book",
			"我無意成為影痴，但是" => "icon-camera-3",
			"故事都未完成" => "icon-puzzle",
			"字型甚至是個學問" => "icon-type",
			"客座徵文" => "icon-comments-5"
		);
		$nodes = $xPath->query("//*[starts-with(@id,'category-menu')]/ul/li/a");
		foreach($nodes as $key=> $node){
			$icon = $dom->createElement('i');
			$arrow = $dom->createElement('i');
			$icon->setAttribute('class', $icons[$node->nodeValue]);
			$arrow->setAttribute('class', 'dqarrow');
			$node->parentNode->insertBefore($icon, $node);
			$node->parentNode->insertBefore($arrow, $node);
		}
		$dom->formatOutput = true;
		return $dom->saveHTML();
	}

	// ** Dock code ** //

	function get_dq_dock($htmlString){
  		$dom = new DOMDocument;
		$dom->loadHTML('<html><body>'.$htmlString.'</body></html>');
		$xPath = new DOMXPath($dom);
		$nodes = $xPath->query("//*[contains(@class, 'scroll-anchor')]");
		foreach($nodes as $node){
	    	$name = $node->getAttribute('name');
	    	$text = $node->getAttribute('data-text');
			$dock_item .= '<div class="icons-dqnav">' . '<a class="dock-item" href="#' . $name . '"><img src="' . get_stylesheet_directory_uri() . '/icons/' . $name . '.png" alt="' . $text . '" /><span>' . $text . '</span></a>' . '</div>';
		}
		return '<div class="dock" id="dock"><div class="dock-container">' . $dock_item . '</div></div>';
	}

	function my_dq_all_filter($buffer) {
		$buffer = insert_dq_nav_icons($buffer);
		return $buffer . get_dq_dock($buffer);
	}

	function buffer_start() { ob_start("my_dq_all_filter"); }
	function buffer_end() { ob_end_flush(); }

	add_action('wp_head', 'buffer_start');
	add_action('wp_footer', 'buffer_end');


	// ** Customized Category ** //
	function author_id_limit( $items, $args ) {
		if ( empty($args->author_id) )
			return $items;
		foreach ( $items as $key => $item ) {
			if ($item->object == 'post' && $item->post_author != $args->author_id ){
				unset($items[$key]);
			}else{
				$ids[] = $item->menu_item_parent;
				if($item->menu_item_parent)
					$ids[] = $item->ID;
			}
		}
		foreach ( $items as $key => $item ) {
			if ( ! in_array( $item->ID, $ids ) )
				unset($items[$key]);
		}
		return $items;
	}

	function submenu_limit( $items, $args ) {
		if ( empty($args->submenu) )
			return $items;
		$parent_id = array_pop( wp_filter_object_list( $items, array( 'title' => $args->submenu ), 'and', 'ID' ) );
		$children  = submenu_get_children_ids( $parent_id, $items );
		$children[] = $parent_id;
		foreach ( $items as $key => $item ) {
			if ( ! in_array( $item->ID, $children ) )
				unset($items[$key]);
		}
		return $items;
	}

	function submenu_get_children_ids( $id, $items ) {
		$ids = wp_filter_object_list( $items, array( 'menu_item_parent' => $id ), 'and', 'ID' );
		foreach ( $ids as $id ) {
			$ids = array_merge( $ids, submenu_get_children_ids( $id, $items ) );
		}
		return $ids;
	}

	function dq_the_category($name = null){
 		$category =  get_the_category();
		if($name == null){
			$name = preg_replace('/<a.*?title="(.*)".*?<\/a>/','${1}',$category);
		}
		$menu = wp_nav_menu(array(
			'container'       => '',
			'fallback_cb'	  =>  false,
			'menu_class'      => 'category-menu',
			'theme_location'  => 'category-menu',
			'submenu' => $name,
			'echo' => false)
		);

		return '<div id="category-menu">' . $menu . '</div>';
	}

	add_filter( 'wp_nav_menu_objects', 'submenu_limit', 10, 2 );
	add_filter( 'wp_nav_menu_objects', 'author_id_limit', 11, 2 );

	//** Customize single the_tags **//

	function dq_the_tags($args = null){
		$defaults = array(
			'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 45, 'single' => true,
			'format' => 'flat', 'separator' => "\n", 'orderby' => 'name', 'order' => 'ASC',
			'exclude' => '', 'include' => '', 'link' => 'view', 'taxonomy' => 'post_tag', 'post_type' => '', 'echo' => true
		);
		$args = wp_parse_args( $args, $defaults );

		if( empty($args['author_id']) ){
			$terms = get_the_terms( 0, $args['taxonomy'] );
		}else{
			$terms = author_get_terms($args['taxonomy'],$args);
		}

		$tags = array();
		foreach ( $terms as $key => $term )
			$tags[ $key ] = $term;

		if ( empty( $tags ) || is_wp_error( $tags ) )
			return;

		foreach ( $tags as $key => $tag ) {
			if ( 'edit' == $args['link'] )
				$link = get_edit_term_link( $tag->term_id, $tag->taxonomy, $args['post_type'] );
			else
				$link = get_term_link( intval($tag->term_id), $tag->taxonomy );
			if ( is_wp_error( $link ) )
				return false;

			$tags[ $key ]->link = $link;
			$tags[ $key ]->id = $tag->term_id;
		}

		$return = wp_generate_tag_cloud( $tags, $args ); // Here's where those top tags get sorted according to $args

		/**
	 	 * Filter the tag cloud output.
	 	 *
	 	 * @since 2.3.0
	 	 *
	 	 * @param string $return HTML output of the tag cloud.
	 	 * @param array  $args   An array of tag cloud arguments.
	 	 */
		$return = apply_filters( 'wp_tag_cloud', $return, $args );

		if ( 'array' == $args['format'] || empty($args['echo']) )
			return $return;

		return $return;
	}

	// ** Tag Cloud shortcode ** //
	function dq_tagcloud_shortcode($atts)
	{
		$out .= wp_tag_cloud(array('number' => 0, 'echo' => false));
		return $out;
	}
	add_shortcode('tagcloud','dq_tagcloud_shortcode');

	// ** Default template settings ** //

	require ( get_template_directory() . '/includes/functions.php' );
	require ( get_template_directory() . '/includes/theme-options.php' );
	require ( get_template_directory() . '/includes/hooks.php' );
	require ( get_template_directory() . '/includes/version.php' );

