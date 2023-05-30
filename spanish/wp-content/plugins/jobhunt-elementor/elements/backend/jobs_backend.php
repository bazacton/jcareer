<?php

namespace WPC\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jobhunt_Jobs_Frontend;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Jobhunt_Jobs extends Widget_Base
{

    public function get_name()
    {
        return 'jobhunt_jobs';
    }

    public function get_title()
    {
        return 'Jobs';
    }

    public function get_icon()
    {
        return 'eicon-bullet-list';
    }

    public function get_categories()
    {
        return ['jobhunt'];
    }

    protected function _register_controls()
    {

        $this->start_controls_section(
            'section_content' , [
                'label' => 'Settings' ,
            ]
        );

        $this->add_control(
            'cs_job_title' , [
                'label' => __('Element Title' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );
        $this->add_control(
            'cs_job_sub_title' , [
                'label' => __('Section Sub Title' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_job_view' , [
                'label' => __('Job View' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'advance' => __('Advance' , 'jobhunt-elementor') ,
                    'classic' => __('Classic' , 'jobhunt-elementor') ,
                    'detail' => __('Detail' , 'jobhunt-elementor') ,
                    'fancy' => __('Fancy' , 'jobhunt-elementor') ,
                    'grid' => __('Grid' , 'jobhunt-elementor') ,
                    'modren' => __('Modren' , 'jobhunt-elementor') ,
                    'simple' => __('Simple' , 'jobhunt-elementor') ,
                    'modernv1' => __('Modern V1' , 'jobhunt-elementor') ,
                    'modernv2' => __('Modern V2' , 'jobhunt-elementor') ,
                    'modernv3' => __('Modern V3' , 'jobhunt-elementor') ,
                    'modernv4' => __('Modern V4' , 'jobhunt-elementor') ,
                    'boxed' => __('Boxed' , 'jobhunt-elementor') ,
                    'grid_classic' => __('Grid Classic' , 'jobhunt-elementor') ,
                    'grid_slider' => __('Grid Slider' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'classic' ,
            ]
        );

        $this->add_control(
            'cs_job_top_search' , [
                'label' => __('Top Content' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'none' => __('None' , 'jobhunt-elementor') ,
                    'section_title' => __('Element Title' , 'jobhunt-elementor') ,
                    'total_records' => __('Total Records with Title' , 'jobhunt-elementor') ,
                    'Filters' => __('Filters' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'none' ,
            ]
        );

        $this->add_control(
            'cs_job_searchbox' , [
                'label' => __('Search Box' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'yes' => __('Yes' , 'jobhunt-elementor') ,
                    'no' => __('No' , 'jobhunt-elementor') ,
                ] ,
                'default' => '' ,
            ]
        );

        $this->add_control(
            'cs_job_map' , [
                'label' => __('Map on Top' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'yes' => __('Yes' , 'jobhunt-elementor') ,
                    'no' => __('No' , 'jobhunt-elementor') ,
                ] ,
                'default' => '' ,
            ]
        );

        $this->add_control(
            'cs_job_result_type' , [
                'label' => __('Result Type' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'all' => __('All' , 'jobhunt-elementor') ,
                    'featured' => __('Featured Only' , 'jobhunt-elementor') ,
                ] ,
                'default' => '' ,
            ]
        );

        $this->add_control(
            'cs_job_alert_button' , [
                'label' => __('Job Alert Shortcode' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'enable' => __('Enable' , 'jobhunt-elementor') ,
                    'disable' => __('Disable' , 'jobhunt-elementor') ,
                ] ,
                'default' => '' ,
            ]
        );

        $this->add_control(
            'cs_job_show_pagination' , [
                'label' => __('Pagination' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'pagination' => __('Pagination' , 'jobhunt-elementor') ,
                    'single_page' => __('Single Page' , 'jobhunt-elementor') ,
                ] ,
                'default' => '' ,
            ]
        );

        $this->add_control(
            'cs_job_pagination' , [
                'label' => __('Post Per Page' , 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );


        $this->end_controls_section();
    }

    protected function render()
    {
        global $wp_query;
        $settings = $this->get_settings_for_display();
        $Jobhunt_Jobs_Frontend = new Jobhunt_Jobs_Frontend();
        $Jobhunt_Jobs_Frontend->render($settings);
    }

    protected function content_template_bk()
    {
        //$control_uid = $this->get_control_uid( '{{settings.label_heading}}' );
        //pre($control_uid, false);
        ?>
        Jobs Listing
        <?php
    }

}
