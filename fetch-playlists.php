<?php

// Get the necessary parameters from the AJAX request
$api_key = $_POST['api_key'];
$channel_id = $_POST['channel_id'];
$max_results = $_POST['maxResults'];

// Build the API request URL
$request_url = sprintf(
    'https://www.googleapis.com/youtube/v3/playlists?part=snippet&channelId=%s&maxResults=%s&key=%s',
    $channel_id,
    $max_results,
    $api_key
);

// Make the API request using the WordPress HTTP API
$response = wp_remote_get($request_url);

// Check if the request was successful
if (is_wp_error($response)) {
    $error_message = $response->get_error_message();
    wp_send_json_error('Failed to fetch playlists: ' . $error_message);
}

// Parse the API response
$response_body = wp_remote_retrieve_body($response);
$data = json_decode($response_body, true);

// Check if the response was valid
if (!$data || isset($data['error'])) {
    wp_send_json_error('Failed to fetch playlists: Invalid response');
}

// Extract necessary information from the response
$playlists_data = array();
foreach ($data['items'] as $item) {
    $playlist = array(
        'id' => $item['id'],
        'title' => $item['snippet']['title']
    );
    $playlists_data[] = $playlist;
}

// Return the playlists as a JSON response
$response = array(
    'playlists' => $playlists_data
);
wp_send_json_success($response);
