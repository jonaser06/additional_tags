<?php
/*
  Plugin Name: Additional Tags
  Description: Create an additional tags author & publisher, team, testimonials, announcements.
  Version: 1.3.0
  Author: ThemeRex
  Author URI: http://themerex.net
  Language: additional-tags
*/

// Current version
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
    define( 'TRX_ADDONS_VERSION', '1.3.0' );
}

// Plugin's storage
if (!defined('TRX_ADDONS_PLUGIN_DIR'))	define('TRX_ADDONS_PLUGIN_DIR', plugin_dir_path(__FILE__));
if (!defined('TRX_ADDONS_PLUGIN_URL'))	define('TRX_ADDONS_PLUGIN_URL', plugin_dir_url(__FILE__));
if (!defined('TRX_ADDONS_PLUGIN_BASE'))	define('TRX_ADDONS_PLUGIN_BASE',dirname(plugin_basename(__FILE__)));


global $TRX_ADDONS_STORAGE;
$TRX_ADDONS_STORAGE = array(
    // Plugin's location and name
    'plugin_dir' => plugin_dir_path(__FILE__),
    'plugin_url' => plugin_dir_url(__FILE__),
    'plugin_base'=> explode('/', plugin_basename(__FILE__)),
    'plugin_active' => false,
    // Custom post types and taxonomies
    'register_taxonomies' => array(),
    'register_post_types' => array()
);

// Load plugin's translation file
// Attention! It must be loaded before the first call of any translation function
if ( !function_exists( 'trx_addons_load_plugin_textdomain' ) ) {
	add_action( 'plugins_loaded', 'trx_addons_load_plugin_textdomain');
	function trx_addons_load_plugin_textdomain() {
		static $loaded = false;
		if ( $loaded ) return true;
		$domain = 'additional-tags';
		if ( is_textdomain_loaded( $domain ) && !is_a( $GLOBALS['l10n'][ $domain ], 'NOOP_Translations' ) ) return true;
		$loaded = true;
		load_plugin_textdomain( $domain, false, TRX_ADDONS_PLUGIN_BASE . '/languages' );
	}
}


// Register theme required types
if (!function_exists('trx_addons_require_data_taxonomy')) {
	function trx_addons_require_data_taxonomy($name, $args) {
		register_taxonomy($name, $args['post_type'], $args);
	}
}
// Register theme required taxes
if (!function_exists('trx_addons_require_data_post_type')) {
	function trx_addons_require_data_post_type($name, $args) {
		register_post_type($name, $args);
	}
}

// Additional Tags
// Theme init
if (!function_exists('trx_addons_additional_tags')) {
    add_action( 'themerex_action_before_init_theme', 'trx_addons_additional_tags', 10 );
    function trx_addons_additional_tags() {

        if ( class_exists( 'woocommerce' ) ) {

            // Author tags
            if (!function_exists('trx_addons_book_author')) {
                function trx_addons_book_author() {

					trx_addons_require_data_taxonomy('authors', array(
                            'post_type' => array('product'),
                            'hierarchical' => true,
                            'labels' => array(
                                'name' => _x('Authors', 'Taxonomy General Name', 'additional-tags'),
                                'singular_name' => _x('Author', 'Taxonomy Singular Name', 'additional-tags'),
                                'menu_name' => __('Author', 'additional-tags'),
                                'all_items' => __('All Authors', 'additional-tags'),
                                'parent_item' => __('Parent Author', 'additional-tags'),
                                'parent_item_colon' => __('Parent Author:', 'additional-tags'),
                                'new_item_name' => __('New Author Name', 'additional-tags'),
                                'add_new_item' => __('Add New Author', 'additional-tags'),
                                'edit_item' => __('Edit Author', 'additional-tags'),
                                'update_item' => __('Update Author', 'additional-tags'),
                                'separate_items_with_commas' => __('Separate authors with commas', 'additional-tags'),
                                'search_items' => __('Search authors', 'additional-tags'),
                                'add_or_remove_items' => __('Add or remove authors', 'additional-tags'),
                                'choose_from_most_used' => __('Choose from the most used authors', 'additional-tags'),
                            ),
                            'show_ui' => true,
                            'show_admin_column' => true,
                            'query_var' => true,
                            'rewrite' => array('slug' => 'authors')
                        )
                    );

                }
            }

            // Hook into the 'init' action
            add_action('init', 'trx_addons_book_author', 0);


            // Publisher tags

            if (!function_exists('trx_addons_book_publisher')) {
                function trx_addons_book_publisher() {

					trx_addons_require_data_taxonomy('publisher', array(

                            'post_type' => array('product'),
                            'hierarchical' => true,
                            'labels' => array(
                                'name' => _x('Publishers', 'Taxonomy General Name', 'additional-tags'),
                                'singular_name' => _x('Publisher', 'Taxonomy Singular Name', 'additional-tags'),
                                'menu_name' => __('Publisher', 'additional-tags'),
                                'all_items' => __('All Publishers', 'additional-tags'),
                                'parent_item' => __('Parent Publisher', 'additional-tags'),
                                'parent_item_colon' => __('Parent Publisher:', 'additional-tags'),
                                'new_item_name' => __('New Publisher Name', 'additional-tags'),
                                'add_new_item' => __('Add New Publisher', 'additional-tags'),
                                'edit_item' => __('Edit Publisher', 'additional-tags'),
                                'update_item' => __('Update Publisher', 'additional-tags'),
                                'separate_items_with_commas' => __('Separate publishers with commas', 'additional-tags'),
                                'search_items' => __('Search publishers ', 'additional-tags'),
                                'add_or_remove_items' => __('Add or remove publishers ', 'additional-tags'),
                                'choose_from_most_used' => __('Choose from the most used publishers ', 'additional-tags'),
                            ),
                            'show_ui' => true,
                            'show_admin_column' => true,
                            'query_var' => true,
                            'rewrite' => array('slug' => 'publisher')
                        )
                    );
                }
            }

            // Hook into the 'init' action
            add_action( 'init', 'trx_addons_book_publisher', 0 );

        }

		// Prepare type "Courses"
		/* trx_addons_require_data_post_type( 'courses', array(
				'label'               => esc_html__( 'Course item', 'additional-tags' ),
				'description'         => esc_html__( 'Course Description', 'additional-tags' ),
				'labels'              => array(
					'name'                => esc_html_x( 'Courses', 'Post Type General Name', 'additional-tags' ),
					'singular_name'       => esc_html_x( 'Course item', 'Post Type Singular Name', 'additional-tags' ),
					'menu_name'           => esc_html__( 'Courses', 'additional-tags' ),
					'parent_item_colon'   => esc_html__( 'Parent Item:', 'additional-tags' ),
					'all_items'           => esc_html__( 'All Courses', 'additional-tags' ),
					'view_item'           => esc_html__( 'View Item', 'additional-tags' ),
					'add_new_item'        => esc_html__( 'Add New Course item', 'additional-tags' ),
					'add_new'             => esc_html__( 'Add New', 'additional-tags' ),
					'edit_item'           => esc_html__( 'Edit Item', 'additional-tags' ),
					'update_item'         => esc_html__( 'Update Item', 'additional-tags' ),
					'search_items'        => esc_html__( 'Search Item', 'additional-tags' ),
					'not_found'           => esc_html__( 'Not found', 'additional-tags' ),
					'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'additional-tags' ),
				),
				'supports'            => array( 'title', 'excerpt', 'editor', 'author', 'thumbnail', 'comments', 'custom-fields'),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'menu_icon'			  => 'dashicons-format-chat',
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 25,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'query_var'           => true,
				'capability_type'     => 'post',
				'rewrite'             => true
			)
		); */

		// Prepare taxonomy for courses
		// Courses groups (categories)
		/* trx_addons_require_data_taxonomy( 'courses_group', array(
				'post_type'			=> array( 'courses' ),
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => esc_html_x( 'Courses Groups', 'taxonomy general name', 'additional-tags' ),
					'singular_name'     => esc_html_x( 'Courses Group', 'taxonomy singular name', 'additional-tags' ),
					'search_items'      => esc_html__( 'Search Groups', 'additional-tags' ),
					'all_items'         => esc_html__( 'All Groups', 'additional-tags' ),
					'parent_item'       => esc_html__( 'Parent Group', 'additional-tags' ),
					'parent_item_colon' => esc_html__( 'Parent Group:', 'additional-tags' ),
					'edit_item'         => esc_html__( 'Edit Group', 'additional-tags' ),
					'update_item'       => esc_html__( 'Update Group', 'additional-tags' ),
					'add_new_item'      => esc_html__( 'Add New Group', 'additional-tags' ),
					'new_item_name'     => esc_html__( 'New Group Name', 'additional-tags' ),
					'menu_name'         => esc_html__( 'Courses Groups', 'additional-tags' ),
				),
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'courses_group' ),
			)
		); */

		// Courses tags
		/* trx_addons_require_data_taxonomy( 'courses_tag', array(
				'post_type'			=> array( 'courses' ),
				'hierarchical'      => false,
				'labels'            => array(
					'name'              => esc_html_x( 'Courses Tags', 'taxonomy general name', 'additional-tags' ),
					'singular_name'     => esc_html_x( 'Courses Tag', 'taxonomy singular name', 'additional-tags' ),
					'search_items'      => esc_html__( 'Search Tags', 'additional-tags' ),
					'all_items'         => esc_html__( 'All Tags', 'additional-tags' ),
					'parent_item'       => esc_html__( 'Parent Tag', 'additional-tags' ),
					'parent_item_colon' => esc_html__( 'Parent Tag:', 'additional-tags' ),
					'edit_item'         => esc_html__( 'Edit Tag', 'additional-tags' ),
					'update_item'       => esc_html__( 'Update Tag', 'additional-tags' ),
					'add_new_item'      => esc_html__( 'Add New Tag', 'additional-tags' ),
					'new_item_name'     => esc_html__( 'New Tag Name', 'additional-tags' ),
					'menu_name'         => esc_html__( 'Courses Tags', 'additional-tags' ),
				),
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'courses_tag' ),
			)
		); */


		// Prepare type "Team"
		/* trx_addons_require_data_post_type( 'team', array(
				'label'               => esc_html__( 'Team member', 'additional-tags' ),
				'description'         => esc_html__( 'Team Description', 'additional-tags' ),
				'labels'              => array(
					'name'                => esc_html_x( 'Team', 'Post Type General Name', 'additional-tags' ),
					'singular_name'       => esc_html_x( 'Team member', 'Post Type Singular Name', 'additional-tags' ),
					'menu_name'           => esc_html__( 'Team', 'additional-tags' ),
					'parent_item_colon'   => esc_html__( 'Parent Item:', 'additional-tags' ),
					'all_items'           => esc_html__( 'All Team', 'additional-tags' ),
					'view_item'           => esc_html__( 'View Item', 'additional-tags' ),
					'add_new_item'        => esc_html__( 'Add New Team member', 'additional-tags' ),
					'add_new'             => esc_html__( 'Add New', 'additional-tags' ),
					'edit_item'           => esc_html__( 'Edit Item', 'additional-tags' ),
					'update_item'         => esc_html__( 'Update Item', 'additional-tags' ),
					'search_items'        => esc_html__( 'Search Item', 'additional-tags' ),
					'not_found'           => esc_html__( 'Not found', 'additional-tags' ),
					'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'additional-tags' ),
				),
				'supports'            => array( 'title', 'excerpt', 'editor', 'author', 'thumbnail', 'comments'),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'menu_icon'			  => 'dashicons-admin-users',
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 25,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'query_var'           => true,
				'capability_type'     => 'page',
				'rewrite'             => true
			)
		); */

		// Prepare taxonomy for team
		/* trx_addons_require_data_taxonomy( 'team_group', array(
				'post_type'			=> array( 'team' ),
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => esc_html_x( 'Team Group', 'taxonomy general name', 'additional-tags' ),
					'singular_name'     => esc_html_x( 'Group', 'taxonomy singular name', 'additional-tags' ),
					'search_items'      => esc_html__( 'Search Groups', 'additional-tags' ),
					'all_items'         => esc_html__( 'All Groups', 'additional-tags' ),
					'parent_item'       => esc_html__( 'Parent Group', 'additional-tags' ),
					'parent_item_colon' => esc_html__( 'Parent Group:', 'additional-tags' ),
					'edit_item'         => esc_html__( 'Edit Group', 'additional-tags' ),
					'update_item'       => esc_html__( 'Update Group', 'additional-tags' ),
					'add_new_item'      => esc_html__( 'Add New Group', 'additional-tags' ),
					'new_item_name'     => esc_html__( 'New Group Name', 'additional-tags' ),
					'menu_name'         => esc_html__( 'Team Group', 'additional-tags' ),
				),
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'team_group' ),
			)
		); */

		// Prepare taxonomy for attachment
		/* trx_addons_require_data_taxonomy(  'media_folder', array(
				'post_type'			=> array( 'attachment' ),
				'hierarchical' 		=> true,
				'labels' 			=> array(
					'name'              => esc_html__('Media Folders', 'additional-tags'),
					'singular_name'     => esc_html__('Media Folder', 'additional-tags'),
					'search_items'      => esc_html__('Search Media Folders', 'additional-tags'),
					'all_items'         => esc_html__('All Media Folders', 'additional-tags'),
					'parent_item'       => esc_html__('Parent Media Folder', 'additional-tags'),
					'parent_item_colon' => esc_html__('Parent Media Folder:', 'additional-tags'),
					'edit_item'         => esc_html__('Edit Media Folder', 'additional-tags'),
					'update_item'       => esc_html__('Update Media Folder', 'additional-tags'),
					'add_new_item'      => esc_html__('Add New Media Folder', 'additional-tags'),
					'new_item_name'     => esc_html__('New Media Folder Name', 'additional-tags'),
					'menu_name'         => esc_html__('Media Folders', 'additional-tags'),
				),
				'query_var'			=> true,
				'exclude_from_search' => false, // Custom search
				'rewrite' 			=> true,
				'show_admin_column'	=> true
			)
		); */


		// Prepare type "Testimonial"
		/* trx_addons_require_data_post_type( 'testimonial', array(
				'label'               => esc_html__( 'Testimonial', 'additional-tags' ),
				'description'         => esc_html__( 'Testimonial Description', 'additional-tags' ),
				'labels'              => array(
					'name'                => esc_html_x( 'Testimonials', 'Post Type General Name', 'additional-tags' ),
					'singular_name'       => esc_html_x( 'Testimonial', 'Post Type Singular Name', 'additional-tags' ),
					'menu_name'           => esc_html__( 'Testimonials', 'additional-tags' ),
					'parent_item_colon'   => esc_html__( 'Parent Item:', 'additional-tags' ),
					'all_items'           => esc_html__( 'All Testimonials', 'additional-tags' ),
					'view_item'           => esc_html__( 'View Item', 'additional-tags' ),
					'add_new_item'        => esc_html__( 'Add New Testimonial', 'additional-tags' ),
					'add_new'             => esc_html__( 'Add New', 'additional-tags' ),
					'edit_item'           => esc_html__( 'Edit Item', 'additional-tags' ),
					'update_item'         => esc_html__( 'Update Item', 'additional-tags' ),
					'search_items'        => esc_html__( 'Search Item', 'additional-tags' ),
					'not_found'           => esc_html__( 'Not found', 'additional-tags' ),
					'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'additional-tags' ),
				),
				'supports'            => array( 'title', 'editor', 'author', 'thumbnail'),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'menu_icon'			  => 'dashicons-cloud',
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 25,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'capability_type'     => 'page',
			)
		); */

		// Prepare taxonomy for testimonial
		/* trx_addons_require_data_taxonomy( 'testimonial_group', array(
				'post_type'			=> array( 'testimonial' ),
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => esc_html_x( 'Testimonials Group', 'taxonomy general name', 'additional-tags' ),
					'singular_name'     => esc_html_x( 'Group', 'taxonomy singular name', 'additional-tags' ),
					'search_items'      => esc_html__( 'Search Groups', 'additional-tags' ),
					'all_items'         => esc_html__( 'All Groups', 'additional-tags' ),
					'parent_item'       => esc_html__( 'Parent Group', 'additional-tags' ),
					'parent_item_colon' => esc_html__( 'Parent Group:', 'additional-tags' ),
					'edit_item'         => esc_html__( 'Edit Group', 'additional-tags' ),
					'update_item'       => esc_html__( 'Update Group', 'additional-tags' ),
					'add_new_item'      => esc_html__( 'Add New Group', 'additional-tags' ),
					'new_item_name'     => esc_html__( 'New Group Name', 'additional-tags' ),
					'menu_name'         => esc_html__( 'Testimonial Group', 'additional-tags' ),
				),
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'testimonial_group' ),
			)
		); */

		// Prepare type "lesson"
		/* trx_addons_require_data_post_type(  'lesson', array(
				'label'               => esc_html__( 'Lesson', 'additional-tags' ),
				'description'         => esc_html__( 'Lesson Description', 'additional-tags' ),
				'labels'              => array(
					'name'                => esc_html_x( 'Lessons', 'Post Type General Name', 'additional-tags' ),
					'singular_name'       => esc_html_x( 'Lesson', 'Post Type Singular Name', 'additional-tags' ),
					'menu_name'           => esc_html__( 'Lessons', 'additional-tags' ),
					'parent_item_colon'   => esc_html__( 'Parent Item:', 'additional-tags' ),
					'all_items'           => esc_html__( 'All lessons', 'additional-tags' ),
					'view_item'           => esc_html__( 'View Item', 'additional-tags' ),
					'add_new_item'        => esc_html__( 'Add New lesson', 'additional-tags' ),
					'add_new'             => esc_html__( 'Add New', 'additional-tags' ),
					'edit_item'           => esc_html__( 'Edit Item', 'additional-tags' ),
					'update_item'         => esc_html__( 'Update Item', 'additional-tags' ),
					'search_items'        => esc_html__( 'Search Item', 'additional-tags' ),
					'not_found'           => esc_html__( 'Not found', 'additional-tags' ),
					'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'additional-tags' ),
				),
				'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'menu_icon'			  => 'dashicons-format-chat',
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 25,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'query_var' => true,
				'publicly_queryable'  => true,
				'capability_type'     => 'post'
			)
		); */

    }
}



/* Support for meta boxes
--------------------------------------------------- */
if (!function_exists('trx_addons_meta_box_add')) {
    add_action('add_meta_boxes', 'trx_addons_meta_box_add');
    function trx_addons_meta_box_add() {
        // Custom theme-specific override-optionses
        $boxes = apply_filters('trx_addons_filter_override_options', array());
        if (is_array($boxes)) {
            foreach ($boxes as $box) {
                $box = array_merge(array('id' => '',
                    'title' => '',
                    'callback' => '',
                    'page' => null,        // screen
                    'context' => 'advanced',
                    'priority' => 'default',
                    'callbacks' => null
                ),
                    $box);
                add_meta_box($box['id'], $box['title'], $box['callback'], $box['page'], $box['context'], $box['priority'], $box['callbacks']);
            }
        }
    }
}

if (!function_exists('trx_addons_utils_get_twitter_data')) {
    function trx_addons_utils_get_twitter_data($cfg) {
        $data = get_transient("twitter_data_".($cfg['mode']));
        if (!$data) {
            require_once(  TRX_ADDONS_PLUGIN_DIR .'lib/tmhOAuth/tmhOAuth.php' );
            $tmhOAuth = new tmhOAuth(array(
                'consumer_key'    => $cfg['consumer_key'],
                'consumer_secret' => $cfg['consumer_secret'],
                'token'           => $cfg['token'],
                'secret'          => $cfg['secret']
            ));
            $code = $tmhOAuth->user_request(array(
                'url' => $tmhOAuth->url(trx_addons_get_twitter_mode_url($cfg['mode']))
            ));
            if ($code == 200) {
                $data = json_decode($tmhOAuth->response['response'], true);
                if (isset($data['status'])) {
                    $code = $tmhOAuth->user_request(array(
                        'url' => $tmhOAuth->url(trx_addons_get_twitter_mode_url($cfg['oembed'])),
                        'params' => array(
                            'id' => $data['status']['id_str']
                        )
                    ));
                    if ($code == 200)
                        $data = json_decode($tmhOAuth->response['response'], true);
                }
                set_transient("twitter_data_".($cfg['mode']), $data, 60*60);
            }
        } else if (!is_array($data) && themerex_substr($data, 0, 2)=='a:') {
            $data = unserialize($data);
        }
        return $data;
    }
}

// Return URL for the specified mode
if (!function_exists('trx_addons_get_twitter_mode_url')) {
	function trx_addons_get_twitter_mode_url($mode) {
		$url = '/1.1/statuses/';
		if ($mode == 'user_timeline')
			$url .= $mode;
		else if ($mode == 'home_timeline')
			$url .= $mode;
		return $url;
	}
}

// AJAX: New user registration
if ( !function_exists( 'trx_addons_users_registration_user' ) ) {
    add_action('wp_ajax_themerex_registration_user',			'trx_addons_users_registration_user');
    add_action('wp_ajax_nopriv_themerex_registration_user',	'trx_addons_users_registration_user');
    function trx_addons_users_registration_user() {

        if ( !wp_verify_nonce( wp_create_nonce(admin_url('admin-ajax.php')),  esc_url(admin_url('admin-ajax.php'))) || (int) get_option('users_can_register') == 0  )
            die();

        $user_name  = substr($_REQUEST['user_name'], 0, 60);
        $user_email = substr($_REQUEST['user_email'], 0, 60);
        $user_pwd   = substr($_REQUEST['user_pwd'], 0, 60);

        $response = array(
            'error' => '',
            'redirect_to' => substr($_REQUEST['redirect_to'], 0, 200)
        );

        $id = wp_insert_user( array ('user_login' => $user_name, 'user_pass' => $user_pwd, 'user_email' => $user_email) );
        if ( is_wp_error($id) ) {
            $response['error'] = $id->get_error_message();
        } else {
            if (($notify = apply_filters('themerex_filter_notify_about_new_registration', 'no')) != 'no' && ($admin_email = get_option('admin_email'))) {
                // Send notify to the site admin
                if (in_array($notify, array('both', 'admin'))) {
                    $subj = sprintf(esc_html__('Site %s - New user registration: %s', 'additional-tags'), esc_html(get_bloginfo('site_name')), esc_html($user_name));
                    $msg = "\n".esc_html__('New registration:', 'additional-tags')
                        ."\n".esc_html__('Name:', 'additional-tags').' '.esc_html($user_name)
                        ."\n".esc_html__('E-mail:', 'additional-tags').' '.esc_html($user_email)
                        ."\n\n............ " . esc_html(get_bloginfo('site_name')) . " (" . esc_html(esc_url(home_url('/'))) . ") ............";
                    $head = "From: " . sanitize_text_field($user_email) . "\n"
                        . "Reply-To: " . sanitize_text_field($user_email) . "\n";
                    $rez = wp_mail($admin_email, $subj, $msg, $head);
                }
                // Send notify to the new user
                if (in_array($notify, array('both', 'user'))) {
                    $subj = sprintf(esc_html__('Welcome to the "%s"', 'additional-tags'), get_bloginfo('site_name'));
                    $msg = "\n".esc_html__('Your registration data:', 'additional-tags')
                        ."\n".esc_html__('Name:', 'additional-tags').' '.esc_html($user_name)
                        ."\n".esc_html__('E-mail:', 'additional-tags').' '.esc_html($user_email)
                        ."\n".esc_html__('Password:', 'additional-tags').' '.esc_html($user_pwd)
                        ."\n\n............ " . esc_html(get_bloginfo('site_name')) . " (<a href=\"" . esc_url(home_url('/')) . "\">" . esc_html(esc_url(home_url('/'))) . "</a>) ............";
                    $head = "From: " . sanitize_text_field($admin_email) . "\n"
                        . "Reply-To: " . sanitize_text_field($admin_email) . "\n";
                    wp_mail($user_email, $subj, $msg, $head);
                }
            }
        }
        echo json_encode($response);
        die();
    }
}


// Load required styles and scripts in the admin mode
if ( !function_exists( 'trx_addons_load_scripts_admin' ) ) {
    add_action("admin_enqueue_scripts", 'trx_addons_load_scripts_admin');
    function trx_addons_load_scripts_admin($all=false) {
        // Font with icons must be loaded before main stylesheet
        if ($all
            || strpos(add_query_arg(array()), 'post.php')!==false
            || strpos(add_query_arg(array()), 'themes.php')!==false
        ) {
            wp_enqueue_style( 'trx_addons-icons', trx_addons_get_file_url('css/font-icons/css/trx_addons_icons-embedded.css'), array(), null );
            wp_enqueue_style( 'trx_addons-icons-animation', trx_addons_get_file_url('css/font-icons/css/animation.css'), array(), null );
        }
        // Fire action to load all other scripts from components
        do_action('trx_addons_action_load_scripts_admin', $all);
    }
}

// Shortcodes init
if (!function_exists('trx_addons_sc_init')) {
    add_action( 'after_setup_theme', 'trx_addons_sc_init' );
    function trx_addons_sc_init() {
        global $TRX_ADDONS_STORAGE;
        if ( !($TRX_ADDONS_STORAGE['plugin_active'] = apply_filters('trx_addons_active', $TRX_ADDONS_STORAGE['plugin_active'])) ) return;

        // Include shortcodes
        require_once trx_addons_get_file_dir('shortcodes/core.shortcodes.php');
    }
}

// Widgets init
if (!function_exists('trx_addons_setup_widgets')) {
    add_action( 'widgets_init', 'trx_addons_setup_widgets', 9 );
    function trx_addons_setup_widgets() {
        global $TRX_ADDONS_STORAGE;
        if ( !($TRX_ADDONS_STORAGE['plugin_active'] = apply_filters('trx_addons_active', $TRX_ADDONS_STORAGE['plugin_active'])) ) return;

        // Include widgets
        require_once trx_addons_get_file_dir('widgets/advert.php');
        require_once trx_addons_get_file_dir('widgets/calendar.php');
        require_once trx_addons_get_file_dir('widgets/categories.php');
        require_once trx_addons_get_file_dir('widgets/flickr.php');
        require_once trx_addons_get_file_dir('widgets/popular_posts.php');
        require_once trx_addons_get_file_dir('widgets/recent_posts.php');
        require_once trx_addons_get_file_dir('widgets/recent_reviews.php');
        require_once trx_addons_get_file_dir('widgets/socials.php');
        require_once trx_addons_get_file_dir('widgets/top10.php');
        require_once trx_addons_get_file_dir('widgets/twitter.php');
        require_once trx_addons_get_file_dir('widgets/qrcode/qrcode.php');
    }
}

// Return text for the Privacy Policy checkbox
if (!function_exists('trx_addons_get_privacy_text')) {
    function trx_addons_get_privacy_text() {
        $page = get_option('wp_page_for_privacy_policy');
        return apply_filters( 'trx_addons_filter_privacy_text', wp_kses_post(
                __( 'I agree that my submitted data is being collected and stored.', 'additional-tags')
                . ( '' != $page
                    // Translators: Add url to the Privacy Policy page
                    ? ' ' . sprintf(__('For further details on handling user data, see our %s', 'additional-tags'),
                        '<a href="' . esc_url(get_permalink($page)) . '" target="_blank">'
                        . __('Privacy Policy', 'additional-tags')
                        . '</a>')
                    : ''
                )
            )
        );
    }
}

// Prepare required styles and scripts for admin mode
if ( !function_exists( 'trx_addons_admin_prepare_scripts' ) ) {
    add_action("admin_head", 'trx_addons_admin_prepare_scripts');
    function trx_addons_admin_prepare_scripts() {
        ?>
        <script>
            if (typeof TRX_ADDONS_GLOBALS == 'undefined') var TRX_ADDONS_GLOBALS = {};
            jQuery(document).ready(function() {


            });
        </script>
        <?php
    }
}


if ( ! function_exists( 'trx_addons_plugin_post_data_atts' ) ) {
    add_filter( 'trx_addons_post_data_atts', 'trx_addons_plugin_post_data_atts' );
    function trx_addons_plugin_post_data_atts($atts){
        $post_id = $atts['post_id'];
        $post_views = themerex_get_post_views($post_id);
        $atts['post_views'] = $post_views;
        return $atts;
    }
}

// Login/Register code
if ( ! function_exists( 'trx_addons_login_code' ) ) {
    add_action( 'trx_addons_action_login_code', 'trx_addons_login_code' );
    function trx_addons_login_code(){
		// Anyone can register ?
		if ( (int) get_option('users_can_register') > 0) {
			// add_action( 'trx_addons_action_login_code');
			?>
			<li class="menu_user_register"><a href="#popup_registration" class="popup_link popup_register_link"><?php esc_html_e('Register', 'additional-tags'); ?></a>
				<?php require_once trx_addons_get_file_dir('templates/register.php'); ?>
			</li>
		<?php } ?>
		<li class="menu_user_login"><a href="#popup_login" class="popup_link popup_login_link"><?php esc_html_e('Login', 'additional-tags'); ?></a>
			<?php require_once trx_addons_get_file_dir('templates/login.php'); ?>
		</li>
		<?php
	}
}

// File functions
if (file_exists(TRX_ADDONS_PLUGIN_DIR . 'includes/plugin.files.php')) {
    require_once TRX_ADDONS_PLUGIN_DIR . 'includes/plugin.files.php';
}

// Third-party plugins support
if (file_exists(TRX_ADDONS_PLUGIN_DIR . 'api/api.php')) {
    require_once TRX_ADDONS_PLUGIN_DIR . 'api/api.php';
}


// Demo data import/export
if (file_exists(TRX_ADDONS_PLUGIN_DIR . 'importer/importer.php')) {
    require_once TRX_ADDONS_PLUGIN_DIR . 'importer/importer.php';
}

require_once trx_addons_get_file_dir('includes/core.socials.php');


if (is_admin()) {
    require_once trx_addons_get_file_dir('tools/emailer/emailer.php');
    require_once trx_addons_get_file_dir('tools/po_composer/po_composer.php');
}
?>
