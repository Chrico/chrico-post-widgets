<?php
/**
 * Plugin Name: Chrico Post Widgets
 * Description: This Plugin adds some posts-widgets to your theme.
 * Plugin URI:  https://www.chrico.info
 * Version:     0.1
 * Author:      Christian Brückner
 * Author URI:  https://www.chrico.info
 * Licence:     GPLv3
 */
namespace ChriCo\PostWidgets;

if ( ! function_exists( 'add_filter' ) ) {
	return;
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\run' );

/**
 * Init of our plugin to register the widgets.
 *
 * @wp-hook plugins_loaded
 *
 * @return void
 */
function run() {

	// registering the widgets
	add_action( 'widgets_init', __NAMESPACE__ . '\register_widgets' );

}

/**
 * registering the widgets
 *
 * @wp-hook widgets_init
 *
 * @return void
 */
function register_widgets() {

	// ByCategory
	include_once( __DIR__ . '/inc/widget/ByCategory.php' );
	register_widget( '\ChriCo\PostWidgets\Widget\ByCategory' );

	// Author Posts
	include_once( __DIR__ . '/inc/widget/AuthorPosts.php' );
	register_widget( '\ChriCo\PostWidgets\Widget\AuthorPosts' );

}
