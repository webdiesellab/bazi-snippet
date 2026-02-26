<?php
/**
 * Plugin Name: Bazi Calculator
 * Plugin URI: https://web-diesel.com/bazi-calculator
 * Description: Accurate Bazi (Four Pillars of Destiny) calculator with precise astronomical solar longitude calculations based on Master Tsai AI Model.
 * Version: 25.0
 * Author: Web Diesel
 * Author URI: https://web-diesel.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bazi-calculator
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('BAZI_CALCULATOR_VERSION', '25.0');
define('BAZI_CALCULATOR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BAZI_CALCULATOR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BAZI_CALCULATOR_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
final class Bazi_Calculator_Plugin {
    
    private static $instance = null;
    private $calculator = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function load_dependencies() {
        require_once BAZI_CALCULATOR_PLUGIN_DIR . 'includes/class-bazi-calculator.php';
    }
    
    private function init_hooks() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        add_action('init', array($this, 'init_calculator'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_filter('plugin_action_links_' . BAZI_CALCULATOR_PLUGIN_BASENAME, array($this, 'add_settings_link'));
    }
    
    public function activate() {
        flush_rewrite_rules();
        if (!get_option('bazi_calculator_version')) {
            add_option('bazi_calculator_version', BAZI_CALCULATOR_VERSION);
        } else {
            update_option('bazi_calculator_version', BAZI_CALCULATOR_VERSION);
        }
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    public function init_calculator() {
        $this->calculator = new Bazi_Calculator();
    }
    
    public function enqueue_frontend_assets() {
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'bazi_calculator')) {
            wp_enqueue_style(
                'bazi-calculator-styles',
                BAZI_CALCULATOR_PLUGIN_URL . 'assets/css/bazi-styles.css',
                array(),
                BAZI_CALCULATOR_VERSION
            );
            
            wp_enqueue_script('jquery');
            wp_enqueue_script(
                'bazi-calculator-scripts',
                BAZI_CALCULATOR_PLUGIN_URL . 'assets/js/bazi-scripts.js',
                array('jquery'),
                BAZI_CALCULATOR_VERSION,
                true
            );
            
            wp_localize_script('bazi-calculator-scripts', 'baziAjax', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('bazi_calculator_nonce')
            ));
        }
    }
    
    public function add_admin_menu() {
        add_options_page(
            __('Bazi Calculator Settings', 'bazi-calculator'),
            __('Bazi Calculator', 'bazi-calculator'),
            'manage_options',
            'bazi-calculator-settings',
            array($this, 'render_settings_page')
        );
    }
    
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Bazi Calculator Settings', 'bazi-calculator'); ?></h1>
            
            <div class="card">
                <h2><?php _e('How to Use', 'bazi-calculator'); ?></h2>
                <p><?php _e('Add the calculator to any page or post using the shortcode:', 'bazi-calculator'); ?></p>
                <code>[bazi_calculator]</code>
            </div>
            
            <div class="card" style="margin-top: 20px;">
                <h2><?php _e('About', 'bazi-calculator'); ?></h2>
                <p><strong><?php _e('Version:', 'bazi-calculator'); ?></strong> <?php echo BAZI_CALCULATOR_VERSION; ?></p>
                <p><?php _e('This calculator uses precise astronomical solar longitude calculations based on the Master Tsai AI Model for accurate Bazi readings.', 'bazi-calculator'); ?></p>
            </div>
            
            <div class="card" style="margin-top: 20px;">
                <h2><?php _e('Features', 'bazi-calculator'); ?></h2>
                <ul style="list-style: disc; margin-left: 20px;">
                    <li><?php _e('Accurate solar longitude-based month pillar calculation', 'bazi-calculator'); ?></li>
                    <li><?php _e('Local solar time correction with longitude input', 'bazi-calculator'); ?></li>
                    <li><?php _e('Ten-year major luck cycles calculation', 'bazi-calculator'); ?></li>
                    <li><?php _e('Geolocation support for automatic longitude detection', 'bazi-calculator'); ?></li>
                    <li><?php _e('Mobile-responsive design', 'bazi-calculator'); ?></li>
                    <li><?php _e('Form data persistence with localStorage', 'bazi-calculator'); ?></li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=bazi-calculator-settings') . '">' . __('Settings', 'bazi-calculator') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
    
    public function get_calculator() {
        return $this->calculator;
    }
}

function bazi_calculator_init() {
    return Bazi_Calculator_Plugin::get_instance();
}

add_action('plugins_loaded', 'bazi_calculator_init');
