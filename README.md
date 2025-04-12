# Tow Location Share

A WordPress plugin that allows customers to share their GPS location with your towing company.

If you want to see how this looks go to https://trustedtowingsantafe.com/location to see it in action.

## Features

- Customers can share their GPS location directly from your WordPress site
- Phone number used as customer identifier for easier tracking
- Google Maps display with satellite view for better visualization
- Map zoomed to level 18 (showing approximately 100-200 yards around their location)
- Customers can adjust the pin location if GPS is inaccurate
- Email notifications when a location is shared
- Admin dashboard to view all shared locations
- Configurable Google Maps API key
- **NEW:** Automatic theme integration - adapts to your website's colors and styling
- **NEW:** Optional attribution link with customizable text

## Installation

1. Download the plugin ZIP file
2. Upload the plugin to your WordPress site via the Plugins > Add New > Upload Plugin
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to 'Tow Locations' > 'Settings' in your WordPress admin to configure:
   - Google Maps API key
   - Theme integration options
   - Attribution settings

## Google Maps API Key

You need to create a Google Maps API key to use this plugin:

1. Go to the [Google Cloud Console](https://console.cloud.google.com/google/maps-apis/overview)
2. Create a new project or select an existing one
3. Enable the following APIs:
   - Maps JavaScript API
   - Geocoding API
4. Create an API key under "Credentials"
5. Restrict the API key to specific websites if you like (recommended for security)
6. Copy the API key to the plugin settings

## Usage

Add the location sharing form to any page or post using the shortcode:

```
[tow_location_share]
```

This will display a form where customers can enter their phone number and share their location.

## Theme Integration

The plugin now automatically adapts to your website's theme:

1. Enable theme integration in the plugin settings
2. The plugin will automatically:
   - Match your theme's colors
   - Use your theme's typography
   - Adopt your theme's border styles and spacing
   - Work with both modern block themes and classic themes

You can further customize the integration by:
- Adding specific theme CSS classes to the form container
- Overriding any styling through your theme's CSS
- Creating a custom template in your theme folder

## Attribution Options

The plugin includes an optional, unobtrusive attribution link that can be:
- Enabled or disabled in the settings
- Customized with your preferred text
- Styled to match your theme's colors
- Positioned discreetly below the form

## How It Works

1. Customers visit your website and navigate to the page with the location sharing form
2. They enter their phone number in the provided field
3. They click "Share My Location" button and grant permission when prompted
4. The map displays in satellite view, showing their current location with a pin
5. Customers can drag the pin to adjust the exact location if needed
6. After clicking "Submit Location", you receive an email notification with their details
7. All submissions can be viewed in the "Tow Locations" dashboard in your WordPress admin

## Advanced Customization

### Theme Developers

You can create custom templates by adding files to your theme:
- `tow-location-share/location-form.php` - Custom form template
- Use WordPress filters to modify classes, text, and HTML structure

### CSS Customization

The plugin uses CSS variables for easy styling. You can:
1. Modify the default styles in `/public/css/tow-location-share.css`
2. Override variables in your theme's CSS
3. Add custom classes through the settings panel

### Available Filters

```php
// Modify element classes
add_filter('tow_location_share_element_classes', function($classes) {
    $classes['container'] .= ' my-custom-class';
    return $classes;
});

// Customize text elements
add_filter('tow_location_share_text_elements', function($texts) {
    $texts['title'] = 'Need a Tow?';
    return $texts;
});

// Disable default styles
add_filter('tow_location_share_disable_styles', '__return_true');
```

## Benefits

- Brand consistency: Keep customers on your website domain
- Better user experience: Simple, straightforward interface
- Improved accuracy: Customers can adjust the pin if GPS is off
- Quick response: Email notifications with Google Maps links
- Organized tracking: Admin dashboard for all submissions

## SEO Potential

By hosting this plugin on the WordPress Plugin Directory, you may receive backlinks to your website, potentially improving your search rankings. The actual SEO benefit will depend on various factors including traffic and linking patterns.

## Support

For support or customization requests, please contact the plugin developer.

## License

This plugin is licensed under the GPL v2 or later. 
