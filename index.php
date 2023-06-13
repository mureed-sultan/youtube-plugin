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
    // Update settings if form is submitted
    if (isset($_POST['mureedsultan_submit'])) {
        $api_key = sanitize_text_field($_POST['mureedsultan_api_key']);
        $channel_id = sanitize_text_field($_POST['mureedsultan_channel_id']);
        $time_format = sanitize_text_field($_POST['mureedsultan_time']);

        update_option('mureedsultan_api_key', $api_key);
        update_option('mureedsultan_channel_id', $channel_id);
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

    // Update settings if form is submitted
    if (isset($_POST['mureedsultan_submit'])) {
        $api_key = sanitize_text_field($_POST['mureedsultan_api_key']);
        $channel_id = sanitize_text_field($_POST['mureedsultan_channel_id']);

        update_option('mureedsultan_api_key', $api_key);
        update_option('mureedsultan_channel_id', $channel_id);

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
                        <input type="text" id="mureedsultan_api_key" name="mureedsultan_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="mureedsultan_channel_id">YouTube Channel ID</label></th>
                    <td>
                        <input type="text" id="mureedsultan_channel_id" name="mureedsultan_channel_id" value="<?php echo esc_attr($channel_id); ?>" class="regular-text">
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
                    <th scope="row"><label for="mureedsultan_time">Time Format (HH:MM)</label></th>
                    <td>
                        <input type="text" id="mureedsultan_time" name="mureedsultan_time" value="<?php echo esc_attr(get_option('mureedsultan_time')); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"></th>
                    <td>
                        <input type="button" name="mureedsultan_fetch" id="mureedsultan_fetch" class="button-secondary" value="Fetch Playlists">
                        <input type="button" name="mureedsultan_push" id="mureedsultan_push" class="button-primary" value="Push Videos">
                    </td>
                </tr>
                <!-- Buzzpropt section start  -->
                <tr>
                    <th scope="row"><label for="mureedsultan_buzzprout_id">Buzzsprout ID</label></th>
                    <td>
                        <input type="text" id="mureedsultan_buzzprout_id" name="mureedsultan_buzzprout_id" value="<?php echo esc_attr($buzzprout_id); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="mureedsultan_buzzprout_api_key">Buzzsprout API Key</label></th>
                    <td>
                        <input type="text" id="mureedsultan_buzzprout_api_key" name="mureedsultan_buzzprout_api_key" value="<?php echo esc_attr($buzzprout_api_key); ?>" class="regular-text">
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
                    <th scope="row"><label for="mureedsultan_time">Time to Update (minutes)</label></th>
                    <td>
                        <input type="number" id="mureedsultan_time" name="mureedsultan_time" value="<?php echo esc_attr(get_option('mureedsultan_time')); ?>" class="regular-text" min="1">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="mureedsultan_num_podcasts">Number of Podcasts</label></th>
                    <td>
                        <input type="number" id="mureedsultan_num_podcasts" name="mureedsultan_num_podcasts" value="<?php echo esc_attr($num_podcasts); ?>" class="regular-text" min="1">
                    </td>
                </tr>
                <tr>
                    <th scope="row"></th>
                    <td>
                        <input type="button" name="mureedsultan_podcast_fetch" id="mureedsultan_podcast_fetch" class="button-secondary" value="Fetch Podcasts">
                        <input type="button" name="mureedsultan_podcast_push" id="mureedsultan_podcast_push" class="button-primary" value="Push Podcasts">
                    </td>
                </tr>
            </table>
            <input type="submit" name="mureedsultan_submit" class="button-primary" value="Save Settings">
        </form>

        <div id="mureedsultan_push_status"></div>
    </div>
    <script>
        jQuery(document).ready(function($) {
            // podcast data 
            $('#mureedsultan_podcast_fetch').on('click', function() {
                var api_token = $('#mureedsultan_buzzprout_api_key').val();
                var buzzprout_id = $('#mureedsultan_buzzprout_id').val();
                console.log(api_token, buzzprout_id)
                // Perform AJAX request to fetch podcast data
                $.ajax({
                    url: 'https://www.buzzsprout.com/api/' + buzzprout_id + '/episodes.json',
                    data: {
                        api_token: api_token
                    },
                    success: function(response) {
                        console.log(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                    }
                });
            });
            // Youtube section data fetching
            var playlistVideos = [];
            $('#mureedsultan_fetch').on('click', function() {
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
                    success: function(response) {
                        var playlists = response.items;

                        // Populate the dropdown with fetched playlists
                        var dropdown = $('#mureedsultan_playlist');
                        dropdown.empty();

                        $.each(playlists, function(index, playlist) {
                            var option = $('<option></option>').attr('value', playlist.id).text(playlist.snippet.title);
                            dropdown.append(option);
                        });

                        // Reset the playlist videos array
                        playlistVideos = [];
                    },
                    error: function(xhr, status, error) {
                        console.log('AJAX error:', error);
                    }
                });
            });

            $('#mureedsultan_push').on('click', function() {
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
                        success: function(response) {
                            var videos = response.items;
                            processVideos(videos, selectedCategoryId, api_key);
                        },
                        error: function(xhr, status, error) {
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
                    success: function(response) {
                        console.log(response);
                        // Handle the response as needed

                        // Process the next video
                        if (videos.length > 0) {
                            processVideos(videos, selectedCategoryId, api_key);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                    }
                });
            }
        });
    </script>
<?php
}


// Function to get YouTube video thumbnail

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
    $rawDescription = sanitize_text_field($_POST['description']);
    $videoId = sanitize_text_field($_POST['videoId']);
    $videoUrl = sanitize_text_field($_POST['videoUrl']);
    $categoryId = intval($_POST['categoryId']);
    $apiKey = sanitize_text_field($_POST['apiKey']);
    $videoBy = isset($_POST['videoBy']) ? sanitize_text_field($_POST['videoBy']) : '';
    $videoType = isset($_POST['videoType']) ? sanitize_text_field($_POST['videoType']) : '';
    $videoCategory = isset($_POST['videoCategory']) ? sanitize_text_field($_POST['videoCategory']) : '';
    $description = format_description($rawDescription);

    $existing_post = get_posts(
        array(
            'post_type' => 'post',
            'meta_query' => array(
                array(
                    'key' => 'video_id',
                    'value' => $videoId
                )
            ),
            'posts_per_page' => 1
        )
    );
    if (!empty($existing_post)) {
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

        // Get the YouTube video thumbnail
        $thumbnail = get_youtube_video_thumbnail($videoId, $apiKey);

        $post_args = array(
            'post_title' => $title,
            'post_content' => $description,
            'post_status' => 'publish',
            'post_category' => array($categoryId)
        );

        $post_id = wp_insert_post($post_args);


        if ($post_id) {
            // Add a custom field to store the video ID
            update_post_meta($post_id, 'video_id', $videoId);

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

            // Generate a relevant video URL
            $videoLink = generate_relevant_video_url($videoId);

            // Update ACF fields with video information
            if (!empty($videoLink)) {
                update_field('video_link', $videoLink, $post_id);
            }
            if (!empty($videoBy)) {
                update_field('video_by', $videoBy, $post_id);
            }
            if (!empty($videoType)) {
                update_field('video_type', $videoType, $post_id);
            }
            if (!empty($videoCategory)) {
                update_field('video_category', $videoCategory, $post_id);
            }

            // Send the number of pushed videos as a response
            wp_send_json_success(array('pushedVideosCount' => 1));
        } else {
            wp_send_json_error('Failed to create a post. Error: ' . get_last_error_message());
        }
    };
}

function format_description($description)
{
    // Convert URLs to clickable links
    $description = preg_replace_callback('/(https?:\/\/[^\s]+)/', function ($matches) {
        return '<a href="' . $matches[0] . '" target="_blank">' . $matches[0] . '</a>';
    }, $description);

    // Add line breaks between paragraphs
    $description = nl2br($description);

    return $description;
}



function generate_relevant_video_url($title)
{
    $youtubeUrl = "https://www.youtube.com/embed/" . $title;
    return $youtubeUrl;
}



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