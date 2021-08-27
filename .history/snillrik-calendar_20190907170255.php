<?php

/*
Plugin Name: Snillrik Calendar
Plugin URI: http://www.snillrik.se/
Description: Snillrik calendar is a plugin to make a new post type that have a date and time array for making pretty calendar typ..
Version: 0.1
Author: Mattias Kallio
Author URI: http://www.snillrik.se
License: GPL2
 */

DEFINE("SNILLRIK_CALENDAR_PLUGIN_URL", plugin_dir_url(__FILE__));
DEFINE("SNILLRIK_CALENDAR_DIR", plugin_dir_path(__FILE__));

require_once SNILLRIK_WAREHOUSE_DIR . 'calendar-type.php';
require_once SNILLRIK_WAREHOUSE_DIR . 'includes/shortcodes.php';

new SNCL_calendar();

?>