<?php

namespace WPC\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jobhunt_Cv_Packages_Frontend;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Jobhunt_Cv_Packages extends Widget_Base {

    public function get_name() {
        return 'jobhunt_cv_packages';
    }

    public function get_title() {
        return 'CV Packages';
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
                'cv_package_title', [
                    'label' => __('Element Title', 'jobhunt-elementor'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => ''
                ]
        );
        
        
        $this->add_control(
                'cv_columns', [
            'label' => __('Columns', 'jobhunt-elementor'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                '2' => __('Two Columns', 'jobhunt-elementor'),
                '3' => __('Three Columns', 'jobhunt-elementor'),
                '4' => __('Four Columns', 'jobhunt-elementor'),
            ],
            'default' => '',
                ]
        );
        
        
        $cs_plugin_options = get_option('cs_plugin_options');
        $cs_cv_pkgs_options = isset($cs_plugin_options['cs_cv_pkgs_options']) ? $cs_plugin_options['cs_cv_pkgs_options'] : array();
        $cs_pkgs_options = array();
        if ( is_array($cs_cv_pkgs_options) && sizeof($cs_cv_pkgs_options) > 0 ) {
            
            foreach ( $cs_cv_pkgs_options as $cv_pkg_key => $cv_pkg ) {
                if ( isset($cv_pkg_key) && $cv_pkg_key <> '' ) {
                    $cv_pkg_id = isset($cv_pkg['cv_pkg_id']) ? $cv_pkg['cv_pkg_id'] : '';
                    $cv_pkg_title = isset($cv_pkg['cv_pkg_title']) ? $cv_pkg['cv_pkg_title'] : '';
                    $cs_pkgs_options[$cv_pkg_id] = __($cv_pkg_title, 'jobhunt-elementor');
                }
            }
        }
        
        
        
        $this->add_control(
                'cv_pkges', [
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
        $Jobhunt_Cv_Packages_Frontend = new Jobhunt_Cv_Packages_Frontend();
        $Jobhunt_Cv_Packages_Frontend->render($settings);
    }

    protected function content_template_bk() {
        //$control_uid = $this->get_control_uid( '{{settings.label_heading}}' );
        //pre($control_uid, false);
        ?>
        CV Packages
        <?php
    }

}
