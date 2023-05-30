<?php
/**
 * Plugin Name: JobHunt Google Structured Jobs
 * Plugin URI: http://themeforest.net/user/Chimpstudio/
 * Description: Job Hunt Google Structured Jobs Add on
 * Version: 1.5
 * Author: ChimpStudio
 * Author URI: http://themeforest.net/user/Chimpstudio/
 * @package Job Hunt
 * Text Domain: jobhunt-google-structured-jobs
 */
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Job_Hunt_Google_Structured_Jobs_Import class.
 */
class JobHunt_Google_Structured_Jobs {

    public $admin_notices;

    /**
     * construct function.
     */
    public function __construct() {

        // Define constants
        define('JOBHUNT_GOOGLE_STRUCTURED_JOBS_PLUGIN_VERSION', '1.5');
        define('JOBHUNT_GOOGLE_STRUCTURED_JOBS_PLUGIN_DOMAIN', 'jobhunt-google-structured-jobs');
        define('JOBHUNT_GOOGLE_STRUCTURED_JOBS_PLUGIN_URL', WP_PLUGIN_URL . '/jobhunt-google-structured-jobs');
        define('JOBHUNT_GOOGLE_STRUCTURED_JOBS_PLUGIN_CORE_DIR', WP_PLUGIN_DIR . '/jobhunt-google-structured-jobs');
        define('JOBHUNT_GOOGLE_STRUCTURED_JOBS_PLUGIN_LANGUAGES_DIR', JOBHUNT_GOOGLE_STRUCTURED_JOBS_PLUGIN_CORE_DIR . '/languages');
        define('JOBHUNT_GOOGLE_STRUCTURED_JOBS_PLUGIN_INCLUDES_DIR', JOBHUNT_GOOGLE_STRUCTURED_JOBS_PLUGIN_CORE_DIR . '/includes');
        $this->admin_notices = array();
        //admin notices
        add_action('admin_notices', array($this, 'google_structured_jobs_notices_callback'));
        if (!$this->check_dependencies()) {
            return false;
        }

        // Initialize Addon
        add_action('init', array($this, 'init'));
    }

    /**
     * Initialize application, load text domain, enqueue scripts, include classes and add actions
     */
    public function init() {
        // Add Plugin textdomain
        $locale = apply_filters('plugin_locale', get_locale(), 'jobhunt-google-structured-jobs');
        load_textdomain('jobhunt-google-structured-jobs', JOBHUNT_GOOGLE_STRUCTURED_JOBS_PLUGIN_LANGUAGES_DIR . '/jobhunt-google-structured-jobs' . "-" . $locale . '.mo');
        load_plugin_textdomain('jobhunt-google-structured-jobs', false, JOBHUNT_GOOGLE_STRUCTURED_JOBS_PLUGIN_LANGUAGES_DIR);


        // Add filters
        add_filter('jobhunt_google_structured_jobs_admin_fields', array(&$this, 'google_structured_jobs_admin_fields'), 10, 1);
        // Add actions
        add_action('jobhunt_google_sturctured_jobs_posting', array(&$this, 'jobhunt_google_sturctured_jobs_posting_callback'), 10, 1);
    }

    /**
     * Check plugin dependencies (JobHunt), nag if missing.
     *
     * @param boolean $disable disable the plugin if true, defaults to false.
     */
    public function check_dependencies($disable = false) {
        $result = true;
        $active_plugins = get_option('active_plugins', array());
        if (is_multisite()) {
            $active_sitewide_plugins = get_site_option('active_sitewide_plugins', array());
            $active_sitewide_plugins = array_keys($active_sitewide_plugins);
            $active_plugins = array_merge($active_plugins, $active_sitewide_plugins);
        }
        $jobhunt_is_active = in_array('wp-jobhunt/wp-jobhunt.php', $active_plugins);
        if (!$jobhunt_is_active) {
            $this->admin_notices = '<div class="error">' . __('<em><b>JobHunt Google Structured Jobs</b></em> needs the <b>Job Hunt</b> plugin. Please install and activate it.', 'jobhunt-google-structured-jobs') . '</div>';
        }
        if (!$jobhunt_is_active) {
            if ($disable) {
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                deactivate_plugins(array(__FILE__));
            }
            $result = false;
        }
        return $result;
    }

    public function google_structured_jobs_notices_callback() {
        if (isset($this->admin_notices) && !empty($this->admin_notices)) {
            foreach ($this->admin_notices as $value) {
                echo $value;
            }
        }
    }

    /**
     * Indeed job admin fields
     */
    public function google_structured_jobs_admin_fields($cs_setting_options = array()) {
        $on_off_option = array("show" => "on", "hide" => "off");
        $cs_setting_options[] = array("name" => esc_html__('Google Structured Jobs Settings', 'jobhunt-google-structured-jobs'),
            "id" => "tab-welcome-page",
            "std" => esc_html__("Google Structured Jobs Settings", 'jobhunt-google-structured-jobs'),
            "type" => "section",
            "options" => "",
        );
        $cs_setting_options[] = array("name" => esc_html__("Google Structured Jobs", "jobhunt"),
            "desc" => "",
            "hint_text" => esc_html__("Turn this switcher OFF to hide jobs from google structured jobs", "jobhunt-google-structured-jobs"),
            "id" => "google_structured_jobs",
            "std" => "off",
            "type" => "checkbox",
            "options" => $on_off_option
        );
        return $cs_setting_options;
    }

    /**
     * Posting jobs to google structure section
     */
    public function jobhunt_google_sturctured_jobs_posting_callback($cs_job_id) {
        global $cs_gateway_options;

        $cs_plugin_options = get_option('cs_plugin_options');
        $google_jobs_posting_check = isset($cs_plugin_options['cs_google_structured_jobs']) ? $cs_plugin_options['cs_google_structured_jobs'] : '';
        if ($google_jobs_posting_check == 'on') {
            $cs_job_title = get_the_title($cs_job_id);
            $cs_job_obj = get_post($cs_job_id);
            $cs_job_desc = isset($cs_job_obj->post_content) ? $cs_job_obj->post_content : '';
            $cs_job_desc = apply_filters('the_content', $cs_job_desc);

            $cs_job_username = get_post_meta($cs_job_id, 'cs_job_username', true);
            $employer_post = get_user_by('login', $cs_job_username);
            $cs_job_employer_id = $employer_post->ID;

            if (isset($cs_job_employer_id)) {
                $cs_emp_user_id = $cs_job_employer_id;
                $cs_emp_user_obj = get_user_by('ID', $cs_emp_user_id);
                $cs_emp_user_url = isset($cs_emp_user_obj->user_url) ? $cs_emp_user_obj->user_url : '';
            }
            if (isset($cs_emp_user_url) && $cs_emp_user_url != '') {
                $cs_emp_user_url = $cs_emp_user_url;
            } else {
                $cs_emp_user_url = home_url();
            }

            $cs_employer_name = $cs_emp_user_obj->display_name;
            $cs_employee_employer_img = get_user_meta($cs_emp_user_obj->ID, 'user_img', true);
            $cs_employee_employer_img = cs_get_img_url($cs_employee_employer_img, 'cs_media_5');
            if (!cs_image_exist($cs_employee_employer_img) || $cs_employee_employer_img == "") {
                $cs_employee_employer_img = esc_url(wp_jobhunt::plugin_url() . 'assets/images/img-not-found16x9.jpg');
            }
            $cs_get_job_contry = get_post_meta($cs_job_id, 'cs_post_loc_country', true);
            $cs_get_job_region = get_post_meta($cs_job_id, 'cs_post_loc_city', true);
            $cs_get_job_full_adres = get_post_meta($cs_job_id, 'cs_post_comp_address', true);

            $cs_woo_currency = get_option('woocommerce_currency');

            $cs_job_currency = isset($cs_gateway_options['cs_currency_type']) && $cs_gateway_options['cs_currency_type'] != '' ? $cs_gateway_options['cs_currency_type'] : 'USD';

            $cs_job_salary = get_post_meta($cs_job_id, 'salarypackage', true);
            $salary_range = explode('-', $cs_job_salary);
            $cs_job_salary_min = $salary_range[0];
            $cs_job_salary_max = $salary_range[1];

            $cs_job_posted_date = get_post_meta($cs_job_id, 'cs_job_posted', true);
            $cs_job_expiry_date = get_post_meta($cs_job_id, 'cs_job_expired', true);

            $cs_job_types = get_the_terms($cs_job_id, 'job_type');
            $cs_job_type = isset($cs_job_types[0]->slug) ? $cs_job_types[0]->slug : 'CONTRACTOR';

            $cs_salary_type_val_str = 'MONTH';


            if ($cs_job_title != '' && $cs_job_desc != '' && $cs_job_posted_date > 0 && $cs_job_expiry_date > 0) {
                ?>
                <script type="application/ld+json">
                    {
                    "@context": "http://schema.org/",
                    "@type": "JobPosting",
                    "title": "<?php echo($cs_job_title) ?>",
                    "description": "<?php echo esc_html__($cs_job_desc, 'jobhunt-google-structured-jobs') ?>",
                    "identifier": {
                    "@type": "PropertyValue",
                    "name": "<?php echo($cs_employer_name) ?>",
                    "value": "<?php echo($cs_job_employer_id) ?>"
                    },
                    "datePosted": "<?php echo date('Y-m-d', $cs_job_posted_date) ?>",
                    "validThrough": "<?php echo date('Y-m-d', $cs_job_expiry_date) ?>
                    T<?php echo date('H:i', $cs_job_expiry_date) ?>
                    ",
                    "employmentType": "<?php echo strtoupper($cs_job_type); ?>",
                    "hiringOrganization": {
                    "@type": "Organization",
                    "name": "<?php echo($cs_employer_name) ?>",
                    "sameAs": "<?php echo esc_url($cs_emp_user_url) ?>",
                    "logo": "<?php echo($cs_employee_employer_img) ?>"
                    },
                    "jobLocation": {
                    "@type": "Place",
                    "address": {
                    "@type": "PostalAddress",
                    "streetAddress": "<?php echo($cs_get_job_full_adres) ?>",
                    "addressLocality": "<?php echo($cs_get_job_region) ?>",
                    "addressRegion": "<?php echo($cs_get_job_region) ?>",
                    "postalCode": "00000",
                    "addressCountry": "<?php echo($cs_get_job_contry) ?>"
                    }
                    }
                <?php if ($cs_job_salary_min > 0) {
                    ?>
                            "baseSalary": {
                            "@type": "MonetaryAmount",
                            "currency": "<?php echo($cs_job_currency) ?>",
                            "value": {
                            "@type": "QuantitativeValue",
                            "minValue": "<?php echo ($cs_job_salary_min) ?>",
                            "maxValue": "<?php echo ($cs_job_salary_max) ?>",
                            "unitText": "<?php echo ($cs_salary_type_val_str) ?>"
                    }
                            }
                    <?php
                }
                ?>
                    }


                </script>
                <?php
            }
        }
    }

}

new JobHunt_Google_Structured_Jobs();
