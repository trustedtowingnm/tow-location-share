<?php
/**
 * Plugin Name: Tow Location Share
 * Plugin URI: https://trustedtowingsantafe.com/
 * Description: A plugin that allows customers to share their GPS location with your towing company. Includes an optional, unobtrusive attribution link that can be disabled in settings.
 * Version: 1.0.3
 * Author: Luke Walliser
 * Author URI: https://trustedtowingsantafe.com/
 * License: GPL2
 * Text Domain: tow-location-share
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('TOW_LOCATION_SHARE_VERSION', '1.0.3');
define('TOW_LOCATION_SHARE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TOW_LOCATION_SHARE_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_tow_location_share() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tow_locations';
    
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        phone_number varchar(20) NOT NULL,
        latitude decimal(10,7) NOT NULL,
        longitude decimal(10,7) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Add default options
    add_option('tow_location_share_google_maps_api_key', '');
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_tow_location_share() {
    // No need to delete data on deactivation
}

register_activation_hook(__FILE__, 'activate_tow_location_share');
register_deactivation_hook(__FILE__, 'deactivate_tow_location_share');

/**
 * Admin menu pages
 */
function tow_location_share_admin_menu() {
    add_menu_page(
        'Tow Locations',
        'Tow Locations',
        'manage_options',
        'tow-locations',
        'tow_location_share_admin_page',
        'dashicons-location',
        30
    );
    
    add_submenu_page(
        'tow-locations',
        'Settings',
        'Settings',
        'manage_options',
        'tow-location-settings',
        'tow_location_share_settings_page'
    );
}
add_action('admin_menu', 'tow_location_share_admin_menu');

/**
 * Admin page callback
 */
function tow_location_share_admin_page() {
    include_once(TOW_LOCATION_SHARE_PLUGIN_DIR . 'admin/admin-page.php');
}

/**
 * Settings page callback
 */
function tow_location_share_settings_page() {
    include_once(TOW_LOCATION_SHARE_PLUGIN_DIR . 'admin/settings-page.php');
}

/**
 * Register settings
 */
function tow_location_share_register_settings() {
    register_setting('tow_location_share_settings', 'tow_location_share_google_maps_api_key');
    register_setting('tow_location_share_settings', 'tow_location_share_use_theme_styling');
    register_setting('tow_location_share_settings', 'tow_location_share_container_class');
    register_setting('tow_location_share_settings', 'tow_location_share_show_attribution');
    register_setting('tow_location_share_settings', 'tow_location_share_attribution_text');
}
add_action('admin_init', 'tow_location_share_register_settings');

/**
 * Enqueue front-end scripts and styles
 */
function tow_location_share_enqueue_scripts() {
    $api_key = get_option('tow_location_share_google_maps_api_key');
    
    if (!empty($api_key)) {
        wp_enqueue_script(
            'google-maps',
            "https://maps.googleapis.com/maps/api/js?key=$api_key",
            array(),
            TOW_LOCATION_SHARE_VERSION,
            true
        );
        wp_script_add_data('google-maps', 'async', true);
    }
    
    // Check if theme styling is enabled
    $use_theme_styling = get_option('tow_location_share_use_theme_styling', '0');
    
    // Add theme-specific colors as CSS variables if theme styling is enabled
    if ($use_theme_styling === '1') {
        add_action('wp_head', 'tow_location_share_add_theme_colors', 999);
    }
    
    // Load the plugin styles with a lower priority (higher number) to allow themes to override
    wp_enqueue_style(
        'tow-location-share-style',
        TOW_LOCATION_SHARE_PLUGIN_URL . 'public/css/tow-location-share.css',
        array(),
        TOW_LOCATION_SHARE_VERSION,
        'all'
    );
    
    // Allow themes to disable the default styles
    $disable_styles = apply_filters('tow_location_share_disable_styles', false);
    if ($disable_styles) {
        wp_dequeue_style('tow-location-share-style');
    }
    
    // Load the main plugin script first, before Google Maps
    wp_enqueue_script(
        'tow-location-share-script',
        TOW_LOCATION_SHARE_PLUGIN_URL . 'public/js/tow-location-share.js',
        array('jquery'),
        TOW_LOCATION_SHARE_VERSION,
        true
    );
    
    // Load the form-specific script after the main script
    wp_enqueue_script(
        'tow-location-share-form',
        TOW_LOCATION_SHARE_PLUGIN_URL . 'public/js/tow-location-form.js',
        array('jquery', 'tow-location-share-script'),
        TOW_LOCATION_SHARE_VERSION,
        true
    );
    
    wp_localize_script(
        'tow-location-share-script',
        'towLocationShareParams',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tow-location-share-nonce')
        )
    );
}
add_action('wp_enqueue_scripts', 'tow_location_share_enqueue_scripts');

/**
 * Extract theme colors and settings and add them as inline CSS variables
 * This makes the plugin adopt the theme's styling automatically
 */
function tow_location_share_add_theme_colors() {
    // Check if we have theme.json support (WordPress 5.8+)
    if (function_exists('wp_get_global_settings')) {
        $colors = array();
        $global_settings = wp_get_global_settings();
        
        // Check for theme.json color palette
        if (!empty($global_settings['color']['palette']['theme'])) {
            foreach ($global_settings['color']['palette']['theme'] as $color) {
                if (!empty($color['slug']) && !empty($color['color'])) {
                    $colors[$color['slug']] = $color['color'];
                }
            }
        }
        
        // Check for typography settings
        $typography = array();
        if (!empty($global_settings['typography']['fontFamilies']['theme'])) {
            foreach ($global_settings['typography']['fontFamilies']['theme'] as $font) {
                if (!empty($font['slug']) && !empty($font['fontFamily'])) {
                    $typography[$font['slug']] = $font['fontFamily'];
                }
            }
        }
        
        // Only output if we have data
        if (!empty($colors) || !empty($typography)) {
            echo '<style id="tow-location-theme-integration">';
            echo ':root {';
            
            // Map common color names to our variables
            $color_mapping = array(
                'primary' => '--tow-primary-color',
                'secondary' => '--tow-primary-hover',
                'background' => '--tow-background',
                'text' => '--tow-text-color',
                'accent' => '--tow-primary-color',
                'success' => '--tow-success-color',
                'error' => '--tow-error-color',
                'border' => '--tow-border-color',
            );
            
            foreach ($color_mapping as $theme_color => $tow_var) {
                if (!empty($colors[$theme_color])) {
                    echo esc_html($tow_var) . ': ' . esc_html($colors[$theme_color]) . ';';
                }
            }
            
            // Add typography
            if (!empty($typography['body'])) {
                echo '--tow-font-family: ' . esc_html($typography['body']) . ';';
            }
            if (!empty($typography['heading'])) {
                echo '--tow-heading-font-family: ' . esc_html($typography['heading']) . ';';
            }
            
            echo '}';
            echo '</style>';
        }
    }
    
    // Fallback for older themes or non-FSE themes
    // Try to extract colors from the active theme's stylesheet
    else {
        $theme_css = get_stylesheet_directory() . '/style.css';
        if (file_exists($theme_css)) {
            $css_content = file_get_contents($theme_css);
            
            // Extract primary button color
            $button_color = '';
            if (preg_match('/button.*?background(-color)?:\s*(#[a-f0-9]{3,6}|rgba?\([^)]+\))/i', $css_content, $matches)) {
                $button_color = $matches[2];
            }
            
            // Extract text color
            $text_color = '';
            if (preg_match('/body.*?color:\s*(#[a-f0-9]{3,6}|rgba?\([^)]+\))/i', $css_content, $matches)) {
                $text_color = $matches[1];
            }
            
            // Only output if we found colors
            if ($button_color || $text_color) {
                echo '<style id="tow-location-theme-integration">';
                echo ':root {';
                if ($button_color) {
                    echo '--tow-primary-color: ' . esc_html($button_color) . ';';
                }
                if ($text_color) {
                    echo '--tow-text-color: ' . esc_html($text_color) . ';';
                }
                echo '}';
                echo '</style>';
            }
        }
    }
}

/**
 * Add shortcode for the location sharing form
 */
function tow_location_share_shortcode() {
    $api_key = get_option('tow_location_share_google_maps_api_key');
    if (empty($api_key)) {
        return '<p class="tow-location-error">Please configure Google Maps API key in the plugin settings.</p>';
    }
    
    ob_start();
    include_once(TOW_LOCATION_SHARE_PLUGIN_DIR . 'public/location-form.php');
    return ob_get_clean();
}
add_shortcode('tow_location_share', 'tow_location_share_shortcode');

/**
 * Handle AJAX submission
 */
function tow_location_share_submit() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tow-location-share-nonce')) {
        wp_send_json_error('Invalid nonce');
    }
    
    // Get and sanitize form data
    $phone_number = isset($_POST['phone_number']) ? sanitize_text_field($_POST['phone_number']) : '';
    $latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : 0;
    $longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : 0;
    
    // Validate data
    if (empty($phone_number) || $latitude == 0 || $longitude == 0) {
        wp_send_json_error('Please provide all required information');
    }
    
    // Save to database
    global $wpdb;
    $table_name = $wpdb->prefix . 'tow_locations';
    
    // Get current WordPress time (local time based on site settings)
    $current_time = current_time('mysql');
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'phone_number' => $phone_number,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'created_at' => $current_time // Use WordPress local time instead of server time
        )
    );
    
    if ($result === false) {
        wp_send_json_error('Database error');
    }
    
    // Send email notification - but first check if this is a duplicate submission
    $duplicate_check = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE phone_number = %s AND latitude = %f AND longitude = %f AND created_at > DATE_SUB(%s, INTERVAL 5 MINUTE)",
        $phone_number,
        $latitude,
        $longitude,
        $current_time
    ));
    
    // Only send email if this is the first submission in the last 5 minutes with these exact details
    if ($duplicate_check <= 1) {
        $admin_email = get_option('admin_email');
        $subject = 'New Tow Location Shared';
        $google_maps_link = "https://www.google.com/maps?q=$latitude,$longitude";
        
        // Format the time in site's timezone for the email
        $time_format = get_option('time_format');
        $date_format = get_option('date_format');
        $formatted_time = date_i18n("$date_format $time_format", strtotime($current_time));
        
        $message = "A customer has shared their location:\n\n";
        $message .= "Phone Number: $phone_number\n";
        $message .= "Latitude: $latitude\n";
        $message .= "Longitude: $longitude\n";
        $message .= "Time: $formatted_time\n";
        $message .= "View on Google Maps: $google_maps_link\n\n";
        $message .= "Log in to your WordPress dashboard to see all submissions.";
        
        wp_mail($admin_email, $subject, $message);
    }
    
    wp_send_json_success('Location shared successfully');
}
add_action('wp_ajax_tow_location_share_submit', 'tow_location_share_submit');
add_action('wp_ajax_nopriv_tow_location_share_submit', 'tow_location_share_submit');

// Include required files
require_once TOW_LOCATION_SHARE_PLUGIN_DIR . 'includes/functions.php'; 