<?php
do_action('jobcareer_cors');
//header('Access-Control-Allow-Origin: *'); // no cabe�alho

if (!class_exists('App_Tabs_Api')) {

    class App_Tabs_Api extends WP_REST_Controller {

        /**
         * Register the routes for the objects of the controller.
         */
        function __construct() {
            add_action('rest_api_init', array($this, 'register_routes_callback'));
        }

        public function register_routes_callback() {
            $version = '1';
            $namespace = 'api/v' . $version;
            $base = '';
            $routes = array(
                array(
                    'request' => 'bottom_tabs',
                    'methods' => 'GET',
                    'callback' => 'bottom_tabs_callback'
                ),
               
            );
            foreach ($routes as $val) {
                $uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
                $uriSegments = array_pop($uriSegments);
                if ($uriSegments == $val['request']) {
                    register_rest_route($namespace, $base . '/' . $val['request'], array(
                        'methods' => $val['methods'],
                        'callback' => array($this, $val['callback']),
                        isset($val['args']) ? ($val['args']) : (''),
                    ));
                }
            }
        }

        /*
         * Bottom Tabs Call
         */

        public function bottom_tabs_callback() {
            //$tabs['my_jobs']['icon']['title']['tab_api'];
            $tabs['find_jobs'] = ['icon' => 'icon_name', 'title' => 'title', 'tab_api' => 'api'];
            $tabs['my_jobs'] = ['icon' => 'icon_name', 'title' => 'title', 'tab_api' => 'api'];
            $tabs['my_resumes'] = ['icon' => 'icon_name', 'title' => 'title', 'tab_api' => 'api'];
            $tabs['settings'] = ['icon' => 'icon_name', 'title' => 'title', 'tab_api' => 'api'];

            $tabs['template'] = '<ul>
           <li><a href = "#" ><i class = "icon-search2" id="find_jobs"></i>Find Jobs</a></li>
            </ul><ul>
           <li><a href = "#" ><i class = "icon-search3  id="my_jobs"></i>My Jobs</a></li>
            </ul><ul>
            <li><a href = "#" ><i class = "icon-search2" id="my_resumes"></i>My Resumes</a></li>
           </ul><ul>
            <li><a href = "#" ><i class = "icon-search3" id="settings"></i>Settings</a></li>
           </ul>';
            $info = array('status' => true,
                'data' => $tabs);
            return new WP_REST_Response($info, 200);
        }

    }

}
$App_Tabs_Api = new App_Tabs_Api();
