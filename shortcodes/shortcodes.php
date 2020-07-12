<?php
/**
 * ThemeREX Shortcodes
*/


// ---------------------------------- [trx_accordion] ---------------------------------------

add_shortcode('trx_accordion', 'themerex_sc_accordion');

/*
[trx_accordion style="1" counter="off" initial="1"]
	[trx_accordion_item title="Accordion Title 1"]Lorem ipsum dolor sit amet, consectetur adipisicing elit[/trx_accordion_item]
	[trx_accordion_item title="Accordion Title 2"]Proin dignissim commodo magna at luctus. Nam molestie justo augue, nec eleifend urna laoreet non.[/trx_accordion_item]
	[trx_accordion_item title="Accordion Title 3 with custom icons" icon_closed="icon-check-2" icon_opened="icon-delete-2"]Curabitur tristique tempus arcu a placerat.[/trx_accordion_item]
[/trx_accordion]
*/
function themerex_sc_accordion($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"style" => "1",
		"initial" => "1",
		"counter" => "off",
		"icon_closed" => "icon-plus-2",
		"icon_opened" => "icon-minus-2",
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
		"animation" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
	$style = max(1, min(2, $style));
	$initial = max(0, (int) $initial);
	global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_accordion_counter'] = 0;
	$THEMEREX_GLOBALS['sc_accordion_show_counter'] = themerex_sc_param_is_on($counter);
	$THEMEREX_GLOBALS['sc_accordion_icon_closed'] = empty($icon_closed) || themerex_sc_param_is_inherit($icon_closed) ? "icon-plus-2" : $icon_closed;
	$THEMEREX_GLOBALS['sc_accordion_icon_opened'] = empty($icon_opened) || themerex_sc_param_is_inherit($icon_opened) ? "icon-minus-2" : $icon_opened;
	wp_enqueue_script('jquery-ui-accordion', false, array('jquery','jquery-ui-core'), null, true);
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_accordion sc_accordion_style_'.esc_attr($style)
				. (!empty($class) ? ' '.esc_attr($class) : '')
				. (themerex_sc_param_is_on($counter) ? ' sc_show_counter' : '') 
			. '"'
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
			. ' data-active="' . ($initial-1) . '"'
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. '>'
			. do_shortcode($content)
			. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_accordion', $atts, $content);
}


add_shortcode('trx_accordion_item', 'themerex_sc_accordion_item');

function themerex_sc_accordion_item($atts, $content=null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	extract(themerex_sc_html_decode(shortcode_atts( array(
		// Individual params
		"icon_closed" => "",
		"icon_opened" => "",
		"title" => "",
		// Common params
		"id" => "",
		"class" => "",
		"css" => ""
	), $atts)));
	global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_accordion_counter']++;
	if (empty($icon_closed) || themerex_sc_param_is_inherit($icon_closed)) $icon_closed = $THEMEREX_GLOBALS['sc_accordion_icon_closed'] ? $THEMEREX_GLOBALS['sc_accordion_icon_closed'] : "icon-plus-2";
	if (empty($icon_opened) || themerex_sc_param_is_inherit($icon_opened)) $icon_opened = $THEMEREX_GLOBALS['sc_accordion_icon_opened'] ? $THEMEREX_GLOBALS['sc_accordion_icon_opened'] : "icon-minus-2";
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_accordion_item' 
			. (!empty($class) ? ' '.esc_attr($class) : '')
			. ($THEMEREX_GLOBALS['sc_accordion_counter'] % 2 == 1 ? ' odd' : ' even') 
			. ($THEMEREX_GLOBALS['sc_accordion_counter'] == 1 ? ' first' : '') 
			. '">'
			. '<h5 class="sc_accordion_title">'
			. (!themerex_sc_param_is_off($icon_closed) ? '<span class="sc_accordion_icon sc_accordion_icon_closed '.esc_attr($icon_closed).'"></span>' : '')
			. (!themerex_sc_param_is_off($icon_opened) ? '<span class="sc_accordion_icon sc_accordion_icon_opened '.esc_attr($icon_opened).'"></span>' : '')
			. ($THEMEREX_GLOBALS['sc_accordion_show_counter'] ? '<span class="sc_items_counter">'.($THEMEREX_GLOBALS['sc_accordion_counter']).'</span>' : '')
			. ($title)
			. '</h5>'
			. '<div class="sc_accordion_content"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				. '>'
				. do_shortcode($content) 
			. '</div>'
			. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_accordion_item', $atts, $content);
}

// ---------------------------------- [/trx_accordion] ---------------------------------------






// ---------------------------------- [trx_anchor] ---------------------------------------

add_shortcode("trx_anchor", "themerex_sc_anchor");
						
/*
[trx_anchor id="unique_id" description="Anchor description" title="Short Caption" icon="icon-class"]
*/

function themerex_sc_anchor($atts, $content = null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"title" => "",
		"description" => '',
		"icon" => '',
		"url" => "",
		"separator" => "no",
		// Common params
		"id" => ""
    ), $atts)));
	$output = $id 
		? '<a name="'.esc_attr($id).'" id="'.esc_attr($id).'"'
			. ' class="sc_anchor"' 
			. ' title="' . ($title ? esc_attr($title) : '') . '"'
			. ' data-description="' . ($description ? esc_attr(str_replace(array("{", "}", "|"), array("<i>", "</i>", "<br>"), $description))   : ''). '"'
			. ' data-icon="' . ($icon ? $icon : '') . '"' 
			. ' data-url="' . ($url ? esc_attr($url) : '') . '"' 
			. ' data-separator="' . (themerex_sc_param_is_on($separator) ? 'yes' : 'no') . '"'
			. '></a>'
		: '';
	return apply_filters('themerex_shortcode_output', $output, 'trx_anchor', $atts, $content);
}
// ---------------------------------- [/trx_anchor] ---------------------------------------





// ---------------------------------- [trx_audio] ---------------------------------------

add_shortcode("trx_audio", "themerex_sc_audio");

/*
[trx_audio url="http://education.themerex.dnw/wp-content/uploads/2014/12/Dream-Music-Relax.mp3" image="http://education.themerex.dnw/wp-content/uploads/2014/10/post_audio.jpg" title="Insert Audio Title Here" author="Lily Hunter" controls="show" autoplay="off"]
*/

function themerex_sc_audio($atts, $content = null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"title" => "",
		"author" => "",
		"image" => "",
		"mp3" => '',
		"wav" => '',
		"src" => '',
		"url" => '',
		"align" => '',
		"controls" => "",
		"autoplay" => "",
		"frame" => "on",
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
		"animation" => "",
		"width" => '',
		"height" => '',
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	if ($src=='' && $url=='' && isset($atts[0])) {
		$src = $atts[0];
	}
	if ($src=='') {
		if ($url) $src = $url;
		else if ($mp3) $src = $mp3;
		else if ($wav) $src = $wav;
	}
	if ($image > 0) {
		$attach = wp_get_attachment_image_src( $image, 'full' );
		if (isset($attach[0]) && $attach[0]!='')
			$image = $attach[0];
	}
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
	$data = ($title != ''  ? ' data-title="'.esc_attr($title).'"'   : '')
			. ($author != '' ? ' data-author="'.esc_attr($author).'"' : '')
			. ($image != ''  ? ' data-image="'.esc_url($image).'"'   : '')
			. ($align && $align!='none' ? ' data-align="'.esc_attr($align).'"' : '')
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '');
	$audio = '<audio'
		. ($id ? ' id="'.esc_attr($id).'"' : '')
		. ' class="sc_audio' . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
		. ' src="'.esc_url($src).'"'
		. (themerex_sc_param_is_on($controls) ? ' controls="controls"' : '')
		. (themerex_sc_param_is_on($autoplay) && is_single() ? ' autoplay="autoplay"' : '')
		. ' width="'.esc_attr($width).'" height="'.esc_attr($height).'"'
		. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
		. ($data)
		. '></audio>';
	if ( themerex_get_custom_option('substitute_audio')=='no') {
		if (themerex_sc_param_is_on($frame)) $audio = themerex_get_audio_frame($audio, $image, $s);
	} else {
		if ((isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') && (isset($_POST['action']) && $_POST['action']=='vc_load_shortcode')) {
			$audio = themerex_substitute_audio($audio, false);
		}
	}
	if (themerex_get_theme_option('use_mediaelement')=='yes')
		wp_enqueue_script('wp-mediaelement');
	return apply_filters('themerex_shortcode_output', $audio, 'trx_audio', $atts, $content);
}
// ---------------------------------- [/trx_audio] ---------------------------------------





// ---------------------------------- [trx_blogger] ---------------------------------------

add_shortcode('trx_blogger', 'themerex_sc_blogger');

/*
[trx_blogger id="unique_id" ids="comma_separated_list" cat="id|slug" orderby="date|views|comments" order="asc|desc" count="5" descr="0" dir="horizontal|vertical" style="regular|date|image_large|image_medium|image_small|accordion|list" border="0"]
*/
global $THEMEREX_GLOBALS;
$THEMEREX_GLOBALS['sc_blogger_busy'] = false;

function themerex_sc_blogger($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger(true)) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"style" => "regular",
		"filters" => "no",
		"post_type" => "post",
		"ids" => "",
		"cat" => "",
		"count" => "3",
		"columns" => "",
		"offset" => "",
		"orderby" => "date",
		"order" => "asc",
		"only" => "no",
		"descr" => "",
		"readmore" => "",
		"loadmore" => "no",
		"location" => "default",
		"dir" => "horizontal",
		"hover" => themerex_get_theme_option('hover_style'),
		"hover_dir" => themerex_get_theme_option('hover_dir'),
		"scroll" => "no",
		"controls" => "no",
		"rating" => "no",
		"info" => "yes",
		"links" => "yes",
		"date_format" => "",
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
		"animation" => "",
		"width" => "",
		"height" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));

	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left, $width, $height);
	$width  = themerex_prepare_css_value($width);
	$height = themerex_prepare_css_value($height);
	
	global $post, $THEMEREX_GLOBALS;

	$THEMEREX_GLOBALS['sc_blogger_busy'] = true;
	$THEMEREX_GLOBALS['sc_blogger_counter'] = 0;

	if (empty($id)) $id = "sc_blogger_".str_replace('.', '', mt_rand());
	
	if ($style=='date' && empty($date_format)) $date_format = 'd.m+Y';

	if (!empty($ids)) {
		$posts = explode(',', str_replace(' ', '', $ids));
		$count = count($posts);
	}
	
	if ($descr == '') $descr = themerex_get_custom_option('post_excerpt_maxlength'.($columns > 1 ? '_masonry' : ''));

	if (!themerex_sc_param_is_off($scroll)) {
		themerex_enqueue_slider();
		if (empty($id)) $id = 'sc_blogger_'.str_replace('.', '', mt_rand());
	}
	
	$class = apply_filters('themerex_filter_blog_class',
				'sc_blogger'
				. ' layout_'.esc_attr($style)
				. ' template_'.esc_attr(themerex_get_template_name($style))
				. (!empty($class) ? ' '.esc_attr($class) : '')
				. ' ' . esc_attr(themerex_get_template_property($style, 'container_classes'))
				. ' sc_blogger_' . ($dir=='vertical' ? 'vertical' : 'horizontal')
				. (themerex_sc_param_is_on($scroll) && themerex_sc_param_is_on($controls) ? ' sc_scroll_controls sc_scroll_controls_type_top sc_scroll_controls_'.esc_attr($dir) : '')
				. ($descr == 0 ? ' no_description' : ''),
				array('style'=>$style, 'dir'=>$dir, 'descr'=>$descr)
	);

	$container = apply_filters('themerex_filter_blog_container', themerex_get_template_property($style, 'container'), array('style'=>$style, 'dir'=>$dir));
	$container_start = $container_end = '';
	if (!empty($container)) {
		$container = explode('%s', $container);
		$container_start = !empty($container[0]) ? $container[0] : '';
		$container_end = !empty($container[1]) ? $container[1] : '';
	}

	$output = ($style=='list' ? '<ul' : '<div')
			. ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="'.esc_attr($class).'"'
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
		. '>'
		. ($container_start)
		. ($dir=='horizontal' && $columns > 1 && themerex_get_template_property($style, 'need_columns') ? '<div class="columns_wrap">' : '')
		. (themerex_sc_param_is_on($scroll) 
			? '<div id="'.esc_attr($id).'_scroll" class="sc_scroll sc_scroll_'.esc_attr($dir).' sc_slider_noresize swiper-slider-container scroll-container"'
				. ' style="'.($dir=='vertical' ? 'height:'.($height != '' ? $height : "230px").';' : 'width:'.($width != '' ? $width.';' : "100%;")).'"'
				. '>'
				. '<div class="sc_scroll_wrapper swiper-wrapper">' 
					. '<div class="sc_scroll_slide swiper-slide">' 
			: '');

	if (themerex_get_template_property($style, 'need_isotope')) {
		if (!themerex_sc_param_is_off($filters))
			$output .= '<div class="isotope_filters"></div>';
		if ($columns<1) $columns = themerex_substr($style, -1);
		$output .= '<div class="isotope_wrap" data-columns="'.max(1, min(4, $columns)).'">';
	}

	$args = array(
		'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
		'posts_per_page' => $count,
		'ignore_sticky_posts' => true,
		'order' => $order=='asc' ? 'asc' : 'desc',
		'orderby' => 'date',
	);

	if ($offset > 0 && empty($ids)) {
		$args['offset'] = $offset;
	}

	$args = themerex_query_add_sort_order($args, $orderby, $order);
	if (!themerex_sc_param_is_off($only)) $args = themerex_query_add_filters($args, $only);
	$args = themerex_query_add_posts_and_cats($args, $ids, $post_type, $cat);

	$query = new WP_Query( $args );

	$flt_ids = array();

	while ( $query->have_posts() ) { $query->the_post();

		$THEMEREX_GLOBALS['sc_blogger_counter']++;

		$args = array(
			'layout' => $style,
			'show' => false,
			'number' => $THEMEREX_GLOBALS['sc_blogger_counter'],
			'add_view_more' => false,
			'posts_on_page' => ($count > 0 ? $count : $query->found_posts),
			// Additional options to layout generator
			"location" => $location,
			"descr" => $descr,
			"readmore" => $readmore,
			"loadmore" => $loadmore,
			"reviews" => themerex_sc_param_is_on($rating),
			"dir" => $dir,
			"scroll" => themerex_sc_param_is_on($scroll),
			"info" => themerex_sc_param_is_on($info),
			"links" => themerex_sc_param_is_on($links),
			"orderby" => $orderby,
			"columns_count" => $columns,
			"date_format" => $date_format,
			// Get post data
			'strip_teaser' => false,
			'content' => themerex_get_template_property($style, 'need_content'),
			'terms_list' => !themerex_sc_param_is_off($filters) || themerex_get_template_property($style, 'need_terms'),
			'filters' => themerex_sc_param_is_off($filters) ? '' : $filters,
			'hover' => $hover,
			'hover_dir' => $hover_dir
		);
		$post_data = themerex_get_post_data($args);
		$output .= themerex_show_post_layout($args, $post_data);
	
		if (!themerex_sc_param_is_off($filters)) {
			if ($filters == 'tags') {			// Use tags as filter items
				if (!empty($post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms)) {
					foreach ($post_data['post_terms'][$post_data['post_taxonomy_tags']]->terms as $tag) {
						$flt_ids[$tag->term_id] = $tag->name;
					}
				}
			}
		}

	}

	wp_reset_postdata();

	// Close isotope wrapper
	if (themerex_get_template_property($style, 'need_isotope'))
		$output .= '</div>';

	// Isotope filters list
	if (!themerex_sc_param_is_off($filters)) {
		$filters_list = '';
		if ($filters == 'categories') {			// Use categories as filter items
			$taxonomy = themerex_get_taxonomy_categories_by_post_type($post_type);
			$portfolio_parent = $cat ? max(0, themerex_get_parent_taxonomy_by_property($cat, 'show_filters', 'yes', true, $taxonomy)) : 0;
			$args2 = array(
				'type'			=> $post_type,
				'child_of'		=> $portfolio_parent,
				'orderby'		=> 'name',
				'order'			=> 'ASC',
				'hide_empty'	=> 1,
				'hierarchical'	=> 0,
				'exclude'		=> '',
				'include'		=> '',
				'number'		=> '',
				'taxonomy'		=> $taxonomy,
				'pad_counts'	=> false
			);
			$portfolio_list = get_categories($args2);
			if (count($portfolio_list) > 0) {
				$filters_list .= '<a href="#" data-filter="*" class="theme_button active">'.__('All', 'additional-tags').'</a>';
				foreach ($portfolio_list as $cat) {
					$filters_list .= '<a href="#" data-filter=".flt_'.esc_attr($cat->term_id).'" class="theme_button">'.($cat->name).'</a>';
				}
			}
		} else {								// Use tags as filter items
			if (count($flt_ids) > 0) {
				$filters_list .= '<a href="#" data-filter="*" class="theme_button active">'.__('All', 'additional-tags').'</a>';
				foreach ($flt_ids as $flt_id=>$flt_name) {
					$filters_list .= '<a href="#" data-filter=".flt_'.esc_attr($flt_id).'" class="theme_button">'.($flt_name).'</a>';
				}
			}
		}
		if ($filters_list) {
			$output .= '<script type="text/javascript">'
				. 'jQuery(document).ready(function () {'
					. 'jQuery("#'.esc_attr($id).' .isotope_filters").append("'.addslashes($filters_list).'");'
				. '});'
				. '</script>';
		}
	}
	$output	.= (themerex_sc_param_is_on($scroll) 
			? '</div></div><div id="'.esc_attr($id).'_scroll_bar" class="sc_scroll_bar sc_scroll_bar_'.esc_attr($dir).' '.esc_attr($id).'_scroll_bar"></div></div>'
				. (!themerex_sc_param_is_off($controls) ? '<div class="sc_scroll_controls_wrap"><a class="sc_scroll_prev" href="#"></a><a class="sc_scroll_next" href="#"></a></div>' : '')
			: '')
		. ($dir=='horizontal' && $columns > 1 && themerex_get_template_property($style, 'need_columns') ? '</div>' : '')
		. ($container_end)
		. ($style == 'list' ? '</ul>' : '</div>');

	// Add template specific scripts and styles
	do_action('themerex_action_blog_scripts', $style);
	
	$THEMEREX_GLOBALS['sc_blogger_busy'] = false;
	
	return apply_filters('themerex_shortcode_output', $output, 'trx_blogger', $atts, $content);
}

function themerex_sc_in_shortcode_blogger($from_blogger = false) {
	if (!$from_blogger) return false;
	global $THEMEREX_GLOBALS;
	return $THEMEREX_GLOBALS['sc_blogger_busy'];
}
// ---------------------------------- [/trx_blogger] ---------------------------------------





// ---------------------------------- [trx_br] ---------------------------------------

add_shortcode("trx_br", "themerex_sc_br");
						
/*
[trx_br clear="left|right|both"]
*/

function themerex_sc_br($atts, $content = null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	extract(themerex_sc_html_decode(shortcode_atts(array(
		"clear" => ""
    ), $atts)));
	$output = in_array($clear, array('left', 'right', 'both', 'all')) 
		? '<div class="clearfix" style="clear:' . str_replace('all', 'both', $clear) . '"></div>'
		: '<br />';
	return apply_filters('themerex_shortcode_output', $output, 'trx_br', $atts, $content);
}
// ---------------------------------- [/trx_br] ---------------------------------------



// ---------------------------------- [trx_button] ---------------------------------------


add_shortcode('trx_button', 'themerex_sc_button');

/*
[trx_button id="unique_id" type="square|round" fullsize="0|1" style="global|light|dark" size="mini|medium|big|huge|banner" icon="icon-name" link='#' target='']Button caption[/trx_button]
*/
function themerex_sc_button($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"type" => "square",
		"style" => "filled",
		"size" => "small",
		"icon" => "",
		"color" => "",
		"bg_color" => "",
		"bg_style" => "link",
		"link" => "",
		"target" => "",
		"align" => "",
		"rel" => "",
		"popup" => "no",
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
		"animation" => "",
		"width" => "",
		"height" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left, $width, $height)
		. ($color !== '' ? 'color:' . esc_attr($color) .';' : '')
		. ($bg_color !== '' ? 'background-color:' . esc_attr($bg_color) . '; border-color:'. esc_attr($bg_color) .';' : '');
	if (themerex_sc_param_is_on($popup)) themerex_enqueue_popup('magnific');
	$output = '<a href="' . (empty($link) ? '#' : $link) . '"'
		. ((!empty($target) &&($target==true || $target=='yes' || $target=='_blank')) ? ' target="_blank"' : '')
		. (!empty($rel) ? ' rel="'.esc_attr($rel).'"' : '')
		. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
		. ' class="sc_button sc_button_' . esc_attr($type) 
				. ' sc_button_style_' . esc_attr($style) 
				. ' sc_button_bg_' . esc_attr($bg_style)
				. ' sc_button_size_' . esc_attr($size)
				. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
				. (!empty($class) ? ' '.esc_attr($class) : '')
				. ($icon!='' ? '  sc_button_iconed '. esc_attr($icon) : '') 
				. (themerex_sc_param_is_on($popup) ? ' popup_link' : '') 
				. '"'
		. ($id ? ' id="'.esc_attr($id).'"' : '') 
		. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
		. '>'
		. do_shortcode($content)
		. '</a>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_button', $atts, $content);
}

// ---------------------------------- [/trx_button] ---------------------------------------





// ---------------------------------- [trx_chat] ---------------------------------------

add_shortcode('trx_chat', 'themerex_sc_chat');

/*
[trx_chat id="unique_id" link="url" title=""]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_chat]
[trx_chat id="unique_id" link="url" title=""]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_chat]
...
*/
function themerex_sc_chat($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"photo" => "",
		"title" => "",
		"link" => "",
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
		"animation" => "",
		"width" => "",
		"height" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left, $width, $height);
	$title = $title=='' ? $link : $title;
	if (!empty($photo)) {
		if ($photo > 0) {
			$attach = wp_get_attachment_image_src( $photo, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$photo = $attach[0];
		}
		$photo = themerex_get_resized_image_tag($photo, 75, 75);
	}
	$content = do_shortcode($content);
	if (themerex_substr($content, 0, 2)!='<p') $content = '<p>' . ($content) . '</p>';
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_chat' . (!empty($class) ? ' '.esc_attr($class) : '') . '"' 
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. ($css ? ' style="'.esc_attr($css).'"' : '') 
			. '>'
				. '<div class="sc_chat_inner">'
					. ($photo ? '<div class="sc_chat_avatar">'.($photo).'</div>' : '')
					. ($title == '' ? '' : ('<div class="sc_chat_title">' . ($link!='' ? '<a href="'.esc_url($link).'">' : '') . ($title) . ($link!='' ? '</a>' : '') . '</div>'))
					. '<div class="sc_chat_content">'.($content).'</div>'
				. '</div>'
			. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_chat', $atts, $content);
}

// ---------------------------------- [/trx_chat] ---------------------------------------




// ---------------------------------- [trx_columns] ---------------------------------------


add_shortcode('trx_columns', 'themerex_sc_columns');

/*
[trx_columns id="unique_id" count="number"]
	[trx_column_item id="unique_id" span="2 - number_columns"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta, odio arcu vut natoque dolor ut, enim etiam vut augue. Ac augue amet quis integer ut dictumst? Elit, augue vut egestas! Tristique phasellus cursus egestas a nec a! Sociis et? Augue velit natoque, amet, augue. Vel eu diam, facilisis arcu.[/trx_column_item]
	[trx_column_item]A pulvinar ut, parturient enim porta ut sed, mus amet nunc, in. Magna eros hac montes, et velit. Odio aliquam phasellus enim platea amet. Turpis dictumst ultrices, rhoncus aenean pulvinar? Mus sed rhoncus et cras egestas, non etiam a? Montes? Ac aliquam in nec nisi amet eros! Facilisis! Scelerisque in.[/trx_column_item]
	[trx_column_item]Duis sociis, elit odio dapibus nec, dignissim purus est magna integer eu porta sagittis ut, pid rhoncus facilisis porttitor porta, et, urna parturient mid augue a, in sit arcu augue, sit lectus, natoque montes odio, enim. Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus, vut enim habitasse cum magna.[/trx_column_item]
	[trx_column_item]Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus, vut enim habitasse cum magna. Duis sociis, elit odio dapibus nec, dignissim purus est magna integer eu porta sagittis ut, pid rhoncus facilisis porttitor porta, et, urna parturient mid augue a, in sit arcu augue, sit lectus, natoque montes odio, enim.[/trx_column_item]
[/trx_columns]
*/
function themerex_sc_columns($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"count" => "2",
		"fluid" => "no",
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
		"animation" => "",
		"width" => "",
		"height" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left, $width, $height);
	$count = max(1, min(12, (int) $count));
	global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_columns_counter'] = 1;
	$THEMEREX_GLOBALS['sc_columns_after_span2'] = false;
	$THEMEREX_GLOBALS['sc_columns_after_span3'] = false;
	$THEMEREX_GLOBALS['sc_columns_after_span4'] = false;
	$THEMEREX_GLOBALS['sc_columns_count'] = $count;
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="columns_wrap sc_columns'
				. ' columns_' . (themerex_sc_param_is_on($fluid) ? 'fluid' : 'nofluid') 
				. ' sc_columns_count_' . esc_attr($count)
				. (!empty($class) ? ' '.esc_attr($class) : '') 
			. '"'
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. '>'
				. do_shortcode($content)
			. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_columns', $atts, $content);
}


add_shortcode('trx_column_item', 'themerex_sc_column_item');

function themerex_sc_column_item($atts, $content=null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	extract(themerex_sc_html_decode(shortcode_atts( array(
		// Individual params
		"span" => "1",
		"align" => "",
		"color" => "",
		"bg_color" => "",
		"bg_image" => "",
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
		"animation" => ""
	), $atts)));
	$css .= ($align !== '' ? 'text-align:' . esc_attr($align) . ';' : '') 
		. ($color !== '' ? 'color:' . esc_attr($color) . ';' : '');
	$span = max(1, min(11, (int) $span));
	global $THEMEREX_GLOBALS;
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') . ' class="column-'.($span > 1 ? esc_attr($span) : 1).'_'.esc_attr($THEMEREX_GLOBALS['sc_columns_count']).' sc_column_item sc_column_item_'.esc_attr($THEMEREX_GLOBALS['sc_columns_counter']) 
				. (!empty($class) ? ' '.esc_attr($class) : '')
				. ($THEMEREX_GLOBALS['sc_columns_counter'] % 2 == 1 ? ' odd' : ' even') 
				. ($THEMEREX_GLOBALS['sc_columns_counter'] == 1 ? ' first' : '') 
				. ($span > 1 ? ' span_'.esc_attr($span) : '') 
				. ($THEMEREX_GLOBALS['sc_columns_after_span2'] ? ' after_span_2' : '') 
				. ($THEMEREX_GLOBALS['sc_columns_after_span3'] ? ' after_span_3' : '') 
				. ($THEMEREX_GLOBALS['sc_columns_after_span4'] ? ' after_span_4' : '') 
				. '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
				. '>'
				. ($bg_color!=='' || $bg_image !== '' ? '<div class="sc_column_item_inner" style="'
						. ($bg_color !== '' ? 'background-color:' . esc_attr($bg_color) . ';' : '')
						. ($bg_image !== '' ? 'background-image:url(' . esc_url($bg_image) . ');' : '')
						. '">' : '')
					. do_shortcode($content)
				. ($bg_color!=='' || $bg_image !== '' ? '</div>' : '')
				. '</div>';
	$THEMEREX_GLOBALS['sc_columns_counter'] += $span;
	$THEMEREX_GLOBALS['sc_columns_after_span2'] = $span==2;
	$THEMEREX_GLOBALS['sc_columns_after_span3'] = $span==3;
	$THEMEREX_GLOBALS['sc_columns_after_span4'] = $span==4;
	return apply_filters('themerex_shortcode_output', $output, 'trx_column_item', $atts, $content);
}

// ---------------------------------- [/trx_columns] ---------------------------------------





// ---------------------------------- [trx_contact_form] ---------------------------------------

add_shortcode("trx_contact_form", "themerex_sc_contact_form");

/*
[trx_contact_form id="unique_id" title="Contact Form" description="Mauris aliquam habitasse magna."]
*/

function themerex_sc_contact_form($atts, $content = null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"custom" => "no",
		"action" => "",
		"title" => "",
		"description" => "",
		"align" => "",
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
		"animation" => "",
		"width" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	if (empty($id)) $id = "sc_contact_form_".str_replace('.', '', mt_rand());
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left, $width);
	// Load core messages
	themerex_enqueue_messages();
	global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_contact_form_id'] = $id;
	$THEMEREX_GLOBALS['sc_contact_form_counter'] = 0;
	$content = do_shortcode($content);
    static $cnt = 0;
    $cnt++;
    $privacy = trx_addons_get_privacy_text();
	$output = '<div ' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. 'class="sc_contact_form sc_contact_form_'.($content != '' && themerex_sc_param_is_on($custom) ? 'custom' : 'standard') 
				. (!empty($align) && !themerex_sc_param_is_off($align) ? ' align'.esc_attr($align) : '') 
				. (!empty($class) ? ' '.esc_attr($class) : '') 
				. '"'
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. '>'
		. ($title ? '<h2 class="sc_contact_form_title">' . ($title) . '</h2>' : '')
		. ($description ? '<p class="sc_contact_form_description">' . ($description) . '</p>' : '')
		. '<form' . ($id ? ' id="'.esc_attr($id).'"' : '') . ' data-formtype="'.($content ? 'custom' : 'contact').'" method="post" action="' . esc_url($action ? $action : admin_url('admin-ajax.php')) . '">'
		. ($content != '' && themerex_sc_param_is_on($custom)
			? $content 
			: '<div class="sc_contact_form_info">'
					.'<div class="sc_contact_form_item sc_contact_form_field label_over"><label class="required" for="sc_contact_form_username">' . __('Name', 'additional-tags') . '</label><input id="sc_contact_form_username" type="text" name="username" placeholder="' . __('Name *', 'additional-tags') . '"></div>'
					. '<div class="sc_contact_form_item sc_contact_form_field label_over"><label class="required" for="sc_contact_form_email">' . __('E-mail', 'additional-tags') . '</label><input id="sc_contact_form_email" type="text" name="email" placeholder="' . __('E-mail *', 'additional-tags') . '"></div>'
					.'<div class="sc_contact_form_item sc_contact_form_field label_over"><label class="required" for="sc_contact_form_subj">' . __('Subject', 'additional-tags') . '</label><input id="sc_contact_form_subj" type="text" name="subject" placeholder="' . __('Subject', 'additional-tags') . '"></div>'
				.'</div>'
            . '<div class="sc_contact_form_item sc_contact_form_message label_over"><label class="required" for="sc_contact_form_message">' . __('Message', 'additional-tags') . '</label><textarea id="sc_contact_form_message" name="message" placeholder="' . __('Message', 'additional-tags') . '"></textarea></div>' .
            ((!empty($privacy)) ? '<div class="sc_form_field sc_form_field_checkbox">
                    <input type="checkbox" id="i_agree_privacy_policy_sc_form_' . esc_attr($cnt) . '" name="i_agree_privacy_policy" class="sc_form_privacy_checkbox" value="1">
                    <label for="i_agree_privacy_policy_sc_form_' . esc_attr($cnt) . '">' . $privacy . '</label></div>' : '')
                .'<div class="sc_contact_form_item sc_contact_form_button" ' . (!empty($privacy) ? ' disabled="disabled"' : '') . '><button>'.__('SEND MESSAGE', 'additional-tags').'</button></div>'
		.'<div class="result sc_infobox"></div>'
		.'</form>'
		.'</div>');
	return apply_filters('themerex_shortcode_output', $output, 'trx_contact_form', $atts, $content);
}


add_shortcode('trx_form_item', 'themerex_sc_contact_form_item');

function themerex_sc_contact_form_item($atts, $content=null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	extract(themerex_sc_html_decode(shortcode_atts( array(
		// Individual params
		"type" => "text",
		"name" => "",
		"value" => "",
		"checked" => "",
		"align" => "",
		"label" => "",
		"label_position" => "top",
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
		"animation" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
	), $atts)));
	global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_contact_form_counter']++;
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
	if (empty($id)) $id = ($THEMEREX_GLOBALS['sc_contact_form_id']).'_'.($THEMEREX_GLOBALS['sc_contact_form_counter']);
	$labelOver = '';
	if ( ( $label != '' ) and ( $label != 'Label' ) ) $labelOver = $label;
	$label = $type!='button' && $type!='submit' && $label ? '<label for="' . esc_attr($id) . '"' . (themerex_sc_param_is_on($checked) ? ' class="selected"' : '') . '>' . esc_attr($label) . '</label>' : $label;
	$output = '<div class="sc_contact_form_item sc_contact_form_item_'.esc_attr($type)
					.' sc_contact_form_'.($type == 'textarea' ? 'message' : ($type == 'button' || $type == 'submit' ? 'button' : 'field'))
					.' label_'.esc_attr($label_position)
					.($class ? ' '.esc_attr($class) : '')
					.($align && $align!='none' ? ' align'.esc_attr($align) : '')
				.'"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
				. '>'
		. ($type!='button' && $type!='submit' && ($label_position=='top' || $label_position=='left') ? $label : '')
		. ($type == 'textarea' 
			? '<textarea id="' . esc_attr($id) . '" name="' . esc_attr($name ? $name : $id) . '">' . esc_attr($value) . '</textarea>'
			: ($type=='button' || $type=='submit' 
				? '<button id="' . esc_attr($id) . '">'.($label ? $label : $value).'</button>'
				: '<input type="'.($type ? $type : 'text').'" id="' . esc_attr($id) . '" name="' . esc_attr($name ? $name : $id) . '" value="' . esc_attr($value) . '"'.((($label_position=='over') and ($labelOver != '')) ? ' placeholder="'.$labelOver.'"' : '')	. (themerex_sc_param_is_on($checked) ? ' checked="checked"' : '') . '>'
				)
			)
		. ($type!='button' && $type!='submit' && $label_position=='bottom' ? $label : '')
		. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_form_item', $atts, $content);
}


// AJAX Callback: Send contact form data
if ( !function_exists( 'sc_contact_form_send' ) ) {
	function themerex_sc_contact_form_send() {
		global $_REQUEST;
	
		if ( !wp_verify_nonce( $_REQUEST['nonce'], 'ajax_nonce' ) )
			die();
	
		$response = array('error'=>'');
		if (!($contact_email = themerex_get_theme_option('contact_email')) && !($contact_email = themerex_get_theme_option('admin_email'))) 
			$response['error'] = __('Unknown admin email!', 'additional-tags');
		else {
			$type = themerex_substr($_REQUEST['type'], 0, 7);
			parse_str($_POST['data'], $post_data);

			if ($type=='contact') {
				$user_name	= themerex_strshort($post_data['username'],	100);
				$user_email	= themerex_strshort($post_data['email'],	100);
				$user_subj	= themerex_strshort($post_data['subject'],	100);
				$user_msg	= themerex_strshort($post_data['message'],	themerex_get_theme_option('message_maxlength_contacts'));
		
				$subj = sprintf(__('Site %s - Contact form message from %s', 'additional-tags'), get_bloginfo('site_name'), $user_name);
				$msg = "\n".__('Name:', 'additional-tags')   .' '.esc_html($user_name)
					.  "\n".__('E-mail:', 'additional-tags') .' '.esc_html($user_email)
					.  "\n".__('Subject:', 'additional-tags').' '.esc_html($user_subj)
					.  "\n".__('Message:', 'additional-tags').' '.esc_html($user_msg);

			} else {

				$subj = sprintf(__('Site %s - Custom form data', 'additional-tags'), get_bloginfo('site_name'));
				$msg = '';
				foreach ($post_data as $k=>$v)
					$msg .= "\n{$k}: $v";
			}

			$msg .= "\n\n............. " . get_bloginfo('site_name') . " (" . home_url() . ") ............";

			$mail = themerex_get_theme_option('mail_function');
			if (is_email($contact_email) && !@$mail($contact_email, $subj, $msg)) {
				$response['error'] = __('Error send message!', 'additional-tags');
			}
		
			echo json_encode($response);
			die();
		}
	}
}

// ---------------------------------- [/trx_contact_form] ---------------------------------------




// ---------------------------------- [trx_content] ---------------------------------------

add_shortcode('trx_content', 'themerex_sc_content');

/*
[trx_content id="unique_id" class="class_name" style="css-styles"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_content]
*/

function themerex_sc_content($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
		"animation" => "",
		"top" => "",
		"bottom" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values('!'.($top), '', '!'.($bottom));
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
		. ' class="sc_content content_wrap' . ($class ? ' '.esc_attr($class) : '') . '"'
		. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
		. ($css!='' ? ' style="'.esc_attr($css).'"' : '').'>' 
		. do_shortcode($content) 
		. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_content', $atts, $content);
}
// ---------------------------------- [/trx_content] ---------------------------------------





// ---------------------------------- [trx_countdown] ---------------------------------------

add_shortcode("trx_countdown", "themerex_sc_countdown");

//[trx_countdown date="" time=""]
function themerex_sc_countdown($atts, $content = null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"date" => "",
		"time" => "",
		"style" => "1",
		"align" => "center",
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
		"animation" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => "",
		"width" => "",
		"height" => ""
    ), $atts)));
	if (empty($id)) $id = "sc_countdown_".str_replace('.', '', mt_rand());
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left, $width, $height);
	if (empty($interval)) $interval = 1;
	wp_enqueue_script( 'themerex-jquery-plugin-script', themerex_get_file_url('js/countdown/jquery.plugin.js'), array('jquery'), null, true );
	wp_enqueue_script( 'themerex-countdown-script', themerex_get_file_url('js/countdown/jquery.countdown.js'), array('jquery'), null, true );
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
		. ' class="sc_countdown sc_countdown_style_' . esc_attr(max(1, min(2, $style))) . (!empty($align) && $align!='none' ? ' align'.esc_attr($align) : '') . (!empty($class) ? ' '.esc_attr($class) : '') .'"'
		. ($css ? ' style="'.esc_attr($css).'"' : '')
		. ' data-date="'.esc_attr(empty($date) ? date('Y-m-d') : $date).'"'
		. ' data-time="'.esc_attr(empty($time) ? '00:00:00' : $time).'"'
		. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
		. '>'
			. '<div class="sc_countdown_item sc_countdown_days">'
				. '<span class="sc_countdown_digits"><span></span><span></span><span></span></span>'
				. '<span class="sc_countdown_label">'.__('Days', 'additional-tags').'</span>'
			. '</div>'
			. '<div class="sc_countdown_separator">:</div>'
			. '<div class="sc_countdown_item sc_countdown_hours">'
				. '<span class="sc_countdown_digits"><span></span><span></span></span>'
				. '<span class="sc_countdown_label">'.__('Hours', 'additional-tags').'</span>'
			. '</div>'
			. '<div class="sc_countdown_separator">:</div>'
			. '<div class="sc_countdown_item sc_countdown_minutes">'
				. '<span class="sc_countdown_digits"><span></span><span></span></span>'
				. '<span class="sc_countdown_label">'.__('Minutes', 'additional-tags').'</span>'
			. '</div>'
			. '<div class="sc_countdown_separator">:</div>'
			. '<div class="sc_countdown_item sc_countdown_seconds">'
				. '<span class="sc_countdown_digits"><span></span><span></span></span>'
				. '<span class="sc_countdown_label">'.__('Seconds', 'additional-tags').'</span>'
			. '</div>'
			. '<div class="sc_countdown_placeholder hide"></div>'
		. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_countdown', $atts, $content);
}
// ---------------------------------- [/trx_countdown] ---------------------------------------



						


// ---------------------------------- [trx_dropcaps] ---------------------------------------

add_shortcode('trx_dropcaps', 'themerex_sc_dropcaps');

//[trx_dropcaps id="unique_id" style="1-6"]paragraph text[/trx_dropcaps]
function themerex_sc_dropcaps($atts, $content=null){
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"style" => "1",
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
		"animation" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
	$style = min(4, max(1, $style));
	$content = do_shortcode($content);
	$output = themerex_substr($content, 0, 1) == '<' 
		? $content 
		: '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_dropcaps sc_dropcaps_style_' . esc_attr($style) . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
			. ($css ? ' style="'.esc_attr($css).'"' : '')
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. '>' 
				. '<span class="sc_dropcaps_item">' . trim(themerex_substr($content, 0, 1)) . '</span>' . trim(themerex_substr($content, 1))
		. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_dropcaps', $atts, $content);
}
// ---------------------------------- [/trx_dropcaps] ---------------------------------------





// ---------------------------------- [trx_emailer] ---------------------------------------

add_shortcode("trx_emailer", "themerex_sc_emailer");

//[trx_emailer group=""]
function themerex_sc_emailer($atts, $content = null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"group" => "",
		"open" => "yes",
		"align" => "",
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
		"animation" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => "",
		"width" => "",
		"height" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left, $width, $height);
	// Load core messages
	themerex_enqueue_messages();
    static $cnt = 4;
    $privacy = themerex_get_privacy_text();
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
				. ' class="sc_emailer' . ($align && $align!='none' ? ' align' . esc_attr($align) : '') . (themerex_sc_param_is_on($open) ? ' sc_emailer_opened' : '') . (!empty($class) ? ' '.esc_attr($class) : '') . '"' 
				. ($css ? ' style="'.esc_attr($css).'"' : '') 
				. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
				. '>'
			. '<form class="sc_emailer_form"><div class="sc_emailer_mail_wrap">'
			. '<input type="text" class="sc_emailer_input" name="email" value="" placeholder="'.__('Please, enter you email address.', 'additional-tags').'">'
			. '<button href="#" class="sc_emailer_button icon-mail-1" ' . (!empty($privacy) ? ' disabled="disabled"' : '') . '"title="'.__('Submit', 'additional-tags').'" data-group="'.($group ? $group : __('E-mailer subscription', 'additional-tags')).'"></button></div>'
        . ((!empty($privacy)) ? '<div class="sc_form_field sc_form_field_checkbox sc_emailer_agree_field">
                    <input type="checkbox" id="i_agree_privacy_policy_sc_form_' . esc_attr($cnt) . '" name="i_agree_privacy_policy" class="sc_form_privacy_checkbox" value="1">
                    <label for="i_agree_privacy_policy_sc_form_' . esc_attr($cnt) . '">' . $privacy . '</label></div>' : '')
        . '</form>'
		. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_emailer', $atts, $content);
}
// ---------------------------------- [/trx_emailer] ---------------------------------------





// ---------------------------------- [trx_gap] ---------------------------------------

add_shortcode("trx_gap", "themerex_sc_gap");
						
//[trx_gap]Fullwidth content[/trx_gap]

function themerex_sc_gap($atts, $content = null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	$output = themerex_sc_gap_start() . do_shortcode($content) . themerex_sc_gap_end();
	return apply_filters('themerex_shortcode_output', $output, 'trx_gap', $atts, $content);
}

function themerex_sc_gap_start() {
	return '<!-- #TRX_GAP_START# -->';
}

function themerex_sc_gap_end() {
	return '<!-- #TRX_GAP_END# -->';
}

function themerex_sc_gap_wrapper($str) {
	// Move VC row and column and wrapper inside gap
	$str_new = preg_replace('/(<div\s+class="[^"]*vc_row[^>]*>)[\r\n\s]*(<div\s+class="[^"]*vc_col[^>]*>)[\r\n\s]*(<div\s+class="[^"]*wpb_wrapper[^>]*>)[\r\n\s]*('.themerex_sc_gap_start().')/i', '\\4\\1\\2\\3', $str);
	if ($str_new != $str) $str_new = preg_replace('/('.themerex_sc_gap_end().')[\r\n\s]*(<\/div>)[\r\n\s]*(<\/div>)[\r\n\s]*(<\/div>)/i', '\\2\\3\\4\\1', $str_new);
	// Gap layout
	return str_replace(
			array(
				themerex_sc_gap_start(),
				themerex_sc_gap_end()
			),
			array(
				themerex_close_all_wrappers(false) . '<div class="sc_gap">',
				'</div>' . themerex_open_all_wrappers(false)
			),
			$str_new
		); 
}
// ---------------------------------- [/trx_gap] ---------------------------------------






// ---------------------------------- [trx_googlemap] ---------------------------------------

add_shortcode("trx_googlemap", "themerex_sc_google_map");

//[trx_googlemap id="unique_id" address="your_address" width="width_in_pixels_or_percent" height="height_in_pixels"]
function themerex_sc_google_map($atts, $content = null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	$atts = themerex_sc_html_decode(shortcode_atts(array(
			// Individual params
			"type" => "default",
			"zoom" => 16,
			"center" => '',
			"style" => 'default',
			"address" => '',
			"markers" => '',
			"cluster" => '',
			"width" => "100%",
			"height" => "400",
			"title" => '',
			"subtitle" => '',
			"description" => '',
			"prevent_scroll" => 0,
			"link" => '',
			"link_style" => 'default',
			"link_image" => '',
			"link_text" => esc_html__('Learn more', 'additional-tags'),
			"title_align" => "left",
			"title_style" => "default",
			"title_tag" => '',
			// Common params
			"id" => "",
			"class" => "",
			"css" => "",
			"animation" => "",
			"top" => "",
			"bottom" => "",
			"left" => "",
			"right" => "",
			// Content from non-containers PageBuilder
			"content" => ""
		), $atts)
	);

	if (!is_array($atts['markers']) && function_exists('vc_param_group_parse_atts')) {
		$atts['markers'] = (array) vc_param_group_parse_atts( $atts['markers'] );
	}

	$output = '';

	if (empty($atts['address']) && empty($atts['latlng'])) {
		$atts['latlng'] = themerex_get_custom_option('googlemap_latlng');
		if (empty($atts['latlng']) && !(is_array($atts['markers']) && count($atts['markers']) > 0)) {
			$atts['address'] = themerex_get_custom_option('googlemap_address');
		}
	}
	if (isset($atts['latlng'])) {
		$atts['markers'] = array(
			array(
				'title' => '',
				'description' => '',
				'address' => '',
				'latlng' => $atts['latlng'],
				'icon' => themerex_get_theme_option('api_google_marker'),
				'icon_width' => '',
				'icon_height' => ''
			)
		);
	}
	if ((is_array($atts['markers']) && count($atts['markers']) > 0) || !empty($atts['address'])) {
		if (!empty($atts['address'])) {
			$atts['markers'] = array(
				array(
					'title' => '',
					'description' => '',
					'address' => $atts['address'],
					'latlng' => '',
					'icon' => themerex_get_theme_option('api_google_marker'),
					'icon_width' => '',
					'icon_height' => ''
				)
			);
		} else {
			foreach ($atts['markers'] as $k=>$v) {
				if (!empty($v['description']) && function_exists('vc_value_from_safe'))
					$atts['markers'][$k]['description'] = trim( vc_value_from_safe( $v['description'] ) );
				if (!empty($v['icon'])) {
					$atts['markers'][$k]['icon'] = themerex_get_attachment_url($v['icon'], 'full');
					if (empty($v['icon_height'])) {
						$attr = themerex_getimagesize($atts['markers'][$k]['icon']);
						$atts['markers'][$k]['icon_width'] = $attr[0];
						$atts['markers'][$k]['icon_height'] = $attr[1];
					}
				} else {
					$v['icon'] = themerex_remove_protocol(themerex_get_theme_option('api_google_marker'));
				}
			}
		}

		$atts['zoom'] = max(0, min(21, $atts['zoom']));
		$atts['center'] = trim($atts['center']);

		if (count($atts['markers']) > 1) {
			if (empty($atts['cluster']))
				$atts['cluster'] = themerex_remove_protocol(themerex_get_theme_option('api_google_cluster'));
			if (empty($atts['cluster']))
				$atts['cluster'] = trx_addons_get_file_url(trx_addons_get_file_url( 'shortcodes/googlemap/cluster/cluster-icon.png'));
			else if ((int) $atts['cluster'] > 0)
				$atts['cluster'] = themerex_get_attachment_url($atts['cluster'], apply_filters('themerex_filter_thumb_size', array(370,   0, false), 'googlemap-cluster'));
		} else if ($atts['zoom'] == 0)
			$atts['zoom'] = 16;

		$atts['class'] .= (!empty($atts['class']) ? ' ' : '') . themerex_add_inline_css_class(themerex_get_css_dimensions_from_values($atts['width'], $atts['height']));

		if (empty($atts['style'])) $atts['style'] = 'default';

		$atts['content'] = do_shortcode(empty($atts['content']) ? $content : $atts['content']);

		themerex_enqueue_googlemap();
		$api_key = themerex_get_theme_option('api_google');
		if (themerex_sc_param_is_on(themerex_get_theme_option('debug_mode')) && !empty($api_key)) {
			wp_enqueue_script( 'themerex-sc_googlemap', trx_addons_get_file_url('shortcodes/googlemap/googlemap.js'), array('jquery'), null, true );
			if (count($atts['markers']) > 1)
				wp_enqueue_script( 'markerclusterer', trx_addons_get_file_url('shortcodes/googlemap/cluster/markerclusterer.min.js'), array('jquery'), null, true );
		}

		ob_start();
        trx_addons_get_template_part(array(
			('shortcodes/googlemap/tpl.'.sanitize_file_name($atts['type']).'.php'),
			('shortcodes/googlemap/tpl.default.php')
		),
			'themerex_args_sc_googlemap',
			$atts
		);
		$output = ob_get_contents();
		ob_end_clean();
	}

	return apply_filters('themerex_shortcode_output', $output, 'trx_googlemap', $atts, $content);
}



// Include part of template with specified parameters
if (!function_exists('trx_addons_get_template_part')) {
    function trx_addons_get_template_part($file, $args_name='', $args=array()) {
        if (!is_array($file))
            $file = array($file);
        foreach ($file as $f) {
            if (($fdirs[$f] = trx_addons_get_file_dir($f)) != '') {
                if (!empty($args_name) && !empty($args))
                    set_query_var($args_name, $args);
                include $fdirs[$f];
                break;
            }
        }
    }
}



// Include part of template with specified parameters
if (!function_exists('themerex_get_template_part')) {
	function themerex_get_template_part($file, $args_name='', $args=array()) {
		if (!is_array($file))
			$file = array($file);
		foreach ($file as $f) {
			if (($fdirs[$f] = themerex_get_file_dir($f)) != '') {
				if (!empty($args_name) && !empty($args))
					set_query_var($args_name, $args);
				include $fdirs[$f];
				break;
			}
		}
	}
}


// Return image url by attachment ID
if (!function_exists('themerex_get_attachment_url')) {
	function themerex_get_attachment_url($image_id, $size='full') {
		if ($image_id > 0) {
			$attach = wp_get_attachment_image_src($image_id, $size);
			$image_id = isset($attach[0]) && $attach[0]!='' ? $attach[0] : '';
		} else
			$image_id = themerex_add_thumb_size($image_id, $size);
		return $image_id;
	}
}
// Add thumb sizes to image name
if (!function_exists('themerex_add_thumb_size')) {
	function themerex_add_thumb_size($url, $thumb_size, $check_exists=true) {

		if (empty($url)) return '';

		$pi = pathinfo($url);
		$pi['dirname'] = themerex_remove_protocol($pi['dirname']);

		// Remove image sizes from filename
		$parts = explode('-', $pi['filename']);
		$suff = explode('x', $parts[count($parts)-1]);
		if (count($suff)==2 && (int) $suff[0] > 0 && (int) $suff[1] > 0) {
			array_pop($parts);
		}
		$url = $pi['dirname'] . '/' . join('-', $parts) . '.' . $pi['extension'];

		// Add new image sizes
		global $_wp_additional_image_sizes;
		if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) && in_array( $thumb_size, array_keys( $_wp_additional_image_sizes ) ) )
			$parts[] = intval( $_wp_additional_image_sizes[$thumb_size]['width'] ) . 'x' . intval( $_wp_additional_image_sizes[$thumb_size]['height'] );
		$pi['filename'] = join('-', $parts);
		$new_url = $pi['dirname'] . '/' . $pi['filename'] . '.' . $pi['extension'];

		// Check exists
		if ($check_exists) {
			$uploads_info = wp_upload_dir();
			$uploads_url = themerex_remove_protocol($uploads_info['baseurl']);
			$uploads_dir = $uploads_info['basedir'];
			if (strpos($new_url, $uploads_url)!==false) {
				if (!file_exists(str_replace($uploads_url, $uploads_dir, $new_url)))
					$new_url = $url;
			} else {
				$new_url = $url;
			}
		}
		return $new_url;
	}
}
// Return url without protocol
if (!function_exists('themerex_remove_protocol')) {
	function themerex_remove_protocol($url, $complete=false) {
		$url = preg_replace('/http[s]?:'.($complete ? '\\/\\/' : '').'/', '', $url);
		return $url;
	}
}
// Get image sizes from image url (if image in the uploads folder)
if (!function_exists('themerex_getimagesize')) {
	function themerex_getimagesize($url, $echo=false) {
		// Remove scheme from url
		$url = themerex_remove_protocol($url);

		// Get upload path & dir
		$upload_info = wp_upload_dir();

		// Where check file
		$locations = array(
			'uploads' => array(
				'dir' => $upload_info['basedir'],
				'url' => themerex_remove_protocol($upload_info['baseurl'])
			),
			'child' => array(
				'dir' => get_stylesheet_directory(),
				'url' => themerex_remove_protocol(get_stylesheet_directory_uri())
			),
			'theme' => array(
				'dir' => get_template_directory(),
				'url' => themerex_remove_protocol(get_template_directory_uri())
			)
		);

		$img_size = false;

		foreach($locations as $key=>$loc) {
			// Check if $img_url is local.
			if ( false === strpos($url, $loc['url']) ) continue;

			// Get path of image.
			$img_path = str_replace($loc['url'], $loc['dir'], $url);

			// Check if img path exists, and is an image indeed.
			if ( !file_exists($img_path)) continue;

			// Get image size
			$img_size = getimagesize($img_path);
			break;
		}

		if ($echo && $img_size!==false && !empty($img_size[3])) {
			echo ' '.trim($img_size[3]);
		}

		return $img_size;
	}
}
// Return string with dimensions rules for the style attr
if (!function_exists('themerex_get_css_dimensions_from_values')) {
	function themerex_get_css_dimensions_from_values($width='',$height='') {
		if (!is_array($width)) {
			$width = compact('width','height');
		}
		$output = '';
		if (is_array($width) && count($width) > 0) {
			foreach ($width as $k=>$v) {
				$imp = themerex_substr($v, 0, 1);
				if ($imp == '!') $v = themerex_substr($v, 1);
				if ($v != '') $output .= esc_attr($k) . ':' . esc_attr(themerex_prepare_css_value($v)) . ($imp=='!' ? ' !important' : '') . ';';
			}
		}
		return $output;
	}
}//  Enqueue Google map script
if ( !function_exists( 'themerex_enqueue_googlemap' ) ) {
	function themerex_enqueue_googlemap() {
		$api_key = themerex_get_theme_option('api_google');
		if (themerex_sc_param_is_on(themerex_get_theme_option('api_google_load')) && !empty($api_key)) {
			wp_enqueue_script( 'google-maps', themerex_get_protocol().'://maps.googleapis.com/maps/api/js'.($api_key ? '?key='.$api_key : ''), array(), null, true );
		}
	}
}

// ---------------------------------- [/trx_googlemap] ---------------------------------------





// ---------------------------------- [trx_hide] ---------------------------------------


add_shortcode('trx_hide', 'themerex_sc_hide');

/*
[trx_hide selector="unique_id"]
*/
function themerex_sc_hide($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"selector" => "",
		"hide" => "on",
		"delay" => 0
    ), $atts)));
	$selector = trim(chop($selector));
	$output = $selector == '' ? '' : 
		'<script type="text/javascript">
			jQuery(document).ready(function() {
				'.($delay>0 ? 'setTimeout(function() {' : '').'
				jQuery("'.esc_attr($selector).'").' . ($hide=='on' ? 'hide' : 'show') . '();
				'.($delay>0 ? '},'.($delay).');' : '').'
			});
		</script>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_hide', $atts, $content);
}
// ---------------------------------- [/trx_hide] ---------------------------------------





// ---------------------------------- [trx_highlight] ---------------------------------------

add_shortcode('trx_highlight', 'themerex_sc_highlight');

/*
[trx_highlight id="unique_id" color="fore_color's_name_or_#rrggbb" backcolor="back_color's_name_or_#rrggbb" style="custom_style"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_highlight]
*/
function themerex_sc_highlight($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"color" => "",
		"bg_color" => "",
		"font_size" => "",
		"type" => "1",
		// Common params
		"id" => "",
		"class" => "",
		"css" => ""
    ), $atts)));
	$css .= ($color != '' ? 'color:' . esc_attr($color) . ';' : '')
		.($bg_color != '' ? 'background-color:' . esc_attr($bg_color) . ';' : '')
		.($font_size != '' ? 'font-size:' . esc_attr(themerex_prepare_css_value($font_size)) . '; line-height: 1em;' : '');
	$output = '<span' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_highlight'.($type>0 ? ' sc_highlight_style_'.esc_attr($type) : ''). (!empty($class) ? ' '.esc_attr($class) : '').'"'
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. '>' 
			. do_shortcode($content) 
			. '</span>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_highlight', $atts, $content);
}
// ---------------------------------- [/trx_highlight] ---------------------------------------





// ---------------------------------- [trx_icon] ---------------------------------------


add_shortcode('trx_icon', 'themerex_sc_icon');

/*
[trx_icon id="unique_id" style='round|square' icon='' color="" bg_color="" size="" weight=""]
*/
function themerex_sc_icon($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"icon" => "",
		"color" => "",
		"bg_color" => "",
		"bg_shape" => "",
		"bg_style" => "",
		"font_size" => "",
		"font_weight" => "",
		"align" => "",
		"link" => "",
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
	$css2 = ($font_weight != '' && $font_weight != 'inherit' ? 'font-weight:'. esc_attr($font_weight).';' : '')
		. ($font_size != '' ? 'font-size:' . esc_attr(themerex_prepare_css_value($font_size)) . '; line-height: ' . (!$bg_shape || themerex_sc_param_is_inherit($bg_shape) ? '1' : '1.2') . 'em;' : '')
		. ($color != '' ? 'color:'.esc_attr($color).';' : '')
		. ($bg_color != '' ? 'background-color:'.esc_attr($bg_color).';border-color:'.esc_attr($bg_color).';' : '')
	;
	$output = $icon!='' 
		? ($link ? '<a href="'.esc_url($link).'"' : '<span') . ($id ? ' id="'.esc_attr($id).'"' : '')
			. ' class="sc_icon '.esc_attr($icon)
				. ($bg_shape && !themerex_sc_param_is_inherit($bg_shape) ? ' sc_icon_shape_'.esc_attr($bg_shape) : '')
				. ($bg_style && !themerex_sc_param_is_inherit($bg_style) ? ' sc_icon_bg_'.esc_attr($bg_style) : '')
				. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
				. (!empty($class) ? ' '.esc_attr($class) : '')
			.'"'
			.($css || $css2 ? ' style="'.($css ? 'display:block;' : '') . ($css) . ($css2) . '"' : '')
			.'>'
			.($link ? '</a>' : '</span>')
		: '';
	return apply_filters('themerex_shortcode_output', $output, 'trx_icon', $atts, $content);
}

// ---------------------------------- [/trx_icon] ---------------------------------------





// ---------------------------------- [trx_image] ---------------------------------------


add_shortcode('trx_image', 'themerex_sc_image');

/*
[trx_image id="unique_id" src="image_url" width="width_in_pixels" height="height_in_pixels" title="image's_title" align="left|right"]
*/
function themerex_sc_image($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"title" => "",
		"align" => "",
		"shape" => "square",
		"src" => "",
		"url" => "",
		"icon" => "",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => "",
		"width" => "",
		"height" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values('!'.($top), '!'.($right), '!'.($bottom), '!'.($left), $width, $height);
	$src = $src!='' ? $src : $url;
	if ($src > 0) {
		$attach = wp_get_attachment_image_src( $src, 'full' );
		if (isset($attach[0]) && $attach[0]!='')
			$src = $attach[0];
	}
	if (!empty($width) || !empty($height)) {
		$w = !empty($width) && strlen(intval($width)) == strlen($width) ? $width : null;
		$h = !empty($height) && strlen(intval($height)) == strlen($height) ? $height : null;
		if ($w || $h) $src = themerex_get_resized_image_url($src, $w, $h);
	}
	$output = empty($src) ? '' : ('<figure' . ($id ? ' id="'.esc_attr($id).'"' : '') 
		. ' class="sc_image ' . ($align && $align!='none' ? ' align' . esc_attr($align) : '') . (!empty($shape) ? ' sc_image_shape_'.esc_attr($shape) : '') . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
		. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
		. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
		. '>'
			. '<img src="'.esc_url($src).'" alt="" />'
			. (trim($title) || trim($icon) ? '<figcaption><span'.($icon ? ' class="'.esc_attr($icon).'"' : '').'></span> ' . ($title) . '</figcaption>' : '')
		. '</figure>');
	return apply_filters('themerex_shortcode_output', $output, 'trx_image', $atts, $content);
}

// ---------------------------------- [/trx_image] ---------------------------------------






// ---------------------------------- [trx_infobox] ---------------------------------------

add_shortcode('trx_infobox', 'themerex_sc_infobox');

/*
[trx_infobox id="unique_id" style="regular|info|success|error|result" static="0|1"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_infobox]
*/
function themerex_sc_infobox($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"style" => "regular",
		"closeable" => "no",
		"icon" => "",
		"color" => "",
		"bg_color" => "",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left)
		. ($color !== '' ? 'color:' . esc_attr($color) .';' : '')
		. ($bg_color !== '' ? 'background-color:' . esc_attr($bg_color) .';' : '');
	if (empty($icon)) {
		if ($icon=='none')
			$icon = '';
		else if ($style=='regular')
			$icon = 'icon-cog-2';
		else if ($style=='success')
			$icon = 'icon-check-2';
		else if ($style=='error')
			$icon = 'icon-alert-2';
		else if ($style=='info')
			$icon = 'icon-info-2';
	}
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_infobox sc_infobox_style_' . esc_attr($style) 
				. (themerex_sc_param_is_on($closeable) ? ' sc_infobox_closeable' : '') 
				. (!empty($class) ? ' '.esc_attr($class) : '') 
				. ($icon!='' && !themerex_sc_param_is_inherit($icon) ? ' sc_infobox_iconed '. esc_attr($icon) : '') 
				. '"'
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. '>'
			. do_shortcode($content) 
			. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_infobox', $atts, $content);
}

// ---------------------------------- [/trx_infobox] ---------------------------------------





// ---------------------------------- [trx_line] ---------------------------------------


add_shortcode('trx_line', 'themerex_sc_line');

/*
[trx_line id="unique_id" style="none|solid|dashed|dotted|double|groove|ridge|inset|outset" top="margin_in_pixels" bottom="margin_in_pixels" width="width_in_pixels_or_percent" height="line_thickness_in_pixels" color="line_color's_name_or_#rrggbb"]
*/
function themerex_sc_line($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"style" => "solid",
		"color" => "",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"width" => "",
		"height" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left, $width)
		.($height !='' ? 'border-top-width:' . esc_attr($height) . 'px;' : '')
		.($style != '' ? 'border-top-style:' . esc_attr($style) . ';' : '')
		.($color != '' ? 'border-top-color:' . esc_attr($color) . ';' : '');
	$output = '<div' . ($id ? ' id="'.esc_attr($id) . '"' : '') 
			. ' class="sc_line' . ($style != '' ? ' sc_line_style_'.esc_attr($style) : '') . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. '></div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_line', $atts, $content);
}

// ---------------------------------- [/trx_line] ---------------------------------------





// ---------------------------------- [trx_list] ---------------------------------------

add_shortcode('trx_list', 'themerex_sc_list');

/*
[trx_list id="unique_id" style="arrows|iconed|ol|ul"]
	[trx_list_item id="unique_id" title="title_of_element"]Et adipiscing integer.[/trx_list_item]
	[trx_list_item]A pulvinar ut, parturient enim porta ut sed, mus amet nunc, in.[/trx_list_item]
	[trx_list_item]Duis sociis, elit odio dapibus nec, dignissim purus est magna integer.[/trx_list_item]
	[trx_list_item]Nec purus, cras tincidunt rhoncus proin lacus porttitor rhoncus.[/trx_list_item]
[/trx_list]
*/
function themerex_sc_list($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"style" => "ul",
		"icon" => "icon-right-2",
		"icon_color" => "",
		"color" => "",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left)
		. ($color !== '' ? 'color:' . esc_attr($color) .';' : '');
	if (trim($style) == '' || (trim($icon) == '' && $style=='iconed')) $style = 'ul';
	global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_list_counter'] = 0;
	$THEMEREX_GLOBALS['sc_list_icon'] = empty($icon) || themerex_sc_param_is_inherit($icon) ? "icon-right-2" : $icon;
	$THEMEREX_GLOBALS['sc_list_icon_color'] = $icon_color;
	$THEMEREX_GLOBALS['sc_list_style'] = $style;
	$output = '<' . ($style=='ol' ? 'ol' : 'ul')
			. ($id ? ' id="'.esc_attr($id).'"' : '')
			. ' class="sc_list sc_list_style_' . esc_attr($style) . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. '>'
			. do_shortcode($content)
			. '</' .($style=='ol' ? 'ol' : 'ul') . '>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_list', $atts, $content);
}


add_shortcode('trx_list_item', 'themerex_sc_list_item');

function themerex_sc_list_item($atts, $content=null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	extract(themerex_sc_html_decode(shortcode_atts( array(
		// Individual params
		"color" => "",
		"icon" => "",
		"icon_color" => "",
		"title" => "",
		"link" => "",
		"target" => "",
		// Common params
		"id" => "",
		"class" => "",
		"css" => ""
	), $atts)));
	global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_list_counter']++;
	$css .= $color !== '' ? 'color:' . esc_attr($color) .';' : '';
	if (trim($icon) == '' || themerex_sc_param_is_inherit($icon)) $icon = $THEMEREX_GLOBALS['sc_list_icon'];
	if (trim($color) == '' || themerex_sc_param_is_inherit($icon_color)) $icon_color = $THEMEREX_GLOBALS['sc_list_icon_color'];
	$output = '<li' . ($id ? ' id="'.esc_attr($id).'"' : '') 
		. ' class="sc_list_item' 
		. (!empty($class) ? ' '.esc_attr($class) : '')
		. ($THEMEREX_GLOBALS['sc_list_counter'] % 2 == 1 ? ' odd' : ' even') 
		. ($THEMEREX_GLOBALS['sc_list_counter'] == 1 ? ' first' : '')  
		. '"' 
		. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
		. ($title ? ' title="'.esc_attr($title).'"' : '') 
		. '>' 
		. (!empty($link) ? '<a href="'.esc_url($link).'"' . (!empty($target) ? ' target="'.esc_attr($target).'"' : '') . '>' : '')
		. ($THEMEREX_GLOBALS['sc_list_style']=='iconed' && $icon!='' ? '<span class="sc_list_icon '.esc_attr($icon).'"'.($icon_color !== '' ? ' style="color:'.esc_attr($icon_color).';"' : '').'></span>' : '')
		. do_shortcode($content)
		. (!empty($link) ? '</a>': '')
		. '</li>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_list_item', $atts, $content);
}

// ---------------------------------- [/trx_list] ---------------------------------------






// ---------------------------------- [trx_number] ---------------------------------------


add_shortcode('trx_number', 'themerex_sc_number');

/*
[trx_number id="unique_id" value="400"]
*/
function themerex_sc_number($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"value" => "",
		"align" => "",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_number' 
				. (!empty($align) ? ' align'.esc_attr($align) : '') 
				. (!empty($class) ? ' '.esc_attr($class) : '') 
				. '"'
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. '>';
	for ($i=0; $i < themerex_strlen($value); $i++) {
		$output .= '<span class="sc_number_item">' . trim(themerex_substr($value, $i, 1)) . '</span>';
	}
	$output .= '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_number', $atts, $content);
}

// ---------------------------------- [/trx_number] ---------------------------------------





// ---------------------------------- [trx_parallax] ---------------------------------------


add_shortcode('trx_parallax', 'themerex_sc_parallax');

/*
[trx_parallax id="unique_id" style="light|dark" dir="up|down" image="" color='']Content for parallax block[/trx_parallax]
*/
function themerex_sc_parallax($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"gap" => "no",
		"dir" => "up",
		"speed" => 0.3,
		"color" => "",
		"bg_tint" => "light",
		"bg_color" => "",
		"bg_image" => "",
		"bg_image_x" => "",
		"bg_image_y" => "",
		"bg_video" => "",
		"bg_video_ratio" => "16:9",
		"bg_overlay" => "",
		"bg_texture" => "",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => "",
		"width" => "",
		"height" => ""
    ), $atts)));
	if ($bg_video!='') {
		$info = pathinfo($bg_video);
		$ext = !empty($info['extension']) ? $info['extension'] : 'mp4';
		$bg_video_ratio = empty($bg_video_ratio) ? "16:9" : str_replace(array('/','\\','-'), ':', $bg_video_ratio);
		$ratio = explode(':', $bg_video_ratio);
		$bg_video_width = !empty($width) && themerex_substr($width, -1) >= '0' && themerex_substr($width, -1) <= '9'  ? $width : 1280;
		$bg_video_height = round($bg_video_width / $ratio[0] * $ratio[1]);
		if (themerex_get_theme_option('use_mediaelement')=='yes')
			wp_enqueue_script('wp-mediaelement');
	}
	if ($bg_image > 0) {
		$attach = wp_get_attachment_image_src( $bg_image, 'full' );
		if (isset($attach[0]) && $attach[0]!='')
			$bg_image = $attach[0];
	}
	$bg_image_x = $bg_image_x!='' ? str_replace('%', '', $bg_image_x).'%' : "50%";
	$bg_image_y = $bg_image_y!='' ? str_replace('%', '', $bg_image_y).'%' : "50%";
	$speed = ($dir=='down' ? -1 : 1) * abs($speed);
	if ($bg_overlay > 0) {
		if ($bg_color=='') $bg_color = apply_filters('themerex_filter_get_theme_bgcolor', '');
		$rgb = themerex_hex2rgb($bg_color);
	}
	$css .= themerex_get_css_position_from_values($top, '!'.($right), $bottom, '!'.($left), $width, $height)
		. ($color !== '' ? 'color:' . esc_attr($color) . ';' : '')
		. ($bg_color !== '' && $bg_overlay==0 ? 'background-color:' . esc_attr($bg_color) . ';' : '')
		;
	$output = (themerex_sc_param_is_on($gap) ? themerex_sc_gap_start() : '')
		. '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
			. ' class="sc_parallax' 
				. ($bg_video!='' ? ' sc_parallax_with_video' : '') 
				. ($bg_tint!='' ? ' bg_tint_'.esc_attr($bg_tint) : '') 
				. (!empty($class) ? ' '.esc_attr($class) : '') 
				. '"' 
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. ' data-parallax-speed="'.esc_attr($speed).'"'
			. ' data-parallax-x-pos="'.esc_attr($bg_image_x).'"'
			. ' data-parallax-y-pos="'.esc_attr($bg_image_y).'"'
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. '>'
		. ($bg_video!='' 
			? '<div class="sc_video_bg_wrapper"><video class="sc_video_bg"'
				. ' width="'.esc_attr($bg_video_width).'" height="'.esc_attr($bg_video_height).'" data-width="'.esc_attr($bg_video_width).'" data-height="'.esc_attr($bg_video_height).'" data-ratio="'.esc_attr($bg_video_ratio).'" data-frame="no"'
				. ' preload="metadata" autoplay="autoplay" loop="loop" src="'.esc_attr($bg_video).'"><source src="'.esc_url($bg_video).'" type="video/'.esc_attr($ext).'"></source></video></div>' 
			: '')
		. '<div class="sc_parallax_content" style="' . ($bg_image !== '' ? 'background-image:url(' . esc_url($bg_image) . '); background-position:'.esc_attr($bg_image_x).' '.esc_attr($bg_image_y).';' : '').'">'
		. ($bg_overlay>0 || $bg_texture!=''
			? '<div class="sc_parallax_overlay'.($bg_texture>0 ? ' texture_bg_'.esc_attr($bg_texture) : '') . '"'
				. ' style="' . ($bg_overlay>0 ? 'background-color:rgba('.(int)$rgb['r'].','.(int)$rgb['g'].','.(int)$rgb['b'].','.min(1, max(0, $bg_overlay)).');' : '')
					. (themerex_strlen($bg_texture)>2 ? 'background-image:url('.esc_url($bg_texture).');' : '')
					. '"'
					. ($bg_overlay > 0 ? ' data-overlay="'.esc_attr($bg_overlay).'" data-bg_color="'.esc_attr($bg_color).'"' : '')
					. '>' 
			: '')
		. do_shortcode($content)
		. ($bg_overlay > 0 || $bg_texture!='' ? '</div>' : '')
		. '</div>'
		. '</div>'
		. (themerex_sc_param_is_on($gap) ? themerex_sc_gap_end() : '');
	return apply_filters('themerex_shortcode_output', $output, 'trx_parallax', $atts, $content);
}
// ---------------------------------- [/trx_parallax] ---------------------------------------




// ---------------------------------- [trx_popup] ---------------------------------------

add_shortcode('trx_popup', 'themerex_sc_popup');

/*
[trx_popup id="unique_id" class="class_name" style="css_styles"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_popup]
*/
function themerex_sc_popup($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
	themerex_enqueue_popup('magnific');
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_popup mfp-with-anim mfp-hide' . ($class ? ' '.esc_attr($class) : '') . '"'
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. '>' 
			. do_shortcode($content) 
			. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_popup', $atts, $content);
}
// ---------------------------------- [/trx_popup] ---------------------------------------






// ---------------------------------- [trx_price] ---------------------------------------


add_shortcode('trx_price', 'themerex_sc_price');

/*
[trx_price id="unique_id" currency="$" money="29.99" period="monthly"]

*/
function themerex_sc_price($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"money" => "",
		"currency" => "$",
		"period" => "",
		"align" => "",
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$output = '';
	if (!empty($money)) {
		$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
		$m = explode('.', str_replace(',', '.', $money));
		$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_price'
				. (!empty($class) ? ' '.esc_attr($class) : '')
				. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
				. '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. '>'
			. '<span class="sc_price_currency">'.($currency).'</span>'
			. '<span class="sc_price_money">'.($m[0]).'</span>'
			. (!empty($m[1]) ? '<span class="sc_price_info">' : '')
			. (!empty($m[1]) ? '<span class="sc_price_penny">'.($m[1]).'</span>' : '')
			. (!empty($period) ? '<span class="sc_price_period">'.($period).'</span>' : (!empty($m[1]) ? '<span class="sc_price_period_empty"></span>' : ''))
			. (!empty($m[1]) ? '</span>' : '')
			. '</div>';
	}
	return apply_filters('themerex_shortcode_output', $output, 'trx_price', $atts, $content);
}

// ---------------------------------- [/trx_price] ---------------------------------------





// ---------------------------------- [trx_price_block] ---------------------------------------


add_shortcode('trx_price_block', 'themerex_sc_price_block');

/*
[trx_price id="unique_id" currency="$" money="29.99" period="monthly"]

*/
function themerex_sc_price_block($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"style" => 1,
		"title" => "",
		"link" => "",
		"link_text" => "",
		"target" => "",
		"icon" => "",
		"money" => "",
		"currency" => "$",
		"period" => "",
		"align" => "",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"width" => "",
		"height" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$output = '';
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left, $width, $height);
	if ($money) $money = do_shortcode('[trx_price money="'.esc_attr($money).'" period="'.esc_attr($period).'"'.($currency ? ' currency="'.esc_attr($currency).'"' : '').']');
	$content = do_shortcode($content);
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_price_block sc_price_block_style_'.max(1, min(3, $style))
					. (!empty($class) ? ' '.esc_attr($class) : '')
					. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
					. '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
				. '>'
			. (!empty($title) ? '<div class="sc_price_block_title">'.($title).'</div>' : '')
			. '<div class="sc_price_block_money">'
				. (!empty($icon) ? '<div class="sc_price_block_icon '.esc_attr($icon).'"></div>' : '')
				. ($money)
			. '</div>'
			. (!empty($content) ? '<div class="sc_price_block_description">'.($content).'</div>' : '')
			. (!empty($link_text) ? '<div class="sc_price_block_link">'. themerex_sc_button( array('link'=> ($link ? esc_url($link) : '#'), 'target' => $target), $link_text).'</div>' : '')
		. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_price_block', $atts, $content);
}

// ---------------------------------- [/trx_price_block] ---------------------------------------




// ---------------------------------- [trx_quote] ---------------------------------------


add_shortcode('trx_quote', 'themerex_sc_quote');

/*
[trx_quote id="unique_id" cite="url" title=""]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/quote]
*/
function themerex_sc_quote($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"title" => "",
		"cite" => "",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"width" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left, $width);
	$cite_param = $cite != '' ? ' cite="'.esc_attr($cite).'"' : '';
	$title = $title=='' ? $cite : $title;
	$content = do_shortcode($content);
	if (themerex_substr($content, 0, 2)!='<p') $content = '<p>' . ($content) . '</p>';
	$output = '<blockquote' 
		. ($id ? ' id="'.esc_attr($id).'"' : '') . ($cite_param) 
		. ' class="sc_quote'. (!empty($class) ? ' '.esc_attr($class) : '').'"' 
		. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
		. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
		. '>'
			. ($content)
			. ($title == '' ? '' : ('<p class="sc_quote_title">' . ($cite!='' ? '<a href="'.esc_url($cite).'">' : '') . ($title) . ($cite!='' ? '</a>' : '') . '</p>'))
		.'</blockquote>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_quote', $atts, $content);
}

// ---------------------------------- [/trx_quote] ---------------------------------------





// ---------------------------------- [trx_reviews] ---------------------------------------

add_shortcode("trx_reviews", "themerex_sc_reviews");
						
/*
[trx_reviews]
*/

function themerex_sc_reviews($atts, $content = null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"align" => "right",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
	$output = themerex_sc_param_is_off(themerex_get_custom_option('show_sidebar_main'))
		? '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_reviews'
						. ($align && $align!='none' ? ' align'.esc_attr($align) : '')
						. ($class ? ' '.esc_attr($class) : '')
						. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
					. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
					. '>'
				. trim(themerex_sc_reviews_placeholder())
				. '</div>'
		: '';
	return apply_filters('themerex_shortcode_output', $output, 'trx_reviews', $atts, $content);
}

function themerex_sc_reviews_placeholder() {
	return '<!-- #TRX_REVIEWS_PLACEHOLDER# -->';
}
	
function themerex_sc_reviews_wrapper($str) {
	$placeholder = themerex_sc_reviews_placeholder();
	if (themerex_strpos($str, $placeholder)!==false) {
		global $THEMEREX_GLOBALS;
		if (!empty($THEMEREX_GLOBALS['reviews_markup'])) {
			$str = str_replace($placeholder, $THEMEREX_GLOBALS['reviews_markup'],	$str);
			$THEMEREX_GLOBALS['reviews_markup'] = '';
		}
	}
	return $str;
}

// ---------------------------------- [/trx_reviews] ---------------------------------------




// ---------------------------------- [trx_search] ---------------------------------------


add_shortcode('trx_search', 'themerex_sc_search');

/*
[trx_search id="unique_id" open="yes|no"]
*/
function themerex_sc_search($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"style" => "regular",
		"open" => "fixed",
		"ajax" => "",
		"title" => __('Search ...', 'additional-tags'),
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
	if (empty($ajax)) $ajax = themerex_get_theme_option('use_ajax_search');
	// Load core messages
	themerex_enqueue_messages();
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') . ' class="search_wrap search_style_'.esc_attr($style)
					. (!themerex_sc_param_is_off($open) ? ' search_opened' : '')
					. ($open=='fixed' ? ' search_fixed' : '')
					. (themerex_sc_param_is_on($ajax) ? ' search_ajax' : '')
					. ($class ? ' '.esc_attr($class) : '')
					. '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
				. ' title="' . __('Open/close search form', 'additional-tags') . '">
					<a href="#" class="search_icon icon-search-2"></a>
					<div class="search_form_wrap">
						<form role="search" method="get" class="search_form" action="' . esc_url( home_url( '/' ) ) . '">
							<button type="submit" class="search_submit icon-zoom-1" title="' . __('Start search', 'additional-tags') . '"></button>
							<input type="text" class="search_field" placeholder="' . esc_attr($title) . '" value="' . esc_attr(get_search_query()) . '" name="s" title="'.esc_attr($title).'" />
						</form>
					</div>
					<div class="search_results widget_area bg_tint_light"><a class="search_results_close icon-delete-2"></a><div class="search_results_content"></div></div>
			</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_search', $atts, $content);
}

// ---------------------------------- [/trx_search] ---------------------------------------




// ---------------------------------- [trx_custom_search] ---------------------------------------


add_shortcode('trx_custom_search', 'themerex_sc_custom_search');

/*
[trx_custom_search id="unique_id" open="yes|no"]
*/
function themerex_sc_custom_search($atts, $content=null){
    if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
        // Individual params
        "style" => "regular",
        "open" => "fixed",
        "ajax" => "",
        "title" => __('Search', 'additional-tags'),
        // Common params
        "id" => "",
        "class" => "",
        "animation" => "",
        "css" => "",
        "top" => "",
        "bottom" => "",
        "left" => "",
        "right" => "",
        "post_type" => "courses",
        "use_tags" => "",
        "tags_title" =>  __('Tags', 'additional-tags'),
        "use_categories" => "",
        "categories_title" => __('Categories', 'additional-tags'),
        "button" =>  __('Search', 'additional-tags'),
        "hide_empty_tax" => false,
    ), $atts)));
    $css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
    if (empty($ajax)) $ajax = themerex_get_theme_option('use_ajax_search');

    if (themerex_sc_param_is_on($hide_empty_tax)) {
        $hide_empty_tax = true;
    }

    if (themerex_sc_param_is_on($use_tags)) {
        $tags_slug =  taxonomy_exists($post_type . '_tag') ? $post_type . '_tag' : false;
        if ($tags_slug) {
            $terms = get_terms( array(
                'taxonomy' => $tags_slug,
                'hide_empty' => $hide_empty_tax,
            ) );
        } else {
            $use_tags = 'no';
        }
    }
    if (themerex_sc_param_is_on($use_categories)) {
        $categories_slug = taxonomy_exists($post_type . '_group') ?
            $post_type . '_group' : ( taxonomy_exists($post_type . '_category') ?
            $post_type . '_category' : ( taxonomy_exists($post_type . '_cat') ?
            $post_type . '_cat' : false ) );

        if ($categories_slug) {
            $categories = get_terms( array(
                'taxonomy' => $categories_slug,
                'hide_empty' => $hide_empty_tax,
            ) );
            $categories_slug .= '_name';
        } else {
            $use_categories = 'no';
        }
    }

    // Load core messages
    themerex_enqueue_messages();

    ob_start();
    require ( themerex_get_file_dir('templates/parts/shortcode-custom-search.php'));
    $output = ob_get_clean();

    return apply_filters('themerex_shortcode_output', $output, 'trx_custom_search', $atts, $content);
}

// ---------------------------------- [/trx_custom_search] ---------------------------------------


// ---------------------------------- [trx_section] and [trx_block] ---------------------------------------

add_shortcode('trx_section', 'themerex_sc_section');
add_shortcode('trx_block', 'themerex_sc_section');

/*
[trx_section id="unique_id" class="class_name" style="css-styles" dedicated="yes|no"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_section]
*/

global $THEMEREX_GLOBALS;
$THEMEREX_GLOBALS['sc_section_dedicated'] = '';

function themerex_sc_section($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"dedicated" => "no",
		"align" => "none",
		"columns" => "none",
		"pan" => "no",
		"scroll" => "no",
		"scroll_dir" => "horizontal",
		"scroll_controls" => "no",
		"color" => "",
		"bg_tint" => "",
		"bg_color" => "",
		"bg_image" => "",
		"bg_overlay" => "",
		"bg_texture" => "",
		"font_size" => "",
		"font_weight" => "",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"width" => "",
		"height" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));

	if ($bg_image > 0) {
		$attach = wp_get_attachment_image_src( $bg_image, 'full' );
		if (isset($attach[0]) && $attach[0]!='')
			$bg_image = $attach[0];
	}

	if ($bg_overlay > 0) {
		if ($bg_color=='') $bg_color = apply_filters('themerex_filter_get_theme_bgcolor', '');
		$rgb = themerex_hex2rgb($bg_color);
	}

	$css .= themerex_get_css_position_from_values('!'.($top), '!'.($right), '!'.($bottom), '!'.($left))
		.($color !== '' ? 'color:' . esc_attr($color) . ';' : '')
		.($bg_color !== '' && $bg_overlay==0 ? 'background-color:' . esc_attr($bg_color) . ';' : '')
		.($bg_image !== '' ? 'background-image:url(' . esc_url($bg_image) . ');' : '')
		.(!themerex_sc_param_is_off($pan) ? 'position:relative;' : '')
		.($font_size != '' ? 'font-size:' . esc_attr(themerex_prepare_css_value($font_size)) . '; line-height: 1.3em;' : '')
		.($font_weight != '' && $font_weight != 'inherit' ? 'font-weight:' . esc_attr($font_weight) . ';' : '');
	$css_dim = themerex_get_css_position_from_values('', '', '', '', $width, $height);
	if ($bg_image == '' && $bg_color == '' && $bg_overlay==0 && $bg_texture==0 && themerex_strlen($bg_texture)<2) $css .= $css_dim;
	
	$width  = themerex_prepare_css_value($width);
	$height = themerex_prepare_css_value($height);

	if ((!themerex_sc_param_is_off($scroll) || !themerex_sc_param_is_off($pan)) && empty($id)) $id = 'sc_section_'.str_replace('.', '', mt_rand());

	if (!themerex_sc_param_is_off($scroll)) themerex_enqueue_slider();

	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_section' 
				. ($class ? ' ' . esc_attr($class) : '') 
				. ($bg_tint ? ' bg_tint_' . esc_attr($bg_tint) : '') 
				. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
				. (!empty($columns) && $columns!='none' ? ' column-'.esc_attr($columns) : '') 
				. (themerex_sc_param_is_on($scroll) && !themerex_sc_param_is_off($scroll_controls) ? ' sc_scroll_controls sc_scroll_controls_'.esc_attr($scroll_dir).' sc_scroll_controls_type_'.esc_attr($scroll_controls) : '')
				. '"'
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '').'>' 
			. ($bg_image !== '' || $bg_color !== '' || $bg_overlay>0 || $bg_texture>0 || themerex_strlen($bg_texture)>2
				? '<div class="sc_section_overlay'.($bg_texture>0 ? ' texture_bg_'.esc_attr($bg_texture) : '') . '"'
					. ' style="' . ($bg_overlay>0 ? 'background-color:rgba('.(int)$rgb['r'].','.(int)$rgb['g'].','.(int)$rgb['b'].','.min(1, max(0, $bg_overlay)).');' : '')
						. (themerex_strlen($bg_texture)>2 ? 'background-image:url('.esc_url($bg_texture).');' : '')
						. '"'
						. ($bg_overlay > 0 ? ' data-overlay="'.esc_attr($bg_overlay).'" data-bg_color="'.esc_attr($bg_color).'"' : '')
						. '>'
						. '<div class="sc_section_content"'
							. ($css_dim)
							. '>'
				: '')
			. (themerex_sc_param_is_on($scroll) 
				? '<div id="'.esc_attr($id).'_scroll" class="sc_scroll sc_scroll_'.esc_attr($scroll_dir).' swiper-slider-container scroll-container"'
					. ' style="'.($height != '' ? 'height:'.esc_attr($height).';' : '') . ($width != '' ? 'width:'.esc_attr($width).';' : '').'"'
					. '>'
					. '<div class="sc_scroll_wrapper swiper-wrapper">' 
					. '<div class="sc_scroll_slide swiper-slide">' 
				: '')
			. (themerex_sc_param_is_on($pan) 
				? '<div id="'.esc_attr($id).'_pan" class="sc_pan sc_pan_'.esc_attr($scroll_dir).'">' 
				: '')
			. do_shortcode($content)
			. (themerex_sc_param_is_on($pan) ? '</div>' : '')
			. (themerex_sc_param_is_on($scroll) 
				? '</div></div><div id="'.esc_attr($id).'_scroll_bar" class="sc_scroll_bar sc_scroll_bar_'.esc_attr($scroll_dir).' '.esc_attr($id).'_scroll_bar"></div></div>'
					. (!themerex_sc_param_is_off($scroll_controls) ? '<div class="sc_scroll_controls_wrap"><a class="sc_scroll_prev" href="#"></a><a class="sc_scroll_next" href="#"></a></div>' : '')
				: '')
			. ($bg_image !== '' || $bg_color !== '' || $bg_overlay > 0 || $bg_texture>0 || themerex_strlen($bg_texture)>2 ? '</div></div>' : '')
		. '</div>';
	if (themerex_sc_param_is_on($dedicated)) {
	    global $THEMEREX_GLOBALS;
		if ($THEMEREX_GLOBALS['sc_section_dedicated']=='') {
			$THEMEREX_GLOBALS['sc_section_dedicated'] = $output;
		}
		$output = '';
	}
	return apply_filters('themerex_shortcode_output', $output, 'trx_section', $atts, $content);
}

function themerex_sc_clear_dedicated_content() {
	global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_section_dedicated'] = '';
}

function themerex_sc_get_dedicated_content() {
	global $THEMEREX_GLOBALS;
	return $THEMEREX_GLOBALS['sc_section_dedicated'];
}
// ---------------------------------- [/trx_section] ---------------------------------------





// ---------------------------------- [trx_skills] ---------------------------------------


add_shortcode('trx_skills', 'themerex_sc_skills');

/*
[trx_skills id="unique_id" type="bar|pie|arc|counter" dir="horizontal|vertical" layout="rows|columns" count="" max_value="100" align="left|right"]
	[trx_skills_item title="Scelerisque pid" value="50%"]
	[trx_skills_item title="Scelerisque pid" value="50%"]
	[trx_skills_item title="Scelerisque pid" value="50%"]
[/trx_skills]
*/
function themerex_sc_skills($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"max_value" => "100",
		"type" => "bar",
		"layout" => "",
		"dir" => "",
		"pie_compact" => "on",
		"pie_cutout" => 0,
		"style" => "1",
		"columns" => "",
		"align" => "",
		"color" => "",
		"bg_color" => "",
		"border_color" => "",
		"title" => "",
		"subtitle" => __("Skills", 'additional-tags'),
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"width" => "",
		"height" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
    global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_skills_counter'] = 0;
	$THEMEREX_GLOBALS['sc_skills_columns'] = 0;
	$THEMEREX_GLOBALS['sc_skills_height']  = 0;
	$THEMEREX_GLOBALS['sc_skills_type']    = $type;
	$THEMEREX_GLOBALS['sc_skills_pie_compact'] = $pie_compact;
	$THEMEREX_GLOBALS['sc_skills_pie_cutout']  = max(0, min(100, $pie_cutout));
	$THEMEREX_GLOBALS['sc_skills_color']   = $color;
	$THEMEREX_GLOBALS['sc_skills_bg_color']= $bg_color;
	$THEMEREX_GLOBALS['sc_skills_border_color']= $border_color;
	$THEMEREX_GLOBALS['sc_skills_legend']  = '';
	$THEMEREX_GLOBALS['sc_skills_data']    = '';
	themerex_enqueue_diagram($type);
	if ($type!='arc') {
		if ($layout=='' || ($layout=='columns' && $columns<1)) $layout = 'rows';
		if ($layout=='columns') $THEMEREX_GLOBALS['sc_skills_columns'] = $columns;
		if ($type=='bar') {
			if ($dir == '') $dir = 'horizontal';
			if ($dir == 'vertical' && $height < 1) $height = 300;
		}
	}
	if (empty($id)) $id = 'sc_skills_diagram_'.str_replace('.','',mt_rand());
	if ($max_value < 1) $max_value = 100;
	if ($style) {
		$style = max(1, min(4, $style));
		$THEMEREX_GLOBALS['sc_skills_style'] = $style;
	}
	$THEMEREX_GLOBALS['sc_skills_max'] = $max_value;
	$THEMEREX_GLOBALS['sc_skills_dir'] = $dir;
	$THEMEREX_GLOBALS['sc_skills_height'] = themerex_prepare_css_value($height);
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left, $width, $height);
	$content = do_shortcode($content);
	$output = ($type!='arc' && ($type!='pie' || !themerex_sc_param_is_on($pie_compact)) && $title!='' ? '<h3 class="sc_skills_title">'.($title).'</h3>' : '')
			. '<div id="'.esc_attr($id).'"' 
				. ' class="sc_skills sc_skills_' . esc_attr($type) 
					. ($type=='bar' ? ' sc_skills_'.esc_attr($dir) : '') 
					. ($type=='pie' ? ' sc_skills_compact_'.esc_attr($pie_compact) : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '') 
					. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
					. '"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
				. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
				. ' data-type="'.esc_attr($type).'"'
				. ' data-subtitle="'.esc_attr($subtitle).'"'
				. ($type=='bar' ? ' data-dir="'.esc_attr($dir).'"' : '')
			. '>'
				. ($layout == 'columns' ? '<div class="columns_wrap sc_skills_'.esc_attr($layout).' sc_skills_columns_'.esc_attr($columns).'">' : '')
				. ($type=='arc' 
					? ('<div class="sc_skills_legend">'.($title!='' ? '<h6 class="sc_skills_title">'.($title).'</h6>' : '').($THEMEREX_GLOBALS['sc_skills_legend']).'</div>'
						. '<div id="'.esc_attr($id).'_diagram" class="sc_skills_arc_canvas"></div>'
						. '<div class="sc_skills_data" style="display:none;">' . ($THEMEREX_GLOBALS['sc_skills_data']) . '</div>'
					  )
					: '')
				. ($type=='pie' && themerex_sc_param_is_on($pie_compact)
					? ('<div class="sc_skills_legend">'.($title!='' ? '<h6 class="sc_skills_title">'.($title).'</h6>' : '').($THEMEREX_GLOBALS['sc_skills_legend']).'</div>'
						. '<div id="'.esc_attr($id).'_pie" class="sc_skills_item">'
							. '<canvas id="'.esc_attr($id).'_pie" class="sc_skills_pie_canvas"></canvas>'
							. '<div class="sc_skills_data" style="display:none;">' . ($THEMEREX_GLOBALS['sc_skills_data']) . '</div>'
						. '</div>'
					  )
					: '')
				. ($content)
				. ($layout == 'columns' ? '</div>' : '')
			. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_skills', $atts, $content);
}


add_shortcode('trx_skills_item', 'themerex_sc_skills_item');

function themerex_sc_skills_item($atts, $content=null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	extract(themerex_sc_html_decode(shortcode_atts( array(
		// Individual params
		"title" => "",
		"value" => "",
		"color" => "",
		"bg_color" => "",
		"border_color" => "",
		"style" => "",
		// Common params
		"id" => "",
		"class" => "",
		"css" => ""
	), $atts)));
	global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_skills_counter']++;
	$ed = themerex_substr($value, -1)=='%' ? '%' : '';
	$value = str_replace('%', '', $value);
	if ($THEMEREX_GLOBALS['sc_skills_max'] < $value) $THEMEREX_GLOBALS['sc_skills_max'] = $value;
	$percent = round($value / $THEMEREX_GLOBALS['sc_skills_max'] * 100);
	$start = 0;
	$stop = $value;
	$steps = 100;
	$step = max(1, round($THEMEREX_GLOBALS['sc_skills_max']/$steps));
	$speed = mt_rand(10,40);
	$animation = round(($stop - $start) / $step * $speed);
	$title_block = '<div class="sc_skills_info"><div class="sc_skills_label">' . ($title) . '</div></div>';
	$old_color = $color;
	if (empty($color)) $color = $THEMEREX_GLOBALS['sc_skills_color'];
	if (empty($color)) $color = themerex_get_custom_option('link_color');
	$color = apply_filters('themerex_filter_get_link_color', $color);
	if (empty($bg_color)) $bg_color = $THEMEREX_GLOBALS['sc_skills_bg_color'];
	if (empty($bg_color)) $bg_color = '#f4f7f9';
	if (empty($border_color)) $border_color = $THEMEREX_GLOBALS['sc_skills_border_color'];
	if (empty($border_color)) $border_color = '#ffffff';
	if (empty($style)) $style = $THEMEREX_GLOBALS['sc_skills_style'];
	$style = max(1, min(4, $style));
	$output = '';
	if ($THEMEREX_GLOBALS['sc_skills_type'] == 'arc' || ($THEMEREX_GLOBALS['sc_skills_type'] == 'pie' && themerex_sc_param_is_on($THEMEREX_GLOBALS['sc_skills_pie_compact']))) {
		if ($THEMEREX_GLOBALS['sc_skills_type'] == 'arc' && empty($old_color)) {
			$rgb = themerex_hex2rgb($color);
			$color = 'rgba('.(int)$rgb['r'].','.(int)$rgb['g'].','.(int)$rgb['b'].','.(1 - 0.1*($THEMEREX_GLOBALS['sc_skills_counter']-1)).')';
		}
		$THEMEREX_GLOBALS['sc_skills_legend'] .= '<div class="sc_skills_legend_item"><span class="sc_skills_legend_marker" style="background-color:'.esc_attr($color).'"></span><span class="sc_skills_legend_title">' . ($title) . '</span><span class="sc_skills_legend_value">' . ($value) . '</span></div>';
		$THEMEREX_GLOBALS['sc_skills_data'] .= '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
			. ' class="'.esc_attr($THEMEREX_GLOBALS['sc_skills_type']).'"'
			. ($THEMEREX_GLOBALS['sc_skills_type']=='pie'
				? ( ' data-start="'.esc_attr($start).'"'
					. ' data-stop="'.esc_attr($stop).'"'
					. ' data-step="'.esc_attr($step).'"'
					. ' data-steps="'.esc_attr($steps).'"'
					. ' data-max="'.esc_attr($THEMEREX_GLOBALS['sc_skills_max']).'"'
					. ' data-speed="'.esc_attr($speed).'"'
					. ' data-duration="'.esc_attr($animation).'"'
					. ' data-color="'.esc_attr($color).'"'
					. ' data-bg_color="'.esc_attr($bg_color).'"'
					. ' data-border_color="'.esc_attr($border_color).'"'
					. ' data-cutout="'.esc_attr($THEMEREX_GLOBALS['sc_skills_pie_cutout']).'"'
					. ' data-easing="easeOutCirc"'
					. ' data-ed="'.esc_attr($ed).'"'
					)
				: '')
			. '><input type="hidden" class="text" value="'.esc_attr($title).'" /><input type="hidden" class="percent" value="'.esc_attr($percent).'" /><input type="hidden" class="color" value="'.esc_attr($color).'" /></div>';
	} else {
		$output .= ($THEMEREX_GLOBALS['sc_skills_columns'] > 0 ? '<div class="sc_skills_column column-1_'.esc_attr($THEMEREX_GLOBALS['sc_skills_columns']).'">' : '')
				. ($THEMEREX_GLOBALS['sc_skills_type']=='bar' && $THEMEREX_GLOBALS['sc_skills_dir']=='horizontal' ? $title_block : '')
				. '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_skills_item' . ($style ? ' sc_skills_style_'.esc_attr($style) : '') 
						. (!empty($class) ? ' '.esc_attr($class) : '')
						. ($THEMEREX_GLOBALS['sc_skills_counter'] % 2 == 1 ? ' odd' : ' even') 
						. ($THEMEREX_GLOBALS['sc_skills_counter'] == 1 ? ' first' : '') 
						. '"'
					. ($THEMEREX_GLOBALS['sc_skills_height'] !='' || $css ? ' style="height: '.esc_attr($THEMEREX_GLOBALS['sc_skills_height']).';'.($css).'"' : '')
				. '>';
		if (in_array($THEMEREX_GLOBALS['sc_skills_type'], array('bar', 'counter'))) {
			$output .= '<div class="sc_skills_count"' . ($THEMEREX_GLOBALS['sc_skills_type']=='bar' && $color ? ' style="background-color:' . esc_attr($color) . '; border-color:' . esc_attr($color) . '"' : '') . '>'
						. '<div class="sc_skills_total"'
							. ' data-start="'.esc_attr($start).'"'
							. ' data-stop="'.esc_attr($stop).'"'
							. ' data-step="'.esc_attr($step).'"'
							. ' data-max="'.esc_attr($THEMEREX_GLOBALS['sc_skills_max']).'"'
							. ' data-speed="'.esc_attr($speed).'"'
							. ' data-duration="'.esc_attr($animation).'"'
							. ' data-ed="'.esc_attr($ed).'">'
							. ($start) . ($ed)
						.'</div>'
					. '</div>';
		} else if ($THEMEREX_GLOBALS['sc_skills_type']=='pie') {
			if (empty($id)) $id = 'sc_skills_canvas_'.str_replace('.','',mt_rand());
			$output .= '<canvas id="'.esc_attr($id).'"></canvas>'
				. '<div class="sc_skills_total"'
					. ' data-start="'.esc_attr($start).'"'
					. ' data-stop="'.esc_attr($stop).'"'
					. ' data-step="'.esc_attr($step).'"'
					. ' data-steps="'.esc_attr($steps).'"'
					. ' data-max="'.esc_attr($THEMEREX_GLOBALS['sc_skills_max']).'"'
					. ' data-speed="'.esc_attr($speed).'"'
					. ' data-duration="'.esc_attr($animation).'"'
					. ' data-color="'.esc_attr($color).'"'
					. ' data-bg_color="'.esc_attr($bg_color).'"'
					. ' data-border_color="'.esc_attr($border_color).'"'
					. ' data-cutout="'.esc_attr($THEMEREX_GLOBALS['sc_skills_pie_cutout']).'"'
					. ' data-easing="easeOutCirc"'
					. ' data-ed="'.esc_attr($ed).'">'
					. ($start) . ($ed)
				.'</div>';
		}
		$output .= 
				  ($THEMEREX_GLOBALS['sc_skills_type']=='counter' ? $title_block : '')
				. '</div>'
				. ($THEMEREX_GLOBALS['sc_skills_type']=='bar' && $THEMEREX_GLOBALS['sc_skills_dir']=='vertical' || $THEMEREX_GLOBALS['sc_skills_type'] == 'pie' ? $title_block : '')
				. ($THEMEREX_GLOBALS['sc_skills_columns'] > 0 ? '</div>' : '');
	}
	return apply_filters('themerex_shortcode_output', $output, 'trx_skills_item', $atts, $content);
}

// ---------------------------------- [/trx_skills] ---------------------------------------






// ---------------------------------- [trx_slider] ---------------------------------------

add_shortcode('trx_slider', 'themerex_sc_slider');

/*
[trx_slider id="unique_id" engine="revo|royal|flex|swiper|chop" alias="revolution_slider_alias|royal_slider_id" titles="no|slide|fixed" cat="id|slug" count="posts_number" ids="comma_separated_id_list" offset="" width="" height="" align="" top="" bottom=""]
[trx_slider_item src="image_url"]
[/trx_slider]
*/

function themerex_sc_slider($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"engine" => themerex_get_custom_option('substitute_slider_engine'),
		"custom" => "no",
		"alias" => "",
		"post_type" => "post",
		"ids" => "",
		"cat" => "",
		"count" => "0",
		"offset" => "",
		"orderby" => "date",
		"order" => 'desc',
		"controls" => "no",
		"pagination" => "no",
		"titles" => "no",
		"descriptions" => themerex_get_custom_option('slider_info_descriptions'),
		"links" => "no",
		"align" => "",
		"interval" => "",
		"date_format" => "",
		"crop" => "yes",
		"autoheight" => "no",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"width" => "",
		"height" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));

	if (empty($width) && $pagination!='full') $width = "100%";
	if (empty($height) && ($pagination=='full' || $pagination=='over')) $height = 250;
	if (!empty($height) && themerex_sc_param_is_on($autoheight)) $autoheight = "off";
	if (empty($interval)) $interval = mt_rand(5000, 10000);
	
	global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_slider_engine'] = $engine;
	$THEMEREX_GLOBALS['sc_slider_width']  = themerex_prepare_css_value($width);
	$THEMEREX_GLOBALS['sc_slider_height'] = themerex_prepare_css_value($height);
	$THEMEREX_GLOBALS['sc_slider_links']  = themerex_sc_param_is_on($links);
	$THEMEREX_GLOBALS['sc_slider_bg_image'] = false;
	$THEMEREX_GLOBALS['sc_slider_crop_image'] = $crop;

	if (empty($id)) $id = "sc_slider_".str_replace('.', '', mt_rand());
	
	$ms = themerex_get_css_position_from_values($top, $right, $bottom, $left);
	$ws = themerex_get_css_position_from_values('', '', '', '', $width);
	$hs = themerex_get_css_position_from_values('', '', '', '', '', $height);

	$css .= (!in_array($pagination, array('full', 'over')) ? $ms : '') . ($hs) . ($ws);
	
	if ($engine!='swiper' && in_array($pagination, array('full', 'over'))) $pagination = 'yes';
	
	$output = (in_array($pagination, array('full', 'over')) 
				? '<div class="sc_slider_pagination_area sc_slider_pagination_'.esc_attr($pagination)
						. ($align!='' && $align!='none' ? ' align'.esc_attr($align) : '')
						. '"'
					. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
					. (($ms).($hs) ? ' style="'.esc_attr(($ms).($hs)).'"' : '') 
					.'>' 
				: '')
			. '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_slider sc_slider_' . esc_attr($engine)
				. ($engine=='swiper' ? ' swiper-slider-container' : '')
				. (!empty($class) ? ' '.esc_attr($class) : '')
				. (themerex_sc_param_is_on($autoheight) ? ' sc_slider_height_auto' : '')
				. ($hs ? ' sc_slider_height_fixed' : '')
				. (themerex_sc_param_is_on($controls) ? ' sc_slider_controls' : ' sc_slider_nocontrols')
				. (themerex_sc_param_is_on($pagination) ? ' sc_slider_pagination' : ' sc_slider_nopagination')
				. (!in_array($pagination, array('full', 'over')) && $align!='' && $align!='none' ? ' align'.esc_attr($align) : '')
				. '"'
			. (!in_array($pagination, array('full', 'over')) && !themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. (!empty($width) && themerex_strpos($width, '%')===false ? ' data-old-width="' . esc_attr($width) . '"' : '')
			. (!empty($height) && themerex_strpos($height, '%')===false ? ' data-old-height="' . esc_attr($height) . '"' : '')
			. ((int) $interval > 0 ? ' data-interval="'.esc_attr($interval).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
		. '>';

	themerex_enqueue_slider($engine);

	if ($engine=='revo') {
		if (themerex_exists_revslider() && !empty($alias))
			$output .= do_shortcode('[rev_slider '.esc_attr($alias).']');
		else
			$output = '';
	} else if ($engine=='swiper') {
		
		$caption = '';

		$output .= '<div class="slides'
			.($engine=='swiper' ? ' swiper-wrapper' : '').'"'
			.($engine=='swiper' && $THEMEREX_GLOBALS['sc_slider_bg_image'] ? ' style="'.esc_attr($hs).'"' : '')
			.'>';

		$content = do_shortcode($content);
		
		if (themerex_sc_param_is_on($custom) && $content) {
			$output .= $content;
		} else {
			global $post;
	
			if (!empty($ids)) {
				$posts = explode(',', $ids);
				$count = count($posts);
			}
		
			$args = array(
				'post_type' => 'post',
				'post_status' => 'publish',
				'posts_per_page' => $count,
				'ignore_sticky_posts' => true,
				'order' => $order=='asc' ? 'asc' : 'desc',
			);
	
			if ($offset > 0 && empty($ids)) {
				$args['offset'] = $offset;
			}
	
			$args = themerex_query_add_sort_order($args, $orderby, $order);
			$args = themerex_query_add_filters($args, 'thumbs');
			$args = themerex_query_add_posts_and_cats($args, $ids, $post_type, $cat);

			$query = new WP_Query( $args );

			$post_number = 0;
			$pagination_items = '';
			$show_image 	= 1;
			$show_types 	= 0;
			$show_date 		= 1;
			$show_author 	= 0;
			$show_links 	= 0;
			$show_counters	= 'views';	//comments | rating
			
			while ( $query->have_posts() ) { 
				$query->the_post();
				$post_number++;
				$post_id = get_the_ID();
				$post_type = get_post_type();
				$post_title = get_the_title();
				$post_link = get_permalink();
				$post_date = get_the_date(!empty($date_format) ? $date_format : 'd.m.y');
				$post_attachment = wp_get_attachment_url(get_post_thumbnail_id($post_id));
				if (themerex_sc_param_is_on($crop)) {
					$post_attachment = $THEMEREX_GLOBALS['sc_slider_bg_image']
						? themerex_get_resized_image_url($post_attachment, !empty($width) && (float) $width.' ' == $width.' ' ? $width : null, !empty($height) && (float) $height.' ' == $height.' ' ? $height : null)
						: themerex_get_resized_image_tag($post_attachment, !empty($width) && (float) $width.' ' == $width.' ' ? $width : null, !empty($height) && (float) $height.' ' == $height.' ' ? $height : null);
				} else if (!$THEMEREX_GLOBALS['sc_slider_bg_image']) {
					$post_attachment = '<img src="'.esc_url($post_attachment).'" alt="">';
				}
				$post_accent_color = '';
				$post_category = '';
				$post_category_link = '';

				if (in_array($pagination, array('full', 'over'))) {
					$old_output = $output;
					$output = '';
					require(themerex_get_file_dir('templates/parts/widgets-posts.php'));
					$pagination_items .= $output;
					$output = $old_output;
				}
				$output .= '<div' 
					. ' class="'.esc_attr($engine).'-slide"'
					. ' data-style="'.esc_attr(($ws).($hs)).'"'
					. ' style="'
						. ($THEMEREX_GLOBALS['sc_slider_bg_image'] ? 'background-image:url(' . esc_url($post_attachment) . ');' : '') . ($ws) . ($hs)
						. '"'
					. '>' 
					. (themerex_sc_param_is_on($links) ? '<a href="'.esc_url($post_link).'" title="'.esc_attr($post_title).'">' : '')
					. (!$THEMEREX_GLOBALS['sc_slider_bg_image'] ? $post_attachment : '')
					;
				$caption = $engine=='swiper' ? '' : $caption;
				if (!themerex_sc_param_is_off($titles)) {
					$post_hover_bg  = themerex_get_custom_option('link_color', null, $post_id);
					$post_bg = '';
					if ($post_hover_bg!='' && !themerex_is_inherit_option($post_hover_bg)) {
						$rgb = themerex_hex2rgb($post_hover_bg);
						$post_hover_ie = str_replace('#', '', $post_hover_bg);
						$post_bg = "background-color: rgba({$rgb['r']},{$rgb['g']},{$rgb['b']},0.8);";
					}
					$caption .= '<div class="sc_slider_info' . ($titles=='fixed' ? ' sc_slider_info_fixed' : '') . ($engine=='swiper' ? ' content-slide' : '') . '"'.($post_bg!='' ? ' style="'.esc_attr($post_bg).'"' : '').'>';
					$post_descr = themerex_get_post_excerpt();
					if (themerex_get_custom_option("slider_info_category")=='yes') { // || empty($cat)) {
						// Get all post's categories
						$post_tax = themerex_get_taxonomy_categories_by_post_type($post_type);
						if (!empty($post_tax)) {
							$post_terms = themerex_get_terms_by_post_id(array('post_id'=>$post_id, 'taxonomy'=>$post_tax));
							if (!empty($post_terms[$post_tax])) {
								if (!empty($post_terms[$post_tax]->closest_parent)) {
									$post_category = $post_terms[$post_tax]->closest_parent->name;
									$post_category_link = $post_terms[$post_tax]->closest_parent->link;
									//$post_accent_color = themerex_taxonomy_get_inherited_property($post_tax, $post_terms[$post_tax]->closest_parent->term_id, 'link_color');
								}
								/*
								if ($post_accent_color == '' && !empty($post_terms[$post_tax]->terms)) {
									for ($i = 0; $i < count($post_terms[$post_tax]->terms); $i++) {
										$post_accent_color = themerex_taxonomy_get_inherited_property($post_tax, $post_terms[$post_tax]->terms[$i]->term_id, 'link_color');
										if ($post_accent_color != '') break;
									}
								}
								*/
								if ($post_category!='') {
									$caption .= '<div class="sc_slider_category"'.(themerex_substr($post_accent_color, 0, 1)=='#' ? ' style="background-color: '.esc_attr($post_accent_color).'"' : '').'><a href="'.esc_url($post_category_link).'">'.($post_category).'</a></div>';
								}
							}
						}
					}
					$output_reviews = '';
					if (themerex_get_custom_option('show_reviews')=='yes' && themerex_get_custom_option('slider_info_reviews')=='yes') {
						$avg_author = themerex_reviews_marks_to_display(get_post_meta($post_id, 'reviews_avg'.((themerex_get_theme_option('reviews_first')=='author' && $orderby != 'users_rating') || $orderby == 'author_rating' ? '' : '2'), true));
						if ($avg_author > 0) {
							$output_reviews .= '<div class="sc_slider_reviews post_rating reviews_summary blog_reviews' . (themerex_get_custom_option("slider_info_category")=='yes' ? ' after_category' : '') . '">'
								. '<div class="criteria_summary criteria_row">' . trim(themerex_reviews_get_summary_stars($avg_author, false, false, 5)) . '</div>'
								. '</div>';
						}
					}
					if (themerex_get_custom_option("slider_info_category")=='yes') $caption .= $output_reviews;
					$caption .= '<h3 class="sc_slider_subtitle"><a href="'.esc_url($post_link).'">'.($post_title).'</a></h3>';
					if (themerex_get_custom_option("slider_info_category")!='yes') $caption .= $output_reviews;
					if ($descriptions > 0) {
						$caption .= '<div class="sc_slider_descr">'.trim(themerex_strshort($post_descr, $descriptions)).'</div>';
					}
					$caption .= '</div>';
				}
				$output .= ($engine=='swiper' ? $caption : '') . (themerex_sc_param_is_on($links) ? '</a>' : '' ) . '</div>';
			}
			wp_reset_postdata();
		}

		$output .= '</div>';
		if ($engine=='swiper') {
			if (themerex_sc_param_is_on($controls))
				$output .= '<div class="sc_slider_controls_wrap"><a class="sc_slider_prev" href="#"></a><a class="sc_slider_next" href="#"></a></div>';
			if (themerex_sc_param_is_on($pagination))
				$output .= '<div class="sc_slider_pagination_wrap"></div>';
		}
	
	} else
		$output = '';
	
	if (!empty($output)) {
		$output .= '</div>';
		if (!empty($pagination_items)) {
			$output .= '
				<div class="sc_slider_pagination widget_area"'.($hs ? ' style="'.esc_attr($hs).'"' : '').'>
					<div id="'.esc_attr($id).'_scroll" class="sc_scroll sc_scroll_vertical swiper-slider-container scroll-container"'.($hs ? ' style="'.esc_attr($hs).'"' : '').'>
						<div class="sc_scroll_wrapper swiper-wrapper">
							<div class="sc_scroll_slide swiper-slide">
								'.($pagination_items).'
							</div>
						</div>
						<div id="'.esc_attr($id).'_scroll_bar" class="sc_scroll_bar sc_scroll_bar_vertical"></div>
					</div>
				</div>';
			$output .= '</div>';
		}
	}

	return apply_filters('themerex_shortcode_output', $output, 'trx_slider', $atts, $content);
}


add_shortcode('trx_slider_item', 'themerex_sc_slider_item');

function themerex_sc_slider_item($atts, $content=null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	extract(themerex_sc_html_decode(shortcode_atts( array(
		// Individual params
		"src" => "",
		"url" => "",
		// Common params
		"id" => "",
		"class" => "",
		"css" => ""
	), $atts)));
	global $THEMEREX_GLOBALS;
	$src = $src!='' ? $src : $url;
	if ($src > 0) {
		$attach = wp_get_attachment_image_src( $src, 'full' );
		if (isset($attach[0]) && $attach[0]!='')
			$src = $attach[0];
	}

	if ($src && themerex_sc_param_is_on($THEMEREX_GLOBALS['sc_slider_crop_image'])) {
		$src = $THEMEREX_GLOBALS['sc_slider_bg_image']
			? themerex_get_resized_image_url($src, !empty($THEMEREX_GLOBALS['sc_slider_width']) && themerex_strpos($THEMEREX_GLOBALS['sc_slider_width'], '%')===false ? $THEMEREX_GLOBALS['sc_slider_width'] : null, !empty($THEMEREX_GLOBALS['sc_slider_height']) && themerex_strpos($THEMEREX_GLOBALS['sc_slider_height'], '%')===false ? $THEMEREX_GLOBALS['sc_slider_height'] : null)
			: themerex_get_resized_image_tag($src, !empty($THEMEREX_GLOBALS['sc_slider_width']) && themerex_strpos($THEMEREX_GLOBALS['sc_slider_width'], '%')===false ? $THEMEREX_GLOBALS['sc_slider_width'] : null, !empty($THEMEREX_GLOBALS['sc_slider_height']) && themerex_strpos($THEMEREX_GLOBALS['sc_slider_height'], '%')===false ? $THEMEREX_GLOBALS['sc_slider_height'] : null);
	} else if ($src && !$THEMEREX_GLOBALS['sc_slider_bg_image']) {
		$src = '<img src="'.esc_url($src).'" alt="">';
	}

	$css .= ($THEMEREX_GLOBALS['sc_slider_bg_image'] ? 'background-image:url(' . esc_url($src) . ');' : '')
			. (!empty($THEMEREX_GLOBALS['sc_slider_width'])  ? 'width:'  . esc_attr($THEMEREX_GLOBALS['sc_slider_width'])  . ';' : '')
			. (!empty($THEMEREX_GLOBALS['sc_slider_height']) ? 'height:' . esc_attr($THEMEREX_GLOBALS['sc_slider_height']) . ';' : '');

	$content = do_shortcode($content);

	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '').' class="'.esc_attr($THEMEREX_GLOBALS['sc_slider_engine']).'-slide' . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
			. ($css ? ' style="'.esc_attr($css).'"' : '')
			.'>' 
			. ($src && themerex_sc_param_is_on($THEMEREX_GLOBALS['sc_slider_links']) ? '<a href="'.esc_url($src).'">' : '')
			. ($src && !$THEMEREX_GLOBALS['sc_slider_bg_image'] ? $src : $content)
			. ($src && themerex_sc_param_is_on($THEMEREX_GLOBALS['sc_slider_links']) ? '</a>' : '')
		. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_slider_item', $atts, $content);
}
// ---------------------------------- [/trx_slider] ---------------------------------------





// ---------------------------------- [trx_socials] ---------------------------------------


add_shortcode('trx_socials', 'themerex_sc_socials');

/*
[trx_socials id="unique_id" size="small"]
	[trx_social_item name="facebook" url="profile url" icon="path for the icon"]
	[trx_social_item name="twitter" url="profile url"]
[/trx_socials]
*/
function themerex_sc_socials($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"size" => "small",	// tiny | small | large
		"socials" => "",
		"custom" => "no",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
	global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_social_icons'] = false;
	if (!empty($socials)) {
		$allowed = explode('|', $socials);
		$list = array();
		for ($i=0; $i<count($allowed); $i++) {
			$s = explode('=', $allowed[$i]);
			if (!empty($s[1])) {
				$list[] = array(
					'icon'	=> themerex_get_socials_url($s[0]),
					'url'	=> $s[1]
					);
			}
		}
		if (count($list) > 0) $THEMEREX_GLOBALS['sc_social_icons'] = $list;
	} else if (themerex_sc_param_is_off($custom))
		$content = do_shortcode($content);
	if ($THEMEREX_GLOBALS['sc_social_icons']===false) $THEMEREX_GLOBALS['sc_social_icons'] = themerex_get_custom_option('social_icons');
	$output = themerex_prepare_socials($THEMEREX_GLOBALS['sc_social_icons']);
	$output = $output
		? '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_socials sc_socials_size_' . esc_attr($size) . (!empty($class) ? ' '.esc_attr($class) : '') . '"' 
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. '>' 
			. ($output)
			. '</div>'
		: '';
	return apply_filters('themerex_shortcode_output', $output, 'trx_socials', $atts, $content);
}



add_shortcode('trx_social_item', 'themerex_sc_social_item');

function themerex_sc_social_item($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"name" => "",
		"url" => "",
		"icon" => ""
    ), $atts)));
	global $THEMEREX_GLOBALS;
	if (!empty($name) && empty($icon) && file_exists(themerex_get_socials_dir($name.'.png')))
		$icon = themerex_get_socials_url($name.'.png');
	if (!empty($icon) && !empty($url)) {
		if ($THEMEREX_GLOBALS['sc_social_icons']===false) $THEMEREX_GLOBALS['sc_social_icons'] = array();
		$THEMEREX_GLOBALS['sc_social_icons'][] = array(
			'icon' => $icon,
			'url' => $url
		);
	}
	return '';
}

// ---------------------------------- [/trx_socials] ---------------------------------------





// ---------------------------------- [trx_table] ---------------------------------------


add_shortcode('trx_table', 'themerex_sc_table');

/*
[trx_table id="unique_id" style="1"]
Table content, generated on one of many public internet resources, for example: http://www.impressivewebs.com/html-table-code-generator/
[/trx_table]
*/
function themerex_sc_table($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"align" => "",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => "",
		"width" => "100%"
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left, $width);
	$content = str_replace(
				array('<p><table', 'table></p>', '><br />'),
				array('<table', 'table>', '>'),
				html_entity_decode($content, ENT_COMPAT, 'UTF-8'));
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_table' 
				. (!empty($align) && $align!='none' ? ' align'.esc_attr($align) : '') 
				. (!empty($class) ? ' '.esc_attr($class) : '') 
				. '"'
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
			.'>' 
			. do_shortcode($content) 
			. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_table', $atts, $content);
}

// ---------------------------------- [/trx_table] ---------------------------------------






// ---------------------------------- [trx_tabs] ---------------------------------------

add_shortcode("trx_tabs", "themerex_sc_tabs");

/*
[trx_tabs id="unique_id" tab_names="Planning|Development|Support" style="1|2" initial="1 - num_tabs"]
	[trx_tab]Randomised words which don't look even slightly believable. If you are going to use a passage. You need to be sure there isn't anything embarrassing hidden in the middle of text established fact that a reader will be istracted by the readable content of a page when looking at its layout.[/trx_tab]
	[trx_tab]Fact reader will be distracted by the <a href="#" class="main_link">readable content</a> of a page when. Looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using content here, content here, making it look like readable English will uncover many web sites still in their infancy. Various versions have evolved over. There are many variations of passages of Lorem Ipsum available, but the majority.[/trx_tab]
	[trx_tab]Distracted by the  readable content  of a page when. Looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using content here, content here, making it look like readable English will uncover many web sites still in their infancy. Various versions have  evolved over.  There are many variations of passages of Lorem Ipsum available.[/trx_tab]
[/trx_tabs]
*/
function themerex_sc_tabs($atts, $content = null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"initial" => "1",
		"scroll" => "no",
		"style" => "1",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"width" => "",
		"height" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));

	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left, $width);

	if (!themerex_sc_param_is_off($scroll)) themerex_enqueue_slider();
	if (empty($id)) $id = 'sc_tabs_'.str_replace('.', '', mt_rand());

	global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_tab_counter'] = 0;
	$THEMEREX_GLOBALS['sc_tab_scroll'] = $scroll;
	$THEMEREX_GLOBALS['sc_tab_height'] = themerex_prepare_css_value($height);
	$THEMEREX_GLOBALS['sc_tab_id']     = $id;
	$THEMEREX_GLOBALS['sc_tab_titles'] = array();

	$content = do_shortcode($content);

	$sc_tab_titles = $THEMEREX_GLOBALS['sc_tab_titles'];

	$initial = max(1, min(count($sc_tab_titles), (int) $initial));

	$tabs_output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
						. ' class="sc_tabs sc_tabs_style_'.esc_attr($style) . (!empty($class) ? ' '.esc_attr($class) : '') . '"'
						. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
						. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
						. ' data-active="' . ($initial-1) . '"'
						. '>'
					.'<ul class="sc_tabs_titles">';
	$titles_output = '';
	for ($i = 0; $i < count($sc_tab_titles); $i++) {
		$classes = array('sc_tabs_title');
		if ($i == 0) $classes[] = 'first';
		else if ($i == count($sc_tab_titles) - 1) $classes[] = 'last';
		$titles_output .= '<li class="'.join(' ', $classes).'">'
							. '<a href="#'.esc_attr($sc_tab_titles[$i]['id']).'" class="theme_button" id="'.esc_attr($sc_tab_titles[$i]['id']).'_tab">' . ($sc_tab_titles[$i]['title']) . '</a>'
							. '</li>';
	}

	wp_enqueue_script('jquery-ui-tabs', false, array('jquery','jquery-ui-core'), null, true);
	wp_enqueue_script('jquery-effects-fade', false, array('jquery','jquery-effects-core'), null, true);

	$tabs_output .= $titles_output
		. '</ul>' 
		. ($content)
		.'</div>';
	return apply_filters('themerex_shortcode_output', $tabs_output, 'trx_tabs', $atts, $content);
}


add_shortcode("trx_tab", "themerex_sc_tab");

function themerex_sc_tab($atts, $content = null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"tab_id" => "",		// get it from VC
		"title" => "",		// get it from VC
		// Common params
		"id" => "",
		"class" => "",
		"css" => ""
    ), $atts)));
    global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_tab_counter']++;
	if (empty($id))
		$id = !empty($tab_id) ? $tab_id : ($THEMEREX_GLOBALS['sc_tab_id']).'_'.($THEMEREX_GLOBALS['sc_tab_counter']);
	$sc_tab_titles = $THEMEREX_GLOBALS['sc_tab_titles'];
	if (isset($sc_tab_titles[$THEMEREX_GLOBALS['sc_tab_counter']-1])) {
		$sc_tab_titles[$THEMEREX_GLOBALS['sc_tab_counter']-1]['id'] = $id;
		if (!empty($title))
			$sc_tab_titles[$THEMEREX_GLOBALS['sc_tab_counter']-1]['title'] = $title;
	} else {
		$sc_tab_titles[] = array(
			'id' => $id,
			'title' => $title
		);
	}
	$THEMEREX_GLOBALS['sc_tab_titles'] = $sc_tab_titles;
	$output = '<div id="'.esc_attr($id).'"'
				.' class="sc_tabs_content' 
					. ($THEMEREX_GLOBALS['sc_tab_counter'] % 2 == 1 ? ' odd' : ' even') 
					. ($THEMEREX_GLOBALS['sc_tab_counter'] == 1 ? ' first' : '') 
					. (!empty($class) ? ' '.esc_attr($class) : '') 
					. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
					. '>' 
			. (themerex_sc_param_is_on($THEMEREX_GLOBALS['sc_tab_scroll']) 
				? '<div id="'.esc_attr($id).'_scroll" class="sc_scroll sc_scroll_vertical" style="height:'.($THEMEREX_GLOBALS['sc_tab_height'] != '' ? $THEMEREX_GLOBALS['sc_tab_height'] : '200px').';"><div class="sc_scroll_wrapper swiper-wrapper"><div class="sc_scroll_slide swiper-slide">' 
				: '')
			. do_shortcode($content) 
			. (themerex_sc_param_is_on($THEMEREX_GLOBALS['sc_tab_scroll']) 
				? '</div></div><div id="'.esc_attr($id).'_scroll_bar" class="sc_scroll_bar sc_scroll_bar_vertical '.esc_attr($id).'_scroll_bar"></div></div>' 
				: '')
		. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_tab', $atts, $content);
}
// ---------------------------------- [/trx_tabs] ---------------------------------------






// ---------------------------------- [trx_team] ---------------------------------------


add_shortcode('trx_team', 'themerex_sc_team');

/*
[trx_team id="unique_id" style="normal|big"]
	[trx_team_item user="user_login"]
[/trx_team]
*/
function themerex_sc_team($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"style" => 1,
		"columns" => 3,
		"custom" => "no",
		"ids" => "",
		"cat" => "",
		"count" => 3,
		"offset" => "",
		"orderby" => "date",
		"order" => "desc",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
	$count = max(1, (int) $count);
	$columns = max(1, min(4, (int) $columns));
	if (themerex_sc_param_is_off($custom) && $count < $columns) $columns = $count;
	global $THEMEREX_GLOBALS;
	$style = max(1, min(2, $style));
	$THEMEREX_GLOBALS['sc_team_style'] = $style;
	$THEMEREX_GLOBALS['sc_team_columns'] = $columns;
	$THEMEREX_GLOBALS['sc_team_counter'] = 0;
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_team sc_team_style_'.esc_attr($style).(!empty($class) ? ' '.esc_attr($class) : '').'"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
				. '>'
				. '<div class="sc_columns columns_wrap">';

	$content = do_shortcode($content);
		
	if (themerex_sc_param_is_on($custom) && $content) {
		$output .= $content;
	} else {
		global $post;
	
		if (!empty($ids)) {
			$posts = explode(',', $ids);
			$count = count($posts);
		}
		
		$args = array(
			'post_type' => 'team',
			'post_status' => 'publish',
			'posts_per_page' => $count,
			'ignore_sticky_posts' => true,
			'order' => $order=='asc' ? 'asc' : 'desc',
		);
	
		if ($offset > 0 && empty($ids)) {
			$args['offset'] = $offset;
		}
	
		$args = themerex_query_add_sort_order($args, $orderby, $order);
		$args = themerex_query_add_posts_and_cats($args, $ids, 'team', $cat, 'team_group');

		$query = new WP_Query( $args );

		$post_number = 0;
			
		while ( $query->have_posts() ) { 
			$query->the_post();
			$post_number++;
			$post_id = get_the_ID();
			$name = apply_filters('the_title', get_the_title());
			$descr = apply_filters('the_excerpt', get_the_excerpt());
			$post_meta = get_post_meta($post_id, 'team_data', true);
			$position = $post_meta['team_member_position'];
			$link = !empty($post_meta['team_member_link']) ? $post_meta['team_member_link'] : get_permalink($post_id);
			$email = $post_meta['team_member_email'];
			$photo = wp_get_attachment_url(get_post_thumbnail_id($post_id));
			if (empty($photo)) {
				if (!empty($email))
					$photo = get_avatar($email, 350*min(2, max(1, themerex_get_theme_option("retina_ready"))));
			} else {
				$photo = themerex_get_resized_image_tag($photo, 350, 290);
			}
			$socials = '';
			$soc_list = $post_meta['team_member_socials'];
			if (is_array($soc_list) && count($soc_list)>0) {
				$soc_str = '';
				foreach ($soc_list as $sn=>$sl) {
					if (!empty($sl))
						$soc_str .= (!empty($soc_str) ? '|' : '') . ($sn) . '=' . ($sl);
				}
				if (!empty($soc_str) && shortcode_exists('trx_socials'))
					$socials = do_shortcode('[trx_socials socials="'.esc_attr($soc_str).'"][/trx_socials]');
			}
			$output .= 	'<div class="column-1_'.esc_attr($columns) . '">'
							. '<div' . ($id ? ' id="'.esc_attr(($id).'_'.($post_number)).'"' : '') 
									. ' class="sc_team_item sc_team_item_' . esc_attr($post_number) 
										. ($post_number % 2 == 1 ? ' odd' : ' even') 
										. ($post_number == 1 ? ' first' : '') 
									. '">'
								. '<div class="sc_team_item_avatar">'
									. ($photo)
									. ($style==2 
										? '<div class="sc_team_item_hover"><div class="sc_team_item_socials">' . ($socials) . '</div></div>'
										: '')
								. '</div>'
								. '<div class="sc_team_item_info">'
									. '<h6 class="sc_team_item_title">' . ($link ? '<a href="'.esc_url($link).'">' : '') . ($name) . ($link ? '</a>' : '') . '</h6>'
									. '<div class="sc_team_item_position">' . ($position) . '</div>'
									. ($style==1 
										? '<div class="sc_team_item_description">' . ($descr) . '</div>' . ($socials)
										: '')
								. '</div>'
							. '</div>'
						. '</div>';
		}
		wp_reset_postdata();
	}

	$output .= '</div>'
			. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_team', $atts, $content);
}


add_shortcode('trx_team_item', 'themerex_sc_team_item');

function themerex_sc_team_item($atts, $content=null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	extract(themerex_sc_html_decode(shortcode_atts( array(
		// Individual params
		"user" => "",
		"member" => "",
		"name" => "",
		"position" => "",
		"photo" => "",
		"email" => "",
		"link" => "",
		"socials" => "",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => ""
	), $atts)));
	global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_team_counter']++;
	$descr = trim(chop(do_shortcode($content)));
	if (!empty($socials) && shortcode_exists('trx_socials')) $socials = do_shortcode('[trx_socials socials="'.esc_attr($socials).'"][/trx_socials]');
	if (!empty($user) && $user!='none' && ($user_obj = get_user_by('login', $user)) != false) {
		$meta = get_user_meta($user_obj->ID);
		if (empty($email))		$email = $user_obj->data->user_email;
		if (empty($name))		$name = $user_obj->data->display_name;
		if (empty($position))	$position = isset($meta['user_position'][0]) ? $meta['user_position'][0] : '';
		if (empty($descr))		$descr = isset($meta['description'][0]) ? $meta['description'][0] : '';
		if (empty($socials))	$socials = themerex_show_user_socials(array('author_id'=>$user_obj->ID, 'echo'=>false));
	}
	if (!empty($member) && $member!='none' && ($member_obj = (intval($member) > 0 ? get_post($member, OBJECT) : get_page_by_title($member, OBJECT, 'team'))) != null) {
		if (empty($name))		$name = $member_obj->post_title;
		if (empty($descr))		$descr = $member_obj->post_excerpt;
		$post_meta = get_post_meta($member_obj->ID, 'team_data', true);
		if (empty($position))	$position = $post_meta['team_member_position'];
		if (empty($link))		$link = !empty($post_meta['team_member_link']) ? $post_meta['team_member_link'] : get_permalink($member_obj->ID);
		if (empty($email))		$email = $post_meta['team_member_email'];
		if (empty($photo)) 		$photo = wp_get_attachment_url(get_post_thumbnail_id($member_obj->ID));
		if (empty($socials)) {
			$socials = '';
			$soc_list = $post_meta['team_member_socials'];
			if (is_array($soc_list) && count($soc_list)>0) {
				$soc_str = '';
				foreach ($soc_list as $sn=>$sl) {
					if (!empty($sl))
						$soc_str .= (!empty($soc_str) ? '|' : '') . ($sn) . '=' . ($sl);
				}
				if (!empty($soc_str) && shortcode_exists('trx_socials'))
					$socials = do_shortcode('[trx_socials socials="'.esc_attr($soc_str).'"][/trx_socials]');
			}
		}
	}
	if (empty($photo)) {
		if (!empty($email)) $photo = get_avatar($email, 350*min(2, max(1, themerex_get_theme_option("retina_ready"))));
	} else {
		if ($photo > 0) {
			$attach = wp_get_attachment_image_src( $photo, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$photo = $attach[0];
		}
		$photo = themerex_get_resized_image_tag($photo, 350, 290);
	}
	$output = !empty($name) && !empty($position) 
		? '<div class="column-1_'.esc_attr($THEMEREX_GLOBALS['sc_team_columns']) 
						. (!empty($class) ? ' '.esc_attr($class) : '') 
					. '"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
					. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
					. '>'
				. '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
						. ' class="sc_team_item sc_team_item_' . esc_attr($THEMEREX_GLOBALS['sc_team_counter']) 
							. ($THEMEREX_GLOBALS['sc_team_counter'] % 2 == 1 ? ' odd' : ' even') 
							. ($THEMEREX_GLOBALS['sc_team_counter'] == 1 ? ' first' : '') 
						. '">'
					. '<div class="sc_team_item_avatar">'
						. ($photo)
						. ($THEMEREX_GLOBALS['sc_team_style']==2 
							? '<div class="sc_team_item_hover"><div class="sc_team_item_socials">' . ($socials) . '</div></div>'
							: '')
					. '</div>'
					. '<div class="sc_team_item_info">'
						. '<h6 class="sc_team_item_title">' . ($link ? '<a href="'.esc_url($link).'">' : '') . ($name) . ($link ? '</a>' : '') . '</h6>'
						. '<div class="sc_team_item_position">' . ($position) . '</div>'
						. ($THEMEREX_GLOBALS['sc_team_style']==1 
							? '<div class="sc_team_item_description">' . ($descr) . '</div>' . ($socials)
							: '')
					. '</div>'
				. '</div>'
			. '</div>'
		: '';
	return apply_filters('themerex_shortcode_output', $output, 'trx_team_item', $atts, $content);
}

// ---------------------------------- [/trx_team] ---------------------------------------






// ---------------------------------- [trx_testimonials] ---------------------------------------


add_shortcode('trx_testimonials', 'themerex_sc_testimonials');

/*
[trx_testimonials id="unique_id" style="1|2|3"]
	[trx_testimonials_item user="user_login"]Testimonials text[/trx_testimonials_item]
	[trx_testimonials_item email="" name="" position="" photo="photo_url"]Testimonials text[/trx_testimonials]
[/trx_testimonials]
*/

function themerex_sc_testimonials($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"controls" => "yes",
		"interval" => "",
		"autoheight" => "no",
		"align" => "",
		"custom" => "no",
		"ids" => "",
		"cat" => "",
		"count" => "3",
		"offset" => "",
		"orderby" => "date",
		"order" => "desc",
		"bg_tint" => "",
		"bg_color" => "",
		"bg_image" => "",
		"bg_overlay" => "",
		"bg_texture" => "",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"width" => "",
		"height" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));

	if (empty($id)) $id = "sc_testimonials_".str_replace('.', '', mt_rand());
	if (empty($width)) $width = "100%";
	if (!empty($height) && themerex_sc_param_is_on($autoheight)) $autoheight = "no";
	if (empty($interval)) $interval = mt_rand(5000, 10000);

	if ($bg_image > 0) {
		$attach = wp_get_attachment_image_src( $bg_image, 'full' );
		if (isset($attach[0]) && $attach[0]!='')
			$bg_image = $attach[0];
	}

	if ($bg_overlay > 0) {
		if ($bg_color=='') $bg_color = apply_filters('themerex_filter_get_theme_bgcolor', '');
		$rgb = themerex_hex2rgb($bg_color);
	}
	
	global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_testimonials_width']  = themerex_prepare_css_value($width);
	$THEMEREX_GLOBALS['sc_testimonials_height'] = themerex_prepare_css_value($height);
	
	$ms = themerex_get_css_position_from_values($top, $right, $bottom, $left);
	$ws = themerex_get_css_position_from_values('', '', '', '', $width);
	$hs = themerex_get_css_position_from_values('', '', '', '', '', $height);

	$css .= ($ms) . ($hs) . ($ws);
	
	themerex_enqueue_slider('swiper');

	$output = ($bg_color!='' || $bg_image!='' || $bg_overlay>0 || $bg_texture>0 || themerex_strlen($bg_texture)>2
				? '<div class="sc_testimonials_wrap sc_section'
						. ($bg_tint ? ' bg_tint_' . esc_attr($bg_tint) : '') 
						. '"'
					.' style="'
						. ($bg_color !== '' && $bg_overlay==0 ? 'background-color:' . esc_attr($bg_color) . ';' : '')
						. ((!empty($bg_image) && $bg_image != 'none') ? 'background-image:url(' . esc_url($bg_image) . ');' : '')
						. '"'
					. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
					. '>'
					. '<div class="sc_section_overlay'.($bg_texture>0 ? ' texture_bg_'.esc_attr($bg_texture) : '') . '"'
							. ' style="' . ($bg_overlay>0 ? 'background-color:rgba('.(int)$rgb['r'].','.(int)$rgb['g'].','.(int)$rgb['b'].','.min(1, max(0, $bg_overlay)).');' : '')
								. (themerex_strlen($bg_texture)>2 ? 'background-image:url('.esc_url($bg_texture).');' : '')
								. '"'
								. ($bg_overlay > 0 ? ' data-overlay="'.esc_attr($bg_overlay).'" data-bg_color="'.esc_attr($bg_color).'"' : '')
								. '>' 
				: '')
			. '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_testimonials sc_slider_swiper swiper-slider-container sc_slider_nopagination'
				. (themerex_sc_param_is_on($controls) ? ' sc_slider_controls' : ' sc_slider_nocontrols')
				. (themerex_sc_param_is_on($autoheight) ? ' sc_slider_height_auto' : '')
				. ($hs ? ' sc_slider_height_fixed' : '')
				. (!empty($class) ? ' '.esc_attr($class) : '')
				. ($align!='' && $align!='none' ? ' align'.esc_attr($align) : '')
				. '"'
			. ($bg_color=='' && $bg_image=='' && $bg_overlay==0 && ($bg_texture=='' || $bg_texture=='0') && !themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. (!empty($width) && themerex_strpos($width, '%')===false ? ' data-old-width="' . esc_attr($width) . '"' : '')
			. (!empty($height) && themerex_strpos($height, '%')===false ? ' data-old-height="' . esc_attr($height) . '"' : '')
			. ((int) $interval > 0 ? ' data-interval="'.esc_attr($interval).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
		. '>'
		. '<div class="slides swiper-wrapper">';

	$content = do_shortcode($content);
		
	if (themerex_sc_param_is_on($custom) && $content) {
		$output .= $content;
	} else {
		global $post;
	
		if (!empty($ids)) {
			$posts = explode(',', $ids);
			$count = count($posts);
		}
		
		$args = array(
			'post_type' => 'testimonial',
			'post_status' => 'publish',
			'posts_per_page' => $count,
			'ignore_sticky_posts' => true,
			'order' => $order=='asc' ? 'asc' : 'desc',
		);
	
		if ($offset > 0 && empty($ids)) {
			$args['offset'] = $offset;
		}
	
		$args = themerex_query_add_sort_order($args, $orderby, $order);
		$args = themerex_query_add_posts_and_cats($args, $ids, 'testimonial', $cat, 'testimonial_group');

		$query = new WP_Query( $args );

		$post_number = 0;
			
		while ( $query->have_posts() ) { 
			$query->the_post();
			$post_number++;
			$post_id = get_the_ID();
			$post_title = get_the_title();
			$post_meta = get_post_meta($post_id, 'testimonial_data', true);
			$author = $post_meta['testimonial_author'];
			$link = $post_meta['testimonial_link'];
			$email = $post_meta['testimonial_email'];
			$content = apply_filters('the_content', get_the_content());
			$photo = wp_get_attachment_url(get_post_thumbnail_id($post_id));
			if (empty($photo)) {
				if (!empty($email))
					$photo = get_avatar($email, 70*min(2, max(1, themerex_get_theme_option("retina_ready"))));
			} else {
				$photo = themerex_get_resized_image_tag($photo, 70, 70);
			}

			$output .= '<div class="swiper-slide" data-style="'.esc_attr(($ws).($hs)).'" style="'.esc_attr(($ws).($hs)).'">'
						. '<div class="sc_testimonial_item">'
							. ($photo ? '<div class="sc_testimonial_avatar">'.($photo).'</div>' : '')
							. '<div class="sc_testimonial_content">' . ($content) . '</div>'
							. ($author ? '<div class="sc_testimonial_author">' . ($link ? '<a href="'.esc_url($link).'">'.($author).'</a>' : $author) . '</div>' : '')
						. '</div>'
					. '</div>';
		}
		wp_reset_postdata();
	}

	$output .= '</div>';
	if (themerex_sc_param_is_on($controls))
		$output .= '<div class="sc_slider_controls_wrap"><a class="sc_slider_prev" href="#"></a><a class="sc_slider_next" href="#"></a></div>';

	$output .= '</div>'
				. ($bg_color!='' || $bg_image!='' || $bg_overlay>0 || $bg_texture>0 || themerex_strlen($bg_texture)>2
					?  '</div></div>'
					: '');
	return apply_filters('themerex_shortcode_output', $output, 'trx_testimonials', $atts, $content);
}


add_shortcode('trx_testimonials_item', 'themerex_sc_testimonials_item');

function themerex_sc_testimonials_item($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"author" => "",
		"link" => "",
		"photo" => "",
		"email" => "",
		// Common params
		"id" => "",
		"class" => "",
		"css" => "",
    ), $atts)));
    global $THEMEREX_GLOBALS;
	if (empty($photo)) {
		if (!empty($email))
			$photo = get_avatar($email, 70*min(2, max(1, themerex_get_theme_option("retina_ready"))));
	} else {
		if ($photo > 0) {
			$attach = wp_get_attachment_image_src( $photo, 'full' );
			if (isset($attach[0]) && $attach[0]!='')
				$photo = $attach[0];
		}
		$photo = themerex_get_resized_image_tag($photo, 70, 70);
	}

	$css2 = (!empty($THEMEREX_GLOBALS['sc_testimonials_width'])  ? 'width:'  . esc_attr($THEMEREX_GLOBALS['sc_testimonials_width'])  . ';' : '')
			. (!empty($THEMEREX_GLOBALS['sc_testimonials_height']) ? 'height:' . esc_attr($THEMEREX_GLOBALS['sc_testimonials_height']) . ';' : '');

	$content = do_shortcode($content);

	$output = '<div class="swiper-slide"' . ($css2 ? ' data-style="'.esc_attr($css2).'" style="'.esc_attr($css2).'"' : '') . '>'
			. '<div' . ($id ? ' id="'.esc_attr($id).'"' : '').' class="sc_testimonial_item' . (!empty($class) ? ' '.esc_attr($class) : '') . '"' . ($css ? ' style="'.esc_attr($css).'"' : '') . '>'
				. ($photo ? '<div class="sc_testimonial_avatar">'.($photo).'</div>' : '')
				. '<div class="sc_testimonial_content">' . ($content) . '</div>'
				. ($author ? '<div class="sc_testimonial_author">' . ($link ? '<a href="'.esc_url($link).'">'.($author).'</a>' : $author) . '</div>' : '')
			. '</div>'
		. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_testimonials_item', $atts, $content);
}

// ---------------------------------- [/trx_testimonials] ---------------------------------------





// ---------------------------------- [trx_title] ---------------------------------------


add_shortcode('trx_title', 'themerex_sc_title');

/*
[trx_title id="unique_id" style='regular|iconed' icon='' image='' background="on|off" type="1-6"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/trx_title]
*/
function themerex_sc_title($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"type" => "1",
		"style" => "regular",
		"align" => "",
		"font_weight" => "",
		"font_size" => "",
		"color" => "",
		"icon" => "",
		"image" => "",
		"picture" => "",
		"image_size" => "small",
		"position" => "left",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"width" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left, $width)
		.($align && $align!='none' && $align!='inherit' ? 'text-align:' . esc_attr($align) .';' : '')
		.($color ? 'color:' . esc_attr($color) .';' : '')
		.($font_weight && $font_weight != 'inherit' ? 'font-weight:' . esc_attr($font_weight) .';' : '')
		.($font_size   ? 'font-size:' . esc_attr($font_size) .';' : '')
		;
	$type = min(6, max(1, $type));
	if ($picture > 0) {
		$attach = wp_get_attachment_image_src( $picture, 'full' );
		if (isset($attach[0]) && $attach[0]!='')
			$picture = $attach[0];
	}
	$pic = $style!='iconed' 
		? '' 
		: '<span class="sc_title_icon sc_title_icon_'.esc_attr($position).'  sc_title_icon_'.esc_attr($image_size).($icon!='' && $icon!='none' ? ' '.esc_attr($icon) : '').'"'.'>'
			.($picture ? '<img src="'.esc_url($picture).'" alt="" />' : '')
			.(empty($picture) && $image && $image!='none' ? '<img src="'.esc_url(themerex_strpos($image, 'http:')!==false ? $image : themerex_get_file_url('images/icons/'.($image).'.png')).'" alt="" />' : '')
			.'</span>';
	$output = '<h' . esc_attr($type) . ($id ? ' id="'.esc_attr($id).'"' : '')
			. ' class="sc_title sc_title_'.esc_attr($style)
				.($align && $align!='none' && $align!='inherit' ? ' sc_align_' . esc_attr($align) : '')
				.(!empty($class) ? ' '.esc_attr($class) : '')
				.'"'
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. '>'
				. ($pic)
				. ($style=='divider' ? '<span class="sc_title_divider_before"'.($color ? ' style="background-color: '.esc_attr($color).'"' : '').'></span>' : '')
				. do_shortcode($content) 
				. ($style=='divider' ? '<span class="sc_title_divider_after"'.($color ? ' style="background-color: '.esc_attr($color).'"' : '').'></span>' : '')
			. '</h' . esc_attr($type) . '>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_title', $atts, $content);
}

// ---------------------------------- [/trx_title] ---------------------------------------






// ---------------------------------- [trx_toggles] ---------------------------------------


add_shortcode('trx_toggles', 'themerex_sc_toggles');

function themerex_sc_toggles($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"style" => "1",
		"counter" => "off",
		"icon_closed" => "icon-plus-2",
		"icon_opened" => "icon-minus-2",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
	global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_toggle_counter'] = 0;
	$THEMEREX_GLOBALS['sc_toggle_style']   = max(1, min(2, $style));
	$THEMEREX_GLOBALS['sc_toggle_show_counter'] = themerex_sc_param_is_on($counter);
	$THEMEREX_GLOBALS['sc_toggles_icon_closed'] = empty($icon_closed) || themerex_sc_param_is_inherit($icon_closed) ? "icon-plus-2" : $icon_closed;
	$THEMEREX_GLOBALS['sc_toggles_icon_opened'] = empty($icon_opened) || themerex_sc_param_is_inherit($icon_opened) ? "icon-minus-2" : $icon_opened;
	wp_enqueue_script('jquery-effects-slide', false, array('jquery','jquery-effects-core'), null, true);
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
			. ' class="sc_toggles sc_toggles_style_'.esc_attr($style)
				. (!empty($class) ? ' '.esc_attr($class) : '')
				. (themerex_sc_param_is_on($counter) ? ' sc_show_counter' : '') 
				. '"'
			. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
			. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
			. '>'
			. do_shortcode($content)
			. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_toggles', $atts, $content);
}


add_shortcode('trx_toggles_item', 'themerex_sc_toggles_item');

//[trx_toggles_item]
function themerex_sc_toggles_item($atts, $content=null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	extract(themerex_sc_html_decode(shortcode_atts( array(
		// Individual params
		"title" => "",
		"open" => "",
		"icon_closed" => "",
		"icon_opened" => "",
		// Common params
		"id" => "",
		"class" => "",
		"css" => ""
	), $atts)));
	global $THEMEREX_GLOBALS;
	$THEMEREX_GLOBALS['sc_toggle_counter']++;
	if (empty($icon_closed) || themerex_sc_param_is_inherit($icon_closed)) $icon_closed = $THEMEREX_GLOBALS['sc_toggles_icon_closed'] ? $THEMEREX_GLOBALS['sc_toggles_icon_closed'] : "icon-plus-2";
	if (empty($icon_opened) || themerex_sc_param_is_inherit($icon_opened)) $icon_opened = $THEMEREX_GLOBALS['sc_toggles_icon_opened'] ? $THEMEREX_GLOBALS['sc_toggles_icon_opened'] : "icon-minus-2";
	$css .= themerex_sc_param_is_on($open) ? 'display:block;' : '';
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_toggles_item'.(themerex_sc_param_is_on($open) ? ' sc_active' : '')
				. (!empty($class) ? ' '.esc_attr($class) : '')
				. ($THEMEREX_GLOBALS['sc_toggle_counter'] % 2 == 1 ? ' odd' : ' even') 
				. ($THEMEREX_GLOBALS['sc_toggle_counter'] == 1 ? ' first' : '')
				. '">'
				. '<h5 class="sc_toggles_title'.(themerex_sc_param_is_on($open) ? ' ui-state-active' : '').'">'
				. (!themerex_sc_param_is_off($icon_closed) ? '<span class="sc_toggles_icon sc_toggles_icon_closed '.esc_attr($icon_closed).'"></span>' : '')
				. (!themerex_sc_param_is_off($icon_opened) ? '<span class="sc_toggles_icon sc_toggles_icon_opened '.esc_attr($icon_opened).'"></span>' : '')
				. ($THEMEREX_GLOBALS['sc_toggle_show_counter'] ? '<span class="sc_items_counter">'.($THEMEREX_GLOBALS['sc_toggle_counter']).'</span>' : '')
				. ($title) 
				. '</h5>'
				. '<div class="sc_toggles_content"'
					. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
					.'>' 
					. do_shortcode($content) 
				. '</div>'
			. '</div>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_toggles_item', $atts, $content);
}

// ---------------------------------- [/trx_toggles] ---------------------------------------





// ---------------------------------- [trx_tooltip] ---------------------------------------


add_shortcode('trx_tooltip', 'themerex_sc_tooltip');

/*
[trx_tooltip id="unique_id" title="Tooltip text here"]Et adipiscing integer, scelerisque pid, augue mus vel tincidunt porta[/tooltip]
*/
function themerex_sc_tooltip($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"title" => "",
		// Common params
		"id" => "",
		"class" => "",
		"css" => ""
    ), $atts)));
	$output = '<span' . ($id ? ' id="'.esc_attr($id).'"' : '') 
				. ' class="sc_tooltip_parent'. (!empty($class) ? ' '.esc_attr($class) : '').'"'
				. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
				. '>'
					. do_shortcode($content)
					. '<span class="sc_tooltip">' . ($title) . '</span>'
				. '</span>';
	return apply_filters('themerex_shortcode_output', $output, 'trx_tooltip', $atts, $content);
}
// ---------------------------------- [/trx_tooltip] ---------------------------------------






// ---------------------------------- [trx_twitter] ---------------------------------------


add_shortcode('trx_twitter', 'themerex_sc_twitter');

/*
[trx_twitter id="unique_id" user="username" consumer_key="" consumer_secret="" token_key="" token_secret=""]
*/

function themerex_sc_twitter($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"user" => "",
		"consumer_key" => "",
		"consumer_secret" => "",
		"token_key" => "",
		"token_secret" => "",
		"count" => "3",
		"controls" => "yes",
		"interval" => "",
		"autoheight" => "no",
		"align" => "",
		"bg_tint" => "",
		"bg_color" => "",
		"bg_image" => "",
		"bg_overlay" => "",
		"bg_texture" => "",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"width" => "",
		"height" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));

	$twitter_username = $user ? $user : themerex_get_theme_option('twitter_username');
	$twitter_consumer_key = $consumer_key ? $consumer_key : themerex_get_theme_option('twitter_consumer_key');
	$twitter_consumer_secret = $consumer_secret ? $consumer_secret : themerex_get_theme_option('twitter_consumer_secret');
	$twitter_token_key = $token_key ? $token_key : themerex_get_theme_option('twitter_token_key');
	$twitter_token_secret = $token_secret ? $token_secret : themerex_get_theme_option('twitter_token_secret');
	$twitter_count = max(1, $count ? $count : intval(themerex_get_theme_option('twitter_count')));

	if (empty($id)) $id = "sc_testimonials_".str_replace('.', '', mt_rand());
	if (empty($width)) $width = "100%";
	if (!empty($height) && themerex_sc_param_is_on($autoheight)) $autoheight = "no";
	if (empty($interval)) $interval = mt_rand(5000, 10000);

	if ($bg_image > 0) {
		$attach = wp_get_attachment_image_src( $bg_image, 'full' );
		if (isset($attach[0]) && $attach[0]!='')
			$bg_image = $attach[0];
	}

	if ($bg_overlay > 0) {
		if ($bg_color=='') $bg_color = apply_filters('themerex_filter_get_theme_bgcolor', '');
		$rgb = themerex_hex2rgb($bg_color);
	}
	
	$ms = themerex_get_css_position_from_values($top, $right, $bottom, $left);
	$ws = themerex_get_css_position_from_values('', '', '', '', $width);
	$hs = themerex_get_css_position_from_values('', '', '', '', '', $height);

	$css .= ($ms) . ($hs) . ($ws);

	$output = '';

	if (!empty($twitter_consumer_key) && !empty($twitter_consumer_secret) && !empty($twitter_token_key) && !empty($twitter_token_secret)) {
		$data = themerex_get_twitter_data(array(
			'mode'            => 'user_timeline',
			'consumer_key'    => $twitter_consumer_key,
			'consumer_secret' => $twitter_consumer_secret,
			'token'           => $twitter_token_key,
			'secret'          => $twitter_token_secret
			)
		);
		if ($data && isset($data[0]['text'])) {
			themerex_enqueue_slider('swiper');
			$output = ($bg_color!='' || $bg_image!='' || $bg_overlay>0 || $bg_texture>0 || themerex_strlen($bg_texture)>2
					? '<div class="sc_twitter_wrap sc_section'
							. ($bg_tint ? ' bg_tint_' . esc_attr($bg_tint) : '') 
							. ($align && $align!='none' && $align!='inherit' ? ' align' . esc_attr($align) : '')
							. '"'
						.' style="'
							. ($bg_color !== '' && $bg_overlay==0 ? 'background-color:' . esc_attr($bg_color) . ';' : '')
							. ($bg_image !== '' ? 'background-image:url('.esc_url($bg_image).');' : '')
							. '"'
						. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
						. '>'
						. '<div class="sc_section_overlay'.($bg_texture>0 ? ' texture_bg_'.esc_attr($bg_texture) : '') . '"'
								. ' style="' 
									. ($bg_overlay>0 ? 'background-color:rgba('.(int)$rgb['r'].','.(int)$rgb['g'].','.(int)$rgb['b'].','.min(1, max(0, $bg_overlay)).');' : '')
									. (themerex_strlen($bg_texture)>2 ? 'background-image:url('.esc_url($bg_texture).');' : '')
									. '"'
									. ($bg_overlay > 0 ? ' data-overlay="'.esc_attr($bg_overlay).'" data-bg_color="'.esc_attr($bg_color).'"' : '')
									. '>' 
					: '')
					. '<div class="sc_twitter sc_slider_swiper sc_slider_nopagination swiper-slider-container"'
							. (themerex_sc_param_is_on($controls) ? ' sc_slider_controls' : ' sc_slider_nocontrols')
							. (themerex_sc_param_is_on($autoheight) ? ' sc_slider_height_auto' : '')
							. ($hs ? ' sc_slider_height_fixed' : '')
							. (!empty($class) ? ' '.esc_attr($class) : '')
							. ($bg_color=='' && $bg_image=='' && $bg_overlay==0 && ($bg_texture=='' || $bg_texture=='0') && $align && $align!='none' && $align!='inherit' ? ' align' . esc_attr($align) : '')
							. '"'
						. ($bg_color=='' && $bg_image=='' && $bg_overlay==0 && ($bg_texture=='' || $bg_texture=='0') && !themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
						. (!empty($width) && themerex_strpos($width, '%')===false ? ' data-old-width="' . esc_attr($width) . '"' : '')
						. (!empty($height) && themerex_strpos($height, '%')===false ? ' data-old-height="' . esc_attr($height) . '"' : '')
						. ((int) $interval > 0 ? ' data-interval="'.esc_attr($interval).'"' : '')
						. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
						. '>'
						. '<div class="slides swiper-wrapper">';
			$cnt = 0;
			foreach ($data as $tweet) {
				if (themerex_substr($tweet['text'], 0, 1)=='@') continue;
					$output .= '<div class="swiper-slide" data-style="'.esc_attr(($ws).($hs)).'" style="'.esc_attr(($ws).($hs)).'">'
								. '<div class="sc_twitter_item">'
									. '<span class="sc_twitter_icon icon-twitter"></span>'
									. '<div class="sc_twitter_content">'
										. '<a href="' . esc_url('https://twitter.com/'.($twitter_username)).'" class="sc_twitter_author" target="_blank">@' . esc_html($tweet['user']['screen_name']) . '</a> '
										. wp_kses_post(themerex_prepare_twitter_text($tweet))
									. '</div>'
								. '</div>'
							. '</div>';
				if (++$cnt >= $twitter_count) break;
			}
			$output .= '</div>'
					. '<div class="sc_slider_controls_wrap"><a class="sc_slider_prev" href="#"></a><a class="sc_slider_next" href="#"></a></div>'
				. '</div>'
				. ($bg_color!='' || $bg_image!='' || $bg_overlay>0 || $bg_texture>0 || themerex_strlen($bg_texture)>2
					?  '</div></div>'
					: '');
		}
	}
	return apply_filters('themerex_shortcode_output', $output, 'trx_twitter', $atts, $content);
}

// ---------------------------------- [/trx_twitter] ---------------------------------------

						


// ---------------------------------- [trx_video] ---------------------------------------

add_shortcode("trx_video", "themerex_sc_video");

//[trx_video id="unique_id" url="http://player.vimeo.com/video/20245032?title=0&amp;byline=0&amp;portrait=0" width="" height=""]
function themerex_sc_video($atts, $content = null) {
	if (themerex_sc_in_shortcode_blogger()) return '';
	extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"url" => '',
		"src" => '',
		"image" => '',
		"ratio" => '16:9',
		"autoplay" => 'off',
		"align" => '',
		"bg_image" => '',
		"bg_top" => '',
		"bg_bottom" => '',
		"bg_left" => '',
		"bg_right" => '',
		"frame" => "on",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"width" => '',
		"height" => '',
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));

	if (empty($autoplay)) $autoplay = 'off';
	
	$ratio = empty($ratio) ? "16:9" : str_replace(array('/','\\','-'), ':', $ratio);
	$ratio_parts = explode(':', $ratio);
	if (empty($height) && empty($width)) $width='100%';
	$ed = themerex_substr($width, -1);
	if (empty($height) && !empty($width) && $ed!='%') {
		$height = round($width / $ratio_parts[0] * $ratio_parts[1]);
	}
	if (!empty($height) && empty($width)) {
		$width = round($height * $ratio_parts[0] / $ratio_parts[1]);
	}
	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left);
	$css_dim = themerex_get_css_position_from_values('', '', '', '', $width, $height);
	$css_bg = themerex_get_css_paddings_from_values($bg_top, $bg_right, $bg_bottom, $bg_left);

	if ($src=='' && $url=='' && isset($atts[0])) {
		$src = $atts[0];
	}
	$url = $src!='' ? $src : $url;
	if ($image!='' && themerex_sc_param_is_off($image))
		$image = '';
	else {
		if (themerex_sc_param_is_on($autoplay) && is_singular() && !themerex_get_global('blog_streampage'))
			$image = '';
		else {
			if ($image > 0) {
				$attach = wp_get_attachment_image_src( $image, 'full' );
				if (isset($attach[0]) && $attach[0]!='')
					$image = $attach[0];
			}
			if ($bg_image) {
				$thumb_sizes = themerex_get_thumb_sizes(array(
					'layout' => 'grid_3'
				));
				if (!is_single() || !empty($image)) $image = themerex_get_resized_image_url(empty($image) ? get_the_ID() : $image, $thumb_sizes['w'], $thumb_sizes['h'], null, false, false, false);
			} else
				if (!is_single() || !empty($image)) $image = themerex_get_resized_image_url(empty($image) ? get_the_ID() : $image, $ed!='%' ? $width : null, $height);
			if (empty($image) && (!is_singular() || themerex_get_global('blog_streampage') || themerex_sc_param_is_off($autoplay)))
				$image = themerex_get_video_cover_image($url);
		}
	}
	if ($bg_image > 0) {
		$attach = wp_get_attachment_image_src( $bg_image, 'full' );
		if (isset($attach[0]) && $attach[0]!='')
			$bg_image = $attach[0];
	}
	if ($bg_image) {
		$css_bg .= $css . 'background-image: url('.esc_url($bg_image).');';
		$css = $css_dim;
	} else {
		$css .= $css_dim;
	}

	$url = themerex_get_video_player_url($src!='' ? $src : $url);
	
	$video = '<video' . ($id ? ' id="' . esc_attr($id) . '"' : '') 
		. ' class="sc_video'. (!empty($class) ? ' '.esc_attr($class) : '').'"'
		. ' src="' . esc_url($url) . '"'
		. ' width="' . esc_attr($width) . '" height="' . esc_attr($height) . '"' 
		. ' data-width="' . esc_attr($width) . '" data-height="' . esc_attr($height) . '"' 
		. ' data-ratio="'.esc_attr($ratio).'"'
		. ($image ? ' poster="'.esc_attr($image).'" data-image="'.esc_attr($image).'"' : '') 
		. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
		. ($align && $align!='none' ? ' data-align="'.esc_attr($align).'"' : '')
		. ($bg_image ? ' data-bg-image="'.esc_attr($bg_image).'"' : '') 
		. ($css_bg!='' ? ' data-style="'.esc_attr($css_bg).'"' : '') 
		. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
		. (($image && themerex_get_theme_option('substitute_video')=='yes') || (themerex_sc_param_is_on($autoplay) && is_singular() && !themerex_get_global('blog_streampage')) ? ' autoplay="autoplay"' : '') 
		. ' controls="controls" loop="loop"'
		. '>'
		. '</video>';
	
//	$video ='<video width="320" height="240" controls>'
//			.'<source src="' . esc_url($url) . '" type="video/mp4">'
//			.'Your browser does not support the video tag.'
//			.'</video>';
	
	if (themerex_get_custom_option('substitute_video')=='no') {
		if (themerex_sc_param_is_on($frame)) $video = themerex_get_video_frame($video, $image, $css, $css_bg);
	} else {
		if ((isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') && (isset($_POST['action']) && $_POST['action']=='vc_load_shortcode')) {
			$video = themerex_substitute_video($video, $width, $height, false);
		}
	}
	if (themerex_get_theme_option('use_mediaelement')=='yes')
		wp_enqueue_script('wp-mediaelement');
	return apply_filters('themerex_shortcode_output', $video, 'trx_video', $atts, $content);
}
// ---------------------------------- [/trx_video] ---------------------------------------






// ---------------------------------- [trx_zoom] ---------------------------------------

add_shortcode('trx_zoom', 'themerex_sc_zoom');

/*
[trx_zoom id="unique_id" border="none|light|dark"]
*/
function themerex_sc_zoom($atts, $content=null){	
	if (themerex_sc_in_shortcode_blogger()) return '';
    extract(themerex_sc_html_decode(shortcode_atts(array(
		// Individual params
		"effect" => "zoom",
		"src" => "",
		"url" => "",
		"over" => "",
		"align" => "",
		"bg_image" => "",
		"bg_top" => '',
		"bg_bottom" => '',
		"bg_left" => '',
		"bg_right" => '',
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"width" => "",
		"height" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
    ), $atts)));

	wp_enqueue_script( 'themerex-elevate-zoom-script', themerex_get_file_url('js/jquery.elevateZoom-3.0.4.js'), array(), null, true );

	$css .= themerex_get_css_position_from_values('!'.($top), '!'.($right), '!'.($bottom), '!'.($left));
	$css_dim = themerex_get_css_position_from_values('', '', '', '', $width, $height);
	$css_bg = themerex_get_css_paddings_from_values($bg_top, $bg_right, $bg_bottom, $bg_left);
	$width  = themerex_prepare_css_value($width);
	$height = themerex_prepare_css_value($height);
	if (empty($id)) $id = 'sc_zoom_'.str_replace('.', '', mt_rand());
	$src = $src!='' ? $src : $url;
	if ($src > 0) {
		$attach = wp_get_attachment_image_src( $src, 'full' );
		if (isset($attach[0]) && $attach[0]!='')
			$src = $attach[0];
	}
	if ($over > 0) {
		$attach = wp_get_attachment_image_src( $over, 'full' );
		if (isset($attach[0]) && $attach[0]!='')
			$over = $attach[0];
	}
	if ($effect=='lens' && ((int) $width > 0 && themerex_substr($width, -2, 2)=='px') || ((int) $height > 0 && themerex_substr($height, -2, 2)=='px')) {
		if ($src)
			$src = themerex_get_resized_image_url($src, (int) $width > 0 && themerex_substr($width, -2, 2)=='px' ? (int) $width : null, (int) $height > 0 && themerex_substr($height, -2, 2)=='px' ? (int) $height : null);
		if ($over)
			$over = themerex_get_resized_image_url($over, (int) $width > 0 && themerex_substr($width, -2, 2)=='px' ? (int) $width : null, (int) $height > 0 && themerex_substr($height, -2, 2)=='px' ? (int) $height : null);
	}
	if ($bg_image > 0) {
		$attach = wp_get_attachment_image_src( $bg_image, 'full' );
		if (isset($attach[0]) && $attach[0]!='')
			$bg_image = $attach[0];
	}
	if ($bg_image) {
		$css_bg .= $css . 'background-image: url('.esc_url($bg_image).');';
		$css = $css_dim;
	} else {
		$css .= $css_dim;
	}
	$output = empty($src) 
			? '' 
			: (
				(!empty($bg_image) 
					? '<div class="sc_zoom_wrap'
							. (!empty($class) ? ' '.esc_attr($class) : '')
							. ($align && $align!='none' ? ' align'.esc_attr($align) : '') 
							. '"'
						. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
						. ($css_bg!='' ? ' style="'.esc_attr($css_bg).'"' : '') 
						. '>' 
					: '')
				.'<div' . ($id ? ' id="'.esc_attr($id).'"' : '') 
					. ' class="sc_zoom' 
							. (empty($bg_image) && !empty($class) ? ' '.esc_attr($class) : '') 
							. (empty($bg_image) && $align && $align!='none' ? ' align'.esc_attr($align) : '')
							. '"'
						. (empty($bg_image) && !themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
						. ($css!='' ? ' style="'.esc_attr($css).'"' : '') 
						. '>'
						. '<img src="'.esc_url($src).'"' . ($css_dim!='' ? ' style="'.esc_attr($css_dim).'"' : '') . ' data-zoom-image="'.esc_url($over).'" alt="" />'
				. '</div>'
				. (!empty($bg_image) 
					? '</div>' 
					: '')
			);
	return apply_filters('themerex_shortcode_output', $output, 'trx_zoom', $atts, $content);
}
// ---------------------------------- [/trx_zoom] ---------------------------------------



// ---------------------------------- [trx_lessons] ---------------------------------------

add_shortcode('trx_lessons', 'themerex_sc_lessons');

/*
[trx_lessons course_id="id"]
*/
function themerex_sc_lessons($atts, $content=null){
	if (themerex_sc_in_shortcode_blogger()) return '';
	extract(shortcode_atts(array(
		// Individual params
		"course_id" => "",
		"align" => "",
		"title" => "",
		"description" => "",
		// Common params
		"id" => "",
		"class" => "",
		"animation" => "",
		"css" => "",
		"width" => "",
		"height" => "",
		"top" => "",
		"bottom" => "",
		"left" => "",
		"right" => ""
	), $atts));

	$css .= themerex_get_css_position_from_values($top, $right, $bottom, $left, $width, $height);
	$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
		. ' class="sc_lessons'
		. (!empty($class) ? ' '.esc_attr($class) : '')
		. ($align && $align!='none' ? ' align'.esc_attr($align) : '')
		. '"'
		. (!themerex_sc_param_is_off($animation) ? ' data-animation="'.esc_attr(themerex_sc_get_animation_classes($animation)).'"' : '')
		. ($css!='' ? ' style="'.esc_attr($css).'"' : '')
		. '>'
		. themerex_get_lessons_links($course_id, 0, array(
				'header' => $title,
				'description' => $description,
				'show_lessons' => true,
				'show_prev_next' => false
			)
		)
		. '</div>';

	return apply_filters('themerex_shortcode_output', $output, 'trx_lessons', $atts, $content);
}
// ---------------------------------- [/trx_lessons] ---------------------------------------


// Add [trx_lessons] in the shortcodes list
if (!function_exists('themerex_lesson_require_shortcodes')) {
	//Handler of add_filter('themerex_action_shortcodes_list',	'themerex_lesson_require_shortcodes');
	function themerex_lesson_require_shortcodes() {
		global $THEMEREX_GLOBALS;
		if (isset($THEMEREX_GLOBALS['shortcodes'])) {

			$courses = themerex_get_list_posts(false, array(
					'post_type' => 'courses',
					'orderby' => 'title',
					'order' => 'asc'
				)
			);

			themerex_array_insert_after($THEMEREX_GLOBALS['shortcodes'], 'trx_infobox', array(

				// Lessons
				"trx_lessons" => array(
					"title" => __("Lessons", 'education'),
					"desc" => __("Insert list of lessons for specified course", 'education'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"course_id" => array(
							"title" => __("Course", 'education'),
							"desc" => __("Select the desired course", 'education'),
							"value" => "",
							"options" => $courses,
							"type" => "select"
						),
						"title" => array(
							"title" => __("Title", 'education'),
							"desc" => __("Title for the section with lessons", 'education'),
							"divider" => true,
							"dependency" => array(
								'course_id' => array('not_empty')
							),
							"value" => "",
							"type" => "text"
						),
						"description" => array(
							"title" => __("Description", 'education'),
							"desc" => __("Description for the section with lessons", 'education'),
							"divider" => true,
							"dependency" => array(
								'course_id' => array('not_empty')
							),
							"value" => "",
							"type" => "text"
						),
						"align" => array(
							"title" => __("Alignment", 'education'),
							"desc" => __("Align block to the left or right side", 'education'),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['float']
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				)

			));
		}
	}
}


// Add [trx_lessons] in the VC shortcodes list
if (!function_exists('themerex_lesson_require_shortcodes_vc')) {
	//Handler of add_filter('themerex_action_shortcodes_list_vc',	'themerex_lesson_require_shortcodes_vc');
	function themerex_lesson_require_shortcodes_vc() {
		global $THEMEREX_GLOBALS;

		$courses = themerex_get_list_posts(false, array(
				'post_type' => 'courses',
				'orderby' => 'title',
				'order' => 'asc'
			)
		);

		// Lessons
		vc_map( array(
			"base" => "trx_lessons",
			"name" => __("Lessons", 'education'),
			"description" => __("Insert list of lessons for specified course", 'education'),
			"category" => __('Content', 'education'),
			'icon' => 'icon_trx_lessons',
			"class" => "trx_sc_single trx_sc_lessons",
			"content_element" => true,
			"is_container" => false,
			"show_settings_on_create" => true,
			"params" => array(
				array(
					"param_name" => "course_id",
					"heading" => __("Course", 'education'),
					"description" => __("Select the desired course", 'education'),
					"admin_label" => true,
					"class" => "",
					"value" => array_flip($courses),
					"type" => "dropdown"
				),
				array(
					"param_name" => "title",
					"heading" => __("Title", 'education'),
					"description" => __("Title for the section with lessons", 'education'),
					"admin_label" => true,
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "description",
					"heading" => __("Description", 'education'),
					"description" => __("Description for the section with lessons", 'education'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
				array(
					"param_name" => "align",
					"heading" => __("Alignment", 'education'),
					"description" => __("Alignment of the lessons block", 'education'),
					"class" => "",
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
					"type" => "dropdown"
				),
				themerex_vc_width(),
				themerex_vc_height(),
				$THEMEREX_GLOBALS['vc_params']['margin_top'],
				$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
				$THEMEREX_GLOBALS['vc_params']['margin_left'],
				$THEMEREX_GLOBALS['vc_params']['margin_right'],
				$THEMEREX_GLOBALS['vc_params']['id'],
				$THEMEREX_GLOBALS['vc_params']['class'],
				$THEMEREX_GLOBALS['vc_params']['animation'],
				$THEMEREX_GLOBALS['vc_params']['css']
			)
		) );

		class WPBakeryShortCode_Trx_Lessons extends THEMEREX_VC_ShortCodeSingle {}
	}
}

?>