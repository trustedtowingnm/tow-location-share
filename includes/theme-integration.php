<?php
/**
 * Theme integration examples for Tow Location Share plugin
 *
 * This file provides examples of how to integrate the Tow Location Share plugin
 * with your WordPress theme to match your site's styling.
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Example 1: Completely disable plugin styles and use theme styles
 * 
 * Add this to your theme's functions.php file to disable the plugin's
 * built-in CSS and style the plugin using your theme's CSS.
 */
function mytheme_disable_tow_location_share_styles() {
    return true;
}
add_filter('tow_location_share_disable_styles', 'mytheme_disable_tow_location_share_styles');

/**
 * Example 2: Add custom CSS variables to match theme styling
 * 
 * Add this to your theme's functions.php file to inject custom CSS
 * variables that will be used by the plugin.
 */
function mytheme_tow_location_share_custom_colors() {
    // Replace these colors with your theme's colors
    ?>
    <style>
        :root {
            --tow-primary-color: #your-theme-primary-color;
            --tow-primary-hover: #your-theme-primary-hover-color;
            --tow-success-color: #your-theme-success-color;
            --tow-error-color: #your-theme-error-color;
            --tow-background: #your-theme-background-color;
            --tow-border-color: #your-theme-border-color;
            --tow-text-color: #your-theme-text-color;
            --tow-border-radius: 4px; /* Change to match your theme */
            --tow-box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Change to match your theme */
        }
    </style>
    <?php
}
add_action('wp_head', 'mytheme_tow_location_share_custom_colors');

/**
 * Example 3: Customize the form template
 * 
 * You can copy the public/location-form.php file to your theme in:
 * your-theme/tow-location-share/location-form.php
 * 
 * The plugin will use your theme's version of the template if available.
 */

/**
 * Example 4: Add custom classes to form elements for theme integration
 * 
 * This filter allows you to add custom classes to the container,
 * form elements, and buttons.
 */
function mytheme_tow_location_share_classes($classes) {
    // Add your theme's button classes
    $classes['container'] .= ' your-theme-container-class';
    $classes['button'] .= ' your-theme-button-class';
    $classes['input'] .= ' your-theme-input-class';
    
    return $classes;
}
add_filter('tow_location_share_element_classes', 'mytheme_tow_location_share_classes'); 