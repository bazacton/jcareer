<?php
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Job_Hunt_Indeed_API
 */
class Job_Hunt_Indeed_API {

    /**
     * Get jobs from the indeed API
     * @return array()
    */
    public static function get_jobs_from_indeed($args) {
        
        // default indeed api arguments
        $default_args = array(
            'v' => 2,
            'format' => 'json',
            'radius' => 25,
            'start' => 0,
            'latlong' => 1
        );
        // getting user ip
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $default_args['userip'] = $ip;
        // getting user agent
        $default_args['useragent'] = $_SERVER['HTTP_USER_AGENT'];
        
        $endpoint = "http://api.indeed.com/ads/apisearch?";
        
        $args = wp_parse_args( $args, $default_args );
        $results = array();
		
        $result = wp_remote_get( $endpoint . http_build_query($args, '', '&') );
        if (!is_wp_error($result) && !empty($result['body'])) {
            $results = (array) json_decode($result['body']);
        }
        return isset($results['results']) ? $results['results'] : $results;
    }
    
    /**
     * indeed api countries list
    */
    public static function indeed_api_countries() {
        $country = array();
        $country['us'] = esc_html__('United States', 'jobhunt-indeed-jobs');
        $country['ar'] = esc_html__('Argentina', 'jobhunt-indeed-jobs');
        $country['au'] = esc_html__('Australia', 'jobhunt-indeed-jobs');
        $country['at'] = esc_html__('Austria', 'jobhunt-indeed-jobs');
        $country['bh'] = esc_html__('Bahrain', 'jobhunt-indeed-jobs');
        $country['be'] = esc_html__('Belgium', 'jobhunt-indeed-jobs');
        $country['br'] = esc_html__('Brazil', 'jobhunt-indeed-jobs');
        $country['ca'] = esc_html__('Canada', 'jobhunt-indeed-jobs');
        $country['cl'] = esc_html__('Chile', 'jobhunt-indeed-jobs');
        $country['cn'] = esc_html__('China', 'jobhunt-indeed-jobs');
        $country['co'] = esc_html__('Colombia', 'jobhunt-indeed-jobs');
        $country['cz'] = esc_html__('Czech Republic', 'jobhunt-indeed-jobs');
        $country['dk'] = esc_html__('Denmark', 'jobhunt-indeed-jobs');
        $country['fi'] = esc_html__('Finland', 'jobhunt-indeed-jobs');
        $country['fr'] = esc_html__('France', 'jobhunt-indeed-jobs');
        $country['de'] = esc_html__('Germany', 'jobhunt-indeed-jobs');
        $country['gr'] = esc_html__('Greece', 'jobhunt-indeed-jobs');
        $country['hk'] = esc_html__('Hong Kong', 'jobhunt-indeed-jobs');
        $country['hu'] = esc_html__('Hungary', 'jobhunt-indeed-jobs');
        $country['in'] = esc_html__('India', 'jobhunt-indeed-jobs');
        $country['id'] = esc_html__('Indonesia', 'jobhunt-indeed-jobs');
        $country['ie'] = esc_html__('Ireland', 'jobhunt-indeed-jobs');
        $country['il'] = esc_html__('Israel', 'jobhunt-indeed-jobs');
        $country['it'] = esc_html__('Italy', 'jobhunt-indeed-jobs');
        $country['jp'] = esc_html__('Japan', 'jobhunt-indeed-jobs');
        $country['kr'] = esc_html__('Korea', 'jobhunt-indeed-jobs');
        $country['kw'] = esc_html__('Kuwait', 'jobhunt-indeed-jobs');
        $country['lu'] = esc_html__('Luxembourg', 'jobhunt-indeed-jobs');
        $country['my'] = esc_html__('Malaysia', 'jobhunt-indeed-jobs');
        $country['mx'] = esc_html__('Mexico', 'jobhunt-indeed-jobs');
        $country['nl'] = esc_html__('Netherlands', 'jobhunt-indeed-jobs');
        $country['nz'] = esc_html__('New Zealand', 'jobhunt-indeed-jobs');
        $country['no'] = esc_html__('Norway', 'jobhunt-indeed-jobs');
        $country['om'] = esc_html__('Oman', 'jobhunt-indeed-jobs');
        $country['pk'] = esc_html__('Pakistan', 'jobhunt-indeed-jobs');
        $country['pe'] = esc_html__('Peru', 'jobhunt-indeed-jobs');
        $country['ph'] = esc_html__('Philippines', 'jobhunt-indeed-jobs');
        $country['pl'] = esc_html__('Poland', 'jobhunt-indeed-jobs');
        $country['pt'] = esc_html__('Portugal', 'jobhunt-indeed-jobs');
        $country['qa'] = esc_html__('Qatar', 'jobhunt-indeed-jobs');
        $country['ro'] = esc_html__('Romania', 'jobhunt-indeed-jobs');
        $country['ru'] = esc_html__('Russia', 'jobhunt-indeed-jobs');
        $country['sa'] = esc_html__('Saudi Arabia', 'jobhunt-indeed-jobs');
        $country['sg'] = esc_html__('Singapore', 'jobhunt-indeed-jobs');
        $country['za'] = esc_html__('South Africa', 'jobhunt-indeed-jobs');
        $country['es'] = esc_html__('Spain', 'jobhunt-indeed-jobs');
        $country['se'] = esc_html__('Sweden', 'jobhunt-indeed-jobs');
        $country['ch'] = esc_html__('Switzerland', 'jobhunt-indeed-jobs');
        $country['tw'] = esc_html__('Taiwan', 'jobhunt-indeed-jobs');
        $country['tr'] = esc_html__('Turkey', 'jobhunt-indeed-jobs');
        $country['ae'] = esc_html__('United Arab Emirates', 'jobhunt-indeed-jobs');
        $country['gb'] = esc_html__('United Kingdom', 'jobhunt-indeed-jobs');
        $country['ve'] = esc_html__('Venezuela', 'jobhunt-indeed-jobs');
        $country['vn'] = esc_html__('Vietnam', 'jobhunt-indeed-jobs');
        $country['th'] = esc_html__('Thailand', 'jobhunt-indeed-jobs');
        
        return $country;
    }

}