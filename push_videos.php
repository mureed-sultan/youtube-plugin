<?php
// Include the WordPress bootstrap file
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

// Check if the current user has sufficient permissions
if (!current_user_can('edit_posts')) {
    wp_send_json_error('You do not have sufficient permissions to create a post.');
}

// Get the necessary parameters from the AJAX request
$selectedPlaylistId = sanitize_text_field($_GET['selectedPlaylistId']);
$selectedCategoryId = intval($_GET['selectedCategoryId']);
$apiKey = sanitize_text_field($_GET['apiKey']);

// Fetch all videos from the selected playlist using YouTube API
// Make sure to replace the API call with the appropriate YouTube API endpoint and parameters
// Perform the necessary logic to retrieve videos from the selected playlist using the YouTube API
// Store the fetched videos in an array variable, e.g., $videos

// Process the fetched videos
processVideos($videos, $selectedCategoryId, $apiKey);

// Function to process and create posts for the videos
function processVideos($videos, $selectedCategoryId, $apiKey)
{
    if (empty($videos)) {
        wp_send_json_error('No videos found in the selected playlist.');
    }

    foreach ($videos as $video) {
        $title = sanitize_text_field($video['title']);
        $description = sanitize_text_field($video['description']);
        $videoId = sanitize_text_field($video['videoId']);

        // Create a new post for the video
        $post_args = array(
            'post_title' => $title,
            'post_content' => $description,
            'post_status' => 'publish',
            'post_category' => array($selectedCategoryId)
        );

        $post_id = wp_insert_post($post_args);

        if ($post_id) {
            // Add a custom field to store the video ID
            update_post_meta($post_id, 'video_id', $videoId);
        } else {
            wp_send_json_error('Failed to create a post. Error: ' . get_last_error_message());
        }
    }

    // Send the number of pushed videos as a response
    wp_send_json_success(array('pushedVideosCount' => count($videos)));
}
