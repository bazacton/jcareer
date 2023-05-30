<?php

namespace WPC\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jobhunt_CallToAction_Frontend;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Jobhunt_CallToAction extends Widget_Base
{

    public function get_name()
    {
        return 'jobhunt_calltoaction';
    }

    public function get_title()
    {
        return 'Call to Action';
    }

    public function get_icon()
    {
        return 'eicon-call-to-action';
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
            'cs_call_to_action_section_title' , [
                'label' => __('Element Title' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_call_action_title' , [
                'label' => __('Title' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_call_action_contents' , [
                'label' => __('Content' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXTAREA ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_contents_color' , [
                'label' => __('Title Color' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::COLOR ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_contents_bg_color' , [
                'label' => __('Background Color' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::COLOR ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_call_to_action_img' , [
                'label' => __('Background Image' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::MEDIA ,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src() ,
                ] ,
            ]
        );

        $this->add_control(
            'cs_call_to_action_icon_background_color' , [
                'label' => __('Button Background Color' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::COLOR ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_call_to_action_icon_text_color' , [
                'label' => __('Button Text Color' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::COLOR ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_call_to_action_button_text' , [
                'label' => __('Button Text' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_call_to_action_button_link' , [
                'label' => __('Button Link' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_call_action_text_align' , [
                'label' => __('Text Align' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'left' => __('Left' , 'jobcareer') ,
                    'right' => __('Right' , 'jobhunt-elementor') ,
                    'center' => __('Center' , 'jobhunt-elementor')
                ] ,
                'default' => 'left' ,
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        global $wp_query;
        $settings = $this->get_settings_for_display();
        $Jobhunt_CallToAction_Frontend = new Jobhunt_CallToAction_Frontend();
        $Jobhunt_CallToAction_Frontend->render($settings);
    }

    protected function content_template_bk()
    {
        //$control_uid = $this->get_control_uid( '{{settings.label_heading}}' );
        //pre($control_uid, false);
        ?>
        Call to Action
        <?php
    }

}
