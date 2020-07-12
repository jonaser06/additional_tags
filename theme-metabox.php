<?php 

add_filter( 'cmb_meta_boxes', 'trx_addons_custom_metaboxes' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function trx_addons_custom_metaboxes(array $meta_boxes) {

	$prefix = '_trx_addons_';
	$book_details = explode("\n", of_get_option('trx_addons_book_details'));

	$meta_boxes[] = array(
		'id'         => 'book_sample',
		'title'      => 'Book <b>preview</b> and <b>sample</b> download',
		'pages'      => array( 'product' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name' => 'Sample File URL',
				'desc' => 'Insert your file URL if you want to add sample product from another location',
				'id'   => $prefix . 'sample_file_url',
				'type' => 'text',
			),

			array(
				'name' => 'Sample File',
				'desc' => 'Upload your sample file here',
				'id'   => $prefix . 'sample_file_path',
				'type' => 'file',
			),
		),
	);

	$meta_boxes[] = array(
		'id'         => 'book_details',
		'title'      => '<b>Book Details</b>',
		'pages'      => array( 'product' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(

			array(
				'name'      => 'Book Information',
				'desc'      => '',
				'id'        => $prefix . 'book_details',
				'type'      => 'exploder',
				'options'   => $book_details
			)
		),
	);

	return $meta_boxes;

}

/**
 *  Register Field Groups
 *
 *  The register_field_group function accepts 1 array which holds the relevant data to register a field group
 *  You may edit the array as you see fit. However, this may result in errors if the array is not compatible with ACF
 */

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_taxonomy-authors-2',
		'title' => 'Taxonomy Authors',
		'fields' => array (
			array (
				'key' => 'field_527cc65ed43fc',
				'label' => 'Book Authors',
				'name' => 'book_authors',
				'type' => 'image',
				'instructions' => 'Upload the book author image here',
				'save_format' => 'url',
				'preview_size' => 'thumbnail',
				'library' => 'all',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'ef_taxonomy',
					'operator' => '==',
					'value' => 'authors',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
	
	register_field_group(array (
		'id' => 'acf_taxonomy-publisher',
		'title' => 'Taxonomy Publisher',
		'fields' => array (
			array (
				'key' => 'field_527b6dfc72019',
				'label' => 'Publisher Image',
				'name' => 'publisher_image',
				'type' => 'image',
				'instructions' => 'Upload the publisher image here',
				'save_format' => 'url',
				'preview_size' => 'thumbnail',
				'library' => 'all',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'ef_taxonomy',
					'operator' => '==',
					'value' => 'publisher',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 3,
	));
}

 ?>