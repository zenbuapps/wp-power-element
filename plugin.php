<?php
/**
 * Plugin Name:       Power Element | 一些自製的 Shortcode, Elementor Widget
 * Plugin URI:        https://cloud.luke.cafe/plugins/
 * Description:       your description
 * Version:           0.0.1
 * Requires at least: 5.7
 * Requires PHP:      8.0
 * Author:            Your Name
 * Author URI:        [YOUR GITHUB URL]
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       power_element
 * Domain Path:       /languages
 * Tags: your tags
 */

declare (strict_types = 1);

namespace J7\PowerElement;

if ( \class_exists( 'J7\PowerElement\Plugin' ) ) {
	return;
}
require_once __DIR__ . '/vendor/autoload.php';

/**
	* Class Plugin
	*/
final class Plugin {
	use \J7\WpUtils\Traits\PluginTrait;
	use \J7\WpUtils\Traits\SingletonTrait;

	/**
	 * Constructor
	 */
	public function __construct() {

		// $this->required_plugins = array(
		// array(
		// 'name'     => 'WooCommerce',
		// 'slug'     => 'woocommerce',
		// 'required' => true,
		// 'version'  => '7.6.0',
		// ),
		// array(
		// 'name'     => 'WP Toolkit',
		// 'slug'     => 'wp-toolkit',
		// 'source'   => 'Author URL/wp-toolkit/releases/latest/download/wp-toolkit.zip',
		// 'required' => true,
		// ),
		// );

		$this->init(
			[
				'app_name'    => 'Power Element',
				'github_repo' => '[YOUR GITHUB URL]/power-element',
				'callback'    => [ Bootstrap::class, 'instance' ],
			]
		);
	}
}

Plugin::instance();
