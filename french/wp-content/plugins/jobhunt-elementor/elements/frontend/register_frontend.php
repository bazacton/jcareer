<?php

class Jobhunt_Register_Frontend
{

    public function render($settings)
    {
        global $wpdb , $cs_plugin_options , $cs_form_fields_frontend , $cs_form_fields2 , $cs_html_fields;
        if (function_exists('cs_socialconnect_scripts')) {
            cs_socialconnect_scripts();
        }
        $defaults = array(
            'column_size' => '1/1' ,
            'candidate_register_element_title' => '' ,
            'register_title' => '' ,
            'register_text' => '' ,
            'register_role' => 'contributor' ,
            'cs_register_class' => '' ,
            'cs_register_animation' => '' ,
            'only_links' => 'no' ,
            'hide_mobile_btns' => 'no'
        );
        extract(shortcode_atts($defaults , $settings));
        $column_size = isset($column_size) ? $column_size : '';

        $user_disable_text = esc_html__('User Registration is disabled' , 'jobhunt');
        $cs_sitekey = isset($cs_plugin_options['cs_sitekey']) ? $cs_plugin_options['cs_sitekey'] : '';
        $cs_secretkey = isset($cs_plugin_options['cs_secretkey']) ? $cs_plugin_options['cs_secretkey'] : '';
        $hide_mobile_btns = ($hide_mobile_btns == 'yes') ? 'visible-xs visible-sm' : '';
        $cs_captcha_switch = isset($cs_plugin_options['cs_captcha_switch']) ? $cs_plugin_options['cs_captcha_switch'] : '';

        $custom_addon = false;
        $custom_addon = apply_filters('jobhunt_custom_addon_depedency' , $custom_addon);
        $celine_active = false;
        $celine_active = apply_filters('jobhunt_celine_depedency' , $celine_active);

        if ($cs_sitekey <> '' and $cs_secretkey <> '' and !is_user_logged_in()) {
            cs_google_recaptcha_scripts();
            ?>
            <script>
                var recaptcha1;
                var recaptcha2;
                var recaptcha3;
                var recaptcha4;
                var cs_multicap = function () {
                    //Render the recaptcha1 on the element with ID "recaptcha1"
                    recaptcha1 = grecaptcha.render('recaptcha1', {
                        'sitekey': '<?php echo($cs_sitekey); ?>', //Replace this with your Site key
                        'theme': 'light'
                    });
                    //Render the recaptcha2 on the element with ID "recaptcha2"
                    recaptcha2 = grecaptcha.render('recaptcha2', {
                        'sitekey': '<?php echo($cs_sitekey); ?>', //Replace this with your Site key
                        'theme': 'light'
                    });
                    recaptcha3 = grecaptcha.render('recaptcha3', {
                        'sitekey': '<?php echo($cs_sitekey); ?>', //Replace this with your Site key
                        'theme': 'light'
                    });
                    //Render the recaptcha2 on the element with ID "recaptcha2"
                    recaptcha4 = grecaptcha.render('recaptcha4', {
                        'sitekey': '<?php echo($cs_sitekey); ?>', //Replace this with your Site key
                        'theme': 'light'
                    });
                };
                jQuery(document).ready(function () {
                    jQuery('.recaptcha-reload-a').click();
                });
            </script>
            <?php
        }

        //
        $output = '';
        $registraion_div_rand_id = rand(5 , 99999);
        $rand_id = rand(5 , 99999);
        $rand_value = rand(0 , 9999999);
        $role = $register_role;
        $output .= '<div class="signup-form">';
        if (isset($candidate_register_element_title) && $candidate_register_element_title != '') {

            $output .= '<div class="cs-element-title">';
            $output .= '<h4>' . $candidate_register_element_title . '</h4>';
            $output .= '</div>';
        }
        if (is_user_logged_in()) {
            $output .= '<div class="alert alert-warning">' .
                esc_html__('You have already logged in, Please logout to try again.' , 'jobhunt') . '<a data-dismiss="alert" class="close" href="#">×</a>'
                . '</div>';
        }
        $cs_employer_registration = isset($cs_plugin_options['cs_employer_reg_switch']) ? $cs_plugin_options['cs_employer_reg_switch'] : 'on';

        $role = 'cs_candidate';
        $role = apply_filters('jobhunt_register_user_role_frontend' , $role);

        if ($cs_employer_registration == 'on' && $role != 'cs_employer') {
            $output .= '<ul class="nav nav-tabs-page" role="tablist">';

            $output .= '<li role="presentation" class="active">
                                <a href="#candidate' . $registraion_div_rand_id . '" onclick="javascript:cs_set_session(\'' . admin_url("admin-ajax.php") . '\',\'candidate\')" role="tab" data-toggle="tab" >
                                <i class="icon-user-add"></i>' . esc_html__('I am a Candidate' , 'jobhunt') . '</a>
                            </li>';
            $output .= '<li role="presentation" >
                                <a href="#employer' . $registraion_div_rand_id . '" onclick="javascript:cs_set_session(\'' . admin_url("admin-ajax.php") . '\',\'employer\')" role="tab" data-toggle="tab" ><i class="icon-briefcase4"></i>' . esc_html__('I am an Employer' , 'jobhunt') . '</a></li>';

            $output .= '</ul>';
        }

        if (is_user_logged_in()) {

            $output .= '<script>'
                . 'jQuery("body").on("keypress", "input#user_login' . absint($rand_id) . ', input#user_pass' . absint($rand_id) . '", function (e) {
                            if (e.which == "13") {
                                show_alert_msg("' . esc_html__("Please logout first then try to login again" , "jobhunt") . '");
                                return false;
                            }
                        });'
                . '</script>';
        } else {
            $output .= '<script>'
                . 'jQuery("body").on("keypress", "input#user_login' . absint($rand_id) . ', input#user_pass' . absint($rand_id) . '", function (e) {
                            if (e.which == "13") {
                                cs_user_authentication("' . esc_url(admin_url("admin-ajax.php")) . '", "' . absint($rand_id) . '");
                                return false;
                            }
                        });'
                . '</script>';
        }

        $login_label = esc_html__('User Login' , 'jobhunt');
        $login_label = apply_filters('jobhunt_login_title_frontend' , $login_label);
        $output .= '<div class="input-info login-box login-from login-form-id-' . $rand_id . '">
                                <div class="scetion-title">
                                    <h2>' . $login_label . '</h2>
                                </div>
                        	<form method="post" class="wp-user-form webkit" id="ControlForm_' . $rand_id . '">
                                    <div class="row">
                                      <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                            <label>' . esc_html__('Username' , 'jobhunt') . '</label>';
        $cs_opt_array = array(
            'id' => '' ,
            'std' => esc_html__('Username' , 'jobhunt') ,
            'cust_id' => 'user_login_' . $rand_id ,
            'cust_name' => 'user_login' ,
            'classes' => 'form-control' ,
            'extra_atr' => ' size="20" tabindex="11" onfocus="if(this.value ==\'' . esc_html__('Username' , 'jobhunt') . '\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value =\'' . esc_html__('Username' , 'jobhunt') . '\'; }"' ,
            'return' => true ,
        );
        $output .= '<div class="field-holder">';
        $output .= '<i class="icon-user9"></i>';
        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
        $output .= '</div>';
        $output .= '
                                      </div>
                                      <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                            <label>' . esc_html__('Password' , 'jobhunt') . '</label>';
        $cs_opt_array = array(
            'id' => '' ,
            'std' => esc_html__('Password' , 'jobhunt') ,
            'cust_id' => 'user_pass' . $rand_id ,
            'cust_name' => 'user_pass' ,
            'cust_type' => 'password' ,
            'classes' => 'form-control' ,
            'extra_atr' => ' size="20" tabindex="12" onfocus="if(this.value ==\'' . esc_html__('Password' , 'jobhunt') . '\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value =\'' . esc_html__('Password' , 'jobhunt') . '\'; }"' ,
            'return' => true ,
        );
        $output .= '<div class="field-holder">';
        $output .= '<i class="icon-key7"></i>';
        $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
        $output .= '</div>';
        $output .= '</div>
                                      <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                        <div class="row">
                    ';
        if (is_user_logged_in()) {
            $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">';

            $cs_opt_array = array(
                'id' => '' ,
                'std' => esc_html__('Log in' , 'jobhunt') ,
                'cust_id' => 'user-submit' ,
                'cust_name' => 'user-submit' ,
                'cust_type' => 'button' ,
                'extra_atr' => ' onclick="javascript:show_alert_msg(\'' . esc_html__("Please logout first then try to login again" , "jobhunt") . '\')"' ,
                'classes' => 'user-submit backcolr cs-bgcolor acc-submit' ,
                'return' => true ,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $output .= '
                           
                    </div>';
        } else {
            $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">';

            $cs_opt_array = array(
                'id' => '' ,
                'std' => esc_html__('Log in' , 'jobhunt') ,
                'cust_id' => 'user-submit' ,
                'cust_name' => 'user-submit' ,
                'cust_type' => 'button' ,
                'extra_atr' => ' onclick="javascript:cs_user_authentication(\'' . admin_url("admin-ajax.php") . '\',\'' . $rand_id . '\')"' ,
                'classes' => 'cs-bgcolor user-submit  backcolr  acc-submit' ,
                'return' => true ,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $cs_opt_array = array(
                'std' => get_permalink() ,
                'id' => 'redirect_to' ,
                'cust_name' => 'redirect_to' ,
                'cust_type' => 'hidden' ,
                'return' => true ,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $cs_opt_array = array(
                'std' => '1' ,
                'id' => 'user_cookie' ,
                'cust_name' => 'user-cookie' ,
                'cust_type' => 'hidden' ,
                'return' => true ,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $cs_opt_array = array(
                'id' => '' ,
                'std' => 'ajax_login' ,
                'cust_name' => 'action' ,
                'cust_type' => 'hidden' ,
                'return' => true ,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $cs_opt_array = array(
                'std' => esc_html__('login' , 'jobhunt') ,
                'id' => 'login' ,
                'cust_name' => 'login' ,
                'cust_type' => 'hidden' ,
                'return' => true ,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $output .= '
        				<!--<span class="status status-message" style="display:none"></span>-->
        				<a class="user-forgot-password-page" href="#">' . esc_html__(' Forgot Password?' , 'jobhunt') . '</a>
                    </div>';
        }
        $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12 login-section">
                                <i class="icon-user-add"></i>' . esc_html__('New to Us? ' , 'jobhunt') . '  <a class="register-link-page" href="#">' . esc_html__('Register Here' , 'jobhunt') . '</a>
                                </div>
                                <span class="status status-message" style="display:none">
                                </span>
                               </div>
                              </div>
                            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                <div class="form-bg"> ';
        /// Social login switche options
        $twitter_login = isset($cs_plugin_options['cs_twitter_api_switch']) ? $cs_plugin_options['cs_twitter_api_switch'] : '';
        $facebook_login = isset($cs_plugin_options['cs_facebook_login_switch']) ? $cs_plugin_options['cs_facebook_login_switch'] : '';
        $linkedin_login = isset($cs_plugin_options['cs_linkedin_login_switch']) ? $cs_plugin_options['cs_linkedin_login_switch'] : '';
        $google_login = isset($cs_plugin_options['cs_google_login_switch']) ? $cs_plugin_options['cs_google_login_switch'] : '';
        if ($twitter_login == 'on' || $facebook_login == 'on' || $linkedin_login == 'on' || $google_login == 'on') {
            ob_start();
            $output .= '<h3>' . esc_html__('Signup / Signin with' , 'jobhunt') . '</h3>';
            $output .= do_action('login_form');
            $output .= ob_get_clean();
        }
        $output .= '</div>
                     </div>
                    </div>
        	</form>';
        $output .= '</div>';

        $output .= '<div class="input-info forgot-box login-from login-form-id-' . $rand_value . '" style="display:none;">';
        ob_start();
        $output .= do_shortcode('[cs_forgot_password]');
        $output .= ob_get_clean();
        $output .= '</div>';

        $role = 'cs_candidate';
        $role = apply_filters('jobhunt_register_user_role_frontend' , $role);


        $employer_tab_active = '';
        $candidate_tab_active = 'active';
        if ($role == 'cs_employer') {
            $employer_tab_active = 'active';
            $candidate_tab_active = '';
        }

        $output .= '<div class="tab-content tab-content-page">';
        $isRegistrationOn = get_option('users_can_register');
        if ($isRegistrationOn) {
            // registration page element
            $output .= '<div id="employer' . $registraion_div_rand_id . '" role="tabpanel" class="tab-pane ' . $employer_tab_active . '">'; //employer tab start
            $output .= '<div class="input-info">';
            $output .= '<div class="row">';

            $output .= '<script>'
                . 'jQuery("body").on("keypress", "input#user_login' . absint($rand_value) . ', input#cs_user_email' . absint($rand_value) . ', input#cs_organization_name' . absint($rand_value) . ', input#cs_employer_specialisms' . absint($rand_value) . ', input#cs_phone_no' . absint($rand_value) . '", function (e) {
                            if (e.which == "13") {
                                cs_registration_validation("' . esc_url(admin_url("admin-ajax.php")) . '", "' . absint($rand_value) . '");
                                return false;
                            }
                        });'
                . '</script>';

            $output .= '<form method="post" class="wp-user-form " id="wp_signup_form_' . $rand_value . '" enctype="multipart/form-data">
                              <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
            $output .= '<div class="field-holder">';
            $output .= '<i class="icon-user9"></i>';
            $cs_opt_array = array(
                'id' => '' ,
                'std' => '' ,
                'cust_id' => 'user_login_' . $rand_value ,
                'cust_name' => 'user_login' . $rand_value ,
                'extra_atr' => ' size="20" tabindex="101" placeholder="' . esc_html__('Username' , 'jobhunt') . '"' ,
                'classes' => 'form-control' ,
                'return' => true ,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

            $output .= '</div></div>';
            $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
            $output .= '<div class="field-holder">';
            $output .= '<i class="icon-envelope4"></i>';
            $output .= $cs_form_fields_frontend->cs_form_text_render(
                array('name' => esc_html__('Email' , 'jobhunt') ,
                    'id' => 'user_email' . $rand_value . '' ,
                    'classes' => '' ,
                    'std' => '' ,
                    'description' => '' ,
                    'return' => true ,
                    'hint' => ''
                )
            );
            $output .= '</div></div>';
            $cs_password_option = isset($cs_plugin_options['cs_user_password_switchs']) ? $cs_plugin_options['cs_user_password_switchs'] : '';
            if ($cs_password_option == 'on') {
                $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
                $output .= '<div class="field-holder">';
                $output .= '<i class="icon-key7"></i>';
                $output .= $cs_form_fields_frontend->cs_form_text_render(
                    array('name' => esc_html__('Password' , 'jobhunt') ,
                        'id' => 'password' . $rand_value . '' ,
                        'classes' => '' ,
                        'std' => '' ,
                        'description' => '' ,
                        'cust_type' => 'password' ,
                        'return' => true ,
                        'hint' => ''
                    )
                );
                $output .= '</div></div>';
            }

            $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
            $output .= '<div class="field-holder">';
            $output .= '<i class="icon-briefcase2"></i>';
            $output .= $cs_form_fields_frontend->cs_form_text_render(
                array('name' => esc_html__('Organization Name' , 'jobhunt') ,
                    'id' => 'organization_name' . $rand_value . '' ,
                    'classes' => '' ,
                    'icon' => '' ,
                    'std' => '' ,
                    'description' => '' ,
                    'return' => true ,
                    'hint' => ''
                )
            );
            $output .= '</div>';
            $output .= '</div>';


            $output .= $cs_form_fields_frontend->cs_form_hidden_render(
                array('name' => esc_html__('Post Type' , 'jobhunt') ,
                    'id' => 'user_role_type' . $rand_value . '' ,
                    'classes' => 'col-md-12 col-lg-12 col-sm-12 col-xs-12' ,
                    'std' => 'employer' ,
                    'description' => '' ,
                    'return' => true ,
                    'hint' => ''
                )
            );


            if (!$custom_addon && !$celine_active) {
                $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
                $output .= '<div class="select-holder">';
                $output .= cs_get_specialisms_dropdown('cs_employer_specialisms' . $rand_value , 'cs_employer_specialisms' . $rand_value , '' , 'chosen-select form-control');
                $output .= '</div>';
                $output .= '</div>';
            }
            if (!$celine_active) {
                $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
                $output .= '<div class="field-holder">';
                $output .= '<i class="icon-phone6"></i>';
                $output .= $cs_form_fields_frontend->cs_form_text_render(
                    array('name' => esc_html__('Phone Number' , 'jobhunt') ,
                        'id' => 'phone_no' . $rand_value . '' ,
                        'classes' => '' ,
                        'std' => '' ,
                        'description' => '' ,
                        'return' => true ,
                        'hint' => ''
                    )
                );
                $output .= '</div></div>';
            }

            $output = apply_filters('jobhunt_signup_terms_field' , $output , $rand_value , 'employer' , 'register');


            $cs_rand_value = rand(54654 , 99999965);
            if ($cs_captcha_switch == 'on' && (!is_user_logged_in())) {
                $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
                $output .= '<div class=" recaptcha-reload" id="recaptcha1_div">';
                $output .= cs_captcha('recaptcha1');
                $output .= '</div>';
                $output .= '</div>';
            }
            $output .= '<div class="upload-file">';
            $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">'
                . '<div class="row">';
            ob_start();
            $output .= do_action('register_form');
            $output .= ob_get_clean();
            if (is_user_logged_in()) {
                $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">';
                $cs_opt_array = array(
                    'id' => '' ,
                    'std' => esc_html__('Create Account' , 'jobhunt') ,
                    'cust_id' => 'submitbtn' . $rand_value ,
                    'cust_name' => 'user-submit' ,
                    'cust_type' => 'button' ,
                    'classes' => 'user-submit cs-bgcolor acc-submit' ,
                    'extra_atr' => ' tabindex="103" onclick="javascript:show_alert_msg(\'' . esc_html__("Please logout first then try to registration again" , "jobhunt") . '\')"' ,
                    'return' => true ,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
            } else {
                $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">';
                $cs_opt_array = array(
                    'id' => '' ,
                    'std' => esc_html__('Create Account' , 'jobhunt') ,
                    'cust_id' => 'submitbtn' . $rand_value ,
                    'cust_name' => 'user-submit' ,
                    'cust_type' => 'button' ,
                    'classes' => 'cs-bgcolor user-submit acc-submit' ,
                    'extra_atr' => ' tabindex="103" onclick="javascript:cs_registration_validation(\'' . admin_url("admin-ajax.php") . '\',\'' . $rand_value . '\')"' ,
                    'return' => true ,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $cs_opt_array = array(
                    'id' => '' ,
                    'std' => $role ,
                    'cust_id' => 'register-role' ,
                    'cust_name' => 'role' ,
                    'cust_type' => 'hidden' ,
                    'return' => true ,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $cs_opt_array = array(
                    'id' => '' ,
                    'std' => 'cs_registration_validation' ,
                    'cust_name' => 'action' ,
                    'cust_type' => 'hidden' ,
                    'return' => true ,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
            }
            $output .= '</div>'; //employer tab end
            $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12 login-section">
        			<i class="icon-user-add"></i>' . esc_html__(' Already have an account?' , 'jobhunt') . ' <a href="javascript:void(0);" class="login-link-page">' . esc_html__('Login Now' , 'jobhunt') . '</a>';
            $output .= '</div>';

            $output .= '</div>';
            $output .= '<div id="result_' . $rand_value . '" class="status-message"><p class="status"></p></div>';
            $output .= '</div>';
            $output .= '</div>
                                </form>
                                <div class="register_content">' . do_shortcode($content . $register_text) . '</div>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            //$output .= '</div>';
            // registration page element
            $output .= '<div role="tabpanel" id="candidate' . $registraion_div_rand_id . '" class="tab-pane ' . $candidate_tab_active . '">';
            $rand_id = rand(50 , 99999);
            $output .= '<div class="input-info">';
            if (is_user_logged_in()) {
                $output .= '<script>'
                    . 'jQuery("body").on("keypress", "input#user_login' . absint($rand_id) . ', input#cs_user_email' . absint($rand_id) . ', input#cs_candidate_specialisms' . absint($rand_id) . ', input#cs_phone_no' . absint($rand_id) . '", function (e) {
                            if (e.which == "13") {
                                show_alert_msg("' . esc_html__("Please logout first then try to registration again" , "jobhunt") . '");
                                return false;
                            }
                        });'
                    . '</script>';
            } else {
                $output .= '<script>'
                    . 'jQuery("body").on("keypress", "input#user_login' . absint($rand_id) . ', input#cs_user_email' . absint($rand_id) . ', input#cs_candidate_specialisms' . absint($rand_id) . ', input#cs_phone_no' . absint($rand_id) . '", function (e) {
                            if (e.which == "13") {
                                cs_registration_validation("' . esc_url(admin_url("admin-ajax.php")) . '", "' . absint($rand_id) . '");
                                return false;
                            }
                        });'
                    . '</script>';
            }
            $output .= '<div class="row">
                                <form method="post" class="wp-user-form " id="wp_signup_form_' . $rand_id . '" enctype="multipart/form-data">';
            $data = '';
            $output .= apply_filters('jobhunt_cand_reg_form' , $data , $rand_id);
            $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
            $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
            $output .= '<div class="field-holder">';
            $output .= '<i class="icon-user9"></i>';
            $cs_opt_array = array(
                'id' => '' ,
                'std' => '' ,
                'cust_id' => 'user_login_' . $rand_id ,
                'cust_name' => 'user_login' . $rand_id ,
                'classes' => 'form-control' ,
                'extra_atr' => ' size="20" tabindex="101" placeholder="' . esc_html__('Username' , 'jobhunt') . '"' ,
                'return' => true ,
            );
            $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);
            $output .= '</div></div>';
            $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
            $output .= '<div class="field-holder">';
            $output .= '<i class="icon-envelope4"></i>';
            $output .= $cs_form_fields_frontend->cs_form_text_render(
                array('name' => esc_html__('Email' , 'jobhunt') ,
                    'id' => 'user_email' . $rand_id . '' ,
                    'classes' => '' ,
                    'std' => '' ,
                    'description' => '' ,
                    'return' => true ,
                    'hint' => ''
                )
            );
            $output .= '</div></div>';
            $cs_password_option = isset($cs_plugin_options['cs_user_password_switchs']) ? $cs_plugin_options['cs_user_password_switchs'] : '';
            if ($cs_password_option == 'on') {
                $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
                $output .= '<div class="field-holder">';
                $output .= '<i class="icon-key7"></i>';
                $output .= $cs_form_fields_frontend->cs_form_text_render(
                    array('name' => esc_html__('Password' , 'jobhunt') ,
                        'id' => 'password' . $rand_id . '' ,
                        'classes' => '' ,
                        'std' => '' ,
                        'description' => '' ,
                        'cust_type' => 'password' ,
                        'return' => true ,
                        'hint' => ''
                    )
                );

                $output .= '</div></div>';
            }
            if (!$custom_addon && !$celine_active) {
                $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
                $output .= '<div class="select-holder">';
                $output .= cs_get_specialisms_dropdown('cs_candidate_specialisms' . $rand_id , 'cs_candidate_specialisms' . $rand_id , '' , 'chosen-select form-control');
                $output .= '</div>';
                $output .= '</div>';
            }

            $output .= $cs_form_fields_frontend->cs_form_hidden_render(
                array('name' => esc_html__('Post Type' , 'jobhunt') ,
                    'id' => 'user_role_type' . $rand_id . '' ,
                    'classes' => 'col-md-12 col-lg-12 col-sm-12 col-xs-12' ,
                    'std' => 'candidate' ,
                    'description' => '' ,
                    'return' => true ,
                    'hint' => ''
                )
            );

            if (!$celine_active) {

                $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
                $output .= '<div class="field-holder">';
                $output .= '<i class="icon-phone6"></i>';
                $output .= $cs_form_fields_frontend->cs_form_text_render(
                    array('name' => esc_html__('Phone Number' , 'jobhunt') ,
                        'id' => 'phone_no' . $rand_id . '' ,
                        'classes' => '' ,
                        'std' => '' ,
                        'description' => '' ,
                        'return' => true ,
                        'hint' => ''
                    )
                );
                $output .= '</div>';
                $output .= '</div>';
            }
            $output = apply_filters('jobhunt_signup_terms_field' , $output , $rand_id , 'candidate' , 'register');

            if ($cs_captcha_switch == 'on' && (!is_user_logged_in())) {
                $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
                $output .= '<div class="recaptcha-reload" id="recaptcha2_div">';
                $output .= cs_captcha('recaptcha2');
                $output .= '</div>';
                $output .= '</div>';
            }
            $output .= '<div class="upload-file">
                                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                                        <div class="row">';
            ob_start();
            $output .= do_action('register_form');
            $output .= ob_get_clean();

            if (is_user_logged_in()) {
                $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">';

                $cs_opt_array = array(
                    'id' => '' ,
                    'std' => esc_html__('Create Account' , 'jobhunt') ,
                    'cust_id' => 'submitbtn' . $rand_id ,
                    'cust_name' => 'user-submit' ,
                    'cust_type' => 'button' ,
                    'extra_atr' => ' tabindex="103" onclick="javascript:show_alert_msg(\'' . esc_html__("Please logout first then try to registration again" , "jobhunt") . '\')"' ,
                    'classes' => 'cs-bgcolor user-submit  acc-submit' ,
                    'return' => true ,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $output .= '</div>';
            } else {
                $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">';

                $cs_opt_array = array(
                    'id' => '' ,
                    'std' => esc_html__('Create Account' , 'jobhunt') ,
                    'cust_id' => 'submitbtn' . $rand_id ,
                    'cust_name' => 'user-submit' ,
                    'cust_type' => 'button' ,
                    'extra_atr' => ' tabindex="103" onclick="javascript:cs_registration_validation(\'' . admin_url("admin-ajax.php") . '\',\'' . $rand_id . '\')"' ,
                    'classes' => 'cs-bgcolor user-submit  acc-submit' ,
                    'return' => true ,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $cs_opt_array = array(
                    'id' => '' ,
                    'std' => $role ,
                    'cust_id' => 'login-role' ,
                    'cust_name' => 'role' ,
                    'cust_type' => 'hidden' ,
                    'return' => true ,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $cs_opt_array = array(
                    'id' => '' ,
                    'std' => 'cs_registration_validation' ,
                    'cust_name' => 'action' ,
                    'cust_type' => 'hidden' ,
                    'return' => true ,
                );
                $output .= $cs_form_fields2->cs_form_text_render($cs_opt_array);

                $output .= '</div>';
            }

            $output .= '
                        <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12 login-section">
                                        <i class="icon-user-add"></i> ' . esc_html__("Already have an account?" , "jobhunt") . ' 
                                        <a href="javascript:void(0);" class="login-link-page">' . esc_html__('Login Now' , 'jobhunt') . '</a>
                                    </div>
                                </div>
                                </div>
                                <div id="result_' . $rand_id . '" class="status-message"><p class="status"></p></div>
                                </div>';
            $output .= '</form>';
            $output .= '</div>';

            /// Social login switche options
            $twitter_login = isset($cs_plugin_options['cs_twitter_api_switch']) ? $cs_plugin_options['cs_twitter_api_switch'] : '';
            $facebook_login = isset($cs_plugin_options['cs_facebook_login_switch']) ? $cs_plugin_options['cs_facebook_login_switch'] : '';
            $linkedin_login = isset($cs_plugin_options['cs_linkedin_login_switch']) ? $cs_plugin_options['cs_linkedin_login_switch'] : '';
            $google_login = isset($cs_plugin_options['cs_google_login_switch']) ? $cs_plugin_options['cs_google_login_switch'] : '';

            if ($twitter_login == 'on' || $facebook_login == 'on' || $linkedin_login == 'on' || $google_login == 'on') {
                $output .= '<div class="row">';
                $output .= '<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">';
                $output .= '<div class="form-bg">';
                ob_start();
                if (class_exists('wp_jobhunt')) {
                    $output .= '<h3>' . esc_html__('Signup / Signin with' , 'jobhunt') . '</h3>';
                    $output .= do_action('login_form');
                }
                $output .= ob_get_clean();
                $output .= '</div>';
                $output .= '</div>';
            }
            $output .= '</div></div></div>';

            $output .= '<div class="register_content">' . do_shortcode($content . $register_text) . '</div>';
        } else {
            $output .= '<div class="col-md-6 col-lg-6 col-sm-12 col-xs-12 register-page">';
            $output .= '<div class="cs-user-register">
                                <div class="cs-element-title">
                                    <h2>' . esc_html__('Register' , 'jobhunt') . '</h2>
                                </div>
                                <p>' . $user_disable_text . '</p>';
            $output .= '</div>';
            $output .= '</div>';
        }
        $output .= '</div></div>';
        echo $output;
    }

}

