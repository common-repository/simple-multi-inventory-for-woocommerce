<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://yourwcninja.com
 * @since             2.0.0
 * @package           Smifw
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Multi-Inventory For Woocommerce
 * Plugin URI:        https://wordpress.org/plugins/simple-multi-inventory-for-woocommerce
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           2.0.0
 * Author:            Your WC Ninja
 * Author URI:        https://yourwcninja.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       simple-multi-inventory-for-woocommerce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SMIFW_VERSION', '2.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-smifw-activator.php
 */
function activate_smifw() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smifw-activator.php';
	Smifw_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-smifw-deactivator.php
 */
function deactivate_smifw() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smifw-deactivator.php';
	Smifw_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_smifw' );
register_deactivation_hook( __FILE__, 'deactivate_smifw' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-smifw.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_smifw() {

	$plugin = new Smifw();
	$plugin->run();

}
run_smifw();
