<?php namespace WPDC_Related_Articles;

/**
 * WP Developers Club Related Articles
 *
 * @package     WPDC_Related_Articles
 * @author      WPDevelopersClub and hellofromTonya
 * @license     GPL-2.0+
 * @link        http://wpdevelopersclub.com/
 * @copyright   2015 WP Developers Club
 *
 * @wordpress-plugin
 * Plugin Name:     WP Developers Club Related Articles
 * Plugin URI:      http://wpdevelopersclub.com/
 * Description:     Give your audience more content choices by adding related articles to your posts.
 * Version:         1.0.0
 * Author:          WP Developers Club and Tonya
 * Author URI:      http://wpdevelopersclub.com
 * Text Domain:     wpdevsclub
 * Requires WP:     3.5
 * Requires PHP:    5.4
 */

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

use WPDevsClub_Core\Models\I_Model;

// Oh no you don't. Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Cheating&#8217; uh?' );
}

if ( ! defined( 'WPDC_RELATED_ARTICLES_DIR' ) ) {
	define( 'WPDC_RELATED_ARTICLES_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WPDC_RELATED_ARTICLES_URL' ) ) {
	$plugin_url = plugin_dir_url( __FILE__ );
	if ( is_ssl() ) {
		$plugin_url = str_replace( 'http://', 'https://', $plugin_url );
	}
	define( 'WPDC_RELATED_ARTICLES_URL', $plugin_url );
}

//require_once( __DIR__ . '/assets/vendor/autoload.php' );
require_once( __DIR__ . '/class-related.php' );

if ( version_compare( $GLOBALS['wp_version'], Related::MIN_WP_VERSION, '>' ) ) {

	add_action( 'wpdevsclub_do_related_articles', __NAMESPACE__ . '\\init_related', 10, 3 );
	/**
	 * Instantiate the class
	 *
	 * @since 1.0.0
	 *
	 * @param I_Model   $model          Data model
	 * @param int       $post_id        Post ID
	 * @param array     $config         Configuration parameters
	 * @return null
	 */
	function init_related( I_Model $model, $post_id, array $config = array() ) {
		new Related( $model, $post_id, $config );
	}
}