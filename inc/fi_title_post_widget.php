<?php
/**
 * Custom widgets that work only for the Future Imperfect theme
 *
 * @package Future_Imperfect
 */

/**
 * Adds Future_Imperfect_Title_List_Widget widget.
 */
class Future_Imperfect_Title_List_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'future_imperfect_title_list_widget', // Base ID
			__( 'Future Imperfect Title List', 'future-imperfect' ), // Name
			array( 'description' => __( 'Lists posts with optional subtitles', 'future-imperfect' ), ) // Args
		);
	}

	/**
	 * Go get the data
	 *
	 * @param array $args     Query arguments.
	 */
	function get_data( $instance = '' ) {

		$output = '';

		$args = array(
			'posts_per_page' => 5,
		);

		if ( isset( $instance['cat'] ) && '' != $instance['cat'] && '-1' != $instance['cat'] ) {
			$args['cat'] = $instance['cat'];
		}

		if ( isset( $instance['posts_per_page'] ) && '' != $instance['posts_per_page'] ) {
			$args['posts_per_page']         = $instance['posts_per_page'];
			$args['posts_per_archive_page'] = $instance['posts_per_page'];
			$args['ignore_sticky_posts']    = true;
			$args['no_found_rows']          = true;
		}

		// The Query
		$the_query = new WP_Query( $args );

		$output .= '<ul class="links">' . "\n";

		// The Loop
		if ( $the_query->have_posts() ) {

			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$output .= '<li>' . "\n";
				$output .= '<a href="' . get_permalink() . '">' . "\n";
				$output .= '<h3>' . get_the_title() . '</h3>' . "\n";

				// check to see if we have subtitles
				if ( function_exists( 'get_the_subtitle' ) && '' != get_the_subtitle( get_the_ID(), '', '', false ) ) {
					$output .= '<p>' . get_the_subtitle( get_the_ID(), '', '', false ) . '</p>' . "\n";
				}

				$output .= '</a>' . "\n";
				$output .= '</li>' . "\n";
			}
		} else {
			// no posts found
		}

		$output .= '</ul>' . "\n";

		/* Restore original Post Data */
		wp_reset_postdata();

		return $output;

	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'] );
		}

		// list posts
		echo wp_kses_post( $this->get_data( $instance ) );

		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_html( $instance['title'] ); ?>">
		</p>

		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'cat' ) ); ?>"><?php _e( 'Category:' ); ?></label> 
		<?php
			$cat_args['name']             = $this->get_field_name( 'cat' );
			$cat_args['selected']         = esc_html( $instance['cat'] );
			$cat_args['show_option_none'] = 'No Category';

			wp_dropdown_categories( $cat_args );
		?>
		</p>

		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php _e( 'Number of posts to show:' ); ?></label> 
		<select id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'posts_per_page' ) ); ?>">

		<?php
			// make options 1-20
			$count = 1;
			$max   = 20;

			while ( $count <= $max ) {
				if ( $count == $instance['posts_per_page'] ) {
					$selected = ' selected="selected"';
				} else {
					$selected = '';
				}
				echo '<option value="' . absint( $count ) . '"' . esc_attr( $selected ) . '>' . absint( $count ) . '</option>' . "\n";
				$count++;
			}
		?>

		</select>
		</p>

		<?php

	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		$instance['title']          = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['cat']            = ( ! empty( $new_instance['cat'] ) ) ? strip_tags( $new_instance['cat'] ) : '';
		$instance['posts_per_page'] = ( ! empty( $new_instance['posts_per_page'] ) ) ? absint( $new_instance['posts_per_page'] ) : '';

		return $instance;
	}

} // class Future_Imperfect_Title_List_Widget