<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://security-ai.com
 * @since             1.0.0
 * @package           Security_Ai
 *
 * @wordpress-plugin
 * Plugin Name:       Security AI
 * Plugin URI:        https://security-ai.com
 * Description:       Plugin de sécurité pour WordPress utilisant l'intelligence artificielle.
 * Version:           1.0.0
 * Author:            Michel & Ouéziza
 * Author URI:        https://security-ai.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       security-ai
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
define( 'SECURITY_AI_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-security-ai-activator.php
 */
function activate_security_ai() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-security-ai-activator.php';
	Security_Ai_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-security-ai-deactivator.php
 */
function deactivate_security_ai() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-security-ai-deactivator.php';
	Security_Ai_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_security_ai' );
register_deactivation_hook( __FILE__, 'deactivate_security_ai' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-security-ai.php';

include_once plugin_dir_path(__FILE__) . 'admin/historique.php';



/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_security_ai() {

	$plugin = new Security_Ai();
	$plugin->run();

}





// Ajouter le menu principal
function security_ai_menu() {
	add_menu_page(
		'Security AI', // Titre de la page
		'Security AI', // Texte du menu
		'manage_options', // Capacité requise
		'security-ai', // Slug de la page
		'security_ai_callback' // Fonction de callback pour le contenu
	);
}
add_action('admin_menu', 'security_ai_menu');

// Ajouter les sous-menus
function security_ai_submenus() {
	add_submenu_page(
		'security-ai', // Nom du menu parent
		'Historique', // Titre du sous-menu
		'Historique', // Texte du sous-menu
		'manage_options', // Capacité requise
		'security-historique', // Slug du sous-menu
		'security_historique_page' // Fonction de callback pour le contenu
	);


	// function security_ai_submenus() {
	// 	add_submenu_page(
	// 		'security-ai', // Nom du menu parent
	// 		'Historique', // Titre du sous-menu
	// 		'Historique', // Texte du sous-menu
	// 		'manage_options', // Capacité requise
	// 		'security-ai-historique', // Slug du sous-menu
	// 		'security_ai_historique_callback' // Fonction de callback pour le contenu
	// 	);

	add_submenu_page(
		'security-ai', // Nom du menu parent
		'Changer Slug', // Titre du sous-menu
		'Changer Slug', // Texte du sous-menu
		'manage_options', // Capacité requise
		'security-ai-changer-slug', // Slug du sous-menu
		'security_ai_changer_slug_callback' // Fonction de callback pour le contenu
	);

	add_submenu_page(
		'security-ai', // Nom du menu parent
		'IA Config', // Titre du sous-menu
		'IA Config', // Texte du sous-menu
		'manage_options', // Capacité requise
		'security-ai-ia-config', // Slug du sous-menu
		'security_ai_ia_config_callback' // Fonction de callback pour le contenu
	);

	add_submenu_page(
		'security-ai', // Nom du menu parent
		'Config Général', // Titre du sous-menu
		'Config Général', // Texte du sous-menu
		'manage_options', // Capacité requise
		'security-ai-config-general', // Slug du sous-menu
		'security_ai_config_general_callback' // Fonction de callback pour le contenu
	);
}
add_action('admin_menu', 'security_ai_submenus');


//add_action('wp_login', 'security_login_history', 10, 2);
// Définir les fonctions de callback pour les sous-menus
// function security_ai_historique_callback() {
// 	//include_once plugin_dir_path(__FILE__) . 'admin/historique.php';
// 	include 'admin/historique.php';

// 	add_action('wp_login_failed', 'record_failed_login');
// 	add_action('wp_login', 'record_user_login', 10, 2);
// 	register_activation_hook(__FILE__, 'create_ia_seure_wordpress_histories_table');
	
// }

function security_ai_changer_slug_callback() {
    include 'admin/changer-slug.php';
}

function security_ai_ia_config_callback() {
    include 'admin/ia-config.php';
}

function security_ai_config_general_callback() {
    include 'admin/config-general.php';
}

function security_ai_callback() {
	echo "Config";
}




run_security_ai();
