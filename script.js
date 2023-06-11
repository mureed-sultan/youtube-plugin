jQuery(document).ready(function($) {
    // Fetch playlists on button click
    $('#mureedsultan_fetch').on('click', function() {
        var api_key = $('#mureedsultan_api_key').val();
        var channel_id = $('#mureedsultan_channel_id').val();

        // Perform AJAX request to fetch playlists
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'mureedsultan_fetch_playlists',
                api_key: api_key,
                channel_id: channel_id
            },
            success: function(response) {
                if (response.success) {
                    var playlists = response.data.playlists;

                    // Populate the dropdown with fetched playlists
                    var dropdown = $('#mureedsultan_playlist');
                    dropdown.empty();

                    $.each(playlists, function(index, playlist) {
                        var option = $('<option></option>').attr('value', playlist.id).text(playlist.title);
                        dropdown.append(option);
                    });
                } else {
                    console.error(response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
            }
        });
    });

    // Push videos on button click
    $('#mureedsultan_push').on('click', function() {
        var selectedPlaylistId = $('#mureedsultan_playlist').val();
        var selectedCategoryId = $('#mureedsultan_category').val();
        if (selectedPlaylistId && selectedCategoryId) {
            var api_key = $('#mureedsultan_api_key').val();

            // Perform AJAX request to fetch videos from the selected playlist
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'mureedsultan_fetch_videos',
                    api_key: api_key,
                    playlist_id: selectedPlaylistId
                },
                success: function(response) {
                    if (response.success) {
                        var videos = response.data.videos;

                        // Process the fetched videos
                        processVideos(videos, selectedCategoryId, api_key);
                    } else {
                        console.error(response.data);
                    }
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
        var title = video.title;
        var description = video.description;
        var videoId = video.videoId;

        // Create a new post for the video (you'll need to implement your own backend endpoint to handle this)
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'mureedsultan_create_post',
                title: title,
                description: description,
                video_id: videoId,
                category_id: selectedCategoryId,
                api_key: api_key
            },
            success: function(response) {
                if (response.success) {
                    console.log(response.data);
                    // Handle the response as needed

                    // Process the next video
                    if (videos.length > 0) {
                        processVideos(videos, selectedCategoryId, api_key);
                    }
                } else {
                    console.error(response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
            }
        });
    }
});
