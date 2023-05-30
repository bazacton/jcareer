<?php
/*
 *
 * @Shortcode Name : Video
 * @retrun
 *
 */

if (!function_exists('jobcareer_video_shortcode')) {

    function jobcareer_video_shortcode($atts, $content = "") {
        $video = '';
        $width='';
        $defaults = array(
            'column_size' => '',
            'cs_video_section_title' => '',
            'cs_video_view' => '',
            'video_url' => '',
            'video_height' => '300',
            'cs_video_custom_animation' => 'slide',
            'cs_video_custom_animation_duration' => ''
        );


        extract(shortcode_atts($defaults, $atts));
        if(isset($column_size) && $column_size!=''){
           $column_class = jobcareer_custom_column_class($column_size);
           $video .= '<div class="'.$column_class.'">';
        }
        $height = isset($video_height) ? $video_height : '300';
        $cs_video_custom_animation = isset($cs_video_custom_animation) ? $cs_video_custom_animation : '';
        $cs_video_custom_animation_duration = isset($cs_video_custom_animation_duration) ? $cs_video_custom_animation_duration : '';
         

        $video_url = isset($video_url) ? $video_url : '';
        $url = parse_url($video_url);
        
        $cs_iframe = '<' . 'i' . 'fr' . 'ame ';

        if (trim($cs_video_custom_animation) != '') {
            $cs_video_custom_animation = 'wow' . ' ' . $cs_video_custom_animation;
        } else {

            $cs_video_custom_animation = '';
        }

        

        $column_class = jobcareer_custom_column_class($column_size);
        $section_title = '';
        
        if (isset($url['host']) && $url['host'] <> '') {
            $url['host'] = $url['host'];
        } else {
            $url['host'] = '';
        }
        if ($url['host'] == cs_get_server_data("SERVER_NAME")) {
            if ($cs_video_section_title != '') {
                $video .= '<div class="cs-element-title"><h2>' . $cs_video_section_title . '</h2></div>';
            }
            
            $video .= '<figure  class="cs-video ' . $column_class . '">';
            $video .= '' . do_shortcode('[video height="' . $height . '" src="' . esc_url($video_url) . '"][/video]') . '';
            $video .= '</figure>';
        }else if($cs_video_view == 'aviation'){
                $aviation_video = 'aviation_video';
            ?>
                <div class="image-frame cs-img-frame" id="<?php echo jobcareer_special_char($aviation_video); ?>" style="padding-top:55px;">
                    <figure style="border-radius: 15px;">
                        <?php $content_exp = explode("/", $video_url);
                        $content_vimo = array_pop($content_exp); ?>
                        <img src="<?php echo plugins_url('wp-jobhunt/assets/images/ifram-vidoe-img1.png', _FILE_); ?>" alt="vidoe-img1" style="width:100%;max-width: 100%;border-radius: 15px;">
                        <div id="video_iframe_link">
                            <?php echo jobcareer_special_char($cs_iframe) . ' height="' . $height . '" src="'.cs_server_protocol().'player.vimeo.com/video/' . $content_vimo . '" allowfullscreen></iframe>'; ?>
                        </div>
                        <figcaption class="img-playicon-frame"><a href="javascript:void(0)" id="video_play"><i class="icon-play7"></i></a></figcaption>
                    </figure>
                </div>
            <script>
                jQuery('#aviation_video').parent().removeClass('col-lg-6').addClass('col-lg-5').prev().removeClass('col-lg-6').addClass('col-lg-7');
                jQuery(function () {
                    jQuery('#video_iframe_link').css('display', 'none');
                    jQuery('#video_play').click(function () {
                        jQuery('.cs-img-frame img').hide();
                        jQuery('.img-playicon-frame').hide();
                        jQuery('#video_iframe_link').css('display', 'block');
                        jQuery('.fluid-width-video-wrapper').css('padding-top', '56%');
                        jQuery('iframe#fitvid0').contents('.vp-preview-cover').data('thumb');
                    });
                });
            </script>
            <?php
        } else {

            if ($url['host'] == 'vimeo.com') {
                $content_exp = explode("/", $video_url);
                $content_vimo = array_pop($content_exp);
                if ($cs_video_section_title != '') {
                    $video .= '<div class="cs-element-title"><h2>' . $cs_video_section_title . '</h2></div>';
                }
                $video .= '<figure  class="cs-video ' . $column_class . '">';
                $video .= $cs_iframe . ' height="' . $height . '" src="'.cs_server_protocol().'player.vimeo.com/video/' . $content_vimo . '" allowfullscreen></iframe>';
                $video .= '</figure>';
            } else {
                
                if ($cs_video_section_title != '') {
                    $video .= '<div class="cs-element-title"><h2>' . $cs_video_section_title . '</h2></div>';
                }
                $video .= wp_oembed_get($video_url, array( 'width' => $width, 'height' => $height ));
            }
        }

		if(isset($column_size) && $column_size!=''){
                   $video .= '</div>';
		}
        return $video;
    }

    if (function_exists('cs_short_code'))
        cs_short_code(CS_SC_VIDEO, 'jobcareer_video_shortcode');
}