<?php

// Get the necessary parameters from the AJAX request
$api_key = $_POST['api_key'];
$playlist_id = $_POST['playlist_id'];
$max_results = $_POST['maxResults'];

// Build the API request URL
$request_url = sprintf(
    'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=%s&maxResults=%s&key=%s',
    $playlist_id,
    $max_results,
    $api_key
);

// Make the API request using the WordPress HTTP API
$response = wp_remote_get($request_url);

// Check if the request was successful
if (is_wp_error($response)) {
    $error_message = $response->get_error_message();
    wp_send_json_error('Failed to fetch videos: ' . $error_message);
}

// Parse the API response
$response_body = wp_remote_retrieve_body($response);
$data = json_decode($response_body, true);

// Check if the response was valid
if (!$data || isset($data['error'])) {
    wp_send_json_error('Failed to fetch videos: Invalid response');
}

// Extract necessary information from the response
$videos_data = array();
foreach ($data['items'] as $item) {
    $video = array(
        'videoId' => $item['snippet']['resourceId']['videoId'],
        'title' => $item['snippet']['title'],
        'description' => $item['snippet']['description']
    );
    $videos_data[] = $video;
}

// Return the videos as a JSON response
$response = array(
    'videos' => $videos_data
);
wp_send_json_success($response);
