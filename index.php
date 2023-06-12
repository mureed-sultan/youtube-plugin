<?php
/*
Plugin Name: Mureed Sultan
Description: YouTube API Plugin
Version: 1.0
Author: Your Name
*/

// Add a menu item under Settings
add_action('admin_menu', 'mureedsultan_menu');
function mureedsultan_menu()
{
    add_options_page('Mureed Sultan Plugin', 'Mureed Sultan', 'manage_options', 'mureedsultan', 'mureedsultan_options_page');
}

// Render the options page
function mureedsultan_options_page()
{
    if (isset($_POST['mureedsultan_submit'])) {
        $api_key = sanitize_text_field($_POST['mureedsultan_api_key']);
        $channel_id = sanitize_text_field($_POST['mureedsultan_channel_id']);
        $buzzsprout_id = sanitize_text_field($_POST['mureedsultan_buzzsprout_id']);
        $buzzsprout_token = sanitize_text_field($_POST['mureedsultan_buzzsprout_token']);
        $buzzsprout_token = sanitize_text_field($_POST['mureedsultan_buzzsprout_token']);
        $time_format = sanitize_text_field($_POST['mureedsultan_time']);

        update_option('mureedsultan_api_key', $api_key);
        update_option('mureedsultan_channel_id', $channel_id);
        update_option('mureedsultan_buzzsprout_id', $buzzsprout_id);
        update_option('mureedsultan_buzzsprout_token', $buzzsprout_token);
        update_option('mureedsultan_time', $time_format);

        // Remove the existing scheduled event (if any)
        wp_clear_scheduled_hook('mureedsultan_update_database_event');

        // Schedule the event at the selected time
        if (!empty($time_format)) {
            $timestamp = strtotime('today ' . $time_format);
            if ($timestamp > time()) {
                wp_schedule_event($timestamp, 'daily', 'mureedsultan_update_database_event');
            }
        }

        echo '<div class="notice notice-success"><p>Settings updated successfully.</p></div>';
    }

    // Check if user has permission
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Get saved values
    $api_key = get_option('mureedsultan_api_key', '');
    $channel_id = get_option('mureedsultan_channel_id', '');
    $buzzsprout_id = get_option('mureedsultan_buzzsprout_id', '');
    $buzzsprout_token = get_option('mureedsultan_buzzsprout_token', '');
    $time_format = get_option('mureedsultan_time', '');

    // Update settings if form is submitted
    if (isset($_POST['mureedsultan_submit'])) {
        $api_key = sanitize_text_field($_POST['mureedsultan_api_key']);
        $channel_id = sanitize_text_field($_POST['mureedsultan_channel_id']);
        $buzzsprout_id = sanitize_text_field($_POST['mureedsultan_buzzsprout_id']);
        $buzzsprout_token = sanitize_text_field($_POST['mureedsultan_buzzsprout_token']);
        $time_format = sanitize_text_field($_POST['mureedsultan_time']);

        update_option('mureedsultan_api_key', $api_key);
        update_option('mureedsultan_channel_id', $channel_id);
        update_option('mureedsultan_time', $time_format);

        echo '<div class="notice notice-success"><p>Settings updated successfully.</p></div>';
    }
    ?>

    <div class="wrap">
        <h1>Mureed Sultan Plugin Settings</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="mureedsultan_api_key">YouTube API Key</label></th>
                    <td>
                        <input type="text" id="mureedsultan_api_key" name="mureedsultan_api_key"
                            value="<?php echo esc_attr($api_key); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="mureedsultan_channel_id">YouTube Channel ID</label></th>
                    <td>
                        <input type="text" id="mureedsultan_channel_id" name="mureedsultan_channel_id"
                            value="<?php echo esc_attr($channel_id); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="mureedsultan_playlist">Select Playlist</label></th>
                    <td>
                        <select id="mureedsultan_playlist" name="mureedsultan_playlist">
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="mureedsultan_category">Post Category</label></th>
                    <td>
                        <select id="mureedsultan_category" name="mureedsultan_category">
                            <?php
                            $categories = get_terms('category', 'orderby=name&hide_empty=0');
                            foreach ($categories as $category) {
                                echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <div id="timeout-counter"></div>

                    <th scope="row"><label for="mureedsultan_time">Time Format (HH:MM)</label></th>
                    <td>
                        <input type="text" id="mureedsultan_time" name="mureedsultan_time"
                            value="<?php echo esc_attr(get_option('mureedsultan_time')); ?>" class="regular-text">
                    </td>
                </tr>

                <tr>
                    <th scope="row"></th>
                    <td>
                        <input type="button" name="mureedsultan_fetch" id="mureedsultan_fetch" class="button-secondary"
                            value="Fetch Playlists">
                        <input type="button" name="mureedsultan_push" id="mureedsultan_push" class="button-primary"
                            value="Push Videos">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Buzzsprout ID:</th>
                    <td>
                        <input type="text" name="mureedsultan_buzzsprout_id" value="<?php echo esc_attr($buzzsprout_id); ?>"
                            class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Buzzsprout API Token:</th>
                    <td>
                        <input type="text" name="mureedsultan_buzzsprout_token"
                            value="<?php echo esc_attr($buzzsprout_token); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="mureedsultan_buzzprout_category">Post Category:</label>
                    </th>
                    <td>
                        <select id="mureedsultan_buzzprout_category" name="mureedsultan_buzzprout_category">
                            <?php
                            $categories = get_terms('category', 'orderby=name&hide_empty=0');
                            foreach ($categories as $category) {
                                echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Buzzsprout push count:</th>
                    <td>
                        <input type="text" name="mureedsultan_push_count" id="mureedsultan_push_count" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"></th>
                    <td>
                        <input type="button" name="mureedsultan_fetch" id="mureedsultan_podcastfetch"
                            class="button-secondary" value="Fetch podcast">
                        <input type="button" name="mureedsultan_push" id="mureedsultan_podcastpush" class="button-primary"
                            value="Push podcast">
                    </td>
                </tr>

            </table>
            <input type="submit" name="mureedsultan_submit" class="button-primary" value="Save Settings">
        </form>

        <div id="mureedsultan_push_status"></div>
    </div>
    <script>
        jQuery(document).ready(function ($) {
            // Fetch Podcasts Button Click Event
            // Fetch Podcasts Button Click Event
            $('#mureedsultan_podcastfetch').click(function () {
                var buzzsproutId = $('input[name="mureedsultan_buzzsprout_id"]').val();
                var buzzsproutToken = $('input[name="mureedsultan_buzzsprout_token"]').val();

                // Perform AJAX request to fetch podcasts
                $.ajax({
                    url: 'https://www.buzzsprout.com/api/' + buzzsproutId + '/episodes.json',
                    type: 'GET',
                    headers: {
                        'Authorization': 'Token ' + buzzsproutToken
                    },
                    success: function (response) {
                        console.log(response);
                        // Handle the response and display data in the console
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX error:', error);
                    }
                });
            });


            $('#mureedsultan_podcastpush').on('click', function () {
                var selectedPlaylistId = $('#mureedsultan_playlist').val();
                var selectedCategoryId = $('#mureedsultan_buzzprout_category').val();
                var pushCount = parseInt($('#mureedsultan_push_count').val());

                if (selectedCategoryId && pushCount) {
                    var buzzsproutId = $('input[name="mureedsultan_buzzsprout_id"]').val();
                    var buzzsproutToken = $('input[name="mureedsultan_buzzsprout_token"]').val();
                    var apiUrl = `https://www.buzzsprout.com/api/${buzzsproutId}/episodes.json?api_token=${buzzsproutToken}`;

                    // Fetch data from the Buzzsprout API
                    $.ajax({
                        url: apiUrl,
                        type: 'GET',
                        success: function (response) {
                            console.log(response.length);
                            var episodes = response;
                            if (episodes.length > 0) {
                                // Sort episodes by date (if required)

                                // Limit the number of episodes to push
                                episodes = episodes.slice(0, pushCount);

                                // Process each episode and push to database
                                episodes.forEach(function (episode) {
                                    var postData = {
                                        post_title: episode.title,
                                        post_content: episode.description,
                                        post_status: 'publish',
                                        post_category: [selectedCategoryId]
                                    };

                                    // Perform AJAX request to create a new post
                                    $.ajax({
                                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                        type: 'POST',
                                        data: {
                                            action: 'mureedsultan_create_post',
                                            post_data: postData
                                        },
                                        success: function (response) {
                                            console.log('Post created:', response.data.post_id);
                                            // Handle success, if required
                                        },
                                        error: function (xhr, status, error) {
                                            console.error('AJAX error:', error);
                                        }
                                    });
                                });
                            } else {
                                console.log('No episodes found.');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX error:', error);
                        }
                    });
                }
            });

            // asdasdas



            var playlistVideos = [];

            $('#mureedsultan_fetch').on('click', function () {
                var api_key = $('#mureedsultan_api_key').val();
                var channel_id = $('#mureedsultan_channel_id').val();

                // Perform AJAX request to fetch playlists
                $.ajax({
                    url: 'https://www.googleapis.com/youtube/v3/playlists',
                    data: {
                        part: 'snippet',
                        channelId: channel_id,
                        key: api_key,
                        maxResults: 50
                    },
                    success: function (response) {
                        var playlists = response.items;

                        // Populate the dropdown with fetched playlists
                        var dropdown = $('#mureedsultan_playlist');
                        dropdown.empty();

                        $.each(playlists, function (index, playlist) {
                            var option = $('<option></option>').attr('value', playlist.id).text(playlist.snippet.title);
                            dropdown.append(option);
                        });

                        // Reset the playlist videos array
                        playlistVideos = [];
                    },
                    error: function (xhr, status, error) {
                        console.log('AJAX error:', error);
                    }
                });
            });

            $('#mureedsultan_push').on('click', function () {
                var selectedPlaylistId = $('#mureedsultan_playlist').val();
                var selectedCategoryId = $('#mureedsultan_category').val();
                if (selectedPlaylistId && selectedCategoryId) {
                    var api_key = $('#mureedsultan_api_key').val();

                    // Perform AJAX request to fetch all videos from the selected playlist
                    $.ajax({
                        url: 'https://www.googleapis.com/youtube/v3/playlistItems',
                        data: {
                            part: 'snippet',
                            playlistId: selectedPlaylistId,
                            key: api_key,
                            maxResults: 50 // Change this value to the desired maximum number of videos per playlist
                        },
                        success: function (response) {
                            var videos = response.items;
                            processVideos(videos, selectedCategoryId, api_key);
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX error:', error);
                        }
                    });
                }
            });

            function processVideos(videos, selectedCategoryId, api_key) {
                if (videos.length === 0) {
                    var statusMessage = 'No videos found in the selected playlist.';
                    $('#mureedsultan_push_status').text(statusMessage);
                    return;
                }

                var video = videos.shift();
                var title = video.snippet.title;
                var description = video.snippet.description;
                var videoId = video.snippet.resourceId.videoId;

                // Create a new post for the video (you'll need to implement your own backend endpoint to handle this)
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>', // Use the admin-ajax.php file as the endpoint
                    type: 'POST',
                    data: {
                        action: 'mureedsultan_create_post', // Use the action hook for creating a post
                        title: title,
                        description: description,
                        videoId: videoId,
                        categoryId: selectedCategoryId,
                        apiKey: api_key
                    },
                    success: function (response) {
                        console.log(response);
                        // Handle the response as needed

                        // Process the next video
                        if (videos.length > 0) {
                            processVideos(videos, selectedCategoryId, api_key);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX error:', error);
                    }
                });
            }
        });
    </script>
    <?php
}



add_action('admin_menu', 'mureedsultan_add_settings_page');
function mureedsultan_add_settings_page()
{
    add_options_page('Mureed Sultan Plugin Settings', 'Mureed Sultan', 'manage_options', 'mureedsultan-settings', 'mureedsultan_render_settings_page');
}

add_action('admin_enqueue_scripts', 'mureedsultan_enqueue_scripts');
function mureedsultan_enqueue_scripts($hook)
{
    if ($hook === 'settings_page_mureedsultan-settings') {
        wp_enqueue_script('mureedsultan-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), '1.0', true);
        wp_localize_script('mureedsultan-script', 'mureedsultan_ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
    }
}

add_action('wp_ajax_mureedsultan_fetch_podcasts', 'mureedsultan_fetch_podcasts');
function mureedsultan_fetch_podcasts()
{
    $buzzsprout_id = sanitize_text_field($_POST['buzzsprout_id']);
    $buzzsprout_token = sanitize_text_field($_POST['buzzsprout_token']);
    $api_url = "https://www.buzzsprout.com/api/{$buzzsprout_id}/episodes.json?api_token={$buzzsprout_token}";

    // Fetch data from the Buzzsprout API
    $response = wp_remote_get($api_url);
    if (is_wp_error($response)) {
        wp_send_json_error('Failed to fetch data from the Buzzsprout API.');
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);

    // Check if the response contains data
    if (empty($data) || !isset($data['episodes']) || empty($data['episodes'])) {
        wp_send_json_error('No episodes found in the Buzzsprout API response.');
    }

    // Process the data and count the episodes
    $episodes = $data['episodes'];
    $count = count($episodes);

    wp_send_json_success($count);
}


add_action('wp_ajax_mureedsultan_push_podcasts', 'mureedsultan_push_podcasts');
function mureedsultan_push_podcasts()
{
    $buzzsprout_id = sanitize_text_field($_POST['buzzsprout_id']);
    $buzzsprout_token = sanitize_text_field($_POST['buzzsprout_token']);
    $api_url = "https://www.buzzsprout.com/api/{$buzzsprout_id}/episodes.json?api_token={$buzzsprout_token}";

    // Fetch data from the Buzzsprout API
    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        wp_send_json_error('Failed to fetch data from the Buzzsprout API.');
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);

    // Check if the response contains data
    if (empty($data) || !isset($data['episodes']) || empty($data['episodes'])) {
        wp_send_json_error('No episodes found in the Buzzsprout API response.');
    }

    // Process and push the episodes to posts
    $episodes = $data['episodes'];
    $count = 0;

    foreach ($episodes as $episode) {
        // Create a new post from each episode
        $post_title = $episode['title'];
        $post_content = $episode['description'];
        $post_date = $episode['published_at'];
        $post_category = $_POST['post_category'];

        $new_post = array(
            'post_title' => $post_title,
            'post_content' => $post_content,
            'post_date' => $post_date,
            'post_category' => array($post_category),
            'post_status' => 'publish',
        );

        // Insert the post
        $post_id = wp_insert_post($new_post);

        if (!is_wp_error($post_id)) {
            $count++;
        }
    }

    wp_send_json_success($count);
}
function get_youtube_video_thumbnail($videoId, $apiKey)
{
    $url = 'https://www.googleapis.com/youtube/v3/videos';
    $params = array(
        'part' => 'snippet',
        'id' => $videoId,
        'key' => $apiKey
    );

    $response = wp_remote_get(add_query_arg($params, $url));

    if (!is_wp_error($response)) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['items'][0]['snippet']['thumbnails']['medium']['url'])) {
            return $data['items'][0]['snippet']['thumbnails']['medium']['url'];
        }
    }

    return '';
}


// Create a new post using AJAX callback
add_action('wp_ajax_mureedsultan_create_post', 'mureedsultan_create_post_callback');
function mureedsultan_create_post_callback()
{
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('You do not have sufficient permissions to create a post.');
    }

    $title = sanitize_text_field($_POST['title']);
    $description = sanitize_text_field($_POST['description']);
    $videoId = sanitize_text_field($_POST['videoId']);
    $categoryId = intval($_POST['categoryId']);
    $apiKey = sanitize_text_field($_POST['apiKey']);

    // Check if a post with the same video ID already exists
    $existing_post = get_posts(array(
        'post_type' => 'post',
        'meta_query' => array(
            array(
                'key' => 'video_id',
                'value' => $videoId
            )
        ),
        'posts_per_page' => 1
    ));

    if (!empty($existing_post)) {
        // Update the existing post instead of creating a new one
        $post_id = $existing_post[0]->ID;

        $post_args = array(
            'ID' => $post_id,
            'post_title' => $title,
            'post_content' => $description,
            'post_status' => 'publish',
            'post_category' => array($categoryId)
        );

        $post_id = wp_update_post($post_args);
    } else {
        // Create a new post
        $post_args = array(
            'post_title' => $title,
            'post_content' => $description,
            'post_status' => 'publish',
            'post_category' => array($categoryId)
        );

        $post_id = wp_insert_post($post_args);

        // Add a custom field to store the video ID
        if ($post_id) {
            update_post_meta($post_id, 'video_id', $videoId);
        }
    }

    if ($post_id) {
        // Get the YouTube video thumbnail
        $thumbnail = get_youtube_video_thumbnail($videoId, $apiKey);

        // Set the thumbnail as the featured image
        if (!empty($thumbnail)) {
            $image_url = media_sideload_image($thumbnail, $post_id, $title, 'src');
            if (!is_wp_error($image_url)) {
                // Set the downloaded image as the featured image
                $attachment_id = attachment_url_to_postid($image_url);
                if ($attachment_id) {
                    set_post_thumbnail($post_id, $attachment_id);
                }
            }
        }

        // Send the number of pushed videos as a response
        wp_send_json_success(array('pushedVideosCount' => 1));
    } else {
        wp_send_json_error('Failed to create/update a post. Error: ' . get_last_error_message());
    }
}


// Function to get YouTube video thumbnail



// Fetch videos using AJAX callback
add_action('wp_ajax_mureedsultan_fetch_videos', 'mureedsultan_fetch_videos_callback');
function mureedsultan_fetch_videos_callback()
{
    $playlistId = sanitize_text_field($_POST['playlistId']);
    $apiKey = sanitize_text_field($_POST['apiKey']);

    $url = 'https://www.googleapis.com/youtube/v3/playlistItems';
    $params = array(
        'part' => 'snippet',
        'playlistId' => $playlistId,
        'key' => $apiKey,
        'maxResults' => 50,
    );

    $response = wp_remote_get(add_query_arg($params, $url));

    if (!is_wp_error($response)) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['items'])) {
            wp_send_json_success(array('items' => $data['items']));
        } else {
            wp_send_json_error('No videos found in the selected playlist.');
        }
    } else {
        wp_send_json_error('Failed to fetch videos from the selected playlist.');
    }
}
?>