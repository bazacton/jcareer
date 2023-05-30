<?php/** * @Start notification helper class * @return */if ( ! class_exists('CS_Plugin_Notification_Helper') ) {    class CS_Plugin_Notification_Helper {        public $message;        /**         * Start construct Functions         */        public function __construct() {            // Do Something here..            $this->message = esc_html__('No recored found', 'jobhunt');        }        /**         * Start Function for createing success Message         */        public function success($message = '') {            global $post;            if ( $message == '' ) {                $message = $this->message;            }            $output = '';            $output .= '<div class="col-md-12 cs-succ_mess"><p>';            $output .= $message;            $output .= '</p></div>';            echo force_balance_tags($output);        }        /**         * Start Function for createing Error Message         */        public function error($message = '') {            global $post;            if ( $message == '' ) {                $message = $this->message;            }            $output = '';            $output .= '<div class="col-md-12 cs-error"><p>';            $output .= $message;            $output .= '</p></div>';            echo force_balance_tags($output);        }        /**         * Start Function for createing Warning Message         */        public function warning($message = '') {            global $post;            if ( $message == '' ) {                $message = $this->message;            }            $output = '';            $output .= '<div class="col-md-12 cs-warning"><p>';            $output .= $message;            $output .= '</p></div>';            echo force_balance_tags($output);        }        /**         * Start Function for Giving the Information to you the user         */        public function informations($message = '') {            global $post;            if ( $message == '' ) {                $message = $this->message;            }            $output = '';            $output .= '<div class="col-md-12 cs-informations"><p>';            $output .= $message;            $output .= '</p></div>';            echo force_balance_tags($output);        }        /**         * Start Function for Giving the Information to you the user         */        public function info_msg($message = '', $classes = '', $before = '', $after = '') {            global $post;            if ( $message == '' ) {                $message = $this->message;            }            $output = '';            $class_str = '';            if ( $classes != '' ) {                $class_str .= ' class="' . $classes . '"';            }            $before_str = '';            if ( $before != '' ) {                $before_str .= $before;            }            $after_str = '';            if ( $after != '' ) {                $after_str .= $after;            }            $output .= $before_str;            $output .= '<span' . $class_str . '>';            $output .= $message;            $output .= '</span>';            $output .= $after_str;            echo force_balance_tags($output);        }    }    $cs_plugin_notify = new CS_Plugin_Notification_Helper();    global $cs_plugin_notify;}