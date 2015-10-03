<?php namespace WPDC_Related_Articles;

/**
 * Related Service Providers
 *
 * @package     WPDC_Related_Articles
 * @since       1.2.0
 * @author      WPDevelopersClub and hellofromTonya
 * @link        https://wpdevelopersclub.com/
 * @license     GNU General Public License 2.0+
 */

use WPDevsClub_Core\Core;
use WPDevsClub_Core\Config\Arr_Config;
use WPDevsClub_Core\Models\Model;
use WPDevsClub_Core\Structures\Post\Post_Info;

$core = Core::getCore();

return array(

	/*********************************************************
	 * Initial Core Parameters, which are loaded into the
	 * Container before anything else occurs.
	 *
	 * Format:
	 *    $unique_id => $value
	 ********************************************************/

	'initial_parameters'            => array(
		'related.post_id'           => 0,
	),

	/*********************************************************
	 * Service Providers - Loaded into the Container
	 ********************************************************/

	'fe_service_providers'      => array(
		'related.model'         => array(
			'autoload'          => false,
			'concrete'          => $core->factory( function( $container ) {
				return new Model( new Arr_Config( $container['config']->model ), $container['related.post_id'] );
			} ),
		),
		'related.post_info' => array(
			'autoload'      => false,
			'concrete'      => $core->factory( function( $container ) {
				return new Post_Info(
					new Arr_Config( $container['config']->related['post_info'], $container['core_config_defaults_dir'] . 'structures/post-info.php' ),
					$container['related.model'],
					$container['related.post_id'],
					get_post_field( 'post_author', $container['related.post_id'] )
				);
			} ),
		),
	),
);
