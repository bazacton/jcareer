<?php
header('Access-Control-Allow-Origin: *');  // no cabeçalho
if (!class_exists('Home_Api')) {

    class Home_Api extends WP_REST_Controller {

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
                    'request' => 'home_employers',
                    'methods' => 'GET',
                    'callback' => 'home_employers_callback',
                ),
                array(
                    'request' => 'featured_jobs',
                    'methods' => 'GET',
                    'callback' => 'featured_jobs_callback',
                ),
                array(
                    'request' => 'categories',
                    'methods' => 'GET',
                    'callback' => 'categories_callback',
                ),
                array(
                    'request' => 'latest_blog',
                    'methods' => 'GET',
                    'callback' => 'latest_blog_callback',
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
         * Employers List For Homepage
         */

        public function home_employers_callback() {
            global $wpdb, $cs_plugin_options, $cs_form_fields2;
            $cs_allow_in_search_user_switch = isset($cs_plugin_options['cs_allow_in_search_user_switch']) ? $cs_plugin_options['cs_allow_in_search_user_switch'] : '';
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
                    $country_slug = get_user_meta($cs_user->ID, 'cs_post_loc_country', true);
                    $city_slug = get_user_meta($cs_user->ID, 'cs_post_loc_city', true);
                    $countryObj = get_term_by('slug', $country_slug, 'cs_locations');
                    $cityObj = get_term_by('slug', $city_slug, 'cs_locations');
                    
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
                                    $specialism_html .= ' <li><a href="#">' . $specialismsitem->name . '</a></li>';
                                } else {
                                    $specialism_html .= ' <li><a href="#">' . $specialismsitem->name . '</a></li>';
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

                    
                    $output .= '<div class="swiper-slide">
                            <div class="item-content">
                                <div class="item-inner">
                                    <div class="vacancies">'. esc_html($count_job_post) .' Vacancies</div>
                                    <div class="image-holder ">
                                        <a href="/employerdetail/' . $cs_user->ID . '" class="">
                                            <figure>
                                                <img src="'. $cs_employee_employer_img .'" alt="" />
                                            </figure>
                                        </a>
                                    </div>
                                    <div class="item-title">
                                        <h4><a href="/employerdetail/' . $cs_user->ID . '" class="">' . esc_html($display_name) . '</a></h4>
                                    </div>
                                    <div class="item-text">
                                        <div class="item-address">
                                            <i class="icon f7-icons">placemark_fill</i><span> '. $cityObj->name . ', '. $countryObj->name .'</span>
                                        </div>
                                        <div class="item-tags">
                                            <ul>' . $specialism_html . '</ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
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
         * Featured Job Results Call
         */

        public function featured_jobs_callback() {
            global $cs_plugin_options;
            $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
            $cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';
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
                    array(
                        'key' => 'cs_job_featured',
                        'value' => 'yes',
                        'compare' => '='
                    ),
                ),
            );
            $loop = new WP_Query($args);
            $found_posts = $loop->found_posts;
            $output = '';
            //$output .= '<ul>';
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
                    $output .= '<a href="#" class="item-favorite job-shortlist-class-home" data-job_id="' . $cs_job_id . '">
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
                    $output .= '</li>';
                    $jobs[$cs_job_id] = ['job_id' => $cs_job_id, 'job_title' => esc_html(get_the_title($cs_job_id)), 'job_type' => esc_html($job_type), 'job_address' => esc_html($cs_jobs_address), 'job_link' => esc_url(get_permalink($cs_job_id)), 'company' => $company_name];

                    $flag ++;
                endwhile;
            }

            //$output .= '</ul>';
            $next_page = $page + 1;
            $jobs['template'] = $output;
            $jobs['next_api'] = site_url() . '/wp-json/api/v1/job_results?p=' . $next_page;
            $info = array('status' => true,
                'data' => $jobs);
            return new WP_REST_Response($info, 200);
        }

        /*
         * Get Categories
         */

        public function categories_callback() {
            global $wpdb;

            $qry = "SELECT * FROM $wpdb->terms 
                    LEFT JOIN $wpdb->term_taxonomy ON $wpdb->terms.term_id=$wpdb->term_taxonomy.term_id
                    WHERE 1=1 
                    AND $wpdb->term_taxonomy.taxonomy='specialisms' limit 9";
            $get_terms = $wpdb->get_col($qry);

            $term_data  = array();
            foreach ($get_terms as $term_id) {
                $cs_term = get_term_by('term_id', $term_id, 'specialisms');
                
                if (is_object($cs_term)) {
                    $cat_meta = get_term_meta($cs_term->term_id, 'spec_meta_data', true);
                    $cat_img = isset($cat_meta['img']) ? $cat_meta['img'] : '';
                    
                    $term_data[$term_id] = $cs_term;
                    $term_data[$term_id]->img = $cat_img;
                }
            }
            
            $info = array('status' => true,
                'data' => $term_data);
            return new WP_REST_Response($info, 200);
        }
        
        /*
         * Latest Blog
         */
        
        public function latest_blog_callback(){
            global $cs_blog_excerpt;
            $args = array( 'posts_per_page' => "3", 'post_type' => 'post', 'order' => 'DESC', 'orderby' => 'date', 'post_status' => 'publish', 'ignore_sticky_posts' => 1 );
            $query = new WP_Query($args);
            $post_count = $query->post_count;
            $output = '';
            $width = '350';
            $height = '210';
            if ($query->have_posts()) {
                $postCounter = 0;
                wp_reset_query();
                while ($query->have_posts()) : $query->the_post();
                    global $post;
                    $thumbnail = jobcareer_get_post_img_src($post->ID, $width, $height);
                    $num_comments = get_comments_number($post->ID); // get_comments_number returns only a numeric value
                    if ($num_comments > 1) {
                        $comment_string = esc_html__('comments', 'jobcareer');
                    } else {
                        $comment_string = esc_html__('comment', 'jobcareer');
                    }
                    $output .= '<div class="blog-post">
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="post-info">
                                    <span>'. get_the_date() .' - </span>
                                    <span>'. $num_comments .' '. $comment_string .'</span>
                                </div>
                                <div class="item-title">
                                    <h4><a href="/blogdetail/'. $post->ID .'" class="">'. get_the_title() .'</a></h4>
                                </div>
                                <div class="item-text">
                                    <p>'. jobcareer_get_excerpt(5, 'true', '') .'</p>
                                </div>
                            </div>
                        </div>
                        <div class="image-holder ">
                            <a href="/blogdetail/'. $post->ID .'" class="">
                                <figure>
                                    <img src="'. esc_url($thumbnail) .'" alt="" />
                                </figure>
                            </a>
                        </div>
                    </div>';
                    
                endwhile;
            }
            
            $info = array('status' => true,
                'data' => $output);
            return new WP_REST_Response($info, 200);
        }

    }

}
$Home_Api = new Home_Api();
