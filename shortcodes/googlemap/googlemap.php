<?php
/**
 * Shortcode: Google Map
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */


// Add dynamic CSS and return class for it
if (!function_exists('themerex_add_inline_css_class')) {
	function themerex_add_inline_css_class($css, $suffix='') {
		$class_name = sprintf('themerex_inline_%d', mt_rand());

		$inline_css = sprintf('.%s%s{%s}', $class_name, !empty($suffix) ? (substr($suffix, 0, 1) != ':' ? ' ' : '') . esc_attr($suffix) : '', $css);
		wp_register_style( 'themerex-sc_googlemap-inline-style', false );
		wp_enqueue_style('themerex-sc_googlemap-inline-style');
		wp_add_inline_style( 'themerex-sc_googlemap-inline-style', $inline_css );

		return $class_name;
	}
}

// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'themerex_sc_googlemap_add_styles' ) ) {
	add_filter("wp_enqueue_scripts", 'themerex_sc_googlemap_add_styles');
	function themerex_sc_googlemap_add_styles() {
		wp_enqueue_style('themerex-sc_googlemap_style', trx_addons_get_file_url('shortcodes/googlemap/googlemap.css'), array(), null);
	}
}

// Load shortcode's specific scripts if current mode is Preview in the PageBuilder
if ( !function_exists( 'themerex_sc_googlemap_load_scripts' ) ) {
	add_action("wp_enqueue_scripts", 'themerex_sc_googlemap_load_scripts');
	function themerex_sc_googlemap_load_scripts() {
		if (!empty(themerex_get_theme_option('api_google'))) {
			themerex_enqueue_googlemap();
			wp_enqueue_script( 'themerex-sc_googlemap', trx_addons_get_file_url( 'shortcodes/googlemap/googlemap.js'), array('jquery'), null, true );
			wp_enqueue_script( 'markerclusterer', trx_addons_get_file_url('shortcodes/googlemap/cluster/markerclusterer.min.js'), array('jquery'), null, true );
		}
	}
}