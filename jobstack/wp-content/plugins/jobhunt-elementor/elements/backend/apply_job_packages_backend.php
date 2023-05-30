<?php

namespace WPC\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jobhunt_Apply_Job_Packages_Frontend;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Jobhunt_Apply_Job_Packages extends Widget_Base {

    public function get_name() {
        return 'jobhunt_apply_job_packages';
    }

    public function get_title() {
        return 'Apply Job Packages';
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
                'membership_package_title', [
            'label' => __('Element Title', 'jobhunt-elementor'),
            'type' => \Elementor\Controls_Manager::TEXT,
            'default' => ''
                ]
        );


        $this->add_control(
                'membership_package_columns', [
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
                'membership_package_style', [
            'label' => __('Style', 'jobhunt-elementor'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'advance' => __('Box', 'jobhunt-elementor'),
                'classic' => __('classic', 'jobhunt-elementor'),
                'fancy' => __('fancy', 'jobhunt-elementor'),
                'modern' => __('modern', 'jobhunt-elementor'),
            ],
            'default' => 'classic',
                ]
        );


        $cs_plugin_options = get_option('cs_plugin_options');
        $cs_membership_packages_options = isset($cs_plugin_options['cs_membership_pkgs_options']) ? $cs_plugin_options['cs_membership_pkgs_options'] : '';


        $cs_membership_pkgs_options = array();
        if (is_array($cs_membership_packages_options) && sizeof($cs_membership_packages_options) > 0) {
            
            foreach ( $cs_membership_packages_options as $package_key => $package ) {
                if ( isset($package_key) && $package_key <> '' ) {
                    $package_id = isset($package['membership_pkg_id']) ? $package['membership_pkg_id'] : '';
                    $package_title = isset($package['memberhsip_pkg_title']) ? $package['memberhsip_pkg_title'] : '';

                    $cs_membership_pkgs_options[$package_id] = __($package_title, 'jobhunt-elementor');
                }
            }
        }



        $this->add_control(
                'membership_packages', [
            'label' => __('Packages', 'jobhunt-elementor'),
            'multiple' => true,
            'type' => \Elementor\Controls_Manager::SELECT2,
            'options' => $cs_membership_pkgs_options,
            'default' => '',
                ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        global $wp_query;
        $settings = $this->get_settings_for_display();
        $Jobhunt_Apply_Job_Packages_Frontend = new Jobhunt_Apply_Job_Packages_Frontend();
        $Jobhunt_Apply_Job_Packages_Frontend->render($settings);
    }

    protected function content_template_bk() {
        //$control_uid = $this->get_control_uid( '{{settings.label_heading}}' );
        //pre($control_uid, false);
        ?>
        Apply Job Packages
        <?php
    }

}
