<?php

/*
Plugin Name: Snillrik Calendar
Plugin URI: http://www.snillrik.se/
Description: Snillrik calendar is a plugin to make a new post type that have a date and time array for making pretty calendar typ..
Version: 0.1
Author: Mattias Kallio
Author URI: http://www.snillrik.se
License: GPL2
Tested up to: 6.3.1
 */

DEFINE("SNILLRIK_CALENDAR_PLUGIN_URL", plugin_dir_url(__FILE__));
DEFINE("SNILLRIK_CALENDAR_DIR", plugin_dir_path(__FILE__));
DEFINE("SNILLRIK_CALENDAR_POST_TYPE_NAME", "snillrik_calendar");
DEFINE("SNILLRIK_CALENDAR_TIMEDATE_NAME", "snillrik_calendar_time_date");


require_once SNILLRIK_CALENDAR_DIR . 'includes/calendar-type.php';
require_once SNILLRIK_CALENDAR_DIR . 'includes/settings.php';
require_once SNILLRIK_CALENDAR_DIR . 'includes/shortcodes.php';

new SNCL_calendar();
new SNCL_shortcodes();

function snillrik_calendar_add_admin_scripts(){
    wp_enqueue_style('snillrik-calendar-main', SNILLRIK_CALENDAR_PLUGIN_URL . 'css/main.css');
    wp_enqueue_script('snillrik-calendar-admin-script', SNILLRIK_CALENDAR_PLUGIN_URL . 'js/admin-main.js', array('jquery'));
}
add_action('admin_enqueue_scripts', 'snillrik_calendar_add_admin_scripts');

function snillrik_calendar_add_scripts(){
    wp_enqueue_style('snillrik-calendar-swiper', SNILLRIK_CALENDAR_PLUGIN_URL . 'css/swiper.min.css');
    wp_enqueue_style('snillrik-calendar-main', SNILLRIK_CALENDAR_PLUGIN_URL . 'css/front.css');
    wp_enqueue_script('snillrik-calendar-swiper-script', SNILLRIK_CALENDAR_PLUGIN_URL . 'js/swiper.min.js', array('jquery'));
    wp_register_script('snillrik-calendar-main-script', SNILLRIK_CALENDAR_PLUGIN_URL . 'js/main.js', array('jquery'));
}
add_action('wp_enqueue_scripts', 'snillrik_calendar_add_scripts');
?>