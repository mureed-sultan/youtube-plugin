jQuery(document).ready(function($) {
    // Handle form submission
    $('#youtube-settings-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.post(ajaxurl, formData + '&action=save_youtube_plugin_settings', function(response) {
            if (response === '1') {
                alert('Settings saved successfully.');
            } else {
                alert('Failed to save settings.');
            }
        });
    });

    // Handle manual data fetch
    $('#get-data-button').on('click', function() {
        $.post(ajaxurl, {
            action: 'my_youtube_plugin_fetch_data'
        }, function(response) {
            if (response.error) {
                alert(response.error);
            } else {
                $('#data-result').html('Fetched ' + response.count + ' videos from YouTube and pushed to WordPress.');
            }
        });
    });
});
