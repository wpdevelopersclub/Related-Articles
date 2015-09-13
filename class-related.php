<?php namespace WPDC_Related_Articles;

/**
 * Related Article Plugin
 *
 * @package     WPDC_Related_Articles
 * @since       1.1.0
 * @author      WPDevelopersClub and hellofromTonya
 * @link        https://wpdevelopersclub.com/
 * @license     GNU General Public License 2.0+
 * @copyright   2015 WP Developers Club
 */

// Oh no you don't. Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Cheatin&#8217; uh?' );
}

use WPDevsClub_Core\Addons\Addon;
use WPDevsClub_Core\Config\I_Config;
use WPDevsClub_Core\Models\I_Model;
use WPDC_Related_Articles\Structures\Related as Related_Articles;

final class Related extends Addon {

	/**
	 * The plugin's version
	 *
	 * @var string
	 */
	const VERSION = '1.1.0';

	/**
	 * The plugin's minimum WordPress requirement
	 *
	 * @var string
	 */
	const MIN_WP_VERSION = '3.5';

	/*************************
	 * Instantiate & Init
	 ************************/

	/**
	 * Addons can overload this method for additional functionality
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_addon() {
		$this->load_service_providers();
	}

	/**
	 * Addons can overload this method for additional functionality
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_events() {
		add_action( 'wpdevsclub_do_related_articles', array( $this, 'init_related' ), 10, 4 );
	}

	/**
	 * Instantiate the class
	 *
	 * @since 1.0.0
	 *
	 * @param I_Config $config Configuration parameters
	 * @param I_Model $model Data model
	 * @param int $post_id Post ID
	 * @return null
	 */
	function init_related( I_Config $config, I_Model $model, $post_id  ) {
		new Related_Articles( $config, $model, $post_id, $this->core );
	}
}