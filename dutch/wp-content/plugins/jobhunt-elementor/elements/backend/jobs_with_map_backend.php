<?php

namespace WPC\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jobhunt_JobsWithMap_Frontend;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Jobhunt_JobsWithMap extends Widget_Base
{

    public function get_name()
    {
        return 'jobhunt_jobswithmap';
    }

    public function get_title()
    {
        return 'Jobs With Map';
    }

    public function get_icon()
    {
        return 'eicon-google-maps';
    }

    public function get_categories()
    {
        return ['jobhunt'];
    }

    protected function _register_controls()
    {
        global $cs_plugin_options;

        $this->start_controls_section(
            'section_content' , [
                'label' => 'Settings' ,
            ]
        );

        $this->add_control(
            'jobs_map_element_title' , [
                'label' => __('Element Title' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'jobs_map_element_subtitle' , [
                'label' => __('Element Subtitle' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'jobs_maps_latitude' , [
                'label' => __('Map Latitude' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'jobs_map_longitude' , [
                'label' => __('Map Longitude' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'jobs_map_zoom_level' , [
                'label' => __('Zoom Level' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'jobs_map_container_height' , [
                'label' => __('Map Height' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'jobs_map_marker_icon' , [
                'label' => __('Jobs Marker Icon' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::MEDIA ,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src() ,
                ] ,
            ]
        );

        $this->add_control(
            'jobs_map_cluster_icon' , [
                'label' => __('Jobs Cluster Icon' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::MEDIA ,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src() ,
                ] ,
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        global $wp_query;
        $settings = $this->get_settings_for_display();
        $Jobhunt_JobsWithMap_Frontend = new Jobhunt_JobsWithMap_Frontend();
        $Jobhunt_JobsWithMap_Frontend->render($settings);
    }

    protected function content_template_bk()
    {
        //$control_uid = $this->get_control_uid( '{{settings.label_heading}}' );
        //pre($control_uid, false);
        ?>
        Jobs With Map
        <?php
    }

}
