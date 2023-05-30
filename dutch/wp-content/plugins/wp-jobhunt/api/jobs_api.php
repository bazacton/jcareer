<?php
header('Access-Control-Allow-Origin: *');  // no cabeçalho
if (!class_exists('Job_Api')) {

    class Job_Api extends WP_REST_Controller {

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
                    'request' => 'job_results',
                    'methods' => 'GET',
                    'callback' => 'job_results_callback',
                ),
                array(
                    'request' => 'shortlisted_jobs',
                    'methods' => 'GET',
                    'callback' => 'shortlisted_jobs_callback',
                ),
                array(
                    'request' => 'applied_jobs',
                    'methods' => 'GET',
                    'callback' => 'applied_jobs_callback',
                ),
                array(
                    'request' => 'job_detail',
                    'methods' => 'GET',
                    'callback' => 'job_detail_callback',
                ),
                array(
                    'request' => 'mark_favourite',
                    'methods' => 'POST',
                    'callback' => 'mark_favourite_callback',
                    'args' => array(
                        'job_id' => isset($_POST['job_id']) ? ($_POST['job_id']) : (''),
                        'user_id' => isset($_POST['user_id']) ? ($_POST['user_id']) : ('')
                    )
                ),
                array(
                    'request' => 'remove_favourite',
                    'methods' => 'POST',
                    'callback' => 'remove_favourite_callback',
                    'args' => array(
                        'job_id' => isset($_POST['job_id']) ? ($_POST['job_id']) : (''),
                        'user_id' => isset($_POST['user_id']) ? ($_POST['user_id']) : ('')
                    )
                ),
                array(
                    'request' => 'job_filters',
                    'methods' => 'GET',
                    'callback' => 'job_filters_callback'
                ),
                array(
                    'request' => 'local_shortlisted_jobs',
                    'methods' => 'GET',
                    'callback' => 'local_shortlisted_jobs_callback'
                ),
                array(
                    'request' => 'local_viewed_jobs',
                    'methods' => 'GET',
                    'callback' => 'local_viewed_jobs_callback'
                ),
                array(
                    'request' => 'account_settings',
                    'methods' => 'POST',
                    'callback' => 'account_settings_callback',
                    'args' => array(
                        'user_id' => isset($_POST['user_id']) ? ($_POST['user_id']) : (''),
                        'name' => isset($_POST['name']) ? ($_POST['name']) : (''),
                        'last_name' => isset($_POST['last_name']) ? ($_POST['last_name']) : (''),
                        'email' => isset($_POST['email']) ? ($_POST['email']) : (''),
                        'phone_number' => isset($_POST['phone_number']) ? ($_POST['phone_number']) : (''),
                        'profile_privacy' => isset($_POST['profile_privacy']) ? ($_POST['profile_privacy']) : ('')
                    )
                ),
                array(
                    'request' => 'job_apply',
                    'methods' => 'POST',
                    'callback' => 'job_apply_callback',
                    'args' => array(
                        'user_id' => isset($_POST['user_id']) ? ($_POST['user_id']) : (''),
                        'job_id' => isset($_POST['job_id']) ? ($_POST['job_id']) : (''),
                        'cv' => isset($_POST['cv']) ? ($_POST['cv']) : (''),
                    )
                ),
                array(
                    'request' => 'job_discard_undo_discard',
                    'methods' => 'POST',
                    'callback' => 'job_discard_undo_discard_callback',
                    'args' => array(
                        'user_id' => isset($_POST['user_id']) ? ($_POST['user_id']) : (''),
                        'job_id' => isset($_POST['job_id']) ? ($_POST['job_id']) : (''),
                        'discard_status' => isset($_POST['discard_status']) ? ($_POST['discard_status']) : (''),
                    )
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
         * Job Results Call
         */

        public function job_results_callback() {
            global $cs_plugin_options;
            $user_id            = isset( $_GET['user_id'] )? $_GET['user_id'] : 0;
            $shortlisted_jobs            = isset( $_GET['shortlisted_jobs'] )? $_GET['shortlisted_jobs'] : array();
            $shortlisted_jobs       = explode( ',', $shortlisted_jobs);
            $cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';
            $filter_arr2[] = '';
            $post_ids = '';
            $current_timestamp = current_time('timestamp');
            $page = 1;
            if (isset($_GET['p'])) {
                $page = $_GET['p'];
            }
            $specialisms = '';
            //submit filters 

            $location_condition_arr = array();
            if (isset($_GET['job_title'])) {
                global $wpdb;
                $job_title = $_GET['job_title'];
                $job_title = str_replace("+", " ", $job_title);
                $meta_join = "LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id=$wpdb->posts.ID";
                $meta_where = "OR UCASE(meta_value) LIKE '%$job_title%'";
                $meta_post_ids_arr = array();
                $meta_post_ids_arr = cs_get_query_whereclase_by_array($filter_arr);
                

                // if no result found in filtration 
                if (empty($meta_post_ids_arr)) {
                    $meta_post_ids_arr = array(0);
                }
                $ids = $meta_post_ids_arr != '' ? implode(",", $meta_post_ids_arr) : '0';
                //$job_title_id_condition = " ID in (" . $ids . ") AND ";
                $post_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts $meta_join WHERE " . $job_title_id_condition . " (UCASE(post_title) LIKE '%$job_title%' OR UCASE(post_content) LIKE '%$job_title%' $meta_where) AND post_type='jobs' AND post_status='publish'");
            }
            if (isset($_REQUEST['cs_search_location_field'])) {
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
            }
            if (isset($_GET['specialisms_string']) && $_GET['specialisms_string'] != '') {
                $specialisms = explode(",", $_GET['specialisms_string']);
                $qrystr .= '&specialisms=' . $_GET['specialisms_string'];
            } elseif (isset($_GET['specialisms_string_all']) && $_GET['specialisms_string_all'] != '') {
                $specialisms = explode(",", $_GET['specialisms_string_all']);
                $qrystr .= '&specialisms=' . $_GET['specialisms_string_all'];
            } elseif (isset($_GET['specialisms']) && $_GET['specialisms'] != '') {
                $specialisms = $_GET['specialisms'];
                $qrystr .= '&specialisms=' . $_GET['specialisms'];
                if (!is_array($specialisms))
                    $specialisms = Array($specialisms);
            }
            
            if (isset($_GET['job_type_string']) && $_GET['job_type_string'] != '') {
                $job_type = explode(",", $_GET['job_type_string']);
            }
            
            // specialism check
            if ($job_type != '') {
                $filter_multi_spec_arr = ['relation' => 'OR',];
                foreach ($job_type as $job_type_key) {
                    if ($job_type_key != '') {
                        $filter_multi_spec_arr[] = array(
                            'taxonomy' => 'job_type',
                            'field' => 'term_id',
                            'terms' => array($job_type_key)
                        );
                    }
                }
                $filter_arr2[] = array(
                    $filter_multi_spec_arr
                );
            }
            
            
            // specialism check
            if ($specialisms != '' && $specialisms != 'All specialisms') {
                $filter_multi_spec_arr = ['relation' => 'OR',];
                foreach ($specialisms as $specialisms_key) {
                    if ($specialisms_key != '') {
                        $filter_multi_spec_arr[] = array(
                            'taxonomy' => 'specialisms',
                            'field' => 'slug',
                            'terms' => array($specialisms_key)
                        );
                    }
                }
                $filter_arr2[] = array(
                    $filter_multi_spec_arr
                );
            }
            $cs_shortlist = array();
            if (isset($user_id) && $user_id <> '') {
                $cs_shortlist_array = get_user_meta($user_id, 'cs-user-jobs-wishlist', true);
                if (!empty($cs_shortlist_array))
                    $cs_shortlist = array_column_by_two_dimensional($cs_shortlist_array, 'post_id');
                else
                    $cs_shortlist = array();
            }
            if( empty( $cs_shortlist ) ){
                $cs_shortlist   = $shortlisted_jobs;
            }
            
            $cs_shortlist   = empty( $cs_shortlist )? array(0): $cs_shortlist;
            $args = array(
                'posts_per_page' => "10",
                'post_type' => 'jobs',
                'paged' => $page,
                'order' => 'DESC',
                'orderby' => array(
                    'meta_value' => 'DESC',
                    'post_date' => 'DESC',
                ),
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'meta_key' => 'cs_job_featured',
                'post__in' => $post_ids,
                'post__not_in' => $cs_shortlist,
                'fields' => 'ids', // only load ids
                'tax_query' => array(
                    'relation' => 'AND',
                    $filter_arr2
                ),
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
                    array(
                        'key' => 'cs_job_featured',
                        'compare' => 'EXISTS',
                        'type' => 'STRING'
                    ),
                    $location_condition_arr,
                ),
            );
            $loop = new WP_Query($args);
            $found_posts = $loop->found_posts;
            $output = '';
            //$output .= '<ul>';
            // getting if record not found
            $jobs   = array();
            if ($loop->have_posts()) {
                $jobs['data_exists']    = 'yes';
                $flag = 1;
                while ($loop->have_posts()) : $loop->the_post();
                    global $post;
                    $cs_job_id = $post;
                    $list_job_id = $cs_job_id;
                    $cs_job_posted = get_post_meta($cs_job_id, 'cs_job_posted', true);
                    $cs_jobs_address = get_user_address_string_for_list($cs_job_id);
                    $cs_jobs_thumb_url = '';
                    // get employer images at run time
                    $cs_job_employer = get_post_meta($cs_job_id, "cs_job_username", true); //
                    $cs_job_employer = cs_get_user_id_by_login($cs_job_employer);
                    $employer_img = get_the_author_meta('user_img', $cs_job_employer);
                    $company_name = get_user_meta( $cs_job_employer, 'first_name', true);
                    $company_name   = $company_name.' '.get_user_meta( $cs_job_employer, 'last_name', true);
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
                    $output .= '<a href="/jobDetail/'. $cs_job_id .'" class="">';
                    if (isset($cs_job_featured) and $cs_job_featured == 'yes' || $cs_job_featured == 'on') {
                        $output .= '<span class="item-featured">Featured</span>';
                    }
                    $output .= '<div class="item-title">';
                    $output .= ' <h4>' . esc_html(get_the_title($cs_job_id)) . '</h4>';
                    $output .= '</div>';
                    $output .= '          </a>';
                    $output .= '<a href="#" class="item-favorite job-shortlist-class" data-job_id="'. $cs_job_id .'">
                                <i class="icon f7-icons">heart</i>
                            </a>';
                    $output .= '<div class="item-text">
                                        <a href="#">';
                    if ($cs_jobs_address <> '')
                        $output .= ' <div class="item-address">';
                    $output .= ' <span> ' . esc_html($cs_jobs_address) . '</span>';
                    $output .= '</div>';
                    $output .= '<div class="item-earning">';
                    $output .= '  <span>'. $company_name .'</span>';
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
                    $output .= '</li>';
                    $jobs[$cs_job_id] = ['job_id' => $cs_job_id, 'job_title' => esc_html(get_the_title($cs_job_id)), 'job_type' => esc_html($job_type), 'job_address' => esc_html($cs_jobs_address), 'job_link' => esc_url(get_permalink($cs_job_id)), 'company' => $company_name];

                    $flag ++;
                endwhile;
            }else{
                $output = '<div class="text-align-center padd-t-40 padd-l-15 padd-r-15 display-flex align-items-center justify-content-center">
                            <div class="not-found-screen">
                                <div class="text-big">
                                    <img src="./static/search-in-folder.png" alt />
                                </div>
                                <div class="text">
                                    <strong>No Items Found</strong>
                                    <p>Please select different filters for results.</p>
                                </div>
                            </div>
                        </div>';
                $jobs['data_exists']    = 'no';
            }

            //$output .= '</ul>';
            $next_page = $page+1;
            $jobs['template'] = $output;
            $jobs['next_api'] = site_url().'/wp-json/api/v1/job_results?p='.$next_page;
            $info = array('status' => true,
                'data' => $jobs);
            return new WP_REST_Response($info, 200);
        }
        
        
        /*
         * Shortlisted Jobs by User ID
         */
        
        public function shortlisted_jobs_callback(){
            global $cs_plugin_options;
            $cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';
            $user_id            = isset( $_GET['user_id'] )? $_GET['user_id'] : 0;
            $cs_shortlist = array(0);
            if (isset($user_id) && $user_id <> '') {
                $cs_shortlist_array = get_user_meta($user_id, 'cs-user-jobs-wishlist', true);
                if (!empty($cs_shortlist_array))
                    $cs_shortlist = array_column_by_two_dimensional($cs_shortlist_array, 'post_id');
                else
                    $cs_shortlist = array(0);
            }
            $jobs = array();
            $jobs_list = array();
            $filter_arr2[] = '';
            $post_ids = '';
            $current_timestamp = current_time('timestamp');
            $page = 1;
            if (isset($_GET['p'])) {
                $page = $_GET['p'];
            }
            
            $args = array(
                'posts_per_page' => "10",
                'post_type' => 'jobs',
                'paged' => $page,
                'order' => 'DESC',
                'orderby' => array(
                    'meta_value' => 'DESC',
                    'post_date' => 'DESC',
                ),
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'meta_key' => 'cs_job_featured',
                'post__in' => $cs_shortlist,
                'fields' => 'ids', // only load ids
                'tax_query' => array(
                    'relation' => 'AND',
                    $filter_arr2
                ),
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
                    array(
                        'key' => 'cs_job_featured',
                        'compare' => 'EXISTS',
                        'type' => 'STRING'
                    ),
                ),
            );
            $loop = new WP_Query($args);
            $found_posts = $loop->found_posts;
            $output = '';
            // getting if record not found
            if ($loop->have_posts()) {
                $flag = 1;
                while ($loop->have_posts()) : $loop->the_post();
                    global $post;
                    $cs_job_id = $post;
                    $list_job_id = $cs_job_id;
                    $cs_job_posted = get_post_meta($cs_job_id, 'cs_job_posted', true);
                    $cs_jobs_address = get_user_address_string_for_list($cs_job_id);
                    $cs_jobs_thumb_url = '';
                    // get employer images at run time
                    $cs_job_employer = get_post_meta($cs_job_id, "cs_job_username", true); //
                    $cs_job_employer = cs_get_user_id_by_login($cs_job_employer);
                    $company_name = get_user_meta( $cs_job_employer, 'first_name', true);
                    $company_name   = $company_name.' '.get_user_meta( $cs_job_employer, 'last_name', true);
                    $employer_img = get_the_author_meta('user_img', $cs_job_employer);
                    
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
                    $output .= '<a href="/jobDetail/'. $cs_job_id .'" class="">';
                    if (isset($cs_job_featured) and $cs_job_featured == 'yes' || $cs_job_featured == 'on') {
                        $output .= '<span class="item-featured">Featured</span>';
                    }
                    $output .= '<div class="item-title">';
                    $output .= ' <h4>' . esc_html(get_the_title($cs_job_id)) . '</h4>';
                    $output .= '</div>';
                    $output .= '          </a>';
                    
                    $output .= '<div class="item-text">
                                        <a href="#">';
                    if ($cs_jobs_address <> '')
                        $output .= ' <div class="item-address">';
                    $output .= ' <span> ' . esc_html($cs_jobs_address) . '</span>';
                    $output .= '</div>';
                    $output .= '<div class="item-earning">';
                    $output .= '  <span>'. $company_name .'</span>';
                    $output .= ' <span>' . $job_type . '</span>';
                    $output .= '</div>';
                    $output .= ' </a>';
                    $output .= '<a href="#" class="item-remove-btn job-shortlist-remove-class" data-job_id="'. $cs_job_id .'">
                                    <i class="icon f7-icons">trash</i>
                                </a>';
                    $output .= '</div>';
                    $output .= '</div>';
                    $output .= '</div>';
                    $output .= '</li>';
                    $jobs_list[$cs_job_id] = ['job_id' => $cs_job_id, 'job_title' => esc_html(get_the_title($cs_job_id)), 'job_type' => esc_html($job_type), 'job_address' => esc_html($cs_jobs_address), 'job_link' => esc_url(get_permalink($cs_job_id))];

                    $flag ++;
                endwhile;
            }else{
                $output = '<div class="text-align-center padd-t-40 padd-l-15 padd-r-15 display-flex align-items-center justify-content-center">
                            <div class="not-found-screen">
                                <div class="text-big">
                                    <img src="./static/search-in-folder.png" alt />
                                </div>
                                <div class="text">
                                    <strong>No Items Found</strong>
                                    <p>To add a favorite. Tap the heart on any jobs</p>
                                </div>
                            </div>
                        </div>';
            }

            //$output .= '</ul>';
            $next_page = $page+1;
            $jobs['total_jobs']   = count($jobs_list);
            $jobs['template'] = $output;
            $jobs['next_api'] = site_url().'/wp-json/api/v1/shortlisted_jobs?user_id='. $user_id .'&p='.$next_page;
            $info = array('status' => true,
                'data' => $jobs);
            return new WP_REST_Response($info, 200);
        }
        
        /*
         * Applied Jobs by User ID
         */
        
        public function applied_jobs_callback(){
            global $cs_plugin_options;
            $cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';
            $user_id            = isset( $_GET['user_id'] )? $_GET['user_id'] : 0;
            
            $job_applied_dates = array();
            if (isset($user_id) && $user_id <> '') {
                $cs_jobapplied_array = get_user_meta($user_id, 'cs-user-jobs-applied-list', true);
                
                foreach( $cs_jobapplied_array as $cs_jobapplied_arr){
                    $cs_jobapplied[]    = $cs_jobapplied_arr['post_id'];
                    $job_applied_dates[$cs_jobapplied_arr['post_id']]   = $cs_jobapplied_arr['date_time'];
                }
                
                //if (!empty($cs_jobapplied_array))
                    //$cs_jobapplied = array_column_by_two_dimensional($cs_jobapplied_array, 'post_id');
                //else
                    //$cs_jobapplied = array(0);
            }
            $cs_jobapplied  = (empty( $cs_jobapplied))? array(0) : $cs_jobapplied;
            
            
            $filter_arr2[] = '';
            $post_ids = '';
            $current_timestamp = current_time('timestamp');
            $page = 1;
            if (isset($_GET['p'])) {
                $page = $_GET['p'];
            }
            
            $args = array(
                'posts_per_page' => "10",
                'post_type' => 'jobs',
                'paged' => $page,
                'order' => 'DESC',
                'orderby' => array(
                    'meta_value' => 'DESC',
                    'post_date' => 'DESC',
                ),
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'meta_key' => 'cs_job_featured',
                'post__in' => $cs_jobapplied,
                'fields' => 'ids', // only load ids
                'tax_query' => array(
                    'relation' => 'AND',
                    $filter_arr2
                ),
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
                    array(
                        'key' => 'cs_job_featured',
                        'compare' => 'EXISTS',
                        'type' => 'STRING'
                    ),
                ),
            );
            $loop = new WP_Query($args);
            $found_posts = $loop->found_posts;
            $output = '';
            // getting if record not found
            if ($loop->have_posts()) {
                $flag = 1;
                while ($loop->have_posts()) : $loop->the_post();
                    global $post;
                    $cs_job_id = $post;
                    $list_job_id = $cs_job_id;
                    $cs_job_posted = get_post_meta($cs_job_id, 'cs_job_posted', true);
                    $cs_jobs_address = get_user_address_string_for_list($cs_job_id);
                    $cs_jobs_thumb_url = '';
                    $applied_date = isset( $job_applied_dates[$cs_job_id]) ? date_i18n(get_option('date_format'), $job_applied_dates[$cs_job_id]) : '';
                    // get employer images at run time
                    $cs_job_employer = get_post_meta($cs_job_id, "cs_job_username", true); //
                    $cs_job_employer = cs_get_user_id_by_login($cs_job_employer);
                    $company_name = get_user_meta( $cs_job_employer, 'first_name', true);
                    $company_name   = $company_name.' '.get_user_meta( $cs_job_employer, 'last_name', true);
                    $employer_img = get_the_author_meta('user_img', $cs_job_employer);
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
                    $output .= '<a href="/jobDetail/'. $cs_job_id .'" class="">';
                    if (isset($cs_job_featured) and $cs_job_featured == 'yes' || $cs_job_featured == 'on') {
                        $output .= '<span class="item-featured">Featured</span>';
                    }
                    $output .= '<div class="item-title">';
                    $output .= ' <h4>' . esc_html(get_the_title($cs_job_id)) . '</h4>';
                    $output .= '</div>';
                    $output .= '          </a>';
                    $output .= '<div class="item-text">
                                        <a href="#">';
                    if ($cs_jobs_address <> '')
                        $output .= ' <div class="item-address">';
                    $output .= ' <span> ' . esc_html($cs_jobs_address) . '</span>';
                    $output .= '</div>';
                    $output .= '<div class="item-date"><span>'.$applied_date.'</span></div>';
                    $output .= '<div class="item-earning">';
                    $output .= '  <span>'. $company_name .'</span>';
                    $output .= ' <span>' . $job_type . '</span>';
                    $output .= '</div>';
                    $output .= ' </a>';
                    $output .= '</div>';
                    $output .= '</div>';
                    $output .= '</div>';
                    $output .= '</li>';
                    $jobs[$cs_job_id] = ['job_id' => $cs_job_id, 'job_title' => esc_html(get_the_title($cs_job_id)), 'job_type' => esc_html($job_type), 'job_address' => esc_html($cs_jobs_address), 'job_link' => esc_url(get_permalink($cs_job_id))];

                    $flag ++;
                endwhile;
            }else{
                $output = '<div class="text-align-center padd-t-40 padd-l-15 padd-r-15 display-flex align-items-center justify-content-center">
                            <div class="not-found-screen">
                                <div class="text-big">
                                    <img src="./static/search-in-folder.png" alt />
                                </div>
                                <div class="text">
                                    <strong>No Items Found</strong>
                                    <p>Apply to any job from the detail page.</p>
                                </div>
                            </div>
                        </div>';
            }

            //$output .= '</ul>';
            $next_page = $page+1;
            $jobs['template'] = $output;
            $jobs['next_api'] = site_url().'/wp-json/api/v1/shortlisted_jobs?user_id='. $user_id .'&p='.$next_page;
            $info = array('status' => true,
                'data' => $jobs);
            return new WP_REST_Response($info, 200);
        }

        /*
         * Job Detail Call
         */

        public function job_detail_callback() {
           $job_id = isset( $_GET['job_id'] )? $_GET['job_id'] : 0;
           $cs_job_username = get_post_meta($job_id, 'cs_job_username', true);
            // getting employer information
            $employer_post = get_user_by('login', $cs_job_username);
            
            
            $cs_employee_employer_img = '';
            $cs_job_username = cs_get_user_id_by_login($cs_job_username);
            $employer_img = get_the_author_meta('user_img', $cs_job_username);
            if ($employer_img != '') {
                $cs_employee_employer_img = cs_get_img_url($employer_img, 'cs_media_5');
            }
            
            if (isset($employer_post) && $employer_post != '') {

                $cs_employee_web_http = $employer_post->user_url;
                $cs_email = $employer_post->user_email;
                $cs_employee_web = preg_replace('#^https?://#', '', $cs_employee_web_http);
                $cs_employee_facebook = get_user_meta($employer_post->ID, 'cs_facebook', true);
                $cs_employee_twitter = get_user_meta($employer_post->ID, 'cs_twitter', true);
                $cs_employee_linkedin = get_user_meta($employer_post->ID, 'cs_linkedin', true);
                $cs_employee_google_plus = get_user_meta($employer_post->ID, 'cs_google_plus', true);
                $cs_phone_number = get_user_meta($employer_post->ID, 'cs_phone_number', true);
                $username = $employer_post->display_name;
            }
            $job_address = get_post_meta($job_id, 'cs_post_loc_address', true);
            $cs_job_posted_date = get_post_meta($job_id, 'cs_job_posted', true);
            
            $all_job_type = get_the_terms($job_id, 'job_type');
            $job_type   = '';
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
            
            
            $cs_job_cus_fields = get_option("cs_job_cus_fields");
            if (is_array($cs_job_cus_fields) && sizeof($cs_job_cus_fields) > 0) {

                $custom_field_box = 1;
                $custom_fields_list = array();
                foreach ($cs_job_cus_fields as $cus_field) {
                    if ($cus_field['meta_key'] != '') {
                        $data = get_post_meta($job_id, $cus_field['meta_key'], true);
                        // empty check of value
                        if ($cus_field['label'] != '')
                            if ($data != "") {
                                // check the data is array or not
                                if (is_array($data)) {
                                    $data_flage = 1;
                                    $comma = '';
                                    foreach ($data as $datavalue) {
                                        if ($cus_field['type'] == 'dropdown') {
                                            $options = $cus_field['options']['value'];
                                            if (isset($options)) {
                                                $finded_array = array_search($datavalue, $options);
                                                $datavalue = isset($finded_array) ? $cus_field['options']['label'][$finded_array] : '';
                                            }
                                            $comma . esc_html($datavalue);
                                            $comma = ', ';
                                        } else {
                                            esc_html($datavalue);
                                        }
                                        if ($data_flage != count($data)) {
                                            "";
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
                                        esc_html($data);
                                    } else {
                                        esc_html($data);
                                    }
                                }
                                //$custom_fields_list[$cus_field['label']] = $data;
                                $custom_fields_list[]   = array( 'label' => $cus_field['label'], 'data' => $data, 'icon' => isset( $cus_field['fontawsome_icon'] )? $cus_field['fontawsome_icon'] : '');

                                if (($custom_field_box % 3 == 0 && $custom_field_box > 0) && count($cs_job_cus_fields) != $custom_field_box)
                                    $custom_field_box ++;
                            }
                    }
                }

                if ($custom_field_box % 3 != 0 && $custom_field_box > 0)
                    "";
            }
            $post_job = get_post($job_id);
            $job_description = $post_job->post_content;
            $apply_btn = '<a class="active btn large like applied_icon ><i class="icon-briefcase4"></i> ' . esc_html__('Apply for this job', 'jobhunt') . '</a>';
            
            $jobs = [
                'company_logo' => esc_url($cs_employee_employer_img),
                'company_name' => esc_attr($username),
                'company_location' => esc_attr($job_address),
                'job_type' => esc_html($job_type),
                'job_posted_date' => date_i18n(get_option('date_format'), $cs_job_posted_date),
                'job_title' => esc_html(get_the_title($job_id)),
                'job_location' => esc_attr($job_address),
                'job_link' => esc_url(get_permalink($job_id)),
                'cust_fields_list' => $custom_fields_list,
                'job_description' => $job_description,
                'apply_job_btn' => $apply_btn,
            ];
            $jobs['template'] = 'html here';
            $jobs['next_api'] = '';
            $info = array('status' => true,
                'data' => $jobs);
            return new WP_REST_Response($info, 200);
        }

        /*
         * Favorite / Un-favorie Call
         */

        public function mark_favourite_callback() {
            //$requested_params = $req->get_params();
            $job_id = $_POST['job_id'];
            $user_id = $_POST['user_id'];
            
            
            $list_name = 'cs-user-jobs-wishlist';
            $current_timestamp = strtotime(current_time('d-m-Y H:i:s'));

            $existing_list_data = array();
            $existing_list_data = get_user_meta($user_id, $list_name, true);

            if (!is_array($existing_list_data)) {
                $existing_list_data = array();
            }
            if (is_array($existing_list_data)) {

                // search duplicat and remove it then arrange new ordering
                $finded = in_multiarray($job_id, $existing_list_data, 'post_id');
                $existing_list_data = $this->remove_index_from_array($existing_list_data, $finded);
                
                // adding one more entry
                $existing_list_data[] = array('post_id' => $job_id, 'date_time' => $current_timestamp);
                update_user_meta($user_id, $list_name, $existing_list_data);

                $info = array('status' => true,
                    'data' => 'Shortlised Successfully');
                return new WP_REST_Response($info, 200);
            } else {
                $info = array('status' => false,
                    'data' => 'Error');
                return new WP_REST_Response($info, 200);
            }
        }
        
        /*
         * Remove Shortlisted Job
         */
        public function remove_favourite_callback(){
            $job_id = $_POST['job_id'];
            $user_id = $_POST['user_id'];
            cs_remove_from_user_meta_list($job_id, 'cs-user-jobs-wishlist', $user_id);
            $info = array('status' => true,
                    'data' => 'Removed From Shortlised Successfully');
            return new WP_REST_Response($info, 200);
        }
        
        public function remove_index_from_array($array, $index_array) {
            $top = sizeof($index_array) - 1;
            $bottom = 0;
            if (is_array($index_array)) {
                while ($bottom <= $top) {
                    unset($array[$index_array[$bottom]]);
                    $bottom ++;
                }
            }
            if (!empty($array))
                return array_values($array);
            else
                return $array;
        }

        /*
         * Job Filters Call
         */

        public function job_filters_callback() {
            global $cs_form_fields2, $cs_plugin_options;
            $specialisms_dropdown = '';
            $job_type_dropdown = '';
            $selected_post_id = '';//job_id
            $search_keyword_field = '';
            $location_field = '';
            $all_specialisms_label = 'Specialisms';
            $specialisms_options = array();
            //$specialisms_options[''] = $all_specialisms_label;
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
                'return' => true,
                'extra_atr' => 'data-placeholder="' . $all_specialisms_label . '"',
            );

            $specialisms_dropdown .= $cs_form_fields2->cs_form_select_render($cs_opt_array);

            //jobtype dropdown
            // get all job types    
            $selected_slug = array();
             if ($selected_post_id != '') {
            // get all job types
            $all_job_type = get_the_terms($selected_post_id, 'job_type');
            $job_type_values = '';
            $job_type_class = '';
            $specialism_flag = 1;
            if ($all_job_type != '') {
                foreach ($all_job_type as $job_typeitem) {
                    $selected_slug = $job_typeitem->term_id;
                }
            }
        }
            $job_types_all_args = array(
                'orderby' => 'name',
                'order' => 'ASC',
                'fields' => 'all',
                'slug' => '',
                'hide_empty' => false,
            );
            $all_job_types = get_terms('job_type', $job_types_all_args);
            $select_options = array();
            if (isset($all_job_types) && is_array($all_job_types)) {
                foreach ($all_job_types as $job_typesitem) {
                    $select_options[$job_typesitem->term_id] = $job_typesitem->name;
                }
            }
            $cs_opt_array = array(
                'cust_id' => 'cs_job_types',
                'cust_name' => 'cs_job_types',
                'std' => $selected_slug,
                'desc' => '',
                'extra_atr' => 'data-placeholder="' . esc_html__("Please Select", "jobhunt") . '"',
                'classes' => 'chosen-select form-control',
                'options' => $select_options,
                'hint_text' => '',
                'return' => true,
                'required' => 'yes',
            );
            if (isset($required_status) && $required_status == 'true') {
                $cs_opt_array['required'] = 'yes';
            }
            $job_type_dropdown .= $cs_form_fields2->cs_form_select_render($cs_opt_array);

            $date_posted_field = '';

            $search_result_page_id = $cs_plugin_options['cs_search_result_page'];
            $submit_url = esc_url(get_permalink($search_result_page_id));
            
            $country_args = array(
                'orderby' => 'name',
                'order' => 'ASC',
                'fields' => 'all',
                'slug' => '',
                'hide_empty' => false,
                'parent' => 0,
            );
            $cs_location_countries = get_terms('cs_locations', $country_args);
            
            
            $form_fields['form_fields'] = [
                'search_keyword_field' => array('label' => 'Keyword Search', 'data' => $search_keyword_field),
                'location_field' => array('label' => 'Locations', 'data' => $location_field),
                'specialisms_dropdown' => array('label' => 'Specialisms', 'data' => $specialisms_options),
                'job_type_dropdown' => array('label' => 'Job Type', 'data' => $select_options),
                'date_posted_field' => array('label' => 'Date Posted', 'data' => $date_posted_field),
                'countries_list'    => $cs_location_countries,
                'submit_url' => $submit_url,
            ];
            $form = $form_fields;
            $info = array('status' => true,
                'data' => $form);
            return new WP_REST_Response($info, 200);
        }

        /*
         * User Account Settings Call
         */

        public function account_settings_callback($req) {
            $requested_params = $req->get_params();
            $user_id = $requested_params['user_id'];
            $name = $requested_params['name'];
            $last_name = $requested_params['last_name'];
            $email = $requested_params['email'];
            $phone_number = $requested_params['phone_number'];
            $profile_privacy = $requested_params['profile_privacy'];
            if (isset($user_id)) {
                $user_id = wp_update_user(array('ID' => $user_id, 'first_name' => $name, 'last_name' => $last_name, 'user_email' => $email));
                update_user_meta($user_id, 'first_name', $name);
                update_user_meta($user_id, 'last_name', $last_name);
                update_user_meta($user_id, 'user_email', $email);
                update_user_meta($user_id, 'cs_phone_number', $phone_number);
                update_user_meta($user_id, 'cs_profile_privacy', $profile_privacy);
                $status = true;
                $info_msg = array(
                    'info_msg' => 'Profile Update Successfully',
                    'userId' => $user_id
                );
            } else {
                $status = false;
                $info_msg = "Error";
            }
            $info = array('status' => $status,
                'data' => $info_msg);
            return new WP_REST_Response($info, 200);
        }

        /*
         * User Job Apply Call
         */

        public function job_apply_callback() {
            $user_id = isset( $_POST['user_id'] )? $_POST['user_id'] : 0;
            $job_id = isset( $_POST['job_id'] )? $_POST['job_id'] : 0;
            $cv = isset( $_POST['cv'] )? $_POST['cv'] : '';

            if (isset($user_id)) {
                $current_timestamp = strtotime(current_time('d-m-Y H:i:s'));
                $existing_list_data = array();
                $list_name = 'cs-user-jobs-applied-list';
                $existing_list_data = get_user_meta($user_id, $list_name, true);
                //updating user meta job applied array
                $cs_joblist = array();
                $cs_joblist = get_user_meta($user_id, 'cs-jobs-applied', true);
                if (!is_array($cs_joblist) && $cs_joblist == '') {
                    $cs_joblist = array();
                }
                $cs_joblist[] = $_POST['post_id'];
                $cs_joblist = array_unique($cs_joblist);
                update_user_meta($user_id, 'cs-jobs-applied', $cs_joblist);

                //updating user meta applied jobs list name
                if (!is_array($existing_list_data)) {
                    $existing_list_data = array();
                }
                if (is_array($existing_list_data)) {
                    // search duplicat and remove it then arrange new ordering
                    $finded = in_multiarray($job_id, $existing_list_data, 'post_id');
                    $existing_list_data = remove_index_from_array($existing_list_data, $finded);
                    // adding one more entry
                    $existing_list_data[] = array('post_id' => $job_id, 'date_time' => $current_timestamp);
                    update_user_meta($user_id, $list_name, $existing_list_data);
                    update_user_meta($user_id, 'cs_candidate_cv', $cv); //cv update
                }


                $status = true;
                $info_msg = array(
                    'info_msg' => 'Job Applied Successfully',
                    'userId' => $user_id
                );
            } else {
                $status = false;
                $info_msg = "Error";
            }
            $info = array('status' => $status,
                'data' => $info_msg);
            return new WP_REST_Response($info, 200);
        }

        /*
         * Discard / Undo Discard Call
         */

        public function job_discard_undo_discard_callback() {
            $requested_params = $req->get_params();
            $job_id = $requested_params['job_id'];
            $user_id = $requested_params['user_id'];
            $list_name = 'cs-user-jobs-dicard';
            $current_timestamp = strtotime(current_time('d-m-Y H:i:s'));

            $existing_list_data = array();
            $existing_list_data = get_user_meta($user_id, $list_name, true);

            if (!is_array($existing_list_data)) {
                $existing_list_data = array();
            }

            if (is_array($existing_list_data)) {

                // search duplicat and remove it then arrange new ordering
                $finded = in_multiarray($job_id, $existing_list_data, 'post_id');
                $status = in_array($finded, $existing_list_data) ? 'Un Discard' : 'Discard';

                $existing_list_data = $this->remove_index_from_array($existing_list_data, $finded);
                // adding one more entry
                $existing_list_data[] = array('post_id' => $job_id, 'date_time' => $current_timestamp);
                update_user_meta($user_id, $list_name, $existing_list_data);



                $info = array('status' => true,
                    'data' => $status . ' Successfully');
                return new WP_REST_Response($info, 200);
            } else {
                $info = array('status' => false,
                    'data' => 'Error');
                return new WP_REST_Response($info, 200);
            }

            function remove_index_from_array($array, $index_array) {
                $top = sizeof($index_array) - 1;
                $bottom = 0;
                if (is_array($index_array)) {
                    while ($bottom <= $top) {
                        unset($array[$index_array[$bottom]]);
                        $bottom ++;
                    }
                }
                if (!empty($array))
                    return array_values($array);
                else
                    return $array;
            }

        }
        
        /*
         * Getting Shortlisted Jobs by IDs
         */
        
        public function local_shortlisted_jobs_callback(){
            global $cs_plugin_options;
            $shortlisted_jobs = isset( $_GET['shortlisted_jobs'] )? $_GET['shortlisted_jobs'] :'';
            $shortlisted_jobs = explode( ',', $shortlisted_jobs);
            
            
            $filter_arr2[] = '';
            $post_ids = '';
            $current_timestamp = current_time('timestamp');
            
            $args = array(
                'posts_per_page' => "-1",
                'post_type' => 'jobs',
                'order' => 'DESC',
                'orderby' => array(
                    'meta_value' => 'DESC',
                    'post_date' => 'DESC',
                ),
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'meta_key' => 'cs_job_featured',
                'post__in' => $shortlisted_jobs,
                'fields' => 'ids', // only load ids
                'tax_query' => array(
                    'relation' => 'AND',
                    $filter_arr2
                ),
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
                    array(
                        'key' => 'cs_job_featured',
                        'compare' => 'EXISTS',
                        'type' => 'STRING'
                    ),
                ),
            );
            $loop = new WP_Query($args);
            $found_posts = $loop->found_posts;
            $output = '';
            // getting if record not found
            if ($loop->have_posts()) {
                $flag = 1;
                while ($loop->have_posts()) : $loop->the_post();
                    global $post;
                    $cs_job_id = $post;
                    $list_job_id = $cs_job_id;
                    $cs_job_posted = get_post_meta($cs_job_id, 'cs_job_posted', true);
                    $cs_jobs_address = get_user_address_string_for_list($cs_job_id);
                    $cs_jobs_thumb_url = '';
                    // get employer images at run time
                    $cs_job_employer = get_post_meta($cs_job_id, "cs_job_username", true); //
                    $cs_job_employer = cs_get_user_id_by_login($cs_job_employer);
                    $company_name = get_user_meta( $cs_job_employer, 'first_name', true);
                    $company_name   = $company_name.' '.get_user_meta( $cs_job_employer, 'last_name', true);
                    $employer_img = get_the_author_meta('user_img', $cs_job_employer);
                    
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
                    $output .= '<a href="/jobDetail/'. $cs_job_id .'" class="">';
                    if (isset($cs_job_featured) and $cs_job_featured == 'yes' || $cs_job_featured == 'on') {
                        $output .= '<span class="item-featured">Featured</span>';
                    }
                    $output .= '<div class="item-title">';
                    $output .= ' <h4>' . esc_html(get_the_title($cs_job_id)) . '</h4>';
                    $output .= '</div>';
                    $output .= '          </a>';
                    
                    $output .= '<div class="item-text">
                                        <a href="#">';
                    if ($cs_jobs_address <> '')
                        $output .= ' <div class="item-address">';
                    $output .= ' <span> ' . esc_html($cs_jobs_address) . '</span>';
                    $output .= '</div>';
                    $output .= '<div class="item-earning">';
                    $output .= '  <span>'. $company_name .'</span>';
                    $output .= ' <span>' . $job_type . '</span>';
                    $output .= '</div>';
                    $output .= ' </a>';
                    $output .= '<a href="#" class="item-remove-btn job-shortlist-remove-class" data-job_id="'. $cs_job_id .'">
                                    <i class="icon f7-icons">trash</i>
                                </a>';
                    $output .= '</div>';
                    $output .= '</div>';
                    $output .= '</div>';
                    $output .= '</li>';
                    $jobs[$cs_job_id] = ['job_id' => $cs_job_id, 'job_title' => esc_html(get_the_title($cs_job_id)), 'job_type' => esc_html($job_type), 'job_address' => esc_html($cs_jobs_address), 'job_link' => esc_url(get_permalink($cs_job_id))];

                    $flag ++;
                endwhile;
            }

            //$output .= '</ul>';
            $next_page = $page+1;
            $jobs['template'] = $output;
            $info = array('status' => true,
                'data' => $jobs);
            return new WP_REST_Response($info, 200);
        }
        
        /*
         * Getting Shortlisted Jobs by IDs
         */
        
        public function local_viewed_jobs_callback(){
            global $cs_plugin_options;
            $viewed_jobs = isset( $_GET['viewed_jobs'] )? $_GET['viewed_jobs'] :'';
            $viewed_jobs = explode( ',', $viewed_jobs);
            
            
            $filter_arr2[] = '';
            $post_ids = '';
            $current_timestamp = current_time('timestamp');
            
            $args = array(
                'posts_per_page' => "-1",
                'post_type' => 'jobs',
                'order' => 'DESC',
                'orderby' => array(
                    'meta_value' => 'DESC',
                    'post_date' => 'DESC',
                ),
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'meta_key' => 'cs_job_featured',
                'post__in' => $viewed_jobs,
                'fields' => 'ids', // only load ids
                'tax_query' => array(
                    'relation' => 'AND',
                    $filter_arr2
                ),
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
                    array(
                        'key' => 'cs_job_featured',
                        'compare' => 'EXISTS',
                        'type' => 'STRING'
                    ),
                ),
            );
            $loop = new WP_Query($args);
            $found_posts = $loop->found_posts;
            $output = '';
            // getting if record not found
            if ($loop->have_posts()) {
                $flag = 1;
                while ($loop->have_posts()) : $loop->the_post();
                    global $post;
                    $cs_job_id = $post;
                    $list_job_id = $cs_job_id;
                    $cs_job_posted = get_post_meta($cs_job_id, 'cs_job_posted', true);
                    $cs_jobs_address = get_user_address_string_for_list($cs_job_id);
                    $cs_jobs_thumb_url = '';
                    // get employer images at run time
                    $cs_job_employer = get_post_meta($cs_job_id, "cs_job_username", true); //
                    $cs_job_employer = cs_get_user_id_by_login($cs_job_employer);
                    $company_name = get_user_meta( $cs_job_employer, 'first_name', true);
                    $company_name   = $company_name.' '.get_user_meta( $cs_job_employer, 'last_name', true);
                    $employer_img = get_the_author_meta('user_img', $cs_job_employer);
                    
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
                    $output .= '<a href="/jobDetail/'. $cs_job_id .'" class="">';
                    if (isset($cs_job_featured) and $cs_job_featured == 'yes' || $cs_job_featured == 'on') {
                        $output .= '<span class="item-featured">Featured</span>';
                    }
                    $output .= '<div class="item-title">';
                    $output .= ' <h4>' . esc_html(get_the_title($cs_job_id)) . '</h4>';
                    $output .= '</div>';
                    $output .= '          </a>';
                    
                    $output .= '<div class="item-text">
                                        <a href="#">';
                    if ($cs_jobs_address <> ''){
                        $output .= ' <div class="item-address">';
                        $output .= ' <span> ' . esc_html($cs_jobs_address) . '</span>';
                        $output .= '</div>';
                    }
                    //$output .= '<div class="item-date"><span>19-08-2019</span></div>';
                    $output .= '<div class="item-earning">';
                    $output .= '  <span>'. $company_name .'</span>';
                    $output .= ' <span>' . $job_type . '</span>';
                    $output .= '</div>';
                    $output .= ' </a>';
                    $output .= '</div>';
                    $output .= '</div>';
                    $output .= '</div>';
                    $output .= '</li>';
                    $jobs[$cs_job_id] = ['job_id' => $cs_job_id, 'job_title' => esc_html(get_the_title($cs_job_id)), 'job_type' => esc_html($job_type), 'job_address' => esc_html($cs_jobs_address), 'job_link' => esc_url(get_permalink($cs_job_id))];

                    $flag ++;
                endwhile;
            }

            //$output .= '</ul>';
            $next_page = $page+1;
            $jobs['template'] = $output;
            $info = array('status' => true,
                'data' => $jobs);
            return new WP_REST_Response($info, 200);
        }

    }

}
$Job_Api = new Job_Api();


if (!function_exists('cs_get_user_id_by_login')) {

    function cs_get_user_id_by_login($login = '') {
        if ($login != '') {
            if (is_numeric($login)) {
                return $login;
            }
            $user_data = get_user_by('login', $login);
            return isset($user_data->ID) ? $user_data->ID : '';
        }
    }

}
