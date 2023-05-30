<?php

namespace WPC\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jobhunt_Blog_Frontend;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Jobhunt_Blog extends Widget_Base
{

    public function get_name()
    {
        return 'jobhunt_blog';
    }

    public function get_title()
    {
        return 'Blog';
    }

    public function get_icon()
    {
        return 'eicon-post-list';
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
            'cs_blog_section_title' , [
                'label' => 'Element Title' ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_blog_element_subtitle' , [
                'label' => 'Element Title' ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_blog_alignment' , [
                'label' => __('Align' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'left' => __('Left' , 'jobhunt-elementor') ,
                    'center' => __('Center' , 'jobhunt-elementor') ,
                    'right' => __('Right' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'left' ,
            ]
        );

        $a_options = array();
        $a_options = jobcareer_jobcareer_show_all_cats('' , '' , '' , "category" , true);

        $this->add_control(
            'cs_blog_cat' , [
                'label' => __('Choose Category' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => $a_options ,
            ]
        );

        //Categories field

        $this->add_control(
            'cs_blog_view' , [
                'label' => __('Blog Views' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'classic' => __('Classic' , 'jobhunt-elementor') ,
                    'grid' => __('Grid' , 'jobhunt-elementor') ,
                    'large' => __('Large' , 'jobhunt-elementor') ,
                    'medium' => __('Medium' , 'jobhunt-elementor') ,
                    'modern' => __('Modern' , 'jobhunt-elementor') ,
                    'simple' => __('Grid Simple' , 'jobhunt-elementor') ,
                    'grid-modern' => __('Grid Modern' , 'jobhunt-elementor') ,
                    'grid-classic' => __('Grid Classic' , 'jobhunt-elementor') ,
                    'grid-fancy' => __('Grid Fancy' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'classic' ,
            ]
        );

        $this->add_control(
            'cs_blog_boxsize' , [
                'label' => __('Columns Size' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'condition' => [
                    'cs_blog_view' => array('grid' , 'modern' , 'simple' , 'grid-modern' , 'grid-classic') ,
                ] ,
                'options' => [
                    '12' => __('1 Columns' , 'jobhunt-elementor') ,
                    '6' => __('2 Columns' , 'jobhunt-elementor') ,
                    '4' => __('3 Columns' , 'jobhunt-elementor') ,
                    '3' => __('4 Columns' , 'jobhunt-elementor') ,
                ] ,
                'default' => '12' ,
            ]
        );


        $this->add_control(
            'cs_blog_orderby' , [
                'label' => __('Post Order by Date' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'ASC' => __('ASC' , 'jobhunt-elementor') ,
                    'DESC' => __('DESC' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'DESC' ,
            ]
        );

        $this->add_control(
            'cs_blog_description' , [
                'label' => __('Enable Post Description' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'yes' => __('Yes' , 'jobhunt-elementor') ,
                    'no' => __('No' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'no' ,
            ]
        );

        $this->add_control(
            'cs_blog_excerpt' , [
                'label' => 'Length of Excerpt in Words' ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'blog_pagination' , [
                'label' => __('Pagination' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'yes' => __('Yes' , 'jobhunt-elementor') ,
                    'no' => __('No' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'no' ,
            ]
        );

        $this->add_control(
            'cs_blog_num_post' , [
                'label' => 'No. of Post Per Page' ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'condition' => [
                    'blog_pagination' => 'yes' ,
                ] ,
                'default' => ''
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        global $wp_query;
        $settings = $this->get_settings_for_display();
        $Jobhunt_Blog_Frontend = new Jobhunt_Blog_Frontend();
        $Jobhunt_Blog_Frontend->render($settings);
    }

    protected function content_template_bk()
    {
        //$control_uid = $this->get_control_uid( '{{settings.label_heading}}' );
        //pre($control_uid, false);
        ?>
        Blog
        <?php
    }

}
