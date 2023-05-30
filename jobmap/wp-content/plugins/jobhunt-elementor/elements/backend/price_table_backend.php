<?php

namespace WPC\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Jobhunt_PriceTable_Frontend;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Jobhunt_PriceTable extends Widget_Base
{

    public function get_name()
    {
        return 'jobhunt_pricetable';
    }

    public function get_title()
    {
        return 'Price Table';
    }

    public function get_icon()
    {
        return 'eicon-price-table';
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
            'jobcareer_multi_price_table_section_title' , [
                'label' => __('Element Title' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $this->add_control(
            'cs_multi_price_col' , [
                'label' => __('Column' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    '1' => __('1 Column' , 'jobhunt-elementor') ,
                    '2' => __('2 Columns' , 'jobhunt-elementor') ,
                    '3' => __('3 Columns' , 'jobhunt-elementor') ,
                    '4' => __('4 Columns' , 'jobhunt-elementor') ,
                    '6' => __('6 Columns' , 'jobhunt-elementor') ,
                ] ,
                'default' => '3' ,
            ]
        );

        $repeater = new \Elementor\Repeater();


        $repeater->add_control(
            'multi_price_table_text' , [
                'label' => __('Title' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $repeater->add_control(
            'multi_price_table_title_color' , [
                'label' => __('Title Color' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::COLOR ,
            ]
        );

        $repeater->add_control(
            'multi_pricetable_price' , [
                'label' => __('Price' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $repeater->add_control(
            'multi_price_table_currency' , [
                'label' => __('Currency Symbols' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $repeater->add_control(
            'multi_price_table_time_duration' , [
                'label' => __('Time Duration' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $repeater->add_control(
            'button_link' , [
                'label' => __('Button Link' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );

        $repeater->add_control(
            'multi_price_table_button_text' , [
                'label' => __('Button Text' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXT ,
                'default' => ''
            ]
        );


        $repeater->add_control(
            'multi_price_table_button_color' , [
                'label' => __('Button Color' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::COLOR ,
                'default' => ''
            ]
        );


        $repeater->add_control(
            'multi_price_table_button_column_color' , [
                'label' => __('Button Background Color' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::COLOR ,
                'default' => ''
            ]
        );

        $repeater->add_control(
            'pricetable_featured' , [
                'label' => __('Featured on/off' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::SELECT ,
                'options' => [
                    'Yes' => __('Yes' , 'jobhunt-elementor') ,
                    'No' => __('No' , 'jobhunt-elementor') ,
                ] ,
                'default' => 'No' ,
            ]
        );

        $repeater->add_control(
            'pricing_features' , [
                'label' => __('Description' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::TEXTAREA ,
                'default' => ''
            ]
        );

        $repeater->add_control(
            'multi_price_table_column_bgcolor' , [
                'label' => __('column Background Color' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::COLOR ,
                'default' => ''
            ]
        );

        $this->add_control(
            'pricetable_item' ,
            [
                'label' => __('Table Items' , 'jobhunt-elementor') ,
                'type' => \Elementor\Controls_Manager::REPEATER ,
                'fields' => $repeater->get_controls() ,
                'default' => [] ,
                'title_field' => '{{{ multi_price_table_text }}}' ,
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        global $wp_query;
        $settings = $this->get_settings_for_display();
        $Jobhunt_PriceTable_Frontend = new Jobhunt_PriceTable_Frontend();
        $Jobhunt_PriceTable_Frontend->render($settings);
    }

    protected function content_template_bk()
    {
        //$control_uid = $this->get_control_uid( '{{settings.label_heading}}' );
        //pre($control_uid, false);
        ?>
        Price Table
        <?php
    }

}
