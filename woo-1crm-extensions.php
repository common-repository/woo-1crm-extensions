<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.visual4.de/
 * @since             1.0.0
 * @package           Woo_1crm_Extensions
 *
 * @wordpress-plugin
 * Plugin Name:       Woo - 1CRM Extensions
 * Plugin URI:        http://www.visual4.de/woo-1crm-extensions/
 * Description:       This plugin extends WooCommerce Checkout Process by custom fields an functions, defined in admin backend and and passes all informations to the WooCommerce-1CRM Interface.
 * Version:           1.0.1
 * Author:            visual4 GmbH
 * Author URI:        http://www.visual4.de/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-1crm-extensions
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woo-1crm-extensions-activator.php
 */
function activate_woo_1crm_extensions() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-1crm-extensions-activator.php';
	Woo_1crm_Extensions_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo-1crm-extensions-deactivator.php
 */
function deactivate_woo_1crm_extensions() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woo-1crm-extensions-deactivator.php';
	Woo_1crm_Extensions_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woo_1crm_extensions' );
register_deactivation_hook( __FILE__, 'deactivate_woo_1crm_extensions' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woo-1crm-extensions.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woo_1crm_extensions() {

	$plugin = new Woo_1crm_Extensions();
	$plugin->run();

}
run_woo_1crm_extensions();
