<?php

namespace WPC\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jobhunt_Newsletter_Frontend;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Jobhunt_Newsletter extends Widget_Base
{

    public function get_name()
    {
        return 'jobhunt_newsletter';
    }

    public function get_title()
    {
        return 'Newsletter';
    }

    public function get_icon()
    {
        return 'eicon-single-post';
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
            'newsletter_title' , [
                'label' => 'Title' ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_newsletter_style' , [
                'label' => __('Style' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'simple' => __('Simple' , 'jobhunt-elementor') ,
                    'classic' => __('Classic' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'simple' ,
            ]
        );

        $this->add_control(
            'newsletter_content' , [
                'label' => __('Content' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXTAREA ,
                'default' => ''
            ]
        );


        $this->end_controls_section();
    }

    protected function render()
    {
        global $wp_query;
        $settings = $this->get_settings_for_display();
        $Jobhunt_Newsletter_Frontend = new Jobhunt_Newsletter_Frontend();
        $Jobhunt_Newsletter_Frontend->render($settings);
    }

    protected function content_template_bk()
    {
        //$control_uid = $this->get_control_uid( '{{settings.label_heading}}' );
        //pre($control_uid, false);
        ?>
        Newsletter
        <?php
    }

}
