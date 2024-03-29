<?php

class Jobhunt_Blog_Frontend
{

    public function render($settings)
    {
        global $post , $wpdb , $jobcareer_options , $cs_counter_node , $jobcareer_column_atts , $cs_blog_cat , $cs_blog_description , $cs_blog_excerpt , $post_thumb_view , $cs_blog_section_title , $cs_exclude_post_id , $args , $column_class , $cs_blog_boxsize;
        $defaults = array(
            'column_size' => '' ,
            'cs_blog_section_title' => '' ,
            'cs_blog_view' => '' ,
            'cs_exclude_post_id' => '0' ,
            'cs_blog_cat' => '' ,
            'cs_blog_element_subtitle' => '' ,
            'cs_blog_alignment' => '' ,
            'cs_blog_orderby' => 'DESC' ,
            'orderby' => 'ID' ,
            'cs_blog_description' => 'yes' ,
            'cs_blog_excerpt' => '255' ,
            'cs_blog_num_post' => '10' ,
            'blog_pagination' => '' ,
            'cs_blog_boxsize' => ''
        );
        extract(shortcode_atts($defaults , $settings));
        $cs_blog_boxsize = isset($cs_blog_boxsize) ? $cs_blog_boxsize : '';
        $cs_dataObject = get_post_meta(get_the_ID() , 'cs_full_data');
        $column_class = jobcareer_custom_column_class($column_size);
        $jobcareer_sidebarLayout = '';
        $section_cs_layout = '';
        $pageSidebar = false;
        $box_col_class = 'col-md-3';
        if (isset($cs_dataObject['cs_page_layout'])) {
            $jobcareer_sidebarLayout = $cs_dataObject['cs_page_layout'];
        }

        if (isset($jobcareer_column_atts->cs_layout)) {
            $section_cs_layout = $jobcareer_column_atts->cs_layout;
            if ($section_cs_layout == 'left' || $section_cs_layout == 'right') {
                $pageSidebar = true;
            }
        }
        if ($jobcareer_sidebarLayout == 'left' || $jobcareer_sidebarLayout == 'right') {
            $pageSidebar = true;
        }
        if ($pageSidebar == true) {
            $box_col_class = 'col-md-4';
        }

        if ((isset($cs_dataObject['cs_page_layout']) && $cs_dataObject['cs_page_layout'] <> '' and $cs_dataObject['cs_page_layout'] <> "none") || $pageSidebar == true) {
            $cs_blog_grid_layout = 'col-md-4';
        } else {
            $cs_blog_grid_layout = 'col-md-3';
        }
        $owlcount = rand(40 , 9999999);
        $cs_counter_node++;
        ob_start();
        //==Filters
        $filter_category = '';
        $filter_tag = '';
        $author_filter = '';

        if (isset($_GET['filter_category']) && $_GET['filter_category'] <> '' && $_GET['filter_category'] <> '0') {
            $filter_category = $_GET['filter_category'];
        }

        if (isset($_GET['sort']) and $_GET['sort'] == 'asc') {
            $cs_blog_orderby = 'ASC';
        } else {
            $cs_blog_orderby = $cs_blog_orderby;
        }
        if (isset($_GET['sort']) and $_GET['sort'] == 'alphabetical') {
            $orderby = 'title';
            $cs_blog_orderby = 'ASC';
        } else {
            $orderby = 'meta_value';
        }
        //==Sorting End
        if (empty($_GET['page_id_all'])) {
            $_GET['page_id_all'] = 1;
        }
        $cs_blog_num_post = $cs_blog_num_post ? $cs_blog_num_post : '-1';
        if ($cs_exclude_post_id == 0 && $cs_exclude_post_id == '') {
            $args = array('posts_per_page' => "-1" , 'post_type' => 'post' , 'order' => $cs_blog_orderby , 'orderby' => $orderby , 'post_status' => 'publish' , 'ignore_sticky_posts' => 1);
        } else {
            $args = array('posts_per_page' => "-1" , 'post__not_in' => array($cs_exclude_post_id) , 'post_type' => 'post' , 'order' => $cs_blog_orderby , 'orderby' => $orderby , 'post_status' => 'publish' , 'ignore_sticky_posts' => 1);
        }
        if (isset($cs_blog_cat) && $cs_blog_cat <> '' && $cs_blog_cat <> '0') {
            $blog_category_array = array('category_name' => "$cs_blog_cat");
            $args = array_merge($args , $blog_category_array);
        }
        if (isset($filter_category) && $filter_category <> '' && $filter_category <> '0') {

            if (isset($_GET['filter-tag'])) {
                $filter_tag = $_GET['filter-tag'];
            }
            if ($filter_tag <> '') {
                $blog_category_array = array('category_name' => "$filter_category" , 'tag' => "$filter_tag");
            } else {
                $blog_category_array = array('category_name' => "$filter_category");
            }
            $args = array_merge($args , $blog_category_array);
        }

        if (isset($_GET['filter-tag']) && $_GET['filter-tag'] <> '' && $_GET['filter-tag'] <> '0') {
            $filter_tag = $_GET['filter-tag'];
            if ($filter_tag <> '') {
                $course_category_array = array('category_name' => "$filter_category" , 'tag' => "$filter_tag");
                $args = array_merge($args , $course_category_array);
            }
        }
        if (isset($_GET['by_author']) && $_GET['by_author'] <> '' && $_GET['by_author'] <> '0') {
            $author_filter = $_GET['by_author'];
            if ($author_filter <> '') {
                $authorArray = array('author' => "$author_filter");
                $args = array_merge($args , $authorArray);
            }
        }
        $query = new WP_Query($args);
        $count_post = $query->post_count;
        $cs_blog_num_post = $cs_blog_num_post ? $cs_blog_num_post : '-1';
        if ($cs_exclude_post_id == 0 && $cs_exclude_post_id == '') {
            $args = array('posts_per_page' => $cs_blog_num_post , 'post_type' => 'post' , 'paged' => $_GET['page_id_all'] , 'order' => $cs_blog_orderby , 'orderby' => $orderby , 'post_status' => 'publish' , 'ignore_sticky_posts' => 1);
        } else {
            $args = array('posts_per_page' => $cs_blog_num_post , 'post__not_in' => array($cs_exclude_post_id) , 'post_type' => 'post' , 'paged' => $_GET['page_id_all'] , 'order' => $cs_blog_orderby , 'orderby' => $orderby , 'post_status' => 'publish' , 'ignore_sticky_posts' => 1);
        }
        if (isset($cs_blog_cat) && $cs_blog_cat <> '' && $cs_blog_cat <> '0') {
            $blog_category_array = array('category_name' => "$cs_blog_cat");
            $args = array_merge($args , $blog_category_array);
        }
        if (isset($filter_category) && $filter_category <> '' && $filter_category <> '0') {
            if (isset($_GET['filter-tag'])) {
                $filter_tag = $_GET['filter-tag'];
            }
            if ($filter_tag <> '') {
                $blog_category_array = array('category_name' => "$filter_category" , 'tag' => "$filter_tag");
            } else {
                $blog_category_array = array('category_name' => "$filter_category");
            }
            $args = array_merge($args , $blog_category_array);
        }
        if (isset($_GET['filter-tag']) && $_GET['filter-tag'] <> '' && $_GET['filter-tag'] <> '0') {
            $filter_tag = $_GET['filter-tag'];
            if ($filter_tag <> '') {
                $course_category_array = array('category_name' => "$filter_category" , 'tag' => "$filter_tag");
                $args = array_merge($args , $course_category_array);
            }
        }
        if (isset($_GET['by_author']) && $_GET['by_author'] <> '' && $_GET['by_author'] <> '0') {
            $author_filter = $_GET['by_author'];
            if ($author_filter <> '') {
                $authorArray = array('author' => "$author_filter");
                $args = array_merge($args , $authorArray);
            }
        }
        if ($cs_blog_cat != '' && $cs_blog_cat != '0') {
            $row_cat = $wpdb->get_row($wpdb->prepare("SELECT * from $wpdb->terms WHERE slug = %s" , $cs_blog_cat));
        }
        $outerDivStart = '';
        $outerDivEnd = '';
        $section_title = '';
        if ((isset($cs_blog_section_title) && trim($cs_blog_section_title) <> '') || (isset($cs_blog_element_subtitle) && trim($cs_blog_element_subtitle) <> '')) {
            $subtitle_html = '';
            $element_title_html = '';
            if (isset($cs_blog_element_subtitle) && trim($cs_blog_element_subtitle) <> '') {
                $subtitle_html = '<p>' . esc_html($cs_blog_element_subtitle) . '</p>';
            }
            if (isset($cs_blog_section_title) && trim($cs_blog_section_title) <> '') {
                $element_title_html = '<h2>' . esc_html($cs_blog_section_title) . '</h2>';
            }
            $section_title = '<div class="main-title col-md-12"><div class="cs-element-title ' . $cs_blog_alignment . '">' . $element_title_html . ' ' . $subtitle_html . ' </div></div>';
        }
        echo jobcareer_special_char($outerDivStart);

        set_query_var('args' , $args);
        $cs_col_class = '';
        if ($column_class != '') {
            $cs_col_class = $column_class;
            ?>
            <div asif class="<?php echo jobcareer_special_char($cs_col_class); ?>">
            <div class="row">
            <?php
        }
        echo jobcareer_special_char($section_title);
        if ($cs_blog_view == 'medium') {
            get_template_part('templates/blog/blog' , 'medium');
        } else if ($cs_blog_view == 'grid') {
            echo '<div class="row">';
            get_template_part('templates/blog/blog' , 'grid');
            echo '</div>';
        } else if ($cs_blog_view == 'grid-fancy') {
            echo '<div class="row">';
            get_template_part('templates/blog/blog' , 'grid-fancy');
            echo '</div>';
        } else if ($cs_blog_view == 'large') {
            echo '<div class="row">';
            get_template_part('templates/blog/blog' , 'large');
            echo '</div>';
        } else if ($cs_blog_view == 'modern') {
            echo '<div class="row">';
            get_template_part('templates/blog/blog' , 'modern');
            echo '</div>';
        } else if ($cs_blog_view == 'simple') {
            echo '<div class="row">';
            get_template_part('templates/blog/blog' , 'simple-grid');
            echo '</div>';
        } else if ($cs_blog_view == 'grid-modern') {
            echo '<div class="row">';
            get_template_part('templates/blog/blog' , 'modern-grid');
            echo '</div>';
        } else if ($cs_blog_view == 'grid-classic') {
            echo '<div class="row">';
            get_template_part('templates/blog/blog' , 'grid-classic');
            echo '</div>';
        } else {
            get_template_part('templates/blog/blog' , 'classic');
        }

        //grid-fancy

        //simple-grid

        echo jobcareer_special_char($outerDivEnd);
        if ($blog_pagination == "yes" && $count_post > $cs_blog_num_post && $cs_blog_num_post > 0 && $cs_blog_view != 'blog-crousel') {
            $qrystr = '';
            if (isset($_GET['page_id']))
                $qrystr .= "&amp;page_id=" . $_GET['page_id'];
            if ($cs_blog_view == 'medium' || $cs_blog_view == 'blog-lrg') {

            }

            echo '<div class="cs-pagination-blog">';
            echo jobcareer_pagination($count_post , $cs_blog_num_post , $qrystr , 'Show Pagination');
            echo '</div>';
            if (isset($column_class) && $column_class != "") {
                ?>
                </div>
                </div>
                <?php
            }
            if ($cs_blog_view == 'medium' || $cs_blog_view == 'blog-lrg') {

            }
        }
        wp_reset_postdata();
        $post_data = ob_get_clean();
        echo $post_data;

    }

}

