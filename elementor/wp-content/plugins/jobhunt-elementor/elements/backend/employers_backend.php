<?php

namespace WPC\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jobhunt_Employers_Frontend;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Jobhunt_Employers extends Widget_Base
{

    public function get_name()
    {
        return 'jobhunt_employers';
    }

    public function get_title()
    {
        return 'Employers';
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
            'cs_employer_title' , [
                'label' => 'Element Title' ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_employer_sub_title' , [
                'label' => 'Element Sub Title' ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );


        $this->add_control(
            'cs_employer_searchbox' , [
                'label' => __('Search Box (sidebar)' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'yes' => __('Yes' , 'jobhunt-elementor') ,
                    'no' => __('No' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'No' ,
            ]
        );


        $this->add_control(
            'cs_employer_searchbox_top' , [
                'label' => __('Alphabatical Filter (top)' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'yes' => __('Yes' , 'jobhunt-elementor') ,
                    'no' => __('No' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'yes' ,
            ]
        );

        $this->add_control(
            'cs_employer_view' , [
                'label' => __('Employer View' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'alphabatic' => __('Alphabatic' , 'jobhunt-elementor') ,
                    'box' => __('Box' , 'jobhunt-elementor') ,
                    'fancy' => __('Fancy' , 'jobhunt-elementor') ,
                    'grid' => __('Grid' , 'jobhunt-elementor') ,
                    'list' => __('List' , 'jobhunt-elementor') ,
                    'simple' => __('Simple' , 'jobhunt-elementor') ,
                    'modern' => __('Modern' , 'jobhunt-elementor') ,
                ] ,
                'default' => '' ,
            ]
        );

        $this->add_control(
            'cs_employer_cols' , [
                'label' => __('No of Cols' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'condition' => [
                    'cs_employer_view' => array('box' , 'fancy' , 'grid' , 'modern') ,
                ] ,
                'options' => [
                    '12' => __('1' , 'jobhunt-elementor') ,
                    '6' => __('2' , 'jobhunt-elementor') ,
                    '3' => __('4' , 'jobhunt-elementor') ,
                    '2' => __('6' , 'jobhunt-elementor') ,
                ] ,
                'default' => '12' ,
            ]
        );

        $this->add_control(
            'cs_employer_all_companies' , [
                'label' => 'See All Companies url' ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'condition' => [
                    'cs_employer_view' => 'modern' ,
                ] ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_employer_show_pagination' , [
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
            'cs_employer_pagination' , [
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
        $Jobhunt_Employers_Frontend = new Jobhunt_Employers_Frontend();
        $Jobhunt_Employers_Frontend->render($settings);
    }

    protected function content_template_bk()
    {
        //$control_uid = $this->get_control_uid( '{{settings.label_heading}}' );
        //pre($control_uid, false);
        ?>
        Jobs Employers
        <?php
    }

}
