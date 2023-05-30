<?php
ob_start();
/**
 * The template for displaying all pages
 */
get_header();

    while (have_posts()) : the_post();
        echo '<div class="rich-text-editor">';
        the_content();
        echo '</div>';
    endwhile;
    wp_link_pages(array(
        'before' => '<div class="page-content-links"><strong class="page-links-title">' . esc_html__('Pages:' , 'jobcareer') . '</strong>' ,
        'after' => '</div>' ,
        'link_before' => '<span>' ,
        'link_after' => '</span>' ,
        'pagelink' => '%' ,
        'separator' => '' ,
    ));
?>
    <div id="comment" class="comment-form">
        <?php
        comments_template();
        ?>
    </div>
<?php

get_footer();
