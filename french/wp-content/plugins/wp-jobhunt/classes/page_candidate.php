<?php

/**
 * File Type: Candidate
 */
function cs_candidate_popup_style() {
    wp_enqueue_style('custom-candidate-style-inline', plugins_url('../assets/css/custom_script.css', __FILE__));
    $cs_plugin_options = get_option('cs_plugin_options');
    $cs_custom_css = '#id_confrmdiv
    {
        display: none;
        background-color: #eee;
        border-radius: 5px;
        border: 1px solid #aaa;
        position: fixed;
        width: 300px;
        left: 50%;
        margin-left: -150px;
        padding: 6px 8px 8px;
        box-sizing: border-box;
        text-align: center;
    }
    #id_confrmdiv .button {
        background-color: #ccc;
        display: inline-block;
        border-radius: 3px;
        border: 1px solid #aaa;
        padding: 2px;
        text-align: center;
        width: 80px;
        cursor: pointer;
    }
    #id_confrmdiv .button:hover
    {
        background-color: #ddd;
    }
    #confirmBox .message
    {
        text-align: left;
        margin-bottom: 8px;
    }';
    wp_add_inline_style('custom-candidate-style-inline', $cs_custom_css);
}

add_action('wp_enqueue_scripts', 'cs_candidate_popup_style', 5);
get_header();



cs_datetime_picker_scripts();   // date time picker scripts
?><!-- alert for complete theme --><div class="cs_alerts" ></div>
<?php
global $post, $current_user, $wp_roles, $userdata, $cs_plugin_options, $cs_form_fields2;
//global $post, $current_user, $cs_form_fields2, $cs_theme_fields, $cs_form_fields_frontend, $cs_plugin_options;
if (class_exists('cs_employer_functions')) {
    $cs_emp_funs = new cs_employer_functions();
}
$uid = $current_user->ID;
if (isset($_GET['uid']) && $_GET['uid'] <> '') {
    $uid = $_GET['uid'];
}
$cs_user_data = get_userdata($uid);
if ($cs_user_data != '') {
    $cs_display_name = $cs_user_data->display_name;
}
$cs_job_title = get_user_meta($uid, 'cs_job_title', true);
$cs_value = get_user_meta($uid, 'user_img', true);
$cs_cover_candidate_img_value = get_user_meta($uid, 'cover_user_img', true);
$imagename_only = $cs_value;
$action = isset($_POST['button_action']) ? $_POST['button_action'] : '';
$post_title = isset($_POST['post_title']) ? $_POST['post_title'] : '';
$post_content = isset($_POST['candidate_content']) ? $_POST['candidate_content'] : '';
$post_author = $uid;
$cs_post_id = cs_candidate_post_id($uid);
// create candidate post
$candidate_post = array(
    'ID' => $cs_post_id,
    'post_title' => $post_title,
    'post_content' => $post_content,
    'post_author' => $post_author,
    'post_type' => 'candidate',
    'post_date' => current_time('Y-m-d h:i:s')
);
if (isset($cs_post_id) and $cs_post_id <> '' and $action == 'update') {
    wp_update_post($candidate_post);
}
if (is_user_logged_in()) {
    global $current_user;
    $cs_candidate_dashboard = isset($cs_plugin_options['cs_js_dashboard']) ? $cs_plugin_options['cs_js_dashboard'] : '';
    $cs_candidate_dashboard_vew = isset($cs_plugin_options['cs_candidate_dashboard_view']) ? $cs_plugin_options['cs_candidate_dashboard_view'] : 'default';
    if ($cs_candidate_dashboard_vew == '') {
        $cs_candidate_dashboard_vew = 'default';
    }
    if ($cs_candidate_dashboard != '') {
        $cs_candidate_dashboard = get_permalink($cs_candidate_dashboard);
    }
}
$uid = $current_user->ID;
if (isset($_GET['uid']) && $_GET['uid'] <> '') {
    $uid = $_GET['uid'];
}
$cs_pkg_array = $cs_blnk_array = array();

$cs_pkg_array['ajax_url'] = esc_url(admin_url('admin-ajax.php'));
$cs_pkg_array['user_id'] = $uid;
$cs_pkg_array['post_array'] = isset($_POST) ? $_POST : '';

if (is_array($cs_pkg_array) && sizeof($cs_pkg_array) > 0) {
    $cs_pkg_array = json_encode($cs_pkg_array, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
}
update_option('wooC_current_page', cs_server_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
?>
<script type="text/javascript">
    var pkg_array = '<?php echo CS_FUNCTIONS()->cs_special_chars($cs_pkg_array) ?>';
    var autocomplete;
</script>

<div class="main-section">
    <div class="content-area" id="primary">
        <main class="site-main">
            <div class="post-1 post type-post status-publish format-standard hentry category-uncategorized">
                <?php
                $cs_emp_funs->cs_init_editor();
                if (is_user_logged_in()) {
                    $user_role = cs_get_loginuser_role();
                    if (isset($user_role) && $user_role <> '' && $user_role == 'cs_candidate') {
                        global $cs_form_fields2;

                        $plugon_active = false;
                        $plugon_active = apply_filters('jobhunt_lucasdemoncuit_depedency', $plugon_active);
                        ?>
                        <div id="main">
                            <div class="main-section cs-jax-area" data-ajaxurl="<?php echo esc_js(admin_url('admin-ajax.php')); ?>">
                                <?php if (isset($cs_candidate_dashboard_vew) && $cs_candidate_dashboard_vew == 'default') { ?>
                                    <section class="dasborad">
                                        <div class="<?php if (isset($cs_plugin_options['cs_plugin_single_container']) && $cs_plugin_options['cs_plugin_single_container'] == 'on') echo 'container' ?>">
                                            <div class="row">
                                                <div class="cs-content-holder">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                                                        <div class="cs-tabs nav-position-left row" id="cstabs">
                                                            <?php cs_profile_menu($action, $uid); ?>
                                                            <div class="tab-content col-lg-9 col-md-9 col-sm-12 col-xs-12 " id="candidate-dashboard" data-validationmsg="<?php esc_html_e("Please ensure that all required fields are completed and formatted correctly", "jobhunt"); ?>">
                                                                <!-- warning popup -->
                                                                <!--                                                            <div id="id_confrmdiv">
                                                                                                                                <div class="cs-confirm-container">
                                                                                                                                    <i class="icon-exclamation2"></i>
                                                                                                                                    <div class="message"><?php //esc_html_e("Do you really want to delete?", "jobhunt");         ?></div>
                                                                                                                                    <a href="javascript:void(0);" id="id_truebtn"><?php //esc_html_e("Yes Delete It", "jobhunt");         ?></a>
                                                                                                                                    <a href="javascript:void(0);" id="id_falsebtn"><?php //esc_html_e("Cancel", "jobhunt");         ?></a>
                                                                                                                                </div>
                                                                                                                            </div>-->
                                                                <!-- end warning popup -->
                                                                <div class="main-cs-loader"></div>
                                                                <?php if (!$plugon_active) { ?>
                                                                    <div class="tab-pane <?php if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'profile') || (!isset($_REQUEST['profile_tab']) || $_REQUEST['profile_tab'] == '')) echo 'active'; ?> fade1 tabs-container" id="profile">
                                                                        <div class="cs-loader"></div>
                                                                        <?php
                                                                        $cs_jobhunt = new wp_jobhunt();
                                                                        $cs_jobhunt->cs_location_gmap_script();
                                                                        $cs_jobhunt->cs_google_place_scripts();
                                                                        $cs_jobhunt->cs_autocomplete_scripts();
                                                                        if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'profile') || (!isset($_REQUEST['profile_tab']) || $_REQUEST['profile_tab'] == '')) {
                                                                            ?>
                                                                            <script>
                                                                                jQuery(window).load(function () {
                                                                                    cs_ajax_candidate_profile('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');
                                                                                });
                                                                            </script>
                                                                            <?php
                                                                        } else {
                                                                            $cs_opt_array = array(
                                                                                'id' => '',
                                                                                'std' => '',
                                                                                'cust_id' => "cs_candidate_img",
                                                                                'cust_name' => "media_img",
                                                                            );

                                                                            $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                                                                            ?>
                                                                            <label class="browse-icon">
                                                                                <?php
                                                                                $cs_opt_array = array(
                                                                                    'id' => '',
                                                                                    'std' => '',
                                                                                    'cust_id' => "",
                                                                                    'cust_name' => "media_upload",
                                                                                    'cust_type' => 'file',
                                                                                    'extra_atr' => '',
                                                                                    'classes' => 'upload cs-uploadimgjobseek',
                                                                                );
                                                                                $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                                                                ?></label>				
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                <?php } ?>
                                                                <div class="tab-pane <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'resume') echo 'active'; ?> fade1 tabs-container" id="resume">
                                                                    <div class="cs-loader"></div>
                                                                    <div id="main_resume_content">
                                                                        <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'resume') { ?>
                                                                            <script>
                                                                                jQuery(window).load(function () {
                                                                                    cs_ajax_candidate_resume('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');
                                                                                });
                                                                            </script>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane <?php if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'shortlisted-jobs') || (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'shortlisted_jobs')) echo 'active'; ?> fade1 tabs-container" id="shortlisted-job">
                                                                    <div class="cs-loader"></div>
                                                                    <?php if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'shortlisted-jobs') || (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'shortlisted_jobs')) { ?>
                                                                        <script>
                                                                            jQuery(window).load(function () {
                                                                                cs_ajax_candidate_favjobs('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');
                                                                            });
                                                                        </script>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </div>  
                                                                <?php if (!$plugon_active) { ?>
                                                                    <div class="tab-pane <?php if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'applied-jobs') || (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'applied_jobs')) echo 'active'; ?> fade1 tabs-container" id="applied-jobs">
                                                                        <div class="cs-loader"></div>
                                                                        <?php if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'applied-jobs') || (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'applied_jobs')) { ?>
                                                                            <script>
                                                                                jQuery(window).load(function () {
                                                                                    cs_ajax_candidate_appliedjobs('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');
                                                                                });
                                                                            </script>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </div> 
                                                                    <div class="tab-pane <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'cv') echo 'active'; ?> fade1 tabs-container" id="cv">
                                                                        <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'cv') { ?>
                                                                            <script>
                                                                                jQuery(window).load(function () {
                                                                                    cs_ajax_candidate_cvcover('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');
                                                                                });
                                                                            </script>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </div>
                                                                    <?php
                                                                }
                                                                $profile_tab = isset($_REQUEST['profile_tab']) ? $_REQUEST['profile_tab'] : '';
                                                                do_action('jobhunt_candidate_dashboard_tabs', $profile_tab, $uid);
                                                                ?>
                                                                <div class="tab-pane <?php if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'packages') || (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'packages')) echo 'active'; ?> fade1 tabs-container" id="packages">
                                                                    <div class="cs-loader"></div>
                                                                    <?php if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'packages') || (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'packages')) { ?>
                                                                        <script>
                                                                            jQuery(window).load(function () {
                                                                                cs_ajax_candidate_membership_packages(pkg_array);
                                                                            });
                                                                        </script>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <?php do_action('background_checks_tabsfront_candidate_dashboard',$profile_tab, $uid, $cs_pkg_array); ?>
                                                                <div class="tab-pane <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'change_password') echo 'active'; ?> fade1 tabs-container" id="change_password">
                                                                    <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'change_password') { ?>
                                                                        <script>
                                                                            jQuery(window).load(function () {
                                                                                cs_candidate_change_password('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');
                                                                            });
                                                                        </script>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </div> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                <?php } ?>
                                <?php if (isset($cs_candidate_dashboard_vew) && $cs_candidate_dashboard_vew == 'fancy' || $cs_candidate_dashboard_vew == 'fancy_full') { ?>

                                    <?php if (isset($cs_candidate_dashboard_vew) && $cs_candidate_dashboard_vew == 'fancy') { ?>
                                        <section class="dasborad dasborad-fancy">
                                        <?php } elseif (isset($cs_candidate_dashboard_vew) && $cs_candidate_dashboard_vew == 'fancy_full') { ?>
                                            <section class="dasborad dasborad-fancy fancy-full">
                                                <div class="cs-upload-sec">
                                                    <div class="container">
                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                <div class="cs-profile-holder">  
                                                                    <div class="cs-img-detail">
                                                                        <div class="alert alert-dismissible user-img"> 
                                                                            <div class="page-wrap" id="cs_user_img_box">
                                                                                <div class="upload-btn-div">
                                                                                    <div class="fileUpload uplaod-btn btn cs-color csborder-color">
                                                                                        <i class="icon-camera6"></i>
                                                                                        <?php
                                                                                        $cs_opt_array = array(
                                                                                            'std' => $imagename_only,
                                                                                            'cust_id' => 'cs_user_img',
                                                                                            'cust_name' => 'media_img',
                                                                                            'cust_type' => 'hidden',
                                                                                        );
                                                                                        $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                                                                        ?>
                                                                                        <label class="browse-icon">
                                                                                            <?php
                                                                                            $cs_opt_array = array(
                                                                                                'std' => esc_html__('Browse', 'jobhunt'),
                                                                                                'cust_id' => 'cs_media_upload',
                                                                                                'cust_name' => 'media_upload',
                                                                                                'cust_type' => 'file',
                                                                                                'classes' => 'upload cs-uploadimgjobseek',
                                                                                            );
                                                                                            $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                                                                            ?>
                                                                                        </label>				
                                                                                    </div>
                                                                                    <br />
                                                                                    <span id="cs_candidate_profile_img_msg" style="display:none;"><?php esc_html_e('Max file size is 1MB, Minimum dimension: 270x210 And Suitable files are .jpg & .png', 'jobhunt'); ?></span>
                                                                                </div>
                                                                                <figure>
                                                                                    <?php
                                                                                    if ($cs_value <> '') {
                                                                                        $cs_value = cs_get_image_url($cs_value, '');
                                                                                        ?>
                                                                                        <img src="<?php echo esc_url($cs_value); ?>" id="cs_user_img_img" width="100" alt="" />

                                                                                    <?php } else { ?>
                                                                                        <img src="<?php echo esc_url($cs_jobhunt->plugin_url()); ?>assets/images/upload-img.jpg" id="cs_user_img_img" width="100" alt="" />
                                                                                        <?php
                                                                                    }
                                                                                    ?>
                                                                                </figure>
                                                                            </div>
                                                                        </div>
                                                                        <div class="user-info">
                                                                            <?php if ($cs_display_name != '') { ?>
                                                                                <h5 class="cs-candidate-title"><span><?php printf(esc_html__('%s', 'jobhunt'), esc_html($cs_display_name)) ?></span></h5>
                                                                            <?php } ?>
                                                                            <span><em><?php echo esc_html__($cs_job_title, 'jobhunt'); ?></em></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="cs-img-detail">
                                                        <div class="cs-cover-img">
                                                            <div class="alert alert-dismissible user-img"> 
                                                                <div class="page-wrap" id="cs_cover_candidate_img_box">
                                                                    <figure>
                                                                        <?php
                                                                        if ($cs_cover_candidate_img_value <> '') {

                                                                            $cs_cover_candidate_img_value = cs_get_img_url($cs_cover_candidate_img_value, 'cs_media_0');
                                                                            ?>
                                                                            <img src="<?php echo esc_url($cs_cover_candidate_img_value); ?>" id="cs_cover_candidate_img_img" width="100" alt="" />

                                                                        <?php } else { ?>
                                                                            <?php if (isset($cs_candidate_dashboard_vew) && $cs_candidate_dashboard_vew == 'fancy') { ?>
                                                                                <img src="<?php echo esc_url($cs_jobhunt->plugin_url()); ?>assets/images/no-cover.jpg" id="cs_cover_candidate_img_img" width="100" alt="" />
                                                                            <?php } else {
                                                                                ?>
                                                                                <img src="<?php echo esc_url($cs_jobhunt->plugin_url()); ?>assets/images/no-cover-full.jpg" id="cs_cover_candidate_img_img" width="100" alt="" />
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </figure>
                                                                </div>
                                                            </div>

                                                            <div class="upload-btn-div">
                                                                <div class="fileUpload uplaod-btn btn cs-color csborder-color">
                                                                    <span class="cs-color"><?php esc_html_e('Browse Cover', 'jobhunt'); ?></span>
                                                                    <i class="icon-camera6"></i>
                                                                    <?php
                                                                    $cs_opt_array = array(
                                                                        'std' => $cs_cover_candidate_img_value,
                                                                        'id' => '',
                                                                        'return' => true,
                                                                        'cust_id' => 'cs_cover_candidate_img',
                                                                        'cust_name' => 'cs_cover_candidate_img',
                                                                        'prefix' => '',
                                                                    );
                                                                    echo force_balance_tags($cs_form_fields2->cs_form_hidden_render($cs_opt_array));
                                                                    $cs_opt_array = array(
                                                                        'std' => esc_html__('Browse Cover', 'jobhunt'),
                                                                        'id' => '',
                                                                        'return' => true,
                                                                        'force_std' => true,
                                                                        'cust_id' => '',
                                                                        'cust_name' => 'cand_cover_media_upload',
                                                                        'classes' => 'left cs-candi-cover-uploadimg upload',
                                                                        'cust_type' => 'file',
                                                                    );
                                                                    echo force_balance_tags($cs_form_fields2->cs_form_text_render($cs_opt_array));
                                                                    ?>
                                                                </div>
                                                                <br />
                                                                <span id="cs_candidate_profile_cover_msg" style="display:none;"><?php esc_html_e('Max file size is 1MB, Minimum dimension: 1600x400 And Suitable files are .jpg & .png', 'jobhunt'); ?></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            <?php } ?>
                                            <div class="<?php if (isset($cs_plugin_options['cs_plugin_single_container']) && $cs_plugin_options['cs_plugin_single_container'] == 'on') echo 'container' ?>">
                                                <div class="row">
                                                    <div class="cs-content-holder">
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
                                                            <div class="cs-tabs nav-position-left row" id="cstabs">
                                                                <?php cs_profile_menu($action, $uid); ?>
                                                                <div class="tab-content col-lg-8 col-md-8 col-sm-12 col-xs-12 " id="candidate-dashboard" data-validationmsg="<?php esc_html_e("Please ensure that all required fields are completed and formatted correctly", "jobhunt"); ?>">
                                                                    <!-- warning popup -->
                                                                    <!--                                                            <div id="id_confrmdiv">
                                                                                                                                    <div class="cs-confirm-container">
                                                                                                                                        <i class="icon-exclamation2"></i>
                                                                                                                                        <div class="message"><?php //esc_html_e("Do you really want to delete?", "jobhunt");         ?></div>
                                                                                                                                        <a href="javascript:void(0);" id="id_truebtn"><?php //esc_html_e("Yes Delete It", "jobhunt");         ?></a>
                                                                                                                                        <a href="javascript:void(0);" id="id_falsebtn"><?php //esc_html_e("Cancel", "jobhunt");         ?></a>
                                                                                                                                    </div>
                                                                                                                                </div>-->
                                                                    <!-- end warning popup -->
                                                                    <div class="main-cs-loader"></div>
                                                                    <?php if (!$plugon_active) { ?>
                                                                        <div class="tab-pane <?php if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'profile') || (!isset($_REQUEST['profile_tab']) || $_REQUEST['profile_tab'] == '')) echo 'active'; ?> fade1 tabs-container" id="profile">
                                                                            <div class="cs-loader"></div>
                                                                            <?php
                                                                            $cs_jobhunt = new wp_jobhunt();
                                                                            $cs_jobhunt->cs_location_gmap_script();
                                                                            $cs_jobhunt->cs_google_place_scripts();
                                                                            $cs_jobhunt->cs_autocomplete_scripts();
                                                                            if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'profile') || (!isset($_REQUEST['profile_tab']) || $_REQUEST['profile_tab'] == '')) {
                                                                                ?>
                                                                                <script>
                                                                                    jQuery(window).load(function () {
                                                                                        cs_ajax_candidate_profile('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');
                                                                                    });
                                                                                </script>
                                                                                <?php
                                                                            } else {
                                                                                $cs_opt_array = array(
                                                                                    'id' => '',
                                                                                    'std' => '',
                                                                                    'cust_id' => "cs_candidate_img",
                                                                                    'cust_name' => "media_img",
                                                                                );

                                                                                $cs_form_fields2->cs_form_hidden_render($cs_opt_array);
                                                                                ?>
                                                                                <label class="browse-icon">
                                                                                    <?php
                                                                                    $cs_opt_array = array(
                                                                                        'id' => '',
                                                                                        'std' => '',
                                                                                        'cust_id' => "",
                                                                                        'cust_name' => "media_upload",
                                                                                        'cust_type' => 'file',
                                                                                        'extra_atr' => '',
                                                                                        'classes' => 'upload cs-uploadimgjobseek',
                                                                                    );
                                                                                    $cs_form_fields2->cs_form_text_render($cs_opt_array);
                                                                                    ?></label>				
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    <?php } ?>
                                                                    <div class="tab-pane <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'resume') echo 'active'; ?> fade1 tabs-container" id="resume">
                                                                        <div class="cs-loader"></div>
                                                                        <div id="main_resume_content">
                                                                            <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'resume') { ?>
                                                                                <script>
                                                                                    jQuery(window).load(function () {
                                                                                        cs_ajax_candidate_resume('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');
                                                                                    });
                                                                                </script>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="tab-pane <?php if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'shortlisted-jobs') || (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'shortlisted_jobs')) echo 'active'; ?> fade1 tabs-container" id="shortlisted-job">
                                                                        <div class="cs-loader"></div>
                                                                        <?php if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'shortlisted-jobs') || (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'shortlisted_jobs')) { ?>
                                                                            <script>
                                                                                jQuery(window).load(function () {
                                                                                    cs_ajax_candidate_favjobs('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');
                                                                                });
                                                                            </script>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </div>  
                                                                    <?php if (!$plugon_active) { ?>
                                                                        <div class="tab-pane <?php if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'applied-jobs') || (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'applied_jobs')) echo 'active'; ?> fade1 tabs-container" id="applied-jobs">
                                                                            <div class="cs-loader"></div>
                                                                            <?php if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'applied-jobs') || (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'applied_jobs')) { ?>
                                                                                <script>
                                                                                    jQuery(window).load(function () {
                                                                                        cs_ajax_candidate_appliedjobs('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');
                                                                                    });
                                                                                </script>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </div> 
                                                                        <div class="tab-pane <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'cv') echo 'active'; ?> fade1 tabs-container" id="cv">
                                                                            <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'cv') { ?>
                                                                                <script>
                                                                                    jQuery(window).load(function () {
                                                                                        cs_ajax_candidate_cvcover('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');
                                                                                    });
                                                                                </script>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                    $profile_tab = isset($_REQUEST['profile_tab']) ? $_REQUEST['profile_tab'] : '';
                                                                    do_action('jobhunt_candidate_dashboard_tabs', $profile_tab, $uid);
                                                                    ?>
                                                                    <div class="tab-pane <?php if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'packages') || (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'packages')) echo 'active'; ?> fade1 tabs-container" id="packages">
                                                                        <div class="cs-loader"></div>
                                                                        <?php if ((isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'packages') || (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'packages')) { ?>
                                                                            <script>
                                                                                jQuery(window).load(function () {
                                                                                    cs_ajax_candidate_membership_packages(pkg_array);
                                                                                });
                                                                            </script>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </div>

                                                                    <div class="tab-pane <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'change_password') echo 'active'; ?> fade1 tabs-container" id="change_password">
                                                                        <?php if (isset($_REQUEST['profile_tab']) && $_REQUEST['profile_tab'] == 'change_password') { ?>
                                                                            <script>
                                                                                jQuery(window).load(function () {
                                                                                    cs_candidate_change_password('<?php echo esc_js(admin_url('admin-ajax.php')); ?>', '<?php echo absint($uid); ?>');
                                                                                });
                                                                            </script>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </div> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    <?php } ?>
                            </div>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div id="main">
                            <div class="main-section">
                                <section class="candidate-profile">
                                    <div class="<?php if (isset($cs_plugin_options['cs_plugin_single_container']) && $cs_plugin_options['cs_plugin_single_container'] == 'on') echo 'container' ?>">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="unauthorized">
                                                    <h1><?php
                                                        _e('Please register yourself as a <span>candidate</span> to access this page.', 'jobhunt');
                                                        ?>
                                                    </h1>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div id="main">
                        <div class="main-section">
                            <section class="candidate-profile">
                                <div class="<?php if (isset($cs_plugin_options['cs_plugin_single_container']) && $cs_plugin_options['cs_plugin_single_container'] == 'on') echo 'container' ?>">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php
                                            echo do_shortcode('[cs_register register_role="contributor"] [/cs_register]');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </main>
    </div>
</div>
<?php
get_footer();
