<?php

// Width and height params
if ( !function_exists( 'themerex_vc_width' ) ) {
	function themerex_vc_width($w='') {
		return array(
			"param_name" => "width",
			"heading" => __("Width", 'additional-tags'),
			"description" => __("Width (in pixels or percent) of the current element", 'additional-tags'),
			"group" => __('Size &amp; Margins', 'additional-tags'),
			"value" => $w,
			"type" => "textfield"
		);
	}
}
if ( !function_exists( 'themerex_vc_height' ) ) {
	function themerex_vc_height($h='') {
		return array(
			"param_name" => "height",
			"heading" => __("Height", 'additional-tags'),
			"description" => __("Height (only in pixels) of the current element", 'additional-tags'),
			"group" => __('Size &amp; Margins', 'additional-tags'),
			"value" => $h,
			"type" => "textfield"
		);
	}
}

// Load scripts and styles for VC support
if ( !function_exists( 'themerex_shortcodes_vc_scripts_admin' ) ) {
	//add_action( 'admin_enqueue_scripts', 'themerex_shortcodes_vc_scripts_admin' );
	function themerex_shortcodes_vc_scripts_admin() {
		// Include CSS 
		wp_enqueue_style ( 'shortcodes_vc-style', trx_addons_get_file_url('shortcodes/shortcodes_vc_admin.css'), array(), null );
		// Include JS
		wp_enqueue_script( 'shortcodes_vc-script', trx_addons_get_file_url('shortcodes/shortcodes_vc_admin.js'), array(), null, true );
	}
}

// Load scripts and styles for VC support
if ( !function_exists( 'themerex_shortcodes_vc_scripts_front' ) ) {
	//add_action( 'wp_enqueue_scripts', 'themerex_shortcodes_vc_scripts_front' );
	function themerex_shortcodes_vc_scripts_front() {
		if (themerex_vc_is_frontend()) {
			// Include CSS 
			wp_enqueue_style ( 'shortcodes_vc-style', trx_addons_get_file_url('shortcodes/shortcodes_vc_front.css'), array(), null );
			// Include JS
//			wp_enqueue_script( 'shortcodes_vc-script', trx_addons_get_file_url('shortcodes/shortcodes_vc_front.js'), array(), null, true );
		}
	}
}

// Add init script into shortcodes output in VC frontend editor
if ( !function_exists( 'themerex_shortcodes_vc_add_init_script' ) ) {
	//add_filter('themerex_shortcode_output', 'themerex_shortcodes_vc_add_init_script', 10, 4);
	function themerex_shortcodes_vc_add_init_script($output, $tag='', $atts=array(), $content='') {
		if ( (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') && (isset($_POST['action']) && $_POST['action']=='vc_load_shortcode')
				&& ( isset($_POST['shortcodes'][0]['tag']) && $_POST['shortcodes'][0]['tag']==$tag )
		) {
			if (themerex_strpos($output, 'themerex_vc_init_shortcodes')===false) {
				$id = "themerex_vc_init_shortcodes_".str_replace('.', '', mt_rand());
				$output .= '
					<script id="'.esc_attr($id).'">
						try {
							themerex_init_post_formats();
							themerex_init_shortcodes(jQuery("body").eq(0));
							themerex_scroll_actions();
						} catch (e) { };
					</script>
				';
			}
		}
		return $output;
	}
}

// Prevent simultaneous editing of posts for Gutenberg and other PageBuilders (VC, Elementor)
if ( ! function_exists( 'trx_addons_gutenberg_disable_cpt' ) ) {
    add_action( 'current_screen', 'trx_addons_gutenberg_disable_cpt' );
    function trx_addons_gutenberg_disable_cpt() {
        $safe_pb = array('vc');
        if ( !empty($safe_pb) && function_exists( 'the_gutenberg_project' ) && function_exists( 'register_block_type' ) ) {
            $current_post_type = get_current_screen()->post_type;
            $disable = false;
            if ( !$disable && in_array('vc', $safe_pb) && function_exists('vc_editor_post_types') ) {
                $post_types = vc_editor_post_types();
                $disable = is_array($post_types) && in_array($current_post_type, $post_types);
            }
            if ( $disable ) {
                remove_filter( 'replace_editor', 'gutenberg_init' );
                remove_action( 'load-post.php', 'gutenberg_intercept_edit_post' );
                remove_action( 'load-post-new.php', 'gutenberg_intercept_post_new' );
                remove_action( 'admin_init', 'gutenberg_add_edit_link_filters' );
                remove_filter( 'admin_url', 'gutenberg_modify_add_new_button_url' );
                remove_action( 'admin_print_scripts-edit.php', 'gutenberg_replace_default_add_new_button' );
                remove_action( 'admin_enqueue_scripts', 'gutenberg_editor_scripts_and_styles' );
                remove_filter( 'screen_options_show_screen', '__return_false' );
            }
        }
    }
}


/* Theme setup section
-------------------------------------------------------------------- */

if ( !function_exists( 'themerex_shortcodes_vc_theme_setup' ) ) {
	//if ( themerex_vc_is_frontend() )
	if ( (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true') || (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline') )
		add_action( 'themerex_action_before_init_theme', 'themerex_shortcodes_vc_theme_setup', 20 );
	else
		add_action( 'themerex_action_after_init_theme', 'themerex_shortcodes_vc_theme_setup' );
	function themerex_shortcodes_vc_theme_setup() {
		if (themerex_shortcodes_is_used()) {
			// Set VC as main editor for the theme
			vc_set_as_theme( true );
			
			// Enable VC on follow post types
			vc_set_default_editor_post_types( array('page', 'team', 'courses') );
			
			// Disable frontend editor
			//vc_disable_frontend();

			// Load scripts and styles for VC support
			add_action( 'wp_enqueue_scripts',		'themerex_shortcodes_vc_scripts_front');
			add_action( 'admin_enqueue_scripts',	'themerex_shortcodes_vc_scripts_admin' );

			// Add init script into shortcodes output in VC frontend editor
			add_filter('themerex_shortcode_output', 'themerex_shortcodes_vc_add_init_script', 10, 4);

/*
			// Remove standard VC shortcodes
			vc_remove_element("vc_button");
			vc_remove_element("vc_posts_slider");
			vc_remove_element("vc_teaser_grid");
			vc_remove_element("vc_progress_bar");
			vc_remove_element("vc_facebook");
			vc_remove_element("vc_tweetmeme");
			vc_remove_element("vc_googleplus");
			vc_remove_element("vc_facebook");
			vc_remove_element("vc_pinterest");
			vc_remove_element("vc_message");
			vc_remove_element("vc_posts_grid");
			vc_remove_element("vc_carousel");
			vc_remove_element("vc_flickr");
			vc_remove_element("vc_tour");
			vc_remove_element("vc_separator");
			vc_remove_element("vc_single_image");
			vc_remove_element("vc_cta_button");
//			vc_remove_element("vc_accordion");
//			vc_remove_element("vc_accordion_tab");
			vc_remove_element("vc_toggle");
			vc_remove_element("vc_tabs");
			vc_remove_element("vc_tab");
			vc_remove_element("vc_images_carousel");
*/
			
			// Remove standard WP widgets
			vc_remove_element("vc_wp_archives");
			vc_remove_element("vc_wp_calendar");
			vc_remove_element("vc_wp_categories");
			vc_remove_element("vc_wp_custommenu");
			vc_remove_element("vc_wp_links");
			vc_remove_element("vc_wp_meta");
			vc_remove_element("vc_wp_pages");
			vc_remove_element("vc_wp_posts");
			vc_remove_element("vc_wp_recentcomments");
			vc_remove_element("vc_wp_rss");
			vc_remove_element("vc_wp_search");
			vc_remove_element("vc_wp_tagcloud");
			vc_remove_element("vc_wp_text");
			
			global $THEMEREX_GLOBALS;
			
			$THEMEREX_GLOBALS['vc_params'] = array(
				
				// Common arrays and strings
				'category' => __("ThemeREX shortcodes", 'additional-tags'),
			
				// Current element id
				'id' => array(
					"param_name" => "id",
					"heading" => __("Element ID", 'additional-tags'),
					"description" => __("ID for current element", 'additional-tags'),
					"group" => __('ID &amp; Class', 'additional-tags'),
					"value" => "",
					"type" => "textfield"
				),
			
				// Current element class
				'class' => array(
					"param_name" => "class",
					"heading" => __("Element CSS class", 'additional-tags'),
					"description" => __("CSS class for current element", 'additional-tags'),
					"group" => __('ID &amp; Class', 'additional-tags'),
					"value" => "",
					"type" => "textfield"
				),

				// Current element animation
				'animation' => array(
					"param_name" => "animation",
					"heading" => __("Animation", 'additional-tags'),
					"description" => __("Select animation while object enter in the visible area of page", 'additional-tags'),
					"group" => __('ID &amp; Class', 'additional-tags'),
					"class" => "",
					"value" => array_flip($THEMEREX_GLOBALS['sc_params']['animations']),
					"type" => "dropdown"
				),
			
				// Current element style
				'css' => array(
					"param_name" => "css",
					"heading" => __("CSS styles", 'additional-tags'),
					"description" => __("Any additional CSS rules (if need)", 'additional-tags'),
					"group" => __('ID &amp; Class', 'additional-tags'),
					"class" => "",
					"value" => "",
					"type" => "textfield"
				),
			
				// Margins params
				'margin_top' => array(
					"param_name" => "top",
					"heading" => __("Top margin", 'additional-tags'),
					"description" => __("Top margin (in pixels).", 'additional-tags'),
					"group" => __('Size &amp; Margins', 'additional-tags'),
					"value" => "",
					"type" => "textfield"
				),
			
				'margin_bottom' => array(
					"param_name" => "bottom",
					"heading" => __("Bottom margin", 'additional-tags'),
					"description" => __("Bottom margin (in pixels).", 'additional-tags'),
					"group" => __('Size &amp; Margins', 'additional-tags'),
					"value" => "",
					"type" => "textfield"
				),
			
				'margin_left' => array(
					"param_name" => "left",
					"heading" => __("Left margin", 'additional-tags'),
					"description" => __("Left margin (in pixels).", 'additional-tags'),
					"group" => __('Size &amp; Margins', 'additional-tags'),
					"value" => "",
					"type" => "textfield"
				),
				
				'margin_right' => array(
					"param_name" => "right",
					"heading" => __("Right margin", 'additional-tags'),
					"description" => __("Right margin (in pixels).", 'additional-tags'),
					"group" => __('Size &amp; Margins', 'additional-tags'),
					"value" => "",
					"type" => "textfield"
				)
			);
	
	
	
			// Accordion
			//-------------------------------------------------------------------------------------
			vc_map( array(
				"base" => "trx_accordion",
				"name" => __("Accordion", 'additional-tags'),
				"description" => __("Accordion items", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_accordion',
				"class" => "trx_sc_collection trx_sc_accordion",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => false,
				"as_parent" => array('only' => 'trx_accordion_item'),	// Use only|except attributes to limit child shortcodes (separate multiple values with comma)
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => __("Accordion style", 'additional-tags'),
						"description" => __("Select style for display accordion", 'additional-tags'),
						"class" => "",
						"admin_label" => true,
						"value" => array(
							__('Style 1', 'additional-tags') => 1,
							__('Style 2', 'additional-tags') => 2
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "counter",
						"heading" => __("Counter", 'additional-tags'),
						"description" => __("Display counter before each accordion title", 'additional-tags'),
						"class" => "",
						"value" => array("Add item numbers before each element" => "on" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "initial",
						"heading" => __("Initially opened item", 'additional-tags'),
						"description" => __("Number of initially opened item", 'additional-tags'),
						"class" => "",
						"value" => 1,
						"type" => "textfield"
					),
					array(
						"param_name" => "icon_closed",
						"heading" => __("Icon while closed", 'additional-tags'),
						"description" => __("Select icon for the closed accordion item from Fontello icons set", 'additional-tags'),
						"class" => "",
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
						"type" => "dropdown"
					),
					array(
						"param_name" => "icon_opened",
						"heading" => __("Icon while opened", 'additional-tags'),
						"description" => __("Select icon for the opened accordion item from Fontello icons set", 'additional-tags'),
						"class" => "",
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
						"type" => "dropdown"
					),
					$THEMEREX_GLOBALS['vc_params']['margin_top'],
					$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
					$THEMEREX_GLOBALS['vc_params']['margin_left'],
					$THEMEREX_GLOBALS['vc_params']['margin_right'],
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['animation'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
				'default_content' => '
					[trx_accordion_item title="' . __( 'Item 1 title', 'additional-tags') . '"][/trx_accordion_item]
					[trx_accordion_item title="' . __( 'Item 2 title', 'additional-tags') . '"][/trx_accordion_item]
				',
				"custom_markup" => '
					<div class="wpb_accordion_holder wpb_holder clearfix vc_container_for_children">
						%content%
					</div>
					<div class="tab_controls">
						<button class="add_tab" title="'.__("Add item", 'additional-tags').'">'.__("Add item", 'additional-tags').'</button>
					</div>
				',
				'js_view' => 'VcTrxAccordionView'
			) );
			
			
			vc_map( array(
				"base" => "trx_accordion_item",
				"name" => __("Accordion item", 'additional-tags'),
				"description" => __("Inner accordion item", 'additional-tags'),
				"show_settings_on_create" => true,
				"content_element" => true,
				"is_container" => true,
				'icon' => 'icon_trx_accordion_item',
				"as_child" => array('only' => 'trx_accordion'), 	// Use only|except attributes to limit parent (separate multiple values with comma)
				"as_parent" => array('except' => 'trx_accordion'),
				"params" => array(
					array(
						"param_name" => "title",
						"heading" => __("Title", 'additional-tags'),
						"description" => __("Title for current accordion item", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "icon_closed",
						"heading" => __("Icon while closed", 'additional-tags'),
						"description" => __("Select icon for the closed accordion item from Fontello icons set", 'additional-tags'),
						"class" => "",
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
						"type" => "dropdown"
					),
					array(
						"param_name" => "icon_opened",
						"heading" => __("Icon while opened", 'additional-tags'),
						"description" => __("Select icon for the opened accordion item from Fontello icons set", 'additional-tags'),
						"class" => "",
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
						"type" => "dropdown"
					),
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
			  'js_view' => 'VcTrxAccordionTabView'
			) );

			class WPBakeryShortCode_Trx_Accordion extends THEMEREX_VC_ShortCodeAccordion {}
			class WPBakeryShortCode_Trx_Accordion_Item extends THEMEREX_VC_ShortCodeAccordionItem {}
			
			
			
			
			
			
			// Anchor
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_anchor",
				"name" => __("Anchor", 'additional-tags'),
				"description" => __("Insert anchor for the TOC (table of content)", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_anchor',
				"class" => "trx_sc_single trx_sc_anchor",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "icon",
						"heading" => __("Anchor's icon", 'additional-tags'),
						"description" => __("Select icon for the anchor from Fontello icons set", 'additional-tags'),
						"class" => "",
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
						"type" => "dropdown"
					),
					array(
						"param_name" => "title",
						"heading" => __("Short title", 'additional-tags'),
						"description" => __("Short title of the anchor (for the table of content)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "description",
						"heading" => __("Long description", 'additional-tags'),
						"description" => __("Description for the popup (then hover on the icon). You can use '{' and '}' - make the text italic, '|' - insert line break", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "url",
						"heading" => __("External URL", 'additional-tags'),
						"description" => __("External URL for this TOC item", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "separator",
						"heading" => __("Add separator", 'additional-tags'),
						"description" => __("Add separator under item in the TOC", 'additional-tags'),
						"class" => "",
						"value" => array("Add separator" => "yes" ),
						"type" => "checkbox"
					),
					$THEMEREX_GLOBALS['vc_params']['id']
				),
			) );
			
			class WPBakeryShortCode_Trx_Anchor extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			// Audio
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_audio",
				"name" => __("Audio", 'additional-tags'),
				"description" => __("Insert audio player", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_audio',
				"class" => "trx_sc_single trx_sc_audio",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "url",
						"heading" => __("URL for audio file", 'additional-tags'),
						"description" => __("Put here URL for audio file", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "image",
						"heading" => __("Cover image", 'additional-tags'),
						"description" => __("Select or upload image or write URL from other site for audio cover", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "title",
						"heading" => __("Title", 'additional-tags'),
						"description" => __("Title of the audio file", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "author",
						"heading" => __("Author", 'additional-tags'),
						"description" => __("Author of the audio file", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "controls",
						"heading" => __("Controls", 'additional-tags'),
						"description" => __("Show/hide controls", 'additional-tags'),
						"class" => "",
						"value" => array("Hide controls" => "hide" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "autoplay",
						"heading" => __("Autoplay", 'additional-tags'),
						"description" => __("Autoplay audio on page load", 'additional-tags'),
						"class" => "",
						"value" => array("Autoplay" => "on" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Select block alignment", 'additional-tags'),
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
				),
			) );
			
			class WPBakeryShortCode_Trx_Audio extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Block
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_block",
				"name" => __("Block container", 'additional-tags'),
				"description" => __("Container for any block ([section] analog - to enable nesting)", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_block',
				"class" => "trx_sc_collection trx_sc_block",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "dedicated",
						"heading" => __("Dedicated", 'additional-tags'),
						"description" => __("Use this block as dedicated content - show it before post title on single page", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(__('Use as dedicated content', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Select block alignment", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "columns",
						"heading" => __("Columns emulation", 'additional-tags'),
						"description" => __("Select width for columns emulation", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['columns']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "pan",
						"heading" => __("Use pan effect", 'additional-tags'),
						"description" => __("Use pan effect to show section content", 'additional-tags'),
						"group" => __('Scroll', 'additional-tags'),
						"class" => "",
						"value" => array(__('Content scroller', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "scroll",
						"heading" => __("Use scroller", 'additional-tags'),
						"description" => __("Use scroller to show section content", 'additional-tags'),
						"group" => __('Scroll', 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(__('Content scroller', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "scroll_dir",
						"heading" => __("Scroll direction", 'additional-tags'),
						"description" => __("Scroll direction (if Use scroller = yes)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"group" => __('Scroll', 'additional-tags'),
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['dir']),
						'dependency' => array(
							'element' => 'scroll',
							'not_empty' => true
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "scroll_controls",
						"heading" => __("Scroll controls", 'additional-tags'),
						"description" => __("Show scroll controls (if Use scroller = yes)", 'additional-tags'),
						"class" => "",
						"group" => __('Scroll', 'additional-tags'),
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['dir']),
						'dependency' => array(
							'element' => 'scroll',
							'not_empty' => true
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "color",
						"heading" => __("Fore color", 'additional-tags'),
						"description" => __("Any color for objects in this section", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_tint",
						"heading" => __("Background tint", 'additional-tags'),
						"description" => __("Main background tint: dark or light", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['tint']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "bg_color",
						"heading" => __("Background color", 'additional-tags'),
						"description" => __("Any background color for this section", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_image",
						"heading" => __("Background image URL", 'additional-tags'),
						"description" => __("Select background image from library for this section", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "bg_overlay",
						"heading" => __("Overlay", 'additional-tags'),
						"description" => __("Overlay color opacity (from 0.0 to 1.0)", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_texture",
						"heading" => __("Texture", 'additional-tags'),
						"description" => __("Texture style from 1 to 11. Empty or 0 - without texture.", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "font_size",
						"heading" => __("Font size", 'additional-tags'),
						"description" => __("Font size of the text (default - in pixels, allows any CSS units of measure)", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "font_weight",
						"heading" => __("Font weight", 'additional-tags'),
						"description" => __("Font weight of the text", 'additional-tags'),
						"class" => "",
						"value" => array(
							__('Default', 'additional-tags') => 'inherit',
							__('Thin (100)', 'additional-tags') => '100',
							__('Light (300)', 'additional-tags') => '300',
							__('Normal (400)', 'additional-tags') => '400',
							__('Bold (700)', 'additional-tags') => '700'
						),
						"type" => "dropdown"
					),
					/*
					array(
						"param_name" => "content",
						"heading" => __("Container content", 'additional-tags'),
						"description" => __("Content for section container", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					),
					*/
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
			
			class WPBakeryShortCode_Trx_Block extends THEMEREX_VC_ShortCodeCollection {}
			
			
			
			
			
			
			// Blogger
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_blogger",
				"name" => __("Blogger", 'additional-tags'),
				"description" => __("Insert posts (pages) in many styles from desired categories or directly from ids", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_blogger',
				"class" => "trx_sc_single trx_sc_blogger",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => __("Output style", 'additional-tags'),
						"description" => __("Select desired style for posts output", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['blogger_styles']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "filters",
						"heading" => __("Show filters", 'additional-tags'),
						"description" => __("Use post's tags or categories as filter buttons", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['filters']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "hover",
						"heading" => __("Hover effect", 'additional-tags'),
						"description" => __("Select hover effect (only if style=Portfolio)", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['hovers']),
						'dependency' => array(
							'element' => 'style',
							'value' => array('portfolio_2','portfolio_3','portfolio_4','grid_2','grid_3','grid_4','square_2','square_3','square_4','courses_2','courses_3','courses_4')
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "hover_dir",
						"heading" => __("Hover direction", 'additional-tags'),
						"description" => __("Select hover direction (only if style=Portfolio and hover=Circle|Square)", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['hovers_dir']),
						'dependency' => array(
							'element' => 'style',
							'value' => array('portfolio_2','portfolio_3','portfolio_4','grid_2','grid_3','grid_4','square_2','square_3','square_4','courses_2','courses_3','courses_4')
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "location",
						"heading" => __("Dedicated content location", 'additional-tags'),
						"description" => __("Select position for dedicated content (only for style=excerpt)", 'additional-tags'),
						"class" => "",
						'dependency' => array(
							'element' => 'style',
							'value' => array('excerpt')
						),
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['locations']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "dir",
						"heading" => __("Posts direction", 'additional-tags'),
						"description" => __("Display posts in horizontal or vertical direction", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['dir']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "rating",
						"heading" => __("Show rating stars", 'additional-tags'),
						"description" => __("Show rating stars under post's header", 'additional-tags'),
						"group" => __('Details', 'additional-tags'),
						"class" => "",
						"value" => array(__('Show rating', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "info",
						"heading" => __("Show post info block", 'additional-tags'),
						"description" => __("Show post info block (author, date, tags, etc.)", 'additional-tags'),
						"class" => "",
						"value" => array(__('Show info', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "descr",
						"heading" => __("Description length", 'additional-tags'),
						"description" => __("How many characters are displayed from post excerpt? If 0 - don't show description", 'additional-tags'),
						"group" => __('Details', 'additional-tags'),
						"class" => "",
						"value" => 0,
						"type" => "textfield"
					),
					array(
						"param_name" => "links",
						"heading" => __("Allow links to the post", 'additional-tags'),
						"description" => __("Allow links to the post from each blogger item", 'additional-tags'),
						"group" => __('Details', 'additional-tags'),
						"class" => "",
						"value" => array(__('Allow links', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "readmore",
						"heading" => __("More link text", 'additional-tags'),
						"description" => __("Read more link text. If empty - show 'More', else - used as link text", 'additional-tags'),
						"group" => __('Details', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "post_type",
						"heading" => __("Post type", 'additional-tags'),
						"description" => __("Select post type to show", 'additional-tags'),
						"group" => __('Query', 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['posts_types']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "ids",
						"heading" => __("Post IDs list", 'additional-tags'),
						"description" => __("Comma separated list of posts ID. If set - parameters above are ignored!", 'additional-tags'),
						"group" => __('Query', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "cat",
						"heading" => __("Categories list", 'additional-tags'),
						"description" => __("Put here comma separated category slugs or ids. If empty - show posts from any category or from IDs list", 'additional-tags'),
						'dependency' => array(
							'element' => 'ids',
							'is_empty' => true
						),
						"group" => __('Query', 'additional-tags'),
						"class" => "",
						"value" => array_flip(themerex_array_merge(array(0 => __('- Select category -', 'additional-tags')), $THEMEREX_GLOBALS['sc_params']['categories'])),
						"type" => "dropdown"
					),
					array(
						"param_name" => "count",
						"heading" => __("Total posts to show", 'additional-tags'),
						"description" => __("How many posts will be displayed? If used IDs - this parameter ignored.", 'additional-tags'),
						'dependency' => array(
							'element' => 'ids',
							'is_empty' => true
						),
						"admin_label" => true,
						"group" => __('Query', 'additional-tags'),
						"class" => "",
						"value" => 3,
						"type" => "textfield"
					),
					array(
						"param_name" => "columns",
						"heading" => __("Columns number", 'additional-tags'),
						"description" => __("How many columns used to display posts?", 'additional-tags'),
						'dependency' => array(
							'element' => 'dir',
							'value' => 'horizontal'
						),
						"group" => __('Query', 'additional-tags'),
						"class" => "",
						"value" => 3,
						"type" => "textfield"
					),
					array(
						"param_name" => "offset",
						"heading" => __("Offset before select posts", 'additional-tags'),
						"description" => __("Skip posts before select next part.", 'additional-tags'),
						'dependency' => array(
							'element' => 'ids',
							'is_empty' => true
						),
						"group" => __('Query', 'additional-tags'),
						"class" => "",
						"value" => 0,
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => __("Post order by", 'additional-tags'),
						"description" => __("Select desired posts sorting method", 'additional-tags'),
						"class" => "",
						"group" => __('Query', 'additional-tags'),
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['sorting']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => __("Post order", 'additional-tags'),
						"description" => __("Select desired posts order", 'additional-tags'),
						"class" => "",
						"group" => __('Query', 'additional-tags'),
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
						'save_always' => true,
						"std" => "asc",
						"type" => "dropdown"
					),
					array(
						"param_name" => "only",
						"heading" => __("Select posts only", 'additional-tags'),
						"description" => __("Select posts only with reviews, videos, audios, thumbs or galleries", 'additional-tags'),
						"class" => "",
						"group" => __('Query', 'additional-tags'),
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['formats']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "scroll",
						"heading" => __("Use scroller", 'additional-tags'),
						"description" => __("Use scroller to show all posts", 'additional-tags'),
						"group" => __('Scroll', 'additional-tags'),
						"class" => "",
						"value" => array(__('Use scroller', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "controls",
						"heading" => __("Show slider controls", 'additional-tags'),
						"description" => __("Show arrows to control scroll slider", 'additional-tags'),
						"group" => __('Scroll', 'additional-tags'),
						"class" => "",
						"value" => array(__('Show controls', 'additional-tags') => 'yes'),
						"type" => "checkbox"
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
				),
			) );
			
			class WPBakeryShortCode_Trx_Blogger extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			// Br
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_br",
				"name" => __("Line break", 'additional-tags'),
				"description" => __("Line break or Clear Floating", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_br',
				"class" => "trx_sc_single trx_sc_br",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "clear",
						"heading" => __("Clear floating", 'additional-tags'),
						"description" => __("Select clear side (if need)", 'additional-tags'),
						"class" => "",
						"value" => "",
						"value" => array(
							__('None', 'additional-tags') => 'none',
							__('Left', 'additional-tags') => 'left',
							__('Right', 'additional-tags') => 'right',
							__('Both', 'additional-tags') => 'both'
						),
						"type" => "dropdown"
					)
				)
			) );
			
			class WPBakeryShortCode_Trx_Br extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Button
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_button",
				"name" => __("Button", 'additional-tags'),
				"description" => __("Button with link", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_button',
				"class" => "trx_sc_single trx_sc_button",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "content",
						"heading" => __("Caption", 'additional-tags'),
						"description" => __("Button caption", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "type",
						"heading" => __("Button's shape", 'additional-tags'),
						"description" => __("Select button's shape", 'additional-tags'),
						"class" => "",
						"value" => array(
							__('Square', 'additional-tags') => 'square',
							__('Round', 'additional-tags') => 'round'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "style",
						"heading" => __("Button's style", 'additional-tags'),
						"description" => __("Select button's style", 'additional-tags'),
						"class" => "",
						"value" => array(
							__('Filled', 'additional-tags') => 'filled',
							__('Border', 'additional-tags') => 'border'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "size",
						"heading" => __("Button's size", 'additional-tags'),
						"description" => __("Select button's size", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							__('Small', 'additional-tags') => 'mini',
							__('Medium', 'additional-tags') => 'medium',
							__('Large', 'additional-tags') => 'big'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "icon",
						"heading" => __("Button's icon", 'additional-tags'),
						"description" => __("Select icon for the title from Fontello icons set", 'additional-tags'),
						"class" => "",
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
						"type" => "dropdown"
					),
					array(
						"param_name" => "bg_style",
						"heading" => __("Button's color scheme", 'additional-tags'),
						"description" => __("Select button's color scheme", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['button_styles']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "color",
						"heading" => __("Button's text color", 'additional-tags'),
						"description" => __("Any color for button's caption", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_color",
						"heading" => __("Button's backcolor", 'additional-tags'),
						"description" => __("Any color for button's background", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "align",
						"heading" => __("Button's alignment", 'additional-tags'),
						"description" => __("Align button to left, center or right", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "link",
						"heading" => __("Link URL", 'additional-tags'),
						"description" => __("URL for the link on button click", 'additional-tags'),
						"class" => "",
						"group" => __('Link', 'additional-tags'),
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "target",
						"heading" => __("Link target", 'additional-tags'),
						"description" => __("Target for the link on button click", 'additional-tags'),
						"class" => "",
						"group" => __('Link', 'additional-tags'),
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "popup",
						"heading" => __("Open link in popup", 'additional-tags'),
						"description" => __("Open link target in popup window", 'additional-tags'),
						"class" => "",
						"group" => __('Link', 'additional-tags'),
						"value" => array(__('Open in popup', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "rel",
						"heading" => __("Rel attribute", 'additional-tags'),
						"description" => __("Rel attribute for the button's link (if need", 'additional-tags'),
						"class" => "",
						"group" => __('Link', 'additional-tags'),
						"value" => "",
						"type" => "textfield"
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
				),
				'js_view' => 'VcTrxTextView'
			) );
			
			class WPBakeryShortCode_Trx_Button extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Chat
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_chat",
				"name" => __("Chat", 'additional-tags'),
				"description" => __("Chat message", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_chat',
				"class" => "trx_sc_container trx_sc_chat",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "title",
						"heading" => __("Item title", 'additional-tags'),
						"description" => __("Title for current chat item", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "photo",
						"heading" => __("Item photo", 'additional-tags'),
						"description" => __("Select or upload image or write URL from other site for the item photo (avatar)", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "link",
						"heading" => __("Link URL", 'additional-tags'),
						"description" => __("URL for the link on chat title click", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					/*
					array(
						"param_name" => "content",
						"heading" => __("Chat item content", 'additional-tags'),
						"description" => __("Current chat item content", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					),
					*/
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
				),
				'js_view' => 'VcTrxTextContainerView'
			
			) );
			
			class WPBakeryShortCode_Trx_Chat extends THEMEREX_VC_ShortCodeContainer {}
			
			
			
			
			
			
			// Columns
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_columns",
				"name" => __("Columns", 'additional-tags'),
				"description" => __("Insert columns with margins", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_columns',
				"class" => "trx_sc_columns",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => false,
				"as_parent" => array('only' => 'trx_column_item'),
				"params" => array(
					array(
						"param_name" => "count",
						"heading" => __("Columns count", 'additional-tags'),
						"description" => __("Number of the columns in the container.", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "2",
						"type" => "textfield"
					),
					array(
						"param_name" => "fluid",
						"heading" => __("Fluid columns", 'additional-tags'),
						"description" => __("To squeeze the columns when reducing the size of the window (fluid=yes) or to rebuild them (fluid=no)", 'additional-tags'),
						"class" => "",
						"value" => array(__('Fluid columns', 'additional-tags') => 'yes'),
						"type" => "checkbox"
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
				),
				'default_content' => '
					[trx_column_item][/trx_column_item]
					[trx_column_item][/trx_column_item]
				',
				'js_view' => 'VcTrxColumnsView'
			) );
			
			
			vc_map( array(
				"base" => "trx_column_item",
				"name" => __("Column", 'additional-tags'),
				"description" => __("Column item", 'additional-tags'),
				"show_settings_on_create" => true,
				"class" => "trx_sc_collection trx_sc_column_item",
				"content_element" => true,
				"is_container" => true,
				'icon' => 'icon_trx_column_item',
				"as_child" => array('only' => 'trx_columns'),
				"as_parent" => array('except' => 'trx_columns'),
				"params" => array(
					array(
						"param_name" => "span",
						"heading" => __("Merge columns", 'additional-tags'),
						"description" => __("Count merged columns from current", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Alignment text in the column", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "color",
						"heading" => __("Fore color", 'additional-tags'),
						"description" => __("Any color for objects in this column", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_color",
						"heading" => __("Background color", 'additional-tags'),
						"description" => __("Any background color for this column", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_image",
						"heading" => __("URL for background image file", 'additional-tags'),
						"description" => __("Select or upload image or write URL from other site for the background", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					/*
					array(
						"param_name" => "content",
						"heading" => __("Column's content", 'additional-tags'),
						"description" => __("Content of the current column", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					),
					*/
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['animation'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
				'js_view' => 'VcTrxColumnItemView'
			) );
			
			class WPBakeryShortCode_Trx_Columns extends THEMEREX_VC_ShortCodeColumns {}
			class WPBakeryShortCode_Trx_Column_Item extends THEMEREX_VC_ShortCodeCollection {}
			
			
			
			
			
			
			
			// Contact form
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_contact_form",
				"name" => __("Contact form", 'additional-tags'),
				"description" => __("Insert contact form", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_contact_form',
				"class" => "trx_sc_collection trx_sc_contact_form",
				"content_element" => true,
				"is_container" => true,
				"as_parent" => array('only' => 'trx_form_item'),
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "custom",
						"heading" => __("Custom", 'additional-tags'),
						"description" => __("Use custom fields or create standard contact form (ignore info from 'Field' tabs)", 'additional-tags'),
						"class" => "",
						"value" => array(__('Create custom form', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "action",
						"heading" => __("Action", 'additional-tags'),
						"description" => __("Contact form action (URL to handle form data). If empty - use internal action", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Select form alignment", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "title",
						"heading" => __("Title", 'additional-tags'),
						"description" => __("Title above contact form", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "description",
						"heading" => __("Description (under the title)", 'additional-tags'),
						"description" => __("Contact form description", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					),
					themerex_vc_width(),
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
			
			
			vc_map( array(
				"base" => "trx_form_item",
				"name" => __("Form item (custom field)", 'additional-tags'),
				"description" => __("Custom field for the contact form", 'additional-tags'),
				"class" => "trx_sc_item trx_sc_form_item",
				'icon' => 'icon_trx_form_item',
				"allowed_container_element" => 'vc_row',
				"show_settings_on_create" => true,
				"content_element" => true,
				"is_container" => false,
				"as_child" => array('only' => 'trx_contact_form'), // Use only|except attributes to limit parent (separate multiple values with comma)
				"params" => array(
					array(
						"param_name" => "type",
						"heading" => __("Type", 'additional-tags'),
						"description" => __("Select type of the custom field", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['field_types']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "name",
						"heading" => __("Name", 'additional-tags'),
						"description" => __("Name of the custom field", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "value",
						"heading" => __("Default value", 'additional-tags'),
						"description" => __("Default value of the custom field", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "label",
						"heading" => __("Label", 'additional-tags'),
						"description" => __("Label for the custom field", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "label_position",
						"heading" => __("Label position", 'additional-tags'),
						"description" => __("Label position relative to the field", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['label_positions']),
						"type" => "dropdown"
					),
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
			
			class WPBakeryShortCode_Trx_Contact_Form extends THEMEREX_VC_ShortCodeCollection {}
			class WPBakeryShortCode_Trx_Form_Item extends THEMEREX_VC_ShortCodeItem {}
			
			
			
			
			
			
			
			// Content block on fullscreen page
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_content",
				"name" => __("Content block", 'additional-tags'),
				"description" => __("Container for main content block (use it only on fullscreen pages)", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_content',
				"class" => "trx_sc_collection trx_sc_content",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"params" => array(
					/*
					array(
						"param_name" => "content",
						"heading" => __("Container content", 'additional-tags'),
						"description" => __("Content for section container", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					),
					*/
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['animation'],
					$THEMEREX_GLOBALS['vc_params']['css'],
					$THEMEREX_GLOBALS['vc_params']['margin_top'],
					$THEMEREX_GLOBALS['vc_params']['margin_bottom']
				)
			) );
			
			class WPBakeryShortCode_Trx_Content extends THEMEREX_VC_ShortCodeCollection {}
			
			
			
			
			
			
			
			// Countdown
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_countdown",
				"name" => __("Countdown", 'additional-tags'),
				"description" => __("Insert countdown object", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_countdown',
				"class" => "trx_sc_single trx_sc_countdown",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "date",
						"heading" => __("Date", 'additional-tags'),
						"description" => __("Upcoming date (format: yyyy-mm-dd)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "time",
						"heading" => __("Time", 'additional-tags'),
						"description" => __("Upcoming time (format: HH:mm:ss)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "style",
						"heading" => __("Style", 'additional-tags'),
						"description" => __("Countdown style", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							__('Style 1', 'additional-tags') => 1,
							__('Style 2', 'additional-tags') => 2
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Align counter to left, center or right", 'additional-tags'),
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
			
			class WPBakeryShortCode_Trx_Countdown extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Dropcaps
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_dropcaps",
				"name" => __("Dropcaps", 'additional-tags'),
				"description" => __("Make first letter of the text as dropcaps", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_dropcaps',
				"class" => "trx_sc_single trx_sc_dropcaps",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => __("Style", 'additional-tags'),
						"description" => __("Dropcaps style", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							__('Style 1', 'additional-tags') => 1,
							__('Style 2', 'additional-tags') => 2,
							__('Style 3', 'additional-tags') => 3,
							__('Style 4', 'additional-tags') => 4
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "content",
						"heading" => __("Paragraph text", 'additional-tags'),
						"description" => __("Paragraph with dropcaps content", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					),
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['animation'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
				'js_view' => 'VcTrxTextView'
			
			) );
			
			class WPBakeryShortCode_Trx_Dropcaps extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Emailer
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_emailer",
				"name" => __("E-mail collector", 'additional-tags'),
				"description" => __("Collect e-mails into specified group", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_emailer',
				"class" => "trx_sc_single trx_sc_emailer",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "group",
						"heading" => __("Group", 'additional-tags'),
						"description" => __("The name of group to collect e-mail address", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "open",
						"heading" => __("Opened", 'additional-tags'),
						"description" => __("Initially open the input field on show object", 'additional-tags'),
						"class" => "",
						"value" => array(__('Initially opened', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Align field to left, center or right", 'additional-tags'),
						"admin_label" => true,
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
			
			class WPBakeryShortCode_Trx_Emailer extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Gap
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_gap",
				"name" => __("Gap", 'additional-tags'),
				"description" => __("Insert gap (fullwidth area) in the post content", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_gap',
				"class" => "trx_sc_collection trx_sc_gap",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => false,
				"params" => array(
					/*
					array(
						"param_name" => "content",
						"heading" => __("Gap content", 'additional-tags'),
						"description" => __("Gap inner content", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					)
					*/
				)
			) );
			
			class WPBakeryShortCode_Trx_Gap extends THEMEREX_VC_ShortCodeCollection {}
			
			
			
			
			
			
			
			// Googlemap
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_googlemap",
				"name" => esc_html__("Google Map", 'additional-tags'),
				"description" => wp_kses_data( __("Google map with custom styles and several markers", 'additional-tags') ),
				"category" => esc_html__('ThemeREX', 'additional-tags'),
				"icon" => 'icon_trx_googlemap',
				"class" => "trx_sc_single trx_sc_googlemap",
				'content_element' => true,
				'is_container' => false,
				'as_child' => array('except' => 'trx_googlemap'),
				"js_view" => 'VcTrxAddonsContainerView',	//'VcColumnView',
				"show_settings_on_create" => true,
				"params" => array(
						array(
							"param_name" => "style",
							"heading" => esc_html__("Style", 'additional-tags'),
							"description" => wp_kses_data( __("Map's custom style", 'additional-tags') ),
							"admin_label" => true,
							'save_always' => true,
							"value" => array_flip($THEMEREX_GLOBALS['sc_params']['googlemap_styles']),
							"std" => "default",
							"type" => "dropdown"
						),
						array(
							"param_name" => "zoom",
							"heading" => esc_html__("Zoom", 'additional-tags'),
							"description" => wp_kses_data( __("Map zoom factor from 1 to 20. If 0 or empty - fit bounds to markers", 'additional-tags') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"value" => "16",
							"type" => "textfield"
						),
						array(
							"param_name" => "center",
							"heading" => esc_html__("Center", 'additional-tags'),
							"description" => wp_kses_data( __("Lat,Lng coordinates of the map's center. If empty - use coordinates of the first marker (or specified address in the field below)", 'additional-tags') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "width",
							"heading" => esc_html__("Width", 'additional-tags'),
							"description" => wp_kses_data( __("Width of the element", 'additional-tags') ),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => '100%',
							"type" => "textfield"
						),
						array(
							"param_name" => "height",
							"heading" => esc_html__("Height", 'additional-tags'),
							"description" => wp_kses_data( __("Height of the element", 'additional-tags') ),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => 350,
							"type" => "textfield"
						),
						array(
							"param_name" => "cluster",
							"heading" => esc_html__("Cluster icon", 'additional-tags'),
							"description" => wp_kses_data( __("Select or upload image for markers clusterer", 'additional-tags') ),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => "",
							"type" => "attach_image"
						),
						array(
							"param_name" => "prevent_scroll",
							"heading" => esc_html__("Prevent scroll", 'additional-tags'),
							"description" => wp_kses_data( __("Disallow scrolling of the map", 'additional-tags') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"std" => 0,
							"value" => array(esc_html__("Prevent scroll", 'additional-tags') => 1 ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "address",
							"heading" => esc_html__("Address", 'additional-tags'),
							"description" => wp_kses_data( __("Specify address in this field if you don't need unique marker, title or latlng coordinates. Otherwise, leave this field empty and fill markers below", 'additional-tags') ),
							"value" => '',
							"type" => "textfield"
						),
						array(
							'type' => 'param_group',
							'param_name' => 'markers',
							'heading' => esc_html__( 'Markers', 'additional-tags'),
							"description" => wp_kses_data( __("Add markers to the map", 'additional-tags') ),
							'value' => urlencode( json_encode( apply_filters('themerex__sc_param_group_value', array(
								array(
									'title' => esc_html__( 'One', 'additional-tags'),
									'description' => '',
									'address' => '',
									'latlng' => '',
									'animation' => 'none',
									'icon' => '',
									'icon_retina' => '',
									'icon_width' => '',
									'icon_height' => '',
								),
							), 'trx_sc_googlemap') ) ),
							'params' => apply_filters('themerex__sc_param_group_params', array(
								array(
									"param_name" => "address",
									"heading" => esc_html__("Address", 'additional-tags'),
									"description" => wp_kses_data( __("Address of this marker", 'additional-tags') ),
									"admin_label" => true,
									"value" => "",
									"type" => "textfield"
								),
								array(
									"param_name" => "latlng",
									"heading" => esc_html__("Latitude and Longitude", 'additional-tags'),
									"description" => wp_kses_data( __("Comma separated coorditanes of the marker (instead Address)", 'additional-tags') ),
									'edit_field_class' => 'vc_col-sm-6',
									"admin_label" => true,
									"value" => "",
									"type" => "textfield"
								),
								array(
									"param_name" => "icon",
									"heading" => esc_html__("Marker image", 'additional-tags'),
									"description" => wp_kses_data( __("Select or upload image of this marker", 'additional-tags') ),
									'edit_field_class' => 'vc_col-sm-6 vc_new_row',
									"value" => "",
									"type" => "attach_image"
								),
								array(
									"param_name" => "icon_width",
									"heading" => esc_html__("Width", 'additional-tags'),
									"description" => wp_kses_data( __("Width of this marker. If empty - use original size", 'additional-tags') ),
									'edit_field_class' => 'vc_col-sm-6 vc_new_row',
									"value" => "",
									"type" => "textfield"
								),
								array(
									"param_name" => "icon_height",
									"heading" => esc_html__("Height", 'additional-tags'),
									"description" => wp_kses_data( __("Height of this marker. If empty - use original size", 'additional-tags') ),
									'edit_field_class' => 'vc_col-sm-6',
									"value" => "",
									"type" => "textfield"
								),
								array(
									"param_name" => "title",
									"heading" => esc_html__("Title", 'additional-tags'),
									"description" => wp_kses_data( __("Title of the marker", 'additional-tags') ),
									"admin_label" => true,
									'edit_field_class' => 'vc_col-sm-6 vc_new_row',
									"value" => "",
									"type" => "textfield"
								),
								array(
									"param_name" => "animation",
									"heading" => esc_html__("Animation", 'additional-tags'),
									"description" => wp_kses_data( __("Marker's animation", 'additional-tags') ),
									'edit_field_class' => 'vc_col-sm-6',
									"value" => array_flip(apply_filters('themerex__filter_sc_googlemap_animations', array(
										'none' => esc_html__('None', 'additional-tags'),
										'drop' => esc_html__('Drop', 'additional-tags'),
										'bounce' => esc_html__('Bounce', 'additional-tags')
									))),
									"std" => "none",
									"type" => "dropdown"
								),
								array(
									"param_name" => "description",
									"heading" => esc_html__("Description", 'additional-tags'),
									"description" => wp_kses_data( __("Description of the marker", 'additional-tags') ),
									"value" => "",
									"type" => "textarea_safe"
								)
							)),
						),
						themerex_vc_width('100%'),
						themerex_vc_height(240),
						$THEMEREX_GLOBALS['vc_params']['margin_top'],
						$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
						$THEMEREX_GLOBALS['vc_params']['margin_left'],
						$THEMEREX_GLOBALS['vc_params']['margin_right'],
						$THEMEREX_GLOBALS['vc_params']['id'],
						$THEMEREX_GLOBALS['vc_params']['class'],
						$THEMEREX_GLOBALS['vc_params']['animation'],
						$THEMEREX_GLOBALS['vc_params']['css']
				)
			));
			
			class WPBakeryShortCode_Trx_Googlemap extends THEMEREX_VC_ShortCodeSingle {}







			// Highlight
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_highlight",
				"name" => __("Highlight text", 'additional-tags'),
				"description" => __("Highlight text with selected color, background color and other styles", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_highlight',
				"class" => "trx_sc_single trx_sc_highlight",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "type",
						"heading" => __("Type", 'additional-tags'),
						"description" => __("Highlight type", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
								__('Custom', 'additional-tags') => 0,
								__('Type 1', 'additional-tags') => 1,
								__('Type 2', 'additional-tags') => 2,
								__('Type 3', 'additional-tags') => 3
							),
						"type" => "dropdown"
					),
					array(
						"param_name" => "color",
						"heading" => __("Text color", 'additional-tags'),
						"description" => __("Color for the highlighted text", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_color",
						"heading" => __("Background color", 'additional-tags'),
						"description" => __("Background color for the highlighted text", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "font_size",
						"heading" => __("Font size", 'additional-tags'),
						"description" => __("Font size for the highlighted text (default - in pixels, allows any CSS units of measure)", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "content",
						"heading" => __("Highlight text", 'additional-tags'),
						"description" => __("Content for highlight", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					),
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
				'js_view' => 'VcTrxTextView'
			) );
			
			class WPBakeryShortCode_Trx_Highlight extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			// Icon
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_icon",
				"name" => __("Icon", 'additional-tags'),
				"description" => __("Insert the icon", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_icon',
				"class" => "trx_sc_single trx_sc_icon",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "icon",
						"heading" => __("Icon", 'additional-tags'),
						"description" => __("Select icon class from Fontello icons set", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
						"type" => "dropdown"
					),
					array(
						"param_name" => "color",
						"heading" => __("Text color", 'additional-tags'),
						"description" => __("Icon's color", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_color",
						"heading" => __("Background color", 'additional-tags'),
						"description" => __("Background color for the icon", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_shape",
						"heading" => __("Background shape", 'additional-tags'),
						"description" => __("Shape of the icon background", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							__('None', 'additional-tags') => 'none',
							__('Round', 'additional-tags') => 'round',
							__('Square', 'additional-tags') => 'square'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "bg_style",
						"heading" => __("Icon's color scheme", 'additional-tags'),
						"description" => __("Select icon's color scheme", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['button_styles']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "font_size",
						"heading" => __("Font size", 'additional-tags'),
						"description" => __("Icon's font size", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "font_weight",
						"heading" => __("Font weight", 'additional-tags'),
						"description" => __("Icon's font weight", 'additional-tags'),
						"class" => "",
						"value" => array(
							__('Default', 'additional-tags') => 'inherit',
							__('Thin (100)', 'additional-tags') => '100',
							__('Light (300)', 'additional-tags') => '300',
							__('Normal (400)', 'additional-tags') => '400',
							__('Bold (700)', 'additional-tags') => '700'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "align",
						"heading" => __("Icon's alignment", 'additional-tags'),
						"description" => __("Align icon to left, center or right", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "link",
						"heading" => __("Link URL", 'additional-tags'),
						"description" => __("Link URL from this icon (if not empty)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					$THEMEREX_GLOBALS['vc_params']['margin_top'],
					$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
					$THEMEREX_GLOBALS['vc_params']['margin_left'],
					$THEMEREX_GLOBALS['vc_params']['margin_right'],
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
			) );
			
			class WPBakeryShortCode_Trx_Icon extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Image
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_image",
				"name" => __("Image", 'additional-tags'),
				"description" => __("Insert image", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_image',
				"class" => "trx_sc_single trx_sc_image",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "url",
						"heading" => __("Select image", 'additional-tags'),
						"description" => __("Select image from library", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "align",
						"heading" => __("Image alignment", 'additional-tags'),
						"description" => __("Align image to left or right side", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['float']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "shape",
						"heading" => __("Image shape", 'additional-tags'),
						"description" => __("Shape of the image: square or round", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							__('Square', 'additional-tags') => 'square',
							__('Round', 'additional-tags') => 'round'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "title",
						"heading" => __("Title", 'additional-tags'),
						"description" => __("Image's title", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "icon",
						"heading" => __("Title's icon", 'additional-tags'),
						"description" => __("Select icon for the title from Fontello icons set", 'additional-tags'),
						"class" => "",
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
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
			
			class WPBakeryShortCode_Trx_Image extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Infobox
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_infobox",
				"name" => __("Infobox", 'additional-tags'),
				"description" => __("Box with info or error message", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_infobox',
				"class" => "trx_sc_container trx_sc_infobox",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => __("Style", 'additional-tags'),
						"description" => __("Infobox style", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
								__('Regular', 'additional-tags') => 'regular',
								__('Info', 'additional-tags') => 'info',
								__('Success', 'additional-tags') => 'success',
								__('Error', 'additional-tags') => 'error',
								__('Result', 'additional-tags') => 'result'
							),
						"type" => "dropdown"
					),
					array(
						"param_name" => "closeable",
						"heading" => __("Closeable", 'additional-tags'),
						"description" => __("Create closeable box (with close button)", 'additional-tags'),
						"class" => "",
						"value" => array(__('Close button', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "icon",
						"heading" => __("Custom icon", 'additional-tags'),
						"description" => __("Select icon for the infobox from Fontello icons set. If empty - use default icon", 'additional-tags'),
						"class" => "",
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
						"type" => "dropdown"
					),
					array(
						"param_name" => "color",
						"heading" => __("Text color", 'additional-tags'),
						"description" => __("Any color for the text and headers", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_color",
						"heading" => __("Background color", 'additional-tags'),
						"description" => __("Any background color for this infobox", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					/*
					array(
						"param_name" => "content",
						"heading" => __("Message text", 'additional-tags'),
						"description" => __("Message for the infobox", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					),
					*/
					$THEMEREX_GLOBALS['vc_params']['margin_top'],
					$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
					$THEMEREX_GLOBALS['vc_params']['margin_left'],
					$THEMEREX_GLOBALS['vc_params']['margin_right'],
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['animation'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
				'js_view' => 'VcTrxTextContainerView'
			) );
			
			class WPBakeryShortCode_Trx_Infobox extends THEMEREX_VC_ShortCodeContainer {}
			
			
			
			
			
			
			
			// Line
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_line",
				"name" => __("Line", 'additional-tags'),
				"description" => __("Insert line (delimiter)", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				"class" => "trx_sc_single trx_sc_line",
				'icon' => 'icon_trx_line',
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => __("Style", 'additional-tags'),
						"description" => __("Line style", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
								__('Solid', 'additional-tags') => 'solid',
								__('Dashed', 'additional-tags') => 'dashed',
								__('Dotted', 'additional-tags') => 'dotted',
								__('Double', 'additional-tags') => 'double',
								__('Shadow', 'additional-tags') => 'shadow'
							),
						"type" => "dropdown"
					),
					array(
						"param_name" => "color",
						"heading" => __("Line color", 'additional-tags'),
						"description" => __("Line color", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
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
			
			class WPBakeryShortCode_Trx_Line extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// List
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_list",
				"name" => __("List", 'additional-tags'),
				"description" => __("List items with specific bullets", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				"class" => "trx_sc_collection trx_sc_list",
				'icon' => 'icon_trx_list',
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => false,
				"as_parent" => array('only' => 'trx_list_item'),
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => __("Bullet's style", 'additional-tags'),
						"description" => __("Bullet's style for each list item", 'additional-tags'),
						"class" => "",
						"admin_label" => true,
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['list_styles']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "color",
						"heading" => __("Color", 'additional-tags'),
						"description" => __("List items color", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "icon",
						"heading" => __("List icon", 'additional-tags'),
						"description" => __("Select list icon from Fontello icons set (only for style=Iconed)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						'dependency' => array(
							'element' => 'style',
							'value' => array('iconed')
						),
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
						"type" => "dropdown"
					),
					array(
						"param_name" => "icon_color",
						"heading" => __("Icon color", 'additional-tags'),
						"description" => __("List icons color", 'additional-tags'),
						"class" => "",
						'dependency' => array(
							'element' => 'style',
							'value' => array('iconed')
						),
						"value" => "",
						"type" => "colorpicker"
					),
					$THEMEREX_GLOBALS['vc_params']['margin_top'],
					$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
					$THEMEREX_GLOBALS['vc_params']['margin_left'],
					$THEMEREX_GLOBALS['vc_params']['margin_right'],
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['animation'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
				'default_content' => '
					[trx_list_item]' . __( 'Item 1', 'additional-tags') . '[/trx_list_item]
					[trx_list_item]' . __( 'Item 2', 'additional-tags') . '[/trx_list_item]
				'
			) );
			
			
			vc_map( array(
				"base" => "trx_list_item",
				"name" => __("List item", 'additional-tags'),
				"description" => __("List item with specific bullet", 'additional-tags'),
				"class" => "trx_sc_container trx_sc_list_item",
				"show_settings_on_create" => true,
				"content_element" => true,
				"is_container" => true,
				'icon' => 'icon_trx_list_item',
				"as_child" => array('only' => 'trx_list'), // Use only|except attributes to limit parent (separate multiple values with comma)
				"as_parent" => array('except' => 'trx_list'),
				"params" => array(
					array(
						"param_name" => "title",
						"heading" => __("List item title", 'additional-tags'),
						"description" => __("Title for the current list item (show it as tooltip)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "link",
						"heading" => __("Link URL", 'additional-tags'),
						"description" => __("Link URL for the current list item", 'additional-tags'),
						"admin_label" => true,
						"group" => __('Link', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "target",
						"heading" => __("Link target", 'additional-tags'),
						"description" => __("Link target for the current list item", 'additional-tags'),
						"admin_label" => true,
						"group" => __('Link', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "color",
						"heading" => __("Color", 'additional-tags'),
						"description" => __("Text color for this item", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "icon",
						"heading" => __("List item icon", 'additional-tags'),
						"description" => __("Select list item icon from Fontello icons set (only for style=Iconed)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
						"type" => "dropdown"
					),
					array(
						"param_name" => "icon_color",
						"heading" => __("Icon color", 'additional-tags'),
						"description" => __("Icon color for this item", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "content",
						"heading" => __("List item text", 'additional-tags'),
						"description" => __("Current list item content", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					),
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['css']
				)
			
			) );
			
			class WPBakeryShortCode_Trx_List extends THEMEREX_VC_ShortCodeCollection {}
			class WPBakeryShortCode_Trx_List_Item extends THEMEREX_VC_ShortCodeContainer {}
			
			
			
			
			
			
			
			
			
			// Number
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_number",
				"name" => __("Number", 'additional-tags'),
				"description" => __("Insert number or any word as set of separated characters", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				"class" => "trx_sc_single trx_sc_number",
				'icon' => 'icon_trx_number',
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "value",
						"heading" => __("Value", 'additional-tags'),
						"description" => __("Number or any word to separate", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Select block alignment", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
						"type" => "dropdown"
					),
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
			
			class WPBakeryShortCode_Trx_Number extends THEMEREX_VC_ShortCodeSingle {}


			
			
			
			
			
			// Parallax
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_parallax",
				"name" => __("Parallax", 'additional-tags'),
				"description" => __("Create the parallax container (with asinc background image)", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_parallax',
				"class" => "trx_sc_collection trx_sc_parallax",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "gap",
						"heading" => __("Create gap", 'additional-tags'),
						"description" => __("Create gap around parallax container (not need in fullscreen pages)", 'additional-tags'),
						"class" => "",
						"value" => array(__('Create gap', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "dir",
						"heading" => __("Direction", 'additional-tags'),
						"description" => __("Scroll direction for the parallax background", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
								__('Up', 'additional-tags') => 'up',
								__('Down', 'additional-tags') => 'down'
							),
						"type" => "dropdown"
					),
					array(
						"param_name" => "speed",
						"heading" => __("Speed", 'additional-tags'),
						"description" => __("Parallax background motion speed (from 0.0 to 1.0)", 'additional-tags'),
						"class" => "",
						"value" => "0.3",
						"type" => "textfield"
					),
					array(
						"param_name" => "color",
						"heading" => __("Text color", 'additional-tags'),
						"description" => __("Select color for text object inside parallax block", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_tint",
						"heading" => __("Bg tint", 'additional-tags'),
						"description" => __("Select tint of the parallax background (for correct font color choise)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
								__('Light', 'additional-tags') => 'light',
								__('Dark', 'additional-tags') => 'dark'
							),
						"type" => "dropdown"
					),
					array(
						"param_name" => "bg_color",
						"heading" => __("Backgroud color", 'additional-tags'),
						"description" => __("Select color for parallax background", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_image",
						"heading" => __("Background image", 'additional-tags'),
						"description" => __("Select or upload image or write URL from other site for the parallax background", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "bg_image_x",
						"heading" => __("Image X position", 'additional-tags'),
						"description" => __("Parallax background X position (in percents)", 'additional-tags'),
						"class" => "",
						"value" => "50%",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_video",
						"heading" => __("Video background", 'additional-tags'),
						"description" => __("Paste URL for video file to show it as parallax background", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_video_ratio",
						"heading" => __("Video ratio", 'additional-tags'),
						"description" => __("Specify ratio of the video background. For example: 16:9 (default), 4:3, etc.", 'additional-tags'),
						"class" => "",
						"value" => "16:9",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_overlay",
						"heading" => __("Overlay", 'additional-tags'),
						"description" => __("Overlay color opacity (from 0.0 to 1.0)", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_texture",
						"heading" => __("Texture", 'additional-tags'),
						"description" => __("Texture style from 1 to 11. Empty or 0 - without texture.", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					/*
					array(
						"param_name" => "content",
						"heading" => __("Content", 'additional-tags'),
						"description" => __("Content for the parallax container", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					),
					*/
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
			
			class WPBakeryShortCode_Trx_Parallax extends THEMEREX_VC_ShortCodeCollection {}
			
			
			
			
			
			
			// Popup
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_popup",
				"name" => __("Popup window", 'additional-tags'),
				"description" => __("Container for any html-block with desired class and style for popup window", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_popup',
				"class" => "trx_sc_collection trx_sc_popup",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"params" => array(
					/*
					array(
						"param_name" => "content",
						"heading" => __("Container content", 'additional-tags'),
						"description" => __("Content for popup container", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					),
					*/
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['css'],
					$THEMEREX_GLOBALS['vc_params']['margin_top'],
					$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
					$THEMEREX_GLOBALS['vc_params']['margin_left'],
					$THEMEREX_GLOBALS['vc_params']['margin_right']
				)
			) );
			
			class WPBakeryShortCode_Trx_Popup extends THEMEREX_VC_ShortCodeCollection {}
			
			
			
			
			
			
			
			// Price
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_price",
				"name" => __("Price", 'additional-tags'),
				"description" => __("Insert price with decoration", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_price',
				"class" => "trx_sc_single trx_sc_price",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "money",
						"heading" => __("Money", 'additional-tags'),
						"description" => __("Money value (dot or comma separated)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "currency",
						"heading" => __("Currency symbol", 'additional-tags'),
						"description" => __("Currency character", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "$",
						"type" => "textfield"
					),
					array(
						"param_name" => "period",
						"heading" => __("Period", 'additional-tags'),
						"description" => __("Period text (if need). For example: monthly, daily, etc.", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Align price to left or right side", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['float']),
						"type" => "dropdown"
					),
					$THEMEREX_GLOBALS['vc_params']['margin_top'],
					$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
					$THEMEREX_GLOBALS['vc_params']['margin_left'],
					$THEMEREX_GLOBALS['vc_params']['margin_right'],
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['css']
				)
			) );
			
			class WPBakeryShortCode_Trx_Price extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Price block
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_price_block",
				"name" => __("Price block", 'additional-tags'),
				"description" => __("Insert price block with title, price and description", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_price_block',
				"class" => "trx_sc_single trx_sc_price_block",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "title",
						"heading" => __("Title", 'additional-tags'),
						"description" => __("Block title", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "link",
						"heading" => __("Link URL", 'additional-tags'),
						"description" => __("URL for link from button (at bottom of the block)", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "link_text",
						"heading" => __("Link text", 'additional-tags'),
						"description" => __("Text (caption) for the link button (at bottom of the block). If empty - button not showed", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "target",
						"heading" => __("Link target", 'additional-tags'),
						"description" => __("Target for link on button click", 'additional-tags'),
						"value" => array(__('Open in new window', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),


					array(
						"param_name" => "icon",
						"heading" => __("Icon", 'additional-tags'),
						"description" => __("Select icon from Fontello icons set (placed before/instead price)", 'additional-tags'),
						"class" => "",
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
						"type" => "dropdown"
					),
					array(
						"param_name" => "money",
						"heading" => __("Money", 'additional-tags'),
						"description" => __("Money value (dot or comma separated)", 'additional-tags'),
						"admin_label" => true,
						"group" => __('Money', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "currency",
						"heading" => __("Currency symbol", 'additional-tags'),
						"description" => __("Currency character", 'additional-tags'),
						"admin_label" => true,
						"group" => __('Money', 'additional-tags'),
						"class" => "",
						"value" => "$",
						"type" => "textfield"
					),
					array(
						"param_name" => "period",
						"heading" => __("Period", 'additional-tags'),
						"description" => __("Period text (if need). For example: monthly, daily, etc.", 'additional-tags'),
						"admin_label" => true,
						"group" => __('Money', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Align price to left or right side", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['float']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "content",
						"heading" => __("Description", 'additional-tags'),
						"description" => __("Description for this price block", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
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
				),
				'js_view' => 'VcTrxTextView'
			) );
			
			class WPBakeryShortCode_Trx_PriceBlock extends THEMEREX_VC_ShortCodeSingle {}

			
			
			
			
			// Quote
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_quote",
				"name" => __("Quote", 'additional-tags'),
				"description" => __("Quote text", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_quote',
				"class" => "trx_sc_single trx_sc_quote",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "cite",
						"heading" => __("Quote cite", 'additional-tags'),
						"description" => __("URL for the quote cite link", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "title",
						"heading" => __("Title (author)", 'additional-tags'),
						"description" => __("Quote title (author name)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "content",
						"heading" => __("Quote content", 'additional-tags'),
						"description" => __("Quote content", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					),
					themerex_vc_width(),
					$THEMEREX_GLOBALS['vc_params']['margin_top'],
					$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
					$THEMEREX_GLOBALS['vc_params']['margin_left'],
					$THEMEREX_GLOBALS['vc_params']['margin_right'],
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['animation'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
				'js_view' => 'VcTrxTextView'
			) );
			
			class WPBakeryShortCode_Trx_Quote extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Reviews
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_reviews",
				"name" => __("Reviews", 'additional-tags'),
				"description" => __("Insert reviews block in the single post", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_reviews',
				"class" => "trx_sc_single trx_sc_reviews",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Align counter to left, center or right", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
						"type" => "dropdown"
					),
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
			
			class WPBakeryShortCode_Trx_Reviews extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Search
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_search",
				"name" => __("Search form", 'additional-tags'),
				"description" => __("Insert search form", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_search',
				"class" => "trx_sc_single trx_sc_search",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => __("Style", 'additional-tags'),
						"description" => __("Select style to display search field", 'additional-tags'),
						"class" => "",
						"value" => array(
							__('Regular', 'additional-tags') => "regular",
							__('Flat', 'additional-tags') => "flat"
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "title",
						"heading" => __("Title", 'additional-tags'),
						"description" => __("Title (placeholder) for the search field", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => __("Search &hellip;", 'additional-tags'),
						"type" => "textfield"
					),
					array(
						"param_name" => "ajax",
						"heading" => __("AJAX", 'additional-tags'),
						"description" => __("Search via AJAX or reload page", 'additional-tags'),
						"class" => "",
						"value" => array(__('Use AJAX search', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
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
			
			class WPBakeryShortCode_Trx_Search extends THEMEREX_VC_ShortCodeSingle {}






            $list_post_types = get_post_types();

            // hide some of post types
            unset($list_post_types['nav_menu_item']);
            unset($list_post_types['custom_css']);
            unset($list_post_types['customize_changeset']);


			// Custom Search
			//-------------------------------------------------------------------------------------

			vc_map( array(
				"base" => "trx_custom_search",
				"name" => __("Custom search form", 'additional-tags'),
				"description" => __("Insert search form", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_search',
				"class" => "trx_sc_single trx_sc_search",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => __("Style", 'additional-tags'),
						"description" => __("Select style to display search field", 'additional-tags'),
						"post_type" => $list_post_types,
						"class" => "",
						"value" => array(
							__('Regular', 'additional-tags') => "regular",
							__('Flat', 'additional-tags') => "flat"
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "title",
						"heading" => __("Title", 'additional-tags'),
						"description" => __("Title (placeholder) for the search field", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => __("Search", 'additional-tags'),
						"type" => "textfield"
					),
//					array(
//						"param_name" => "ajax",
//						"heading" => __("AJAX", 'additional-tags'),
//						"description" => __("Search via AJAX or reload page", 'additional-tags'),
//						"class" => "",
//						"value" => array(__('Use AJAX search', 'additional-tags') => 'yes'),
//						"type" => "checkbox"
//					),
					array(
                        "param_name" => "post_type",
                        "heading" => __("Post type", 'additional-tags'),
                        "description" => __("Search by post type", 'additional-tags'),
                        "value" => $list_post_types,
                        "type" => "dropdown"
                    ),
                    array(
                        "param_name" => "use_tags",
                        "heading" => __("Tags (if available for post type specified above)", 'additional-tags'),
                        "description" => __("Search by tags", 'additional-tags'),
                        "class" => "",
                        "value" => array(__('Use tags', 'additional-tags') => 'yes'),
                        "type" => "checkbox"
                    ),
                    array(
                        "param_name" => "tags_title",
                        "heading" => __("Tags title", 'additional-tags'),
                        "value" => __('Tags', 'additional-tags'),
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "use_categories",
                        "heading" => __("Categories (if available for post type specified above)", 'additional-tags'),
                        "description" => __("Search by categories", 'additional-tags'),
                        "class" => "",
                        "value" => array(__('Use categories', 'additional-tags') => 'yes'),
                        "type" => "checkbox"
                    ),
                    array(
                        "param_name" => "categories_title",
                        "heading" => __("Categories title", 'additional-tags'),
                        "value" => __('Categories', 'additional-tags'),
                        "type" => "textfield"
                    ),
                    array(
                        "param_name" => "hide_empty_tax",
						"heading" => __("Hide empty term", 'additional-tags'),
						"description" => __("Hide taxonomy without posts.", 'additional-tags'),
						"class" => "",
						"value" => array(__('Hide empty', 'additional-tags') => 'yes'),
						"type" => "checkbox"
                    ),
                    array(
                        "param_name" => "button",
                        "heading" => __("Button text", 'additional-tags'),
                        "value" => __('Search', 'additional-tags'),
                        "type" => "textfield"
                    ),
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

			class WPBakeryShortCode_Trx_CustomSearch extends THEMEREX_VC_ShortCodeSingle {}





			
			
			// Section
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_section",
				"name" => __("Section container", 'additional-tags'),
				"description" => __("Container for any block ([block] analog - to enable nesting)", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				"class" => "trx_sc_collection trx_sc_section",
				'icon' => 'icon_trx_block',
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "dedicated",
						"heading" => __("Dedicated", 'additional-tags'),
						"description" => __("Use this block as dedicated content - show it before post title on single page", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(__('Use as dedicated content', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Select block alignment", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "columns",
						"heading" => __("Columns emulation", 'additional-tags'),
						"description" => __("Select width for columns emulation", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['columns']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "pan",
						"heading" => __("Use pan effect", 'additional-tags'),
						"description" => __("Use pan effect to show section content", 'additional-tags'),
						"group" => __('Scroll', 'additional-tags'),
						"class" => "",
						"value" => array(__('Content scroller', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "scroll",
						"heading" => __("Use scroller", 'additional-tags'),
						"description" => __("Use scroller to show section content", 'additional-tags'),
						"group" => __('Scroll', 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(__('Content scroller', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "scroll_dir",
						"heading" => __("Scroll and Pan direction", 'additional-tags'),
						"description" => __("Scroll and Pan direction (if Use scroller = yes or Pan = yes)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"group" => __('Scroll', 'additional-tags'),
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['dir']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "scroll_controls",
						"heading" => __("Scroll controls", 'additional-tags'),
						"description" => __("Show scroll controls (if Use scroller = yes)", 'additional-tags'),
						"class" => "",
						"group" => __('Scroll', 'additional-tags'),
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['dir']),
						'dependency' => array(
							'element' => 'scroll',
							'not_empty' => true
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "color",
						"heading" => __("Fore color", 'additional-tags'),
						"description" => __("Any color for objects in this section", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_tint",
						"heading" => __("Background tint", 'additional-tags'),
						"description" => __("Main background tint: dark or light", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['tint']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "bg_color",
						"heading" => __("Background color", 'additional-tags'),
						"description" => __("Any background color for this section", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_image",
						"heading" => __("Background image URL", 'additional-tags'),
						"description" => __("Select background image from library for this section", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "bg_overlay",
						"heading" => __("Overlay", 'additional-tags'),
						"description" => __("Overlay color opacity (from 0.0 to 1.0)", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_texture",
						"heading" => __("Texture", 'additional-tags'),
						"description" => __("Texture style from 1 to 11. Empty or 0 - without texture.", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "font_size",
						"heading" => __("Font size", 'additional-tags'),
						"description" => __("Font size of the text (default - in pixels, allows any CSS units of measure)", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "font_weight",
						"heading" => __("Font weight", 'additional-tags'),
						"description" => __("Font weight of the text", 'additional-tags'),
						"class" => "",
						"value" => array(
							__('Default', 'additional-tags') => 'inherit',
							__('Thin (100)', 'additional-tags') => '100',
							__('Light (300)', 'additional-tags') => '300',
							__('Normal (400)', 'additional-tags') => '400',
							__('Bold (700)', 'additional-tags') => '700'
						),
						"type" => "dropdown"
					),
					/*
					array(
						"param_name" => "content",
						"heading" => __("Container content", 'additional-tags'),
						"description" => __("Content for section container", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					),
					*/
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
			
			class WPBakeryShortCode_Trx_Section extends THEMEREX_VC_ShortCodeCollection {}
			
			
			
			
			
			
			
			// Skills
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_skills",
				"name" => __("Skills", 'additional-tags'),
				"description" => __("Insert skills diagramm", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_skills',
				"class" => "trx_sc_collection trx_sc_skills",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"as_parent" => array('only' => 'trx_skills_item'),
				"params" => array(
					array(
						"param_name" => "max_value",
						"heading" => __("Max value", 'additional-tags'),
						"description" => __("Max value for skills items", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "100",
						"type" => "textfield"
					),
					array(
						"param_name" => "type",
						"heading" => __("Skills type", 'additional-tags'),
						"description" => __("Select type of skills block", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							__('Bar', 'additional-tags') => 'bar',
							__('Pie chart', 'additional-tags') => 'pie',
							__('Counter', 'additional-tags') => 'counter',
							__('Arc', 'additional-tags') => 'arc'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "layout",
						"heading" => __("Skills layout", 'additional-tags'),
						"description" => __("Select layout of skills block", 'additional-tags'),
						"admin_label" => true,
						'dependency' => array(
							'element' => 'type',
							'value' => array('counter','bar','pie')
						),
						"class" => "",
						"value" => array(
							__('Rows', 'additional-tags') => 'rows',
							__('Columns', 'additional-tags') => 'columns'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "dir",
						"heading" => __("Direction", 'additional-tags'),
						"description" => __("Select direction of skills block", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['dir']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "style",
						"heading" => __("Counters style", 'additional-tags'),
						"description" => __("Select style of skills items (only for type=counter)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							__('Style 1', 'additional-tags') => '1',
							__('Style 2', 'additional-tags') => '2',
							__('Style 3', 'additional-tags') => '3',
							__('Style 4', 'additional-tags') => '4'
						),
						'dependency' => array(
							'element' => 'type',
							'value' => array('counter')
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "columns",
						"heading" => __("Columns count", 'additional-tags'),
						"description" => __("Skills columns count (required)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "2",
						"type" => "textfield"
					),
					array(
						"param_name" => "color",
						"heading" => __("Color", 'additional-tags'),
						"description" => __("Color for all skills items", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_color",
						"heading" => __("Background color", 'additional-tags'),
						"description" => __("Background color for all skills items (only for type=pie)", 'additional-tags'),
						'dependency' => array(
							'element' => 'type',
							'value' => array('pie')
						),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "border_color",
						"heading" => __("Border color", 'additional-tags'),
						"description" => __("Border color for all skills items (only for type=pie)", 'additional-tags'),
						'dependency' => array(
							'element' => 'type',
							'value' => array('pie')
						),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "title",
						"heading" => __("Title", 'additional-tags'),
						"description" => __("Title of the skills block", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "subtitle",
						"heading" => __("Subtitle", 'additional-tags'),
						"description" => __("Default subtitle of the skills block (only if type=arc)", 'additional-tags'),
						'dependency' => array(
							'element' => 'type',
							'value' => array('arc')
						),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Align skills block to left or right side", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['float']),
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
			
			
			vc_map( array(
				"base" => "trx_skills_item",
				"name" => __("Skill", 'additional-tags'),
				"description" => __("Skills item", 'additional-tags'),
				"show_settings_on_create" => true,
				"class" => "trx_sc_single trx_sc_skills_item",
				"content_element" => true,
				"is_container" => false,
				"as_child" => array('only' => 'trx_skills'),
				"as_parent" => array('except' => 'trx_skills'),
				"params" => array(
					array(
						"param_name" => "title",
						"heading" => __("Title", 'additional-tags'),
						"description" => __("Title for the current skills item", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "value",
						"heading" => __("Value", 'additional-tags'),
						"description" => __("Value for the current skills item", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "50",
						"type" => "textfield"
					),
					array(
						"param_name" => "color",
						"heading" => __("Color", 'additional-tags'),
						"description" => __("Color for current skills item", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_color",
						"heading" => __("Background color", 'additional-tags'),
						"description" => __("Background color for current skills item (only for type=pie)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "border_color",
						"heading" => __("Border color", 'additional-tags'),
						"description" => __("Border color for current skills item (only for type=pie)", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "style",
						"heading" => __("Item style", 'additional-tags'),
						"description" => __("Select style for the current skills item (only for type=counter)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							__('Style 1', 'additional-tags') => '1',
							__('Style 2', 'additional-tags') => '2',
							__('Style 3', 'additional-tags') => '3',
							__('Style 4', 'additional-tags') => '4'
						),
						"type" => "dropdown"
					),
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['css']
				)
			) );
			
			class WPBakeryShortCode_Trx_Skills extends THEMEREX_VC_ShortCodeCollection {}
			class WPBakeryShortCode_Trx_Skills_Item extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Slider
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_slider",
				"name" => __("Slider", 'additional-tags'),
				"description" => __("Insert slider", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_slider',
				"class" => "trx_sc_collection trx_sc_slider",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"as_parent" => array('only' => 'trx_slider_item'),
				"params" => array_merge(array(
					array(
						"param_name" => "engine",
						"heading" => __("Engine", 'additional-tags'),
						"description" => __("Select engine for slider. Attention! Swiper is built-in engine, all other engines appears only if corresponding plugings are installed", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['sliders']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "align",
						"heading" => __("Float slider", 'additional-tags'),
						"description" => __("Float slider to left or right side", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['float']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "custom",
						"heading" => __("Custom slides", 'additional-tags'),
						"description" => __("Make custom slides from inner shortcodes (prepare it on tabs) or prepare slides from posts thumbnails", 'additional-tags'),
						"class" => "",
						"value" => array(__('Custom slides', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					)
					),
					themerex_exists_revslider() || themerex_exists_royalslider() ? array(
					array(
						"param_name" => "alias",
						"heading" => __("Revolution slider alias or Royal Slider ID", 'additional-tags'),
						"description" => __("Alias for Revolution slider or Royal slider ID", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						'dependency' => array(
							'element' => 'engine',
							'value' => array('revo','royal')
						),
						"value" => "",
						"type" => "textfield"
					)) : array(), array(
					array(
						"param_name" => "cat",
						"heading" => __("Categories list", 'additional-tags'),
						"description" => __("Select category. If empty - show posts from any category or from IDs list", 'additional-tags'),
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"class" => "",
						"value" => array_flip(themerex_array_merge(array(0 => __('- Select category -', 'additional-tags')), $THEMEREX_GLOBALS['sc_params']['categories'])),
						"type" => "dropdown"
					),
					array(
						"param_name" => "count",
						"heading" => __("Swiper: Number of posts", 'additional-tags'),
						"description" => __("How many posts will be displayed? If used IDs - this parameter ignored.", 'additional-tags'),
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"class" => "",
						"value" => "3",
						"type" => "textfield"
					),
					array(
						"param_name" => "offset",
						"heading" => __("Swiper: Offset before select posts", 'additional-tags'),
						"description" => __("Skip posts before select next part.", 'additional-tags'),
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => __("Swiper: Post sorting", 'additional-tags'),
						"description" => __("Select desired posts sorting method", 'additional-tags'),
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['sorting']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => __("Swiper: Post order", 'additional-tags'),
						"description" => __("Select desired posts order", 'additional-tags'),
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "ids",
						"heading" => __("Swiper: Post IDs list", 'additional-tags'),
						"description" => __("Comma separated list of posts ID. If set - parameters above are ignored!", 'additional-tags'),
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "controls",
						"heading" => __("Swiper: Show slider controls", 'additional-tags'),
						"description" => __("Show arrows inside slider", 'additional-tags'),
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"class" => "",
						"value" => array(__('Show controls', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "pagination",
						"heading" => __("Swiper: Show slider pagination", 'additional-tags'),
						"description" => __("Show bullets or titles to switch slides", 'additional-tags'),
						"group" => __('Details', 'additional-tags'),
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"class" => "",
						"value" => array(
								__('Dots', 'additional-tags') => 'yes', 
								__('Side Titles', 'additional-tags') => 'full',
								__('Over Titles', 'additional-tags') => 'over',
								__('None', 'additional-tags') => 'no'
							),
						"type" => "dropdown"
					),
					array(
						"param_name" => "titles",
						"heading" => __("Swiper: Show titles section", 'additional-tags'),
						"description" => __("Show section with post's title and short post's description", 'additional-tags'),
						"group" => __('Details', 'additional-tags'),
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"class" => "",
						"value" => array(
								__('Not show', 'additional-tags') => "no",
								__('Show/Hide info', 'additional-tags') => "slide",
								__('Fixed info', 'additional-tags') => "fixed"
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "descriptions",
						"heading" => __("Swiper: Post descriptions", 'additional-tags'),
						"description" => __("Show post's excerpt max length (characters)", 'additional-tags'),
						"group" => __('Details', 'additional-tags'),
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "links",
						"heading" => __("Swiper: Post's title as link", 'additional-tags'),
						"description" => __("Make links from post's titles", 'additional-tags'),
						"group" => __('Details', 'additional-tags'),
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"class" => "",
						"value" => array(__('Titles as a links', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "crop",
						"heading" => __("Swiper: Crop images", 'additional-tags'),
						"description" => __("Crop images in each slide or live it unchanged", 'additional-tags'),
						"group" => __('Details', 'additional-tags'),
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"class" => "",
						"value" => array(__('Crop images', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "autoheight",
						"heading" => __("Swiper: Autoheight", 'additional-tags'),
						"description" => __("Change whole slider's height (make it equal current slide's height)", 'additional-tags'),
						"group" => __('Details', 'additional-tags'),
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"class" => "",
						"value" => array(__('Autoheight', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					array(
						"param_name" => "interval",
						"heading" => __("Swiper: Slides change interval", 'additional-tags'),
						"description" => __("Slides change interval (in milliseconds: 1000ms = 1s)", 'additional-tags'),
						"group" => __('Details', 'additional-tags'),
						'dependency' => array(
							'element' => 'engine',
							'value' => array('swiper')
						),
						"class" => "",
						"value" => "5000",
						"type" => "textfield"
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
				))
			) );
			
			
			vc_map( array(
				"base" => "trx_slider_item",
				"name" => __("Slide", 'additional-tags'),
				"description" => __("Slider item - single slide", 'additional-tags'),
				"show_settings_on_create" => true,
				"content_element" => true,
				"is_container" => false,
				'icon' => 'icon_trx_slider_item',
				"as_child" => array('only' => 'trx_slider'),
				"as_parent" => array('except' => 'trx_slider'),
				"params" => array(
					array(
						"param_name" => "src",
						"heading" => __("URL (source) for image file", 'additional-tags'),
						"description" => __("Select or upload image or write URL from other site for the current slide", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['css']
				)
			) );
			
			class WPBakeryShortCode_Trx_Slider extends THEMEREX_VC_ShortCodeCollection {}
			class WPBakeryShortCode_Trx_Slider_Item extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Socials
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_socials",
				"name" => __("Social icons", 'additional-tags'),
				"description" => __("Custom social icons", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_socials',
				"class" => "trx_sc_collection trx_sc_socials",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"as_parent" => array('only' => 'trx_social_item'),
				"params" => array_merge(array(
					array(
						"param_name" => "size",
						"heading" => __("Icon's size", 'additional-tags'),
						"description" => __("Size of the icons", 'additional-tags'),
						"class" => "",
						"value" => array(
							__('Tiny', 'additional-tags') => 'tiny',
							__('Small', 'additional-tags') => 'small',
							__('Large', 'additional-tags') => 'large'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "socials",
						"heading" => __("Manual socials list", 'additional-tags'),
						"description" => __("Custom list of social networks. For example: twitter=http://twitter.com/my_profile|facebook=http://facebooc.com/my_profile. If empty - use socials from Theme options.", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "custom",
						"heading" => __("Custom socials", 'additional-tags'),
						"description" => __("Make custom icons from inner shortcodes (prepare it on tabs)", 'additional-tags'),
						"class" => "",
						"value" => array(__('Custom socials', 'additional-tags') => 'yes'),
						"type" => "checkbox"
					),
					$THEMEREX_GLOBALS['vc_params']['margin_top'],
					$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
					$THEMEREX_GLOBALS['vc_params']['margin_left'],
					$THEMEREX_GLOBALS['vc_params']['margin_right'],
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['animation'],
					$THEMEREX_GLOBALS['vc_params']['css']
				))
			) );
			
			
			vc_map( array(
				"base" => "trx_social_item",
				"name" => __("Custom social item", 'additional-tags'),
				"description" => __("Custom social item: name, profile url and icon url", 'additional-tags'),
				"show_settings_on_create" => true,
				"content_element" => true,
				"is_container" => false,
				'icon' => 'icon_trx_social_item',
				"as_child" => array('only' => 'trx_socials'),
				"as_parent" => array('except' => 'trx_socials'),
				"params" => array(
					array(
						"param_name" => "name",
						"heading" => __("Social name", 'additional-tags'),
						"description" => __("Name (slug) of the social network (twitter, facebook, linkedin, etc.)", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "url",
						"heading" => __("Your profile URL", 'additional-tags'),
						"description" => __("URL of your profile in specified social network", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "icon",
						"heading" => __("URL (source) for icon file", 'additional-tags'),
						"description" => __("Select or upload image or write URL from other site for the current social icon", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					)
				)
			) );
			
			class WPBakeryShortCode_Trx_Socials extends THEMEREX_VC_ShortCodeCollection {}
			class WPBakeryShortCode_Trx_Social_Item extends THEMEREX_VC_ShortCodeSingle {}
			

			
			
			
			
			
			// Table
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_table",
				"name" => __("Table", 'additional-tags'),
				"description" => __("Insert a table", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_table',
				"class" => "trx_sc_container trx_sc_table",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "align",
						"heading" => __("Cells content alignment", 'additional-tags'),
						"description" => __("Select alignment for each table cell", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "content",
						"heading" => __("Table content", 'additional-tags'),
						"description" => __("Content, created with any table-generator", 'additional-tags'),
						"class" => "",
						"value" => "Paste here table content, generated on one of many public internet resources, for example: http://www.impressivewebs.com/html-table-code-generator/ or http://html-tables.com/",
						"type" => "textarea_html"
					),
					themerex_vc_width(),
					$THEMEREX_GLOBALS['vc_params']['margin_top'],
					$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
					$THEMEREX_GLOBALS['vc_params']['margin_left'],
					$THEMEREX_GLOBALS['vc_params']['margin_right'],
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['animation'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
				'js_view' => 'VcTrxTextContainerView'
			) );
			
			class WPBakeryShortCode_Trx_Table extends THEMEREX_VC_ShortCodeContainer {}
			
			
			
			
			
			
			
			// Tabs
			//-------------------------------------------------------------------------------------
			
			$tab_id_1 = 'sc_tab_'.time() . '_1_' . rand( 0, 100 );
			$tab_id_2 = 'sc_tab_'.time() . '_2_' . rand( 0, 100 );
			vc_map( array(
				"base" => "trx_tabs",
				"name" => __("Tabs", 'additional-tags'),
				"description" => __("Tabs", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_tabs',
				"class" => "trx_sc_collection trx_sc_tabs",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => false,
				"as_parent" => array('only' => 'trx_tab'),
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => __("Tabs style", 'additional-tags'),
						"description" => __("Select style of tabs items", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							__('Style 1', 'additional-tags') => '1',
							__('Style 2', 'additional-tags') => '2'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "initial",
						"heading" => __("Initially opened tab", 'additional-tags'),
						"description" => __("Number of initially opened tab", 'additional-tags'),
						"class" => "",
						"value" => 1,
						"type" => "textfield"
					),
					array(
						"param_name" => "scroll",
						"heading" => __("Scroller", 'additional-tags'),
						"description" => __("Use scroller to show tab content (height parameter required)", 'additional-tags'),
						"class" => "",
						"value" => array("Use scroller" => "yes" ),
						"type" => "checkbox"
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
				),
				'default_content' => '
					[trx_tab title="' . __( 'Tab 1', 'additional-tags') . '" tab_id="'.esc_attr($tab_id_1).'"][/trx_tab]
					[trx_tab title="' . __( 'Tab 2', 'additional-tags') . '" tab_id="'.esc_attr($tab_id_2).'"][/trx_tab]
				',
				"custom_markup" => '
					<div class="wpb_tabs_holder wpb_holder vc_container_for_children">
						<ul class="tabs_controls">
						</ul>
						%content%
					</div>
				',
				'js_view' => 'VcTrxTabsView'
			) );
			
			
			vc_map( array(
				"base" => "trx_tab",
				"name" => __("Tab item", 'additional-tags'),
				"description" => __("Single tab item", 'additional-tags'),
				"show_settings_on_create" => true,
				"class" => "trx_sc_collection trx_sc_tab",
				"content_element" => true,
				"is_container" => true,
				'icon' => 'icon_trx_tab',
				"as_child" => array('only' => 'trx_tabs'),
				"as_parent" => array('except' => 'trx_tabs'),
				"params" => array(
					array(
						"param_name" => "title",
						"heading" => __("Tab title", 'additional-tags'),
						"description" => __("Title for current tab", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "tab_id",
						"heading" => __("Tab ID", 'additional-tags'),
						"description" => __("ID for current tab (required). Please, start it from letter.", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
			  'js_view' => 'VcTrxTabView'
			) );
			class WPBakeryShortCode_Trx_Tabs extends THEMEREX_VC_ShortCodeTabs {}
			class WPBakeryShortCode_Trx_Tab extends THEMEREX_VC_ShortCodeTab {}
			
			
			
			
			// Team
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_team",
				"name" => __("Team", 'additional-tags'),
				"description" => __("Insert team members", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_team',
				"class" => "trx_sc_columns trx_sc_team",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"as_parent" => array('only' => 'trx_team_item'),
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => __("Team style", 'additional-tags'),
						"description" => __("Select style to display team members", 'additional-tags'),
						"class" => "",
						"admin_label" => true,
						"value" => array(
							__('Style 1', 'additional-tags') => 1,
							__('Style 2', 'additional-tags') => 2
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "columns",
						"heading" => __("Columns", 'additional-tags'),
						"description" => __("How many columns use to show team members", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "3",
						"type" => "textfield"
					),
					array(
						"param_name" => "custom",
						"heading" => __("Custom", 'additional-tags'),
						"description" => __("Allow get team members from inner shortcodes (custom) or get it from specified group (cat)", 'additional-tags'),
						"class" => "",
						"value" => array("Custom members" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "cat",
						"heading" => __("Categories", 'additional-tags'),
						"description" => __("Put here comma separated categories (ids or slugs) to show team members. If empty - select team members from any category (group) or from IDs list", 'additional-tags'),
						"group" => __('Query', 'additional-tags'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "count",
						"heading" => __("Number of posts", 'additional-tags'),
						"description" => __("How many posts will be displayed? If used IDs - this parameter ignored.", 'additional-tags'),
						"group" => __('Query', 'additional-tags'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "3",
						"type" => "textfield"
					),
					array(
						"param_name" => "offset",
						"heading" => __("Offset before select posts", 'additional-tags'),
						"description" => __("Skip posts before select next part.", 'additional-tags'),
						"group" => __('Query', 'additional-tags'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => __("Post sorting", 'additional-tags'),
						"description" => __("Select desired posts sorting method", 'additional-tags'),
						"group" => __('Query', 'additional-tags'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['sorting']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => __("Post order", 'additional-tags'),
						"description" => __("Select desired posts order", 'additional-tags'),
						"group" => __('Query', 'additional-tags'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "ids",
						"heading" => __("Team member's IDs list", 'additional-tags'),
						"description" => __("Comma separated list of team members's ID. If set - parameters above (category, count, order, etc.)  are ignored!", 'additional-tags'),
						"group" => __('Query', 'additional-tags'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					$THEMEREX_GLOBALS['vc_params']['margin_top'],
					$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
					$THEMEREX_GLOBALS['vc_params']['margin_left'],
					$THEMEREX_GLOBALS['vc_params']['margin_right'],
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['animation'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
				'default_content' => '
					[trx_team_item user="' . __( 'Member 1', 'additional-tags') . '"][/trx_team_item]
					[trx_team_item user="' . __( 'Member 2', 'additional-tags') . '"][/trx_team_item]
				',
				'js_view' => 'VcTrxColumnsView'
			) );
			
			
			vc_map( array(
				"base" => "trx_team_item",
				"name" => __("Team member", 'additional-tags'),
				"description" => __("Team member - all data pull out from it account on your site", 'additional-tags'),
				"show_settings_on_create" => true,
				"class" => "trx_sc_item trx_sc_column_item trx_sc_team_item",
				"content_element" => true,
				"is_container" => false,
				'icon' => 'icon_trx_team_item',
				"as_child" => array('only' => 'trx_team'),
				"as_parent" => array('except' => 'trx_team'),
				"params" => array(
					array(
						"param_name" => "user",
						"heading" => __("Registered user", 'additional-tags'),
						"description" => __("Select one of registered users (if present) or put name, position, etc. in fields below", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['users']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "member",
						"heading" => __("Team member", 'additional-tags'),
						"description" => __("Select one of team members (if present) or put name, position, etc. in fields below", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['members']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "link",
						"heading" => __("Link", 'additional-tags'),
						"description" => __("Link on team member's personal page", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "name",
						"heading" => __("Name", 'additional-tags'),
						"description" => __("Team member's name", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "position",
						"heading" => __("Position", 'additional-tags'),
						"description" => __("Team member's position", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "email",
						"heading" => __("E-mail", 'additional-tags'),
						"description" => __("Team member's e-mail", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "photo",
						"heading" => __("Member's Photo", 'additional-tags'),
						"description" => __("Team member's photo (avatar", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "socials",
						"heading" => __("Socials", 'additional-tags'),
						"description" => __("Team member's socials icons: name=url|name=url... For example: facebook=http://facebook.com/myaccount|twitter=http://twitter.com/myaccount", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['animation'],
					$THEMEREX_GLOBALS['vc_params']['css']
				)
			) );
			
			class WPBakeryShortCode_Trx_Team extends THEMEREX_VC_ShortCodeColumns {}
			class WPBakeryShortCode_Trx_Team_Item extends THEMEREX_VC_ShortCodeItem {}
			
			
			
			
			
			
			
			// Testimonials
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_testimonials",
				"name" => __("Testimonials", 'additional-tags'),
				"description" => __("Insert testimonials slider", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_testimonials',
				"class" => "trx_sc_collection trx_sc_testimonials",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => true,
				"as_parent" => array('only' => 'trx_testimonials_item'),
				"params" => array(
					array(
						"param_name" => "controls",
						"heading" => __("Show arrows", 'additional-tags'),
						"description" => __("Show control buttons", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['yes_no']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "interval",
						"heading" => __("Testimonials change interval", 'additional-tags'),
						"description" => __("Testimonials change interval (in milliseconds: 1000ms = 1s)", 'additional-tags'),
						"class" => "",
						"value" => "7000",
						"type" => "textfield"
					),
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Alignment of the testimonials block", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "autoheight",
						"heading" => __("Autoheight", 'additional-tags'),
						"description" => __("Change whole slider's height (make it equal current slide's height)", 'additional-tags'),
						"class" => "",
						"value" => array("Autoheight" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "custom",
						"heading" => __("Custom", 'additional-tags'),
						"description" => __("Allow get testimonials from inner shortcodes (custom) or get it from specified group (cat)", 'additional-tags'),
						"class" => "",
						"value" => array("Custom slides" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "cat",
						"heading" => __("Categories", 'additional-tags'),
						"description" => __("Select categories (groups) to show testimonials. If empty - select testimonials from any category (group) or from IDs list", 'additional-tags'),
						"group" => __('Query', 'additional-tags'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "count",
						"heading" => __("Number of posts", 'additional-tags'),
						"description" => __("How many posts will be displayed? If used IDs - this parameter ignored.", 'additional-tags'),
						"group" => __('Query', 'additional-tags'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "3",
						"type" => "textfield"
					),
					array(
						"param_name" => "offset",
						"heading" => __("Offset before select posts", 'additional-tags'),
						"description" => __("Skip posts before select next part.", 'additional-tags'),
						"group" => __('Query', 'additional-tags'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "0",
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => __("Post sorting", 'additional-tags'),
						"description" => __("Select desired posts sorting method", 'additional-tags'),
						"group" => __('Query', 'additional-tags'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['sorting']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => __("Post order", 'additional-tags'),
						"description" => __("Select desired posts order", 'additional-tags'),
						"group" => __('Query', 'additional-tags'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "ids",
						"heading" => __("Post IDs list", 'additional-tags'),
						"description" => __("Comma separated list of posts ID. If set - parameters above are ignored!", 'additional-tags'),
						"group" => __('Query', 'additional-tags'),
						'dependency' => array(
							'element' => 'custom',
							'is_empty' => true
						),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_tint",
						"heading" => __("Background tint", 'additional-tags'),
						"description" => __("Main background tint: dark or light", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['tint']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "bg_color",
						"heading" => __("Background color", 'additional-tags'),
						"description" => __("Any background color for this section", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_image",
						"heading" => __("Background image URL", 'additional-tags'),
						"description" => __("Select background image from library for this section", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "bg_overlay",
						"heading" => __("Overlay", 'additional-tags'),
						"description" => __("Overlay color opacity (from 0.0 to 1.0)", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_texture",
						"heading" => __("Texture", 'additional-tags'),
						"description" => __("Texture style from 1 to 11. Empty or 0 - without texture.", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
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
			
			
			vc_map( array(
				"base" => "trx_testimonials_item",
				"name" => __("Testimonial", 'additional-tags'),
				"description" => __("Single testimonials item", 'additional-tags'),
				"show_settings_on_create" => true,
				"class" => "trx_sc_single trx_sc_testimonials_item",
				"content_element" => true,
				"is_container" => false,
				'icon' => 'icon_trx_testimonials_item',
				"as_child" => array('only' => 'trx_testimonials'),
				"as_parent" => array('except' => 'trx_testimonials'),
				"params" => array(
					array(
						"param_name" => "author",
						"heading" => __("Author", 'additional-tags'),
						"description" => __("Name of the testimonmials author", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "link",
						"heading" => __("Link", 'additional-tags'),
						"description" => __("Link URL to the testimonmials author page", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "email",
						"heading" => __("E-mail", 'additional-tags'),
						"description" => __("E-mail of the testimonmials author", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "photo",
						"heading" => __("Photo", 'additional-tags'),
						"description" => __("Select or upload photo of testimonmials author or write URL of photo from other site", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "content",
						"heading" => __("Testimonials text", 'additional-tags'),
						"description" => __("Current testimonials text", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					),
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
				'js_view' => 'VcTrxTextView'
			) );
			
			class WPBakeryShortCode_Trx_Testimonials extends THEMEREX_VC_ShortCodeColumns {}
			class WPBakeryShortCode_Trx_Testimonials_Item extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Title
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_title",
				"name" => __("Title", 'additional-tags'),
				"description" => __("Create header tag (1-6 level) with many styles", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_title',
				"class" => "trx_sc_single trx_sc_title",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "content",
						"heading" => __("Title content", 'additional-tags'),
						"description" => __("Title content", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textarea_html"
					),
					array(
						"param_name" => "type",
						"heading" => __("Title type", 'additional-tags'),
						"description" => __("Title type (header level)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							__('Header 1', 'additional-tags') => '1',
							__('Header 2', 'additional-tags') => '2',
							__('Header 3', 'additional-tags') => '3',
							__('Header 4', 'additional-tags') => '4',
							__('Header 5', 'additional-tags') => '5',
							__('Header 6', 'additional-tags') => '6'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "style",
						"heading" => __("Title style", 'additional-tags'),
						"description" => __("Title style: only text (regular) or with icon/image (iconed)", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							__('Regular', 'additional-tags') => 'regular',
							__('Underline', 'additional-tags') => 'underline',
							__('Divider', 'additional-tags') => 'divider',
							__('With icon (image)', 'additional-tags') => 'iconed'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Title text alignment", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "font_size",
						"heading" => __("Font size", 'additional-tags'),
						"description" => __("Custom font size. If empty - use theme default", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "font_weight",
						"heading" => __("Font weight", 'additional-tags'),
						"description" => __("Custom font weight. If empty or inherit - use theme default", 'additional-tags'),
						"class" => "",
						"value" => array(
							__('Default', 'additional-tags') => 'inherit',
							__('Thin (100)', 'additional-tags') => '100',
							__('Light (300)', 'additional-tags') => '300',
							__('Normal (400)', 'additional-tags') => '400',
							__('Semibold (600)', 'additional-tags') => '600',
							__('Bold (700)', 'additional-tags') => '700',
							__('Black (900)', 'additional-tags') => '900'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "color",
						"heading" => __("Title color", 'additional-tags'),
						"description" => __("Select color for the title", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "icon",
						"heading" => __("Title font icon", 'additional-tags'),
						"description" => __("Select font icon for the title from Fontello icons set (if style=iconed)", 'additional-tags'),
						"class" => "",
						"group" => __('Icon &amp; Image', 'additional-tags'),
						'dependency' => array(
							'element' => 'style',
							'value' => array('iconed')
						),
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
						"type" => "dropdown"
					),
					array(
						"param_name" => "image",
						"heading" => __("or image icon", 'additional-tags'),
						"description" => __("Select image icon for the title instead icon above (if style=iconed)", 'additional-tags'),
						"class" => "",
						"group" => __('Icon &amp; Image', 'additional-tags'),
						'dependency' => array(
							'element' => 'style',
							'value' => array('iconed')
						),
						"value" => $THEMEREX_GLOBALS['sc_params']['images'],
						"type" => "dropdown"
					),
					array(
						"param_name" => "picture",
						"heading" => __("or select uploaded image", 'additional-tags'),
						"description" => __("Select or upload image or write URL from other site (if style=iconed)", 'additional-tags'),
						"group" => __('Icon &amp; Image', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "image_size",
						"heading" => __("Image (picture) size", 'additional-tags'),
						"description" => __("Select image (picture) size (if style=iconed)", 'additional-tags'),
						"group" => __('Icon &amp; Image', 'additional-tags'),
						"class" => "",
						"value" => array(
							__('Small', 'additional-tags') => 'small',
							__('Medium', 'additional-tags') => 'medium',
							__('Large', 'additional-tags') => 'large'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "position",
						"heading" => __("Icon (image) position", 'additional-tags'),
						"description" => __("Select icon (image) position (if style=iconed)", 'additional-tags'),
						"group" => __('Icon &amp; Image', 'additional-tags'),
						"class" => "",
						"value" => array(
							__('Top', 'additional-tags') => 'top',
							__('Left', 'additional-tags') => 'left'
						),
						"type" => "dropdown"
					),
					$THEMEREX_GLOBALS['vc_params']['margin_top'],
					$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
					$THEMEREX_GLOBALS['vc_params']['margin_left'],
					$THEMEREX_GLOBALS['vc_params']['margin_right'],
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['animation'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
				'js_view' => 'VcTrxTextView'
			) );
			
			class WPBakeryShortCode_Trx_Title extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Toggles
			//-------------------------------------------------------------------------------------
				
			vc_map( array(
				"base" => "trx_toggles",
				"name" => __("Toggles", 'additional-tags'),
				"description" => __("Toggles items", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_toggles',
				"class" => "trx_sc_collection trx_sc_toggles",
				"content_element" => true,
				"is_container" => true,
				"show_settings_on_create" => false,
				"as_parent" => array('only' => 'trx_toggles_item'),
				"params" => array(
					array(
						"param_name" => "style",
						"heading" => __("Toggles style", 'additional-tags'),
						"description" => __("Select style for display toggles", 'additional-tags'),
						"class" => "",
						"admin_label" => true,
						"value" => array(
							__('Style 1', 'additional-tags') => 1,
							__('Style 2', 'additional-tags') => 2
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "counter",
						"heading" => __("Counter", 'additional-tags'),
						"description" => __("Display counter before each toggles title", 'additional-tags'),
						"class" => "",
						"value" => array("Add item numbers before each element" => "on" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "icon_closed",
						"heading" => __("Icon while closed", 'additional-tags'),
						"description" => __("Select icon for the closed toggles item from Fontello icons set", 'additional-tags'),
						"class" => "",
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
						"type" => "dropdown"
					),
					array(
						"param_name" => "icon_opened",
						"heading" => __("Icon while opened", 'additional-tags'),
						"description" => __("Select icon for the opened toggles item from Fontello icons set", 'additional-tags'),
						"class" => "",
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
						"type" => "dropdown"
					),
					$THEMEREX_GLOBALS['vc_params']['margin_top'],
					$THEMEREX_GLOBALS['vc_params']['margin_bottom'],
					$THEMEREX_GLOBALS['vc_params']['margin_left'],
					$THEMEREX_GLOBALS['vc_params']['margin_right'],
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class']
				),
				'default_content' => '
					[trx_toggles_item title="' . __( 'Item 1 title', 'additional-tags') . '"][/trx_toggles_item]
					[trx_toggles_item title="' . __( 'Item 2 title', 'additional-tags') . '"][/trx_toggles_item]
				',
				"custom_markup" => '
					<div class="wpb_accordion_holder wpb_holder clearfix vc_container_for_children">
						%content%
					</div>
					<div class="tab_controls">
						<button class="add_tab" title="'.__("Add item", 'additional-tags').'">'.__("Add item", 'additional-tags').'</button>
					</div>
				',
				'js_view' => 'VcTrxTogglesView'
			) );
			
			
			vc_map( array(
				"base" => "trx_toggles_item",
				"name" => __("Toggles item", 'additional-tags'),
				"description" => __("Single toggles item", 'additional-tags'),
				"show_settings_on_create" => true,
				"content_element" => true,
				"is_container" => true,
				'icon' => 'icon_trx_toggles_item',
				"as_child" => array('only' => 'trx_toggles'),
				"as_parent" => array('except' => 'trx_toggles'),
				"params" => array(
					array(
						"param_name" => "title",
						"heading" => __("Title", 'additional-tags'),
						"description" => __("Title for current toggles item", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "open",
						"heading" => __("Open on show", 'additional-tags'),
						"description" => __("Open current toggle item on show", 'additional-tags'),
						"class" => "",
						"value" => array("Opened" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "icon_closed",
						"heading" => __("Icon while closed", 'additional-tags'),
						"description" => __("Select icon for the closed toggles item from Fontello icons set", 'additional-tags'),
						"class" => "",
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
						"type" => "dropdown"
					),
					array(
						"param_name" => "icon_opened",
						"heading" => __("Icon while opened", 'additional-tags'),
						"description" => __("Select icon for the opened toggles item from Fontello icons set", 'additional-tags'),
						"class" => "",
						"value" => $THEMEREX_GLOBALS['sc_params']['icons'],
						"type" => "dropdown"
					),
					$THEMEREX_GLOBALS['vc_params']['id'],
					$THEMEREX_GLOBALS['vc_params']['class'],
					$THEMEREX_GLOBALS['vc_params']['css']
				),
				'js_view' => 'VcTrxTogglesTabView'
			) );
			class WPBakeryShortCode_Trx_Toggles extends THEMEREX_VC_ShortCodeToggles {}
			class WPBakeryShortCode_Trx_Toggles_Item extends THEMEREX_VC_ShortCodeTogglesItem {}
			
			
			
			
			
			
			// Twitter
			//-------------------------------------------------------------------------------------

			vc_map( array(
				"base" => "trx_twitter",
				"name" => __("Twitter", 'additional-tags'),
				"description" => __("Insert twitter feed into post (page)", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_twitter',
				"class" => "trx_sc_single trx_sc_twitter",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "user",
						"heading" => __("Twitter Username", 'additional-tags'),
						"description" => __("Your username in the twitter account. If empty - get it from Theme Options.", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "consumer_key",
						"heading" => __("Consumer Key", 'additional-tags'),
						"description" => __("Consumer Key from the twitter account", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "consumer_secret",
						"heading" => __("Consumer Secret", 'additional-tags'),
						"description" => __("Consumer Secret from the twitter account", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "token_key",
						"heading" => __("Token Key", 'additional-tags'),
						"description" => __("Token Key from the twitter account", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "token_secret",
						"heading" => __("Token Secret", 'additional-tags'),
						"description" => __("Token Secret from the twitter account", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "count",
						"heading" => __("Tweets number", 'additional-tags'),
						"description" => __("Number tweets to show", 'additional-tags'),
						"class" => "",
						"divider" => true,
						"value" => 3,
						"type" => "textfield"
					),
					array(
						"param_name" => "controls",
						"heading" => __("Show arrows", 'additional-tags'),
						"description" => __("Show control buttons", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['yes_no']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "interval",
						"heading" => __("Tweets change interval", 'additional-tags'),
						"description" => __("Tweets change interval (in milliseconds: 1000ms = 1s)", 'additional-tags'),
						"class" => "",
						"value" => "7000",
						"type" => "textfield"
					),
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Alignment of the tweets block", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "autoheight",
						"heading" => __("Autoheight", 'additional-tags'),
						"description" => __("Change whole slider's height (make it equal current slide's height)", 'additional-tags'),
						"class" => "",
						"value" => array("Autoheight" => "yes" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "bg_tint",
						"heading" => __("Background tint", 'additional-tags'),
						"description" => __("Main background tint: dark or light", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['tint']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "bg_color",
						"heading" => __("Background color", 'additional-tags'),
						"description" => __("Any background color for this section", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "colorpicker"
					),
					array(
						"param_name" => "bg_image",
						"heading" => __("Background image URL", 'additional-tags'),
						"description" => __("Select background image from library for this section", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "bg_overlay",
						"heading" => __("Overlay", 'additional-tags'),
						"description" => __("Overlay color opacity (from 0.0 to 1.0)", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_texture",
						"heading" => __("Texture", 'additional-tags'),
						"description" => __("Texture style from 1 to 11. Empty or 0 - without texture.", 'additional-tags'),
						"group" => __('Colors and Images', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
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
				),
			) );
			
			class WPBakeryShortCode_Trx_Twitter extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Video
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_video",
				"name" => __("Video", 'additional-tags'),
				"description" => __("Insert video player", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_video',
				"class" => "trx_sc_single trx_sc_video",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "url",
						"heading" => __("URL for video file", 'additional-tags'),
						"description" => __("Paste URL for video file", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "ratio",
						"heading" => __("Ratio", 'additional-tags'),
						"description" => __("Select ratio for display video", 'additional-tags'),
						"class" => "",
						"value" => array(
							__('16:9', 'additional-tags') => "16:9",
							__('4:3', 'additional-tags') => "4:3"
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "autoplay",
						"heading" => __("Autoplay video", 'additional-tags'),
						"description" => __("Autoplay video on page load", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array("Autoplay" => "on" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Select block alignment", 'additional-tags'),
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['align']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "image",
						"heading" => __("Cover image", 'additional-tags'),
						"description" => __("Select or upload image or write URL from other site for video preview", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "bg_image",
						"heading" => __("Background image", 'additional-tags'),
						"description" => __("Select or upload image or write URL from other site for video background. Attention! If you use background image - specify paddings below from background margins to video block in percents!", 'additional-tags'),
						"group" => __('Background', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "bg_top",
						"heading" => __("Top offset", 'additional-tags'),
						"description" => __("Top offset (padding) from background image to video block (in percent). For example: 3%", 'additional-tags'),
						"group" => __('Background', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_bottom",
						"heading" => __("Bottom offset", 'additional-tags'),
						"description" => __("Bottom offset (padding) from background image to video block (in percent). For example: 3%", 'additional-tags'),
						"group" => __('Background', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_left",
						"heading" => __("Left offset", 'additional-tags'),
						"description" => __("Left offset (padding) from background image to video block (in percent). For example: 20%", 'additional-tags'),
						"group" => __('Background', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_right",
						"heading" => __("Right offset", 'additional-tags'),
						"description" => __("Right offset (padding) from background image to video block (in percent). For example: 12%", 'additional-tags'),
						"group" => __('Background', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
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
			
			class WPBakeryShortCode_Trx_Video extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
			
			
			
			// Zoom
			//-------------------------------------------------------------------------------------
			
			vc_map( array(
				"base" => "trx_zoom",
				"name" => __("Zoom", 'additional-tags'),
				"description" => __("Insert the image with zoom/lens effect", 'additional-tags'),
				"category" => __('ThemeREX', 'additional-tags'),
				'icon' => 'icon_trx_zoom',
				"class" => "trx_sc_single trx_sc_zoom",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "effect",
						"heading" => __("Effect", 'additional-tags'),
						"description" => __("Select effect to display overlapping image", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array(
							__('Lens', 'additional-tags') => 'lens',
							__('Zoom', 'additional-tags') => 'zoom'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "url",
						"heading" => __("Main image", 'additional-tags'),
						"description" => __("Select or upload main image", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "over",
						"heading" => __("Overlaping image", 'additional-tags'),
						"description" => __("Select or upload overlaping image", 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "align",
						"heading" => __("Alignment", 'additional-tags'),
						"description" => __("Float zoom to left or right side", 'additional-tags'),
						"admin_label" => true,
						"class" => "",
						"value" => array_flip($THEMEREX_GLOBALS['sc_params']['float']),
						"type" => "dropdown"
					),
					array(
						"param_name" => "bg_image",
						"heading" => __("Background image", 'additional-tags'),
						"description" => __("Select or upload image or write URL from other site for zoom background. Attention! If you use background image - specify paddings below from background margins to video block in percents!", 'additional-tags'),
						"group" => __('Background', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "attach_image"
					),
					array(
						"param_name" => "bg_top",
						"heading" => __("Top offset", 'additional-tags'),
						"description" => __("Top offset (padding) from background image to zoom block (in percent). For example: 3%", 'additional-tags'),
						"group" => __('Background', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_bottom",
						"heading" => __("Bottom offset", 'additional-tags'),
						"description" => __("Bottom offset (padding) from background image to zoom block (in percent). For example: 3%", 'additional-tags'),
						"group" => __('Background', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_left",
						"heading" => __("Left offset", 'additional-tags'),
						"description" => __("Left offset (padding) from background image to zoom block (in percent). For example: 20%", 'additional-tags'),
						"group" => __('Background', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "bg_right",
						"heading" => __("Right offset", 'additional-tags'),
						"description" => __("Right offset (padding) from background image to zoom block (in percent). For example: 12%", 'additional-tags'),
						"group" => __('Background', 'additional-tags'),
						"class" => "",
						"value" => "",
						"type" => "textfield"
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
			
			class WPBakeryShortCode_Trx_Zoom extends THEMEREX_VC_ShortCodeSingle {}
			

			do_action('themerex_action_shortcodes_list_vc');
			
			
			if (false && themerex_exists_woocommerce()) {
			
				// WooCommerce - Cart
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "woocommerce_cart",
					"name" => __("Cart", 'additional-tags'),
					"description" => __("WooCommerce shortcode: show cart page", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_wooc_cart',
					"class" => "trx_sc_alone trx_sc_woocommerce_cart",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => false,
					"params" => array()
				) );
				
				class WPBakeryShortCode_Woocommerce_Cart extends THEMEREX_VC_ShortCodeAlone {}
			
			
				// WooCommerce - Checkout
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "woocommerce_checkout",
					"name" => __("Checkout", 'additional-tags'),
					"description" => __("WooCommerce shortcode: show checkout page", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_wooc_checkout',
					"class" => "trx_sc_alone trx_sc_woocommerce_checkout",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => false,
					"params" => array()
				) );
				
				class WPBakeryShortCode_Woocommerce_Checkout extends THEMEREX_VC_ShortCodeAlone {}
			
			
				// WooCommerce - My Account
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "woocommerce_my_account",
					"name" => __("My Account", 'additional-tags'),
					"description" => __("WooCommerce shortcode: show my account page", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_wooc_my_account',
					"class" => "trx_sc_alone trx_sc_woocommerce_my_account",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => false,
					"params" => array()
				) );
				
				class WPBakeryShortCode_Woocommerce_My_Account extends THEMEREX_VC_ShortCodeAlone {}
			
			
				// WooCommerce - Order Tracking
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "woocommerce_order_tracking",
					"name" => __("Order Tracking", 'additional-tags'),
					"description" => __("WooCommerce shortcode: show order tracking page", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_wooc_order_tracking',
					"class" => "trx_sc_alone trx_sc_woocommerce_order_tracking",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => false,
					"params" => array()
				) );
				
				class WPBakeryShortCode_Woocommerce_Order_Tracking extends THEMEREX_VC_ShortCodeAlone {}
			
			
				// WooCommerce - Shop Messages
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "shop_messages",
					"name" => __("Shop Messages", 'additional-tags'),
					"description" => __("WooCommerce shortcode: show shop messages", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_wooc_shop_messages',
					"class" => "trx_sc_alone trx_sc_shop_messages",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => false,
					"params" => array()
				) );
				
				class WPBakeryShortCode_Shop_Messages extends THEMEREX_VC_ShortCodeAlone {}
			
			
				// WooCommerce - Product Page
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "product_page",
					"name" => __("Product Page", 'additional-tags'),
					"description" => __("WooCommerce shortcode: display single product page", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_product_page',
					"class" => "trx_sc_single trx_sc_product_page",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => true,
					"params" => array(
						array(
							"param_name" => "sku",
							"heading" => __("SKU", 'additional-tags'),
							"description" => __("SKU code of displayed product", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "id",
							"heading" => __("ID", 'additional-tags'),
							"description" => __("ID of displayed product", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "posts_per_page",
							"heading" => __("Number", 'additional-tags'),
							"description" => __("How many products showed", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "1",
							"type" => "textfield"
						),
						array(
							"param_name" => "post_type",
							"heading" => __("Post type", 'additional-tags'),
							"description" => __("Post type for the WP query (leave 'product')", 'additional-tags'),
							"class" => "",
							"value" => "product",
							"type" => "textfield"
						),
						array(
							"param_name" => "post_status",
							"heading" => __("Post status", 'additional-tags'),
							"description" => __("Display posts only with this status", 'additional-tags'),
							"class" => "",
							"value" => array(
								__('Publish', 'additional-tags') => 'publish',
								__('Protected', 'additional-tags') => 'protected',
								__('Private', 'additional-tags') => 'private',
								__('Pending', 'additional-tags') => 'pending',
								__('Draft', 'additional-tags') => 'draft'
							),
							"type" => "dropdown"
						)
					)
				) );
				
				class WPBakeryShortCode_Product_Page extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
				// WooCommerce - Product
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "product",
					"name" => __("Product", 'additional-tags'),
					"description" => __("WooCommerce shortcode: display one product", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_product',
					"class" => "trx_sc_single trx_sc_product",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => true,
					"params" => array(
						array(
							"param_name" => "sku",
							"heading" => __("SKU", 'additional-tags'),
							"description" => __("Product's SKU code", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "id",
							"heading" => __("ID", 'additional-tags'),
							"description" => __("Product's ID", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "",
							"type" => "textfield"
						)
					)
				) );
				
				class WPBakeryShortCode_Product extends THEMEREX_VC_ShortCodeSingle {}
			
			
				// WooCommerce - Best Selling Products
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "best_selling_products",
					"name" => __("Best Selling Products", 'additional-tags'),
					"description" => __("WooCommerce shortcode: show best selling products", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_best_selling_products',
					"class" => "trx_sc_single trx_sc_best_selling_products",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => true,
					"params" => array(
						array(
							"param_name" => "per_page",
							"heading" => __("Number", 'additional-tags'),
							"description" => __("How many products showed", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => __("Columns", 'additional-tags'),
							"description" => __("How many columns per row use for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						)
					)
				) );
				
				class WPBakeryShortCode_Best_Selling_Products extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
				// WooCommerce - Recent Products
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "recent_products",
					"name" => __("Recent Products", 'additional-tags'),
					"description" => __("WooCommerce shortcode: show recent products", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_recent_products',
					"class" => "trx_sc_single trx_sc_recent_products",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => true,
					"params" => array(
						array(
							"param_name" => "per_page",
							"heading" => __("Number", 'additional-tags'),
							"description" => __("How many products showed", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => __("Columns", 'additional-tags'),
							"description" => __("How many columns per row use for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby",
							"heading" => __("Order by", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array(
								__('Date', 'additional-tags') => 'date',
								__('Title', 'additional-tags') => 'title'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "order",
							"heading" => __("Order", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
							"type" => "dropdown"
						)
					)
				) );
				
				class WPBakeryShortCode_Recent_Products extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
				// WooCommerce - Related Products
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "related_products",
					"name" => __("Related Products", 'additional-tags'),
					"description" => __("WooCommerce shortcode: show related products", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_related_products',
					"class" => "trx_sc_single trx_sc_related_products",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => true,
					"params" => array(
						array(
							"param_name" => "posts_per_page",
							"heading" => __("Number", 'additional-tags'),
							"description" => __("How many products showed", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => __("Columns", 'additional-tags'),
							"description" => __("How many columns per row use for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby",
							"heading" => __("Order by", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array(
								__('Date', 'additional-tags') => 'date',
								__('Title', 'additional-tags') => 'title'
							),
							"type" => "dropdown"
						)
					)
				) );
				
				class WPBakeryShortCode_Related_Products extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
				// WooCommerce - Featured Products
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "featured_products",
					"name" => __("Featured Products", 'additional-tags'),
					"description" => __("WooCommerce shortcode: show featured products", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_featured_products',
					"class" => "trx_sc_single trx_sc_featured_products",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => true,
					"params" => array(
						array(
							"param_name" => "per_page",
							"heading" => __("Number", 'additional-tags'),
							"description" => __("How many products showed", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => __("Columns", 'additional-tags'),
							"description" => __("How many columns per row use for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby",
							"heading" => __("Order by", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array(
								__('Date', 'additional-tags') => 'date',
								__('Title', 'additional-tags') => 'title'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "order",
							"heading" => __("Order", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
							"type" => "dropdown"
						)
					)
				) );
				
				class WPBakeryShortCode_Featured_Products extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
				// WooCommerce - Top Rated Products
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "top_rated_products",
					"name" => __("Top Rated Products", 'additional-tags'),
					"description" => __("WooCommerce shortcode: show top rated products", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_top_rated_products',
					"class" => "trx_sc_single trx_sc_top_rated_products",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => true,
					"params" => array(
						array(
							"param_name" => "per_page",
							"heading" => __("Number", 'additional-tags'),
							"description" => __("How many products showed", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => __("Columns", 'additional-tags'),
							"description" => __("How many columns per row use for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby",
							"heading" => __("Order by", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array(
								__('Date', 'additional-tags') => 'date',
								__('Title', 'additional-tags') => 'title'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "order",
							"heading" => __("Order", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
							"type" => "dropdown"
						)
					)
				) );
				
				class WPBakeryShortCode_Top_Rated_Products extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
				// WooCommerce - Sale Products
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "sale_products",
					"name" => __("Sale Products", 'additional-tags'),
					"description" => __("WooCommerce shortcode: list products on sale", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_sale_products',
					"class" => "trx_sc_single trx_sc_sale_products",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => true,
					"params" => array(
						array(
							"param_name" => "per_page",
							"heading" => __("Number", 'additional-tags'),
							"description" => __("How many products showed", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => __("Columns", 'additional-tags'),
							"description" => __("How many columns per row use for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby",
							"heading" => __("Order by", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array(
								__('Date', 'additional-tags') => 'date',
								__('Title', 'additional-tags') => 'title'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "order",
							"heading" => __("Order", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
							"type" => "dropdown"
						)
					)
				) );
				
				class WPBakeryShortCode_Sale_Products extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
				// WooCommerce - Product Category
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "product_category",
					"name" => __("Products from category", 'additional-tags'),
					"description" => __("WooCommerce shortcode: list products in specified category(-ies)", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_product_category',
					"class" => "trx_sc_single trx_sc_product_category",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => true,
					"params" => array(
						array(
							"param_name" => "per_page",
							"heading" => __("Number", 'additional-tags'),
							"description" => __("How many products showed", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => __("Columns", 'additional-tags'),
							"description" => __("How many columns per row use for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby",
							"heading" => __("Order by", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array(
								__('Date', 'additional-tags') => 'date',
								__('Title', 'additional-tags') => 'title'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "order",
							"heading" => __("Order", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
							"type" => "dropdown"
						),
						array(
							"param_name" => "category",
							"heading" => __("Categories", 'additional-tags'),
							"description" => __("Comma separated category slugs", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "operator",
							"heading" => __("Operator", 'additional-tags'),
							"description" => __("Categories operator", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array(
								__('IN', 'additional-tags') => 'IN',
								__('NOT IN', 'additional-tags') => 'NOT IN',
								__('AND', 'additional-tags') => 'AND'
							),
							"type" => "dropdown"
						)
					)
				) );
				
				class WPBakeryShortCode_Product_Category extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
				// WooCommerce - Products
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "products",
					"name" => __("Products", 'additional-tags'),
					"description" => __("WooCommerce shortcode: list all products", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_products',
					"class" => "trx_sc_single trx_sc_products",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => true,
					"params" => array(
						array(
							"param_name" => "skus",
							"heading" => __("SKUs", 'additional-tags'),
							"description" => __("Comma separated SKU codes of products", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "ids",
							"heading" => __("IDs", 'additional-tags'),
							"description" => __("Comma separated ID of products", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => __("Columns", 'additional-tags'),
							"description" => __("How many columns per row use for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby",
							"heading" => __("Order by", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array(
								__('Date', 'additional-tags') => 'date',
								__('Title', 'additional-tags') => 'title'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "order",
							"heading" => __("Order", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
							"type" => "dropdown"
						)
					)
				) );
				
				class WPBakeryShortCode_Products extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
			
				// WooCommerce - Product Attribute
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "product_attribute",
					"name" => __("Products by Attribute", 'additional-tags'),
					"description" => __("WooCommerce shortcode: show products with specified attribute", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_product_attribute',
					"class" => "trx_sc_single trx_sc_product_attribute",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => true,
					"params" => array(
						array(
							"param_name" => "per_page",
							"heading" => __("Number", 'additional-tags'),
							"description" => __("How many products showed", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => __("Columns", 'additional-tags'),
							"description" => __("How many columns per row use for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby",
							"heading" => __("Order by", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array(
								__('Date', 'additional-tags') => 'date',
								__('Title', 'additional-tags') => 'title'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "order",
							"heading" => __("Order", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
							"type" => "dropdown"
						),
						array(
							"param_name" => "attribute",
							"heading" => __("Attribute", 'additional-tags'),
							"description" => __("Attribute name", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "filter",
							"heading" => __("Filter", 'additional-tags'),
							"description" => __("Attribute value", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "",
							"type" => "textfield"
						)
					)
				) );
				
				class WPBakeryShortCode_Product_Attribute extends THEMEREX_VC_ShortCodeSingle {}
			
			
			
				// WooCommerce - Products Categories
				//-------------------------------------------------------------------------------------
				
				vc_map( array(
					"base" => "product_categories",
					"name" => __("Product Categories", 'additional-tags'),
					"description" => __("WooCommerce shortcode: show categories with products", 'additional-tags'),
					"category" => __('WooCommerce', 'additional-tags'),
					'icon' => 'icon_trx_product_categories',
					"class" => "trx_sc_single trx_sc_product_categories",
					"content_element" => true,
					"is_container" => false,
					"show_settings_on_create" => true,
					"params" => array(
						array(
							"param_name" => "number",
							"heading" => __("Number", 'additional-tags'),
							"description" => __("How many categories showed", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => __("Columns", 'additional-tags'),
							"description" => __("How many columns per row use for categories output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby",
							"heading" => __("Order by", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array(
								__('Date', 'additional-tags') => 'date',
								__('Title', 'additional-tags') => 'title'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "order",
							"heading" => __("Order", 'additional-tags'),
							"description" => __("Sorting order for products output", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => array_flip($THEMEREX_GLOBALS['sc_params']['ordering']),
							"type" => "dropdown"
						),
						array(
							"param_name" => "parent",
							"heading" => __("Parent", 'additional-tags'),
							"description" => __("Parent category slug", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "date",
							"type" => "textfield"
						),
						array(
							"param_name" => "ids",
							"heading" => __("IDs", 'additional-tags'),
							"description" => __("Comma separated ID of products", 'additional-tags'),
							"admin_label" => true,
							"class" => "",
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "hide_empty",
							"heading" => __("Hide empty", 'additional-tags'),
							"description" => __("Hide empty categories", 'additional-tags'),
							"class" => "",
							"value" => array("Hide empty" => "1" ),
							"type" => "checkbox"
						)
					)
				) );
				
				class WPBakeryShortCode_Products_Categories extends THEMEREX_VC_ShortCodeSingle {}

			}

		}
	}
}
?>