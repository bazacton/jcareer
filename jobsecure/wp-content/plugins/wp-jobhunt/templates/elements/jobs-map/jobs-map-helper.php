<?php

/**
 * File Type: Jons Map Helper Function
 */
if ( ! class_exists('Jobs_Map_Helper_Function') ) {

    class Jobs_Map_Helper_Function {

        public function __construct() {
            add_action('wp_ajax_jobhunt_map_get_all_locations', array( $this, 'jobhunt_map_get_all_locations_callback' ));
            add_action('wp_ajax_jobhunt_map_get_selected_locations', array( $this, 'jobhunt_map_get_selected_locations_callback' ));
            add_action('wp_ajax_nopriv_jobhunt_map_get_all_locations', array( $this, 'jobhunt_map_get_all_locations_callback' ));
            add_action('wp_ajax_nopriv_jobhunt_map_get_selected_locations', array( $this, 'jobhunt_map_get_selected_locations_callback' ));
        }

        public function jobhunt_map_get_selected_locations_callback() {
            global $cs_plugin_options;
            $location = isset($_POST['search_location']) && ! empty($_POST['search_location']) ? $_POST['search_location'] : '';
            $search_radius = isset($_POST['radius']) && ! empty($_POST['radius']) ? $_POST['radius'] : '';
            $location_condition_arr = array();
            if ( $location != '' ) {
                $cs_radius_switch = isset($cs_plugin_options['cs_radius_switch']) ? $cs_plugin_options['cs_radius_switch'] : '';
                $cs_google_api_key = isset($cs_plugin_options['cs_google_api_key']) ? $cs_plugin_options['cs_google_api_key'] : '';
                if ( isset($search_radius) && $search_radius > 0 && $cs_radius_switch == 'on' ) {
                    $cs_radius = $search_radius;
                    $cs_radius_measure = isset($cs_plugin_options['cs_radius_measure']) ? $cs_plugin_options['cs_radius_measure'] : '';
                    $distance_km_miles = $cs_radius_measure;
                    $qrystr .= '&radius=' . $cs_radius;  // added again this var in query string for linking again
                    $cs_radius = preg_replace("/[^0-9,.]/", "", $cs_radius);
                    if ( $distance_km_miles == 'km' ) {
                        if ( isset($search_radius) ) {
                            $cs_radius = $cs_radius * 0.621371; // for km
                        }
                    }
                    $Latitude = '';
                    $Longitude = '';
                    $prepAddr = '';
                    $minLat = '';
                    $maxLat = '';
                    $minLong = '';
                    $maxLong = '';
                    if ( isset($location) && ! empty($location) ) {
                        $address = sanitize_text_field($location);
                        $prepAddr = str_replace(' ', '+', $address);
                        $geocode = file_get_contents('https://google.com/maps/api/geocode/json?key=' . $cs_google_api_key . '&address=' . $prepAddr . '&sensor=false');
                        $output = json_decode($geocode);
                        $Latitude = isset($output->results[0]->geometry->location->lat) ? $output->results[0]->geometry->location->lat : '';
                        $Longitude = isset($output->results[0]->geometry->location->lng) ? $output->results[0]->geometry->location->lng : '';
                        if ( isset($Latitude) && $Latitude <> '' && isset($Longitude) && $Longitude <> '' ) {
                            $zcdRadius = new RadiusCheck($Latitude, $Longitude, $cs_radius);
                            $minLat = $zcdRadius->MinLatitude();
                            $maxLat = $zcdRadius->MaxLatitude();
                            $minLong = $zcdRadius->MinLongitude();
                            $maxLong = $zcdRadius->MaxLongitude();
                        }
                    }
                    $cs_compare_type = 'CHAR';
                    if ( $cs_radius > 0 ) {
                        $cs_compare_type = 'DECIMAL(10,6)';
                    }
                    if ( $minLat != '' && $maxLat != '' && $minLong != '' && $maxLong != '' ) {
                        $radius_array = array(
                            'relation' => 'AND',
                            array(
                                'key' => 'cs_post_loc_latitude',
                                'value' => array( $minLat, $maxLat ),
                                'compare' => 'BETWEEN',
                                'type' => $cs_compare_type
                            ),
                            array(
                                'key' => 'cs_post_loc_longitude',
                                'value' => array( $minLong, $maxLong ),
                                'compare' => 'BETWEEN',
                                'type' => $cs_compare_type
                            ),
                        );
                    }
                }
                $qrystr .= '&location=' . $location;
                $cs_location_type = isset($cs_plugin_options['cs_search_by_location']) ? $cs_plugin_options['cs_search_by_location'] : '';
                if ( $cs_location_type == 'countries_and_cities' || $cs_location_type == 'countries_only' ) {
                    if ( isset($radius_array) && is_array($radius_array) ) {
                        $location_condition_arr[] = array(
                            'relation' => 'AND',
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'cs_post_loc_city',
                                    'value' => $location,
                                    'compare' => 'LIKE',
                                ),
                                array(
                                    'key' => 'cs_post_loc_country',
                                    'value' => $location,
                                    'compare' => 'LIKE',
                                ),
                                array(
                                    'key' => 'cs_post_loc_address',
                                    'value' => $location,
                                    'compare' => 'LIKE',
                                ),
                                $radius_array,
                            )
                        );
                        // for count query
                        $cus_fields_count_arr['location'][] = array(
                            'relation' => 'AND',
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'cs_post_loc_city',
                                    'value' => $location,
                                    'compare' => 'LIKE',
                                ),
                                array(
                                    'key' => 'cs_post_loc_country',
                                    'value' => $location,
                                    'compare' => 'LIKE',
                                ),
                                array(
                                    'key' => 'cs_post_loc_address',
                                    'value' => $location,
                                    'compare' => 'LIKE',
                                ),
                                $radius_array,
                            )
                        );
                    } else {
                        $location_condition_arr[] = array(
                            'relation' => 'OR',
                            array(
                                'key' => 'cs_post_loc_city',
                                'value' => $location,
                                'compare' => 'LIKE',
                            ),
                            array(
                                'key' => 'cs_post_loc_country',
                                'value' => $location,
                                'compare' => 'LIKE',
                            ),
                            array(
                                'key' => 'cs_post_loc_address',
                                'value' => $location,
                                'compare' => 'LIKE',
                            ),
                        );
                        $cus_fields_count_arr['location'][] = array(
                            'key' => 'cs_post_loc_city',
                            'value' => $location,
                            'compare' => 'LIKE',
                        );
                        $cus_fields_count_arr['location'][] = array(
                            'key' => 'cs_post_loc_country',
                            'value' => $location,
                            'compare' => 'LIKE',
                        );
                        $cus_fields_count_arr['location'][] = array(
                            'key' => 'cs_post_loc_address',
                            'value' => $location,
                            'compare' => 'LIKE',
                        );
                    }
                } elseif ( $cs_location_type == 'cities_only' || $cs_location_type == 'single_city' ) {
                    if ( isset($radius_array) && is_array($radius_array) ) {
                        $location_condition_arr[] = array(
                            'relation' => 'AND',
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'cs_post_loc_city',
                                    'value' => $location,
                                    'compare' => 'LIKE',
                                ),
                                array(
                                    'key' => 'cs_post_loc_address',
                                    'value' => $location,
                                    'compare' => 'LIKE',
                                ),
                                $radius_array,
                            )
                        );
                        $cus_fields_count_arr['location'][] = array(
                            'relation' => 'AND',
                            array(
                                'relation' => 'OR',
                                array(
                                    'key' => 'cs_post_loc_city',
                                    'value' => $location,
                                    'compare' => 'LIKE',
                                ),
                                array(
                                    'key' => 'cs_post_loc_address',
                                    'value' => $location,
                                    'compare' => 'LIKE',
                                ),
                                $radius_array,
                            )
                        );
                    } else {
                        $location_condition_arr[] = array(
                            'relation' => 'OR',
                            array(
                                'key' => 'cs_post_loc_city',
                                'value' => $location,
                                'compare' => 'LIKE',
                            ),
                            array(
                                'key' => 'cs_post_loc_address',
                                'value' => $location,
                                'compare' => 'LIKE',
                            ),
                        );
                        $cus_fields_count_arr['location'][] = array(
                            'key' => 'cs_post_loc_city',
                            'value' => $location,
                            'compare' => 'LIKE',
                        );
                        $cus_fields_count_arr['location'][] = array(
                            'key' => 'cs_post_loc_address',
                            'value' => $location,
                            'compare' => 'LIKE',
                        );
                    }
                }
            }
            $current_timestamp = current_time('timestamp');
            $args_jobs_map = array(
                'posts_per_page' => -1,
                'post_type' => 'Jobs',
                'post_status' => 'Publish',
                'fields' => 'ids',
                'ignore_sticky_posts' => 1,
                'meta_query' => array(
                    array(
                        'key' => 'cs_job_posted',
                        'value' => $current_timestamp,
                        'compare' => '<=',
                    ),
                    array(
                        'key' => 'cs_job_expired',
                        'value' => $current_timestamp,
                        'compare' => '>=',
                    ),
                    array(
                        'key' => 'cs_job_status',
                        'value' => 'active',
                        'compare' => '=',
                    ),
                    $location_condition_arr,
                ),
            );
            $json_array = array();
            $jobs_map_query = new WP_Query($args_jobs_map);
            if ( $jobs_map_query->have_posts() ) {
                while ( $jobs_map_query->have_posts() ) : $jobs_map_query->the_post();
                    global $post;
                    $lat = get_post_meta($post, 'cs_post_loc_latitude', true);
                    $long = get_post_meta($post, 'cs_post_loc_longitude', true);
                    $cs_jobs_address = get_user_address_string_for_list($post);
                    $cs_job_employer = get_post_meta($post, "cs_job_username", true); //
                    $cs_job_employer = cs_get_user_id_by_login($cs_job_employer);
                    $employer_img = get_the_author_meta('user_img', $cs_job_employer);
                    if ( $employer_img != '' ) {
                        $cs_jobs_thumb_url = cs_get_img_url($employer_img, 'cs_media_5');
                    }
                    if ( ! cs_image_exist($cs_jobs_thumb_url) || $cs_jobs_thumb_url == "" ) {
                        $cs_jobs_thumb_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                    }
                    $all_specialisms = get_the_terms($post, 'specialisms');
                    $specialisms_values = '';
                    $specialisms_class = '';
                    $specialism_flag = 1;
                    if ( $all_specialisms != '' ) {
                        foreach ( $all_specialisms as $specialismsitem ) {
                            $specialisms_values .= $specialismsitem->name;
                            $specialisms_class .= $specialismsitem->slug;
                            if ( $specialism_flag != count($all_specialisms) ) {
                                $specialisms_values .= ", ";
                                $specialisms_class .= " ";
                            }
                            $specialism_flag ++;
                        }
                    }
                    if ( (isset($lat) && ! empty($lat)) && (isset($long) && ! empty($long)) ) {
                        $json_array[$post] = array(
                            'title' => get_the_title($post),
                            'job_link' => get_the_permalink($post),
                            'lat' => $lat,
                            'long' => $long,
                            'img' => $cs_jobs_thumb_url,
                            'address' => $cs_jobs_address,
                            'specialisms' => $specialisms_values,
                        );
                    } else {
                        //$json_array['type'] = 'error';
                        //$json_array['msg'] = esc_html__('Something wrong.Please check your jobs address(latitude,longitude)', 'jobhunt');
                    }
                endwhile;
            } else {
                $json_array['type'] = 'error';
                $json_array['msg'] = esc_html__('Sorry! there are no jobs matching  your criteria', 'jobhunt');
            }
            echo json_encode($json_array);
            wp_die();
        }

        public function jobhunt_map_get_all_locations_callback() {
            $current_timestamp = current_time('timestamp');
            $args_jobs_map = array(
                'posts_per_page' => -1,
                'post_type' => 'Jobs',
                'post_status' => 'Publish',
                'fields' => 'ids',
                'ignore_sticky_posts' => 1,
                'meta_query' => array(
                    array(
                        'key' => 'cs_job_posted',
                        'value' => $current_timestamp,
                        'compare' => '<=',
                    ),
                    array(
                        'key' => 'cs_job_expired',
                        'value' => $current_timestamp,
                        'compare' => '>=',
                    ),
                    array(
                        'key' => 'cs_job_status',
                        'value' => 'active',
                        'compare' => '=',
                    ),
                ),
            );
            $json_array = array();
            $jobs_map_query = new WP_Query($args_jobs_map);
            if ( $jobs_map_query->have_posts() ) {
                while ( $jobs_map_query->have_posts() ) : $jobs_map_query->the_post();
                    global $post;
                    $lat = get_post_meta($post, 'cs_post_loc_latitude', true);
                    $long = get_post_meta($post, 'cs_post_loc_longitude', true);
                    $cs_jobs_address = get_user_address_string_for_list($post);
                    $cs_job_employer = get_post_meta($post, "cs_job_username", true); //
                    $cs_job_employer = cs_get_user_id_by_login($cs_job_employer);
                    $employer_img = get_the_author_meta('user_img', $cs_job_employer);
                    if ( $employer_img != '' ) {
                        $cs_jobs_thumb_url = cs_get_img_url($employer_img, 'cs_media_6');
                    }
                    if ( ! cs_image_exist($cs_jobs_thumb_url) || $cs_jobs_thumb_url == "" ) {
                        $cs_jobs_thumb_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                    }
                    $all_specialisms = get_the_terms($post, 'specialisms');
                    $specialisms_values = '';
                    $specialisms_class = '';
                    $specialism_flag = 1;
                    if ( $all_specialisms != '' ) {
                        foreach ( $all_specialisms as $specialismsitem ) {
                            $specialisms_values .= $specialismsitem->name;
                            $specialisms_class .= $specialismsitem->slug;
                            if ( $specialism_flag != count($all_specialisms) ) {
                                $specialisms_values .= ", ";
                                $specialisms_class .= " ";
                            }
                            $specialism_flag ++;
                        }
                    }
                    if ( (isset($lat) && ! empty($lat)) && (isset($long) && ! empty($long)) ) {
                        $json_array[$post] = array(
                            'title' => get_the_title($post),
                            'job_link' => get_the_permalink($post),
                            'lat' => $lat,
                            'long' => $long,
                            'img' => $cs_jobs_thumb_url,
                            'address' => $cs_jobs_address,
                            'specialisms' => $specialisms_values,
                        );
                    } else {
                        //$json_array['type'] = 'error';
                        //$json_array['msg'] = esc_html__('Something wrong.Please check your jobs address(latitude,longitude)', 'jobhunt');
                    }
                endwhile;
            } else {
                $json_array['type'] = 'error';
                $json_array['msg'] = esc_html__('Sorry! there are no jobs matching  your criteria', 'jobhunt');
            }

            echo json_encode($json_array);

            wp_die();
        }

    }

    new Jobs_Map_Helper_Function();
}