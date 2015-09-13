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

use WPDevsClub_Core\I_Core;
use WPDevsClub_Core\Config\I_Config;

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

require_once( __DIR__ . '/assets/vendor/autoload.php' );

add_action( 'wpdevclub_setup_related_articles', __NAMESPACE__ . '\\launch', 20 );
/**
 * Instantiate the class
 *
 * @since 1.0.0
 *
 * @param I_Config $config Configuration parameters
 * @param I_Core $core Instance of Core
 * @return null
 */
function launch( I_Config $config, I_Core $core = null  ) {
	if ( version_compare( $GLOBALS['wp_version'], Related::MIN_WP_VERSION, '>' ) ) {
		new Related( $config, $core );
	}
}

/**
 * Load up the plugin's variables within the Container
 *
 * @since 1.1.0
 *
 * @return null
 */
add_action( 'wpdevsclub_do_service_providers', function( $core ) {
	$core['related.dir'] = WPDC_RELATED_ARTICLES_DIR;
	$core['related.url'] = WPDC_RELATED_ARTICLES_URL;
	$core['related.config.plugin'] = WPDC_RELATED_ARTICLES_DIR . 'config/plugin.php';
	$core['related.config.defaults'] = WPDC_RELATED_ARTICLES_DIR . 'config/defaults.php';
} );