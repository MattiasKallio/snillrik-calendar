<?php

class SNCL_shortcodes
{

    public function __construct()
    {
        add_shortcode('snillrik_calendar', array($this, 'snillrik_calendar_shortcode'));
        add_shortcode('snillrik_calendar_swipe', array($this, 'snillrik_calendar_swipe_shortcode'));
    }

/**
 * Ads, as a calendar from bizzes
 */
    public function snillrik_calendar_shortcode($atts)
    {
        $attributes = shortcode_atts(array(
            'category' => '',
            'numposts' => 4,
            'skip' => 0,
            'type' => 'normal',
        ), $atts);

        $calendaritem = SNCL_calendar::get_calendaritems($attributes);
        $return_str = "";
        $boxstr = "";
        $counter = 1;
        $type_num = 1;

        setlocale(LC_TIME, 'sv_SE.utf8');
        if(count($calendaritem)==0){
            return;
        }
        foreach ($calendaritem as $item) {
            //$calendaritem_metanum[$item->ID] = isset($calendaritem_metanum[$item->ID]) ? $calendaritem_metanum[$item->ID] + 1 : 0;
            $tumme = SNCL_calendar::first_image($item->ID);
            if ($tumme == "" || !$tumme) {
                //$tumme = get_the_post_thumbnail($item->ID);
                $tumme = wp_get_attachment_image_src(get_post_thumbnail_id($item->ID), "large");
                $tumme = esc_url($tumme[0]);
            }
            $content = preg_replace("/\[caption.*\[\/caption\]/", '', $item->post_content);
            $meta_datefrom = $item->datumet;
            setlocale(LC_TIME, 'sv_SE.utf8');
            $manad = strtoupper(strftime('%b', strtotime($meta_datefrom)));
            $veckodag = strftime('%A', strtotime($meta_datefrom));
            $dag = date('d', strtotime($meta_datefrom));

            $thurl = get_permalink($item->ID);

            $boxstr_big = "<div class='snillrik_calenderbox'>
        <div class='snillrik_calenderbox_inner snillrik_calenderbox_boxtype_" . $attributes["type"] . "' style='background-image:url($tumme)'>
            <div class='snillrik_calenderbox_datebox'>
                <span class='snillrik_calenderbox_datenum'>$dag</span>
                <span class='snillrik_calenderbox_datemon'>$manad </span>
                <span class='snillrik_calenderbox_dateweekday'>$veckodag</span>
            </div>
            <div class='snillrik_big_calenderbox_textblock'>
                <div><h4><a href='" . $thurl . "' class='calendar_url'>" . substr($item->post_title, 0, 30) . "</a></h4><p>" . wp_trim_words($content, 10) . " <a href='" . $thurl . "'>Läs mer</a></p></div>
            </div>

        </div>
        </div>";

            // $return_str .= "here: ".print_r($skala_banan,true);
            switch ($attributes["type"]) {
                case 'normal':
                    $boxstr .= "<div class='snillrik_calenderbox snillrik_calenderbox_boxtype_" . $attributes["type"] . "'>
            <div class='snillrik_calenderbox_inner'>
            <div class='snillrik_calenderbox_datebox'>
                <span class='snillrik_calenderbox_datenum'>$dag</span>
                <span class='snillrik_calenderbox_datemon'>$manad </span>
                <span class='snillrik_calenderbox_dateweekday'>$veckodag</span>
            </div>
                <div class='snillrik_calenderbox_main'>
                    <div><h4><a href='" . $thurl . "' class='calendar_url'>" . $item->post_title . "</a></h4><p>" . wp_trim_words($content, 8) . " <a href='" . $thurl . "'>Läs mer</a></p></div>
                </div>
            </div>
            </div>";
                    break;
                case 'big1':
                    $boxstr .= $boxstr_big;
                    $type_num = 1;
                    break;
                case 'big2':
                    $boxstr .= $boxstr_big;
                    $type_num = 2;
                    break;
                case 'big3':
                    $boxstr .= $boxstr_big;
                    $type_num = 3;
                    break;
                case 'big4':
                    $boxstr .= $boxstr_big;
                    $type_num = 4;
                    break;
            }

            if ($counter == $type_num || count($calendaritem) == $counter) {
                $return_str .= "<div class='snillrik_calendarboxes'>$boxstr</div>";
                $boxstr = "";
                $counter = 1;
            } else {
                $counter++;
            }
        }

        return $return_str;

    }

/**
 * Ads, as a calendar from bizzes
 */
    public function snillrik_calendar_swipe_shortcode($atts)
    {
        $attributes = shortcode_atts(array(
            'category' => '',
            'numposts' => 4,
            'skip' => 0,
            'type' => 'normal',
            'slidesperview' => 1,
            'effect' => 'coverflow', //cube, flip, fade, coverflow
        ), $atts);

        $js_params = array(
            "slidesperview" => wp_is_mobile() ? 1 : $attributes["slidesperview"],
            "effect" => $attributes["effect"],
        );

        //print_r($atts);
        wp_enqueue_script('snillrik-calendar-main-script');
        wp_localize_script('snillrik-calendar-main-script', 'swipeparams', $js_params);

        $calendaritem = SNCL_calendar::get_calendaritems($attributes);
        //print_r($calendaritem);
        //$calendaritem_metanum[] = array();
        $counter = 1;
        $type_num = 1;

        $swiper_out = '';

        setlocale(LC_TIME, 'sv_SE.utf8');

        if(count($calendaritem)==0)
            return;
        foreach ($calendaritem as $item) {
            $tumme = SNCL_calendar::first_image($item->ID);
            if ($tumme == "" || !$tumme) {
                //$tumme = get_the_post_thumbnail($item->ID);
                $tumme = wp_get_attachment_image_src(get_post_thumbnail_id($item->ID), "large");
                $tumme = esc_url($tumme[0]);
            }
            $content = preg_replace("/\[caption.*\[\/caption\]/", '', $item->post_content);
            $meta_datefrom = $item->datumet;
            setlocale(LC_TIME, 'sv_SE.utf8');
            $manad = strtoupper(strftime('%b', strtotime($meta_datefrom)));
            $veckodag = strftime('%A', strtotime($meta_datefrom));
            $dag = date('d', strtotime($meta_datefrom));

            $thurl = get_permalink($item->ID);

            $swiper_out .= "
            <div class='swiper-slide'>
                <div class='snillrik_calenderbox'>
                    <div class='snillrik_calenderbox_inner " . $attributes["category"] . "' style='background-image:url($tumme)'>
                        <div class='snillrik_calenderbox_datebox'>
                            <span class='snillrik_calenderbox_datenum'>$dag</span>
                            <span class='snillrik_calenderbox_datemon'>$manad </span>
                            <span class='snillrik_calenderbox_dateweekday'>$veckodag</span>
                        </div>
                        <div class='snillrik_big_calenderbox_textblock'>
                            <h4><a href='" . $thurl . "' class='calendar_url'>" . substr($item->post_title, 0, 30) . "</a>
                            <p>" . wp_trim_words($content, 12) . " <a href='" . $thurl . "'>Läs mer</a></p>
                        </div>

                    </div>
                </div>
            </div>";
        }

        //return "<div class='swiper-container'>$swiper_out</div>";

        return '<div class="swiper-container">
        <div class="swiper-scrollbar"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
    <div class="swiper-wrapper">
    ' . $swiper_out . '
    </div>
    <div class="swiper-pagination"></div>
    </div>';
    }

}
