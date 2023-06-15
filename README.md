**Mureed Sultan Youtube API Plugin Documentation**

**Plugin Overview**

The "Mureed Sultan" plugin is a WordPress plugin that integrates with the YouTube API and Buzzsprout API to fetch and push YouTube videos and Buzzsprout podcasts to your WordPress website. It provides a settings page where users can enter their API keys and configure various options for fetching and pushing videos and podcasts.

**Plugin Details**

**Plugin Name:** Mureed Sultan

**Description:** YouTube API Plugin

**Version:** 1.0

**Author:** Mureed Sultan


**Plugin Functionality**

The Mureed Sultan plugin adds a menu item under the "Settings" section in the WordPress admin panel. The menu item allows users with sufficient permissions to access the Mureed Sultan Plugin settings page.


**Settings Page**

The settings page provides a form where users can configure the plugin settings. The form includes the following fields:


**YouTube API Key:** The API key required to access the YouTube API.

**YouTube Channel ID:** The ID of the YouTube channel from which videos will be fetched.

**Select Playlist:** A dropdown to select a playlist from the YouTube channel.

**Post Category:** A dropdown to select the category for the WordPress posts created from the fetched videos.

**Time Format (HH:MM):** The format for specifying the time to schedule the event for updating the database.

**Fetch Playlists Button:** A button to fetch the playlists from the YouTube channel.

**Push Videos Button:** A button to push the fetched videos to WordPress posts.


Additionally, the settings page includes fields for the Buzzsprout integration:


**Buzzsprout ID:** The ID of the Buzzsprout podcast.

**Buzzsprout API Key:** The API key required to access the Buzzsprout API.

**Podcast Category:** A dropdown to select the category for the WordPress posts created from the pushed podcasts.

**Number of Podcasts:** The number of podcasts to fetch or push.

**Fetch Podcasts Button:** A button to fetch the podcasts from the Buzzsprout API.

**Push Podcasts Button:** A button to push the fetched podcasts to WordPress posts.


**AJAX Requests**

The plugin utilizes AJAX requests to fetch YouTube videos and Buzzsprout podcasts and push them to WordPress posts.

**Fetch Playlists:** When the "Fetch Playlists" button is clicked, an AJAX request is sent to the YouTube API to fetch the playlists from the specified YouTube channel. The fetched playlists are then populated in the dropdown field.

**Push Videos:** When the "Push Videos" button is clicked, an AJAX request is sent for each fetched video to create a WordPress post with the video details, such as title, description, and video URL.

**Fetch Podcasts:** When the "Fetch Podcasts" button is clicked, an AJAX request is sent to the Buzzsprout API to fetch the specified number of podcasts. The fetched podcasts' details are then used to create WordPress posts.

**Push Podcasts:** When the "Push Podcasts" button is clicked, an AJAX request is sent for each fetched podcast to create a WordPress post with the podcast details, such as title, description, and audio URL.


**Installation**

To install the Mureed Sultan plugin, follow these steps:

**1.** Download the plugin files.

**2.** Upload the plugin folder to the **wp-content/plugins/** directory.

**3.** Activate the plugin through the WordPress admin panel.


**Configuration**

After installing and activating the plugin, follow these steps to configure the plugin settings:

**1.** Log in to the WordPress admin panel.

**2.** Go to the "Settings" section and click on "Mureed Sultan."

**3.** Enter the YouTube API Key and YouTube Channel ID.

**4.** Select a playlist from the dropdown.

**5.** Choose a post category for the fetched videos.

**6.** Specify the time format (HH:MM) for scheduling the event to update the database.

**7.** Click the "Fetch Playlists" button to fetch the playlists from the specified YouTube channel. The fetched playlists will be populated in the dropdown field.

**8.** Optionally, enter the Buzzsprout ID and Buzzsprout API Key for integrating with Buzzsprout.

**9.** Select a podcast category for the pushed podcasts.

**10.** Specify the number of podcasts to fetch or push.

**11.** Click the "Fetch Podcasts" button to fetch the podcasts from Buzzsprout.

**12.** Once the playlists and podcasts are fetched, you can click the "Push Videos" or "Push Podcasts" buttons to create WordPress posts with the respective video or podcast details.

Note: Make sure to save the settings after making any changes.

**Support and Documentation**

For any questions or issues related to the Mureed Sultan plugin, you can reach out to the author, Mureed Sultan, for support. Additionally, the plugin documentation may provide further information on how to use and customize the plugin's features.
