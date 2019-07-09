<?php
namespace WPEngineShowPageTemplate;

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://nathanonline.us
 * @since             1.0.0
 * @package           WPEngine_Show_Page_Template
 *
 * @wordpress-plugin
 * Plugin Name:       WPEngine Show Page Template
 * Plugin URI:        http://nathanonline.us
 * Description:       This Wordpress plugin shows the current page template in a more visible manner for admins. It also extends the functionality of the page list view to allow sorting and filtering by the page template.
 * Version:           1.0.0
 * Author:            Nathan Corbin
 * Author URI:        http://nathanonline.us
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpengine-show-page-template
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'WPENGINE_SHOW_PAGE_TEMPLATE_VERSION', '1.0.0' );

/**
 * Plugin text domain
 */
define( 'WPENGINE_SHOW_PAGE_TEMPLATE_TEXT_DOMAIN', 'wpengine-show-page-template');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpengine-show-page-template.php';

/**
 * The loader plugin class.
 *
 * This is the loader class for the plugin
 *
 * @since      1.0.0
 * @package    WPEngine_Show_Page_Template
 * @subpackage WPEngine_Show_Page_Template
 * @author     Nathan Corbin <contact@nathanonline.us>
 */
class WPEngine_Show_Page_Template_Plugin {

	private $plugin;

	/**
	 * Construct the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		register_activation_hook( __FILE__, array($this, 'activate_wpengine_show_page_template'));
		register_deactivation_hook( __FILE__, array($this, 'deactivate_wpengine_show_page_template' ));

		// Assign the private plugin and begin execution of the plugin.
		$this->plugin = new WPEngine_Show_Page_Template();
		$this->plugin->run();
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-wpengine-show-page-template-activator.php
	 */
	public function activate_wpengine_show_page_template() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpengine-show-page-template-activator.php';
		WPEngine_Show_Page_Template_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-wpengine-show-page-template-deactivator.php
	 */
	public function deactivate_wpengine_show_page_template() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpengine-show-page-template-deactivator.php';
		WPEngine_Show_Page_Template_Deactivator::deactivate();
	}

}

// Start the plugin
$wpengine_show_page_template = new WPEngine_Show_Page_Template_Plugin();
