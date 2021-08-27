<?php

/**
 * Calendar class
 */
class SNCL
{
    public function __construct()
    {
/*         add_action('init', array($this, 'snillrik_warehouse_bil_posttype'));
        add_action('save_post', array($this, 'snillrik_bil_info_save_meta'), 1, 2); // save the custom fields
        add_action('wp_ajax_snillrik_warehouse_get_car_info', array($this, 'get_car_info'));
        add_action('wp_ajax_snillrik_warehouse_save_car_info', array($this, 'save_car_info'));
        add_action('wp_ajax_snillrik_warehouse_change_multiple_status', array($this, 'change_multiple_status'));
        add_action('wp_ajax_snillrik_warehouse_change_status', array($this, 'ajax_change_status')); */

        add_action('init', array($this,'calendar_init'));

    }

/**
 * Add calendar post type
 */
public function calendar_init()
{
    $labels = [
        'name' => __('Calendar'),
        'singular_name' => __('Calendar date'),
    ];
    $args = [
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'register_meta_box_cb' => 'snillrik_calendar_metaboxes',
        'supports' => array('title', 'editor', 'author', 'thumbnail'),
        'taxonomies' => array('category'),
    ];
    register_post_type('snillrik_calendar', $args);
}



/**
 * Add custom fields to eventer ie dates.
 */
function snillrik_calendar_metaboxes(){
    add_meta_box(
        'snillrik_calendar_metabox',
        'Datelist',
        'snillrik_calendar_datetime_list'
    );
}

/**
 * The date-time info code Metabox
 */
public function snillrik_calendar_datetime_list()
{
    global $post;
    $time_list = get_post_meta($post->ID, 'snillrik_calendar_timeslist_full', true);
    $trtd_str = "";

    if ($time_list != "") {
        foreach (json_decode($time_list) as $item) {
            //echo print_r($item,true);
            $datum = $item->datum;
            $tid = $item->tid;
            $trtd_str .= "<tr><td class='bizumbrella_event_listdate'>$datum</td><td class='bizumbrella_event_listtime'>$tid</td><td><span class='delete_box'>X</span></td></tr>";
        }
    }

    $ovrigt_text = get_post_meta($post->ID, 'snillrik_calendar_ovrigttext', true);
    echo '<input type="hidden" name="snillrik_calendar_noncename" id="snillrik_calendar_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
    echo "<div><h3>Datum för eventen</h3><p>Lägg till de datum som är aktuella för eventen. Kom ihåg att spara eventen efter du lagt till alla datum.</p>";
    echo "<input type='hidden' id='snillrik_calendar_timeslist_full' name='snillrik_calendar_timeslist_full' value='" . $time_list . "' /> ";
    echo "<table id='snillrik_calendar_times_list'>";
    echo "<tr><th>Datum</th><th>Tid</th><th></th></tr>";
    echo $trtd_str;
    echo "</table>";
    echo '<table class="snillrik-warehouse-table">';
    echo '<tr><td><input type="date" class="widefat" placeholder="Datum start" id="snillrik_calendar_dateadd" value="' . $datestart . '"/></td>';
    echo '<td><input type="time" class="widefat" placeholder="Datum slut" id="snillrik_calendar_timeadd" value="' . $timestart . '"/></td>';
    echo '<td><input type="button" id="snillrik_calendar_addeventdate" value="+"></td></tr>';
    echo '</table></div>';
    echo "<div><h3>Övrig information</h3><p>Tid, plats, eller vad som kan vara viktigt att veta, det är den informationen<br />som dyker upp tillsammans med datumet längst ner till höger på eventsidan. <br />För att formatera texten kan man använda html-taggarna a, strong, em och br.</p>";

    echo '<table class="snillrik-warehouse-table">';
    echo '<tr><td><textarea class="widefat" placeholder="Övrig information" name="snillrik_calendar_ovrigttext" style="width:320px;">' . $ovrigt_text . '</textarea></td></tr>';
    echo '</table></div>';
}

add_action('save_post', 'snillrik_calendar_save_meta', 1, 2); // save the custom fields
function snillrik_calendar_save_meta($post_id, $post)
{

    if ($post->post_type == "event") {
        if (!wp_verify_nonce($_POST['snillrik_calendar_noncename'], plugin_basename(__FILE__))) {
            return $post->ID;
        }

        if (!current_user_can('edit_post', $post->ID)) {
            return $post->ID;
        }

        $menuscode_meta['snillrik_calendar_timeslist_full'] = $_POST['snillrik_calendar_timeslist_full'];
        $decoded = json_decode(stripslashes($menuscode_meta['snillrik_calendar_timeslist_full']));
        //die("watt!".print_r($decoded,true).":".$menuscode_meta['snillrik_calendar_timeslist_full']);
        delete_post_meta($post->ID, "snillrik_calendar_time_date");
        foreach ($decoded as $listitems) {

            add_post_meta($post->ID, "snillrik_calendar_time_date", $listitems->datum . " " . $listitems->tid);
        }
        //$menuscode_meta['snillrik_calendar_dateend'] = $_POST['snillrik_calendar_dateend'];
        //$menuscode_meta['snillrik_calendar_ovrigttext'] = sanitize_textarea_field($_POST['snillrik_calendar_ovrigttext']);
        //die("WAAT!".$menuscode_meta['snillrik_calendar_timeslist_full']);
        $allowed_html = [
            'a' => [
                'href' => [],
                'title' => [],
            ],
            'br' => [],
            'em' => [],
            'strong' => [],
        ];
        $menuscode_meta['snillrik_calendar_ovrigttext'] = wp_kses($_POST['snillrik_calendar_ovrigttext'], $allowed_html);

        //if(datediffInWeeks($menuscode_meta['snillrik_calendar_datestart'], $menuscode_meta['snillrik_calendar_dateend'])<2){

        foreach ($menuscode_meta as $key => $value) { // Cycle through the $events_meta array!
            if ($post->post_type == 'revision') {
                return;
            }

            $value = implode(',', (array) $value); // If $value is an array, make it a CSV (unlikely)
            if (get_post_meta($post->ID, $key, false)) { // If the custom field already has a value
                update_post_meta($post->ID, $key, $value);
            } else { // If the custom field doesn't have a value
                add_post_meta($post->ID, $key, $value);
            }
            if (!$value) {
                delete_post_meta($post->ID, $key);
            }
        }

    }
?>