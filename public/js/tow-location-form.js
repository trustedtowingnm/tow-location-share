/**
 * Tow Location Share Form JavaScript
 */
(function($) {
    'use strict';
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        // Find the get location button and attach the event handler
        $('#get-location-btn').on('click', function(e) {
            e.preventDefault();
            if (typeof window.getLocation === 'function') {
                window.getLocation();
            } else {
                console.error('getLocation function not available');
            }
        });
        
        // Find the form and attach the submit handler
        $('#tow-location-form').on('submit', function(e) {
            e.preventDefault();
            if (typeof window.submitLocation === 'function') {
                window.submitLocation();
            } else {
                console.error('submitLocation function not available');
            }
        });
    });
    
})(jQuery); 