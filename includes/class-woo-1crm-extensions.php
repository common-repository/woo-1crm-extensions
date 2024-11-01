<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.visual4.de/
 * @since      1.0.0
 *
 * @package    Woo_1crm_Extensions
 * @subpackage Woo_1crm_Extensions/includes
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
 * @package    Woo_1crm_Extensions
 * @subpackage Woo_1crm_Extensions/includes
 * @author     visual4 GmbH <info@visual4.de>
 */
class Woo_1crm_Extensions {

    /**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Woo_1crm_Extensions_Loader    $loader    Maintains and registers all hooks for the plugin.
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
     * The Webhookhandler Class.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Class_Woo_1crm_Extensions_Webhookhandler    $webhookhandler
     */
    protected $webhookhandler;

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

		$this->plugin_name = 'woo-1crm-extensions';
		$this->version = '1.0.1';

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
	 * - Woo_1crm_Extensions_Loader. Orchestrates the hooks of the plugin.
	 * - Woo_1crm_Extensions_i18n. Defines internationalization functionality.
	 * - Woo_1crm_Extensions_Admin. Defines all hooks for the admin area.
	 * - Woo_1crm_Extensions_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-1crm-extensions-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woo-1crm-extensions-i18n.php';

        /**
         * The class responsible for handling all webhooks.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woo-1crm-extensions-webhookhandler.php';

        $this->webhookhandler = new Class_Woo_1crm_Extensions_Webhookhandler();

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woo-1crm-extensions-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woo-1crm-extensions-public.php';

        /**
         * Additional Fields
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woo-1crm-extensions-public-helper.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woo-1crm-extensions-public-setting.php';


        $this->loader = new Woo_1crm_Extensions_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Woo_1crm_Extensions_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Woo_1crm_Extensions_i18n();

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

		$plugin_admin = new Woo_1crm_Extensions_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// add Endpoint
        $this->loader->add_action('woocommerce_api_loaded', $plugin_admin, 'include_endpointhandler_class');
        $this->loader->add_filter('woocommerce_api_classes', $plugin_admin, 'add_endpointhandler_class_to_wc');

        // webhook response send error message via mail to admin
        $this->loader->add_filter('woocommerce_settings_rest_api', $plugin_admin, 'add_errorhandling_option_to_wc_settings');
        $this->loader->add_action('woocommerce_webhook_delivery', $plugin_admin, 'send_mail_if_webhook_fails', 10, 2);

        // add prices with all decimals to order
        $this->loader->add_filter( 'woocommerce_api_order_response', $plugin_admin, 'add_raw_prices_to_order_webhook', 10, 4);

        // additional fields
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_menue' );
        $this->loader->add_action('admin_init', $plugin_admin, 'register_woo1crmadditionalfields_plugin_settings' );

    }

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Woo_1crm_Extensions_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// additional fields
        $this->loader->add_action('woocommerce_webhook_http_args', $plugin_public, 'add_http_args_to_woocommerce_webhook');
        $this->loader->add_action('woocommerce_checkout_update_order_meta', $plugin_public, 'custom_order_field_update' );
        $this->loader->add_action('woocommerce_checkout_fields', $plugin_public, 'display_custom_fields_in_checkout' );

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
	 * @return    Woo_1crm_Extensions_Loader    Orchestrates the hooks of the plugin.
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
