<?php
/*
Plugin Name: Mureed Sultan
Description: A plugin to fetch YouTube data and push it to WordPress posts.
Version: 1.0
Author: Your Name
*/

// Plugin activation hook
function mureedsultan_activate()
{
  // Create default plugin settings on activation
  add_option('mureedsultan_api_key', '');
  add_option('mureedsultan_channel_id', '');
  add_option('mureedsultan_auto_update_time', '');

  // Schedule cron event for automatic database updates
  if (!wp_next_scheduled('mureedsultan_cron_event')) {
    wp_schedule_event(time(), 'daily', 'mureedsultan_cron_event');
  }
}
register_activation_hook(__FILE__, 'mureedsultan_activate');

// Plugin deactivation hook
function mureedsultan_deactivate()
{
  // Remove plugin settings on deactivation
  delete_option('mureedsultan_api_key');
  delete_option('mureedsultan_channel_id');
  delete_option('mureedsultan_auto_update_time');

  // Remove cron event for automatic database updates
  wp_clear_scheduled_hook('mureedsultan_cron_event');
}
register_deactivation_hook(__FILE__, 'mureedsultan_deactivate');

// Add plugin settings page
function mureedsultan_settings_page()
{
?>

  <div class="wrap">
    <h1>Mureed Sultan Settings</h1>
    <form method="post" id="mureedsultan-fetch-form">
      <?php settings_fields('mureedsultan-settings-group'); ?>
      <?php do_settings_sections('mureedsultan-settings-group'); ?>
      <?php submit_button('Save Settings'); ?>

      <table class="form-table">
        <tr valign="top">
          <th scope="row">Notification</th>
          <td>
            <div id="mureedsultan_notification"></div>
          </td>
        </tr>

        <tbody>
          <tr valign="top">
            <th scope="row">API Key</th>
            <td>
              <input type="text" name="mureedsultan_api_key" value="<?php echo esc_attr(get_option('mureedsultan_api_key')); ?>">
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">Channel ID</th>
            <td>
              <input type="text" name="mureedsultan_channel_id" value="<?php echo esc_attr(get_option('mureedsultan_channel_id')); ?>">
            </td>
          </tr>
          <tr valign="top">
            <th scope="row"></th>
            <td>
              <button type="button" id="mureedsultan_fetch_playlist_button" class="button">Fetch Playlists</button>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">Automatic Update Time</th>
            <td>
              <input type="text" name="mureedsultan_auto_update_time" value="<?php echo esc_attr(get_option('mureedsultan_auto_update_time')); ?>">
              <p class="description">Specify the time for automatic database updates in HH:MM format.</p>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">Playlist</th>
            <td>
              <select name="mureedsultan_playlist" id="mureedsultan_playlist"></select>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">Category</th>
            <td>
              <?php
              $selected_category = isset($_POST['mureedsultan_category']) ? $_POST['mureedsultan_category'] : get_option('mureedsultan_category');
              $args = array(
                'name' => 'mureedsultan_category',
                'selected' => $selected_category,
                'show_option_none' => 'Select Category',
                'taxonomy' => 'category',
                'hide_empty' => 0,
              );
              wp_dropdown_categories($args);
              ?>

            </td>
          </tr>



        </tbody>

      </table>
      <?php submit_button('Fetch Data', 'primary', 'mureedsultan_fetch_data'); ?>
      <button type="button" id="mureedsultan_push_data_button" class="button">Push Data to Post</button>
      <input type="button" class="button-primary" id="mureedsultan-fetch-button" value="Fetch Data">

    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {

      var fetchDataButton = document.getElementById('mureedsultan-fetch-button');

fetchDataButton.addEventListener('click', function() {
  // Retrieve the selected playlist and category values
  var selectedPlaylist = document.getElementById('mureedsultan_playlist').value;
  var selectedCategory = document.getElementById('mureedsultan_category').value;

  // Prepare the form data
  var formData = new FormData();
  formData.append('action', 'mureedsultan_fetch_data');
  formData.append('mureedsultan_playlist', selectedPlaylist);
  formData.append('mureedsultan_category', selectedCategory);

  // Send the AJAX request
  var xhr = new XMLHttpRequest();
  xhr.open('POST', '<?php echo admin_url('admin-ajax.php'); ?>', true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      alert(xhr.responseText);
    } else {
      alert('Error: ' + xhr.statusText);
    }
  };
  xhr.onerror = function() {
    alert('Request failed');
  };
  xhr.send(formData);
});








      var fetchPlaylistsButton = document.getElementById('mureedsultan_fetch_playlist_button');

      fetchPlaylistsButton.addEventListener('click', function() {
        var apiKey = document.getElementsByName('mureedsultan_api_key')[0].value;
        var channelId = document.getElementsByName('mureedsultan_channel_id')[0].value;
        var playlistDropdown = document.getElementById('mureedsultan_playlist');

        var url = 'https://www.googleapis.com/youtube/v3/playlists?part=snippet&channelId=' + channelId + '&key=' + apiKey + '&maxResults=50';

        fetch(url)
          .then(function(response) {
            return response.json();
          })
          .then(function(data) {
            if (data && data.items) {
              console.log(data);
              playlistDropdown.innerHTML = '';
              data.items.forEach(function(playlist) {
                var option = document.createElement('option');
                option.value = playlist.id;
                option.text = playlist.snippet.title;
                playlistDropdown.appendChild(option);
              });
            }
          })
          .catch(function(error) {
            console.error('Error fetching playlists:', error);
          });
      });
      // Push the data into a WordPress post
      var pushDataToPostButton = document.getElementById('mureedsultan_push_data_button');

      pushDataToPostButton.addEventListener('click', function() {
        // Retrieve the selected playlist and category values
        var selectedPlaylist = document.getElementById('mureedsultan_playlist').value;
        var selectedCategory = document.getElementById('mureedsultan_category').value;


        // Fetch and push the data to the post using the selected playlist and category
        fetchPlaylistData(selectedPlaylist, selectedCategory);
      });

      // Function to fetch playlist data and push to post
      function fetchPlaylistData(playlistId, category) {
        var apiKey = document.getElementsByName('mureedsultan_api_key')[0].value;
        var selectedCategory = document.getElementById('mureedsultan_category').value;

        // Fetch videos from the selected playlist using the playlist ID
        var videosUrl = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId=' + playlistId + '&key=' + apiKey;

        fetch(videosUrl)
          .then(function(response) {
            return response.json();
          })
          .then(function(data) {
            if (data && data.items) {
              console.log('List of videos:', data.items);

              // Push the videos into WordPress posts
              data.items.forEach(function(video) {
                var videoTitle = video.snippet.title;
                var videoDescription = video.snippet.description;

                // Create a new post
                var post_data = new FormData();
                post_data.append('action', 'mureedsultan_fetch_data');
                post_data.append('mureedsultan_playlist', playlistId);
                post_data.append('mureedsultan_category', category);
                post_data.append('post_title', videoTitle);
                post_data.append('post_content', videoDescription);
                post_data.append('post_category[]',selectedCategory); 

                fetch(ajaxurl, {
                    method: 'POST',
                    body: post_data,
                    credentials: 'same-origin'
                  })
                  .then(function(response) {
                    return response.text();
                  })
                  .then(function(data) {
                    console.log(data);
                  })
                  .catch(function(error) {
                    console.error('Error pushing data to post:', error);
                  });
              });


              // Display the notification message
              var notificationContainer = document.getElementById('mureedsultan_notification');
              notificationContainer.innerHTML = 'Data pushed to posts under category: ' + category;
            }
          })
          .catch(function(error) {
            console.error('Error fetching playlist data:', error);
          });
      }

    });
  </script>
<?php
}

// Fetch playlists AJAX action
add_action('wp_ajax_mureedsultan_fetch_playlists', 'mureedsultan_ajax_fetch_playlists');
add_action('wp_ajax_nopriv_mureedsultan_fetch_playlists', 'mureedsultan_ajax_fetch_playlists');

function mureedsultan_ajax_fetch_playlists()
{
  $channel_id = sanitize_text_field($_POST['channel_id']);
  $api_key = sanitize_text_field($_POST['api_key']);

  $next_page_token = '';
  $playlists = array();

  do {
    $url = "https://www.googleapis.com/youtube/v3/playlists?part=snippet&maxResults=50&channelId={$channel_id}&key={$api_key}&pageToken={$next_page_token}";

    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
      wp_send_json_error('Error fetching playlists');
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!$data || !isset($data['items'])) {
      wp_send_json_error('Invalid API response');
    }

    $playlists = array_merge($playlists, $data['items']);

    $next_page_token = isset($data['nextPageToken']) ? $data['nextPageToken'] : '';
  } while (!empty($next_page_token));

  $response_data = array();
  foreach ($playlists as $playlist) {
    $response_data[] = array(
      'id' => $playlist['id'],
      'title' => $playlist['snippet']['title']
    );
  }

  wp_send_json_success(array('playlists' => $response_data));
}


function mureedsultan_fetch_data()
{
  $api_key = get_option('mureedsultan_api_key');
  $playlist_id = sanitize_text_field($_POST['mureedsultan_playlist']);
  $category = $_POST['mureedsultan_category'];
  $videos_url = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50&playlistId={$playlist_id}&key={$api_key}";

  echo 'API Key: ' . $api_key . '<br>';
  echo 'Playlist ID: ' . $playlist_id . '<br>';
  echo 'Category: ' . $category . '<br>';

  $videos_response = wp_remote_get($videos_url);
  if (is_wp_error($videos_response)) {
    wp_die('Error fetching videos');
  }

  $videos_body = wp_remote_retrieve_body($videos_response);
  $videos_data = json_decode($videos_body, true);

  if (!$videos_data || !isset($videos_data['items'])) {
    wp_die('Invalid API response for videos');
  }

  $videos = $videos_data['items'];

  // Push the videos into WordPress posts
  foreach ($videos as $video) {
    $video_title = $video['snippet']['title'];
    $video_description = $video['snippet']['description'];

    // Create a new post
    $post_data = array(
      'post_title' => $video_title,
      'post_content' => $video_description,
      'post_status' => 'publish',
      'post_category' => array($category)
    );

    // Insert the post into the database
    $post_id = wp_insert_post($post_data);

    if (is_wp_error($post_id)) {
      wp_die('Error creating post');
    }

    // Set the post format to "video"
    set_post_format($post_id, 'video');
  }

  wp_die('Data fetched and saved successfully');
}
add_action('admin_post_mureedsultan_fetch_data', 'mureedsultan_fetch_data');


// Register plugin settings
function mureedsultan_register_settings()
{
  add_option('mureedsultan_api_key', '');
  add_option('mureedsultan_channel_id', '');
  add_option('mureedsultan_auto_update_time', '00:01');
}
add_action('admin_init', 'mureedsultan_register_settings');

// Add the plugin settings page
function mureedsultan_add_menu_item()
{
  add_menu_page('Mureed Sultan', 'Mureed Sultan', 'manage_options', 'mureedsultan', 'mureedsultan_settings_page', 'dashicons-video-alt2');
}
add_action('admin_menu', 'mureedsultan_add_menu_item');

// Cron event callback for automatic database updates
function mureedsultan_auto_update_database()
{
  // Get the specified time for automatic database update
  $auto_update_time = get_option('mureedsultan_auto_update_time');

  // Schedule a cron job to update the database at the specified time
  wp_schedule_single_event(strtotime($auto_update_time), 'mureedsultan_cron_event');
}
add_action('mureedsultan_cron_event', 'mureedsultan_fetch_data');
add_action('admin_init', 'mureedsultan_auto_update_database');


// Add the mureedsultan-settings-group to the allowed options list
function add_mureedsultan_settings_to_allowed_options($options)
{
  $options[] = 'mureedsultan-settings-group';
  return $options;
}
add_filter('whitelist_options', 'add_mureedsultan_settings_to_allowed_options');
