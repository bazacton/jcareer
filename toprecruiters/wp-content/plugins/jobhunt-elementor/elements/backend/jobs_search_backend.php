<?php

namespace WPC\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jobhunt_Jobs_Search_Frontend;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Jobhunt_Jobs_Search extends Widget_Base {

    public function get_name() {
        return 'jobhunt_jobs_search';
    }

    public function get_title() {
        return 'Jobs Search';
    }

    public function get_icon() {
        return 'eicon-site-search';
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
            'jobs_search_title', [
                'label' => __('Element Title', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => ''
            ]
        );

        $this->add_control(
            'job_search_style', [
                'label' => __('View', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'career' => __('Default', 'jobhunt-elementor'),
                    'simple' => __('Simple', 'jobhunt-elementor'),
                    'modren' => __('Modern', 'jobhunt-elementor'),
                    'modren_v2' => __('Modern V2', 'jobhunt-elementor'),
                    'classic' => __('Classic', 'jobhunt-elementor'),
                    'fancy' => __('Fancy', 'jobhunt-elementor'),
                    'default_fancy' => __('Default Fancy', 'jobhunt-elementor'),
                ],
                'default' => 'career',
            ]
        );

        $this->add_control(
            'job_search_layout_bg', [
                'label' => __('Background Color', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
            'job_search_layout_heading_color', [
                'label' => __('Heading Color', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
            'job_search_title_field_switch', [
                'label' => __('Keyword Title', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'yes' => __('Yes', 'jobhunt-elementor'),
                    'no' => __('No', 'jobhunt-elementor'),
                ],
                'default' => 'no',
            ]
        );

        $this->add_control(
            'job_search_specialisam_field_switch', [
                'label' => __('Specialisms Field', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'yes' => __('Yes', 'jobhunt-elementor'),
                    'no' => __('No', 'jobhunt-elementor'),
                ],
                'default' => 'no',
            ]
        );

        $this->add_control(
            'job_search_location_field_switch', [
                'label' => __('Location Field', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'yes' => __('Yes', 'jobhunt-elementor'),
                    'no' => __('No', 'jobhunt-elementor'),
                ],
                'default' => 'no',
            ]
        );

        $this->add_control(
            'job_lable_switch', [
                'label' => __('Labels on/off', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'yes' => __('Yes', 'jobhunt-elementor'),
                    'no' => __('No', 'jobhunt-elementor'),
                ],
                'default' => 'no',
            ]
        );

        $this->add_control(
            'job_search_hint_switch', [
                'label' => __('Hint text on/off', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'yes' => __('Yes', 'jobhunt-elementor'),
                    'no' => __('No', 'jobhunt-elementor'),
                ],
                'default' => 'no',
            ]
        );

        $this->add_control(
            'job_advance_search_switch', [
                'label' => __('Advance Search on/off', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'yes' => __('Yes', 'jobhunt-elementor'),
                    'no' => __('No', 'jobhunt-elementor'),
                ],
                'default' => 'no',
            ]
        );

        $this->add_control(
            'job_advance_search_url', [
                'label' => __('URL', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'condition' => [
                    'job_advance_search_switch' => 'yes',
                ],
                'default' => ''
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        global $wp_query;
        $settings = $this->get_settings_for_display();
        $Jobhunt_Jobs_Search_Frontend = new Jobhunt_Jobs_Search_Frontend();
        $Jobhunt_Jobs_Search_Frontend->render($settings);
    }

    protected function content_template_bk() {
        //$control_uid = $this->get_control_uid( '{{settings.label_heading}}' );
        //pre($control_uid, false);
        ?>
        Jobs Search
        <?php
    }

}
