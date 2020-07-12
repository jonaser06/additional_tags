<?php
/**
 * The style "default" of the Googlemap
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('themerex_args_sc_googlemap');

?><div id="<?php echo esc_attr($args['id']); ?>_wrap" class="sc_googlemap_wrap" style="<?php echo  themerex_get_css_position_from_values($args['top'], $args['right'], $args['bottom'], $args['left']); ?>"><?php

	if (!empty($args['title'])) {
		$title_tag = 'h2';
		?><h2 class="<?php
			echo esc_attr('sc_item_title sc_googlemap_title');
			?>"><?php
			themerex_show_layout($args['title']);
		?></h2><?php
	}


	if ($args['content']) {
		?><div class="sc_googlemap_content_wrap"><?php
	}
	?><div id="<?php echo esc_attr($args['id']); ?>"
			class="sc_googlemap sc_googlemap_<?php
				echo esc_attr($args['type']);
				if (themerex_sc_param_is_on($args['prevent_scroll'])) echo ' sc_googlemap_prevent_scroll';
				echo (!empty($args['class']) ? ' '.esc_attr($args['class']) : '');
			?>"
			<?php echo ($args['css']!='' ? ' style="'.esc_attr($args['css']).'"' : ''); ?>
			data-zoom="<?php echo esc_attr($args['zoom']); ?>"
			data-center="<?php echo esc_attr($args['center']); ?>"
			data-style="<?php echo esc_attr($args['style']); ?>"
			data-cluster-icon="<?php echo esc_attr($args['cluster']); ?>"
			><?php
			$cnt = 0;
			foreach ($args['markers'] as $marker) {
				$cnt++;
				if (empty($api_key)) {
					?><iframe src="https://maps.google.com/maps?t=m&output=embed&iwloc=near&z=<?php echo esc_attr($args['zoom'] > 0 ? $args['zoom'] : 14); ?>&q=<?php
						echo esc_attr(!empty($marker['address']) ? urlencode($marker['address']) : '')
						. ( !empty($marker['latlng'])
							? ( !empty($marker['address']) ? '@' : '' ) . str_replace(' ', '', $marker['latlng'])
							: ''
						);?> aria-label="<?php echo esc_attr(!empty($marker['title']) ? $marker['title'] : '') ;?>"></iframe><?php
					break; // Remove this line if you want display separate iframe for each marker (otherwise only first marker shown)
				} else {
					?><div id="<?php echo esc_attr($args['id'].'_'.intval($cnt)); ?>" class="sc_googlemap_marker"
						   data-latlng="<?php echo esc_attr(!empty($marker['latlng']) ? str_replace(' ', '', $marker['latlng']) : ''); ?>"
						   data-address="<?php echo esc_attr(!empty($marker['address']) ? $marker['address'] : ''); ?>"
						   data-description="<?php echo esc_attr(!empty($marker['description']) ? $marker['description'] : ''); ?>"
						   data-title="<?php echo esc_attr(!empty($marker['title']) ? $marker['title'] : ''); ?>"
						   data-animation="<?php echo esc_attr(!empty($marker['animation']) ? $marker['animation'] : ''); ?>"
						   data-icon="<?php echo esc_attr(!empty($marker['icon']) ? $marker['icon'] : ''); ?>"
						   data-icon_shadow="<?php echo esc_attr(!empty($marker['icon']) && !empty($marker['icon_shadow'])
							   ? $marker['icon_shadow']
							   : ''); ?>"
						   data-icon_width="<?php echo esc_attr(!empty($marker['icon']) && !empty($marker['icon_width'])
							   ? $marker['icon_width']
							   : ''); ?>"
						   data-icon_height="<?php echo esc_attr(!empty($marker['icon']) && !empty($marker['icon_height'])
							   ? $marker['icon_height']
							   : ''); ?>"
					></div><?php
				}
			}
	?></div><?php
	
	if ($args['content']) {
		?>
			<div class="sc_googlemap_content sc_googlemap_content_<?php echo esc_attr($args['type']); ?>"><?php themerex_show_layout($args['content']); ?></div>
		</div>
		<?php
	}

?></div><!-- /.sc_googlemap_wrap -->