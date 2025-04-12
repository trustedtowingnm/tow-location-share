<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Generate a unique ID for the form elements
$unique_id = 'tow-location-' . uniqid();

// Define default classes
$default_classes = array(
    'container' => 'tow-location-container',
    'header' => 'tow-location-header',
    'form' => 'tow-location-form',
    'form_group' => 'form-group',
    'input' => '',
    'map_container' => 'tow-location-map-container',
    'buttons' => 'tow-location-buttons',
    'button' => 'tow-location-btn',
    'info' => 'tow-location-info'
);

// Allow themes to filter the classes
$classes = apply_filters('tow_location_share_element_classes', $default_classes);

// Default text elements - make them filterable
$texts = apply_filters('tow_location_share_text_elements', array(
    'title' => __('Share Your Location', 'tow-location-share'),
    'subtitle' => __('Let us know where you are so we can help you quickly.', 'tow-location-share'),
    'phone_label' => __('Your Phone Number:', 'tow-location-share'),
    'phone_placeholder' => __('Enter your phone number', 'tow-location-share'),
    'location_message' => __('Click the "Find My Location" button below to show your location on the map.', 'tow-location-share'),
    'get_location_btn' => __('Find My Location', 'tow-location-share'),
    'submit_btn' => __('Send Location (REQUIRED FINAL STEP)', 'tow-location-share'),
    'note_text' => __('<strong>Note:</strong> You\'ll need to allow location access in your browser when prompted. The map will show a satellite view of your area, and you can drag the pin to adjust your exact location if needed.', 'tow-location-share'),
));

// Check if a theme template exists
$theme_template = locate_template('tow-location-share/location-form.php');
if (!empty($theme_template)) {
    include($theme_template);
    return;
}

// Add theme classes from Customizer (for older themes)
$container_classes = $classes['container'];
if (function_exists('get_theme_mod')) {
    $custom_class = get_theme_mod('tow_location_container_class', '');
    if (!empty($custom_class)) {
        $container_classes .= ' ' . esc_attr($custom_class);
    }
}

// Allow Block themes to specify different wrapper elements
$container_tag = apply_filters('tow_location_share_container_tag', 'div');
$header_tag = apply_filters('tow_location_share_header_tag', 'div');
$title_tag = apply_filters('tow_location_share_title_tag', 'h2');
?>

<<?php echo tag_escape($container_tag); ?> class="<?php echo esc_attr($container_classes); ?>">
    <<?php echo tag_escape($header_tag); ?> class="<?php echo esc_attr($classes['header']); ?>">
        <<?php echo tag_escape($title_tag); ?>><?php echo esc_html($texts['title']); ?></<?php echo tag_escape($title_tag); ?>>
        <p><?php echo esc_html($texts['subtitle']); ?></p>
    </<?php echo tag_escape($header_tag); ?>>
    
    <form id="tow-location-form" class="<?php echo esc_attr($classes['form']); ?>">
        <div class="<?php echo esc_attr($classes['form_group']); ?>">
            <label for="phone_number"><?php echo esc_html($texts['phone_label']); ?></label>
            <input type="text" id="phone_number" name="phone_number" placeholder="<?php echo esc_attr($texts['phone_placeholder']); ?>" class="<?php echo esc_attr($classes['input']); ?>" required>
        </div>
        
        <div id="tow-location-map-container" class="<?php echo esc_attr($classes['map_container']); ?>">
            <div class="tow-location-message"><?php echo esc_html($texts['location_message']); ?></div>
        </div>
        
        <!-- Hidden fields to store coordinates -->
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
        
        <div class="<?php echo esc_attr($classes['buttons']); ?>">
            <button type="button" id="get-location-btn" class="<?php echo esc_attr($classes['button']); ?>"><?php echo esc_html($texts['get_location_btn']); ?></button>
            <button type="submit" id="submit-location-btn" class="<?php echo esc_attr($classes['button']); ?> disabled" disabled><?php echo esc_html($texts['submit_btn']); ?></button>
        </div>
    </form>
    
    <div class="<?php echo esc_attr($classes['info']); ?>">
        <p><?php echo wp_kses_post($texts['note_text']); ?></p>
    </div>

    <?php if (get_option('tow_location_share_show_attribution', '1') === '1'): ?>
    <div class="tow-location-attribution">
        <a href="<?php echo esc_url('https://trustedtowingsantafe.com/'); ?>" target="_blank" rel="noopener noreferrer">
            <?php echo esc_html(get_option('tow_location_share_attribution_text', __('Powered by Trusted Towing', 'tow-location-share'))); ?>
        </a>
    </div>
    <?php endif; ?>
</<?php echo tag_escape($container_tag); ?>> 