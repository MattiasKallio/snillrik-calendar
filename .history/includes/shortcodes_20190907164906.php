/**
 * Ads, as a calendar from bizzes
 */
function bizumbrella_kalender_shortcode($atts)
{
    $attributes = shortcode_atts(array(
        'category' => '',
        'numposts' => 4,
        'type' => 'normal'
    ), $atts);

    $eventer = get_eventer_for_calendar($attributes["category"],$attributes["numposts"]);

 //echo $querystr;
 //$eventer = $wpdb->get_results($querystr, OBJECT);
    $eventer_metanum[] = array();


setlocale(LC_TIME,'sv_SE.utf8');
    foreach ($eventer as $annon) {
        $eventer_metanum[$annon->ID] = isset($eventer_metanum[$annon->ID]) ? $eventer_metanum[$annon->ID]+1 : 0;
        $tumme = bizumbrella_get_first_image($annon->ID);
        if($tumme=="" || !$tumme){
            //$tumme = get_the_post_thumbnail($annon->ID);
            $tumme = wp_get_attachment_image_src( get_post_thumbnail_id( $annon->ID ),"large");
            $tumme = esc_url( $tumme[0] );
        }
        $content = preg_replace("/\[caption.*\[\/caption\]/", '', $annon->post_content);
        $meta_datefrom = $annon->datumet;
        setlocale(LC_TIME,'sv_SE.utf8');
        $manad = strtoupper(strftime('%b', strtotime($meta_datefrom)));
        $veckodag = strftime('%A', strtotime($meta_datefrom));
        $dag = date('d', strtotime($meta_datefrom));

        // $return_str .= "here: ".print_r($skala_banan,true);
        switch($attributes["type"]){
            case 'normal':
            $return_str .= "<div class='bizumbrella_calenderbox ".$attributes["category"]."'>
            <div class='row bizumbrella_calenderbox_inner'>
                <div class='col-3 col-sm-3 bizumbrella_calenderbox_datebox'>
                    <div class='bizumbrella_calenderbox_datenum'>$dag</div><div class='bizumbrella_calenderbox_datemon'>$manad<br /><span class='bizumbrella_calenderbox_dateweekday'>$veckodag</span></div>
                </div>
                <div class='col-9 col-sm-9 bizumbrella_calenderbox_main'>
                    <div class=''><h4><a href='" . $annon->guid . "'>" . $annon->post_title . "</a></h4><p>" . wp_trim_words($content, 8) . " <a href='" . $annon->guid . "'>Läs mer</a></p></div>
                </div>        
            </div>
            </div>";
            break;
            case 'big2':
            $return_str .= "<div class='col-md-12 col-lg-6'>
                <div class='bizumbrella_big_calenderbox ".$attributes["category"]."' style='background-image:url($tumme)'>            
                    <div class='bizumbrella_big_calenderbox_textblock'><h4><a href='" . $annon->guid . "'>" . substr($annon->post_title,0,30) . "</a></h4><p>" . wp_trim_words($content, 10) . " <a href='" . $annon->guid . "'>Läs mer</a></p></div>
                    <div class='bizumbrella_calenderbox_datebox'>
                        <div class='bizumbrella_calenderbox_datenum'>$dag $manad<span class='bizumbrella_calenderbox_dateweekday'>$veckodag</span></div>
                    </div>
                </div>
            </div>";
            
            break; 
            case 'big4':
            $return_str .= "<div class='col-md-6 col-lg-3'>
                <div class='bizumbrella_big_calenderbox ".$attributes["category"]."' style='background-image:url($tumme)'>            
                    <div class='bizumbrella_big_calenderbox_textblock'><h4><a href='" . $annon->guid . "'>" . substr($annon->post_title,0,30) . "</a></h4><p>" . wp_trim_words($content, 10) . " <a href='" . $annon->guid . "'>Läs mer</a></p></div>
                    <div class='bizumbrella_calenderbox_datebox'>
                        <div class='bizumbrella_calenderbox_datenum'>$dag $manad <span class='bizumbrella_calenderbox_dateweekday'>$veckodag</span></div>
                    </div>
                </div>
            </div>";
            
            break;                        
        }

    }

    return in_array($attributes["type"],array('big2','big4')) ? "<div class='row'>".$return_str."</div>":$return_str;

}
add_shortcode('bizumbrella_kalender', 'bizumbrella_kalender_shortcode');