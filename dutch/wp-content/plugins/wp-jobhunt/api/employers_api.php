<?php
do_action('jobcareer_cors');
//header('Access-Control-Allow-Origin: *');  // no cabe�alho
if (!class_exists('Employers_Api')) {

    class Employers_Api extends WP_REST_Controller {

        /**
         * Register the routes for the objects of the controller.
         */
        function __construct() {
            add_action('rest_api_init', array($this, 'register_routes_callback'));
        }

        public function register_routes_callback() {
            $version = '1';
            $namespace = 'api/v' . $version;
            $base = '';
            $routes = array(
                array(
                    'request' => 'employers',
                    'methods' => 'GET',
                    'callback' => 'employers_callback',
                ),
                array(
                    'request' => 'employer_details',
                    'methods' => 'GET',
                    'callback' => 'employer_details_callback',
                ),
            );
            foreach ($routes as $val) {
                $uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
                $uriSegments = array_pop($uriSegments);
                if ($uriSegments == $val['request']) {
                    register_rest_route($namespace, $base . '/' . $val['request'], array(
                        'methods' => $val['methods'],
                        'callback' => array($this, $val['callback']),
                        isset($val['args']) ? ($val['args']) : (''),
                    ));
                }
            }
        }

        /*
         * Employers List
         */

        public function employers_callback() {
            global $wpdb, $cs_plugin_options, $cs_form_fields2;
            $cs_allow_in_search_user_switch = isset($cs_plugin_options['cs_allow_in_search_user_switch']) ? $cs_plugin_options['cs_allow_in_search_user_switch'] : '';
            $employer_title = isset( $_GET['employer_title'] )? $_GET['employer_title'] : '';
            
            $employer_data = array();
            $qrystr = '';
            $filter_arr = array();
            $posted = '';
            $specialisms = array();
            $location = '';
            $default_date_time_formate = 'd-m-Y H:i:s';
            $cs_employer_activity_date_formate = 'd-m-Y H:i:s';

            $cus_fields_count_arr = array();
            $location_condition_arr = array();
            $user_allow_in_search_query = array();


            if (isset($cs_allow_in_search_user_switch) && $cs_allow_in_search_user_switch == 'on') {
                $user_allow_in_search_query = array(
                    'relation' => 'OR',
                    array(
                        'key' => 'cs_allow_search',
                        'value' => 'yes',
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'cs_allow_search',
                        'value' => '',
                        'compare' => '=',
                    ),
                );
            }
            $alphanumaric = '';
            $alphabatic_qrystr = '';
            $filter_arr2[] = '';
            $cs_employer_cus_fields = get_option("cs_employer_cus_fields");

            // end load all custom fileds for filtration
            $meta_post_ids_arr = array();
            $company_name_id_condition = '';
            
             if( $employer_title != ''){
                $company_name_id_condition = "UCASE(display_name) LIKE '%$employer_title%' AND";
            }

            $cs_company_name = '';

            $mypost = '';
            $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->users WHERE " . $company_name_id_condition . " 1=1 " . $alphabatic_qrystr);
            if ($post_ids) {
                $mypost = array('role' => 'cs_employer', 'order' => 'DESC', 'orderby' => 'registered',
                    'include' => $post_ids,
                    'fields' => 'ID',
                    'meta_query' => array(
                        array(
                            'key' => 'cs_user_status',
                            'value' => 'active',
                            'compare' => '=',
                        ),
                        array(
                            'key' => 'cs_user_last_activity_date',
                            'value' => strtotime(current_time($cs_employer_activity_date_formate)),
                            'compare' => '<=',
                        ),
                        $user_allow_in_search_query,
                        $location_condition_arr,
                    )
                );
            }
            $loop_count = new WP_User_Query($mypost);
            $count_post = $loop_count->total_users;


            $args = '';
            if ($count_post > 0) {
                $total_users = $count_post;
                $page = 1;
                if (isset($_GET['p'])) {
                    $page = $_GET['p'];
                }
                $users_per_page = 10;
                $total_pages = 1;
                $offset = 1;
                if ($users_per_page > 0) {
                    $offset = $users_per_page * ($page - 1);
                }
                if ($total_users > 0 && $users_per_page > 0) {
                    $total_pages = ceil($total_users / $users_per_page);
                }
                $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->users WHERE " . $company_name_id_condition . " 1=1 " . $alphabatic_qrystr);
                if ($post_ids) {
                    $args = array('number' => $users_per_page, 'role' => 'cs_employer', 'offset' => $offset, 'order' => 'ASC', 'orderby' => 'display_name',
                        'include' => $post_ids,
                        'fields' => array('ID', 'display_name', 'user_login'),
                        'meta_query' => array(
                            array(
                                'key' => 'cs_user_status',
                                'value' => 'active',
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'cs_user_last_activity_date',
                                'value' => strtotime(current_time($cs_employer_activity_date_formate)),
                                'compare' => '<=',
                            ),
                            $user_allow_in_search_query,
                            $location_condition_arr,
                        )
                    );
                }
                // end result query with paging
            }
            $loop = new WP_User_Query($args);
            $loop_count = $loop->total_users;

            $cs_job_posted_date_formate = 'd-m-Y H:i:s';
            $cs_job_expired_date_formate = 'd-m-Y H:i:s';
            //echo '<li>';
            $flag = 0;
            $output = '';
            if (!empty($loop->results)) {
                foreach ($loop->results as $cs_user) {

                    $cs_employee_emp_username = $cs_user->user_login;
                    $all_specialisms = get_user_meta($cs_user->ID, 'cs_specialisms', true);
                    $specialisms_values = '';
                    $specialisms_class = '';
                    $specialism_html = '';
                    $specialism_flag = 1;

                    if ($all_specialisms != '') {
                        foreach ($all_specialisms as $specialisms_item) {
                            $specialismsitem = get_term_by('slug', $specialisms_item, 'specialisms');
                            if (is_object($specialismsitem)) {
                                if ($specialism_flag == 1) {
                                    $specialism_html .= ' <span>' . $specialismsitem->name . '</span>';
                                } else {
                                    $specialism_html .= ' <span>,' . $specialismsitem->name . '</span>';
                                }
                                $specialisms_values .= $specialismsitem->name;
                                $specialisms_class .= $specialismsitem->slug;
                                if ($specialism_flag != count($all_specialisms)) {
                                    $specialisms_values .= ", ";
                                    $specialisms_class .= " ";
                                }
                                $specialism_flag ++;
                            }
                        }
                    }


                    $emp_jobpost = array('posts_per_page' => "1", 'post_type' => 'jobs', 'order' => "DESC", 'orderby' => 'post_date',
                        'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                        'meta_query' => array(
                            array(
                                'key' => 'cs_job_username',
                                'value' => $cs_employee_emp_username,
                                'compare' => '=',
                            ),
                            array(
                                'key' => 'cs_job_posted',
                                'value' => strtotime(date($cs_job_posted_date_formate)),
                                'compare' => '<=',
                            ),
                            array(
                                'key' => 'cs_job_expired',
                                'value' => strtotime(date($cs_job_expired_date_formate)),
                                'compare' => '>=',
                            ),
                            array(
                                'key' => 'cs_job_status',
                                'value' => 'active',
                                'compare' => '=',
                            ),
                        )
                    );
                    $loop_job_count = new WP_Query($emp_jobpost);
                    $count_job_post = $loop_job_count->found_posts;



                    $display_name = $cs_user->display_name;
                    $cs_employee_address = get_user_address_string_for_list($cs_user->ID, 'usermeta');

                    $cs_employee_employer_img = get_user_meta($cs_user->ID, 'user_img', true);
                    $cs_employee_employer_img = cs_get_img_url($cs_employee_employer_img, 'cs_media_5');

                    $output .= '<li>';
                    $output .= '<div class="item-content">';
                    $output .= '<a href="/employerdetail/' . $cs_user->ID . '" class="item-link">';
                    $output .= '<div class="image-holder">
                                        <img src="' . $cs_employee_employer_img . '"
                                            width="80">
                                    </div>';
                    $output .= '<div class="item-inner">';
                    $output .= '<div class="item-title-row">';
                    $output .= '<div class="item-title">';
                    $output .= ' <h4>' . esc_html($display_name) . '</h4>';
                    $output .= '<sapn class="item-after">' . esc_html($count_job_post) . "  " . esc_html__('Job(s)', 'jobhunt') . '</sapn>';
                    $output .= '</div>';
                    $output .= ' </div>';
                    $output .= '<div class="item-subtitle">' . $specialism_html . '</div>';
                    $output .= ' </div>';
                    $output .= '</a>';
                    $output .= ' </div>';
                    $output .= '</li>';
                }
            }
            $next_page = $page + 1;
            $employer_data['template'] = $output;
            $employer_data['next_api'] = site_url() . '/wp-json/api/v1/employers?p=' . $next_page;
            $info = array('status' => true,
                'data' => $employer_data);
            return new WP_REST_Response($info, 200);
        }

        /*
         * Employer Detail
         */

        public function employer_details_callback() {
            $author = isset($_GET['employer_id']) ? $_GET['employer_id'] : 0;
            $employer_data = array();
            $cs_user_data = get_userdata($author);

            $display_name = $cs_user_data->display_name;

            $cs_employee_employer_img = get_user_meta($cs_user_data->ID, 'user_img', true);
            $cs_employee_employer_img = cs_get_img_url($cs_employee_employer_img, 'cs_media_5');
            $description = $cs_user_data->description;

            $cs_post_comp_address = get_user_meta($cs_user_data->ID, 'cs_post_comp_address', true);
            $cs_post_comp_address = apply_filters('jobhunt_employer_address_frontend', $cs_post_comp_address, $cs_user_data->ID);
            $cs_post_comp_address = nl2br($cs_post_comp_address);

            // Specialisms
            $all_specialisms = get_user_meta($cs_user_data->ID, 'cs_specialisms', true);
            $specialism_html = '';
            if ($all_specialisms != '') {
                foreach ($all_specialisms as $specialisms_item) {
                    $specialismsitem = get_term_by('slug', $specialisms_item, 'specialisms');
                    if (is_object($specialismsitem)) {
                        if ($specialism_flag == 1) {
                            $specialism_html .= '<span class="item-detail-badge">' . $specialismsitem->name . '</span>';
                        } else {
                            $specialism_html .= '<span class="item-detail-badge">' . $specialismsitem->name . '</span>';
                        }
                        $specialisms_values .= $specialismsitem->name;
                        $specialisms_class .= $specialismsitem->slug;
                        if ($specialism_flag != count($all_specialisms)) {
                            $specialisms_values .= ", ";
                            $specialisms_class .= " ";
                        }
                        $specialism_flag ++;
                    }
                }
            }

            //Custom Fields

            $cs_employer_cus_fields = get_option("cs_employer_cus_fields");
            $custom_fields_list = array();
            if (is_array($cs_employer_cus_fields) && sizeof($cs_employer_cus_fields) > 0) {
                $custom_field_box = 1;
                foreach ($cs_employer_cus_fields as $cus_field) {
                    if ($cus_field['meta_key'] != '') {
                        $data = get_user_meta($cs_user_data->ID, $cus_field['meta_key'], true);
                        // empty check of value
                        if ($cus_field['label'] != '') {
                            if ($data != "") {
                                // check the data is array or not
                                if (is_array($data)) {
                                    $data_flage = 1;
                                    foreach ($data as $datavalue) {
                                        if ($cus_field['type'] == 'dropdown') {
                                            $options = $cus_field['options']['value'];
                                            if (isset($options)) {
                                                $finded_array = array_search($datavalue, $options);
                                                $datavalue = isset($finded_array) ? $cus_field['options']['label'][$finded_array] : '';
                                            }
                                            $data = esc_html($datavalue);
                                        } else {
                                            $data = esc_html($datavalue);
                                        }
                                        $data_flage ++;
                                    }
                                } else {
                                    if ($cus_field['type'] == 'dropdown') {
                                        $options = $cus_field['options']['value'];
                                        if (isset($options)) {
                                            $finded_array = array_search($data, $options);
                                            $data = isset($finded_array) ? $cus_field['options']['label'][$finded_array] : '';
                                        }
                                        $data = esc_html($data);
                                    } else {
                                        $data = esc_html($data);
                                    }
                                }

                                $custom_fields_list[] = array('label' => $cus_field['label'], 'data' => $data, 'icon' => isset($cus_field['fontawsome_icon']) ? $cus_field['fontawsome_icon'] : '');
                                $custom_field_box ++;
                            }
                        }
                    }
                }
            }


            /*
             * Employer's Posted Jobs
             */

            $args = array('posts_per_page' => -1, 'post_type' => 'jobs',
                'order' => 'DESC', 'orderby' => 'post_date', 'post_status' => 'publish', 'ignore_sticky_posts' => 1,
                'meta_query' => array(
                    array(
                        'key' => 'cs_job_username',
                        'value' => $cs_user_data->user_login,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'cs_job_posted',
                        'value' => current_time('timestamp'),
                        'compare' => '<=',
                    ),
                    array(
                        'key' => 'cs_job_expired',
                        'value' => current_time('timestamp'),
                        'compare' => '>=',
                    ),
                    array(
                        'key' => 'cs_job_status',
                        'value' => 'active',
                        'compare' => '=',
                    ),
                )
            );
            $loop = new WP_Query($args);
            $flag = 1;
            $output = '';
            while ($loop->have_posts()) : $loop->the_post();
                global $post;
                $cs_job_id = $post->ID;
                $list_job_id = $cs_job_id;
                $cs_job_posted = get_post_meta($cs_job_id, 'cs_job_posted', true);
                $cs_jobs_address = get_user_address_string_for_list($cs_job_id);
                $cs_jobs_thumb_url = '';
                // get employer images at run time
                $cs_job_employer = get_post_meta($cs_job_id, "cs_job_username", true); //
                $cs_job_employer = cs_get_user_id_by_login($cs_job_employer);
                $employer_img = get_the_author_meta('user_img', $cs_job_employer);
                $company_name = get_user_meta($cs_job_employer, 'first_name', true);
                $company_name = $company_name . ' ' . get_user_meta($cs_job_employer, 'last_name', true);
                if ($employer_img != '') {
                    $cs_jobs_thumb_url = cs_get_img_url($employer_img, 'cs_media_5');
                }
                $cs_jobs_thumb_url = apply_filters('digitalmarketing_job_image', $cs_jobs_thumb_url, $cs_job_id);
                $cs_job_featured = get_post_meta($cs_job_id, 'cs_job_featured', true);
                if (!cs_image_exist($cs_jobs_thumb_url) || $cs_jobs_thumb_url == "") {
                    $cs_jobs_thumb_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
                }
                $cs_job_featured = get_post_meta($cs_job_id, 'cs_job_featured', true);
                $cs_jobs_feature_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-feature.png');
                $all_job_type = get_the_terms($cs_job_id, 'job_type');
                $job_type_values = '';
                $job_type_class = '';
                $job_type_flag = 1;
                if ($all_job_type != '') {
                    foreach ($all_job_type as $job_type) {

                        $t_id_main = $job_type->term_id;
                        $job_type_color_arr = get_option("job_type_color_$t_id_main");
                        $job_type_color = '';
                        if (isset($job_type_color_arr['text'])) {
                            $job_type_color = $job_type_color_arr['text'];
                        }
                        $cs_link = ' href="javascript:void(0);"';
                        if ($cs_search_result_page != '') {
                            $cs_link = ' href="' . esc_url_raw(get_page_link($cs_search_result_page) . '?job_type=' . $job_type->slug) . '"';
                        }
                        $job_type = $job_type->name;
                        $job_type_flag ++;
                    }
                }
                $output .= '<li>';
                $output .= '<div class="item-content">';
                $output .= '<div class="item-inner">';
                $output .= '<a href="/jobDetail/' . $cs_job_id . '" class="">';
                if (isset($cs_job_featured) and $cs_job_featured == 'yes' || $cs_job_featured == 'on') {
                    $output .= '<span class="item-featured">Featured</span>';
                }
                
                $output .= '<div class="item-title">';
                $output .= ' <h4>' . esc_html(get_the_title($cs_job_id)) . '</h4>';
                $output .= '</div>';
                $output .= '          </a>';
                $output .= '<a href="#" class="item-favorite job-shortlist-class-emp" data-job_id="' . $cs_job_id . '">
                                <i class="icon f7-icons">heart</i>
                            </a>';
                $output .= '<div class="item-text">
                                        <a href="#">';
                if ($cs_jobs_address <> '')
                    $output .= ' <div class="item-address">';
                $output .= ' <span> ' . esc_html($cs_jobs_address) . '</span>';
                $output .= '</div>';
                $output .= '<div class="item-earning">';
                $output .= '  <span>' . $company_name . '</span>';
                $output .= ' <span>' . $job_type . '</span>';
                $output .= '</div>';
                $output .= ' </a>';
                
                $finded_result_list = cs_find_index_user_meta_list($cs_job_id, 'cs-user-jobs-wishlist', 'post_id', cs_get_user_id());
                if (isset($user) and $user <> '' and is_user_logged_in()) {
                    if (is_array($finded_result_list) && !empty($finded_result_list)) {
                        ?>
                        <a class="item-favorite" href="javascript:void(0)"  data-toggle="tooltip" data-placement="top" title="<?php esc_html_e("Added to Shortlist", "jobhunt"); ?>" id="<?php echo 'addjobs_to_wishlist' . intval($cs_job_id); ?>" onclick="cs_removejobs_to_wishlist('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo absint($cs_job_id); ?>', this)" ><i class="icon f7-icons"></i></a> 
                        <?php
                    } else {
                        ?>
                        <a class="item-favorite" href="javascript:void(0)"  data-toggle="tooltip" data-placement="top" title="<?php esc_html_e("Add to Shortlist", "jobhunt"); ?>" id="<?php echo 'addjobs_to_wishlist' . intval($cs_job_id); ?>" onclick="cs_addjobs_to_wishlist('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo absint($cs_job_id); ?>', this)" ><i class="icon f7-icons"></i></a> 
                        <?php
                    }
                }
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
                $output .= '</li>';
                
                $jobs[$cs_job_id] = ['job_id' => $cs_job_id, 'job_title' => esc_html(get_the_title($cs_job_id)), 'job_type' => esc_html($job_type), 'job_address' => esc_html($cs_jobs_address), 'job_link' => esc_url(get_permalink($cs_job_id)), 'company' => $company_name];

                $flag ++;
            endwhile;
            $employer_jobs = $output;
            wp_reset_postdata();






            $employer_data['company_name'] = $display_name;
            $employer_data['employer_img'] = $cs_employee_employer_img;
            $employer_data['employer_address'] = $cs_post_comp_address;
            $employer_data['employer_specialisms'] = $specialism_html;
            $employer_data['employer_custom_fields'] = $custom_fields_list;
            $employer_data['employer_description'] = $description;
            $employer_data['employer_jobs'] = $employer_jobs;
            $info = array('status' => true,
                'data' => $employer_data);
            return new WP_REST_Response($info, 200);
        }

    }

}
$Employers_Api = new Employers_Api();
