<?php
namespace EasySocialMedia;

class Plugin {
    private static $instance = null;
    private $admin;
    private $frontend;

    public function __construct() {
        $this->load_dependencies();
        $this->setup_hooks();
    }

    private function load_dependencies() {
        require_once ESM_PLUGIN_DIR . 'includes/Admin/Admin.php';
        require_once ESM_PLUGIN_DIR . 'includes/Frontend/Frontend.php';
        
        $this->admin = new Admin\Admin();
        $this->frontend = new Frontend\Frontend();
    }

    private function setup_hooks() {
        add_action('init', [$this, 'init_plugin']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
    }

    public function init_plugin() {
        load_plugin_textdomain('easy-social-media', false, dirname(plugin_basename(ESM_PLUGIN_DIR)) . '/languages');
    }

    public function enqueue_scripts() {
        wp_enqueue_style(
            'easy-social-media',
            ESM_PLUGIN_URL . 'assets/css/frontend.css',
            [],
            ESM_VERSION
        );

        wp_enqueue_script(
            'easy-social-media',
            ESM_PLUGIN_URL . 'assets/js/frontend.js',
            ['jquery'],
            ESM_VERSION,
            true
        );

        wp_localize_script('easy-social-media', 'esmData', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('esm-nonce'),
        ]);
    }

    public function admin_enqueue_scripts($hook) {
        if ('settings_page_easy-social-media' !== $hook) {
            return;
        }

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style(
            'easy-social-media-admin',
            ESM_PLUGIN_URL . 'assets/css/admin.css',
            [],
            ESM_VERSION
        );

        wp_enqueue_script(
            'easy-social-media-admin',
            ESM_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery', 'jquery-ui-sortable', 'wp-color-picker'],
            ESM_VERSION,
            true
        );
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        $this->admin->init();
        $this->frontend->init();
    }
}
