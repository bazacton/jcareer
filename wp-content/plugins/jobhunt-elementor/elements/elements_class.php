<?php

namespace WPC;

// use Elementor\Plugin; ?????

class Widget_Loader
{

    private static $_instance = null;

    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function include_widgets_files()
    {
        //Jobs Element
        require_once(__DIR__ . '/backend/jobs_backend.php');
        require_once(__DIR__ . '/frontend/jobs_frontend.php');

        //Candidates Element
        require_once(__DIR__ . '/backend/candidates_backend.php');
        require_once(__DIR__ . '/frontend/candidates_frontend.php');

        //Employers Element
        require_once(__DIR__ . '/backend/employers_backend.php');
        require_once(__DIR__ . '/frontend/employers_frontend.php');

        //CV Packages Element
        require_once(__DIR__ . '/backend/cv_packages_backend.php');
        require_once(__DIR__ . '/frontend/cv_packages_frontend.php');

        //JOB Packages Element
        require_once(__DIR__ . '/backend/job_packages_backend.php');
        require_once(__DIR__ . '/frontend/job_packages_frontend.php');

        //Apply JOB Packages Element
        require_once(__DIR__ . '/backend/apply_job_packages_backend.php');
        require_once(__DIR__ . '/frontend/apply_job_packages_frontend.php');

        //JOB Specialisms
        require_once(__DIR__ . '/backend/job_specialisms_backend.php');
        require_once(__DIR__ . '/frontend/job_specialisms_frontend.php');

        //JOBS Search
        require_once(__DIR__ . '/backend/jobs_search_backend.php');
        require_once(__DIR__ . '/frontend/jobs_search_frontend.php');

        //Testimonials
        require_once(__DIR__ . '/backend/testimonials_backend.php');
        require_once(__DIR__ . '/frontend/testimonials_frontend.php');

        //Register
        require_once(__DIR__ . '/backend/register_backend.php');
        require_once(__DIR__ . '/frontend/register_frontend.php');

        //Blog
        require_once(__DIR__ . '/backend/blog_backend.php');
        require_once(__DIR__ . '/frontend/blog_frontend.php');

        //Price Table
        require_once(__DIR__ . '/backend/price_table_backend.php');
        require_once(__DIR__ . '/frontend/price_table_frontend.php');

        //Newsletter
        require_once(__DIR__ . '/backend/newsletter_backend.php');
        require_once(__DIR__ . '/frontend/newsletter_frontend.php');

        //Call to Action
        require_once(__DIR__ . '/backend/call_to_action_backend.php');
        require_once(__DIR__ . '/frontend/call_to_action_frontend.php');

        //Jobs With Map
        require_once(__DIR__ . '/backend/jobs_with_map_backend.php');
        require_once(__DIR__ . '/frontend/jobs_with_map_frontend.php');

        //Listing Tab
        require_once(__DIR__ . '/backend/listing_tab_backend.php');
        require_once(__DIR__ . '/frontend/listing_tab_frontend.php');

    }

    public function register_widgets()
    {

        $this->include_widgets_files();

        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Jobhunt_Jobs());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Jobhunt_Candidates());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Jobhunt_Employers());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Jobhunt_Cv_Packages());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Jobhunt_Job_Packages());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Jobhunt_Apply_Job_Packages());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Jobhunt_Job_Specialisms());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Jobhunt_Jobs_Search());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Jobhunt_Testimonials());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Jobhunt_Register());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Jobhunt_Blog());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Jobhunt_PriceTable());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Jobhunt_Newsletter());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Jobhunt_CallToAction());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Jobhunt_JobsWithMap());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Widgets\Jobhunt_ListingTab());
    }

    public function add_elementor_widget_categories($elements_manager)
    {

        $elements_manager->add_category(
            'jobhunt' , [
                'title' => __('JOBHUNT' , 'jobhunt') ,
                'icon' => 'eicon-font' ,
            ]
        );
    }

    public function __construct()
    {
        add_action('elementor/widgets/widgets_registered' , [$this , 'register_widgets'] , 99);
        add_action('elementor/elements/categories_registered' , array($this , 'add_elementor_widget_categories'));
        add_action('elementor/documents/register_controls' , array($this , 'jobhunt_register_page_settings_sub_header_callback'));
    }

    function jobhunt_register_page_settings_sub_header_callback($document)
    {


        $document->start_controls_section(
            'jobhunt_page_options_sub_header' , [
                'label' => esc_html__('JOBHUNT: Subheader Options' , 'jobhunt-elementor') ,
                'tab' => \Elementor\Controls_Manager::TAB_SETTINGS ,
            ]
        );


        $document->add_control(
            'cs_header_banner_style' , [
                'label' => __('Choose Sub-Header' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'default_header' => __('Default Subheader' , 'jobhunt-elementor') ,
                    'breadcrumb_header' => __('Custom Subheader' , 'jobhunt-elementor') ,
                    'custom_slider' => __('Revolution Slider' , 'jobhunt-elementor') ,
                    'map' => __('Map' , 'jobhunt-elementor') ,
                    'no-header' => __('No Subheader' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'default_header' ,
            ]
        );

        /*
         * Custom Subheader Fields Start
         */

        $document->add_control(
            'cs_page_title_align' , [
                'label' => __('Text Align' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'condition' => [
                    'cs_header_banner_style' => 'breadcrumb_header' ,
                ] ,
                'options' => [
                    'left' => __('Left' , 'jobhunt-elementor') ,
                    'center' => __('Center' , 'jobhunt-elementor') ,
                    'right' => __('Right' , 'jobhunt-elementor') ,
                    'bottom' => __('Bottom' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'left' ,
            ]
        );


        $document->add_control(
            'cs_page_title' , [
                'label' => esc_html__('Title' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SWITCHER ,
                'condition' => [
                    'cs_header_banner_style' => 'breadcrumb_header' ,
                ] ,
                'label_on' => esc_html__('On' , 'jobhunt-elementor') ,
                'label_off' => esc_html__('Off' , 'jobhunt-elementor') ,
                'return_value' => 'on' ,
                'default' => 'off' ,
            ]
        );

        $document->add_control(
            'cs_page_subheading_title' , [
                'label' => __('Subtitle' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'condition' => [
                    'cs_header_banner_style' => 'breadcrumb_header' ,
                ] ,
                'default' => ''
            ]
        );

        $document->add_control(
            'cs_page_breadcrumbs' , [
                'label' => esc_html__('Breadcrumbs' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SWITCHER ,
                'condition' => [
                    'cs_header_banner_style' => 'breadcrumb_header' ,
                ] ,
                'label_on' => esc_html__('On' , 'jobhunt-elementor') ,
                'label_off' => esc_html__('Off' , 'jobhunt-elementor') ,
                'return_value' => 'on' ,
                'default' => 'off' ,
            ]
        );

        $document->add_control(
            'cs_subheader_padding_top' , [
                'label' => __('Padding Top' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'condition' => [
                    'cs_header_banner_style' => 'breadcrumb_header' ,
                ] ,
                'default' => ''
            ]
        );

        $document->add_control(
            'cs_subheader_padding_bottom' , [
                'label' => __('Padding Bottom' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'condition' => [
                    'cs_header_banner_style' => 'breadcrumb_header' ,
                ] ,
                'default' => ''
            ]
        );

        $document->add_control(
            'cs_page_subheader_color' , [
                'label' => __('Background Color' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::COLOR ,
                'condition' => [
                    'cs_header_banner_style' => 'breadcrumb_header' ,
                ] ,
            ]
        );

        $document->add_control(
            'cs_page_subheader_text_color' , [
                'label' => __('Text Color' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::COLOR ,
                'condition' => [
                    'cs_header_banner_style' => 'breadcrumb_header' ,
                ] ,
            ]
        );

        $document->add_control(
            'cs_page_subheader_no_image' , [
                'label' => esc_html__('Show Image' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SWITCHER ,
                'condition' => [
                    'cs_header_banner_style' => 'breadcrumb_header' ,
                ] ,
                'label_on' => esc_html__('On' , 'jobhunt-elementor') ,
                'label_off' => esc_html__('Off' , 'jobhunt-elementor') ,
                'return_value' => 'on' ,
                'default' => 'off' ,
            ]
        );


        $document->add_control(
            'cs_header_banner_image' , [
                'label' => esc_html__('Background Image' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::MEDIA ,
                'condition' => [
                    'cs_header_banner_image' => 'breadcrumb_header' ,
                ] ,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src() ,
                ] ,
            ]
        );

        $document->add_control(
            'cs_page_subheader_parallax' , [
                'label' => esc_html__('Parallax Background Image' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SWITCHER ,
                'condition' => [
                    'cs_header_banner_style' => 'breadcrumb_header' ,
                ] ,
                'label_on' => esc_html__('On' , 'jobhunt-elementor') ,
                'label_off' => esc_html__('Off' , 'jobhunt-elementor') ,
                'return_value' => 'on' ,
                'default' => 'off' ,
            ]
        );

        /*
         * Custom Subheader Fields Ends
         */


        /*
         * Slider Fields Starts
         */

        $sliders_array = array();
        if (class_exists('RevSlider') && class_exists('jobcareer_revSlider')) {

            $slider = new \jobcareer_revSlider();
            $arrSliders = $slider->getAllSliderAliases();

            if (is_array($arrSliders)) {
                foreach ($arrSliders as $key => $entry) {
                    $sliders_array[$entry['alias']] = $entry['title'];
                }
            }
        }

        $document->add_control(
            'cs_custom_slider_id' , [
                'label' => __('Slider' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'condition' => [
                    'cs_header_banner_style' => 'custom_slider' ,
                ] ,
                'options' => $sliders_array ,
                'default' => '' ,
            ]
        );


        /*
         * Slider Fields Ends
         */


        /*
         * Map Fields Starts
         */

        $document->add_control(
            'cs_custom_map' , [
                'label' => __('Custom Map Short Code' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXTAREA ,
                'condition' => [
                    'cs_header_banner_style' => 'map' ,
                ] ,
                'rows' => 5 ,
                'default' => ''
            ]
        );


        /*
         * Map Fields Ends
         */


        $document->end_controls_section();
    }

}

// Instantiate Plugin Class
Widget_Loader::instance();
