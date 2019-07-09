<?php
namespace WPEngineShowPageTemplate\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://nathanonline.us
 * @since      1.0.0
 *
 * @package    WPEngine_Show_Page_Template
 * @subpackage WPEngine_Show_Page_Template/Admin
 * @author     Nathan Corbin <contact@nathanonline.us>
 */

class WPEngine_Show_Page_Template_Settings {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name) {

		$this->plugin_name = $plugin_name;
		
	}

	/**
	 * Adds the custom page template column into the row of columns
	 * that are displayed on the page list view.
	 * 
	 * @param  array $columns
	 * @todo  The method for checking if the table columns have been edited
	 * isn't great, should look into a more accurate way. If WP makes updates to the default cols this will "break".
	 * @return array          modified columns
	 */
	public function custom_page_column($columns) {

		//If there are other plugins or themes that have already edited
		//the column list we don't want to mess up anything so just add 
		//the template column at the end.
		$default_columns_array = array('cb' => '', 'title' => '', 'author' => '', 'comments' => '', 'date' => '');
		$result = array_diff_key($columns, $default_columns_array);

		if(!empty($result)) {
			$columns['template'] = __('Page Template', WPENGINE_SHOW_PAGE_TEMPLATE_TEXT_DOMAIN);
			return $columns;
		} else {
			$columns_new = array(
				'cb' => '<input type="checkbox">',
				'title' => 'Title',
				'template' => __('Page Template', WPENGINE_SHOW_PAGE_TEMPLATE_TEXT_DOMAIN),
				'author' => 'Author',
				'comments' => '<span class="vers comment-grey-bubble" title="Comments"><span class="screen-reader-text">Comments</span></span>',
				'date' => 'Date',
			);
		}

		return $columns_new;
	}

	/**
	 * Adds the custom page template column into the row of columns
	 * that are displayed on the page list view.
	 * 
	 * @param  array $columns
	 * @return array          modified columns
	 */
	public function custom_page_sortable_column($columns) {
		$columns['template'] = 'template';
		return $columns;
	}


	/**
	 * Prints out the content for the template page column
	 * 
	 * @param  string $column_name 
	 * @param  int $post_id     
	 * @return [type]              
	 */
	public function custom_page_column_content($column_name, $post_id) {
		if ($column_name == 'template') {
			echo $this->get_page_template_name();
		}
	}

	/**
	 * Appends an additional filter selection to the page view list
	 * @param  string $post_type 
	 * @param  string $which     
	 * @return [type]            
	 */
	public function list_filter_by_template($post_type, $which) {
		if($post_type != 'page') {
			return;
		}

		$templates = get_page_templates();
		?>
		<select name="template" id="template">
			<option value="">Show all Templates</option>
			<?php foreach ($templates as $key => $value) : ?>
				<?php $selected = isset($_GET['template']) && $_GET['template'] == $value ? 'selected="selected"' : ''; ?>
				<option value="<?= $value; ?>" <?= $selected; ?>><?= $key; ?></option>
			<?php endforeach; ?>
		</select>
		<?php 
	}

	/**
	 * Calls whenever a filter template item has been selected
	 * 
	 * @param  array $request 
	 * @return [type]          
	 */
	public function filter_by_template($request) {
		if(isset($_GET['template']) && !empty($_GET['template'])) {
			$template = sanitize_text_field($_GET['template']);

			//Set the filter parameters
		    $request['meta_key'] = '_wp_page_template';
		    $request['meta_value'] = $template;
		}
		return $request;
	}

	/**
	 * Shows the page template in the admin bar on the page preview.
	 * 
	 * @param  array $wp_admin_bar 
	 * @return array               
	 */
	public function admin_menu_show_page_template($wp_admin_bar) {
		//Dont show this on the admin pages, just the frontend view
		if(is_admin() == true) {
			return $wp_admin_bar;
		}

		$current_template = $this->get_page_template_name();

		$wp_admin_bar->add_menu( array(
			'id' => 'wpengine-show-page-template',
			'title' => "Template: <u>$current_template</u>",
		));

		return $wp_admin_bar;
	}

	/**
	 * Registers the JS and styling for page editor specific styling
	 *
	 * @todo Not specefic to this function but a JSX and SCSS
	 * compiler should be used.
	 */
	public function post_page_template_display() {
		if(get_post_type() != 'page') {
			return;
		}

		// Enqueue block editor JS
		wp_enqueue_script(
			'wpengine-show-page-template',
			plugins_url( '/js/page-template-sidebar.js', __FILE__ ),
			[ 'wp-element', 'wp-components', 'wp-i18n', 'wp-plugins', 'wp-edit-post' ],
			filemtime( plugin_dir_path( __FILE__ ) . 'admin/js/page-template-sidebar.js' ) 
		);

		// Include the current page template to display
		$wp_template = $this->get_page_template_name();
		wp_localize_script('wpengine-show-page-template', 'wp_template', $wp_template);

		// Enqueue the styling for page template display
		wp_enqueue_style(
			'wpengine-show-page-template-css',
			plugins_url( '/css/wpengine-show-page-template.css', __FILE__ )
		);
	}

	/**
	 * Gets the page template name.
	 * 
	 * @param  int $post_id optional, if not provided will use WP id
	 * @return string          The page template name
	 */
	private function get_page_template_name($post_id = 0) {
		$post_id = ($post_id > 0) ? $post_id : get_the_ID();
		$templates = wp_get_theme()->get_page_templates();
		$current_template = get_page_template_slug($post_id);

		//Go through all the templates to get the actual name
		//instead of printing out 'template.php' it prints Template.
		foreach ($templates as $key => $value) {
			if($key == $current_template) {
				return $value;
			}
		}

		return 'Default';
	}
}