<?php
namespace ChriCo\PostWidgets\Widget;

/**
 * Class AuthorPosts
 *
 * @package ChriCo\PostWidgets\Widget
 */
class AuthorPosts extends \WP_Widget {

	/**
	 * Start the widget.
	 *
	 * @return AuthorPosts
	 */
	public function __construct() {

		parent::__construct(
			'chrico-post-widgets__author-posts',
			_x( 'Chrico Author Posts', 'widget title', 'chrico-post-widgets' ),
			array(
				'classname'   => 'chrico-post-widgets__author-posts',
				'description' => __(
					'The widget shows on single pages more posts from the current Author', 'chrico-post-widgets'
				)
			)
		);

	}

	/**
	 * Widget output.
	 *
	 * @param    array $args
	 * @param    array $instance
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {

		if ( ! is_single() ) {
			return;
		}

		$author_id = get_the_author_meta( 'ID' );
		if ( ! $author_id ) {
			return;
		}

		$post_args = array(
			"numberposts"  => $instance[ 'numberposts' ],
			"author"       => $author_id,
			"post__not_in" => array( get_the_ID() )
		);
		$posts     = get_posts( $post_args );
		if ( count( $posts ) < 1 ) {
			return;
		}

		// Title
		if ( empty( $instance[ 'title' ] ) ) {
			$author_name = get_the_author_meta( 'display_name', $author_id );
			$title       = sprintf(
				__( 'More posts from %s', 'inpsyde-post-widgets' ),
				$author_name
			);
		} else {
			$title = $instance[ 'title' ];
		}
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$output = $args[ 'before_widget' ];
		if ( $title ) {
			$output .= $args[ 'before_title' ] . $title . $args[ 'after_title' ];
		}

		$output .= '<ul class="' . esc_attr( $this->id_base . '__list' ) . '">';
		foreach ( $posts as $post ) :

			$classes = array( esc_attr( $this->id_base . '__item' ) );

			$categories = get_the_category( $post->ID );
			foreach ( $categories as $category ) :
				$classes[] = esc_attr( $this->id_base . '__item--' . $category->slug );
			endforeach;

			$output .= '<li class="' . implode( " ", $classes ) . '">';
			$output .= '<a class="' . esc_attr( $this->id_base . '__link' ) . '" href="' . get_permalink( $post->ID ) . '">';
			$output .= '<span class="' . esc_attr( $this->id_base . '__title' ) . '">' . $post->post_title . '</span>';
			$output .= '</a>';
			$output .= '</li>';

		endforeach;
		$output .= '</ul>';
		$output .= $args[ 'after_widget' ];

		echo $output;
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
