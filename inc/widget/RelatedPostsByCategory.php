<?php
namespace ChriCo\PostWidgets\Widget;

/**
 * Class AuthorPosts
 *
 * @package ChriCo\PostWidgets\Widget
 */
class RelatedPostsByCategory extends \WP_Widget {

	/**
	 * Start the widget.
	 *
	 * @return RelatedPostsByCategory
	 */
	public function __construct() {

		parent::__construct(
			'chrico-post-widgets__related-posts-by-category',
			_x( 'ChriCo Related Posts By Category', 'widget title', 'chrico-post-widgets' ),
			array(
				'classname'   => 'chrico-post-widgets__related-posts-by-category',
				'description' => __(
					'The widget shows on single pages related posts by category', 'chrico-post-widgets'
				)
			)
		);
	}

	/**
	 * Widget output.
	 *
	 * @param    array $args
	 * @param    array $instance
	 */
	public function widget( $args, $instance ) {

		if ( ! is_single() ) {
			return;
		}
		// set post id
		$post_id = get_the_ID();
		// get current tags
		$terms = wp_get_post_categories( $post_id );
		if ( ! $terms ) {
			return;
		}
		// get the term
		$term = get_term( $terms[ 0 ], 'category' );
		if ( is_wp_error( $term ) ) {
			return;
		}
		// set query args
		$post_args = array(
			'category__in'         => array( $term->term_id ),
			'post__not_in'         => array( $post_id ),
			'posts_per_page'       => $instance[ 'numberposts' ],
			'ignore_sticky_posts ' => 1
		);
		$posts     = get_posts( $post_args );
		if ( empty( $posts ) ) {
			return;
		}
		// Title
		if ( empty( $instance[ 'title' ] ) ) {
			$title = sprintf(
				__( 'Weitere Beiträge aus %s', 'chrico-post-widgets' ),
				$term->name
			);
		} else {
			$title = $instance[ 'title' ];
		}
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$output = $args[ 'before_widget' ];
		if ( $title ) {
			$output .= $args[ 'before_title' ] . $title . $args[ 'after_title' ];
		}
		// getting the slug for the classes...

		$output .= '<ul class="' . esc_attr( $this->id_base . '__list' ) . '">';
		foreach ( $posts as $post ) {
			$output .= '<li class="' . esc_attr( $this->id_base . '__item' ) . '">';
			$output .= '<a class="' . esc_attr( $this->id_base . '__link' ) . '" href="' . get_permalink(
					$post->ID
				) . '">';
			$output .= '<span class="' . esc_attr( $this->id_base . '__title' ) . '">' . $post->post_title . '</span>';
			$output .= '</a>';
			$output .= '</li>';
		}
		$output .= '</ul>';
		$output .= $args[ 'after_widget' ];

		echo $output;
	}

	/**
	 * Saves widget settings.
	 *
	 * @param    array $new_instance
	 * @param    array $old_instance
	 *
	 * @return    array
	 */
	function update( $new_instance, $old_instance ) {

		$instance                  = $old_instance;
		$instance[ 'title' ]       = strip_tags( $new_instance[ 'title' ] );
		$instance[ 'numberposts' ] = absint( $new_instance[ 'numberposts' ] );

		return $instance;
	}

	/**
	 * Prints the settings form.
	 *
	 * @param    array $instance
	 *
	 * @return    void
	 */
	function form( $instance ) {

		$defaults = array(
			'title'       => '',
			'numberposts' => 6,
		);

		$instance = wp_parse_args(
			(array) $instance,
			$defaults
		);
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php _ex( 'Title', 'widget title label', 'chrico-post-widgets' ) ?>
			</label>
			<input class="widefat"
				id="<?php echo $this->get_field_id( 'title' ); ?>"
				name="<?php echo $this->get_field_name( 'title' ); ?>"
				type="text"
				value="<?php echo esc_attr( $instance[ 'title' ] ); ?>"
			/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'numberposts' ); ?>">
				<?php _e( 'Number of posts to show:', 'chrico-post-widgets' ) ?>
			</label>
			<input class="widefat"
				id="<?php echo $this->get_field_id( 'numberposts' ); ?>"
				name="<?php echo $this->get_field_name( 'numberposts' ); ?>"
				type="number"
				min="1"
				step="1"
				value="<?php echo esc_attr( $instance[ 'numberposts' ] ); ?>"
				style="width:50px"
			/>
		</p>
		<?php
	}
}