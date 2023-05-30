<?php

namespace WPC\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jobhunt_Testimonials_Frontend;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Jobhunt_Testimonials extends Widget_Base {

    public function get_name() {
        return 'jobhunt_testimonials';
    }

    public function get_title() {
        return 'Testimonials';
    }

    public function get_icon() {
        return 'eicon-testimonial-carousel';
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
            'cs_testimonial_section_title', [
                'label' => __('Element Title', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => ''
            ]
        );

        $this->add_control(
            'testimonial_author_color', [
                'label' => __('Author Color', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
            'testimonial_comp_color', [
                'label' => __('Company Color', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::COLOR,
            ]
        );

        $this->add_control(
            'testimonial_style', [
                'label' => __('Choose View', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'advance-slider' => __('Advance Slider', 'jobhunt-elementor'),
                    'classic' => __('Classic', 'jobhunt-elementor'),
                    'simple' => __('Modern', 'jobhunt-elementor'),
                    'modern-v2' => __('Modern V2', 'jobhunt-elementor'),
                    'modern-box' => __('Box Modern', 'jobhunt-elementor'),
                    'box' => __('Box', 'jobhunt-elementor'),
                    'fancy' => __('Fancy', 'jobhunt-elementor'),
                    'default-slider' => __('Default Slider', 'jobhunt-elementor'),
                ],
                'default' => 'simple',
            ]
        );

        $this->add_control(
            'testimonial_border', [
                'label' => __('Border', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'condition' => [
                    'testimonial_style' => 'classic',
                ],
                'options' => [
                    'yes' => __('Yes', 'jobhunt-elementor'),
                    'no' => __('No', 'jobhunt-elementor'),
                ],
                'default' => 'no',
            ]
        );

        $repeater = new \Elementor\Repeater();



        $repeater->add_control(
            'testimonial_text', [
                'label' => __('Text', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => ''
            ]
        );

        $repeater->add_control(
            'testimonial_author', [
                'label' => __('Author', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => ''
            ]
        );

        $repeater->add_control(
            'testimonial_company', [
                'label' => __('Company', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => ''
            ]
        );
        $repeater->add_control(
            'testimonial_facebook', [
                'label' => __('Facebook', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => ''
            ]
        );
        $repeater->add_control(
            'testimonial_twitter', [
                'label' => __('Twitter', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => ''
            ]
        );
        $repeater->add_control(
            'testimonial_google', [
                'label' => __('Google', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => ''
            ]
        );
        $repeater->add_control(
            'testimonial_img_user', [
                'label' => __('Image', 'jobhunt-elementor'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'testimonial_item',
            [
                'label' => __( 'Testimonial Items', 'jobhunt' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [],
                'title_field' => '{{{ testimonial_author }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        global $wp_query;
        $settings = $this->get_settings_for_display();
        $Jobhunt_Testimonials_Frontend = new Jobhunt_Testimonials_Frontend();
        $Jobhunt_Testimonials_Frontend->render($settings);
    }

    protected function content_template_bk() {
        //$control_uid = $this->get_control_uid( '{{settings.label_heading}}' );
        //pre($control_uid, false);
        ?>
        Testimonials
        <?php
    }

}
