<?php
/**
 * Jobs list view
 *
 */
global $wpdb, $cs_plugin_options;
$main_col = '';
if ($a['cs_job_searchbox'] == 'yes') {
    $main_col = 'col-lg-9 col-md-9 col-sm-12 col-xs-12';
}
$cs_emp_funs = new cs_employer_functions();
$cs_search_result_page = isset($cs_plugin_options['cs_search_result_page']) ? $cs_plugin_options['cs_search_result_page'] : '';
$args = apply_filters('jobhunt_jobs_args', $args);
$loops = new WP_Query($args);
$loop = $loops;
$loop = apply_filters('jobhunt_featured_jobs_frontend', $loop, $args);
$found_posts = $loops->found_posts;
$found_posts = apply_filters('jobhunt_feature_count_post', $found_posts);
$count_post = $found_posts;

$job_type_args = array(
    'orderby' => 'name',
    'order' => 'ASC',
    'fields' => 'all',
    'slug' => '',
    'hide_empty' => false,
    'parent' => 0,
);

$all_job_types = get_terms('job_type', $job_type_args);

$jobs_list = array();
if ($loop->have_posts()) {
    while ($loop->have_posts()) : $loop->the_post();
        global $post;


        $cs_job_id = $post;
        $cs_job_posted = get_post_meta($cs_job_id, 'cs_job_posted', true);
        $cs_jobs_address = get_user_address_string_for_list($cs_job_id);
        $cs_jobs_thumb_url = '';
// get employer images at run time
        $cs_job_employer = get_post_meta($cs_job_id, "cs_job_username", true); //
        $cs_job_employer = cs_get_user_id_by_login($cs_job_employer);
        $employer_img = get_the_author_meta('user_img', $cs_job_employer);
        if ($employer_img != '') {
            $cs_jobs_thumb_url = cs_get_img_url($employer_img, 'cs_media_5');
        }
        $employer_title = '';
        $cs_user = get_userdata($cs_job_employer);
        if (isset($cs_user->display_name)) {
            $employer_title = $cs_user->display_name;
        }

        $cs_jobs_thumb_url = apply_filters('digitalmarketing_job_image', $cs_jobs_thumb_url, $cs_job_id);
        if (!cs_image_exist($cs_jobs_thumb_url) || $cs_jobs_thumb_url == "") {
            $cs_jobs_thumb_url = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
        }

        $cs_jobs_address = apply_filters('jobhunt_job_address_frontend', $cs_jobs_address, $cs_job_id);
        $categories = get_the_terms($post, 'job_type');
        $jobs_list[$categories[0]->slug][] = array(
            'image_url' => $cs_jobs_thumb_url,
            'title' => get_the_title($cs_job_id),
            'company_title' => $employer_title,
            'location' => $cs_jobs_address,
            'posted_date' => cs_time_elapsed_string($cs_job_posted),
            'job_id' => $cs_job_id,
        );

    endwhile;
}
$cs_rand_id_ajax = rand(34563, 34323990);
?>

<div class="<?php echo cs_allow_special_char($main_col); ?>">

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="cs-heading" style="text-align: left; margin-bottom: 15px;">
                <h2 style="margin-bottom: 0; font-size: 30px; color: #000; font-weight: 600; line-height: 36px;"><?php echo esc_html__("Find a good job", 'jobcareer'); ?></h2>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="listing-tabs-holvder">
                <div class="nav-tabs">
                    <ul>
                        <li class="active">
                            <?php
                            echo '<a href="javascript:void(0)" onClick="get_jobs_of_type(jQuery(this),\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'all\', \'' . $cs_rand_id_ajax . '\', \'' . ($a['cs_job_pagination']) . '\')" data-toggle="tab"> ' . esc_html__("All Jobs", 'jobcareer') . '</a>';
                            ?>
                        </li>
                        <?php
                        if (!empty($all_job_types)) {
                            foreach ($all_job_types as $job_type_obj) {
                                echo '<li>
                                        <a href="javascript:void(0)" onClick="get_jobs_of_type(jQuery(this),\'' . esc_js(admin_url('admin-ajax.php')) . '\', \'' . $job_type_obj->slug . '\', \'' . $cs_rand_id_ajax . '\', \'' . ($a['cs_job_pagination']) . '\')" aria-controls="' . $job_type_obj->slug . '" data-toggle="tab">' . $job_type_obj->name . '</a>
                                    </li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="cs-jobs-holder jobee-listing">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade in active" id="all_jobs">
                        <div class="jobs-listing" id="jobs-listing-content-<?php echo $cs_rand_id_ajax; ?>">
                            <div class="main-cs-loader"></div>
                            <ul>
                                <?php
                                foreach ($jobs_list as $key => $jobData) {
                                    if (!empty($jobData)) {
                                        foreach ($jobData as $jobData_row) {
                                            $cs_jobs_thumb_url = isset($jobData_row['image_url']) ? $jobData_row['image_url'] : '';
                                            $job_title = isset($jobData_row['title']) ? $jobData_row['title'] : '';
                                            $employer_title = isset($jobData_row['company_title']) ? $jobData_row['company_title'] : '';
                                            $cs_jobs_address = isset($jobData_row['location']) ? $jobData_row['location'] : '';
                                            $cs_job_posted = isset($jobData_row['posted_date']) ? $jobData_row['posted_date'] : '';
                                            $cs_job_id = isset($jobData_row['job_id']) ? $jobData_row['job_id'] : '';
                                            ?>
                                            <li> 
                                                <div class="jobs-content">
                                                    <div class="cs-media">
                                                        <?php if ($cs_jobs_thumb_url <> '') { ?>
                                                            <figure><a href="<?php echo esc_url(get_permalink($cs_job_id)); ?>"><img alt="" src="<?php echo esc_url($cs_jobs_thumb_url); ?>"></a></figure>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="cs-text">
                                                        <div class="content-holder">
                                                            <div class="post-title">
                                                                <h5><a href="<?php echo esc_url(get_permalink($cs_job_id)); ?>"><?php echo esc_html($job_title); ?></a></h5>
                                                                <span>
                                                                    <?php echo esc_html($employer_title); ?>
                                                                </span>
                                                            </div>
                                                            <div class="post-options"> 
                                                                <?php $jobs_locations_enabled = apply_filters('jobhunt_jobs_locations_enabled', 'yes');
                                                                    if( $jobs_locations_enabled == 'yes'){
                                                                        if ($cs_jobs_address <> '') { ?> 
                                                                    <span class="cs-location">
                                                                        <i class="icon-map-marker"></i>
                                                                        <?php echo esc_html($cs_jobs_address); ?> 
                                                                    </span>
                                                                    <?php } } ?>
                                                            </div>
                                                            <div class="cs-post-time">
                                                                <span><?php echo esc_html($cs_job_posted); ?></span>
                                                            </div>
                                                            <div class="job-post">
                                                                <?php
                                                                /*
                                                                 * Apply now functionality
                                                                 */
                                                                $user = cs_get_user_id();
                                                                $user_can_apply = 'on';
                                                                $user_can_apply = apply_filters('jobhunt_candidate_can_apply', $user_can_apply);
                                                                if ($user_can_apply == 'on') {

                                                                    $class_apply = '';
                                                                    if (isset($_SESSION['apply_job_id'])) {
                                                                        $class_apply = 'applyauto';
                                                                        unset($_SESSION['apply_job_id']);
                                                                    }
                                                                    $cs_without_login_switch = isset($cs_plugin_options['cs_without_login_switch']) && !empty($cs_plugin_options['cs_without_login_switch']) ? $cs_plugin_options['cs_without_login_switch'] : '';
                                                                    if ($cs_without_login_switch == '') {
                                                                        $cs_without_login_switch = 'yes';
                                                                    }
                                                                    if (is_user_logged_in()) {
                                                                        $user = cs_get_user_id();
                                                                        //update_user_meta($user, 'cs-user-jobs-applied-list', $user);
                                                                        $user_role = cs_get_loginuser_role();
                                                                        if (isset($user_role) && $user_role <> '' && $user_role == 'cs_candidate') {
                                                                            $cs_applied_list = array();
                                                                            if (isset($user) and $user <> '' and is_user_logged_in()) {
                                                                                $finded_result_list = cs_find_index_user_meta_list($cs_job_id, 'cs-user-jobs-applied-list', 'post_id', cs_get_user_id());
                                                                                if (is_array($finded_result_list) && !empty($finded_result_list)) {
                                                                                    ?>
                                                                                    <a href="javascript:void(0);" class="jobtype-btn cs-color cs-br-color" >
                                                                                        <?php esc_html_e('Applied', 'jobhunt') ?>
                                                                                    </a>
                                                                                    <?php
                                                                                } else {
                                                                                    ?>
                                                                                    <a class="jobtype-btn cs-color cs-br-color <?php echo $class_apply; ?>" href="javascript:void(0);" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($cs_job_id); ?>', this, 'modern-v1', '<?php echo $cs_without_login_switch; ?>')" >
                                                                                        <?php esc_html_e('Apply', 'jobhunt') ?>
                                                                                    </a>
                                                                                    <?php
                                                                                }
                                                                            } else {
                                                                                ?>
                                                                                <a class="jobtype-btn cs-color cs-br-color <?php echo $class_apply; ?>" href="javascript:void(0);" onclick="cs_addjobs_left_to_applied('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', '<?php echo intval($cs_job_id); ?>', this, 'modern-v1'), '<?php echo $cs_without_login_switch; ?>')" >
                                                                                    <?php esc_html_e('Apply', 'jobhunt') ?>
                                                                                </a>	
                                                                                <?php
                                                                            }
                                                                        }
                                                                    } else {
                                                                        $cs_rand_id = rand(34563, 34323990);
                                                                        ?>
                                                                        <a href="javascript:void(0);" class="jobtype-btn cs-color cs-br-color    " onclick="trigger_func('#btn-header-main-login');"> 
                                                                            <?php esc_html_e('Apply', 'jobhunt') ?></a>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>



                                                </div>
                                            </li>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </ul>

                        </div>
                    </div>
                </div>
                <?php if ($cs_search_result_page != '') { ?>
                    <div class="list-pagination-holder">
                        <div class="btn-holder">
                            <a href="<?php echo esc_url_raw(get_page_link($cs_search_result_page)); ?>" class="jobs-btn"> <?php esc_html_e('See All Jobs', 'jobhunt') ?>
                                <i class="icon-arrow-right11"></i>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
//Pagination Start
    if ($found_posts > $cs_blog_num_post && $cs_blog_num_post > 0 && $a['cs_job_show_pagination'] == "pagination") {
        echo '<nav>';
        echo cs_pagination($found_posts, $cs_blog_num_post, $qrystr, 'Show Pagination', 'page_job');
        echo '</nav>';
    }//Pagination End 
    ?>
</div>
