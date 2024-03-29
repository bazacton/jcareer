<?php
class Jobhunt_Jobs_Search_Frontend{

    public function render($settings) {
        global $wpdb, $cs_plugin_options, $cs_form_fields2;
        $defaults = array(
            'column_size' => '1/1',
            'jobs_search_title' => '',
            'job_search_style' => '',
            'job_search_layout_bg' => '',
            'job_search_layout_heading_color' => '',
            'job_search_title_field_switch' => '',
            'job_search_specialisam_field_switch' => '',
            'job_search_location_field_switch' => '',
            'job_lable_switch' => '',
            'job_search_hint_switch' => '',
            'job_advance_search_switch' => '',
            'job_advance_search_url' => '',
        );
        $defaults = apply_filters('jobhunt_add_variable_job_array_list', $defaults);
        extract(shortcode_atts($defaults , $settings));
        $cs_google_api_key = isset($cs_plugin_options['cs_google_api_key']) ? $cs_plugin_options['cs_google_api_key'] : '';
        $search_result_page_id = '';
        $title_field_switch = 'no';
        if (isset($job_search_title_field_switch) && $job_search_title_field_switch == 'yes') {
            $title_field_switch = $job_search_title_field_switch;
        }
        $modern_class = '';
        if (isset($job_search_style) and $job_search_style == "modren") {
            $modern_class = 'cs-jobsearch-modern';
        } elseif (isset($job_search_style) and $job_search_style == "simple") {
            $modern_class = 'cs-jobsearch-simple';
        } elseif (isset($job_search_style) and $job_search_style == "classic") {
            $modern_class = 'cs-jobsearch-modern v1';
        } elseif (isset($job_search_style) and $job_search_style == "fancy") {
            $modern_class = 'v3';
        } elseif (isset($job_search_style) and $job_search_style == "default_fancy") {
            $modern_class = 'default-fancy';
        } elseif (isset($job_search_style) and $job_search_style == "modren_v2") {
            $modern_class = 'search-simple-v2';
        }
        $specialisam_field_switch = 'no';
        if (isset($job_search_specialisam_field_switch) && $job_search_specialisam_field_switch == 'yes') {
            $specialisam_field_switch = $job_search_specialisam_field_switch;
        }

        $location_field_switch = 'no';
        if (isset($job_search_location_field_switch) && $job_search_location_field_switch == 'yes') {
            $location_field_switch = $job_search_location_field_switch;
        }

        $title_field_lable_switch = 'no';
        if (isset($job_search_title_field_lable_switch) && $job_search_title_field_lable_switch == 'yes') {
            $title_field_lable_switch = $job_search_title_field_lable_switch;
        }

        //$job_search_layout_bg = 'none';
        if (isset($job_search_layout_bg) && $job_search_layout_bg <> "") {
            $job_search_layout_bg = $job_search_layout_bg;
        }
        $job_search_layout_heading_color = isset($job_search_layout_heading_color) ? $job_search_layout_heading_color : '';
        if (isset($cs_plugin_options['cs_search_result_page'])) {
            $search_result_page_id = $cs_plugin_options['cs_search_result_page'];
        }
        if (isset($job_lable_switch) && $job_lable_switch == 'yes') {
            $job_lable_switch = $job_lable_switch;
        }
        //$job_search_hint_switch = 'no';
        if (isset($job_search_hint_switch) && $job_search_hint_switch == 'yes') {
            $job_search_hint_switch = $job_search_hint_switch;
        }
        $job_advance_search_url = '';
        if (isset($job_advance_search_url) && $job_advance_search_url <> "") {
            $job_advance_search_url = $job_advance_search_url;
        }

        $job_search_professions_field_switch = apply_filters('jobhunt_check_job_professions_null_conditions', $settings);

        if (isset($job_advance_search_switch) && $job_advance_search_switch == 'yes') {
            $job_advance_search_switch = $job_advance_search_switch;
        }
        $jobs_search_title = isset($jobs_search_title) ? $jobs_search_title : '';

        $column_size = isset($column_size) ? $column_size : '';
        $column_class = jobcareer_custom_column_class($column_size);
        ob_start();
        //cs_range_slider_scripts();

    if (isset($column_class) && $column_class != "") {
        ?>
        <div class="<?php echo esc_html($column_class); ?>">
            <?php
            }
            ?>
            <div <?php
            /*if the view is aviation*/
            $has_duration_filter_class = '';
            if (isset($job_search_style) && $job_search_style == "aviation") {
                $modern_class .= ' aviation-search ';
                $has_duration_filter_class = 'has-duration-filter';
            }
            if (isset($job_search_layout_bg) && ($job_search_layout_bg != '' && $job_search_layout_bg != 'none')) {
                echo ' class="main-search has-bgcolor ' . $modern_class . '" style="background:' . esc_attr($job_search_layout_bg) . ' !important;"';
            } else {
                echo 'class="main-search ' . $modern_class . '"';
            }
            ?>>
                <div class="row">
                    <?php
                    if ($search_result_page_id != '') { // search result page
                        if (isset($jobs_search_title) && $jobs_search_title <> "") {
                            ?>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="cs-element-title">
                                    <h2 <?php
                                    if ($job_search_layout_heading_color != '') {
                                        echo 'style="color:' . esc_html($job_search_layout_heading_color) . ' !important;"';
                                    }
                                    ?>><?php echo esc_html($jobs_search_title); ?></h2>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="row">
                                <form id="frm_jobs_filtration" action="<?php echo esc_url(get_permalink($search_result_page_id)); ?>" method="get"  class="search-area <?php echo $has_duration_filter_class; ?>">
                                    <?php
                                    $title_col = 'col-lg-4 col-md-4 col-sm-6';
                                    $specialisam_col = 'col-lg-3 col-md-3 col-sm-6';
                                    $location_col = 'col-lg-3 col-md-3 col-sm-6';

                                    if (isset($job_search_style) and $job_search_style == "simple") {
                                    ?>
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="bg-holder">
                                            <?php
                                            if ($title_field_switch == 'yes' && $specialisam_field_switch == 'yes' && $location_field_switch == 'yes') {
                                                $title_col = 'col-lg-6 col-md-6 col-sm-12';
                                            } else if ($title_field_switch == 'yes' && $specialisam_field_switch != 'yes' && $location_field_switch == 'yes') {
                                                $title_col = 'col-lg-8 col-md-8 col-sm-12';
                                                $specialisam_col = 'col-lg-4 col-md-4 col-sm-12';
                                                $location_col = 'col-lg-4 col-md-4 col-sm-12';
                                            } else if ($title_field_switch == 'yes' && $specialisam_field_switch == 'yes' && $location_field_switch != 'yes') {
                                                $title_col = 'col-lg-8 col-md-8 col-sm-12';
                                                $specialisam_col = 'col-lg-4 col-md-4 col-sm-12';
                                                $location_col = 'col-lg-4 col-md-4 col-sm-12';
                                            } else if ($title_field_switch == 'yes' && $specialisam_field_switch != 'yes' && $location_field_switch != 'yes') {
                                                $title_col = 'col-lg-12 col-md-12 col-sm-12';
                                            } else if ($title_field_switch != 'yes' && $specialisam_field_switch == 'yes' && $location_field_switch == 'yes') {
                                                $specialisam_col = 'col-lg-6 col-md-6 col-sm-12';
                                                $location_col = 'col-lg-6 col-md-6 col-sm-12';
                                            } else if ($title_field_switch != 'yes' && $specialisam_field_switch != 'yes' && $location_field_switch == 'yes') {
                                                $location_col = 'col-lg-12 col-md-12 col-sm-12';
                                            } else if ($title_field_switch != 'yes' && $specialisam_field_switch == 'yes' && $location_field_switch != 'yes') {
                                                $specialisam_col = 'col-lg-12 col-md-12 col-sm-12';
                                            }
                                            ?>
                                            <?php } ?>
                                            <?php if ($title_field_switch == 'yes') { ?>
                                                <?php
                                                if (isset($job_search_style) and $job_search_style == "modren") {
                                                    echo '<div class="col-lg-12 col-md-12 col-sm-12"> ';
                                                } else if (isset($job_search_style) and $job_search_style == "classic") {
                                                    echo '<div class="col-lg-12 col-md-12 col-sm-12"> ';
                                                } else {
                                                    $title_col = (isset($job_search_style) && $job_search_style == "modren_v2") ? 'col-lg-7 col-md-7 col-sm-12 col-xs-12' : $title_col;
                                                    ?>
                                                    <div class="<?php echo $title_col; ?>">
                                                <?php } ?>
                                                <?php if ($job_lable_switch == 'yes' && $job_search_style != "fancy") { ?>
                                                    <span class="search_title"<?php
                                                    if ($job_search_layout_heading_color != '') {
                                                        echo ' style="color:' . $job_search_layout_heading_color . ' !important;"';
                                                    }
                                                    ?>><?php esc_html_e('Title ', 'jobhunt') ?>
                                                                </span>
                                                <?php } ?>

                                                <div class="search-input">

                                                    <?php if ($job_lable_switch == 'yes' && $job_search_style == "fancy") { ?>
                                                        <span class="search_title"<?php
                                                        if ($job_search_layout_heading_color != '') {
                                                            echo ' style="color:' . $job_search_layout_heading_color . ' !important;"';
                                                        }
                                                        ?>><?php esc_html_e('Keyword ', 'jobhunt') ?>
                                                                    </span>
                                                        <?php
                                                    }

                                                    if (isset($job_search_style) and $job_search_style != "fancy" and $job_search_style != "default_fancy" and $job_search_style != "modren_v2") {
                                                        /*if the view is aviation*/
                                                        if (isset($job_search_style) && $job_search_style != "aviation") {
                                                            ?>

                                                            <i class="icon-search7"></i>
                                                            <?php
                                                        }
                                                    }
                                                    if (isset($job_search_style) and $job_search_style == "default_fancy") {
                                                        echo '<i class="icon-search6"></i>';
                                                    }
                                                    ?>
                                                    <?php
                                                    $cs_opt_array = array(
                                                        'std' => isset($_GET['job_title']) ? $_GET['job_title'] : '',
                                                        'id' => '',
                                                        'cust_id' => 'job_title',
                                                        'cust_name' => 'job_title',
                                                        'classes' => '',
                                                        'extra_atr' => 'placeholder="' . esc_html__("Search Keywords", "jobhunt") . '" autocomplete="off"',
                                                    );
                                                    $cs_form_fields2->cs_form_text_render($cs_opt_array);

                                                    if ($job_search_hint_switch == 'yes') {
                                                        ?>
                                                        <label<?php
                                                        if ($job_search_layout_heading_color != '') {
                                                            echo ' style="color:' . $job_search_layout_heading_color . ' !important;"';
                                                        }
                                                        ?>>
                                                            <?php esc_html_e('Search keywords e.g. web design', 'jobhunt') ?>
                                                        </label>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                </div>
                                            <?php } ?>
                                            <?php if ($specialisam_field_switch == 'yes') { ?>
                                                <?php
                                                $specialisms_label = esc_html__('Specialism', 'jobhunt');
                                                $specialisms_label = apply_filters('jobhunt_replace_specialism_to_category', $specialisms_label);

                                                $all_specialisms_label = esc_html__('All specialisms', 'jobhunt');
                                                $all_specialisms_label = apply_filters('jobhunt_replace_all_specialisms', $all_specialisms_label);


                                                if (isset($job_search_style) and $job_search_style == "modren") {
                                                    echo '<div class="col-lg-12 col-md-12 col-sm-12"> ';
                                                } else if (isset($job_search_style) and $job_search_style == "classic") {
                                                    echo '<div class="col-lg-12 col-md-12 col-sm-12"> ';
                                                } else {
                                                    ?>
                                                    <div class="<?php echo $specialisam_col; ?>">
                                                    <?php
                                                }
                                                $res = apply_filters('jobhunt_multi_sepcialism_search','');
                                                if (empty($res)) {
                                                    ?>
                                                    <?php if ($job_lable_switch == 'yes' && $job_search_style != "fancy") { ?>
                                                        <span class="search_title"<?php
                                                        if ($job_search_layout_heading_color != '') {
                                                            echo ' style="color:' . $job_search_layout_heading_color . ' !important;"';
                                                        }
                                                        ?>><?php echo esc_html($specialisms_label); ?></span>
                                                    <?php } ?>
                                                    <div class="select-dropdown">

                                                        <?php if ($job_lable_switch == 'yes' && $job_search_style == "fancy") { ?>
                                                            <span class="search_title"<?php
                                                            if ($job_search_layout_heading_color != '') {
                                                                echo ' style="color:' . $job_search_layout_heading_color . ' !important;"';
                                                            }
                                                            ?>><?php echo esc_html($specialisms_label); ?></span>
                                                        <?php } ?>
                                                        <?php
                                                        $specialisms_options = array();
                                                        $specialisms_options[''] = $all_specialisms_label;
                                                        $specialisms_args = array(
                                                            'orderby' => 'name',
                                                            'order' => 'ASC',
                                                            'number' => '',
                                                            'fields' => 'all',
                                                            'slug' => '',
                                                            'hide_empty' => false,
                                                            'parent' => '0',
                                                        );
                                                        // get all job types
                                                        $all_specialisms = get_terms('specialisms', $specialisms_args);
                                                        if ($all_specialisms != '') {
                                                            foreach ($all_specialisms as $specialismsitem) {
                                                                if (isset($specialismsitem->name) && isset($specialismsitem->slug)) {
                                                                    $specialisms_options[$specialismsitem->slug] = $specialismsitem->name;
                                                                }
                                                            }
                                                        }

                                                        $cs_opt_array = array(
                                                            'std' => '',
                                                            'id' => '',
                                                            'cust_id' => 'specialisms',
                                                            'cust_name' => 'specialisms',
                                                            'options' => $specialisms_options,
                                                            'classes' => 'chosen-select',
                                                            'extra_atr' => 'data-placeholder="' . $all_specialisms_label . '"',
                                                        );

                                                        $cs_form_fields2->cs_form_select_render($cs_opt_array);
                                                        ?>

                                                        <?php if ($job_search_hint_switch == 'yes') { ?>

                                                            <label

                                                                <?php
                                                                if ($job_search_layout_heading_color != '') {
                                                                    echo ' style="color:' . $job_search_layout_heading_color . ' !important;"';
                                                                }
                                                                $filter_by_specialisms_label = esc_html__('Filter by specialisms e.g. developer, designer', 'jobhunt');
                                                                $filter_by_specialisms_label = apply_filters('jobhunt_replace_filter_by_specialisms', $filter_by_specialisms_label);
                                                                ?>><?php echo $filter_by_specialisms_label; ?></label>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                                </div>
                                                <?php
                                            }

                                            do_action('jobhunt_job_search_by_professions', $job_search_professions_field_switch, $settings, $job_lable_switch, $job_search_hint_switch, $specialisam_col, $job_search_layout_heading_color);


                                            if ($location_field_switch == 'yes') {
                                                if (isset($cs_plugin_options['cs_jobhunt_search_location']) && $cs_plugin_options['cs_jobhunt_search_location'] == 'on') {
                                                    ?>
                                                    <?php
                                                    $jobs_locations_enabled = apply_filters('jobhunt_jobs_locations_enabled', 'yes');
                                                    if( $jobs_locations_enabled == 'yes'){
                                                        ?>
                                                        <?php
                                                        if (isset($job_search_style) and $job_search_style == "modren") {
                                                            echo '<div class="col-lg-12 col-md-12 col-sm-12"> ';
                                                        } else if (isset($job_search_style) and $job_search_style == "classic") {
                                                            echo '<div class="col-lg-12 col-md-12 col-sm-12"> ';
                                                        } else {
                                                            ?>

                                                            <div class="<?php echo $location_col; ?>">
                                                        <?php } ?>
                                                        <?php if ($job_lable_switch == 'yes' && $job_search_style != "fancy") { ?>
                                                            <span class="search_title"<?php
                                                            if ($job_search_layout_heading_color != '') {
                                                                echo ' style="color:' . $job_search_layout_heading_color . ' !important;"';
                                                            }
                                                            ?>><?php esc_html_e('Locations', 'jobhunt') ?></span>
                                                        <?php } ?>
                                                        <div class="select-location">

                                                            <?php if ($job_lable_switch == 'yes' && $job_search_style == "fancy") { ?>
                                                                <span class="search_title"<?php
                                                                if ($job_search_layout_heading_color != '') {
                                                                    echo ' style="color:' . $job_search_layout_heading_color . ' !important;"';
                                                                }
                                                                ?>><?php esc_html_e('Location', 'jobhunt') ?></span>
                                                                <?php
                                                            }
                                                            $cs_radius = '';
                                                            if (isset($_GET['radius']) && $_GET['radius'] > 0) {
                                                                $cs_radius = $_GET['radius'];
                                                            }
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
                                                                $cs_radius_measure = $cs_radius_measure == 'km' ? esc_html__('KM', 'jobhunt') : esc_html__('Miles', 'jobhunt');
                                                                $min_value = isset($cs_plugin_options['cs_radius_min']) ? $cs_plugin_options['cs_radius_min'] : '';
                                                                $max_value = isset($cs_plugin_options['cs_radius_max']) ? $cs_plugin_options['cs_radius_max'] : '';
                                                                $radius_step = isset($cs_plugin_options['cs_radius_step']) ? $cs_plugin_options['cs_radius_step'] : '';

                                                                // from submitted value
                                                                $cs_radius = preg_replace("/[^0-9,.]/", "", $cs_radius);
                                                                if ($cs_radius == '') {
                                                                    $cs_radius = $cs_default_radius;
                                                                }
                                                            }
                                                            ?>

                                                            <div id="cs-top-select-holder" class="select-location" data-locationadminurl="<?php echo esc_url(admin_url("admin-ajax.php")) ?>">
                                                                <?php
                                                                if (isset($job_search_style) and $job_search_style == "default_fancy") {
                                                                    echo '<i class="icon-location6"></i>';
                                                                }
                                                                $hint_text = '';
                                                                if ($job_search_hint_switch == 'yes') {
                                                                    $hint_color = '';
                                                                    if ($job_search_layout_heading_color != '') {
                                                                        $hint_color = ' style="color:' . $job_search_layout_heading_color . ' !important;"';
                                                                    }
                                                                    $hint_text = '<span ' . $hint_color . '>' . esc_html__('Please select your desired location', 'jobhunt') . '</span>';
                                                                }

                                                                if ($cs_plugin_options['cs_google_autocomplete_enable'] == 'on') {
                                                                    cs_get_custom_locationswith_google_auto('<div id="cs-top-select-holder" class="search-country" style="display:' . cs_allow_special_char($cs_select_display) . '"><div class="select-holder">', '</div>' . $hint_text . ' </div>', false, true);
                                                                } else {
                                                                    cs_get_custom_locations('<div id="cs-top-select-holder" class="search-country" style="display:' . cs_allow_special_char($cs_select_display) . '">', $hint_text . ' </div>');
                                                                }
                                                                $list_rand = rand(0, 499999999);
                                                                ?>

                                                                <?php
                                                                if ($cs_radius_switch == 'on') {
                                                                    ?>
                                                                    <?php
                                                                    $cs_job_map_type = isset($cs_plugin_options['cs_job_map_type']) ? $cs_plugin_options['cs_job_map_type'] : 'google';
                                                                    if( $cs_job_map_type == 'google'){ ?>
                                                                        <a id="location_redius_popup<?php echo absint($list_rand); ?>" href="javascript:void(0);" class="location-btn pop"><i class="icon-target3"></i></a>
                                                                    <?php } ?>
                                                                    <div id="popup<?php echo absint($list_rand); ?>" style="display:none;"  class="select-popup">
                                                                        <a class="cs-location-close-popup" id="cs_close<?php echo absint($list_rand); ?>"><i class="cs-color icon-times"></i></a>
                                                                        <p><?php esc_html_e("Show With in", "jobhunt"); ?></p>
                                                                        <input id="ex6<?php echo absint($list_rand); ?>" name="radius" type="text" data-slider-min="<?php echo absint($min_value); ?>" data-slider-max="<?php echo absint($max_value); ?>" data-slider-step="<?php echo absint($radius_step); ?>" data-slider-value="<?php echo absint($cs_radius); ?>"/>
                                                                        <span id="ex6CurrentSliderValLabel_job"><span id="ex6SliderVal<?php echo absint($list_rand); ?>"><?php echo absint($cs_radius); ?></span><?php echo esc_html($cs_radius_measure); ?></span>
                                                                        <?php
                                                                        if ($cs_geo_location == 'on') {
                                                                            ?>
                                                                            <p class="my-location"><?php esc_html_e("of", "jobhunt"); ?> <i class="cs-color icon-location-arrow"></i><a class="cs-color" onclick="cs_get_location(this, '<?php echo $cs_google_api_key; ?>')"><?php esc_html_e("My location", "jobhunt"); ?></a></p>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </div>


                                                            <?php
                                                            $cs_form_fields2->cs_form_text_render(
                                                                array(
                                                                    'id' => '',
                                                                    'classes' => 'cs-geo-location form-control txt-field geo-search-location',
                                                                    'cust_name' => $cs_loc_name,
                                                                    'extra_atr' => ' onchange="this.form.submit()" style="display:' . cs_allow_special_char($cs_input_display) . ';" ' . $cs_loc_name,
                                                                    'std' => $cs_locatin_cust,
                                                                )
                                                            );
                                                            ?>

                                                            <div class="cs-undo-select" style="display:<?php echo cs_allow_special_char($cs_undo_display) ?>;">
                                                                <i class="icon-times"></i>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php }
                                            do_action('jobhunt_job_search_element_field_frontend', $settings);
                                            ?>

                                            <?php if (isset($job_search_style) and $job_search_style == "simple") { ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                    <?php if ($title_field_switch == 'yes' || $specialisam_field_switch == 'yes' || $location_field_switch == 'yes') { ?>
                                        <?php
                                        if (isset($job_search_style) and $job_search_style == "simple") {
                                            echo '<div class="col-lg-12 col-md-12 col-sm-12"> ';
                                        } else if (isset($job_search_style) and $job_search_style == "modren") {
                                            echo '<div class="col-lg-4 col-md-4 col-sm-12"> ';
                                        } else if (isset($job_search_style) and $job_search_style == "classic") {
                                            echo '<div class="col-lg-12 col-md-12 col-sm-12">';
                                        } else {
                                            ?>
                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                        <?php } ?>
                                        <?php if ($job_lable_switch == 'yes' && $job_search_style and ( $job_search_style != "simple" && $job_search_style != "fancy" )) { ?>
                                            <span class="search_title">&nbsp;</span>
                                        <?php } ?>
                                        <div class="search-btn">
                                            <?php if (isset($job_search_style) && ($job_search_style == "modren_v2")) { ?>
                                                <button class="cs-bgcolor" onclick="document.getElementById('frm_jobs_filtration').submit();">
                                                    <i class="icon-search6"></i>
                                                </button>
                                                <?php
                                            } else {
                                                $cs_opt_array = array(
                                                    'std' => esc_html__('Find Job', 'jobhunt'),
                                                    'id' => '',
                                                    'classes' => ' cs-bgcolor',
                                                    'extra_atr' => '',
                                                    'cust_id' => '',
                                                    'cust_name' => '',
                                                    'cust_type' => 'submit',
                                                );
                                                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                            }

                                            if ($job_advance_search_switch == 'yes' && isset($job_search_style) && $job_search_style != "default_fancy") {
                                                ?>
                                                <label>
                                                    <a<?php
                                                    if ($job_search_layout_heading_color != '') {
                                                        echo ' style="color:' . $job_search_layout_heading_color . ' !important;"';
                                                    }
                                                    ?> href="<?php echo esc_url($job_advance_search_url); ?>"  target="_blank">
                                                        <?php esc_html_e("+ Advance Search", "jobhunt") ?>
                                                    </a>
                                                </label>
                                            <?php } ?>
                                        </div>
                                        </div>
                                    <?php } ?>
                                    <?php  do_action('custom_search_frontfields_view');  ?>
                            </div></div>

                        </form>
                        <?php
                    } else {
                        if (isset($full_view) && $full_view == true) {
                            ?>
                            <div class="container">
                        <?php }
                        ?>
                        <div class="cs-search-result-warning">
                        <h2><?php esc_html_e("Please Set Search result page via following steps.") ?></h2>
                        <span><?php esc_html_e("Plugin Option => General Settings => Other => Search Result Page") ?></span>
                        </div><?php
                        if (isset($full_view) && $full_view == true) {
                            ?>
                            </div><?php
                        }
                    }
                    ?>

                </div>
                <?php
                if (isset($column_class) && $column_class != "") {
                ?>
            </div>
        <?php
        }
        ?>
        </div>
        <?php
        if ($job_advance_search_switch == 'yes' && isset($job_search_style) && $job_search_style == "default_fancy") {

            //advance-btn
            $adv_btn_class = '';
            if (isset($job_search_style) and $job_search_style == "default_fancy") {
                $adv_btn_class = ' class="advance-btn"';
            }
            ?>
            <label<?php echo $adv_btn_class; ?>>
                <a<?php
                if ($job_search_layout_heading_color != '') {
                    echo ' style="color:' . $job_search_layout_heading_color . ' !important;"';
                }
                ?> href="<?php echo esc_url($job_advance_search_url); ?>"  target="_blank">
                    <?php esc_html_e("+ Advance Search", "jobhunt") ?>
                </a>
            </label>
        <?php } ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                <?php
                if (!isset($list_rand)) {
                    $list_rand = '';
                }
                ?>
                jQuery("#ex6<?php echo absint($list_rand); ?>").slider();
                jQuery("#ex6<?php echo absint($list_rand); ?>").on("slide", function (slideEvt) {
                    jQuery("#ex6SliderVal<?php echo absint($list_rand); ?>").text(slideEvt.value);
                });

                jQuery('#location_redius_popup<?php echo absint($list_rand); ?>').click(function (event) {
                    event.preventDefault();
                    jQuery("#popup<?php echo absint($list_rand); ?>").css('display', 'block') //to show
                    return false;
                });
                jQuery('#cs_close<?php echo absint($list_rand); ?>').click(function () {
                    jQuery("#popup<?php echo absint($list_rand); ?>").css('display', 'none') //to show
                    return false;
                });
            });
        </script>
        <?php
        $eventpost_data = ob_get_clean();
        echo $eventpost_data;
    }

}

