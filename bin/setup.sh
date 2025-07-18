#!/bin/bash

wp-env run cli wp theme activate twentytwentythree
wp-env run cli wp rewrite structure /%postname%
wp-env run cli wp option update blogname "Display Site Notification Bar"
wp-env run cli wp option update blogdescription "Display a notice bar on your WP home page."
