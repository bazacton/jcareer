<?php

class Jobhunt_Newsletter_Frontend
{

    public function render($settings)
    {
        ob_start();
        $defaults = array(
            'column_size' => '' ,
            'newsletter_title' => '' ,
            'color_title' => '' ,
            'cs_newsletter_style' => '' ,
            'newsletter_color' => '#000' ,
            'class' => 'cs-newsletter-shortcode' ,
            'newsletter_style' => '' ,
            'newsletter_size' => '' ,
            'font_weight' => '' ,
            'sub_newsletter_title' => '' ,
            'newsletter_font_style' => '' ,
            'newsletter_align' => 'center' ,
            'newsletter_divider' => '' ,
            'newsletter_color' => '' ,
            'newsletter_content' => '' ,
            'newsletter_content_color' => ''
        );
        extract(shortcode_atts($defaults , $settings));
        $newsletter_class = 'widget widget-newsletter';
        if ($cs_newsletter_style == 'classic') {
            $newsletter_class = 'cs-newsletter';
        }
        if ($cs_newsletter_style == 'aviation') {
            ?>

            <?php
        }
        $column_class = jobcareer_custom_column_class($column_size);
        $html = "";
        if (isset($column_size) && $column_size != '') {
            $column_class = jobcareer_custom_column_class($column_size);
        }
        ?>
        <div class="<?php echo jobcareer_special_char($newsletter_class); ?>">
            <?php if (isset($newsletter_title) && $newsletter_title != '') { ?>
                <div class="widget-title"><h5><?php echo esc_html($newsletter_title); ?></h5></div>
            <?php } ?>
            <div class="fieldset">
                <?php echo '<p>' . $newsletter_content . '</p>'; ?>
                <?php cs_custom_mailchimp($cs_newsletter_style); ?>
            </div>
        </div>
        <?php
        $html .= ob_get_clean();
        echo $html;

    }

}

