<?php
/**
 * Add function to widgets_init that will load our widget.
 */
add_action( 'widgets_init', 'themerex_widget_categories_load' );

/**
 * Register our widget.
 */
function themerex_widget_categories_load() {
	register_widget( 'themerex_widget_categories' );
}

/**
 * Categories Widget class.
 */
class themerex_widget_categories extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_subcategories', 'description' => __('Subcategories list', 'additional-tags') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 200, 'height' => 250, 'id_base' => 'themerex_widget_subcategories' );

		/* Create the widget. */
		parent::__construct( 'themerex_widget_subcategories', __('ThemeREX - Subcategories list', 'additional-tags'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '' );

		$post_type = isset($instance['post_type']) ? $instance['post_type'] : 'post';
		$taxonomy = themerex_get_taxonomy_categories_by_post_type($post_type);

		$c = !empty( $instance['count'] ) ? '1' : '0';
		$h = !empty( $instance['hierarchical'] ) ? '1' : '0';
		$d = !empty( $instance['dropdown'] ) ? '1' : '0';

		$root = isset($instance['root']) ? (int) $instance['root'] : 0;

		$cat_args = array(
		        'orderby' => 'name',
                'show_count' => $c,
                'hierarchical' => $h,
                'taxonomy' => $taxonomy,
		        'value_field' => 'slug',
        );

		if ($root > 0) $cat_args['child_of'] = $root;

		/* Before widget (defined by themes). */			
		echo ($before_widget);

		if ($title) echo ($before_title) . ($title) . ($after_title);
		?>			
		<div class="widget_subcategories_inner">
			<?php
			if ( $d ) {
				$tax_terms = get_terms($taxonomy, array('hide_empty' => false, 'child_of' => $root));
				?>
                <select name="<?php echo $taxonomy;?>" id="<?php echo $taxonomy;?>" class="postform">
                    <option value="-1"><?php echo __('Select Category', 'additional-tags');?></option>
                    <?php foreach ($tax_terms as $term_single): ?>
                        <option class="level-0" value="<?php echo get_term_link( $term_single->slug ,$taxonomy);?>"><?php echo $term_single->name;?>&nbsp;&nbsp;(<?php echo $term_single->count;?>)</option>
                    <?php endforeach; ?>
                </select>
                
				<script type='text/javascript'>
				/* <![CDATA[ */
					jQuery('.widget_subcategories select').change(function() {
						var dropdown = jQuery(this).get(0);
						location.href = dropdown.options[dropdown.selectedIndex].value;
					});
				/* ]]> */
				</script>
	
				<?php
			} else {
				?>
				<ul>
				<?php
				$cat_args['title_li'] = '';
				wp_list_categories( apply_filters( 'widget_categories_args', $cat_args ) );
				?>
				</ul>
				<?php
			}
			?>
		</div>
		<?php
		/* After widget (defined by themes). */
		echo ($after_widget);
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] 			= strip_tags( $new_instance['title'] );
		$instance['count'] 			= !empty($new_instance['count']) ? 1 : 0;
		$instance['hierarchical'] 	= !empty($new_instance['hierarchical']) ? 1 : 0;
		$instance['dropdown'] 		= !empty($new_instance['dropdown']) ? 1 : 0;
		$instance['root'] 			= (int) $new_instance['root'];
		$instance['post_type'] 		= strip_tags( $new_instance['post_type'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'count'=>0,
			'dropdown'=>0,
			'hierarchical'=>0,
			'root' => 0,
			'post_type'=>'post'
			)
		);

		$title = $instance['title'];
		$root = (int) $instance['root'];
		$post_type = $instance['post_type'];
		$count = (bool) $instance['count'];
		$hierarchical = (bool) $instance['hierarchical'];
		$dropdown = (bool) $instance['dropdown'];
		
		$posts_types = themerex_get_list_posts_types(false);
		$categories = themerex_get_list_terms(false, themerex_get_taxonomy_categories_by_post_type($post_type));
		?>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e( 'Title:', 'additional-tags'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('post_type')); ?>"><?php _e('Post type:', 'additional-tags'); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id('post_type')); ?>" name="<?php echo esc_attr($this->get_field_name('post_type')); ?>" style="width:100%;" onchange="themerex_admin_change_post_type(this);">
			<?php
				foreach ($posts_types as $type => $type_name) {
					echo '<option value="'.esc_attr($type).'"'.($post_type==$type ? ' selected="selected"' : '').'>'.esc_html($type_name).'</option>';
				}
			?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr($this->get_field_id('root')); ?>"><?php _e('Root category:', 'additional-tags'); ?></label>
			<select id="<?php echo esc_attr($this->get_field_id('root')); ?>" name="<?php echo esc_attr($this->get_field_name('root')); ?>" style="width:100%;">
				<option value="0"><?php _e('-- Any category --', 'additional-tags'); ?></option>
			<?php
				foreach ($categories as $cat_id => $cat_name) {
					echo '<option value="'.esc_attr($cat_id).'"'.($root==$cat_id ? ' selected="selected"' : '').'>'.($cat_name).'</option>';
				}
			?>
			</select>
		</p>

		<p>
		<input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('dropdown')); ?>" name="<?php echo esc_attr($this->get_field_name('dropdown')); ?>"<?php checked( $dropdown ); ?> />
		<label for="<?php echo esc_attr($this->get_field_id('dropdown')); ?>"><?php _e( 'Display as dropdown', 'additional-tags'); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php _e( 'Show post counts', 'additional-tags'); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>" name="<?php echo esc_attr($this->get_field_name('hierarchical')); ?>"<?php checked( $hierarchical ); ?> />
		<label for="<?php echo esc_attr($this->get_field_id('hierarchical')); ?>"><?php _e( 'Show hierarchy', 'additional-tags'); ?></label>
		</p>
		<?php
	}
}
?>