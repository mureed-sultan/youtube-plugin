<?php
// options.php

function my_youtube_plugin_register_settings() {
    register_setting('my_youtube_plugin_settings_group', 'my_youtube_plugin_api_key');
    register_setting('my_youtube_plugin_settings_group', 'my_youtube_plugin_playlist_id');
    register_setting('my_youtube_plugin_settings_group', 'my_youtube_plugin_channel_id');
    register_setting('my_youtube_plugin_settings_group', 'my_youtube_plugin_update_interval');
}

function my_youtube_plugin_settings_page() {
    // Code to display the settings page
}
