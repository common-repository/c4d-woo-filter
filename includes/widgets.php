<?php

class c4dWooFilterWidget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'c4d-woo-filter-widget',
			'description' => 'My Widget is awesome',
		);
		parent::__construct( 'c4d-woo-filter-widget', esc_html__('C4D Woo Filter', 'c4d-woo-filter'), $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		if ( ! empty( $instance['shortcode'] ) ) {
			echo do_shortcode($instance['shortcode']);
		}
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'c4d-woo-filter' );
		$shortcode = ! empty( $instance['shortcode'] ) ? $instance['shortcode'] : '<div class="row">
										<div class="col-sm-3"><h3>Soft By</h3>[c4d-woo-filter-soft]</div>
										<div class="col-sm-3"><h3>Price</h3>[c4d-woo-filter-price]</div>
										<div class="col-sm-3"><h3>Colors</h3>[c4d-woo-filter-tax tax="color"]</div>
										<div class="col-sm-3"><h3>Tags</h3>[c4d-woo-filter-tag]</div>
										</div>';
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'c4d-woo-filter' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p><label for="<?php echo $this->get_field_id('shortcode'); ?>"><?php _e( 'Shortcode:' ); ?></label>
		<textarea class="widefat" class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('shortcode'); ?>" name="<?php echo $this->get_field_name('shortcode'); ?>" type="text"><?php echo esc_attr( $shortcode ); ?></textarea></p>

		

		<?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		return $new_instance;
	}
}
add_action( 'widgets_init', function(){
	register_widget( 'c4dWooFilterWidget' );
});