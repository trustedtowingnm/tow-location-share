<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Check user capabilities
if (!current_user_can('manage_options')) {
    return;
}

// Save settings if the form was submitted
if (isset($_POST['submit'])) {
    // Check nonce for security
    check_admin_referer('tow_location_share_settings_nonce');
    
    // Save the API key
    $api_key = sanitize_text_field($_POST['tow_location_share_google_maps_api_key']);
    update_option('tow_location_share_google_maps_api_key', $api_key);
    
    // Add settings saved message
    add_settings_error(
        'tow_location_share_messages',
        'tow_location_share_message',
        'Settings Saved',
        'updated'
    );
}

// Get the current API key
$api_key = get_option('tow_location_share_google_maps_api_key', '');

// Show settings errors/notices
settings_errors('tow_location_share_messages');
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="options.php">
        <?php settings_fields('tow_location_share_settings'); ?>
        <?php do_settings_sections('tow_location_share_settings'); ?>
        
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e('Google Maps API Key', 'tow-location-share'); ?></th>
                <td>
                    <input type="text" name="tow_location_share_google_maps_api_key" value="<?php echo esc_attr(get_option('tow_location_share_google_maps_api_key')); ?>" class="regular-text" />
                    <p class="description"><?php _e('Enter your Google Maps API key. You can create one in the <a href="https://console.cloud.google.com/google/maps-apis/credentials" target="_blank">Google Cloud Console</a>.', 'tow-location-share'); ?></p>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row"><?php _e('Theme Integration', 'tow-location-share'); ?></th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php _e('Theme Integration', 'tow-location-share'); ?></span></legend>
                        <label for="tow_location_share_use_theme_styling">
                            <input type="checkbox" id="tow_location_share_use_theme_styling" name="tow_location_share_use_theme_styling" value="1" <?php checked(get_option('tow_location_share_use_theme_styling', '0'), '1'); ?> />
                            <?php _e('Use theme styling (recommended)', 'tow-location-share'); ?>
                        </label>
                        <p class="description"><?php _e('When enabled, the plugin will attempt to match your theme\'s colors and styling.', 'tow-location-share'); ?></p>
                    </fieldset>
                </td>
            </tr>
            
            <tr valign="top" class="tow-theme-class-row" <?php echo get_option('tow_location_share_use_theme_styling', '0') == '0' ? 'style="display:none;"' : ''; ?>>
                <th scope="row"><?php _e('Container CSS Class', 'tow-location-share'); ?></th>
                <td>
                    <input type="text" name="tow_location_share_container_class" value="<?php echo esc_attr(get_option('tow_location_share_container_class', '')); ?>" class="regular-text" />
                    <p class="description"><?php _e('Optional: Add CSS classes from your theme to the form container for better integration.', 'tow-location-share'); ?></p>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e('Attribution Link', 'tow-location-share'); ?></th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php _e('Attribution Link', 'tow-location-share'); ?></span></legend>
                        <label for="tow_location_share_show_attribution">
                            <input type="checkbox" id="tow_location_share_show_attribution" name="tow_location_share_show_attribution" value="1" <?php checked(get_option('tow_location_share_show_attribution', '1'), '1'); ?> />
                            <?php _e('Show attribution link', 'tow-location-share'); ?>
                        </label>
                        <p class="description"><?php _e('Display a small "Powered by Trusted Towing" link below the form. This helps spread the word about our plugin and supports its continued development.', 'tow-location-share'); ?></p>
                    </fieldset>
                </td>
            </tr>

            <tr valign="top" class="tow-attribution-options" <?php echo get_option('tow_location_share_show_attribution', '1') == '0' ? 'style="display:none;"' : ''; ?>>
                <th scope="row"><?php _e('Attribution Text', 'tow-location-share'); ?></th>
                <td>
                    <input type="text" name="tow_location_share_attribution_text" value="<?php echo esc_attr(get_option('tow_location_share_attribution_text', __('Powered by Trusted Towing', 'tow-location-share'))); ?>" class="regular-text" />
                    <p class="description"><?php _e('Customize the text of the attribution link (optional).', 'tow-location-share'); ?></p>
                </td>
            </tr>
        </table>
        
        <h2>Plugin Usage</h2>
        <p>
            To add the location sharing form to a page or post, use the following shortcode:
            <code>[tow_location_share]</code>
        </p>
        
        <p>
            This will display a form where customers can enter their phone number and share their location.
            The location will be shown on a Google Map with satellite view at zoom level 18 (showing about 100-200 yards around their location).
            Customers can adjust the pin if needed before submitting.
        </p>
        
        <h3>Features:</h3>
        <ul>
            <li>Google Maps with satellite view for clearer visuals</li>
            <li>Phone number as customer identifier</li>
            <li>Email notifications for new submissions</li>
            <li>Admin dashboard to view all submissions</li>
        </ul>
        
        <?php submit_button(); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    $('#tow_location_share_use_theme_styling').change(function() {
        if ($(this).is(':checked')) {
            $('.tow-theme-class-row').show();
        } else {
            $('.tow-theme-class-row').hide();
        }
    });

    $('#tow_location_share_show_attribution').change(function() {
        if ($(this).is(':checked')) {
            $('.tow-attribution-options').show();
        } else {
            $('.tow-attribution-options').hide();
        }
    });
});
</script> 