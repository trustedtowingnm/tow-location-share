<?php
/**
 * Helper functions for the Tow Location Share plugin
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Get a Google Maps link for the given coordinates
 *
 * @param float $latitude
 * @param float $longitude
 * @return string
 */
function tow_location_share_get_map_link($latitude, $longitude) {
    return "https://www.google.com/maps?q=$latitude,$longitude";
}

/**
 * Format a phone number for display
 *
 * @param string $phone_number
 * @return string
 */
function tow_location_share_format_phone($phone_number) {
    // Remove all non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone_number);
    
    // Format based on length
    if (strlen($phone) == 10) {
        return '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
    } elseif (strlen($phone) == 11 && substr($phone, 0, 1) == '1') {
        return '1-' . '(' . substr($phone, 1, 3) . ') ' . substr($phone, 4, 3) . '-' . substr($phone, 7);
    }
    
    // Return as is if not a standard format
    return $phone_number;
}

/**
 * Get all location submissions, ordered by latest first
 *
 * @param int $limit Optional. Number of entries to retrieve. Default 50.
 * @return array
 */
function tow_location_share_get_submissions($limit = 50) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tow_locations';
    
    $query = "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d";
    
    return $wpdb->get_results($wpdb->prepare($query, $limit));
}

/**
 * Get a single location submission by ID
 *
 * @param int $id
 * @return object|null
 */
function tow_location_share_get_submission($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tow_locations';
    
    $query = "SELECT * FROM $table_name WHERE id = %d";
    
    return $wpdb->get_row($wpdb->prepare($query, $id));
}

/**
 * Search location submissions by phone number
 *
 * @param string $phone_number
 * @return array
 */
function tow_location_share_search_by_phone($phone_number) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'tow_locations';
    
    $query = "SELECT * FROM $table_name WHERE phone_number LIKE %s ORDER BY created_at DESC";
    
    return $wpdb->get_results($wpdb->prepare($query, '%' . $wpdb->esc_like($phone_number) . '%'));
} 