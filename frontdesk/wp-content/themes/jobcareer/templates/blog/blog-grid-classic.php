<?php
/**
 * @ Start front end Blog list view 
 *
 *
 */
$cs_blog_vars = array( 'post', 'cs_blog_cat', 'cs_blog_description', 'cs_blog_excerpt', 'cs_notification', 'wp_query', 'column_class', 'cs_blog_boxsize' );
$cs_blog_vars = CS_JOBCAREER_GLOBALS()->globalizing($cs_blog_vars);
extract($cs_blog_vars);
extract($wp_query->query_vars);
$width = '350';
$height = '210';
$query = new WP_Query($args);
$post_count = $query->post_count;
$box_size = 'col-lg-4 col-md-4 col-sm-6 col-xs-12';
// $cs_blog_boxsize_larg = $cs_blog_boxsize < 6 ? 6 : '12';
if ( $cs_blog_boxsize == 4 ) {
    $box_size = 'col-lg-4 col-md-4 col-sm-6 col-xs-12';
} else if ( $cs_blog_boxsize == 3 ) {
    $box_size = ' col-lg-3 col-md-3 col-sm-6 col-xs-12';
} else if ( $cs_blog_boxsize == 6 ) {
    $box_size = 'col-lg-6 col-md-6 col-sm-12 col-xs-12';
} else if ( $cs_blog_boxsize == 12 ) {
    $box_size = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
}
// $box_size = 'col-lg-' . $cs_blog_boxsize . ' col-md-' . $cs_blog_boxsize . ' col-sm-' . $cs_blog_boxsize_larg . ' col-xs-' . $cs_blog_boxsize_larg;
?>

<?php
if ( $query->have_posts() ) {
    $postCounter = 0;
    wp_reset_query();
    while ( $query->have_posts() ) : $query->the_post();
        global $post;
        $thumbnail = jobcareer_get_post_img_src($post->ID, $width, $height);
        $cs_postObject = get_post_meta($post->ID, "cs_full_data", true);
        $cs_gallery = get_post_meta($post->ID, 'cs_post_list_gallery', true);
        $cs_gallery = explode(',', $cs_gallery);
        $cs_thumb_view = get_post_meta($post->ID, 'cs_detail_view', true);
        $cs_post_view = isset($cs_thumb_view) ? $cs_thumb_view : '';
        $current_user = wp_get_current_user();
        $custom_image_url = get_user_meta(get_the_author_meta('ID'), 'user_avatar_display', true);
        $tags = get_tags();
        $author_id = $post->post_author;
        if( function_exists('jobcareer_addthis_script_init_method')) {
            jobcareer_addthis_script_init_method();
        }
        ?> 
        <div class="<?php echo esc_html($box_size); ?>">
            <div class="cs-blog blog-grid classic">
                <div class="cs-media">
                    <figure> <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img alt="<?php the_title(); ?>" src="<?php echo esc_url($thumbnail); ?>"></a> </figure>
                </div>
                <div class="blog-text">
                    <div class="cs-inner-bolg">
                        <div class="post-option"> <span class="post-date"><a href="<?php echo get_month_link(get_the_time('Y'), get_the_time('m')); ?>"><?php echo get_the_date('F j, Y'); ?></a> </span> <span class="post-comment"><?php the_category(' '); ?></span> </div>
                        <div class="cs-post-title">
                            <h3><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3>
                        </div>
                        <?php if ( $cs_blog_description == 'yes' ) { ?><p> <?php echo jobcareer_get_excerpt($cs_blog_excerpt, 'true', ''); ?></p>
                            <a href="<?php the_permalink(); ?>" class="read-more cs-color"><?php esc_html_e('Read More', 'jobcareer'); ?></a>
                        <?php }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    endwhile;
    ?><?php
} else {
    $cs_notification->error(esc_html__('No blog post found.', 'jobcareer'));
}
?>

