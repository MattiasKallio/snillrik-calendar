<?php
/**
 * Add eventer as post type
 */
function bizumbrella_init()
{
    $labels = [
        'name' => __('eventer'),
        'singular_name' => __('event'),
    ];
    $args = [
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'capability_type' => 'market',
        'map_meta_cap' => true,
        'register_meta_box_cb' => 'bizumbrella_eventer_metaboxes',
        'supports' => array('title', 'editor', 'author', 'thumbnail'),
        'taxonomies' => array('category'),
    ];
    register_post_type('event', $args);
}

add_action('init', 'bizumbrella_init');

/**
 * Add custom fields to eventer ie dates.
 */
function bizumbrella_eventer_metaboxes()
{
/*     add_meta_box(
'bizumbrella_eventer_meta_box',
'Datum från',
'bizumbrella_eventer_dates'
//'eventer_datefrom',
//'normal',
//'high'
); */

    add_meta_box(
        'bizumbrella_eventer_meta_box2',
        'Datumlista',
        'bizumbrella_eventer_datetime_list'
        //'eventer_datefrom',
        //'normal',
        //'high'
    );
}

/**
 * The date-time info code Metabox
 */
function bizumbrella_eventer_datetime_list()
{
    global $post;
    //$current_user = wp_get_current_user();
    //$datestart = get_post_meta($post->ID, "bizumbrella_eventer_datestart", true);
    //$timestart = get_post_meta($post->ID, 'bizumbrella_eventer_timestart', true);
    $time_list = get_post_meta($post->ID, 'bizumbrella_eventer_timeslist_full', true);
    $trtd_str = "";

    if ($time_list != "") {
        foreach (json_decode($time_list) as $item) {
            //echo print_r($item,true);
            $datum = $item->datum;
            $tid = $item->tid;
            $trtd_str .= "<tr><td class='bizumbrella_event_listdate'>$datum</td><td class='bizumbrella_event_listtime'>$tid</td><td><span class='delete_box'>X</span></td></tr>";
        }
    }

    $ovrigt_text = get_post_meta($post->ID, 'bizumbrella_eventer_ovrigttext', true);
    echo '<input type="hidden" name="bizumbrella_eventer_noncename" id="bizumbrella_eventer_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
    echo "<div><h3>Datum för eventen</h3><p>Lägg till de datum som är aktuella för eventen. Kom ihåg att spara eventen efter du lagt till alla datum.</p>";
    echo "<input type='hidden' id='bizumbrella_eventer_timeslist_full' name='bizumbrella_eventer_timeslist_full' value='" . $time_list . "' /> ";
    echo "<table id='bizumbrella_eventer_times_list'>";
    echo "<tr><th>Datum</th><th>Tid</th><th></th></tr>";
    echo $trtd_str;
    echo "</table>";
    echo '<table class="snillrik-warehouse-table">';
    echo '<tr><td><input type="date" class="widefat" placeholder="Datum start" id="bizumbrella_eventer_dateadd" value="' . $datestart . '"/></td>';
    echo '<td><input type="time" class="widefat" placeholder="Datum slut" id="bizumbrella_eventer_timeadd" value="' . $timestart . '"/></td>';
    echo '<td><input type="button" id="bizumbrella_eventer_addeventdate" value="+"></td></tr>';
    echo '</table></div>';
    echo "<div><h3>Övrig information</h3><p>Tid, plats, eller vad som kan vara viktigt att veta, det är den informationen<br />som dyker upp tillsammans med datumet längst ner till höger på eventsidan. <br />För att formatera texten kan man använda html-taggarna a, strong, em och br.</p>";

    echo '<table class="snillrik-warehouse-table">';
    echo '<tr><td><textarea class="widefat" placeholder="Övrig information" name="bizumbrella_eventer_ovrigttext" style="width:320px;">' . $ovrigt_text . '</textarea></td></tr>';
    echo '</table></div>';
}

add_action('save_post', 'bizumbrella_eventer_save_meta', 1, 2); // save the custom fields
function bizumbrella_eventer_save_meta($post_id, $post)
{

    if ($post->post_type == "event") {
        if (!wp_verify_nonce($_POST['bizumbrella_eventer_noncename'], plugin_basename(__FILE__))) {
            return $post->ID;
        }

        if (!current_user_can('edit_post', $post->ID)) {
            return $post->ID;
        }

        $menuscode_meta['bizumbrella_eventer_timeslist_full'] = $_POST['bizumbrella_eventer_timeslist_full'];
        $decoded = json_decode(stripslashes($menuscode_meta['bizumbrella_eventer_timeslist_full']));
        //die("watt!".print_r($decoded,true).":".$menuscode_meta['bizumbrella_eventer_timeslist_full']);
        delete_post_meta($post->ID, "bizumbrella_eventer_time_date");
        foreach ($decoded as $listitems) {

            add_post_meta($post->ID, "bizumbrella_eventer_time_date", $listitems->datum . " " . $listitems->tid);
        }
        //$menuscode_meta['bizumbrella_eventer_dateend'] = $_POST['bizumbrella_eventer_dateend'];
        //$menuscode_meta['bizumbrella_eventer_ovrigttext'] = sanitize_textarea_field($_POST['bizumbrella_eventer_ovrigttext']);
        //die("WAAT!".$menuscode_meta['bizumbrella_eventer_timeslist_full']);
        $allowed_html = [
            'a' => [
                'href' => [],
                'title' => [],
            ],
            'br' => [],
            'em' => [],
            'strong' => [],
        ];
        $menuscode_meta['bizumbrella_eventer_ovrigttext'] = wp_kses($_POST['bizumbrella_eventer_ovrigttext'], $allowed_html);

        //if(datediffInWeeks($menuscode_meta['bizumbrella_eventer_datestart'], $menuscode_meta['bizumbrella_eventer_dateend'])<2){

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