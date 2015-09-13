<?php namespace WPDC_Related_Articles\Structures;

/**
 * Related Article
 *
 * @package     WPDC_Related_Articles\Structures
 * @since       1.1.0
 * @author      WPDevelopersClub and hellofromTonya
 * @link        https://wpdevelopersclub.com/
 * @license     GNU General Public License 2.0+
 * @copyright   2015 WP Developers Club
 */

use WP_Query;
use WPDevsClub_Core\I_Config;
use WPDevsClub_Core\Support\Structure;

class Related extends Structure {

	/**
	 * The plugin's version
	 *
	 * @var string
	 */
	const VERSION = '1.0.0';

	/**
	 * The plugin's minimum WordPress requirement
	 *
	 * @var string
	 */
	const MIN_WP_VERSION = '3.5';

	/*************************
	 * Getters
	 ************************/

	public function version() {
		return self::VERSION;
	}

	public function min_wp_version() {
		return self::MIN_WP_VERSION;
	}

	/**************************
	 * Instantiate & Initialize
	 *************************/

	/**
	 * Initialize the events
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_events() {
		add_action( 'genesis_after_entry', array( $this, 'render_post_nav' ), 8 );
		add_action( 'genesis_after_entry', array( $this, 'render_related' ), 9 );
	}

	/**
	 * renders the Post Navigation (prev/next)
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	public function render_post_nav() {
		$this->load_view( 'post_nav' );
	}

	/**
	 * Query the related posts.  If any are present, render out the HTML
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	public function render_related() {
		$query_args = $this->get_query_args( $this->build_initial_args(), $this->get_categories() );
		if ( false === $query_args ) {
			return;
		}

		$query = new WP_Query( $query_args );

		if ( $query->have_posts() ) :

			echo $this->config->patterns['opening_tag'];

			while ( $query->have_posts() ) : $query->the_post();
				$this->core['related.post_id'] = get_the_ID();
				$post_info = $this->core['related.post_info'];

				$this->load_view( 'main' );
			endwhile;

			echo $this->config->patterns['closing_tag'];

			wp_reset_postdata();
		endif;
	}

	/**
	 * Checks if there is an adjacent post
	 *
	 * @since 1.0.0
	 *
	 * @param bool $prev
	 * @return bool
	 */
	public function has_adjacent_post( $prev = true ) {
		$response = $this->model->has_adjacent_post( $prev );
		return $response;
	}

	/**
	 * Build the query args for the related posts loop
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Query Args
	 * @param mixed $categories
	 * @return array|false
	 */
	protected function get_query_args( array $args, $categories ) {
		if ( false === $categories ) {
			return false;
		}

		$this->build_tax_query( $args, $categories );
		$this->build_categories_query( $args, $categories );

		return $args;
	}

	/**
	 * Builds the initial query args
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	protected function build_initial_args() {
		return array(
			'post_type'         => $this->config->post_type,
			'posts_per_page'    => $this->config->number_posts,
			'post__not_in'      => array( $this->post_id ),
			'orderby'           => 'rand',
		);
	}

	/**
	 * Build the Tax Query, if applicable
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 * @param mixed $categories
	 * @return null
	 */
	protected function build_tax_query( array &$args, $categories ) {
		$tags = wp_get_post_tags( $this->post_id, array( 'fields' => 'ids' ) );
		if ( ! is_array( $tags ) || empty( $tags ) ) {
			return;
		}

		$args['tax_query'] = array(
			'relation'      => 'OR',
			array(
				'taxonomy'  => 'category',
				'field'     => 'id',
				'terms'     => $categories,
				'operator'  => 'IN',
			),
			array(
				'taxonomy'  => 'post_tag',
				'field'     => 'id',
				'terms'     => $tags,
				'operator'  => 'IN',
			),
		);
	}

	/**
	 * Build the Tax Query, if applicable
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 * @param mixed $categories
	 * @return null
	 */
	protected function build_categories_query( array &$args, $categories ) {
		if ( ! array_key_exists( 'tax_query', $args ) ) {
			$args['category__in'] = $categories;
		}
	}

	/**
	 * Get post categories
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_categories() {
		$ids = array();
		$categories = get_the_category( $this->post_id );
		foreach ( $categories as $category ) {
			$ids[] = $category->cat_ID;
		}

		return $ids;
	}

	/**
	 * Render Post Info HTML
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function render_subtitle() {
		return $this->core['related.model']->get_subtitle();
	}

	/**
	 * Render Post Info HTML
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function render_post_info() {
		echo do_shortcode( $this->core['related.post_info']->render() );
	}

	/**
	 * Render TL;DR
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function render_tldr() {
		$content = wp_kses_post( $this->core['related.model']->get_meta('_tldr') );
		echo wpdevsclub_word_limiter( $content );
	}

	/**
	 * Echo the post image on archive pages.
	 *
	 * If this an archive page and the option is set to show thumbnail, then it gets the image size as per the theme
	 * setting, wraps it in the post permalink and echoes it.
	 *
	 * @since 1.0.0
	 *
	 * @uses genesis_get_option() Get theme setting value.
	 * @uses genesis_get_image()  Return an image pulled from the media library.
	 * @uses genesis_parse_attr() Return contextual attributes.
	 */
	protected function do_post_image() {
		$img = genesis_get_image( array(
			'format'  => 'html',
			'size'    => 'thumbnail',
			'context' => 'archive',
			'attr'    => genesis_parse_attr( 'entry-image' ),
		) );

		if ( ! empty( $img ) ) {
			$this->load_view( 'post_image', compact( 'img' ) );
		}
	}
}