<?php
/**
 * Plugin Name: Custom Side Tab
 * Plugin URI: 
 * Description: A customisable side tab plugin with collapsible functionality
 * Version: 1.0.0
 * Author: Noah
 * Author URI: 
 * Text Domain: custom-side-tab
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('CST_VERSION', '1.0.0');
define('CST_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CST_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once CST_PLUGIN_DIR . 'includes/class-custom-side-tab.php';
require_once CST_PLUGIN_DIR . 'admin/class-custom-side-tab-admin.php';

// Initialize the plugin
function run_custom_side_tab() {
    $plugin = new Custom_Side_Tab();
    $plugin->run();

    // Initialize admin
    if (is_admin()) {
        $admin = new Custom_Side_Tab_Admin();
    }
}

// Hook into WordPress init
add_action('init', 'run_custom_side_tab');

// Activation hook
register_activation_hook(__FILE__, 'custom_side_tab_activate');

function custom_side_tab_activate() {
    // Add default options if they don't exist
    if (!get_option('cst_tab_items')) {
        $default_items = array(
            array(
                'icon' => CST_PLUGIN_URL . 'assets/images/phone.svg',
                'text' => 'Contact Us',
                'link' => 'tel:123456789',
                'target' => '_self'
            ),
            array(
                'icon' => CST_PLUGIN_URL . 'assets/images/quote.svg',
                'text' => 'Get a Quote',
                'link' => '/get-a-quote',
                'target' => '_self'
            ),
            array(
                'icon' => CST_PLUGIN_URL . 'assets/images/contact.svg',
                'text' => 'Contact Form',
                'link' => '/contact-form',
                'target' => '_self'
            )
        );
        add_option('cst_tab_items', $default_items);
    }

    if (!get_option('cst_settings')) {
        $default_settings = array(
            'background_color' => '#f39c12',
            'text_color' => '#ffffff',
            'hover_color' => '#e67e22',
            'enabled' => 1,
            'position' => 'right'
        );
        add_option('cst_settings', $default_settings);
    }
} 