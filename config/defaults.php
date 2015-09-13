<?php namespace WPDC_Related_Articles;

/**
 * Related Service Providers
 *
 * @package     WPDC_Related_Articles
 * @since       1.0.0
 * @author      WPDevelopersClub and hellofromTonya
 * @link        https://wpdevelopersclub.com/
 * @license     GNU General Public License 2.0+
 */

return array(
	'number_posts'          => 4,
	'post_type'             => array( 'post' ),
	'post_info'             => array(
		'views'             => array(
			'main'          => WPDC_RELATED_ARTICLES_DIR . 'src/views/post-info.php',
		),
		'avatar_size'       => 32,
	),
	'patterns'              => array(
		'opening_tag'       => sprintf( '<section id="related-posts"><div class="wrap"><h4 class="related-title">%s</h4>', __( 'More Articles to Explore', 'wpdc' ) ),
		'closing_tag'       => '</div></section>',
	),


	/*********************************************************
	 * Extras
	 ********************************************************/

	'views' => array(
		'main'          => WPDC_RELATED_ARTICLES_DIR . 'src/views/related.php',
		'post_nav'      => WPDC_RELATED_ARTICLES_DIR . 'src/views/post-nav.php',
		'post_image'    => WPDC_RELATED_ARTICLES_DIR . 'src/views/post-image.php',
	),
);
