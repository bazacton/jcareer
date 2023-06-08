<?php

namespace WPC\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jobhunt_Job_Packages_Frontend;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Jobhunt_Job_Packages extends Widget_Base {

    public function get_name() {
        return 'jobhunt_job_packages';
    }

    public function get_title() {
        return 'Job Packages';
    }

    public function get_icon() {
        return 'eicon-price-table';
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
                'job_package_title', [
                    'label' => __('Element Title', 'jobhunt-elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,    
                    'default' => ''
                ]
        );
        
        
        $this->add_control(
            'job_packages_columns', [
            'label' => __('Columns', 'jobhunt-elementor'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                '2' => __('Two Columns', 'jobhunt-elementor'),
                '3' => __('Three Columns', 'jobhunt-elementor'),
                '4' => __('Four Columns', 'jobhunt-elementor'),
            ],
            'default' => '2',
                ]
        );
        
        $this->add_control(
            'job_package_style', [
            'label' => __('Style', 'jobhunt-elementor'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'advance' => __('Box', 'jobhunt-elementor'),
                'basic' => __('Basic', 'jobhunt-elementor'),
                'classic' => __('Classic', 'jobhunt-elementor'),
                'fancy' => __('Fancy', 'jobhunt-elementor'),
                'modern' => __('Modern', 'jobhunt-elementor'),
                'simple' => __('Simple', 'jobhunt-elementor'),
            ],
            'default' => 'basic',
                ]
        );
        
        
        $cs_plugin_options = get_option('cs_plugin_options');
        $cs_packages_options = isset($cs_plugin_options['cs_packages_options']) ? $cs_plugin_options['cs_packages_options'] : '';
        
        
        $cs_pkgs_options = array();
        if ( is_array($cs_packages_options) && sizeof($cs_packages_options) > 0 ) {
            
            
            foreach ( $cs_packages_options as $package_key => $package ) {
                if ( isset($package_key) && $package_key <> '' ) {
                    $package_id = isset($package['package_id']) ? $package['package_id'] : '';
                    $package_title = isset($package['package_title']) ? $package['package_title'] : '';
                    $cs_pkgs_options[$package_id] = __($package_title, 'jobhunt-elementor');
                }
            }
            
        }
        
        
        
        $this->add_control(
            'job_pkges', [
                'label' => __('Packages', 'jobhunt-elementor'),
                'multiple'  => true,
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $cs_pkgs_options,
                'default' => '',
                ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        global $wp_query;
        $settings = $this->get_settings_for_display();
        $Jobhunt_Job_Packages_Frontend = new Jobhunt_Job_Packages_Frontend();
        $Jobhunt_Job_Packages_Frontend->render($settings);
    }

    protected function content_template_bk() {
        //$control_uid = $this->get_control_uid( '{{settings.label_heading}}' );
        //pre($control_uid, false);
        ?>
        Job Packages
        <?php
    }

}
