<?php namespace WPDC_Related_Articles;

/**
 * Related Article
 *
 * @package     WPDC_Related_Articles
 * @since       1.0.0
 * @author      WPDevelopersClub and hellofromTonya
 * @link        http://wpdevelopersclub.com/
 * @license     GNU General Public License 2.0+
 * @copyright   2015 WP Developers Club
 */

use WP_Query;
use WPDevsClub_Core\Support\Base;
use WPDevsClub_Core\Models\I_Model;
use WPDevsClub_Core\Models\Base as Model;
use WPDevsClub_Core\Structures\Post\Post_Info;

class Related extends Base {

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

	protected $related = array();

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
	 * Instantiate the class
	 *
	 * @since 1.0.0
	 *
	 * @param I_Model   $model          Data model
	 * @param int       $post_id        Post ID
	 * @param array     $config         Configuration parameters
	 * @return self
	 */
	public function __construct( I_Model &$model, $post_id, array $config = array() ) {
		$this->init_config( $config );
		$this->model        = $model;
		$this->post_id      = $post_id;

		$this->init_hooks();
	}

	/**
	 * Initialize the hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_hooks() {
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

		$query_args = $this->get_query_args();
		if ( false === $query_args ) {
			return;
		}

		$query = new WP_Query( $query_args );

		if ( $query->have_posts() ) :

			echo $this->config['patterns']['opening_tag'];

			while ( $query->have_posts() ) : $query->the_post();

				$post_id = get_the_ID();
				$this->related['model']     = new Model( $this->model->get_config(), $post_id );
				$this->related['post_info'] = new Post_Info( $this->related['model'], $this->config['post_info'], $post_id, get_the_author_meta( 'ID' ) );

				$this->load_view( 'main' );

				$this->related = array();

			endwhile;

			echo $this->config['patterns']['closing_tag'];

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
	 * @return array|false
	 */
	protected function get_query_args() {
		$categories = $this->get_categories();
		if ( false === $categories ) {
			return false;
		}

		$args = array(
			'post_type'         => $this->config['post_type'],
			'posts_per_page'    => $this->config['number_posts'],
			'post__not_in'      => array( $this->post_id ),
			'orderby'           => 'rand',
		);

		$tags = wp_get_post_tags( $this->post_id, array( 'fields' => 'ids' ) );
		if ( is_array( $tags ) || ! empty( $tags ) ) {
			$args['tax_query'] = array(
				'relation'  => 'OR',
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
		} else {
			$args['category__in'] = $categories;
		}

		return $args;
	}

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
		return $this->related['model']->get_subtitle();
	}

	/**
	 * Render Post Info HTML
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function render_post_info() {
		echo do_shortcode( $this->related['post_info']->render() );
	}

	/**
	 * Render TL;DR
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function render_tldr() {
		$content = wp_kses_post( $this->related['model']->get_meta('_tldr') );
		echo fulcrum_word_limiter( $content );
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

	/**
	 * Initialize the configuration parameters
	 *
	 * @since 1.0.0
	 *
	 * @param array     $config     Configuration parameters
	 * @return null
	 */
	protected function init_config( array $config ) {
		$this->config = wp_parse_args(
			$config,
			array(
				'views'             => array(
					'main'          => WPDC_RELATED_ARTICLES_DIR . 'lib/views/related.php',
					'post_nav'      => WPDC_RELATED_ARTICLES_DIR . 'lib/views/post-nav.php',
					'post_image'    => WPDC_RELATED_ARTICLES_DIR . 'lib/views/post-image.php',
				),
				'number_posts'      => 4,
				'post_type'         => 'post',
				'post_info'         => array(
					'views'         => array(
						'main'      => WPDC_RELATED_ARTICLES_DIR . 'lib/views/post-info.php',
					),
					'avatar_size'   => 32,
				),
				'patterns'          => array(
					'opening_tag'   => sprintf( '<section id="related-posts"><div class="wrap"><h4 class="related-title">%s</h4>', __( 'More Articles to Explore', 'wpdevsclub' ) ),
					'closing_tag'   => '</div></section>',
				),
			)
		);
	}
}