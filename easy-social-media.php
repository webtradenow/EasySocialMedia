<?php
/**
 * Plugin Name: Easy Social Media Widget
 * Plugin URI: https://yourwebsite.com/easy-social-media
 * Description: A floating social media widget with customizable options
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: easy-social-media
 * Domain Path: /languages
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('ESM_VERSION', '1.0.0');
define('ESM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ESM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoloader for classes
spl_autoload_register(function ($class) {
    $prefix = 'EasySocialMedia\\';
    $base_dir = ESM_PLUGIN_DIR . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Initialize the plugin
function esm_init() {
    if (class_exists('EasySocialMedia\\Plugin')) {
        $plugin = new EasySocialMedia\Plugin();
        $plugin->init();
    }
}
add_action('plugins_loaded', 'esm_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    if (class_exists('EasySocialMedia\\Activator')) {
        EasySocialMedia\Activator::activate();
    }
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    if (class_exists('EasySocialMedia\\Deactivator')) {
        EasySocialMedia\Deactivator::deactivate();
    }
});
