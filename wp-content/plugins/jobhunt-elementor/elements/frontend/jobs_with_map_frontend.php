<?php

class Jobhunt_JobsWithMap_Frontend
{

    public function render($settings)
    {
        global $post , $current_user , $cs_form_fields2 , $cs_plugin_options;
        $cs_job_map_type = isset($cs_plugin_options['cs_job_map_type']) ? $cs_plugin_options['cs_job_map_type'] : 'google';
        $defaults = array(
            'jobs_map_element_title' => '' ,
            'jobs_map_element_subtitle' => '' ,
            'jobs_maps_latitude' => '' ,
            'jobs_map_longitude' => '' ,
            'jobs_map_zoom_level' => '' ,
            'jobs_map_container_height' => '' ,
            'jobs_map_marker_icon' => '' ,
            'jobs_map_cluster_icon' => '' ,
        );
        extract(shortcode_atts($defaults , $settings));
        $jobs_map_marker_icon   = isset( $jobs_map_marker_icon['url'] )? $jobs_map_marker_icon['url'] : '';
        $jobs_map_cluster_icon   = isset( $jobs_map_cluster_icon['url'] )? $jobs_map_cluster_icon['url'] : '';
        if (isset($cs_plugin_options['cs_search_result_page'])) {
            $search_result_page_id = $cs_plugin_options['cs_search_result_page'];
        }
        ob_start();
        $map_height = 400;
        if (isset($jobs_map_container_height) && !empty($jobs_map_container_height)) {
            $map_height = $jobs_map_container_height;
        }
        $element_title_html = '';
        if ((isset($jobs_map_element_title) && !empty($jobs_map_element_title)) || (isset($jobs_map_element_subtitle) && !empty($jobs_map_element_subtitle))) {
            $element_title_html .= '<div class="cs-element-title">';
            if (isset($jobs_map_element_title) && !empty($jobs_map_element_title)) {
                $element_title_html .= '<h2>' . $jobs_map_element_title . '</h2>';
            }
            if (isset($jobs_map_element_subtitle) && !empty($jobs_map_element_subtitle)) {
                $element_title_html .= '<p>' . $jobs_map_element_subtitle . '</p>';
            }
            $element_title_html .= '</div>';
        }
        ?>
        <div class="map-search-holder">
            <?php echo force_balance_tags($element_title_html); ?>
            <div id="jobcareer-map-holder"
                 style="position: inherit;overflow: hidden;height: <?php echo($map_height); ?>px;"></div>
            <div class="main-search has-bgcolor search-on-map">
                <div class="container">
                    <div class="row">
                        <form id="frm_jobs_filtration"
                              action="<?php echo esc_url(get_permalink($search_result_page_id)); ?>" method="get"
                              class="search-area">
                            <div class="col-lg-4 col-md-4 col-sm-6">
                                <div class="search-input">
                                    <i class="icon-search7"></i>
                                    <?php
                                    $cs_opt_array = array(
                                        'std' => '' ,
                                        'id' => '' ,
                                        'cust_id' => '' ,
                                        'cust_name' => 'job_title' ,
                                        'classes' => '' ,
                                        'extra_atr' => 'placeholder="' . esc_html__("Search Keywords" , "jobhunt") . '"' ,
                                    );
                                    $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                    ?>
                                </div>
                            </div>
                            <?php
                            $jobs_locations_enabled = apply_filters('jobhunt_jobs_locations_enabled' , 'yes');
                            if ($jobs_locations_enabled == 'yes') {
                                ?>
                                <div class="col-lg-3 col-md-3 col-sm-6">
                                    <div class="select-location">
                                        <?php
                                        $cs_radius = '';
                                        $cs_locatin_cust = cs_location_convert();
                                        $cs_geo_location = isset($cs_plugin_options['cs_geo_location']) ? $cs_plugin_options['cs_geo_location'] : '';
                                        $cookie_geo_loc = isset($_COOKIE['cs_geo_loc']) ? $_COOKIE['cs_geo_loc'] : '';
                                        $cookie_geo_switch = isset($_COOKIE['cs_geo_switch']) ? $_COOKIE['cs_geo_switch'] : '';
                                        if ($cs_geo_location == 'on' && $cookie_geo_switch == 'on' && $cookie_geo_loc != '') {
                                            $cs_locatin_cust = $cookie_geo_loc;
                                        }
                                        if (isset($_GET['location'])) {
                                            $cs_locatin_cust = cs_location_convert();
                                        }
                                        $cs_loc_name = '';
                                        $cs_select_display = 'block';
                                        $cs_input_display = 'none';
                                        $cs_undo_display = 'none';
                                        if ($cs_locatin_cust != '') {
                                            $cs_loc_name = ' location';
                                            $cs_select_display = 'none';
                                            $cs_input_display = 'block';
                                            $cs_undo_display = 'block';
                                        }
                                        $cs_radius_switch = isset($cs_plugin_options['cs_radius_switch']) ? $cs_plugin_options['cs_radius_switch'] : '';
                                        $min_value = 0;
                                        $max_value = '';
                                        if ($cs_radius_switch == 'on') {
                                            $cs_default_radius = isset($cs_plugin_options['cs_default_radius']) ? $cs_plugin_options['cs_default_radius'] : '';
                                            $cs_radius_measure = isset($cs_plugin_options['cs_radius_measure']) ? $cs_plugin_options['cs_radius_measure'] : '';
                                            $cs_radius_measure = $cs_radius_measure == 'km' ? esc_html__('KM' , 'jobhunt') : esc_html__('Miles' , 'jobhunt');
                                            $min_value = isset($cs_plugin_options['cs_radius_min']) ? $cs_plugin_options['cs_radius_min'] : '';
                                            $max_value = isset($cs_plugin_options['cs_radius_max']) ? $cs_plugin_options['cs_radius_max'] : '';
                                            $radius_step = isset($cs_plugin_options['cs_radius_step']) ? $cs_plugin_options['cs_radius_step'] : '';
                                            $cs_radius = preg_replace("/[^0-9,.]/" , "" , $cs_radius);
                                            $cs_radius = $cs_default_radius;
                                            $cs_google_api_key = isset($cs_plugin_options['cs_google_api_key']) ? $cs_plugin_options['cs_google_api_key'] : '';
                                        }
                                        ?>

                                        <div id="cs-top-select-holder" class="select-location"
                                             data-locationadminurl="<?php echo esc_url(admin_url("admin-ajax.php")) ?>">
                                            <?php
                                            if (isset($atts['job_search_style']) and $atts['job_search_style'] == "default_fancy") {
                                                echo '<i class="icon-location6"></i>';
                                            }
                                            $hint_text = '';

                                            if (isset($atts['job_search_hint_switch']) and $atts['job_search_hint_switch'] == 'yes') {
                                                $hint_color = '';
                                                if ($job_search_layout_heading_color != '') {
                                                    $hint_color = ' style="color:' . $job_search_layout_heading_color . ' !important;"';
                                                }
                                                $hint_text = '<span ' . $hint_color . '>' . esc_html__('Please select your desired location' , 'jobhunt') . '</span>';
                                            }

                                            if ($cs_plugin_options['cs_google_autocomplete_enable'] == 'on') {
                                                cs_get_custom_locationswith_google_auto('<div id="cs-top-select-holder" class="search-country" style="display:' . cs_allow_special_char($cs_select_display) . '"><div class="select-holder">' , '</div>' . $hint_text . ' </div>' , false , true);
                                            } else {
                                                cs_get_custom_locations('<div id="cs-top-select-holder" class="search-country" style="display:' . cs_allow_special_char($cs_select_display) . '">' , $hint_text . ' </div>');
                                            }
                                            if ($cs_radius_switch == 'on') {
                                                $list_rand = rand(0 , 499999999);
                                                ?>
                                                <?php if ($cs_job_map_type == 'google'){ ?>
                                                <a id="radius_jobs_maps<?php echo absint($list_rand); ?>"
                                                   href="javascript:void(0);" class="location-btn pop"><i
                                                            class="icon-target3"></i></a>
                                            <?php } ?>
                                                <div id="popup_jobs_maps<?php echo absint($list_rand); ?>"
                                                     style="display:none;" class="select-popup">
                                                    <a class="cs-location-close-popup"
                                                       id="cs_close<?php echo absint($list_rand); ?>"><i
                                                                class="cs-color icon-times"></i></a>
                                                    <p><?php esc_html_e("Show With in" , "jobhunt"); ?></p>
                                                    <input id="ex6<?php echo absint($list_rand); ?>" name="radius"
                                                           type="text"
                                                           data-slider-min="<?php echo absint($min_value); ?>"
                                                           data-slider-max="<?php echo absint($max_value); ?>"
                                                           data-slider-step="<?php echo absint($radius_step); ?>"
                                                           data-slider-value="<?php echo absint($cs_radius); ?>"/>
                                                    <span id="ex6CurrentSliderValLabel_job"><span
                                                                id="ex6SliderVal<?php echo absint($list_rand); ?>"><?php echo absint($cs_radius); ?></span><?php echo esc_html($cs_radius_measure); ?></span>
                                                    <?php
                                                    if ($cs_geo_location == 'on') {
                                                        ?>
                                                        <p class="my-location"><?php esc_html_e("of" , "jobhunt"); ?> <i
                                                                    class="cs-color icon-location-arrow"></i><a
                                                                    class="cs-color"
                                                                    onclick="cs_get_location(this '<?php echo $cs_google_api_key; ?>')"><?php esc_html_e("My location" , "jobhunt"); ?></a>
                                                        </p>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <script type="text/javascript">
                                                    jQuery(document).ready(function () {
                                                        jQuery("#ex6<?php echo absint($list_rand); ?>").slider();
                                                        jQuery("#ex6<?php echo absint($list_rand); ?>").on("slide", function (slideEvt) {
                                                            jQuery("#ex6SliderVal<?php echo absint($list_rand); ?>").text(slideEvt.value);
                                                        });
                                                        jQuery('#radius_jobs_maps<?php echo absint($list_rand); ?>').click(function (event) {
                                                            event.preventDefault();
                                                            jQuery("#popup_jobs_maps<?php echo absint($list_rand); ?>").css('display', 'block') //to show
                                                            return false;
                                                        });
                                                        jQuery('#cs_close<?php echo absint($list_rand); ?>').click(function () {
                                                            jQuery("#popup_jobs_maps<?php echo absint($list_rand); ?>").css('display', 'none') //to show
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                                <?php
                                            }
                                            ?>
                                        </div>

                                        <?php
                                        $cs_form_fields2->cs_form_text_render(
                                            array(
                                                'id' => '' ,
                                                'classes' => 'cs-geo-location form-control txt-field geo-search-location' ,
                                                'cust_name' => $cs_loc_name ,
                                                'extra_atr' => ' onchange="this.form.submit()" style="display:' . cs_allow_special_char($cs_input_display) . ';" ' . $cs_loc_name ,
                                                'std' => $cs_locatin_cust ,
                                            )
                                        );
                                        ?>
                                        <div class="cs-undo-select"
                                             style="display:<?php echo cs_allow_special_char($cs_undo_display) ?>;">
                                            <i class="icon-times"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-lg-2 col-md-2 col-sm-12">
                                <div class="search-btn">
                                    <input type="submit" class=" cs-bgcolor" name="cs_" value="Find Job"></div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <?php
        wp_jobhunt::cs_googlemapcluster_scripts();
        ?>
        <script type="text/javascript">

            jQuery(document).ready(function () {

                jQuery('#jobcareer-map-holder').html('<div class="cs-spinner"><i class="icon-spinner8 icon-spin"></i></div>');
                jQuery.ajax({
                    type: 'POST',
                    url: '<?php echo esc_url(admin_url("admin-ajax.php")) ?>',
                    data: 'action=jobhunt_map_get_all_locations',
                    dataType: "JSON",
                    success: function (res) {
                        console.log(res);
                        if (typeof res.type !== 'undefined' && res.type == 'error') {
                            show_error_alert_msg(res.msg);
                            var laitude = '<?php echo $jobs_maps_latitude; ?>';
                            var longitude = '<?php echo $jobs_map_longitude; ?>';
                            var map_zoom = '<?php echo $jobs_map_zoom_level; ?>';


                            var cs_map_type = '<?php echo $cs_job_map_type; ?>';
                            if (typeof cs_map_type == 'undefined') {
                                var cs_map_type = 'google'
                            }
                            if (cs_map_type == 'llmaps') {
                                var mapConfigs = {
                                    center: ['<?php echo $jobs_maps_latitude; ?>', '<?php echo $jobs_map_longitude; ?>'],
                                    zoom: '<?php echo $jobs_map_zoom_level; ?>',
                                    map_type: 'default',
                                }
                                var llmapsObject = new LLMAPS(mapConfigs);
                                llmapsObject.llmapsInitiate('jobcareer-map-holder');
                                return;
                            }


                            var map = new google.maps.Map(document.getElementById('jobcareer-map-holder'), {
                                zoom: parseInt(map_zoom),
                                center: new google.maps.LatLng(laitude, longitude),
                                mapTypeId: google.maps.MapTypeId.ROADMAP
                            });
                        } else {

                            var cs_map_type = '<?php echo $cs_job_map_type; ?>';
                            if (typeof cs_map_type == 'undefined') {
                                var cs_map_type = 'google'
                            }
                            if (cs_map_type == 'llmaps') {
                                var mapConfigs = {
                                    center: ['<?php echo $jobs_maps_latitude; ?>', '<?php echo $jobs_map_longitude; ?>'],
                                    zoom: '<?php echo $jobs_map_zoom_level; ?>',
                                    map_type: 'default',
                                    marker_icon: '<?php echo $jobs_map_marker_icon; ?>',
                                }
                                var llmapsObject = new LLMAPS(mapConfigs);


                                var markersArray = [];
                                jQuery.each(res, function (key, markerData) {
                                    var popup_template = '<div class="map-tooltip map-tooltip1">\
                                                                <div class="cs-media">\
                                                                <figure><img alt="" src="' + markerData.img + '"></figure>\
                                                                </div>\
                                                                <div class="cs-text">\
                                                                <div class="cs-post-title">\
                                                                <h6><a href="' + markerData.job_link + '">' + markerData.title + '</a></h6>\
                                                                ' + markerData.address + '</div>\
                                                                ' + markerData.specialisms + '\
                                                                </div>\
                                                                </div>';


                                    var markerData = {
                                        latitude: markerData.lat,
                                        longitude: markerData.long,
                                        popup_template: popup_template,
                                    }

                                    markersArray.push(markerData);
                                });
                                llmapsObject.registerMarkers(markersArray);

                                llmapsObject.llmapsInitiate('jobcareer-map-holder');
                                return;
                            }


                            jQuery('#jobcareer-map-holder').html('');
                            jobhunt_get_locations(res);
                        }
                    }
                });
            });

            function jobhunt_get_locations(response) {
                var locations = jQuery.map(response, function (el) {
                    return el;
                })
                var laitude = '<?php echo $jobs_maps_latitude; ?>';
                var longitude = '<?php echo $jobs_map_longitude; ?>';
                var map_zoom = '<?php echo $jobs_map_zoom_level; ?>';
                if (map_zoom == '') {
                    map_zoom = 10;
                }
                if (laitude == '' || longitude == '') {
                    for (i = 0; i < locations.length; i++) {
                        var laitude = locations[i]['lat'];
                        var longitude = locations[i]['long'];
                        break;
                    }
                }
                var markerClusterer = null;
                var map = null;
                var map = new google.maps.Map(document.getElementById('jobcareer-map-holder'), {
                    zoom: parseInt(map_zoom),
                    center: new google.maps.LatLng(laitude, longitude),
                    position: new google.maps.LatLng(laitude, longitude),
                    streetViewControl: true,
                    draggable: true,
                    scrollwheel: false,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    scrollwheel: false,
                });
                var infowindow = new InfoBox({
                    boxClass: 'jobs_map_info_wrapper',
                    disableAutoPan: true,
                    maxWidth: 60,
                    alignBottom: true,
                    pixelOffset: new google.maps.Size(-120, -50),
                    zIndex: null,
                    closeBoxMargin: "2px",
                    closeBoxURL: "close",
                    infoBoxClearance: new google.maps.Size(1, 1),
                    isHidden: false,
                    pane: "floatPane",
                    enableEventPropagation: false
                });
                var contentString;
                var markers = [];
                var marker, i;
                for (i = 0; i < locations.length; i++) {
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(locations[i]['lat'], locations[i]['long']),
                        animation: google.maps.Animation.DROP,
                        map: map,
                        icon: '<?php echo $jobs_map_marker_icon; ?>',
                    });
                    google.maps.event.addListener(marker, 'click', (function (marker, i) {
                        var contentString = '<div class="liting_map_info" >\
        				<div class="job-info-inner">\
        					       <div class="info-main-container">\
        						 <figure>\
        						  <a class="info-title" href="#">\
        						     <img alt="" class="img-map-info" src="' + locations[i]['img'] + '">\
        						  </a>\
        						</figure>\
        					     <div class="info-txt-holder">\
        					      <a class="info-title" href="' + locations[i]['job_link'] + '">' + locations[i]['title'] + '</a>\
        						<span class="job-location">\
        						   <span class="new-loc text-color">' + locations[i]['address'] + '</span>\
        						  <span class="new-loc text-color">' + locations[i]['specialisms'] + '</span>\
        					      </span>\
        					  </div>\
        				      </div>\
        				  </div>\
        			      </div>';
                        return function () {
                            map.panTo(marker.getPosition());
                            map.panBy(40, -70);
                            infowindow.setContent(contentString);
                            infowindow.open(map, marker);
                        }
                    })(marker, i));
                    markers.push(marker);


                }

                var mcOptions;
                var map_color = "<?php echo isset($cs_plugin_options['cs_map_cluster_color']) ? $cs_plugin_options['cs_map_cluster_color'] : '#000000'; ?>";
                var cluster_icon = "<?php echo isset($jobs_map_cluster_icon) ? $jobs_map_cluster_icon : wp_jobhunt::plugin_url() . 'assets/images/culster-icon.png'; ?>";
                var clusterStyles = [
                    {
                        textColor: map_color,
                        opt_textColor: map_color,
                        url: cluster_icon,
                        height: 80,
                        width: 80,
                        textSize: 12
                    }
                ];
                mcOptions = {
                    gridSize: 45,
                    ignoreHidden: true,
                    maxZoom: 12,
                    styles: clusterStyles
                };
                var mc = new MarkerClusterer(map, markers, mcOptions);
            }


            jQuery(document).on('click', '.cs_location_autocomplete > div', function () {
                var searched_keyword = jQuery('.search_keyword').val();
                var radius = jQuery("input[name=radius]").val();
                if (typeof radius === 'undefined') {
                    radius = '';
                }
                jQuery('#jobcareer-map-holder').html('<div class="cs-spinner"><i class="icon-spinner8 icon-spin"></i></div>');
                jQuery.ajax({
                    type: 'POST',
                    url: '<?php echo esc_url(admin_url("admin-ajax.php")) ?>',
                    data: 'radius=' + radius + '&search_location=' + searched_keyword + '&action=jobhunt_map_get_selected_locations',
                    dataType: "JSON",
                    success: function (res) {
                        if (typeof res.type !== 'undefined' && res.type == 'error') {
                            show_error_alert_msg(res.msg);
                        } else {
                            jQuery('#jobcareer-map-holder').html('');
                            jobhunt_get_locations(res);
                        }
                    }
                });
            });
        </script>
        <?php
        $cs_html = ob_get_clean();
        echo $cs_html;

    }

}

