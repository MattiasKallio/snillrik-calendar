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

require_once SNILLRIK_WAREHOUSE_DIR . 'settings.php';
require_once SNILLRIK_WAREHOUSE_DIR . 'info.php';
require_once SNILLRIK_WAREHOUSE_DIR . 'functions.php';
require_once SNILLRIK_WAREHOUSE_DIR . 'ajax_calls.php';
require_once SNILLRIK_WAREHOUSE_DIR . 'shortcodes.php';

require_once SNILLRIK_WAREHOUSE_DIR . 'includes/anlaggning.php';
require_once SNILLRIK_WAREHOUSE_DIR . 'includes/warehouse.php';
require_once SNILLRIK_WAREHOUSE_DIR . 'includes/cars.php';
require_once SNILLRIK_WAREHOUSE_DIR . 'includes/users.php';
require_once SNILLRIK_WAREHOUSE_DIR . 'includes/global.php';
require_once SNILLRIK_WAREHOUSE_DIR . 'includes/statuses.php';

new SNWH_Bilar();
new SNWH_Anlaggning();
new SNWH_Warehouse();
new SNWH_Users();
new SNWH_Global();
?>