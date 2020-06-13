<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              /
 * @since             1.0.0
 * @package           Custom_Comments
 *
 * @wordpress-plugin
 * Plugin Name:       Custom Comments
 * Plugin URI:        /
 * Description:       Plugin for custom comments for terms and posts
 * Version:           1.0.0
 * Author:            Amelya
 * Author URI:        /
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       custom-comments
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
define( 'CUSTOM_COMMENTS_VERSION', '1.0.1' );
define( 'CUSTOM_COMMENTS_NAME', 'custom-comments' );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-custom-comments-activator.php
 */
function activate_custom_comments() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-comments-activator.php';
	Custom_Comments_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-custom-comments-deactivator.php
 */
function deactivate_custom_comments() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-comments-deactivator.php';
	Custom_Comments_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_custom_comments' );
register_deactivation_hook( __FILE__, 'deactivate_custom_comments' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-custom-comments.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_custom_comments() {

	$plugin = new Custom_Comments();
	$plugin->run();

}

run_custom_comments();
