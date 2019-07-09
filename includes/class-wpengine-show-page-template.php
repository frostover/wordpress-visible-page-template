<?php
namespace WPEngineShowPageTemplate;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://nathanonline.us
 * @since      1.0.0
 *
 * @package    WPEngine_Show_Page_Template
 * @subpackage WPEngine_Show_Page_Template/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WPEngine_Show_Page_Template
 * @subpackage WPEngine_Show_Page_Template/includes
 * @author     Nathan Corbin <contact@nathanonline.us>
 */
class WPEngine_Show_Page_Template {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WPEngine_Show_Page_Template_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if(defined('WPENGINE_SHOW_PAGE_TEMPLATE_VERSION')) {
			$this->version = WPENGINE_SHOW_PAGE_TEMPLATE_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		if(!defined('WPENGINE_SHOW_PAGE_TEMPLATE_TEXT_DOMAIN')) {
			define( 'WPENGINE_SHOW_PAGE_TEMPLATE_TEXT_DOMAIN', 'wpengine-show-page-template');
		}

		$this->plugin_name = 'wpengine-show-page-template';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WPEngine_Show_Page_Template_Loader. Orchestrates the hooks of the plugin.
	 * - WPEngine_Show_Page_Template_i18n. Defines internationalization functionality.
	 * - WPEngine_Show_Page_Template_Admin. Defines all hooks for the admin area.
	 * - WPEngine_Show_Page_Template_Settings. Loads custom WooCommerce settings.
	 * - WPEngine_Show_Page_Template_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpengine-show-page-template-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpengine-show-page-template-i18n.php';

		/**
		 * The class establishes the settings
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wpengine-show-page-template-settings.php';

		$this->loader = new WPEngine_Show_Page_Template_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WPEngine_Show_Page_Template_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WPEngine_Show_Page_Template_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$admin = new Admin\WPEngine_Show_Page_Template_Settings($this->get_plugin_name());

		//Custom column on page list view to show the template
		$this->loader->add_filter('manage_pages_columns', $admin, 'custom_page_column', 100);
		$this->loader->add_filter('manage_edit-page_sortable_columns', $admin, 'custom_page_sortable_column');
		$this->loader->add_action('manage_pages_custom_column', $admin, 'custom_page_column_content', 10, 2);

		//Add filter option by page template
		$this->loader->add_action('restrict_manage_posts', $admin, 'list_filter_by_template', 10, 2);
		$this->loader->add_filter('request', $admin, 'filter_by_template');

		//Add page template info to the admin bar on page preview
		$this->loader->add_action('admin_bar_menu', $admin, 'admin_menu_show_page_template', 100);

		//Add in the page editor specific js and styling
		$this->loader->add_action('enqueue_block_editor_assets', $admin, 'post_page_template_display');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WPEngine_Show_Page_Template_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}