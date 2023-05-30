<?php
do_action('jobcareer_cors');
//header('Access-Control-Allow-Origin: *');  // no cabe�alho
if (!class_exists('Blog_Api')) {

    class Blog_Api extends WP_REST_Controller {

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
                    'request' => 'blog_detail',
                    'methods' => 'GET',
                    'callback' => 'blog_detail_callback',
                ),
                array(
                    'request' => 'blog',
                    'methods' => 'GET',
                    'callback' => 'blog_callback',
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
         * Blog
         */
        
        public function blog_callback(){
            global $cs_blog_excerpt;
            $page = 1;
            if (isset($_GET['p'])) {
                $page = $_GET['p'];
            }
            $args = array( 'posts_per_page' => "5", 'paged' => $page, 'post_type' => 'post', 'order' => 'DESC', 'orderby' => 'date', 'post_status' => 'publish', 'ignore_sticky_posts' => 1 );
            $query = new WP_Query($args);
            $post_count = $query->post_count;
            $output = '';
            $width = '350';
            $height = '210';
            if ($query->have_posts()) {
                $postCounter = 0;
                wp_reset_query();
                while ($query->have_posts()) : $query->the_post();
                    global $post;
                    $thumbnail = jobcareer_get_post_img_src($post->ID, $width, $height);
                    $num_comments = get_comments_number($post->ID); // get_comments_number returns only a numeric value
                    if ($num_comments > 1) {
                        $comment_string = esc_html__('comments', 'jobcareer');
                    } else {
                        $comment_string = esc_html__('comment', 'jobcareer');
                    }
                    $output .= '<div class="blog-post">
                        <div class="item-content">
                            <div class="item-inner">
                                <div class="post-info">
                                    <span>'. get_the_date() .' - </span>
                                    <span>'. $num_comments .' '. $comment_string .'</span>
                                </div>
                                <div class="item-title">
                                    <h4><a href="/blogdetail/'. $post->ID .'" class="">'. get_the_title() .'</a></h4>
                                </div>
                                <div class="item-text">
                                    <p>'. jobcareer_get_excerpt(5, 'true', '') .'</p>
                                </div>
                            </div>
                        </div>
                        <div class="image-holder ">
                            <a href="/blogdetail/'. $post->ID .'" class="">
                                <figure>
                                    <img src="'. esc_url($thumbnail) .'" alt="" />
                                </figure>
                            </a>
                        </div>
                    </div>';
                    
                endwhile;
            }
            
            $next_page = $page+1;
            $response = array(
                'template' => $output,
                'next_api' => site_url().'/wp-json/api/v1/blog?p='.$next_page,
            );
            $info = array('status' => true,
                'data' => $response);
            return new WP_REST_Response($info, 200);
        }
        
        
        /*
         * Blog Detail Page
         */

        public function blog_detail_callback() {
            $blog_id = isset( $_GET['blog_id'] )? $_GET['blog_id'] : 0;
            $postObj = get_post($blog_id);
            $blog_data   = array();
            $width = '350';
            $height = '210';
            $thumbnail = jobcareer_get_post_img_src($blog_id, $width, $height);
            $author_name = get_the_author_meta('display_name', $postObj->post_author);
            $author_img = get_avatar(get_the_author_meta('ID', $postObj->post_author), 32);

            $num_comments = get_comments_number($blog_id); // get_comments_number returns only a numeric value
            if ($num_comments > 1) {
                $comment_string = esc_html__('comments', 'jobcareer');
            } else {
                $comment_string = esc_html__('comment', 'jobcareer');
            }
            
            $blog_date  = get_the_date('', $blog_id);
            $blog_tags  = get_the_tags( $blog_id );
            
            
            $blog_data  = array(
                'title' => $postObj->post_title,
                'blog_image' => $thumbnail,
                'comments_count' => $num_comments,
                'comments_string' => $comment_string,
                'blog_date' => $blog_date,
                'blog_content' => $postObj->post_content,
                'blog_tags' => $blog_tags,
                'author_name' => $author_name,
                'author_img' => $author_img,
            );
            
            $info = array('status' => true,
                'data' => $blog_data);
            return new WP_REST_Response($info, 200);
        }

    }

}
$Blog_Api = new Blog_Api();
