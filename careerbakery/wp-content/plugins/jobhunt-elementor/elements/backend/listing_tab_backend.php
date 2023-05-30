<?php

namespace WPC\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jobhunt_ListingTab_Frontend;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Jobhunt_ListingTab extends Widget_Base
{

    public function get_name()
    {
        return 'jobhunt_listingtab';
    }

    public function get_title()
    {
        return 'Listing Tab';
    }

    public function get_icon()
    {
        return 'eicon-tabs';
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
            'listing_tab_element_title' , [
                'label' => __('Element Title' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'listing_tab_element_subtitle' , [
                'label' => __('Element Subtitle' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'listing_tab_post_per_tab' , [
                'label' => __('Posts Per Tab' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );


        $this->add_control(
            'listing_tab_job_tab_switch' , [
                'label' => __('Jobs Tab' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'yes' => __('Yes' , 'jobhunt-elementor') ,
                    'no' => __('No' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'no' ,
            ]
        );

        $this->add_control(
            'listing_tab_candidate_tab_switch' , [
                'label' => __('Candidate Tab' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'yes' => __('Yes' , 'jobhunt-elementor') ,
                    'no' => __('No' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'no' ,
            ]
        );

        $this->add_control(
            'listing_tab_employer_tab_switch' , [
                'label' => __('Employer Tab' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'yes' => __('Yes' , 'jobhunt-elementor') ,
                    'no' => __('No' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'no' ,
            ]
        );

        $this->add_control(
            'listing_tab_sidebar_switch' , [
                'label' => __('Tab Sidebar' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'yes' => __('Yes' , 'jobhunt-elementor') ,
                    'no' => __('No' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'no' ,
            ]
        );

        $sidebar_list = array('' => esc_html__('Select Sidebar' , 'jobhunt-elementor'));
        foreach ($GLOBALS['wp_registered_sidebars'] as $sidebar) {
            $sidebar_list[$sidebar['id']] = $sidebar['name'];
        }

        $this->add_control(
            'listing_tab_sidebar_select' , [
                'label' => __('Select Sidebar' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'condition' => [
                    'listing_tab_sidebar_switch' => 'yes' ,
                ] ,
                'options' => $sidebar_list ,
                'default' => '' ,
            ]
        );


        $this->end_controls_section();
    }

    protected function render()
    {
        global $wp_query;
        $settings = $this->get_settings_for_display();
        $Jobhunt_ListingTab_Frontend = new Jobhunt_ListingTab_Frontend();
        $Jobhunt_ListingTab_Frontend->render($settings);
    }

    protected function content_template_bk()
    {
        //$control_uid = $this->get_control_uid( '{{settings.label_heading}}' );
        //pre($control_uid, false);
        ?>
        Listing Tab
        <?php
    }

}
