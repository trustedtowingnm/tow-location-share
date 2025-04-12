/**
 * Tow Location Share JavaScript
 */
(function($) {
    'use strict';
    
    // Map variables
    var map, marker, infoWindow;
    var defaultZoom = 18; // Zoom level showing approximately 100-200 yards
    var mapElementId = 'tow-location-map-element'; // Consistent ID for the map element
    
    // Initialize once DOM is fully loaded
    $(document).ready(function() {
        // Initialize event listeners on page load
        $('#get-location-btn').on('click', function(e) {
            e.preventDefault();
            window.getLocation();
        });
        
        // Form submission
        $('#tow-location-form').on('submit', function(e) {
            e.preventDefault();
            window.submitLocation();
        });
    });
    
    /**
     * Get the user's current location
     */
    window.getLocation = function() {
        // Clear any previous messages
        $('.tow-location-message').remove();
        
        // Check if geolocation is supported
        if (!navigator.geolocation) {
            showError('Geolocation is not supported by your browser');
            return;
        }
        
        // Check if Google Maps API is loaded
        if (!window.google || !window.google.maps) {
            showError('Google Maps API is not loaded yet. Please try again in a few seconds.');
            return;
        }
        
        // Always create a fresh map container with our consistent ID
        $('#tow-location-map-container').html('<div id="' + mapElementId + '" style="width: 100%; height: 300px;"></div><div class="tow-location-message">Getting your location...</div>');
        
        // Get current position
        navigator.geolocation.getCurrentPosition(
            // Success callback
            function(position) {
                // Initialize map after we're sure the element exists
                initializeMap(position.coords.latitude, position.coords.longitude);
                $('#submit-location-btn').prop('disabled', false).removeClass('disabled');
                
                // Update hidden form fields
                $('#latitude').val(position.coords.latitude);
                $('#longitude').val(position.coords.longitude);
                
                // Show coordinates text
                updateCoordinatesText(position.coords.latitude, position.coords.longitude);
            },
            // Error callback
            function(error) {
                var errorMessage;
                
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = 'Location permission denied. Please enable location services.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = 'Location information is unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMessage = 'The request to get your location timed out.';
                        break;
                    case error.UNKNOWN_ERROR:
                    default:
                        errorMessage = 'An unknown error occurred while getting your location.';
                        break;
                }
                
                showError(errorMessage);
            },
            // Options
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    };
    
    /**
     * Initialize the Google Map with the given coordinates
     */
    function initializeMap(latitude, longitude) {
        // Double check that our map element exists
        var mapElement = document.getElementById(mapElementId);
        if (!mapElement) {
            console.error('Map container element not found with ID: ' + mapElementId);
            return;
        }
        
        // Remove any loading messages
        $('.tow-location-message').remove();
        
        // Create LatLng object
        var latLng = new google.maps.LatLng(latitude, longitude);
        
        // Map options
        var mapOptions = {
            zoom: defaultZoom,
            center: latLng,
            mapTypeId: google.maps.MapTypeId.HYBRID, // Hybrid view (satellite with labels)
            streetViewControl: false,
            tilt: 0 // Set tilt to 0 for directly overhead view
        };
        
        try {
            // Create the map
            map = new google.maps.Map(mapElement, mapOptions);
            
            // Create marker
            marker = new google.maps.Marker({
                position: latLng,
                map: map,
                draggable: true, // Allow the user to drag the marker
                title: 'Your Location'
            });
            
            // Update coordinates when marker is dragged
            google.maps.event.addListener(marker, 'dragend', function() {
                var position = marker.getPosition();
                
                // Update hidden form fields
                $('#latitude').val(position.lat());
                $('#longitude').val(position.lng());
                
                // Update coordinates text
                updateCoordinatesText(position.lat(), position.lng());
            });
        } catch (e) {
            console.error('Error initializing Google Maps:', e);
            showError('There was an error initializing the map. Please try again.');
        }
    }
    
    /**
     * Update the coordinates text display
     */
    function updateCoordinatesText(latitude, longitude) {
        var coordsText = 'Latitude: ' + latitude.toFixed(6) + ', Longitude: ' + longitude.toFixed(6);
        
        if ($('.tow-location-coords').length === 0) {
            $('#tow-location-map-container').append('<div class="tow-location-coords">' + coordsText + '</div>');
        } else {
            $('.tow-location-coords').text(coordsText);
        }
    }
    
    /**
     * Submit the location to the server
     */
    window.submitLocation = function() {
        // Clear any previous messages
        $('.tow-location-message').remove();
        
        // Get form data
        var phoneNumber = $('#phone_number').val();
        var latitude = $('#latitude').val();
        var longitude = $('#longitude').val();
        
        // Validate data
        if (!phoneNumber) {
            showError('Please enter your phone number');
            return;
        }
        
        if (!latitude || !longitude) {
            showError('Please share your location first');
            return;
        }
        
        // Disable the submit button to prevent double submission
        $('#submit-location-btn').prop('disabled', true).addClass('disabled').text('Submitting...');
        
        // Send AJAX request
        $.ajax({
            url: towLocationShareParams.ajaxurl,
            type: 'POST',
            data: {
                action: 'tow_location_share_submit',
                nonce: towLocationShareParams.nonce,
                phone_number: phoneNumber,
                latitude: latitude,
                longitude: longitude
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    $('#tow-location-form').html(
                        '<div class="tow-location-success">' +
                        'Your location has been shared successfully! We\'ll contact you shortly.' +
                        '</div>'
                    );
                } else {
                    // Show error message
                    showError(response.data || 'An error occurred while submitting your location');
                    $('#submit-location-btn').prop('disabled', false).removeClass('disabled').text('Submit Location');
                }
            },
            error: function() {
                showError('A network error occurred. Please try again.');
                $('#submit-location-btn').prop('disabled', false).removeClass('disabled').text('Submit Location');
            }
        });
    };
    
    /**
     * Show an error message
     */
    function showError(message) {
        // Remove any existing error messages
        $('.tow-location-error').remove();
        
        // Add new error message
        $('#tow-location-form').prepend(
            '<div class="tow-location-error">' + message + '</div>'
        );
    }
    
})(jQuery); 