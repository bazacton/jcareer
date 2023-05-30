<?php
/**
 * The template for displaying Footer
 */
$jobcareer_options = CS_JOBCAREER_GLOBALS()->theme_options();
$cs_footer_back_to_top = isset($jobcareer_options['cs_footer_back_to_top']) ? $jobcareer_options['cs_footer_back_to_top'] : '';
$cs_sub_footer_social_icons = isset($jobcareer_options['cs_sub_footer_social_icons']) ? $jobcareer_options['cs_sub_footer_social_icons'] : '';
$cs_footer_back_to_top_color = isset($jobcareer_options['$cs_footer_back_to_top_color']) ? $jobcareer_options['$cs_footer_back_to_top_color'] : '';
?>
<div class="clearfix"></div>
<!-- Footer -->
<?php
$cs_footer_switch = isset($jobcareer_options['cs_footer_switch']) ? $jobcareer_options['cs_footer_switch'] : '';
$cs_footer_style = isset($jobcareer_options['cs_footer_style']) ? $jobcareer_options['cs_footer_style'] : '';
$footer_background_color = isset($jobcareer_options['cs_copyright_bg_color']) ? $jobcareer_options['cs_copyright_bg_color'] : '';
$cs_sub_footer_menu = isset($jobcareer_options['cs_sub_footer_menu']) ? $jobcareer_options['cs_sub_footer_menu'] : '';
$cs_copy_right = isset($jobcareer_options['cs_copy_right']) ? $jobcareer_options['cs_copy_right'] : '';
$cs_copyright_color = isset($jobcareer_options['$cs_copyright_color']) ? $jobcareer_options['$cs_copyright_color'] : '';
$cs_footer_logo = isset($jobcareer_options['jobcareer_footer_background']) ? $jobcareer_options['jobcareer_footer_background'] : '';
$cs_ftr_class = $cs_footer_style;
$cs_ftr_class = 'footer-v1 ' . $cs_footer_style;
if ($cs_footer_style == 'modern-footer') {
    $cs_ftr_class = 'footer-v1 ' . $cs_footer_style;
}
if ($cs_footer_style == 'modern-footer2') {
    $cs_ftr_class = 'footer-v3 default-footer';
}
if ($cs_footer_style == 'classic-footer') {
    $cs_ftr_class = 'classic-footer';
}
if ($cs_footer_style == 'aviation-footer') {
    $cs_ftr_class = 'cs-aviation-footer';
}
$footer_bg = '';
if ($cs_footer_style == 'aviation-footer') {
    $footer_bg = 'style="background-color:transparent !important; padding:93px 0 70px 0; background: url(' . $cs_footer_logo . ') no-repeat; background-size: cover;"';
}
if ((isset($cs_footer_switch) && $cs_footer_switch == 'on')) {
    ?> 	
    <footer id="footer" <?php echo jobcareer_special_char($footer_bg); ?>>
        <div class="cs-footer <?php echo jobcareer_special_char($cs_ftr_class); ?>">
            <?php
            if ($cs_footer_style == 'modern-footer') {
                echo get_template_part('frontend/templates/footers/modern');
            } else if ($cs_footer_style == 'modern-footer2') {
                echo get_template_part('frontend/templates/footers/modern2');
            } else if ($cs_footer_style == 'classic-footer') {
                echo get_template_part('frontend/templates/footers/classic');
            } elseif ($cs_footer_style == 'fancy-footer') {
                echo '<div class="container">';
                echo get_template_part('frontend/templates/footers/fancy');
                echo '</div>';
            } else {
                echo get_template_part('frontend/templates/footers/default');
            }
            ?>
        </div>
    </footer>
    <?php
}
cs_facebook_cache_clear();
?>
<!-- Wrapper End -->   
</div>
<?php
wp_footer();

$cs_plugin_options = get_option('cs_plugin_options');
$cs_cookies_page = isset($cs_plugin_options['cs_cookies_dashboard']) ? $cs_plugin_options['cs_cookies_dashboard'] : '';
$cs_terms_policy_switch = isset($cs_plugin_options['cs_terms_policy_switch']) ? $cs_plugin_options['cs_terms_policy_switch'] : '';
if (isset($cs_terms_policy_switch) && $cs_terms_policy_switch == 'on') {
    ?>
    <div class="alert alert-dismissible text-center cookiealert" role="alert">
        <div class="cookiealert-container">
            <?php echo esc_html__('We use cookies to provide you with the best possible user experience. By continuing to use our site, you agree to their use.', 'jobcareer'); ?> <a style="color:#fff;" href="<?php echo esc_url_raw(get_page_link($cs_cookies_page)); ?>" target="_blank"><?php echo esc_html__('Learn more', 'jobcareer'); ?></a>

            <button type="button" class="btn btn-primary btn-sm acceptcookies" aria-label="Close">
                <?php echo esc_html__('I agree', 'jobcareer'); ?>
            </button>
        </div>
    </div>
<?php } ?>
<div class="user-account form-user-account">
    <div class="sign-in-popup"> </div>
    <div class="join-us sign-up-popup"> </div>
</div>
</body>
</html>