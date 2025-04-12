<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Check user capabilities
if (!current_user_can('manage_options')) {
    return;
}

// Process search if submitted
$search_term = '';
$search_results = array();
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = sanitize_text_field($_GET['search']);
    $search_results = tow_location_share_search_by_phone($search_term);
}

// Get all submissions if not searching
$locations = empty($search_term) ? tow_location_share_get_submissions() : $search_results;
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="tow-location-search">
        <form method="get">
            <input type="hidden" name="page" value="tow-locations">
            <label for="search">Search by Phone Number:</label>
            <input type="text" id="search" name="search" value="<?php echo esc_attr($search_term); ?>" placeholder="Enter phone number...">
            <input type="submit" class="button" value="Search">
            <?php if (!empty($search_term)): ?>
                <a href="?page=tow-locations" class="button">Clear</a>
            <?php endif; ?>
        </form>
    </div>
    
    <?php if (empty($locations)): ?>
        <p>No location submissions found.</p>
    <?php else: ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Phone Number</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Date/Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($locations as $location): ?>
                    <tr>
                        <td><?php echo esc_html($location->id); ?></td>
                        <td><?php echo esc_html(tow_location_share_format_phone($location->phone_number)); ?></td>
                        <td><?php echo esc_html($location->latitude); ?></td>
                        <td><?php echo esc_html($location->longitude); ?></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($location->created_at))); ?></td>
                        <td>
                            <a href="<?php echo esc_url(tow_location_share_get_map_link($location->latitude, $location->longitude)); ?>" target="_blank" class="button button-small">
                                View on Map
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<style>
    .tow-location-search {
        margin: 20px 0;
        padding: 15px;
        background: #fff;
        border: 1px solid #ccd0d4;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
    }
    
    .tow-location-search label {
        margin-right: 10px;
    }
    
    .tow-location-search input[type="text"] {
        width: 250px;
        margin-right: 10px;
    }
</style> 