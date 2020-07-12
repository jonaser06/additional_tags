<?php

// Check if shortcodes settings are now used
if ( !function_exists( 'themerex_shortcodes_is_used' ) ) {
	function themerex_shortcodes_is_used() {
		return themerex_options_is_used() 															// All modes when Theme Options are used
			|| (is_admin() && isset($_POST['action']) 
					&& in_array($_POST['action'], array('vc_edit_form', 'wpb_show_edit_form')))		// AJAX query when save post/page
			|| themerex_vc_is_frontend();															// VC Frontend editor mode
	}
}

// Width and height params
if ( !function_exists( 'themerex_shortcodes_width' ) ) {
	function themerex_shortcodes_width($w="") {
		return array(
			"title" => __("Width", 'additional-tags'),
			"divider" => true,
			"value" => $w,
			"type" => "text"
		);
	}
}
if ( !function_exists( 'themerex_shortcodes_height' ) ) {
	function themerex_shortcodes_height($h='') {
		return array(
			"title" => __("Height", 'additional-tags'),
			"desc" => __("Width (in pixels or percent) and height (only in pixels) of element", 'additional-tags'),
			"value" => $h,
			"type" => "text"
		);
	}
}

/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'themerex_shortcodes_settings_theme_setup' ) ) {
//	if ( themerex_vc_is_frontend() )
	if ( (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') || (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline') )
		add_action( 'themerex_action_before_init_theme', 'themerex_shortcodes_settings_theme_setup', 20 );
	else
		add_action( 'themerex_action_after_init_theme', 'themerex_shortcodes_settings_theme_setup' );
	function themerex_shortcodes_settings_theme_setup() {
		if (themerex_shortcodes_is_used()) {
			global $THEMEREX_GLOBALS;

			// Prepare arrays 
			$THEMEREX_GLOBALS['sc_params'] = array(
			
				// Current element id
				'id' => array(
					"title" => __("Element ID", 'additional-tags'),
					"desc" => __("ID for current element", 'additional-tags'),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
			
				// Current element class
				'class' => array(
					"title" => __("Element CSS class", 'additional-tags'),
					"desc" => __("CSS class for current element (optional)", 'additional-tags'),
					"value" => "",
					"type" => "text"
				),
			
				// Current element style
				'css' => array(
					"title" => __("CSS styles", 'additional-tags'),
					"desc" => __("Any additional CSS rules (if need)", 'additional-tags'),
					"value" => "",
					"type" => "text"
				),
			
				// Margins params
				'top' => array(
					"title" => __("Top margin", 'additional-tags'),
					"divider" => true,
					"value" => "",
					"type" => "text"
				),
			
				'bottom' => array(
					"title" => __("Bottom margin", 'additional-tags'),
					"value" => "",
					"type" => "text"
				),
			
				'left' => array(
					"title" => __("Left margin", 'additional-tags'),
					"value" => "",
					"type" => "text"
				),
			
				'right' => array(
					"title" => __("Right margin", 'additional-tags'),
					"desc" => __("Margins around list (in pixels).", 'additional-tags'),
					"value" => "",
					"type" => "text"
				),
			
				// Switcher choises
				'list_styles' => array(
					'ul'	=> __('Unordered', 'additional-tags'),
					'ol'	=> __('Ordered', 'additional-tags'),
					'iconed'=> __('Iconed', 'additional-tags')
				),
				'yes_no'	=> themerex_get_list_yesno(),
				'on_off'	=> themerex_get_list_onoff(),
				'dir' 		=> themerex_get_list_directions(),
				'align'		=> themerex_get_list_alignments(),
				'float'		=> themerex_get_list_floats(),
				'show_hide'	=> themerex_get_list_showhide(),
				'sorting' 	=> themerex_get_list_sortings(),
				'ordering' 	=> themerex_get_list_orderings(),
				'sliders'	=> themerex_get_list_sliders(),
				'users'		=> themerex_get_list_users(),
				'members'	=> themerex_get_list_posts(false, array('post_type'=>'team', 'orderby'=>'title', 'order'=>'asc', 'return'=>'title')),
				'categories'=> is_admin() && themerex_get_value_gp('action')=='vc_edit_form' && substr(themerex_get_value_gp('tag'), 0, 4)=='trx_' && isset($_POST['params']['post_type']) && $_POST['params']['post_type']!='post'
					? themerex_get_list_terms(false, themerex_get_taxonomy_categories_by_post_type($_POST['params']['post_type']))
					: themerex_get_list_categories(),
				'testimonials_groups'=> themerex_get_list_terms(false, 'testimonial_group'),
				'team_groups'=> themerex_get_list_terms(false, 'team_group'),
				'columns'	=> themerex_get_list_columns(),
				'images'	=> array_merge(array('none'=>"none"), themerex_get_list_files("images/icons", "png")),
				'icons'		=> array_merge(array("inherit", "none"), themerex_get_list_icons()),
				'locations'	=> themerex_get_list_dedicated_locations(),
				'filters'	=> themerex_get_list_portfolio_filters(),
				'formats'	=> themerex_get_list_post_formats_filters(),
				'hovers'	=> themerex_get_list_hovers(),
				'hovers_dir'=> themerex_get_list_hovers_directions(),
				'tint'		=> themerex_get_list_bg_tints(),
				'animations'=> themerex_get_list_animations_in(),
				'blogger_styles'	=> themerex_get_list_templates_blogger(),
				'posts_types'		=> themerex_get_list_posts_types(),
				'button_styles'		=> themerex_get_list_button_styles(),
				'googlemap_styles'	=> themerex_get_list_googlemap_styles(),
				'field_types'		=> themerex_get_list_field_types(),
				'label_positions'	=> themerex_get_list_label_positions()
			);

			$THEMEREX_GLOBALS['sc_params']['animation'] = array(
				"title" => __("Animation", 'additional-tags'),
				"desc" => __('Select animation while object enter in the visible area of page', 'additional-tags'),
				"value" => "none",
				"type" => "select",
				"options" => $THEMEREX_GLOBALS['sc_params']['animations']
			);

            $list_post_types = get_post_types();

            // hide some of post types
            unset($list_post_types['nav_menu_item']);
            unset($list_post_types['custom_css']);
            unset($list_post_types['customize_changeset']);




			// Shortcodes list
			//------------------------------------------------------------------
			$THEMEREX_GLOBALS['shortcodes'] = array(
			
				// Accordion
				"trx_accordion" => array(
					"title" => __("Accordion", 'additional-tags'),
					"desc" => __("Accordion items", 'additional-tags'),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"style" => array(
							"title" => __("Accordion style", 'additional-tags'),
							"desc" => __("Select style for display accordion", 'additional-tags'),
							"value" => 1,
							"options" => array(
								1 => __('Style 1', 'additional-tags'),
								2 => __('Style 2', 'additional-tags')
							),
							"type" => "radio"
						),
						"counter" => array(
							"title" => __("Counter", 'additional-tags'),
							"desc" => __("Display counter before each accordion title", 'additional-tags'),
							"value" => "off",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['on_off']
						),
						"initial" => array(
							"title" => __("Initially opened item", 'additional-tags'),
							"desc" => __("Number of initially opened item", 'additional-tags'),
							"value" => 1,
							"min" => 0,
							"type" => "spinner"
						),
						"icon_closed" => array(
							"title" => __("Icon while closed", 'additional-tags'),
							"desc" => __('Select icon for the closed accordion item from Fontello icons set', 'additional-tags'),
							"value" => "",
							"type" => "icons",
							"options" => $THEMEREX_GLOBALS['sc_params']['icons']
						),
						"icon_opened" => array(
							"title" => __("Icon while opened", 'additional-tags'),
							"desc" => __('Select icon for the opened accordion item from Fontello icons set', 'additional-tags'),
							"value" => "",
							"type" => "icons",
							"options" => $THEMEREX_GLOBALS['sc_params']['icons']
						),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					),
					"children" => array(
						"name" => "trx_accordion_item",
						"title" => __("Item", 'additional-tags'),
						"desc" => __("Accordion item", 'additional-tags'),
						"container" => true,
						"params" => array(
							"title" => array(
								"title" => __("Accordion item title", 'additional-tags'),
								"desc" => __("Title for current accordion item", 'additional-tags'),
								"value" => "",
								"type" => "text"
							),
							"icon_closed" => array(
								"title" => __("Icon while closed", 'additional-tags'),
								"desc" => __('Select icon for the closed accordion item from Fontello icons set', 'additional-tags'),
								"value" => "",
								"type" => "icons",
								"options" => $THEMEREX_GLOBALS['sc_params']['icons']
							),
							"icon_opened" => array(
								"title" => __("Icon while opened", 'additional-tags'),
								"desc" => __('Select icon for the opened accordion item from Fontello icons set', 'additional-tags'),
								"value" => "",
								"type" => "icons",
								"options" => $THEMEREX_GLOBALS['sc_params']['icons']
							),
							"_content_" => array(
								"title" => __("Accordion item content", 'additional-tags'),
								"desc" => __("Current accordion item content", 'additional-tags'),
								"rows" => 4,
								"value" => "",
								"type" => "textarea"
							),
							"id" => $THEMEREX_GLOBALS['sc_params']['id'],
							"class" => $THEMEREX_GLOBALS['sc_params']['class'],
							"css" => $THEMEREX_GLOBALS['sc_params']['css']
						)
					)
				),
			
			
			
			
				// Anchor
				"trx_anchor" => array(
					"title" => __("Anchor", 'additional-tags'),
					"desc" => __("Insert anchor for the TOC (table of content)", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"icon" => array(
							"title" => __("Anchor's icon", 'additional-tags'),
							"desc" => __('Select icon for the anchor from Fontello icons set', 'additional-tags'),
							"value" => "",
							"type" => "icons",
							"options" => $THEMEREX_GLOBALS['sc_params']['icons']
						),
						"title" => array(
							"title" => __("Short title", 'additional-tags'),
							"desc" => __("Short title of the anchor (for the table of content)", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"description" => array(
							"title" => __("Long description", 'additional-tags'),
							"desc" => __("Description for the popup (then hover on the icon). You can use '{' and '}' - make the text italic, '|' - insert line break", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"url" => array(
							"title" => __("External URL", 'additional-tags'),
							"desc" => __("External URL for this TOC item", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"separator" => array(
							"title" => __("Add separator", 'additional-tags'),
							"desc" => __("Add separator under item in the TOC", 'additional-tags'),
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"id" => $THEMEREX_GLOBALS['sc_params']['id']
					)
				),
			
			
				// Audio
				"trx_audio" => array(
					"title" => __("Audio", 'additional-tags'),
					"desc" => __("Insert audio player", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"url" => array(
							"title" => __("URL for audio file", 'additional-tags'),
							"desc" => __("URL for audio file", 'additional-tags'),
							"readonly" => false,
							"value" => "",
							"type" => "media",
							"before" => array(
								'title' => __('Choose audio', 'additional-tags'),
								'action' => 'media_upload',
								'type' => 'audio',
								'multiple' => false,
								'linked_field' => '',
								'captions' => array( 	
									'choose' => __('Choose audio file', 'additional-tags'),
									'update' => __('Select audio file', 'additional-tags')
								)
							),
							"after" => array(
								'icon' => 'icon-cancel',
								'action' => 'media_reset'
							)
						),
						"image" => array(
							"title" => __("Cover image", 'additional-tags'),
							"desc" => __("Select or upload image or write URL from other site for audio cover", 'additional-tags'),
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"title" => array(
							"title" => __("Title", 'additional-tags'),
							"desc" => __("Title of the audio file", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "text"
						),
						"author" => array(
							"title" => __("Author", 'additional-tags'),
							"desc" => __("Author of the audio file", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"controls" => array(
							"title" => __("Show controls", 'additional-tags'),
							"desc" => __("Show controls in audio player", 'additional-tags'),
							"divider" => true,
							"size" => "medium",
							"value" => "show",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['show_hide']
						),
						"autoplay" => array(
							"title" => __("Autoplay audio", 'additional-tags'),
							"desc" => __("Autoplay audio on page load", 'additional-tags'),
							"value" => "off",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['on_off']
						),
						"align" => array(
							"title" => __("Align", 'additional-tags'),
							"desc" => __("Select block alignment", 'additional-tags'),
							"value" => "none",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
				// Block
				"trx_block" => array(
					"title" => __("Block container", 'additional-tags'),
					"desc" => __("Container for any block ([section] analog - to enable nesting)", 'additional-tags'),
					"decorate" => true,
					"container" => true,
					"params" => array(
						"dedicated" => array(
							"title" => __("Dedicated", 'additional-tags'),
							"desc" => __("Use this block as dedicated content - show it before post title on single page", 'additional-tags'),
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"align" => array(
							"title" => __("Align", 'additional-tags'),
							"desc" => __("Select block alignment", 'additional-tags'),
							"value" => "none",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						),
						"columns" => array(
							"title" => __("Columns emulation", 'additional-tags'),
							"desc" => __("Select width for columns emulation", 'additional-tags'),
							"value" => "none",
							"type" => "checklist",
							"options" => $THEMEREX_GLOBALS['sc_params']['columns']
						), 
						"pan" => array(
							"title" => __("Use pan effect", 'additional-tags'),
							"desc" => __("Use pan effect to show section content", 'additional-tags'),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"scroll" => array(
							"title" => __("Use scroller", 'additional-tags'),
							"desc" => __("Use scroller to show section content", 'additional-tags'),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"scroll_dir" => array(
							"title" => __("Scroll direction", 'additional-tags'),
							"desc" => __("Scroll direction (if Use scroller = yes)", 'additional-tags'),
							"dependency" => array(
								'scroll' => array('yes')
							),
							"value" => "horizontal",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['dir']
						),
						"scroll_controls" => array(
							"title" => __("Scroll controls", 'additional-tags'),
							"desc" => __("Show scroll controls (if Use scroller = yes)", 'additional-tags'),
							"dependency" => array(
								'scroll' => array('yes')
							),
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"color" => array(
							"title" => __("Fore color", 'additional-tags'),
							"desc" => __("Any color for objects in this section", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "color"
						),
						"bg_tint" => array(
							"title" => __("Background tint", 'additional-tags'),
							"desc" => __("Main background tint: dark or light", 'additional-tags'),
							"value" => "",
							"type" => "checklist",
							"options" => $THEMEREX_GLOBALS['sc_params']['tint']
						),
						"bg_color" => array(
							"title" => __("Background color", 'additional-tags'),
							"desc" => __("Any background color for this section", 'additional-tags'),
							"value" => "",
							"type" => "color"
						),
						"bg_image" => array(
							"title" => __("Background image URL", 'additional-tags'),
							"desc" => __("Select or upload image or write URL from other site for the background", 'additional-tags'),
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"bg_overlay" => array(
							"title" => __("Overlay", 'additional-tags'),
							"desc" => __("Overlay color opacity (from 0.0 to 1.0)", 'additional-tags'),
							"min" => "0",
							"max" => "1",
							"step" => "0.1",
							"value" => "0",
							"type" => "spinner"
						),
						"bg_texture" => array(
							"title" => __("Texture", 'additional-tags'),
							"desc" => __("Predefined texture style from 1 to 11. 0 - without texture.", 'additional-tags'),
							"min" => "0",
							"max" => "11",
							"step" => "1",
							"value" => "0",
							"type" => "spinner"
						),
						"font_size" => array(
							"title" => __("Font size", 'additional-tags'),
							"desc" => __("Font size of the text (default - in pixels, allows any CSS units of measure)", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"font_weight" => array(
							"title" => __("Font weight", 'additional-tags'),
							"desc" => __("Font weight of the text", 'additional-tags'),
							"value" => "",
							"type" => "select",
							"size" => "medium",
							"options" => array(
								'100' => __('Thin (100)', 'additional-tags'),
								'300' => __('Light (300)', 'additional-tags'),
								'400' => __('Normal (400)', 'additional-tags'),
								'700' => __('Bold (700)', 'additional-tags')
							)
						),
						"_content_" => array(
							"title" => __("Container content", 'additional-tags'),
							"desc" => __("Content for section container", 'additional-tags'),
							"divider" => true,
							"rows" => 4,
							"value" => "",
							"type" => "textarea"
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
				// Blogger
				"trx_blogger" => array(
					"title" => __("Blogger", 'additional-tags'),
					"desc" => __("Insert posts (pages) in many styles from desired categories or directly from ids", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"style" => array(
							"title" => __("Posts output style", 'additional-tags'),
							"desc" => __("Select desired style for posts output", 'additional-tags'),
							"value" => "regular",
							"type" => "select",
							"options" => $THEMEREX_GLOBALS['sc_params']['blogger_styles']
						),
						"filters" => array(
							"title" => __("Show filters", 'additional-tags'),
							"desc" => __("Use post's tags or categories as filter buttons", 'additional-tags'),
							"value" => "no",
							"dir" => "horizontal",
							"type" => "checklist",
							"options" => $THEMEREX_GLOBALS['sc_params']['filters']
						),
						"hover" => array(
							"title" => __("Hover effect", 'additional-tags'),
							"desc" => __("Select hover effect (only if style=Portfolio)", 'additional-tags'),
							"dependency" => array(
								'style' => array('portfolio','grid','square','courses')
							),
							"value" => "",
							"type" => "select",
							"options" => $THEMEREX_GLOBALS['sc_params']['hovers']
						),
						"hover_dir" => array(
							"title" => __("Hover direction", 'additional-tags'),
							"desc" => __("Select hover direction (only if style=Portfolio and hover=Circle|Square)", 'additional-tags'),
							"dependency" => array(
								'style' => array('portfolio','grid','square','courses'),
								'hover' => array('square','circle')
							),
							"value" => "left_to_right",
							"type" => "select",
							"options" => $THEMEREX_GLOBALS['sc_params']['hovers_dir']
						),
						"dir" => array(
							"title" => __("Posts direction", 'additional-tags'),
							"desc" => __("Display posts in horizontal or vertical direction", 'additional-tags'),
							"value" => "horizontal",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['dir']
						),
						"post_type" => array(
							"title" => __("Post type", 'additional-tags'),
							"desc" => __("Select post type to show", 'additional-tags'),
							"value" => "post",
							"type" => "select",
							"options" => $THEMEREX_GLOBALS['sc_params']['posts_types']
						),
						"ids" => array(
							"title" => __("Post IDs list", 'additional-tags'),
							"desc" => __("Comma separated list of posts ID. If set - parameters above are ignored!", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"cat" => array(
							"title" => __("Categories list", 'additional-tags'),
							"desc" => __("Select the desired categories. If not selected - show posts from any category or from IDs list", 'additional-tags'),
							"dependency" => array(
								'ids' => array('is_empty'),
								'post_type' => array('refresh')
							),
							"divider" => true,
							"value" => "",
							"type" => "select",
							"style" => "list",
							"multiple" => true,
							"options" => $THEMEREX_GLOBALS['sc_params']['categories']
						),
						"count" => array(
							"title" => __("Total posts to show", 'additional-tags'),
							"desc" => __("How many posts will be displayed? If used IDs - this parameter ignored.", 'additional-tags'),
							"dependency" => array(
								'ids' => array('is_empty')
							),
							"value" => 3,
							"min" => 1,
							"max" => 100,
							"type" => "spinner"
						),
						"columns" => array(
							"title" => __("Columns number", 'additional-tags'),
							"desc" => __("How many columns used to show posts? If empty or 0 - equal to posts number", 'additional-tags'),
							"dependency" => array(
								'dir' => array('horizontal')
							),
							"value" => 3,
							"min" => 1,
							"max" => 100,
							"type" => "spinner"
						),
						"offset" => array(
							"title" => __("Offset before select posts", 'additional-tags'),
							"desc" => __("Skip posts before select next part.", 'additional-tags'),
							"dependency" => array(
								'ids' => array('is_empty')
							),
							"value" => 0,
							"min" => 0,
							"max" => 100,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => __("Post order by", 'additional-tags'),
							"desc" => __("Select desired posts sorting method", 'additional-tags'),
							"value" => "date",
							"type" => "select",
							"options" => $THEMEREX_GLOBALS['sc_params']['sorting']
						),
						"order" => array(
							"title" => __("Post order", 'additional-tags'),
							"desc" => __("Select desired posts order", 'additional-tags'),
							"value" => "asc",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
						),
						"only" => array(
							"title" => __("Select posts only", 'additional-tags'),
							"desc" => __("Select posts only with reviews, videos, audios, thumbs or galleries", 'additional-tags'),
							"value" => "no",
							"type" => "select",
							"options" => $THEMEREX_GLOBALS['sc_params']['formats']
						),
						"scroll" => array(
							"title" => __("Use scroller", 'additional-tags'),
							"desc" => __("Use scroller to show all posts", 'additional-tags'),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"controls" => array(
							"title" => __("Show slider controls", 'additional-tags'),
							"desc" => __("Show arrows to control scroll slider", 'additional-tags'),
							"dependency" => array(
								'scroll' => array('yes')
							),
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"location" => array(
							"title" => __("Dedicated content location", 'additional-tags'),
							"desc" => __("Select position for dedicated content (only for style=excerpt)", 'additional-tags'),
							"divider" => true,
							"dependency" => array(
								'style' => array('excerpt')
							),
							"value" => "default",
							"type" => "select",
							"options" => $THEMEREX_GLOBALS['sc_params']['locations']
						),
						"rating" => array(
							"title" => __("Show rating stars", 'additional-tags'),
							"desc" => __("Show rating stars under post's header", 'additional-tags'),
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"info" => array(
							"title" => __("Show post info block", 'additional-tags'),
							"desc" => __("Show post info block (author, date, tags, etc.)", 'additional-tags'),
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"links" => array(
							"title" => __("Allow links on the post", 'additional-tags'),
							"desc" => __("Allow links on the post from each blogger item", 'additional-tags'),
							"value" => "yes",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"descr" => array(
							"title" => __("Description length", 'additional-tags'),
							"desc" => __("How many characters are displayed from post excerpt? If 0 - don't show description", 'additional-tags'),
							"value" => 0,
							"min" => 0,
							"step" => 10,
							"type" => "spinner"
						),
						"readmore" => array(
							"title" => __("More link text", 'additional-tags'),
							"desc" => __("Read more link text. If empty - show 'More', else - used as link text", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
			
				// Br
				"trx_br" => array(
					"title" => __("Break", 'additional-tags'),
					"desc" => __("Line break with clear floating (if need)", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"clear" => 	array(
							"title" => __("Clear floating", 'additional-tags'),
							"desc" => __("Clear floating (if need)", 'additional-tags'),
							"value" => "",
							"type" => "checklist",
							"options" => array(
								'none' => __('None', 'additional-tags'),
								'left' => __('Left', 'additional-tags'),
								'right' => __('Right', 'additional-tags'),
								'both' => __('Both', 'additional-tags')
							)
						)
					)
				),
			
			
			
			
				// Button
				"trx_button" => array(
					"title" => __("Button", 'additional-tags'),
					"desc" => __("Button with link", 'additional-tags'),
					"decorate" => false,
					"container" => true,
					"params" => array(
						"_content_" => array(
							"title" => __("Caption", 'additional-tags'),
							"desc" => __("Button caption", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"type" => array(
							"title" => __("Button's shape", 'additional-tags'),
							"desc" => __("Select button's shape", 'additional-tags'),
							"value" => "square",
							"size" => "medium",
							"options" => array(
								'square' => __('Square', 'additional-tags'),
								'round' => __('Round', 'additional-tags')
							),
							"type" => "switch"
						), 
						"style" => array(
							"title" => __("Button's style", 'additional-tags'),
							"desc" => __("Select button's style", 'additional-tags'),
							"value" => "default",
							"dir" => "horizontal",
							"options" => array(
								'filled' => __('Filled', 'additional-tags'),
								'border' => __('Border', 'additional-tags')
							),
							"type" => "checklist"
						), 
						"size" => array(
							"title" => __("Button's size", 'additional-tags'),
							"desc" => __("Select button's size", 'additional-tags'),
							"value" => "small",
							"dir" => "horizontal",
							"options" => array(
								'small' => __('Small', 'additional-tags'),
								'medium' => __('Medium', 'additional-tags'),
								'large' => __('Large', 'additional-tags')
							),
							"type" => "checklist"
						), 
						"icon" => array(
							"title" => __("Button's icon", 'additional-tags'),
							"desc" => __('Select icon for the title from Fontello icons set', 'additional-tags'),
							"value" => "",
							"type" => "icons",
							"options" => $THEMEREX_GLOBALS['sc_params']['icons']
						),
						"bg_style" => array(
							"title" => __("Button's color scheme", 'additional-tags'),
							"desc" => __("Select button's color scheme", 'additional-tags'),
							"value" => "custom",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['button_styles']
						), 
						"color" => array(
							"title" => __("Button's text color", 'additional-tags'),
							"desc" => __("Any color for button's caption", 'additional-tags'),
							"value" => "",
							"type" => "color"
						),
						"bg_color" => array(
							"title" => __("Button's backcolor", 'additional-tags'),
							"desc" => __("Any color for button's background", 'additional-tags'),
							"value" => "",
							"type" => "color"
						),
						"align" => array(
							"title" => __("Button's alignment", 'additional-tags'),
							"desc" => __("Align button to left, center or right", 'additional-tags'),
							"value" => "none",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						), 
						"link" => array(
							"title" => __("Link URL", 'additional-tags'),
							"desc" => __("URL for link on button click", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "text"
						),
						"target" => array(
							"title" => __("Link target", 'additional-tags'),
							"desc" => __("Target for link on button click", 'additional-tags'),
							"dependency" => array(
								'link' => array('not_empty')
							),
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"popup" => array(
							"title" => __("Open link in popup", 'additional-tags'),
							"desc" => __("Open link target in popup window", 'additional-tags'),
							"dependency" => array(
								'link' => array('not_empty')
							),
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						), 
						"rel" => array(
							"title" => __("Rel attribute", 'additional-tags'),
							"desc" => __("Rel attribute for button's link (if need)", 'additional-tags'),
							"dependency" => array(
								'link' => array('not_empty')
							),
							"value" => "",
							"type" => "text"
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
				// Chat
				"trx_chat" => array(
					"title" => __("Chat", 'additional-tags'),
					"desc" => __("Chat message", 'additional-tags'),
					"decorate" => true,
					"container" => true,
					"params" => array(
						"title" => array(
							"title" => __("Item title", 'additional-tags'),
							"desc" => __("Chat item title", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"photo" => array(
							"title" => __("Item photo", 'additional-tags'),
							"desc" => __("Select or upload image or write URL from other site for the item photo (avatar)", 'additional-tags'),
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"link" => array(
							"title" => __("Item link", 'additional-tags'),
							"desc" => __("Chat item link", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"_content_" => array(
							"title" => __("Chat item content", 'additional-tags'),
							"desc" => __("Current chat item content", 'additional-tags'),
							"rows" => 4,
							"value" => "",
							"type" => "textarea"
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
				// Columns
				"trx_columns" => array(
					"title" => __("Columns", 'additional-tags'),
					"desc" => __("Insert up to 5 columns in your page (post)", 'additional-tags'),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"fluid" => array(
							"title" => __("Fluid columns", 'additional-tags'),
							"desc" => __("To squeeze the columns when reducing the size of the window (fluid=yes) or to rebuild them (fluid=no)", 'additional-tags'),
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						), 
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					),
					"children" => array(
						"name" => "trx_column_item",
						"title" => __("Column", 'additional-tags'),
						"desc" => __("Column item", 'additional-tags'),
						"container" => true,
						"params" => array(
							"span" => array(
								"title" => __("Merge columns", 'additional-tags'),
								"desc" => __("Count merged columns from current", 'additional-tags'),
								"value" => "",
								"type" => "text"
							),
							"align" => array(
								"title" => __("Alignment", 'additional-tags'),
								"desc" => __("Alignment text in the column", 'additional-tags'),
								"value" => "",
								"type" => "checklist",
								"dir" => "horizontal",
								"options" => $THEMEREX_GLOBALS['sc_params']['align']
							),
							"color" => array(
								"title" => __("Fore color", 'additional-tags'),
								"desc" => __("Any color for objects in this column", 'additional-tags'),
								"value" => "",
								"type" => "color"
							),
							"bg_color" => array(
								"title" => __("Background color", 'additional-tags'),
								"desc" => __("Any background color for this column", 'additional-tags'),
								"value" => "",
								"type" => "color"
							),
							"bg_image" => array(
								"title" => __("URL for background image file", 'additional-tags'),
								"desc" => __("Select or upload image or write URL from other site for the background", 'additional-tags'),
								"readonly" => false,
								"value" => "",
								"type" => "media"
							),
							"_content_" => array(
								"title" => __("Column item content", 'additional-tags'),
								"desc" => __("Current column item content", 'additional-tags'),
								"divider" => true,
								"rows" => 4,
								"value" => "",
								"type" => "textarea"
							),
							"id" => $THEMEREX_GLOBALS['sc_params']['id'],
							"class" => $THEMEREX_GLOBALS['sc_params']['class'],
							"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
							"css" => $THEMEREX_GLOBALS['sc_params']['css']
						)
					)
				),
			
			
			
			
				// Contact form
				"trx_contact_form" => array(
					"title" => __("Contact form", 'additional-tags'),
					"desc" => __("Insert contact form", 'additional-tags'),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"custom" => array(
							"title" => __("Custom", 'additional-tags'),
							"desc" => __("Use custom fields or create standard contact form (ignore info from 'Field' tabs)", 'additional-tags'),
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						), 
						"action" => array(
							"title" => __("Action", 'additional-tags'),
							"desc" => __("Contact form action (URL to handle form data). If empty - use internal action", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "text"
						),
						"align" => array(
							"title" => __("Align", 'additional-tags'),
							"desc" => __("Select form alignment", 'additional-tags'),
							"value" => "none",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						),
						"title" => array(
							"title" => __("Title", 'additional-tags'),
							"desc" => __("Contact form title", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "text"
						),
						"description" => array(
							"title" => __("Description", 'additional-tags'),
							"desc" => __("Short description for contact form", 'additional-tags'),
							"divider" => true,
							"rows" => 4,
							"value" => "",
							"type" => "textarea"
						),
						"width" => themerex_shortcodes_width(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					),
					"children" => array(
						"name" => "trx_form_item",
						"title" => __("Field", 'additional-tags'),
						"desc" => __("Custom field", 'additional-tags'),
						"container" => false,
						"params" => array(
							"type" => array(
								"title" => __("Type", 'additional-tags'),
								"desc" => __("Type of the custom field", 'additional-tags'),
								"value" => "text",
								"type" => "checklist",
								"dir" => "horizontal",
								"options" => $THEMEREX_GLOBALS['sc_params']['field_types']
							), 
							"name" => array(
								"title" => __("Name", 'additional-tags'),
								"desc" => __("Name of the custom field", 'additional-tags'),
								"value" => "",
								"type" => "text"
							),
							"value" => array(
								"title" => __("Default value", 'additional-tags'),
								"desc" => __("Default value of the custom field", 'additional-tags'),
								"value" => "",
								"type" => "text"
							),
							"label" => array(
								"title" => __("Label", 'additional-tags'),
								"desc" => __("Label for the custom field", 'additional-tags'),
								"value" => "",
								"type" => "text"
							),
							"label_position" => array(
								"title" => __("Label position", 'additional-tags'),
								"desc" => __("Label position relative to the field", 'additional-tags'),
								"value" => "top",
								"type" => "checklist",
								"dir" => "horizontal",
								"options" => $THEMEREX_GLOBALS['sc_params']['label_positions']
							), 
							"top" => $THEMEREX_GLOBALS['sc_params']['top'],
							"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
							"left" => $THEMEREX_GLOBALS['sc_params']['left'],
							"right" => $THEMEREX_GLOBALS['sc_params']['right'],
							"id" => $THEMEREX_GLOBALS['sc_params']['id'],
							"class" => $THEMEREX_GLOBALS['sc_params']['class'],
							"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
							"css" => $THEMEREX_GLOBALS['sc_params']['css']
						)
					)
				),
			
			
			
			
				// Content block on fullscreen page
				"trx_content" => array(
					"title" => __("Content block", 'additional-tags'),
					"desc" => __("Container for main content block with desired class and style (use it only on fullscreen pages)", 'additional-tags'),
					"decorate" => true,
					"container" => true,
					"params" => array(
						"_content_" => array(
							"title" => __("Container content", 'additional-tags'),
							"desc" => __("Content for section container", 'additional-tags'),
							"divider" => true,
							"rows" => 4,
							"value" => "",
							"type" => "textarea"
						),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
			
				// Countdown
				"trx_countdown" => array(
					"title" => __("Countdown", 'additional-tags'),
					"desc" => __("Insert countdown object", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"date" => array(
							"title" => __("Date", 'additional-tags'),
							"desc" => __("Upcoming date (format: yyyy-mm-dd)", 'additional-tags'),
							"value" => "",
							"format" => "yy-mm-dd",
							"type" => "date"
						),
						"time" => array(
							"title" => __("Time", 'additional-tags'),
							"desc" => __("Upcoming time (format: HH:mm:ss)", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"style" => array(
							"title" => __("Style", 'additional-tags'),
							"desc" => __("Countdown style", 'additional-tags'),
							"value" => "1",
							"type" => "checklist",
							"options" => array(
								1 => __('Style 1', 'additional-tags'),
								2 => __('Style 2', 'additional-tags')
							)
						),
						"align" => array(
							"title" => __("Alignment", 'additional-tags'),
							"desc" => __("Align counter to left, center or right", 'additional-tags'),
							"divider" => true,
							"value" => "none",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						), 
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
				// Dropcaps
				"trx_dropcaps" => array(
					"title" => __("Dropcaps", 'additional-tags'),
					"desc" => __("Make first letter as dropcaps", 'additional-tags'),
					"decorate" => false,
					"container" => true,
					"params" => array(
						"style" => array(
							"title" => __("Style", 'additional-tags'),
							"desc" => __("Dropcaps style", 'additional-tags'),
							"value" => "1",
							"type" => "checklist",
							"options" => array(
								1 => __('Style 1', 'additional-tags'),
								2 => __('Style 2', 'additional-tags'),
								3 => __('Style 3', 'additional-tags'),
								4 => __('Style 4', 'additional-tags')
							)
						),
						"_content_" => array(
							"title" => __("Paragraph content", 'additional-tags'),
							"desc" => __("Paragraph with dropcaps content", 'additional-tags'),
							"divider" => true,
							"rows" => 4,
							"value" => "",
							"type" => "textarea"
						),
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
			
				// Emailer
				"trx_emailer" => array(
					"title" => __("E-mail collector", 'additional-tags'),
					"desc" => __("Collect the e-mail address into specified group", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"group" => array(
							"title" => __("Group", 'additional-tags'),
							"desc" => __("The name of group to collect e-mail address", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"open" => array(
							"title" => __("Open", 'additional-tags'),
							"desc" => __("Initially open the input field on show object", 'additional-tags'),
							"divider" => true,
							"value" => "yes",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"align" => array(
							"title" => __("Alignment", 'additional-tags'),
							"desc" => __("Align object to left, center or right", 'additional-tags'),
							"divider" => true,
							"value" => "none",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						), 
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
			
				// Gap
				"trx_gap" => array(
					"title" => __("Gap", 'additional-tags'),
					"desc" => __("Insert gap (fullwidth area) in the post content. Attention! Use the gap only in the posts (pages) without left or right sidebar", 'additional-tags'),
					"decorate" => true,
					"container" => true,
					"params" => array(
						"_content_" => array(
							"title" => __("Gap content", 'additional-tags'),
							"desc" => __("Gap inner content", 'additional-tags'),
							"rows" => 4,
							"value" => "",
							"type" => "textarea"
						)
					)
				),
			
			
			
			
			
				// Google map
				"trx_googlemap" => array(
					"title" => __("Google map", 'additional-tags'),
					"desc" => __("Google map with custom styles and several markers", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"style" => array(
							"title" => __("Map style", 'additional-tags'),
							"desc" => __("Select map style", 'additional-tags'),
							"divider" => true,
							"value" => "default",
							"type" => "checklist",
							"options" => $THEMEREX_GLOBALS['sc_params']['googlemap_styles']
						),
						"zoom" => array(
							"title" => __("Zoom", 'additional-tags'),
							"desc" => __("Map zoom factor", 'additional-tags'),
							"divider" => true,
							"value" => 16,
							"min" => 1,
							"max" => 20,
							"type" => "spinner"
						),
						"center" => array(
							"title" => esc_html__("Center", 'additional-tags'),
							"desc" => wp_kses_data( __("Lat,Lng coordinates of the map's center. If empty - use coordinates of the first marker (or specified address in the field below)", 'additional-tags') ),
							"value" => "",
							"divider" => true,
							"type" => "text"
						),
//						"width" => array(
//							"title" => esc_html__("Width", 'additional-tags'),
//							"desc" => wp_kses_data( __("Width of the element", 'additional-tags') ),
//							"value" => '100%',
//							"type" => "text"
//						),
//						"height" => array(
//							"title" => esc_html__("Height", 'additional-tags'),
//							"desc" => wp_kses_data( __("Height of the element", 'additional-tags') ),
//							"divider" => true,
//							"value" => 350,
//							"type" => "text"
//						),
						"cluster" => array(
							"title" => esc_html__("Cluster icon", 'additional-tags'),
							"desc" => wp_kses_data( __("Select or upload image for markers clusterer", 'additional-tags') ),
							"value" => "",
							"type" => "media"
						),
						"prevent_scroll" => array(
							"title" => esc_html__("Prevent scroll", 'additional-tags'),
							"desc" => wp_kses_data( __("Disallow scrolling of the map", 'additional-tags') ),
							"value" => "no",
							"size" => "small",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no'],
							"type" => "switch"
						),
						"address" => array(
							"title" => __("Address", 'additional-tags'),
							"desc" => __("Specify address in this field if you don't need unique marker, title or latlng coordinates. Otherwise, leave this field empty and fill markers below", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"width" => themerex_shortcodes_width('100%'),
						"height" => themerex_shortcodes_height(350),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					),

				),

				// Hide or show any block
				"trx_hide" => array(
					"title" => __("Hide/Show any block", 'additional-tags'),
					"desc" => __("Hide or Show any block with desired CSS-selector", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"selector" => array(
							"title" => __("Selector", 'additional-tags'),
							"desc" => __("Any block's CSS-selector", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"hide" => array(
							"title" => __("Hide or Show", 'additional-tags'),
							"desc" => __("New state for the block: hide or show", 'additional-tags'),
							"value" => "yes",
							"size" => "small",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no'],
							"type" => "switch"
						)
					)
				),
			
			
			
				// Highlght text
				"trx_highlight" => array(
					"title" => __("Highlight text", 'additional-tags'),
					"desc" => __("Highlight text with selected color, background color and other styles", 'additional-tags'),
					"decorate" => false,
					"container" => true,
					"params" => array(
						"type" => array(
							"title" => __("Type", 'additional-tags'),
							"desc" => __("Highlight type", 'additional-tags'),
							"value" => "1",
							"type" => "checklist",
							"options" => array(
								0 => __('Custom', 'additional-tags'),
								1 => __('Type 1', 'additional-tags'),
								2 => __('Type 2', 'additional-tags'),
								3 => __('Type 3', 'additional-tags')
							)
						),
						"color" => array(
							"title" => __("Color", 'additional-tags'),
							"desc" => __("Color for the highlighted text", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "color"
						),
						"bg_color" => array(
							"title" => __("Background color", 'additional-tags'),
							"desc" => __("Background color for the highlighted text", 'additional-tags'),
							"value" => "",
							"type" => "color"
						),
						"font_size" => array(
							"title" => __("Font size", 'additional-tags'),
							"desc" => __("Font size of the highlighted text (default - in pixels, allows any CSS units of measure)", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"_content_" => array(
							"title" => __("Highlighting content", 'additional-tags'),
							"desc" => __("Content for highlight", 'additional-tags'),
							"divider" => true,
							"rows" => 4,
							"value" => "",
							"type" => "textarea"
						),
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
				// Icon
				"trx_icon" => array(
					"title" => __("Icon", 'additional-tags'),
					"desc" => __("Insert icon", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"icon" => array(
							"title" => __('Icon', 'additional-tags'),
							"desc" => __('Select font icon from the Fontello icons set', 'additional-tags'),
							"value" => "",
							"type" => "icons",
							"options" => $THEMEREX_GLOBALS['sc_params']['icons']
						),
						"color" => array(
							"title" => __("Icon's color", 'additional-tags'),
							"desc" => __("Icon's color", 'additional-tags'),
							"dependency" => array(
								'icon' => array('not_empty')
							),
							"value" => "",
							"type" => "color"
						),
						"bg_shape" => array(
							"title" => __("Background shape", 'additional-tags'),
							"desc" => __("Shape of the icon background", 'additional-tags'),
							"dependency" => array(
								'icon' => array('not_empty')
							),
							"value" => "none",
							"type" => "radio",
							"options" => array(
								'none' => __('None', 'additional-tags'),
								'round' => __('Round', 'additional-tags'),
								'square' => __('Square', 'additional-tags')
							)
						),
						"bg_style" => array(
							"title" => __("Background style", 'additional-tags'),
							"desc" => __("Select icon's color scheme", 'additional-tags'),
							"value" => "custom",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['button_styles']
						), 
						"bg_color" => array(
							"title" => __("Icon's background color", 'additional-tags'),
							"desc" => __("Icon's background color", 'additional-tags'),
							"dependency" => array(
								'icon' => array('not_empty'),
								'background' => array('round','square')
							),
							"value" => "",
							"type" => "color"
						),
						"font_size" => array(
							"title" => __("Font size", 'additional-tags'),
							"desc" => __("Icon's font size", 'additional-tags'),
							"dependency" => array(
								'icon' => array('not_empty')
							),
							"value" => "",
							"type" => "spinner",
							"min" => 8,
							"max" => 240
						),
						"font_weight" => array(
							"title" => __("Font weight", 'additional-tags'),
							"desc" => __("Icon font weight", 'additional-tags'),
							"dependency" => array(
								'icon' => array('not_empty')
							),
							"value" => "",
							"type" => "select",
							"size" => "medium",
							"options" => array(
								'100' => __('Thin (100)', 'additional-tags'),
								'300' => __('Light (300)', 'additional-tags'),
								'400' => __('Normal (400)', 'additional-tags'),
								'700' => __('Bold (700)', 'additional-tags')
							)
						),
						"align" => array(
							"title" => __("Alignment", 'additional-tags'),
							"desc" => __("Icon text alignment", 'additional-tags'),
							"dependency" => array(
								'icon' => array('not_empty')
							),
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						), 
						"link" => array(
							"title" => __("Link URL", 'additional-tags'),
							"desc" => __("Link URL from this icon (if not empty)", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
				// Image
				"trx_image" => array(
					"title" => __("Image", 'additional-tags'),
					"desc" => __("Insert image into your post (page)", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"url" => array(
							"title" => __("URL for image file", 'additional-tags'),
							"desc" => __("Select or upload image or write URL from other site", 'additional-tags'),
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"title" => array(
							"title" => __("Title", 'additional-tags'),
							"desc" => __("Image title (if need)", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"icon" => array(
							"title" => __("Icon before title", 'additional-tags'),
							"desc" => __('Select icon for the title from Fontello icons set', 'additional-tags'),
							"value" => "",
							"type" => "icons",
							"options" => $THEMEREX_GLOBALS['sc_params']['icons']
						),
						"align" => array(
							"title" => __("Float image", 'additional-tags'),
							"desc" => __("Float image to left or right side", 'additional-tags'),
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['float']
						), 
						"shape" => array(
							"title" => __("Image Shape", 'additional-tags'),
							"desc" => __("Shape of the image: square (rectangle) or round", 'additional-tags'),
							"value" => "square",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => array(
								"square" => __('Square', 'additional-tags'),
								"round" => __('Round', 'additional-tags')
							)
						), 
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
				// Infobox
				"trx_infobox" => array(
					"title" => __("Infobox", 'additional-tags'),
					"desc" => __("Insert infobox into your post (page)", 'additional-tags'),
					"decorate" => false,
					"container" => true,
					"params" => array(
						"style" => array(
							"title" => __("Style", 'additional-tags'),
							"desc" => __("Infobox style", 'additional-tags'),
							"value" => "regular",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => array(
								'regular' => __('Regular', 'additional-tags'),
								'info' => __('Info', 'additional-tags'),
								'success' => __('Success', 'additional-tags'),
								'error' => __('Error', 'additional-tags')
							)
						),
						"closeable" => array(
							"title" => __("Closeable box", 'additional-tags'),
							"desc" => __("Create closeable box (with close button)", 'additional-tags'),
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"icon" => array(
							"title" => __("Custom icon", 'additional-tags'),
							"desc" => __('Select icon for the infobox from Fontello icons set. If empty - use default icon', 'additional-tags'),
							"value" => "",
							"type" => "icons",
							"options" => $THEMEREX_GLOBALS['sc_params']['icons']
						),
						"color" => array(
							"title" => __("Text color", 'additional-tags'),
							"desc" => __("Any color for text and headers", 'additional-tags'),
							"value" => "",
							"type" => "color"
						),
						"bg_color" => array(
							"title" => __("Background color", 'additional-tags'),
							"desc" => __("Any background color for this infobox", 'additional-tags'),
							"value" => "",
							"type" => "color"
						),
						"_content_" => array(
							"title" => __("Infobox content", 'additional-tags'),
							"desc" => __("Content for infobox", 'additional-tags'),
							"divider" => true,
							"rows" => 4,
							"value" => "",
							"type" => "textarea"
						),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
				// Line
				"trx_line" => array(
					"title" => __("Line", 'additional-tags'),
					"desc" => __("Insert Line into your post (page)", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"style" => array(
							"title" => __("Style", 'additional-tags'),
							"desc" => __("Line style", 'additional-tags'),
							"value" => "solid",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => array(
								'solid' => __('Solid', 'additional-tags'),
								'dashed' => __('Dashed', 'additional-tags'),
								'dotted' => __('Dotted', 'additional-tags'),
								'double' => __('Double', 'additional-tags')
							)
						),
						"color" => array(
							"title" => __("Color", 'additional-tags'),
							"desc" => __("Line color", 'additional-tags'),
							"value" => "",
							"type" => "color"
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
				// List
				"trx_list" => array(
					"title" => __("List", 'additional-tags'),
					"desc" => __("List items with specific bullets", 'additional-tags'),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"style" => array(
							"title" => __("Bullet's style", 'additional-tags'),
							"desc" => __("Bullet's style for each list item", 'additional-tags'),
							"value" => "ul",
							"type" => "checklist",
							"options" => $THEMEREX_GLOBALS['sc_params']['list_styles']
						), 
						"color" => array(
							"title" => __("Color", 'additional-tags'),
							"desc" => __("List items color", 'additional-tags'),
							"value" => "",
							"type" => "color"
						),
						"icon" => array(
							"title" => __('List icon', 'additional-tags'),
							"desc" => __("Select list icon from Fontello icons set (only for style=Iconed)", 'additional-tags'),
							"dependency" => array(
								'style' => array('iconed')
							),
							"value" => "",
							"type" => "icons",
							"options" => $THEMEREX_GLOBALS['sc_params']['icons']
						),
						"icon_color" => array(
							"title" => __("Icon color", 'additional-tags'),
							"desc" => __("List icons color", 'additional-tags'),
							"value" => "",
							"dependency" => array(
								'style' => array('iconed')
							),
							"type" => "color"
						),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					),
					"children" => array(
						"name" => "trx_list_item",
						"title" => __("Item", 'additional-tags'),
						"desc" => __("List item with specific bullet", 'additional-tags'),
						"decorate" => false,
						"container" => true,
						"params" => array(
							"_content_" => array(
								"title" => __("List item content", 'additional-tags'),
								"desc" => __("Current list item content", 'additional-tags'),
								"rows" => 4,
								"value" => "",
								"type" => "textarea"
							),
							"title" => array(
								"title" => __("List item title", 'additional-tags'),
								"desc" => __("Current list item title (show it as tooltip)", 'additional-tags'),
								"value" => "",
								"type" => "text"
							),
							"color" => array(
								"title" => __("Color", 'additional-tags'),
								"desc" => __("Text color for this item", 'additional-tags'),
								"value" => "",
								"type" => "color"
							),
							"icon" => array(
								"title" => __('List icon', 'additional-tags'),
								"desc" => __("Select list item icon from Fontello icons set (only for style=Iconed)", 'additional-tags'),
								"value" => "",
								"type" => "icons",
								"options" => $THEMEREX_GLOBALS['sc_params']['icons']
							),
							"icon_color" => array(
								"title" => __("Icon color", 'additional-tags'),
								"desc" => __("Icon color for this item", 'additional-tags'),
								"value" => "",
								"type" => "color"
							),
							"link" => array(
								"title" => __("Link URL", 'additional-tags'),
								"desc" => __("Link URL for the current list item", 'additional-tags'),
								"divider" => true,
								"value" => "",
								"type" => "text"
							),
							"target" => array(
								"title" => __("Link target", 'additional-tags'),
								"desc" => __("Link target for the current list item", 'additional-tags'),
								"value" => "",
								"type" => "text"
							),
							"id" => $THEMEREX_GLOBALS['sc_params']['id'],
							"class" => $THEMEREX_GLOBALS['sc_params']['class'],
							"css" => $THEMEREX_GLOBALS['sc_params']['css']
						)
					)
				),
			
			
			
				// Number
				"trx_number" => array(
					"title" => __("Number", 'additional-tags'),
					"desc" => __("Insert number or any word as set separate characters", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"value" => array(
							"title" => __("Value", 'additional-tags'),
							"desc" => __("Number or any word", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"align" => array(
							"title" => __("Align", 'additional-tags'),
							"desc" => __("Select block alignment", 'additional-tags'),
							"value" => "none",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
				// Parallax
				"trx_parallax" => array(
					"title" => __("Parallax", 'additional-tags'),
					"desc" => __("Create the parallax container (with asinc background image)", 'additional-tags'),
					"decorate" => false,
					"container" => true,
					"params" => array(
						"gap" => array(
							"title" => __("Create gap", 'additional-tags'),
							"desc" => __("Create gap around parallax container", 'additional-tags'),
							"value" => "no",
							"size" => "small",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no'],
							"type" => "switch"
						), 
						"dir" => array(
							"title" => __("Dir", 'additional-tags'),
							"desc" => __("Scroll direction for the parallax background", 'additional-tags'),
							"value" => "up",
							"size" => "medium",
							"options" => array(
								'up' => __('Up', 'additional-tags'),
								'down' => __('Down', 'additional-tags')
							),
							"type" => "switch"
						), 
						"speed" => array(
							"title" => __("Speed", 'additional-tags'),
							"desc" => __("Image motion speed (from 0.0 to 1.0)", 'additional-tags'),
							"min" => "0",
							"max" => "1",
							"step" => "0.1",
							"value" => "0.3",
							"type" => "spinner"
						),
						"color" => array(
							"title" => __("Text color", 'additional-tags'),
							"desc" => __("Select color for text object inside parallax block", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "color"
						),
						"bg_tint" => array(
							"title" => __("Bg tint", 'additional-tags'),
							"desc" => __("Select tint of the parallax background (for correct font color choise)", 'additional-tags'),
							"value" => "light",
							"size" => "medium",
							"options" => array(
								'light' => __('Light', 'additional-tags'),
								'dark' => __('Dark', 'additional-tags')
							),
							"type" => "switch"
						), 
						"bg_color" => array(
							"title" => __("Background color", 'additional-tags'),
							"desc" => __("Select color for parallax background", 'additional-tags'),
							"value" => "",
							"type" => "color"
						),
						"bg_image" => array(
							"title" => __("Background image", 'additional-tags'),
							"desc" => __("Select or upload image or write URL from other site for the parallax background", 'additional-tags'),
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"bg_image_x" => array(
							"title" => __("Image X position", 'additional-tags'),
							"desc" => __("Image horizontal position (as background of the parallax block) - in percent", 'additional-tags'),
							"min" => "0",
							"max" => "100",
							"value" => "50",
							"type" => "spinner"
						),
						"bg_video" => array(
							"title" => __("Video background", 'additional-tags'),
							"desc" => __("Select video from media library or paste URL for video file from other site to show it as parallax background", 'additional-tags'),
							"readonly" => false,
							"value" => "",
							"type" => "media",
							"before" => array(
								'title' => __('Choose video', 'additional-tags'),
								'action' => 'media_upload',
								'type' => 'video',
								'multiple' => false,
								'linked_field' => '',
								'captions' => array( 	
									'choose' => __('Choose video file', 'additional-tags'),
									'update' => __('Select video file', 'additional-tags')
								)
							),
							"after" => array(
								'icon' => 'icon-cancel',
								'action' => 'media_reset'
							)
						),
						"bg_video_ratio" => array(
							"title" => __("Video ratio", 'additional-tags'),
							"desc" => __("Specify ratio of the video background. For example: 16:9 (default), 4:3, etc.", 'additional-tags'),
							"value" => "16:9",
							"type" => "text"
						),
						"bg_overlay" => array(
							"title" => __("Overlay", 'additional-tags'),
							"desc" => __("Overlay color opacity (from 0.0 to 1.0)", 'additional-tags'),
							"min" => "0",
							"max" => "1",
							"step" => "0.1",
							"value" => "0",
							"type" => "spinner"
						),
						"bg_texture" => array(
							"title" => __("Texture", 'additional-tags'),
							"desc" => __("Predefined texture style from 1 to 11. 0 - without texture.", 'additional-tags'),
							"min" => "0",
							"max" => "11",
							"step" => "1",
							"value" => "0",
							"type" => "spinner"
						),
						"_content_" => array(
							"title" => __("Content", 'additional-tags'),
							"desc" => __("Content for the parallax container", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "text"
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
				// Popup
				"trx_popup" => array(
					"title" => __("Popup window", 'additional-tags'),
					"desc" => __("Container for any html-block with desired class and style for popup window", 'additional-tags'),
					"decorate" => true,
					"container" => true,
					"params" => array(
						"_content_" => array(
							"title" => __("Container content", 'additional-tags'),
							"desc" => __("Content for section container", 'additional-tags'),
							"divider" => true,
							"rows" => 4,
							"value" => "",
							"type" => "textarea"
						),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
				// Price
				"trx_price" => array(
					"title" => __("Price", 'additional-tags'),
					"desc" => __("Insert price with decoration", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"money" => array(
							"title" => __("Money", 'additional-tags'),
							"desc" => __("Money value (dot or comma separated)", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"currency" => array(
							"title" => __("Currency", 'additional-tags'),
							"desc" => __("Currency character", 'additional-tags'),
							"value" => "$",
							"type" => "text"
						),
						"period" => array(
							"title" => __("Period", 'additional-tags'),
							"desc" => __("Period text (if need). For example: monthly, daily, etc.", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"align" => array(
							"title" => __("Alignment", 'additional-tags'),
							"desc" => __("Align price to left or right side", 'additional-tags'),
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['float']
						), 
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
				// Price block
				"trx_price_block" => array(
					"title" => __("Price block", 'additional-tags'),
					"desc" => __("Insert price block with title, price and description", 'additional-tags'),
					"decorate" => false,
					"container" => true,
					"params" => array(
						"title" => array(
							"title" => __("Title", 'additional-tags'),
							"desc" => __("Block title", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"link" => array(
							"title" => __("Link URL", 'additional-tags'),
							"desc" => __("URL for link from button (at bottom of the block)", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"link_text" => array(
							"title" => __("Link text", 'additional-tags'),
							"desc" => __("Text (caption) for the link button (at bottom of the block). If empty - button not showed", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"target" => array(
							"title" => __("Link target", 'additional-tags'),
							"desc" => __("Target for link on button click", 'additional-tags'),
							"dependency" => array(
								'link' => array('not_empty')
							),
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"icon" => array(
							"title" => __("Icon", 'additional-tags'),
							"desc" => __('Select icon from Fontello icons set (placed before/instead price)', 'additional-tags'),
							"value" => "",
							"type" => "icons",
							"options" => $THEMEREX_GLOBALS['sc_params']['icons']
						),
						"money" => array(
							"title" => __("Money", 'additional-tags'),
							"desc" => __("Money value (dot or comma separated)", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "text"
						),
						"currency" => array(
							"title" => __("Currency", 'additional-tags'),
							"desc" => __("Currency character", 'additional-tags'),
							"value" => "$",
							"type" => "text"
						),
						"period" => array(
							"title" => __("Period", 'additional-tags'),
							"desc" => __("Period text (if need). For example: monthly, daily, etc.", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"align" => array(
							"title" => __("Alignment", 'additional-tags'),
							"desc" => __("Align price to left or right side", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['float']
						), 
						"_content_" => array(
							"title" => __("Description", 'additional-tags'),
							"desc" => __("Description for this price block", 'additional-tags'),
							"divider" => true,
							"rows" => 4,
							"value" => "",
							"type" => "textarea"
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
				// Quote
				"trx_quote" => array(
					"title" => __("Quote", 'additional-tags'),
					"desc" => __("Quote text", 'additional-tags'),
					"decorate" => false,
					"container" => true,
					"params" => array(
						"cite" => array(
							"title" => __("Quote cite", 'additional-tags'),
							"desc" => __("URL for quote cite", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"title" => array(
							"title" => __("Title (author)", 'additional-tags'),
							"desc" => __("Quote title (author name)", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"_content_" => array(
							"title" => __("Quote content", 'additional-tags'),
							"desc" => __("Quote content", 'additional-tags'),
							"rows" => 4,
							"value" => "",
							"type" => "textarea"
						),
						"width" => themerex_shortcodes_width(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
				// Reviews
				"trx_reviews" => array(
					"title" => __("Reviews", 'additional-tags'),
					"desc" => __("Insert reviews block in the single post", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"align" => array(
							"title" => __("Alignment", 'additional-tags'),
							"desc" => __("Align counter to left, center or right", 'additional-tags'),
							"divider" => true,
							"value" => "none",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						), 
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
				// Search
				"trx_search" => array(
					"title" => __("Search", 'additional-tags'),
					"desc" => __("Show search form", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"ajax" => array(
							"title" => __("Style", 'additional-tags'),
							"desc" => __("Select style to display search field", 'additional-tags'),
							"value" => "regular",
							"options" => array(
								"regular" => __('Regular', 'additional-tags'),
								"flat" => __('Flat', 'additional-tags')
							),
							"type" => "checklist"
						),
						"title" => array(
							"title" => __("Title", 'additional-tags'),
							"desc" => __("Title (placeholder) for the search field", 'additional-tags'),
							"value" => __("Search &hellip;", 'additional-tags'),
							"type" => "text"
						),
						"ajax" => array(
							"title" => __("AJAX", 'additional-tags'),
							"desc" => __("Search via AJAX or reload page", 'additional-tags'),
							"value" => "yes",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no'],
							"type" => "switch"
						),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),


                // Custom Search
				"trx_custom_search" => array(
					"title" => __("Custom Search", 'additional-tags'),
					"desc" => __("Show custom search form", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"ajax" => array(
							"title" => __("Style", 'additional-tags'),
							"desc" => __("Select style to display search field", 'additional-tags'),
							"value" => "regular",
							"options" => array(
								"regular" => __('Regular', 'additional-tags'),
								"flat" => __('Flat', 'additional-tags')
							),
							"type" => "checklist"
						),
						"title" => array(
							"title" => __("Title", 'additional-tags'),
							"desc" => __("Title (placeholder) for the search field", 'additional-tags'),
							"value" => __("Search", 'additional-tags'),
							"type" => "text"
						),
//						"ajax" => array(
//							"title" => __("AJAX", 'additional-tags'),
//							"desc" => __("Search via AJAX or reload page", 'additional-tags'),
//							"value" => "yes",
//							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no'],
//							"type" => "switch"
//						),
                        "post_type" => array(
							"title" => __("Post type", 'additional-tags'),
							"desc" => __("Search by post type", 'additional-tags'),
							"value" => 'courses',
							"options" =>  $list_post_types,
                            "type" => "select"
						),
                        "use_tags" => array(
							"title" => __("Use tags", 'additional-tags'),
							"desc" => __("Search by tags", 'additional-tags'),
							"value" => 'yes',
                            "options" => $THEMEREX_GLOBALS['sc_params']['yes_no'],
                            "type" => "switch"
						),
                        "tags_title" => array(
							"title" => __("Tags title", 'additional-tags'),
							"value" => __('Tags', 'additional-tags'),
                            "type" => "text"
						),
                        "use_categories" => array(
							"title" => __("Use categories", 'additional-tags'),
							"desc" => __("Search by categories", 'additional-tags'),
							"value" => 'yes',
                            "options" => $THEMEREX_GLOBALS['sc_params']['yes_no'],
                            "type" => "switch"
						),
                        "categories_title" => array(
                            "title" => __("Categories title", 'additional-tags'),
                            "value" => __('Categories', 'additional-tags'),
                            "type" => "text"
                        ),
                        "hide_empty_tax" => array(
                            "title" => __("Hide empty term", 'additional-tags'),
                            "desc" => __("Hide taxonomy without posts.", 'additional-tags'),
                            "value" => 'no',
                            "options" => $THEMEREX_GLOBALS['sc_params']['yes_no'],
                            "type" => "switch"
                        ),
                        "button" => array(
                            "title" => __("Button text", 'additional-tags'),
                            "value" => __('Search', 'additional-tags'),
                            "type" => "text"
                        ),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
				// Section
				"trx_section" => array(
					"title" => __("Section container", 'additional-tags'),
					"desc" => __("Container for any block with desired class and style", 'additional-tags'),
					"decorate" => true,
					"container" => true,
					"params" => array(
						"dedicated" => array(
							"title" => __("Dedicated", 'additional-tags'),
							"desc" => __("Use this block as dedicated content - show it before post title on single page", 'additional-tags'),
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"align" => array(
							"title" => __("Align", 'additional-tags'),
							"desc" => __("Select block alignment", 'additional-tags'),
							"value" => "none",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						),
						"columns" => array(
							"title" => __("Columns emulation", 'additional-tags'),
							"desc" => __("Select width for columns emulation", 'additional-tags'),
							"value" => "none",
							"type" => "checklist",
							"options" => $THEMEREX_GLOBALS['sc_params']['columns']
						), 
						"pan" => array(
							"title" => __("Use pan effect", 'additional-tags'),
							"desc" => __("Use pan effect to show section content", 'additional-tags'),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"scroll" => array(
							"title" => __("Use scroller", 'additional-tags'),
							"desc" => __("Use scroller to show section content", 'additional-tags'),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"scroll_dir" => array(
							"title" => __("Scroll and Pan direction", 'additional-tags'),
							"desc" => __("Scroll and Pan direction (if Use scroller = yes or Pan = yes)", 'additional-tags'),
							"dependency" => array(
								'pan' => array('yes'),
								'scroll' => array('yes')
							),
							"value" => "horizontal",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['dir']
						),
						"scroll_controls" => array(
							"title" => __("Scroll controls", 'additional-tags'),
							"desc" => __("Show scroll controls (if Use scroller = yes)", 'additional-tags'),
							"dependency" => array(
								'scroll' => array('yes')
							),
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"color" => array(
							"title" => __("Fore color", 'additional-tags'),
							"desc" => __("Any color for objects in this section", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "color"
						),
						"bg_tint" => array(
							"title" => __("Background tint", 'additional-tags'),
							"desc" => __("Main background tint: dark or light", 'additional-tags'),
							"value" => "",
							"type" => "checklist",
							"options" => $THEMEREX_GLOBALS['sc_params']['tint']
						),
						"bg_color" => array(
							"title" => __("Background color", 'additional-tags'),
							"desc" => __("Any background color for this section", 'additional-tags'),
							"value" => "",
							"type" => "color"
						),
						"bg_image" => array(
							"title" => __("Background image URL", 'additional-tags'),
							"desc" => __("Select or upload image or write URL from other site for the background", 'additional-tags'),
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"bg_overlay" => array(
							"title" => __("Overlay", 'additional-tags'),
							"desc" => __("Overlay color opacity (from 0.0 to 1.0)", 'additional-tags'),
							"min" => "0",
							"max" => "1",
							"step" => "0.1",
							"value" => "0",
							"type" => "spinner"
						),
						"bg_texture" => array(
							"title" => __("Texture", 'additional-tags'),
							"desc" => __("Predefined texture style from 1 to 11. 0 - without texture.", 'additional-tags'),
							"min" => "0",
							"max" => "11",
							"step" => "1",
							"value" => "0",
							"type" => "spinner"
						),
						"font_size" => array(
							"title" => __("Font size", 'additional-tags'),
							"desc" => __("Font size of the text (default - in pixels, allows any CSS units of measure)", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"font_weight" => array(
							"title" => __("Font weight", 'additional-tags'),
							"desc" => __("Font weight of the text", 'additional-tags'),
							"value" => "",
							"type" => "select",
							"size" => "medium",
							"options" => array(
								'100' => __('Thin (100)', 'additional-tags'),
								'300' => __('Light (300)', 'additional-tags'),
								'400' => __('Normal (400)', 'additional-tags'),
								'700' => __('Bold (700)', 'additional-tags')
							)
						),
						"_content_" => array(
							"title" => __("Container content", 'additional-tags'),
							"desc" => __("Content for section container", 'additional-tags'),
							"divider" => true,
							"rows" => 4,
							"value" => "",
							"type" => "textarea"
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
				// Skills
				"trx_skills" => array(
					"title" => __("Skills", 'additional-tags'),
					"desc" => __("Insert skills diagramm in your page (post)", 'additional-tags'),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"max_value" => array(
							"title" => __("Max value", 'additional-tags'),
							"desc" => __("Max value for skills items", 'additional-tags'),
							"value" => 100,
							"min" => 1,
							"type" => "spinner"
						),
						"type" => array(
							"title" => __("Skills type", 'additional-tags'),
							"desc" => __("Select type of skills block", 'additional-tags'),
							"value" => "bar",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => array(
								'bar' => __('Bar', 'additional-tags'),
								'pie' => __('Pie chart', 'additional-tags'),
								'counter' => __('Counter', 'additional-tags'),
								'arc' => __('Arc', 'additional-tags')
							)
						), 
						"layout" => array(
							"title" => __("Skills layout", 'additional-tags'),
							"desc" => __("Select layout of skills block", 'additional-tags'),
							"dependency" => array(
								'type' => array('counter','pie','bar')
							),
							"value" => "rows",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => array(
								'rows' => __('Rows', 'additional-tags'),
								'columns' => __('Columns', 'additional-tags')
							)
						),
						"dir" => array(
							"title" => __("Direction", 'additional-tags'),
							"desc" => __("Select direction of skills block", 'additional-tags'),
							"dependency" => array(
								'type' => array('counter','pie','bar')
							),
							"value" => "horizontal",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['dir']
						), 
						"style" => array(
							"title" => __("Counters style", 'additional-tags'),
							"desc" => __("Select style of skills items (only for type=counter)", 'additional-tags'),
							"dependency" => array(
								'type' => array('counter')
							),
							"value" => 1,
							"min" => 1,
							"max" => 4,
							"type" => "spinner"
						), 
						// "columns" - autodetect, not set manual
						"color" => array(
							"title" => __("Skills items color", 'additional-tags'),
							"desc" => __("Color for all skills items", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "color"
						),
						"bg_color" => array(
							"title" => __("Background color", 'additional-tags'),
							"desc" => __("Background color for all skills items (only for type=pie)", 'additional-tags'),
							"dependency" => array(
								'type' => array('pie')
							),
							"value" => "",
							"type" => "color"
						),
						"border_color" => array(
							"title" => __("Border color", 'additional-tags'),
							"desc" => __("Border color for all skills items (only for type=pie)", 'additional-tags'),
							"dependency" => array(
								'type' => array('pie')
							),
							"value" => "",
							"type" => "color"
						),
						"title" => array(
							"title" => __("Skills title", 'additional-tags'),
							"desc" => __("Skills block title", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "text"
						),
						"subtitle" => array(
							"title" => __("Skills subtitle", 'additional-tags'),
							"desc" => __("Skills block subtitle - text in the center (only for type=arc)", 'additional-tags'),
							"dependency" => array(
								'type' => array('arc')
							),
							"value" => "",
							"type" => "text"
						),
						"align" => array(
							"title" => __("Align skills block", 'additional-tags'),
							"desc" => __("Align skills block to left or right side", 'additional-tags'),
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
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					),
					"children" => array(
						"name" => "trx_skills_item",
						"title" => __("Skill", 'additional-tags'),
						"desc" => __("Skills item", 'additional-tags'),
						"container" => false,
						"params" => array(
							"title" => array(
								"title" => __("Title", 'additional-tags'),
								"desc" => __("Current skills item title", 'additional-tags'),
								"value" => "",
								"type" => "text"
							),
							"value" => array(
								"title" => __("Value", 'additional-tags'),
								"desc" => __("Current skills level", 'additional-tags'),
								"value" => 50,
								"min" => 0,
								"step" => 1,
								"type" => "spinner"
							),
							"color" => array(
								"title" => __("Color", 'additional-tags'),
								"desc" => __("Current skills item color", 'additional-tags'),
								"value" => "",
								"type" => "color"
							),
							"bg_color" => array(
								"title" => __("Background color", 'additional-tags'),
								"desc" => __("Current skills item background color (only for type=pie)", 'additional-tags'),
								"value" => "",
								"type" => "color"
							),
							"border_color" => array(
								"title" => __("Border color", 'additional-tags'),
								"desc" => __("Current skills item border color (only for type=pie)", 'additional-tags'),
								"value" => "",
								"type" => "color"
							),
							"style" => array(
								"title" => __("Counter tyle", 'additional-tags'),
								"desc" => __("Select style for the current skills item (only for type=counter)", 'additional-tags'),
								"value" => 1,
								"min" => 1,
								"max" => 4,
								"type" => "spinner"
							), 
							"id" => $THEMEREX_GLOBALS['sc_params']['id'],
							"class" => $THEMEREX_GLOBALS['sc_params']['class'],
							"css" => $THEMEREX_GLOBALS['sc_params']['css']
						)
					)
				),
			
			
			
			
				// Slider
				"trx_slider" => array(
					"title" => __("Slider", 'additional-tags'),
					"desc" => __("Insert slider into your post (page)", 'additional-tags'),
					"decorate" => true,
					"container" => false,
					"params" => array_merge(array(
						"engine" => array(
							"title" => __("Slider engine", 'additional-tags'),
							"desc" => __("Select engine for slider. Attention! Swiper is built-in engine, all other engines appears only if corresponding plugings are installed", 'additional-tags'),
							"value" => "swiper",
							"type" => "checklist",
							"options" => $THEMEREX_GLOBALS['sc_params']['sliders']
						),
						"align" => array(
							"title" => __("Float slider", 'additional-tags'),
							"desc" => __("Float slider to left or right side", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['float']
						),
						"custom" => array(
							"title" => __("Custom slides", 'additional-tags'),
							"desc" => __("Make custom slides from inner shortcodes (prepare it on tabs) or prepare slides from posts thumbnails", 'additional-tags'),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						)
						),
						themerex_exists_revslider() || themerex_exists_royalslider() ? array(
						"alias" => array(
							"title" => __("Revolution slider alias or Royal Slider ID", 'additional-tags'),
							"desc" => __("Alias for Revolution slider or Royal slider ID", 'additional-tags'),
							"dependency" => array(
								'engine' => array('revo','royal')
							),
							"divider" => true,
							"value" => "",
							"type" => "text"
						)) : array(), array(
						"cat" => array(
							"title" => __("Swiper: Category list", 'additional-tags'),
							"desc" => __("Comma separated list of category slugs. If empty - select posts from any category or from IDs list", 'additional-tags'),
							"dependency" => array(
								'engine' => array('swiper')
							),
							"divider" => true,
							"value" => "",
							"type" => "select",
							"style" => "list",
							"multiple" => true,
							"options" => $THEMEREX_GLOBALS['sc_params']['categories']
						),
						"count" => array(
							"title" => __("Swiper: Number of posts", 'additional-tags'),
							"desc" => __("How many posts will be displayed? If used IDs - this parameter ignored.", 'additional-tags'),
							"dependency" => array(
								'engine' => array('swiper')
							),
							"value" => 3,
							"min" => 1,
							"max" => 100,
							"type" => "spinner"
						),
						"offset" => array(
							"title" => __("Swiper: Offset before select posts", 'additional-tags'),
							"desc" => __("Skip posts before select next part.", 'additional-tags'),
							"dependency" => array(
								'engine' => array('swiper')
							),
							"value" => 0,
							"min" => 0,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => __("Swiper: Post order by", 'additional-tags'),
							"desc" => __("Select desired posts sorting method", 'additional-tags'),
							"dependency" => array(
								'engine' => array('swiper')
							),
							"value" => "date",
							"type" => "select",
							"options" => $THEMEREX_GLOBALS['sc_params']['sorting']
						),
						"order" => array(
							"title" => __("Swiper: Post order", 'additional-tags'),
							"desc" => __("Select desired posts order", 'additional-tags'),
							"dependency" => array(
								'engine' => array('swiper')
							),
							"value" => "desc",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
						),
						"ids" => array(
							"title" => __("Swiper: Post IDs list", 'additional-tags'),
							"desc" => __("Comma separated list of posts ID. If set - parameters above are ignored!", 'additional-tags'),
							"dependency" => array(
								'engine' => array('swiper')
							),
							"value" => "",
							"type" => "text"
						),
						"controls" => array(
							"title" => __("Swiper: Show slider controls", 'additional-tags'),
							"desc" => __("Show arrows inside slider", 'additional-tags'),
							"dependency" => array(
								'engine' => array('swiper')
							),
							"divider" => true,
							"value" => "yes",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"pagination" => array(
							"title" => __("Swiper: Show slider pagination", 'additional-tags'),
							"desc" => __("Show bullets for switch slides", 'additional-tags'),
							"dependency" => array(
								'engine' => array('swiper')
							),
							"value" => "yes",
							"type" => "checklist",
							"options" => array(
								'yes'  => __('Dots', 'additional-tags'),
								'full' => __('Side Titles', 'additional-tags'),
								'over' => __('Over Titles', 'additional-tags'),
								'no'   => __('None', 'additional-tags')
							)
						),
						"titles" => array(
							"title" => __("Swiper: Show titles section", 'additional-tags'),
							"desc" => __("Show section with post's title and short post's description", 'additional-tags'),
							"dependency" => array(
								'engine' => array('swiper')
							),
							"divider" => true,
							"value" => "no",
							"type" => "checklist",
							"options" => array(
								"no"    => __('Not show', 'additional-tags'),
								"slide" => __('Show/Hide info', 'additional-tags'),
								"fixed" => __('Fixed info', 'additional-tags')
							)
						),
						"descriptions" => array(
							"title" => __("Swiper: Post descriptions", 'additional-tags'),
							"dependency" => array(
								'engine' => array('swiper')
							),
							"desc" => __("Show post's excerpt max length (characters)", 'additional-tags'),
							"value" => 0,
							"min" => 0,
							"max" => 1000,
							"step" => 10,
							"type" => "spinner"
						),
						"links" => array(
							"title" => __("Swiper: Post's title as link", 'additional-tags'),
							"desc" => __("Make links from post's titles", 'additional-tags'),
							"dependency" => array(
								'engine' => array('swiper')
							),
							"value" => "yes",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"crop" => array(
							"title" => __("Swiper: Crop images", 'additional-tags'),
							"desc" => __("Crop images in each slide or live it unchanged", 'additional-tags'),
							"dependency" => array(
								'engine' => array('swiper')
							),
							"value" => "yes",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"autoheight" => array(
							"title" => __("Swiper: Autoheight", 'additional-tags'),
							"desc" => __("Change whole slider's height (make it equal current slide's height)", 'additional-tags'),
							"dependency" => array(
								'engine' => array('swiper')
							),
							"value" => "yes",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"interval" => array(
							"title" => __("Swiper: Slides change interval", 'additional-tags'),
							"desc" => __("Slides change interval (in milliseconds: 1000ms = 1s)", 'additional-tags'),
							"dependency" => array(
								'engine' => array('swiper')
							),
							"value" => 5000,
							"step" => 500,
							"min" => 0,
							"type" => "spinner"
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)),
					"children" => array(
						"name" => "trx_slider_item",
						"title" => __("Slide", 'additional-tags'),
						"desc" => __("Slider item", 'additional-tags'),
						"container" => false,
						"params" => array(
							"src" => array(
								"title" => __("URL (source) for image file", 'additional-tags'),
								"desc" => __("Select or upload image or write URL from other site for the current slide", 'additional-tags'),
								"readonly" => false,
								"value" => "",
								"type" => "media"
							),
							"id" => $THEMEREX_GLOBALS['sc_params']['id'],
							"class" => $THEMEREX_GLOBALS['sc_params']['class'],
							"css" => $THEMEREX_GLOBALS['sc_params']['css']
						)
					)
				),
			
			
			
			
				// Socials
				"trx_socials" => array(
					"title" => __("Social icons", 'additional-tags'),
					"desc" => __("List of social icons (with hovers)", 'additional-tags'),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"size" => array(
							"title" => __("Icon's size", 'additional-tags'),
							"desc" => __("Size of the icons", 'additional-tags'),
							"value" => "small",
							"type" => "checklist",
							"options" => array(
								"tiny" => __('Tiny', 'additional-tags'),
								"small" => __('Small', 'additional-tags'),
								"large" => __('Large', 'additional-tags')
							)
						), 
						"socials" => array(
							"title" => __("Manual socials list", 'additional-tags'),
							"desc" => __("Custom list of social networks. For example: twitter=http://twitter.com/my_profile|facebook=http://facebooc.com/my_profile. If empty - use socials from Theme options.", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "text"
						),
						"custom" => array(
							"title" => __("Custom socials", 'additional-tags'),
							"desc" => __("Make custom icons from inner shortcodes (prepare it on tabs)", 'additional-tags'),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					),
					"children" => array(
						"name" => "trx_social_item",
						"title" => __("Custom social item", 'additional-tags'),
						"desc" => __("Custom social item: name, profile url and icon url", 'additional-tags'),
						"decorate" => false,
						"container" => false,
						"params" => array(
							"name" => array(
								"title" => __("Social name", 'additional-tags'),
								"desc" => __("Name (slug) of the social network (twitter, facebook, linkedin, etc.)", 'additional-tags'),
								"value" => "",
								"type" => "text"
							),
							"url" => array(
								"title" => __("Your profile URL", 'additional-tags'),
								"desc" => __("URL of your profile in specified social network", 'additional-tags'),
								"value" => "",
								"type" => "text"
							),
							"icon" => array(
								"title" => __("URL (source) for icon file", 'additional-tags'),
								"desc" => __("Select or upload image or write URL from other site for the current social icon", 'additional-tags'),
								"readonly" => false,
								"value" => "",
								"type" => "media"
							)
						)
					)
				),
			
			
			
			
				// Table
				"trx_table" => array(
					"title" => __("Table", 'additional-tags'),
					"desc" => __("Insert a table into post (page). ", 'additional-tags'),
					"decorate" => true,
					"container" => true,
					"params" => array(
						"align" => array(
							"title" => __("Content alignment", 'additional-tags'),
							"desc" => __("Select alignment for each table cell", 'additional-tags'),
							"value" => "none",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						),
						"_content_" => array(
							"title" => __("Table content", 'additional-tags'),
							"desc" => __("Content, created with any table-generator", 'additional-tags'),
							"divider" => true,
							"rows" => 8,
							"value" => "Paste here table content, generated on one of many public internet resources, for example: http://www.impressivewebs.com/html-table-code-generator/ or http://html-tables.com/",
							"type" => "textarea"
						),
						"width" => themerex_shortcodes_width(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
			
				// Tabs
				"trx_tabs" => array(
					"title" => __("Tabs", 'additional-tags'),
					"desc" => __("Insert tabs in your page (post)", 'additional-tags'),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"style" => array(
							"title" => __("Tabs style", 'additional-tags'),
							"desc" => __("Select style for tabs items", 'additional-tags'),
							"value" => 1,
							"options" => array(
								1 => __('Style 1', 'additional-tags'),
								2 => __('Style 2', 'additional-tags')
							),
							"type" => "radio"
						),
						"initial" => array(
							"title" => __("Initially opened tab", 'additional-tags'),
							"desc" => __("Number of initially opened tab", 'additional-tags'),
							"divider" => true,
							"value" => 1,
							"min" => 0,
							"type" => "spinner"
						),
						"scroll" => array(
							"title" => __("Use scroller", 'additional-tags'),
							"desc" => __("Use scroller to show tab content (height parameter required)", 'additional-tags'),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					),
					"children" => array(
						"name" => "trx_tab",
						"title" => __("Tab", 'additional-tags'),
						"desc" => __("Tab item", 'additional-tags'),
						"container" => true,
						"params" => array(
							"title" => array(
								"title" => __("Tab title", 'additional-tags'),
								"desc" => __("Current tab title", 'additional-tags'),
								"value" => "",
								"type" => "text"
							),
							"_content_" => array(
								"title" => __("Tab content", 'additional-tags'),
								"desc" => __("Current tab content", 'additional-tags'),
								"divider" => true,
								"rows" => 4,
								"value" => "",
								"type" => "textarea"
							),
							"id" => $THEMEREX_GLOBALS['sc_params']['id'],
							"class" => $THEMEREX_GLOBALS['sc_params']['class'],
							"css" => $THEMEREX_GLOBALS['sc_params']['css']
						)
					)
				),
			
			
			
			
			
				// Team
				"trx_team" => array(
					"title" => __("Team", 'additional-tags'),
					"desc" => __("Insert team in your page (post)", 'additional-tags'),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"style" => array(
							"title" => __("Team style", 'additional-tags'),
							"desc" => __("Select style to display team members", 'additional-tags'),
							"value" => "1",
							"type" => "select",
							"options" => array(
								1 => __('Style 1', 'additional-tags'),
								2 => __('Style 2', 'additional-tags')
							)
						),
						"columns" => array(
							"title" => __("Columns", 'additional-tags'),
							"desc" => __("How many columns use to show team members", 'additional-tags'),
							"value" => 3,
							"min" => 2,
							"max" => 5,
							"step" => 1,
							"type" => "spinner"
						),
						"custom" => array(
							"title" => __("Custom", 'additional-tags'),
							"desc" => __("Allow get team members from inner shortcodes (custom) or get it from specified group (cat)", 'additional-tags'),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"cat" => array(
							"title" => __("Categories", 'additional-tags'),
							"desc" => __("Select categories (groups) to show team members. If empty - select team members from any category (group) or from IDs list", 'additional-tags'),
							"dependency" => array(
								'custom' => array('no')
							),
							"divider" => true,
							"value" => "",
							"type" => "select",
							"style" => "list",
							"multiple" => true,
							"options" => $THEMEREX_GLOBALS['sc_params']['team_groups']
						),
						"count" => array(
							"title" => __("Number of posts", 'additional-tags'),
							"desc" => __("How many posts will be displayed? If used IDs - this parameter ignored.", 'additional-tags'),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => 3,
							"min" => 1,
							"max" => 100,
							"type" => "spinner"
						),
						"offset" => array(
							"title" => __("Offset before select posts", 'additional-tags'),
							"desc" => __("Skip posts before select next part.", 'additional-tags'),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => 0,
							"min" => 0,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => __("Post order by", 'additional-tags'),
							"desc" => __("Select desired posts sorting method", 'additional-tags'),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "title",
							"type" => "select",
							"options" => $THEMEREX_GLOBALS['sc_params']['sorting']
						),
						"order" => array(
							"title" => __("Post order", 'additional-tags'),
							"desc" => __("Select desired posts order", 'additional-tags'),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "asc",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
						),
						"ids" => array(
							"title" => __("Post IDs list", 'additional-tags'),
							"desc" => __("Comma separated list of posts ID. If set - parameters above are ignored!", 'additional-tags'),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "",
							"type" => "text"
						),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					),
					"children" => array(
						"name" => "trx_team_item",
						"title" => __("Member", 'additional-tags'),
						"desc" => __("Team member", 'additional-tags'),
						"container" => true,
						"params" => array(
							"user" => array(
								"title" => __("Registerd user", 'additional-tags'),
								"desc" => __("Select one of registered users (if present) or put name, position, etc. in fields below", 'additional-tags'),
								"value" => "",
								"type" => "select",
								"options" => $THEMEREX_GLOBALS['sc_params']['users']
							),
							"member" => array(
								"title" => __("Team member", 'additional-tags'),
								"desc" => __("Select one of team members (if present) or put name, position, etc. in fields below", 'additional-tags'),
								"value" => "",
								"type" => "select",
								"options" => $THEMEREX_GLOBALS['sc_params']['members']
							),
							"link" => array(
								"title" => __("Link", 'additional-tags'),
								"desc" => __("Link on team member's personal page", 'additional-tags'),
								"divider" => true,
								"value" => "",
								"type" => "text"
							),
							"name" => array(
								"title" => __("Name", 'additional-tags'),
								"desc" => __("Team member's name", 'additional-tags'),
								"divider" => true,
								"dependency" => array(
									'user' => array('is_empty', 'none'),
									'member' => array('is_empty', 'none')
								),
								"value" => "",
								"type" => "text"
							),
							"position" => array(
								"title" => __("Position", 'additional-tags'),
								"desc" => __("Team member's position", 'additional-tags'),
								"dependency" => array(
									'user' => array('is_empty', 'none'),
									'member' => array('is_empty', 'none')
								),
								"value" => "",
								"type" => "text"
							),
							"email" => array(
								"title" => __("E-mail", 'additional-tags'),
								"desc" => __("Team member's e-mail", 'additional-tags'),
								"dependency" => array(
									'user' => array('is_empty', 'none'),
									'member' => array('is_empty', 'none')
								),
								"value" => "",
								"type" => "text"
							),
							"photo" => array(
								"title" => __("Photo", 'additional-tags'),
								"desc" => __("Team member's photo (avatar)", 'additional-tags'),
								"dependency" => array(
									'user' => array('is_empty', 'none'),
									'member' => array('is_empty', 'none')
								),
								"value" => "",
								"readonly" => false,
								"type" => "media"
							),
							"socials" => array(
								"title" => __("Socials", 'additional-tags'),
								"desc" => __("Team member's socials icons: name=url|name=url... For example: facebook=http://facebook.com/myaccount|twitter=http://twitter.com/myaccount", 'additional-tags'),
								"dependency" => array(
									'user' => array('is_empty', 'none'),
									'member' => array('is_empty', 'none')
								),
								"value" => "",
								"type" => "text"
							),
							"_content_" => array(
								"title" => __("Description", 'additional-tags'),
								"desc" => __("Team member's short description", 'additional-tags'),
								"divider" => true,
								"rows" => 4,
								"value" => "",
								"type" => "textarea"
							),
							"id" => $THEMEREX_GLOBALS['sc_params']['id'],
							"class" => $THEMEREX_GLOBALS['sc_params']['class'],
							"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
							"css" => $THEMEREX_GLOBALS['sc_params']['css']
						)
					)
				),
			
			
			
			
				// Testimonials
				"trx_testimonials" => array(
					"title" => __("Testimonials", 'additional-tags'),
					"desc" => __("Insert testimonials into post (page)", 'additional-tags'),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"controls" => array(
							"title" => __("Show arrows", 'additional-tags'),
							"desc" => __("Show control buttons", 'additional-tags'),
							"value" => "yes",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"interval" => array(
							"title" => __("Testimonials change interval", 'additional-tags'),
							"desc" => __("Testimonials change interval (in milliseconds: 1000ms = 1s)", 'additional-tags'),
							"value" => 7000,
							"step" => 500,
							"min" => 0,
							"type" => "spinner"
						),
						"align" => array(
							"title" => __("Alignment", 'additional-tags'),
							"desc" => __("Alignment of the testimonials block", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						),
						"autoheight" => array(
							"title" => __("Autoheight", 'additional-tags'),
							"desc" => __("Change whole slider's height (make it equal current slide's height)", 'additional-tags'),
							"value" => "yes",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"custom" => array(
							"title" => __("Custom", 'additional-tags'),
							"desc" => __("Allow get testimonials from inner shortcodes (custom) or get it from specified group (cat)", 'additional-tags'),
							"divider" => true,
							"value" => "no",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"cat" => array(
							"title" => __("Categories", 'additional-tags'),
							"desc" => __("Select categories (groups) to show testimonials. If empty - select testimonials from any category (group) or from IDs list", 'additional-tags'),
							"dependency" => array(
								'custom' => array('no')
							),
							"divider" => true,
							"value" => "",
							"type" => "select",
							"style" => "list",
							"multiple" => true,
							"options" => $THEMEREX_GLOBALS['sc_params']['testimonials_groups']
						),
						"count" => array(
							"title" => __("Number of posts", 'additional-tags'),
							"desc" => __("How many posts will be displayed? If used IDs - this parameter ignored.", 'additional-tags'),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => 3,
							"min" => 1,
							"max" => 100,
							"type" => "spinner"
						),
						"offset" => array(
							"title" => __("Offset before select posts", 'additional-tags'),
							"desc" => __("Skip posts before select next part.", 'additional-tags'),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => 0,
							"min" => 0,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => __("Post order by", 'additional-tags'),
							"desc" => __("Select desired posts sorting method", 'additional-tags'),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "date",
							"type" => "select",
							"options" => $THEMEREX_GLOBALS['sc_params']['sorting']
						),
						"order" => array(
							"title" => __("Post order", 'additional-tags'),
							"desc" => __("Select desired posts order", 'additional-tags'),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "desc",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
						),
						"ids" => array(
							"title" => __("Post IDs list", 'additional-tags'),
							"desc" => __("Comma separated list of posts ID. If set - parameters above are ignored!", 'additional-tags'),
							"dependency" => array(
								'custom' => array('no')
							),
							"value" => "",
							"type" => "text"
						),
						"bg_tint" => array(
							"title" => __("Background tint", 'additional-tags'),
							"desc" => __("Main background tint: dark or light", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"options" => $THEMEREX_GLOBALS['sc_params']['tint']
						),
						"bg_color" => array(
							"title" => __("Background color", 'additional-tags'),
							"desc" => __("Any background color for this section", 'additional-tags'),
							"value" => "",
							"type" => "color"
						),
						"bg_image" => array(
							"title" => __("Background image URL", 'additional-tags'),
							"desc" => __("Select or upload image or write URL from other site for the background", 'additional-tags'),
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"bg_overlay" => array(
							"title" => __("Overlay", 'additional-tags'),
							"desc" => __("Overlay color opacity (from 0.0 to 1.0)", 'additional-tags'),
							"min" => "0",
							"max" => "1",
							"step" => "0.1",
							"value" => "0",
							"type" => "spinner"
						),
						"bg_texture" => array(
							"title" => __("Texture", 'additional-tags'),
							"desc" => __("Predefined texture style from 1 to 11. 0 - without texture.", 'additional-tags'),
							"min" => "0",
							"max" => "11",
							"step" => "1",
							"value" => "0",
							"type" => "spinner"
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					),
					"children" => array(
						"name" => "trx_testimonials_item",
						"title" => __("Item", 'additional-tags'),
						"desc" => __("Testimonials item", 'additional-tags'),
						"container" => true,
						"params" => array(
							"author" => array(
								"title" => __("Author", 'additional-tags'),
								"desc" => __("Name of the testimonmials author", 'additional-tags'),
								"value" => "",
								"type" => "text"
							),
							"link" => array(
								"title" => __("Link", 'additional-tags'),
								"desc" => __("Link URL to the testimonmials author page", 'additional-tags'),
								"value" => "",
								"type" => "text"
							),
							"email" => array(
								"title" => __("E-mail", 'additional-tags'),
								"desc" => __("E-mail of the testimonmials author (to get gravatar)", 'additional-tags'),
								"value" => "",
								"type" => "text"
							),
							"photo" => array(
								"title" => __("Photo", 'additional-tags'),
								"desc" => __("Select or upload photo of testimonmials author or write URL of photo from other site", 'additional-tags'),
								"value" => "",
								"type" => "media"
							),
							"_content_" => array(
								"title" => __("Testimonials text", 'additional-tags'),
								"desc" => __("Current testimonials text", 'additional-tags'),
								"divider" => true,
								"rows" => 4,
								"value" => "",
								"type" => "textarea"
							),
							"id" => $THEMEREX_GLOBALS['sc_params']['id'],
							"class" => $THEMEREX_GLOBALS['sc_params']['class'],
							"css" => $THEMEREX_GLOBALS['sc_params']['css']
						)
					)
				),
			
			
			
			
				// Title
				"trx_title" => array(
					"title" => __("Title", 'additional-tags'),
					"desc" => __("Create header tag (1-6 level) with many styles", 'additional-tags'),
					"decorate" => false,
					"container" => true,
					"params" => array(
						"_content_" => array(
							"title" => __("Title content", 'additional-tags'),
							"desc" => __("Title content", 'additional-tags'),
							"rows" => 4,
							"value" => "",
							"type" => "textarea"
						),
						"type" => array(
							"title" => __("Title type", 'additional-tags'),
							"desc" => __("Title type (header level)", 'additional-tags'),
							"divider" => true,
							"value" => "1",
							"type" => "select",
							"options" => array(
								'1' => __('Header 1', 'additional-tags'),
								'2' => __('Header 2', 'additional-tags'),
								'3' => __('Header 3', 'additional-tags'),
								'4' => __('Header 4', 'additional-tags'),
								'5' => __('Header 5', 'additional-tags'),
								'6' => __('Header 6', 'additional-tags'),
							)
						),
						"style" => array(
							"title" => __("Title style", 'additional-tags'),
							"desc" => __("Title style", 'additional-tags'),
							"value" => "regular",
							"type" => "select",
							"options" => array(
								'regular' => __('Regular', 'additional-tags'),
								'underline' => __('Underline', 'additional-tags'),
								'divider' => __('Divider', 'additional-tags'),
								'iconed' => __('With icon (image)', 'additional-tags')
							)
						),
						"align" => array(
							"title" => __("Alignment", 'additional-tags'),
							"desc" => __("Title text alignment", 'additional-tags'),
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						), 
						"font_size" => array(
							"title" => __("Font_size", 'additional-tags'),
							"desc" => __("Custom font size. If empty - use theme default", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"font_weight" => array(
							"title" => __("Font weight", 'additional-tags'),
							"desc" => __("Custom font weight. If empty or inherit - use theme default", 'additional-tags'),
							"value" => "",
							"type" => "select",
							"size" => "medium",
							"options" => array(
								'inherit' => __('Default', 'additional-tags'),
								'100' => __('Thin (100)', 'additional-tags'),
								'300' => __('Light (300)', 'additional-tags'),
								'400' => __('Normal (400)', 'additional-tags'),
								'600' => __('Semibold (600)', 'additional-tags'),
								'700' => __('Bold (700)', 'additional-tags'),
								'900' => __('Black (900)', 'additional-tags')
							)
						),
						"color" => array(
							"title" => __("Title color", 'additional-tags'),
							"desc" => __("Select color for the title", 'additional-tags'),
							"value" => "",
							"type" => "color"
						),
						"icon" => array(
							"title" => __('Title font icon', 'additional-tags'),
							"desc" => __("Select font icon for the title from Fontello icons set (if style=iconed)", 'additional-tags'),
							"dependency" => array(
								'style' => array('iconed')
							),
							"value" => "",
							"type" => "icons",
							"options" => $THEMEREX_GLOBALS['sc_params']['icons']
						),
						"image" => array(
							"title" => __('or image icon', 'additional-tags'),
							"desc" => __("Select image icon for the title instead icon above (if style=iconed)", 'additional-tags'),
							"dependency" => array(
								'style' => array('iconed')
							),
							"value" => "",
							"type" => "images",
							"size" => "small",
							"options" => $THEMEREX_GLOBALS['sc_params']['images']
						),
						"picture" => array(
							"title" => __('or URL for image file', 'additional-tags'),
							"desc" => __("Select or upload image or write URL from other site (if style=iconed)", 'additional-tags'),
							"dependency" => array(
								'style' => array('iconed')
							),
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"image_size" => array(
							"title" => __('Image (picture) size', 'additional-tags'),
							"desc" => __("Select image (picture) size (if style='iconed')", 'additional-tags'),
							"dependency" => array(
								'style' => array('iconed')
							),
							"value" => "small",
							"type" => "checklist",
							"options" => array(
								'small' => __('Small', 'additional-tags'),
								'medium' => __('Medium', 'additional-tags'),
								'large' => __('Large', 'additional-tags')
							)
						),
						"position" => array(
							"title" => __('Icon (image) position', 'additional-tags'),
							"desc" => __("Select icon (image) position (if style=iconed)", 'additional-tags'),
							"dependency" => array(
								'style' => array('iconed')
							),
							"value" => "left",
							"type" => "checklist",
							"options" => array(
								'top' => __('Top', 'additional-tags'),
								'left' => __('Left', 'additional-tags')
							)
						),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
			
				// Toggles
				"trx_toggles" => array(
					"title" => __("Toggles", 'additional-tags'),
					"desc" => __("Toggles items", 'additional-tags'),
					"decorate" => true,
					"container" => false,
					"params" => array(
						"style" => array(
							"title" => __("Toggles style", 'additional-tags'),
							"desc" => __("Select style for display toggles", 'additional-tags'),
							"value" => 1,
							"options" => array(
								1 => __('Style 1', 'additional-tags'),
								2 => __('Style 2', 'additional-tags')
							),
							"type" => "radio"
						),
						"counter" => array(
							"title" => __("Counter", 'additional-tags'),
							"desc" => __("Display counter before each toggles title", 'additional-tags'),
							"value" => "off",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['on_off']
						),
						"icon_closed" => array(
							"title" => __("Icon while closed", 'additional-tags'),
							"desc" => __('Select icon for the closed toggles item from Fontello icons set', 'additional-tags'),
							"value" => "",
							"type" => "icons",
							"options" => $THEMEREX_GLOBALS['sc_params']['icons']
						),
						"icon_opened" => array(
							"title" => __("Icon while opened", 'additional-tags'),
							"desc" => __('Select icon for the opened toggles item from Fontello icons set', 'additional-tags'),
							"value" => "",
							"type" => "icons",
							"options" => $THEMEREX_GLOBALS['sc_params']['icons']
						),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					),
					"children" => array(
						"name" => "trx_toggles_item",
						"title" => __("Toggles item", 'additional-tags'),
						"desc" => __("Toggles item", 'additional-tags'),
						"container" => true,
						"params" => array(
							"title" => array(
								"title" => __("Toggles item title", 'additional-tags'),
								"desc" => __("Title for current toggles item", 'additional-tags'),
								"value" => "",
								"type" => "text"
							),
							"open" => array(
								"title" => __("Open on show", 'additional-tags'),
								"desc" => __("Open current toggles item on show", 'additional-tags'),
								"value" => "no",
								"type" => "switch",
								"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
							),
							"icon_closed" => array(
								"title" => __("Icon while closed", 'additional-tags'),
								"desc" => __('Select icon for the closed toggles item from Fontello icons set', 'additional-tags'),
								"value" => "",
								"type" => "icons",
								"options" => $THEMEREX_GLOBALS['sc_params']['icons']
							),
							"icon_opened" => array(
								"title" => __("Icon while opened", 'additional-tags'),
								"desc" => __('Select icon for the opened toggles item from Fontello icons set', 'additional-tags'),
								"value" => "",
								"type" => "icons",
								"options" => $THEMEREX_GLOBALS['sc_params']['icons']
							),
							"_content_" => array(
								"title" => __("Toggles item content", 'additional-tags'),
								"desc" => __("Current toggles item content", 'additional-tags'),
								"rows" => 4,
								"value" => "",
								"type" => "textarea"
							),
							"id" => $THEMEREX_GLOBALS['sc_params']['id'],
							"class" => $THEMEREX_GLOBALS['sc_params']['class'],
							"css" => $THEMEREX_GLOBALS['sc_params']['css']
						)
					)
				),
			
			
			
			
			
				// Tooltip
				"trx_tooltip" => array(
					"title" => __("Tooltip", 'additional-tags'),
					"desc" => __("Create tooltip for selected text", 'additional-tags'),
					"decorate" => false,
					"container" => true,
					"params" => array(
						"title" => array(
							"title" => __("Title", 'additional-tags'),
							"desc" => __("Tooltip title (required)", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"_content_" => array(
							"title" => __("Tipped content", 'additional-tags'),
							"desc" => __("Highlighted content with tooltip", 'additional-tags'),
							"divider" => true,
							"rows" => 4,
							"value" => "",
							"type" => "textarea"
						),
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
				// Twitter
				"trx_twitter" => array(
					"title" => __("Twitter", 'additional-tags'),
					"desc" => __("Insert twitter feed into post (page)", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"user" => array(
							"title" => __("Twitter Username", 'additional-tags'),
							"desc" => __("Your username in the twitter account. If empty - get it from Theme Options.", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"consumer_key" => array(
							"title" => __("Consumer Key", 'additional-tags'),
							"desc" => __("Consumer Key from the twitter account", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"consumer_secret" => array(
							"title" => __("Consumer Secret", 'additional-tags'),
							"desc" => __("Consumer Secret from the twitter account", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"token_key" => array(
							"title" => __("Token Key", 'additional-tags'),
							"desc" => __("Token Key from the twitter account", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"token_secret" => array(
							"title" => __("Token Secret", 'additional-tags'),
							"desc" => __("Token Secret from the twitter account", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"count" => array(
							"title" => __("Tweets number", 'additional-tags'),
							"desc" => __("Tweets number to show", 'additional-tags'),
							"divider" => true,
							"value" => 3,
							"max" => 20,
							"min" => 1,
							"type" => "spinner"
						),
						"controls" => array(
							"title" => __("Show arrows", 'additional-tags'),
							"desc" => __("Show control buttons", 'additional-tags'),
							"value" => "yes",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"interval" => array(
							"title" => __("Tweets change interval", 'additional-tags'),
							"desc" => __("Tweets change interval (in milliseconds: 1000ms = 1s)", 'additional-tags'),
							"value" => 7000,
							"step" => 500,
							"min" => 0,
							"type" => "spinner"
						),
						"align" => array(
							"title" => __("Alignment", 'additional-tags'),
							"desc" => __("Alignment of the tweets block", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						),
						"autoheight" => array(
							"title" => __("Autoheight", 'additional-tags'),
							"desc" => __("Change whole slider's height (make it equal current slide's height)", 'additional-tags'),
							"value" => "yes",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						),
						"bg_tint" => array(
							"title" => __("Background tint", 'additional-tags'),
							"desc" => __("Main background tint: dark or light", 'additional-tags'),
							"divider" => true,
							"value" => "",
							"type" => "checklist",
							"options" => $THEMEREX_GLOBALS['sc_params']['tint']
						),
						"bg_color" => array(
							"title" => __("Background color", 'additional-tags'),
							"desc" => __("Any background color for this section", 'additional-tags'),
							"value" => "",
							"type" => "color"
						),
						"bg_image" => array(
							"title" => __("Background image URL", 'additional-tags'),
							"desc" => __("Select or upload image or write URL from other site for the background", 'additional-tags'),
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"bg_overlay" => array(
							"title" => __("Overlay", 'additional-tags'),
							"desc" => __("Overlay color opacity (from 0.0 to 1.0)", 'additional-tags'),
							"min" => "0",
							"max" => "1",
							"step" => "0.1",
							"value" => "0",
							"type" => "spinner"
						),
						"bg_texture" => array(
							"title" => __("Texture", 'additional-tags'),
							"desc" => __("Predefined texture style from 1 to 11. 0 - without texture.", 'additional-tags'),
							"min" => "0",
							"max" => "11",
							"step" => "1",
							"value" => "0",
							"type" => "spinner"
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
				// Video
				"trx_video" => array(
					"title" => __("Video", 'additional-tags'),
					"desc" => __("Insert video player", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"url" => array(
							"title" => __("URL for video file", 'additional-tags'),
							"desc" => __("Select video from media library or paste URL for video file from other site", 'additional-tags'),
							"readonly" => false,
							"value" => "",
							"type" => "media",
							"before" => array(
								'title' => __('Choose video', 'additional-tags'),
								'action' => 'media_upload',
								'type' => 'video',
								'multiple' => false,
								'linked_field' => '',
								'captions' => array( 	
									'choose' => __('Choose video file', 'additional-tags'),
									'update' => __('Select video file', 'additional-tags')
								)
							),
							"after" => array(
								'icon' => 'icon-cancel',
								'action' => 'media_reset'
							)
						),
						"ratio" => array(
							"title" => __("Ratio", 'additional-tags'),
							"desc" => __("Ratio of the video", 'additional-tags'),
							"value" => "16:9",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => array(
								"16:9" => __("16:9", 'additional-tags'),
								"4:3" => __("4:3", 'additional-tags')
							)
						),
						"autoplay" => array(
							"title" => __("Autoplay video", 'additional-tags'),
							"desc" => __("Autoplay video on page load", 'additional-tags'),
							"value" => "off",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['on_off']
						),
						"align" => array(
							"title" => __("Align", 'additional-tags'),
							"desc" => __("Select block alignment", 'additional-tags'),
							"value" => "none",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['align']
						),
						"image" => array(
							"title" => __("Cover image", 'additional-tags'),
							"desc" => __("Select or upload image or write URL from other site for video preview", 'additional-tags'),
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"bg_image" => array(
							"title" => __("Background image", 'additional-tags'),
							"desc" => __("Select or upload image or write URL from other site for video background. Attention! If you use background image - specify paddings below from background margins to video block in percents!", 'additional-tags'),
							"divider" => true,
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"bg_top" => array(
							"title" => __("Top offset", 'additional-tags'),
							"desc" => __("Top offset (padding) inside background image to video block (in percent). For example: 3%", 'additional-tags'),
							"dependency" => array(
								'bg_image' => array('not_empty')
							),
							"value" => "",
							"type" => "text"
						),
						"bg_bottom" => array(
							"title" => __("Bottom offset", 'additional-tags'),
							"desc" => __("Bottom offset (padding) inside background image to video block (in percent). For example: 3%", 'additional-tags'),
							"dependency" => array(
								'bg_image' => array('not_empty')
							),
							"value" => "",
							"type" => "text"
						),
						"bg_left" => array(
							"title" => __("Left offset", 'additional-tags'),
							"desc" => __("Left offset (padding) inside background image to video block (in percent). For example: 20%", 'additional-tags'),
							"dependency" => array(
								'bg_image' => array('not_empty')
							),
							"value" => "",
							"type" => "text"
						),
						"bg_right" => array(
							"title" => __("Right offset", 'additional-tags'),
							"desc" => __("Right offset (padding) inside background image to video block (in percent). For example: 12%", 'additional-tags'),
							"dependency" => array(
								'bg_image' => array('not_empty')
							),
							"value" => "",
							"type" => "text"
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				),
			
			
			
			
				// Zoom
				"trx_zoom" => array(
					"title" => __("Zoom", 'additional-tags'),
					"desc" => __("Insert the image with zoom/lens effect", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"effect" => array(
							"title" => __("Effect", 'additional-tags'),
							"desc" => __("Select effect to display overlapping image", 'additional-tags'),
							"value" => "lens",
							"size" => "medium",
							"type" => "switch",
							"options" => array(
								"lens" => __('Lens', 'additional-tags'),
								"zoom" => __('Zoom', 'additional-tags')
							)
						),
						"url" => array(
							"title" => __("Main image", 'additional-tags'),
							"desc" => __("Select or upload main image", 'additional-tags'),
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"over" => array(
							"title" => __("Overlaping image", 'additional-tags'),
							"desc" => __("Select or upload overlaping image", 'additional-tags'),
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"align" => array(
							"title" => __("Float zoom", 'additional-tags'),
							"desc" => __("Float zoom to left or right side", 'additional-tags'),
							"value" => "",
							"type" => "checklist",
							"dir" => "horizontal",
							"options" => $THEMEREX_GLOBALS['sc_params']['float']
						), 
						"bg_image" => array(
							"title" => __("Background image", 'additional-tags'),
							"desc" => __("Select or upload image or write URL from other site for zoom block background. Attention! If you use background image - specify paddings below from background margins to zoom block in percents!", 'additional-tags'),
							"divider" => true,
							"readonly" => false,
							"value" => "",
							"type" => "media"
						),
						"bg_top" => array(
							"title" => __("Top offset", 'additional-tags'),
							"desc" => __("Top offset (padding) inside background image to zoom block (in percent). For example: 3%", 'additional-tags'),
							"dependency" => array(
								'bg_image' => array('not_empty')
							),
							"value" => "",
							"type" => "text"
						),
						"bg_bottom" => array(
							"title" => __("Bottom offset", 'additional-tags'),
							"desc" => __("Bottom offset (padding) inside background image to zoom block (in percent). For example: 3%", 'additional-tags'),
							"dependency" => array(
								'bg_image' => array('not_empty')
							),
							"value" => "",
							"type" => "text"
						),
						"bg_left" => array(
							"title" => __("Left offset", 'additional-tags'),
							"desc" => __("Left offset (padding) inside background image to zoom block (in percent). For example: 20%", 'additional-tags'),
							"dependency" => array(
								'bg_image' => array('not_empty')
							),
							"value" => "",
							"type" => "text"
						),
						"bg_right" => array(
							"title" => __("Right offset", 'additional-tags'),
							"desc" => __("Right offset (padding) inside background image to zoom block (in percent). For example: 12%", 'additional-tags'),
							"dependency" => array(
								'bg_image' => array('not_empty')
							),
							"value" => "",
							"type" => "text"
						),
						"width" => themerex_shortcodes_width(),
						"height" => themerex_shortcodes_height(),
						"top" => $THEMEREX_GLOBALS['sc_params']['top'],
						"bottom" => $THEMEREX_GLOBALS['sc_params']['bottom'],
						"left" => $THEMEREX_GLOBALS['sc_params']['left'],
						"right" => $THEMEREX_GLOBALS['sc_params']['right'],
						"id" => $THEMEREX_GLOBALS['sc_params']['id'],
						"class" => $THEMEREX_GLOBALS['sc_params']['class'],
						"animation" => $THEMEREX_GLOBALS['sc_params']['animation'],
						"css" => $THEMEREX_GLOBALS['sc_params']['css']
					)
				)
			);
	
			// Woocommerce Shortcodes list
			//------------------------------------------------------------------
			if (themerex_exists_woocommerce()) {
				
				// WooCommerce - Cart
				$THEMEREX_GLOBALS['shortcodes']["woocommerce_cart"] = array(
					"title" => __("Woocommerce: Cart", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: show Cart page", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array()
				);
				
				// WooCommerce - Checkout
				$THEMEREX_GLOBALS['shortcodes']["woocommerce_checkout"] = array(
					"title" => __("Woocommerce: Checkout", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: show Checkout page", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array()
				);
				
				// WooCommerce - My Account
				$THEMEREX_GLOBALS['shortcodes']["woocommerce_my_account"] = array(
					"title" => __("Woocommerce: My Account", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: show My Account page", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array()
				);
				
				// WooCommerce - Order Tracking
				$THEMEREX_GLOBALS['shortcodes']["woocommerce_order_tracking"] = array(
					"title" => __("Woocommerce: Order Tracking", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: show Order Tracking page", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array()
				);
				
				// WooCommerce - Shop Messages
				$THEMEREX_GLOBALS['shortcodes']["shop_messages"] = array(
					"title" => __("Woocommerce: Shop Messages", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: show shop messages", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array()
				);
				
				// WooCommerce - Product Page
				$THEMEREX_GLOBALS['shortcodes']["product_page"] = array(
					"title" => __("Woocommerce: Product Page", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: display single product page", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"sku" => array(
							"title" => __("SKU", 'additional-tags'),
							"desc" => __("SKU code of displayed product", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"id" => array(
							"title" => __("ID", 'additional-tags'),
							"desc" => __("ID of displayed product", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"posts_per_page" => array(
							"title" => __("Number", 'additional-tags'),
							"desc" => __("How many products showed", 'additional-tags'),
							"value" => "1",
							"min" => 1,
							"type" => "spinner"
						),
						"post_type" => array(
							"title" => __("Post type", 'additional-tags'),
							"desc" => __("Post type for the WP query (leave 'product')", 'additional-tags'),
							"value" => "product",
							"type" => "text"
						),
						"post_status" => array(
							"title" => __("Post status", 'additional-tags'),
							"desc" => __("Display posts only with this status", 'additional-tags'),
							"value" => "publish",
							"type" => "select",
							"options" => array(
								"publish" => __('Publish', 'additional-tags'),
								"protected" => __('Protected', 'additional-tags'),
								"private" => __('Private', 'additional-tags'),
								"pending" => __('Pending', 'additional-tags'),
								"draft" => __('Draft', 'additional-tags')
							)
						)
					)
				);
				
				// WooCommerce - Product
				$THEMEREX_GLOBALS['shortcodes']["product"] = array(
					"title" => __("Woocommerce: Product", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: display one product", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"sku" => array(
							"title" => __("SKU", 'additional-tags'),
							"desc" => __("SKU code of displayed product", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"id" => array(
							"title" => __("ID", 'additional-tags'),
							"desc" => __("ID of displayed product", 'additional-tags'),
							"value" => "",
							"type" => "text"
						)
					)
				);
				
				// WooCommerce - Best Selling Products
				$THEMEREX_GLOBALS['shortcodes']["best_selling_products"] = array(
					"title" => __("Woocommerce: Best Selling Products", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: show best selling products", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"per_page" => array(
							"title" => __("Number", 'additional-tags'),
							"desc" => __("How many products showed", 'additional-tags'),
							"value" => 4,
							"min" => 1,
							"type" => "spinner"
						),
						"columns" => array(
							"title" => __("Columns", 'additional-tags'),
							"desc" => __("How many columns per row use for products output", 'additional-tags'),
							"value" => 4,
							"min" => 2,
							"max" => 4,
							"type" => "spinner"
						)
					)
				);
				
				// WooCommerce - Recent Products
				$THEMEREX_GLOBALS['shortcodes']["recent_products"] = array(
					"title" => __("Woocommerce: Recent Products", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: show recent products", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"per_page" => array(
							"title" => __("Number", 'additional-tags'),
							"desc" => __("How many products showed", 'additional-tags'),
							"value" => 4,
							"min" => 1,
							"type" => "spinner"
						),
						"columns" => array(
							"title" => __("Columns", 'additional-tags'),
							"desc" => __("How many columns per row use for products output", 'additional-tags'),
							"value" => 4,
							"min" => 2,
							"max" => 4,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => __("Order by", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "date",
							"type" => "select",
							"options" => array(
								"date" => __('Date', 'additional-tags'),
								"title" => __('Title', 'additional-tags')
							)
						),
						"order" => array(
							"title" => __("Order", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "desc",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
						)
					)
				);
				
				// WooCommerce - Related Products
				$THEMEREX_GLOBALS['shortcodes']["related_products"] = array(
					"title" => __("Woocommerce: Related Products", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: show related products", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"posts_per_page" => array(
							"title" => __("Number", 'additional-tags'),
							"desc" => __("How many products showed", 'additional-tags'),
							"value" => 4,
							"min" => 1,
							"type" => "spinner"
						),
						"columns" => array(
							"title" => __("Columns", 'additional-tags'),
							"desc" => __("How many columns per row use for products output", 'additional-tags'),
							"value" => 4,
							"min" => 2,
							"max" => 4,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => __("Order by", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "date",
							"type" => "select",
							"options" => array(
								"date" => __('Date', 'additional-tags'),
								"title" => __('Title', 'additional-tags')
							)
						)
					)
				);
				
				// WooCommerce - Featured Products
				$THEMEREX_GLOBALS['shortcodes']["featured_products"] = array(
					"title" => __("Woocommerce: Featured Products", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: show featured products", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"per_page" => array(
							"title" => __("Number", 'additional-tags'),
							"desc" => __("How many products showed", 'additional-tags'),
							"value" => 4,
							"min" => 1,
							"type" => "spinner"
						),
						"columns" => array(
							"title" => __("Columns", 'additional-tags'),
							"desc" => __("How many columns per row use for products output", 'additional-tags'),
							"value" => 4,
							"min" => 2,
							"max" => 4,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => __("Order by", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "date",
							"type" => "select",
							"options" => array(
								"date" => __('Date', 'additional-tags'),
								"title" => __('Title', 'additional-tags')
							)
						),
						"order" => array(
							"title" => __("Order", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "desc",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
						)
					)
				);
				
				// WooCommerce - Top Rated Products
				$THEMEREX_GLOBALS['shortcodes']["featured_products"] = array(
					"title" => __("Woocommerce: Top Rated Products", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: show top rated products", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"per_page" => array(
							"title" => __("Number", 'additional-tags'),
							"desc" => __("How many products showed", 'additional-tags'),
							"value" => 4,
							"min" => 1,
							"type" => "spinner"
						),
						"columns" => array(
							"title" => __("Columns", 'additional-tags'),
							"desc" => __("How many columns per row use for products output", 'additional-tags'),
							"value" => 4,
							"min" => 2,
							"max" => 4,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => __("Order by", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "date",
							"type" => "select",
							"options" => array(
								"date" => __('Date', 'additional-tags'),
								"title" => __('Title', 'additional-tags')
							)
						),
						"order" => array(
							"title" => __("Order", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "desc",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
						)
					)
				);
				
				// WooCommerce - Sale Products
				$THEMEREX_GLOBALS['shortcodes']["featured_products"] = array(
					"title" => __("Woocommerce: Sale Products", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: list products on sale", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"per_page" => array(
							"title" => __("Number", 'additional-tags'),
							"desc" => __("How many products showed", 'additional-tags'),
							"value" => 4,
							"min" => 1,
							"type" => "spinner"
						),
						"columns" => array(
							"title" => __("Columns", 'additional-tags'),
							"desc" => __("How many columns per row use for products output", 'additional-tags'),
							"value" => 4,
							"min" => 2,
							"max" => 4,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => __("Order by", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "date",
							"type" => "select",
							"options" => array(
								"date" => __('Date', 'additional-tags'),
								"title" => __('Title', 'additional-tags')
							)
						),
						"order" => array(
							"title" => __("Order", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "desc",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
						)
					)
				);
				
				// WooCommerce - Product Category
				$THEMEREX_GLOBALS['shortcodes']["product_category"] = array(
					"title" => __("Woocommerce: Products from category", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: list products in specified category(-ies)", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"per_page" => array(
							"title" => __("Number", 'additional-tags'),
							"desc" => __("How many products showed", 'additional-tags'),
							"value" => 4,
							"min" => 1,
							"type" => "spinner"
						),
						"columns" => array(
							"title" => __("Columns", 'additional-tags'),
							"desc" => __("How many columns per row use for products output", 'additional-tags'),
							"value" => 4,
							"min" => 2,
							"max" => 4,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => __("Order by", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "date",
							"type" => "select",
							"options" => array(
								"date" => __('Date', 'additional-tags'),
								"title" => __('Title', 'additional-tags')
							)
						),
						"order" => array(
							"title" => __("Order", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "desc",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
						),
						"category" => array(
							"title" => __("Categories", 'additional-tags'),
							"desc" => __("Comma separated category slugs", 'additional-tags'),
							"value" => '',
							"type" => "text"
						),
						"operator" => array(
							"title" => __("Operator", 'additional-tags'),
							"desc" => __("Categories operator", 'additional-tags'),
							"value" => "IN",
							"type" => "checklist",
							"size" => "medium",
							"options" => array(
								"IN" => __('IN', 'additional-tags'),
								"NOT IN" => __('NOT IN', 'additional-tags'),
								"AND" => __('AND', 'additional-tags')
							)
						)
					)
				);
				
				// WooCommerce - Products
				$THEMEREX_GLOBALS['shortcodes']["products"] = array(
					"title" => __("Woocommerce: Products", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: list all products", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"skus" => array(
							"title" => __("SKUs", 'additional-tags'),
							"desc" => __("Comma separated SKU codes of products", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"ids" => array(
							"title" => __("IDs", 'additional-tags'),
							"desc" => __("Comma separated ID of products", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"columns" => array(
							"title" => __("Columns", 'additional-tags'),
							"desc" => __("How many columns per row use for products output", 'additional-tags'),
							"value" => 4,
							"min" => 2,
							"max" => 4,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => __("Order by", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "date",
							"type" => "select",
							"options" => array(
								"date" => __('Date', 'additional-tags'),
								"title" => __('Title', 'additional-tags')
							)
						),
						"order" => array(
							"title" => __("Order", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "desc",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
						)
					)
				);
				
				// WooCommerce - Product attribute
				$THEMEREX_GLOBALS['shortcodes']["product_attribute"] = array(
					"title" => __("Woocommerce: Products by Attribute", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: show products with specified attribute", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"per_page" => array(
							"title" => __("Number", 'additional-tags'),
							"desc" => __("How many products showed", 'additional-tags'),
							"value" => 4,
							"min" => 1,
							"type" => "spinner"
						),
						"columns" => array(
							"title" => __("Columns", 'additional-tags'),
							"desc" => __("How many columns per row use for products output", 'additional-tags'),
							"value" => 4,
							"min" => 2,
							"max" => 4,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => __("Order by", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "date",
							"type" => "select",
							"options" => array(
								"date" => __('Date', 'additional-tags'),
								"title" => __('Title', 'additional-tags')
							)
						),
						"order" => array(
							"title" => __("Order", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "desc",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
						),
						"attribute" => array(
							"title" => __("Attribute", 'additional-tags'),
							"desc" => __("Attribute name", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"filter" => array(
							"title" => __("Filter", 'additional-tags'),
							"desc" => __("Attribute value", 'additional-tags'),
							"value" => "",
							"type" => "text"
						)
					)
				);
				
				// WooCommerce - Products Categories
				$THEMEREX_GLOBALS['shortcodes']["product_categories"] = array(
					"title" => __("Woocommerce: Product Categories", 'additional-tags'),
					"desc" => __("WooCommerce shortcode: show categories with products", 'additional-tags'),
					"decorate" => false,
					"container" => false,
					"params" => array(
						"number" => array(
							"title" => __("Number", 'additional-tags'),
							"desc" => __("How many categories showed", 'additional-tags'),
							"value" => 4,
							"min" => 1,
							"type" => "spinner"
						),
						"columns" => array(
							"title" => __("Columns", 'additional-tags'),
							"desc" => __("How many columns per row use for categories output", 'additional-tags'),
							"value" => 4,
							"min" => 2,
							"max" => 4,
							"type" => "spinner"
						),
						"orderby" => array(
							"title" => __("Order by", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "date",
							"type" => "select",
							"options" => array(
								"date" => __('Date', 'additional-tags'),
								"title" => __('Title', 'additional-tags')
							)
						),
						"order" => array(
							"title" => __("Order", 'additional-tags'),
							"desc" => __("Sorting order for products output", 'additional-tags'),
							"value" => "desc",
							"type" => "switch",
							"size" => "big",
							"options" => $THEMEREX_GLOBALS['sc_params']['ordering']
						),
						"parent" => array(
							"title" => __("Parent", 'additional-tags'),
							"desc" => __("Parent category slug", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"ids" => array(
							"title" => __("IDs", 'additional-tags'),
							"desc" => __("Comma separated ID of products", 'additional-tags'),
							"value" => "",
							"type" => "text"
						),
						"hide_empty" => array(
							"title" => __("Hide empty", 'additional-tags'),
							"desc" => __("Hide empty categories", 'additional-tags'),
							"value" => "yes",
							"type" => "switch",
							"options" => $THEMEREX_GLOBALS['sc_params']['yes_no']
						)
					)
				);

			}
			
			do_action('themerex_action_shortcodes_list');

		}
	}
}
?>