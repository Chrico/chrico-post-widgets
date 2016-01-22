<?php
namespace ChriCo\RelatedPosts\Widget;

/**
 * Class ByCategory
 *
 * @package ChriCo\RelatedPosts\Widget
 */
class ByCategory extends \WP_Widget {

	/**
	 * Start the widget.
	 *
	 * @return ByCategory
	 */
	public function __construct() {

		parent::__construct(
			'chrico-related-posts-by-category',
			_x( 'ChriCo Related Posts By Category', 'widget title', 'chrico-related-posts' ),
			array(
				'classname'   => 'chrico-related-posts-by',
				'description' => __(
					'The widget shows on single pages related posts by category', 'chrico-related-posts'
				)
			)
		);

	}

	/**
	 * Widget output.
	 *
	 * @since     1.0
	 * @access    public
	 *
	 * @param    array $args
	 * @param    array $instance
	 */
	public function widget( $args, $instance ) {

		// widget should only work on single pages!
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
				__( 'Weitere Beiträge aus %s', 'chrico-related-posts' ),
				$term->name
			);
		} else {
			$title = $instance[ 'title' ];
		}
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		// before Widget
		echo $args[ 'before_widget' ];
		if ( $title ) {
			echo $args[ 'before_title' ] . $title . $args[ 'after_title' ];
		}

		// getting the slug for the classes...
		$slug = 'chrico-releated-posts-by-category';

		echo '<ul class="' . esc_attr( $slug ) . '-list">';
		foreach ( $posts as $post ) {
			echo '<li class="' . esc_attr( $slug ) . '-list-item">';
			echo '<a rel="nofollow" href="' . get_permalink( $post->ID ) . '">';
			echo '<span class="' . esc_attr( $slug ) . '-post-title">' . $post->post_title . '</span>';
			echo '</a>';
			echo '</li>';
		}
		echo '</ul>';
		echo $args[ 'after_widget' ];
	}

	/**
	 * Saves widget settings.
	 *
	 * @since     1.0
	 * @access    public
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
	 * @since     1.0
	 * @access    public
	 *
	 * @param    array $instance
	 *
	 * @return    void
	 */
	function form( $instance ) {

		global $wp_roles;

		// Defaults
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title'       => '',
				'numberposts' => 6,
			)
		);

		$the_title       = esc_attr( $instance[ 'title' ] );
		$the_numberposts = esc_attr( $instance[ 'numberposts' ] );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php _ex( 'Title', 'widget title label', 'chrico-related-posts' ) ?>
			</label>
			<input class="widefat"
				id="<?php echo $this->get_field_id( 'title' ); ?>"
				name="<?php echo $this->get_field_name( 'title' ); ?>"
				type="text"
				value="<?php echo esc_attr( $the_title ); ?>"
			/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'numberposts' ); ?>">
				<?php _e( 'Number of posts to show:', 'chrico-related-posts' ) ?>
			</label>
			<input class="widefat"
				id="<?php echo $this->get_field_id( 'numberposts' ); ?>"
				name="<?php echo $this->get_field_name( 'numberposts' ); ?>"
				type="number"
				min="1"
				step="1"
				value="<?php echo esc_attr( $the_numberposts ); ?>"
				style="width:50px"
			/>
		</p>
		<?php
	}

}
