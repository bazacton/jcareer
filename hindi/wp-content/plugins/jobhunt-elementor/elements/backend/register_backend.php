<?php

namespace WPC\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jobhunt_Register_Frontend;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Jobhunt_Register extends Widget_Base {

    public function get_name() {
        return 'jobhunt_register';
    }

    public function get_title() {
        return 'Register';
    }

    public function get_icon() {
        return 'eicon-lock-user';
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
            'candidate_register_element_title', [
                'label' => __('Element Title', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => ''
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        global $wp_query;
        $settings = $this->get_settings_for_display();
        $Jobhunt_Register_Frontend = new Jobhunt_Register_Frontend();
        $Jobhunt_Register_Frontend->render($settings);
    }

    protected function content_template_bk() {
        //$control_uid = $this->get_control_uid( '{{settings.label_heading}}' );
        //pre($control_uid, false);
        ?>
        Register
        <?php
    }

}
