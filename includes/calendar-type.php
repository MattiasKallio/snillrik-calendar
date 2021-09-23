<?php

/**
 * Calendar class
 */
class SNCL_calendar
{

    public function __construct()
    {
        add_action('init', array($this, 'calendar_init'));
        add_action('save_post', array($this, 'snillrik_calendar_save_meta'), 1, 2); // save the custom fields
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
        /* 
        register_taxonomy(
            'snillrik_calendar-category',
            'calendar',
            array(
                'label' => __( 'Category' ),
                'rewrite' => array( 'slug' => 'team-category' ),
                'hierarchical' => true,
            )
        ); */

        $args = [
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'register_meta_box_cb' => array($this, 'snillrik_calendar_metaboxes'),
            'supports' => array('title', 'editor', 'author', 'thumbnail'),
            'taxonomies' => array('calendar', 'category'),
        ];

        if (!post_type_exists(SNILLRIK_CALENDAR_POST_TYPE_NAME))
            register_post_type(SNILLRIK_CALENDAR_POST_TYPE_NAME, $args);
    }

    /**
     * Add custom fields to calendar ie dates.
     */
    public function snillrik_calendar_metaboxes()
    {
        add_meta_box(
            'snillrik_calendar_metabox',
            'Datelist',
            array($this, 'snillrik_calendar_datetime_list')
        );
    }

    /**
     * The date-time info code Metabox
     */
    public function snillrik_calendar_datetime_list()
    {
        global $post;
        $time_list = get_post_meta($post->ID, 'snillrik_calendar_timeslist_full', true);
        $trtd_str = "inga datum valda";

        if ($time_list != "") {
            $trtd_str = "";
            foreach (json_decode($time_list) as $item) {
                //echo print_r($item,true);
                $datum = $item->datum;
                $tid = $item->tid;
                $trtd_str .= "<tr><td class='snillrik_event_listdate'>$datum</td><td class='snillrik_event_listtime'>$tid</td><td><span class='delete_box'>X</span></td></tr>";
            }
        }

        //echo SNILLRIK_CALENDAR_POST_TYPE_NAME;

        $ovrigt_text = get_post_meta($post->ID, 'snillrik_calendar_ovrigttext', true);
        echo '<input type="hidden" name="snillrik_calendar_noncename" id="snillrik_calendar_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
        echo "<div><h3>Datum för eventet</h3><p>Lägg till de datum som är aktuella för eventet. Kom ihåg att spara eventen efter du lagt till alla datum.</p>";
        echo "<input type='hidden' id='snillrik_calendar_timeslist_full' name='snillrik_calendar_timeslist_full' value='" . $time_list . "' /> ";
        echo "<table id='snillrik_calendar_times_list' class='snillrik-calendar-table'>";
        echo "<tr><th>Datum</th><th>Tid</th><th></th></tr>";
        echo $trtd_str;
        echo "</table>";
        echo '<table class="snillrik-calendar-table">';
        echo '<tr><td><input type="date" class="widefat" placeholder="Datum start" id="snillrik_calendar_dateadd" value=""/></td>';
        echo '<td><input type="time" class="widefat" placeholder="Datum slut" id="snillrik_calendar_timeadd" value=""/></td>';
        echo '<td><input type="button" id="snillrik_calendar_addeventdate" value="+"></td></tr>';
        echo '</table></div>';
        echo "<div><h3>Övrig information</h3><p>Tid, plats, eller vad som kan vara viktigt att veta, det är den informationen<br />som dyker upp tillsammans med datumet längst ner till höger på eventsidan. <br />För att formatera texten kan man använda html-taggarna a, strong, em och br.</p>";

        echo '<table class="snillrik-calendar-table">';
        echo '<tr><td><textarea class="widefat" placeholder="Övrig information" name="snillrik_calendar_ovrigttext" style="width:320px;">' . $ovrigt_text . '</textarea></td></tr>';
        echo '</table></div>';
    }

    public function snillrik_calendar_save_meta($post_id, $post)
    {
        if ($post->post_type == SNILLRIK_CALENDAR_POST_TYPE_NAME) {
            if (!isset($_POST['snillrik_calendar_noncename']) || !wp_verify_nonce($_POST['snillrik_calendar_noncename'], plugin_basename(__FILE__))) {
                return $post->ID;
            }

            if (!current_user_can('edit_post', $post->ID)) {
                return $post->ID;
            }

            $menuscode_meta['snillrik_calendar_timeslist_full'] = $_POST['snillrik_calendar_timeslist_full'];
            $decoded = json_decode(stripslashes($menuscode_meta['snillrik_calendar_timeslist_full']));

            delete_post_meta($post->ID, SNILLRIK_CALENDAR_TIMEDATE_NAME);
            foreach ($decoded as $listitems) {
                add_post_meta($post->ID, SNILLRIK_CALENDAR_TIMEDATE_NAME, $listitems->datum . " " . $listitems->tid);
            }

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

            foreach ($menuscode_meta as $key => $value) {
                if ($post->post_type == 'revision') {
                    return;
                }

                $value = implode(',', (array) $value);
                if (get_post_meta($post->ID, $key, false)) {
                    update_post_meta($post->ID, $key, $value);
                } else {
                    add_post_meta($post->ID, $key, $value);
                }
                if (!$value) {
                    delete_post_meta($post->ID, $key);
                }
            }
        }
    }

    /**
     * Get calendar items
     * $args category, numposts and skip
     */
    public static function get_calendaritems($args)
    {
        global $wpdb;

        extract($args);
        $skip = isset($skip) ? $skip : 0;

        if (!is_numeric($category) && $category != "") {
            $idObj = get_category_by_slug($category);
            $catnum = $idObj->term_id;
        } else if (is_numeric($category)) {
            $catnum = $category;
        } else {
            $catnum = false;
        }

        $timeperiod_sql = "AND $wpdb->postmeta.meta_value > '" . date("Y-m-d h:i:s") . "'";
        if (isset($timeperiod)) {
            switch ($timeperiod) {
                case "weekend":
                    $next_friday = date("Y-m-d", strtotime('friday this week'));
                    $next_sunday = date("Y-m-d 23:59:59", strtotime('sunday this week'));
                    $timeperiod_sql = "AND $wpdb->postmeta.meta_value BETWEEN '$next_friday' and '$next_sunday'";
                    break;
                case "thisweek":
                    $fromday = date("Y-m-d", strtotime('monday this week'));
                    $today = date("Y-m-d 23:59:59", strtotime('sunday this week'));
                    $timeperiod_sql = "AND $wpdb->postmeta.meta_value BETWEEN '$fromday' and '$today'";
                    break;
            }
        }

        $return_str = "";

        $querystr = "
            SELECT $wpdb->posts.*, $wpdb->postmeta.meta_value AS datumet
            FROM $wpdb->posts, $wpdb->postmeta, $wpdb->term_relationships
            WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
            AND $wpdb->postmeta.meta_key = '" . SNILLRIK_CALENDAR_TIMEDATE_NAME . "'
            $timeperiod_sql
            AND $wpdb->posts.post_status = 'publish'
            AND $wpdb->posts.post_type = '" . SNILLRIK_CALENDAR_POST_TYPE_NAME . "'";

        if ($catnum) {
            $querystr .= "AND $wpdb->term_relationships.object_id = $wpdb->posts.ID AND $wpdb->term_relationships.term_taxonomy_id = $catnum";
        }

        $querystr .= " GROUP BY $wpdb->posts.ID, datumet ";
        $querystr .= " ORDER BY $wpdb->postmeta.meta_value ASC";
        if ($numposts > 0) {
            $querystr .= " LIMIT $skip, $numposts";
        }

        //echo $querystr;
        $items = $wpdb->get_results($querystr, OBJECT);
        return $items;
    }

    /**
     * Get the first image of the attachments
     */
    public static function first_image($parid, $path = false)
    {
        $attachment = get_children(
            array(
                'post_parent' => $parid,
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'order' => 'DESC',
                'numberposts' => 1,
            )
        );
        if (!is_array($attachment) || empty($attachment)) {
            return false;
        }
        $attachment = current($attachment);

        return $path ? get_attached_file($attachment->ID) : wp_get_attachment_url($attachment->ID, 'thumbnail');
    }
}
