<?php

namespace WPC\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jobhunt_Candidates_Frontend;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Jobhunt_Candidates extends Widget_Base
{

    public function get_name()
    {
        return 'jobhunt_candidates';
    }

    public function get_title()
    {
        return 'Candidates';
    }

    public function get_icon()
    {
        return 'eicon-post-list';
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
            'cs_candidate_title' , [
                'label' => 'Element Title' ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_candidate_view' , [
                'label' => __('Candidate Styles' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'grid' => __('Grid' , 'jobhunt-elementor') ,
                    'list' => __('List' , 'jobhunt-elementor') ,
                    'box' => __('Box' , 'jobhunt-elementor') ,
                    'modern' => __('Modern' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'grid' ,
            ]
        );

        $this->add_control(
            'cs_candidate_cols' , [
                'label' => __('Columns' , 'jobhunt-elementor') ,
                'condition' => ['cs_candidate_view' => 'grid'] ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    '1' => __('1 Column' , 'jobhunt-elementor') ,
                    '2' => __('2 Column' , 'jobhunt-elementor') ,
                    '3' => __('3 Column' , 'jobhunt-elementor') ,
                    '4' => __('4 Column' , 'jobhunt-elementor') ,
                    '6' => __('6 Column' , 'jobhunt-elementor') ,
                ] ,
                'default' => '' ,
            ]
        );

        $this->add_control(
            'cs_candidate_view_type' , [
                'label' => esc_html__('Box View' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SWITCHER ,
                'label_on' => esc_html__('On' , 'jobhunt-elementor') ,
                'label_off' => esc_html__('Off' , 'jobhunt-elementor') ,
                'return_value' => 'on' ,
                'default' => 'off' ,
            ]
        );

        $this->add_control(
            'cs_candidate_map' , [
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
            'cs_candidate_map_lat' , [
                'label' => __('Map Latitude' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'condition' => [
                    'cs_candidate_map' => 'yes' ,
                ] ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_candidate_map_long' , [
                'label' => __('Map Longitude' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'condition' => [
                    'cs_candidate_map' => 'yes' ,
                ] ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_candidate_map_zoom' , [
                'label' => __('Zoom Level' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'condition' => [
                    'cs_candidate_map' => 'yes' ,
                ] ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_candidate_map_height' , [
                'label' => __('Map Container Height ' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'condition' => [
                    'cs_candidate_map' => 'yes' ,
                ] ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_candidate_searchbox' , [
                'label' => __('Filterable' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'yes' => __('Yes' , 'jobhunt-elementor') ,
                    'no' => __('No' , 'jobhunt-elementor') ,
                ] ,
                'default' => '' ,
            ]
        );

        $this->add_control(
            'cs_candidate_searchbox_top' , [
                'label' => __('Search Box On Top' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'yes' => __('Yes' , 'jobhunt-elementor') ,
                    'no' => __('No' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'no' ,
            ]
        );


        $this->add_control(
            'cs_candidate_show_pagination' , [
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
            'cs_candidate_pagination' , [
                'label' => 'Records Per Page' ,
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
        $Jobhunt_Candidates_Frontend = new Jobhunt_Candidates_Frontend();
        $Jobhunt_Candidates_Frontend->render($settings);
    }

    protected function content_template_bk()
    {
        //$control_uid = $this->get_control_uid( '{{settings.label_heading}}' );
        //pre($control_uid, false);
        ?>
        Jobs Candidates
        <?php
    }

}
