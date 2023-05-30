<?php
/*
 *
 * @Shortcode Name : Job Post
 * @retrun
 *
 */
if ( ! function_exists('jobcareer_pb_jobs_map') ) {

    function jobcareer_pb_jobs_map($die = 0) {
        global $cs_node, $cs_html_fields, $post, $cs_form_fields2;
        $shortcode_element = '';
        $filter_element = 'filterdrag';
        $shortcode_view = '';
        $output = array();
        $counter = $_POST['counter'];
        $cs_counter = $_POST['counter'];
        if ( isset($_POST['action']) && ! isset($_POST['shortcode_element_id']) ) {
            $POSTID = '';
            $shortcode_element_id = '';
        } else {
            $POSTID = $_POST['POSTID'];
            $PREFIX = 'cs_jobs_map';
            $parseObject = new ShortcodeParse();
            $shortcode_element_id = $_POST['shortcode_element_id'];
            $shortcode_str = stripslashes($shortcode_element_id);
            $output = $parseObject->cs_shortcodes($output, $shortcode_str, true, $PREFIX);
        }
        $defaults = array(
            'jobs_map_element_title' => '',
            'jobs_map_element_subtitle' => '',
            'jobs_maps_latitude' => '',
            'jobs_map_longitude' => '',
            'jobs_map_zoom_level' => '',
            'jobs_map_container_height' => '',
            'jobs_map_marker_icon' => '',
	    'jobs_map_cluster_icon' => '',
	    
            
        );
        if ( isset($output['0']['atts']) )
            $atts = $output['0']['atts'];
        else
            $atts = array();
        if ( isset($output['0']['content']) )
            $jobs_map_content = $output['0']['content'];
        else
            $jobs_map_content = '';
        $jobs_map_element_size = '100';
        foreach ( $defaults as $key => $values ) {
            if ( isset($atts[$key]) )
                $$key = $atts[$key];
            else
                $$key = $values;
        }
        $name = 'jobcareer_pb_jobs_map';
        $coloumn_class = 'column_' . $jobs_map_element_size;
        if ( isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode' ) {
            $shortcode_element = 'shortcode_element_class';
            $shortcode_view = 'cs-pbwp-shortcode';
            $filter_element = 'ajax-drag';
            $coloumn_class = '';
        }
        $cs_rand_id = rand(13441324, 93441324);
        ?>
        <div id="<?php echo esc_attr($name . $cs_counter) ?>_del" class="column  parentdelete <?php echo esc_attr($coloumn_class); ?> <?php echo esc_attr($shortcode_view); ?>" item="jobs_map" data="<?php echo cs_element_size_data_array_index($jobs_map_element_size) ?>" >
            <?php cs_element_setting($name, $cs_counter, $jobs_map_element_size, '', 'ellipsis-h', $type = ''); ?>
            <div class="cs-wrapp-class-<?php echo intval($cs_counter) ?> <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $cs_counter) ?>" data-shortcode-template="[cs_jobs_map {{attributes}}]{{content}}[/cs_jobs_map]" style="display: none;">
                <div class="cs-heading-area">
                    <h5><?php esc_html_e('JC: Jobs Map Option', 'jobhunt'); ?></h5>
                    <a href="javascript:removeoverlay('<?php echo esc_js($name . $cs_counter) ?>','<?php echo esc_js($filter_element); ?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
                <div class="cs-pbwp-content">
                    <div class="cs-wrapp-clone cs-shortcode-wrapp">
                        <?php
                        $cs_opt_array = array(
                            'name' => esc_html__('Element Title', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => '',
                            'echo' => true,
                            'field_params' => array(
                                'std' => $jobs_map_element_title,
                                'id' => 'jobs_map_element_title',
                                'cust_name' => 'jobs_map_element_title[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);
                        $cs_opt_array = array(
                            'name' => esc_html__('Element Subtitle', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => '',
                            'echo' => true,
                            'field_params' => array(
                                'std' => $jobs_map_element_subtitle,
                                'id' => 'jobs_map_element_subtitle',
                                'cust_name' => 'jobs_map_element_subtitle[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);
                        $cs_opt_array = array(
                            'name' => esc_html__('Map Latitude', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => '',
                            'echo' => true,
                            'field_params' => array(
                                'std' => $jobs_maps_latitude,
                                'id' => 'jobs_maps_latitude',
                                'cust_name' => 'jobs_maps_latitude[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);
                        $cs_opt_array = array(
                            'name' => esc_html__('Map Longitude', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => '',
                            'echo' => true,
                            'field_params' => array(
                                'std' => $jobs_map_longitude,
                                'id' => 'jobs_map_longitude',
                                'cust_name' => 'jobs_map_longitude[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);
                        $cs_opt_array = array(
                            'name' => esc_html__('Zoom Level', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => '',
                            'echo' => true,
                            'field_params' => array(
                                'std' => $jobs_map_zoom_level,
                                'id' => 'jobs_map_zoom_level',
                                'cust_name' => 'jobs_map_zoom_level[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);
                        $cs_opt_array = array(
                            'name' => esc_html__('Map Height', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => '',
                            'echo' => true,
                            'field_params' => array(
                                'std' => $jobs_map_container_height,
                                'id' => 'jobs_map_container_height',
                                'cust_name' => 'jobs_map_container_height[]',
                                'return' => true,
                            ),
                        );
                        $cs_html_fields->cs_text_field($cs_opt_array);
                        $cs_opt_array = array(
                            'std' => $jobs_map_marker_icon,
                            'cust_name' => 'jobs_map_marker_icon[]',
                            'name' => esc_html__('Jobs Marker Icon', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => esc_html__('Select the Marker Icon Path for element.', 'jobhunt'),
                            'echo' => true,
                            'array' => true,
                            'prefix' => '',
                            'field_params' => array(
                                'std' => $jobs_map_marker_icon,
                                'cust_id' => '',
                                'cust_name' => 'jobs_map_marker_icon[]',
                                'return' => true,
                                'array' => true,
                                'array_txt' => false,
                                'prefix' => '',
                            ),
                        );
                        $cs_html_fields->cs_upload_file_field($cs_opt_array);
			
			 $cs_opt_array = array(
                            'std' => $jobs_map_cluster_icon,
                            'cust_name' => 'jobs_map_cluster_icon[]',
                            'name' => esc_html__('Jobs Cluster Icon', 'jobhunt'),
                            'desc' => '',
                            'hint_text' => esc_html__('Select the Cluster Icon Path for element.', 'jobhunt'),
                            'echo' => true,
                            'array' => true,
                            'prefix' => '',
                            'field_params' => array(
                                'std' => $jobs_map_cluster_icon,
                                'cust_id' => '',
                                'cust_name' => 'jobs_map_cluster_icon[]',
                                'return' => true,
                                'array' => true,
                                'array_txt' => false,
                                'prefix' => '',
                            ),
                        );
                        $cs_html_fields->cs_upload_file_field($cs_opt_array);
                        ?>
                    </div>
                    <?php if ( isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode' ) { ?>
                        <ul class="form-elements insert-bg">
                            <li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo str_replace('jobcareer_pb_', '', $name); ?>', '<?php echo esc_js($name . $cs_counter) ?>', '<?php echo esc_js($filter_element); ?>')" ><?php esc_html_e('Insert', 'jobhunt'); ?></a> </li>
                        </ul>
                        <div id="results-shortocde"></div>
                    <?php } else { ?>
                        <ul class="form-elements noborder">
                            <li class="to-label"></li>
                            <li class="to-field">
                                <?php
                                $cs_opt_array = array(
                                    'id' => '',
                                    'std' => 'jobs_map',
                                    'cust_id' => "",
                                    'cust_name' => "cs_orderby[]",
                                );

                                $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                                $cs_opt_array = array(
                                    'id' => '',
                                    'std' => esc_html__('Save', 'jobhunt'),
                                    'cust_id' => "",
                                    'cust_name' => "",
                                    'cust_type' => 'button',
                                    'extra_atr' => 'style="margin-right:10px;" onclick="javascript:_removerlay(jQuery(this))"',
                                );

                                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                ?>
                            </li>
                        </ul>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php
        if ( $die <> 1 )
            die();
    }

    add_action('wp_ajax_jobcareer_pb_jobs_map', 'jobcareer_pb_jobs_map');
}