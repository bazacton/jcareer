<?php

namespace WPC\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jobhunt_Job_Specialisms_Frontend;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Jobhunt_Job_Specialisms extends Widget_Base {

    public function get_name() {
        return 'jobhunt_job_specialisms';
    }

    public function get_title() {
        return 'Job Specialisms';
    }

    public function get_icon() {
        return 'eicon-bullet-list';
    }

    public function get_categories() {
        return ['jobhunt'];
    }

    protected function _register_controls() {
        global $cs_plugin_options;

        $this->start_controls_section(
                'section_content', [
            'label' => 'Settings',
                ]
        );

        $this->add_control(
                'job_specialisms_title', [
            'label' => __('Element Title', 'jobhunt-elementor'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => ''
                ]
        );
        
         $this->add_control(
                'job_specialisms_title_align', [
            'label' => __('Element Title Align', 'jobhunt-elementor'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'left' => __('Left', 'jobhunt-elementor'),
                'center' => __('Center', 'jobhunt-elementor'),
                'right' => __('Right', 'jobhunt-elementor'),
            ],
            'default' => 'Center',
                ]
        );
         
        $this->add_control(
                'job_specialisms_content', [
            'label' => 'Content',
            'type' => \Elementor\Controls_Manager::TEXTAREA,
            'default' => ''
                ]
        ); 
        
        
        $this->add_control(
                'specialisms_view', [
            'label' => __('Styles', 'jobhunt-elementor'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'classic' => __('Classic', 'jobhunt-elementor'),
                'classic-list' => __('Classic List', 'jobhunt-elementor'),
                'modern' => __('Modern', 'jobhunt-elementor'),
                'grid' => __('Grid', 'jobhunt-elementor'),
                'fancy' => __('Fancy', 'jobhunt-elementor'),
                'simple' => __('Simple', 'jobhunt-elementor'),
                'grid-fancy' => __('Grid Fancy', 'jobhunt-elementor'),
                'grid-fancy-v2' => __('Grid Fancy V2', 'jobhunt-elementor'),
                'grid-modern' => __('Grid Modern', 'jobhunt-elementor'),
            ],
            'default' => 'classic',
                ]
        );
        
        $this->add_control(
                'specialisms_columns', [
            'label' => __('Columns', 'jobhunt-elementor'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                '2' => __('Two Columns', 'jobhunt-elementor'),
                '3' => __('Three Columns', 'jobhunt-elementor'),
                '4' => __('Four Columns', 'jobhunt-elementor'),
                '6' => __('Six Columns', 'jobhunt-elementor'),
            ],
            'default' => '3',
                ]
        );
        
        $this->add_control(
                'job_specialisms_subtitle_switch', [
            'label' => __('Total Jobs Count', 'jobhunt-elementor'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'yes' => __('Yes', 'jobhunt-elementor'),
                'no' => __('No', 'jobhunt-elementor'),
            ],
            'default' => 'no',
                ]
        );
        
        
        
        $cs_all_cats = get_categories('taxonomy=specialisms&child_of=0&hide_empty=0');
        if (is_array($cs_all_cats) && sizeof($cs_all_cats) > 0) {
            foreach ( $cs_all_cats as $cs_cat ) {
                $cs_spce_options[$cs_cat->slug] = __($cs_cat->name, 'jobhunt-elementor');
            }
        }
        $this->add_control(
                'spec_cats', [
            'label' => __('Specialisms', 'jobhunt-elementor'),
            'multiple' => true,
            'type' => \Elementor\Controls_Manager::SELECT2,
            'options' => $cs_spce_options,
            'default' => '',
                ]
        );
        
        
        $this->add_control(
                'job_specialisms_view_all_link', [
            'label' => 'All Specialisms Link',
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => ''
                ]
        ); 
        
        

        $this->end_controls_section();
    }

    protected function render() {
        global $wp_query;
        $settings = $this->get_settings_for_display();
        $Jobhunt_Job_Specialisms_Frontend = new Jobhunt_Job_Specialisms_Frontend();
        $Jobhunt_Job_Specialisms_Frontend->render($settings);
    }

    protected function content_template_bk() {
        //$control_uid = $this->get_control_uid( '{{settings.label_heading}}' );
        //pre($control_uid, false);
        ?>
        Job Specialisms
        <?php
    }

}
