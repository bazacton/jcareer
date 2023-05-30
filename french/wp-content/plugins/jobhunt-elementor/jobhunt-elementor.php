<?php

/**
 * Plugin Name: JobHunt Elementor Addon
 * Plugin URI: http://themeforest.net/user/Chimpstudio/
 * Description: JobHunt Elementor Addon
 * Version: 1.0
 * Author: ChimpStudio
 * Author URI: http://themeforest.net/user/Chimpstudio/
 *
 * @package Job Hunt
 * Text Domain: jobhunt-elementor
 */
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * JobHunt_Elementor class.
 */
class JobHunt_Elementor {

    public $admin_notices;

    /**
     * construct function.
     */
    public function __construct() {

        // Define constants
        define('JOBHUNT_ELEMENTOR_PLUGIN_VERSION', '1.0');
        define('JOBHUNT_ELEMENTOR_PLUGIN_DOMAIN', 'jobhunt-elementor');
        define('JOBHUNT_ELEMENTOR_PLUGIN_URL', WP_PLUGIN_URL . '/jobhunt-elementor');
        define('JOBHUNT_ELEMENTOR_CORE_DIR', WP_PLUGIN_DIR . '/jobhunt-elementor');
        define('JOBHUNT_ELEMENTOR_LANGUAGES_DIR', JOBHUNT_ELEMENTOR_CORE_DIR . '/languages');

        $this->admin_notices = array();
        //admin notices
        add_action('admin_notices', array($this, 'jobhunt_elementor_notices_callback'));
        if (!$this->check_dependencies()) {
            return false;
        }
        // Initialize Addon
        add_action('init', array($this, 'init'));

        add_filter('jobhunt_plugin_settings_general_tab', array($this, 'jobhunt_plugin_settings_general_tab_callback'));
    }

    /**
     * Add Field to Choose Frameworks
     *
     * @param $cs_setting_options
     * @return void
     */
    public function jobhunt_plugin_settings_general_tab_callback($cs_setting_options) {
        if (class_exists('Elementor\Core\Settings\Manager')) {
            $cs_setting_options[] = array(
                'name' => __('Framework', 'jobhunt-elementor'),
                "desc" => __("You will loose the content for all Pages by Swithing Framework.", "jobhunt-elementor"),
                "hint_text" => '',
                'id' => 'jobhunt_framework',
                "std" => "",
                "classes" => "chosen-select",
                "type" => "select",
                "options" => array(
                    'jobhunt_builtin' => esc_html__("Jobhunt Builtin", "jobhunt-elementor"),
                    'elementor' => esc_html__("Elementor", "jobhunt-elementor"),
                )
            );
        }

        return $cs_setting_options;
    }

    /**
     *  Load text domain and enqueue style
     */
    public function init() {
        global $cs_plugin_options;
        $cs_jobhunt_framework = isset($cs_plugin_options['cs_jobhunt_framework']) ? $cs_plugin_options['cs_jobhunt_framework'] : 'jobhunt_builtin';
        // Add Plugin textdomain
        $locale = apply_filters('plugin_locale', get_locale(), 'jobhunt-elementor');
        load_textdomain('jobhunt-elementor', JOBHUNT_ELEMENTOR_LANGUAGES_DIR . '/jobhunt-elementor' . "-" . $locale . '.mo');
        load_plugin_textdomain('jobhunt-elementor', false, JOBHUNT_ELEMENTOR_LANGUAGES_DIR);

        // Enqueue CSS
        wp_enqueue_style('jobhunt-elementor-styles', esc_url(JOBHUNT_ELEMENTOR_PLUGIN_URL . '/assets/css/jobhunt-elementor-style.css'));

        if ($cs_jobhunt_framework == 'elementor') {
            // Include Classes
            require_once 'elements/elements_class.php';
        }

        $cpt_support = get_option('elementor_cpt_support');
        $cpt_support = [ 'page']; //create array of our default supported post types
        update_option('elementor_cpt_support', $cpt_support); //write it to the database
        add_action('jobcareer_after_demo_content_import', array($this, 'jobcareer_after_demo_content_import_callback'));
    }

    public function jobcareer_after_demo_content_import_callback($demo_data_name) {
        if ((strpos($demo_data_name, 'elementor') !== false)) {
            global $wp_filesystem;
            $current_demo = 'elementor';
            $verification_code = get_option('item_purchase_code_verification');
            $urls = isset($verification_code['urls']) ? json_decode($verification_code['urls']) : array();
            $elementor_data_url = isset($urls->$current_demo->postdata) ? $urls->$current_demo->postdata : '';

            $elementor_demo_data_array = $wp_filesystem->get_contents($elementor_data_url);
            $elementor_demo_data_array = file_get_contents($elementor_data_url);
            $elementor_demo_data_array = json_decode($elementor_demo_data_array);
            $elementor_demo_data_array = (array) $elementor_demo_data_array;

            if (!empty($elementor_demo_data_array)) {
                foreach ($elementor_demo_data_array as $elementor_demo_slug => $elementor_demo_data) {
                    $post = get_page_by_path($elementor_demo_slug);
                    $elementor_demo_data = htmlspecialchars_decode($elementor_demo_data);
                    update_post_meta($post->ID, '_elementor_data', $elementor_demo_data);
                }
            }

            update_option('elementor_data', $elementor_demo_data_array);
        }
    }

    public function export_elementor_data() {

        $args = array(
            'post_type' => 'any',
            'fields' => 'ids',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => '_elementor_data',
                    'value' => '',
                    'compare' => '!=',
                ),
            ),
        );
        $query = new WP_Query($args);
        $posts_array = isset($query->posts) ? $query->posts : array();

        $elementor_demo_data = array();

        if (!empty($posts_array)) {
            foreach ($posts_array as $post_id) {
                //if( $post_id == 18162){

                $post = get_post($post_id);
                $post_slug = $post->post_name;
                $post_elementor_data = get_post_meta($post_id, '_elementor_data', true);
                $post_elementor_data = htmlspecialchars($post_elementor_data, ENT_QUOTES);
                $post_elementor_data = htmlspecialchars($post_elementor_data);
                $post_elementor_data = addslashes($post_elementor_data);
                //$post_elementor_data    = serialize($post_elementor_data);

                $elementor_demo_data[$post_slug] = $post_elementor_data;
                //}
            }
        }
        $elementor_demo_data = json_encode($elementor_demo_data);
        echo $elementor_demo_data;
        exit;
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
            $this->admin_notices[] = '<div class="error">' . __('<em><b>JobHunt Elementor</b></em> needs the <b>Job Hunt</b> plugin. Please install and activate it.', 'jobhunt-elementor') . '</div>';
        }

        if (!$jobhunt_is_active) {
            if ($disable) {
                include_once(ABSPATH . 'wp-admin/includes/plugin.php');
                deactivate_plugins(array(__FILE__));
            }
            $result = false;
        }

        return $result;
    }

    public function jobhunt_elementor_notices_callback() {
        foreach ($this->admin_notices as $value) {
            echo $value;
        }
    }

    public static function plugin_path() {
        return untrailingslashit(plugin_dir_path(__FILE__));
    }

    public static function template_path() {
        return apply_filters('wp_pb_elementor_template_path', 'wp-pinboard/');
    }

    public static function jobhunt_elementor_get_template_part($slug = '', $name = '', $ext_template = '') {
        $template = '';

        if ($ext_template != '') {
            $ext_template = trailingslashit($ext_template);
        }
        if ($name) {
            $template = locate_template(array("{$slug}-{$name}.php", JobHunt_Elementor::template_path() . "{$ext_template}{$slug}-{$name}.php"));
        }
        if (!$template && $name && file_exists(JobHunt_Elementor::plugin_path() . "/elements/frontend/templates/{$ext_template}{$slug}-{$name}.php")) {

            $template = JobHunt_Elementor::plugin_path() . "/elements/frontend/templates/{$ext_template}{$slug}-{$name}.php";
        }
        if (!$template) {

            $template = locate_template(array("{$slug}.php", JobHunt_Elementor::template_path() . "{$ext_template}{$slug}.php"));
        }
        if ($template) {
            echo load_template($template, false);
        }
    }

}

new JobHunt_Elementor();
