<?php
/*
 *
 * @Shortcode Name : about
 * @retrun
 *
 */
if (!function_exists('jobcareer_pb_about')) {

    function jobcareer_pb_about($die = 0) {
	global $jobcareer_node, $count_node, $post, $jobcareer_html_fields, $jobcareer_form_fields;
	$shortcode_element = '';
	$filter_element = 'filterdrag';
	$shortcode_view = '';
	$output = array();
	$counter = $_POST['counter'];
	$cs_counter = $_POST['counter'];
	$album_num = 0;
	if (isset($_POST['action']) && !isset($_POST['shortcode_element_id'])) {
	    $POSTID = '';
	    $shortcode_element_id = '';
	} else {
	    $POSTID = $_POST['POSTID'];
	    $shortcode_element_id = $_POST['shortcode_element_id'];
	    $shortcode_str = stripslashes($shortcode_element_id);
	    $PREFIX = CS_SC_ABOUT;
	    $parseObject = new ShortcodeParse();
	    $output = $parseObject->cs_shortcodes($output, $shortcode_str, true, $PREFIX);
	}
	$defaults = array(
	    'cs_about_section_title' => '',
	    'cs_about_info_style' => '',
	    'about_url' => '',
	    'cs_bg_color' => '',
	    'cs_text_color' => '',
	    'cs_image_about_url' => '',
	    'button_text' => '',
	    'content_texarea', '',
	    'about_action_textarea' => '',
	    'cs_title_color' => '',
	    'cs_content_color' => '',
	    'cs_icon_box_icon' => '',
	);
	if (isset($output['0']['atts'])) {
	    $atts = $output['0']['atts'];
	} else {
	    $atts = array();
	}
	if (isset($output['0']['content'])) {
	    $atts_content = $output['0']['content'];
	} else {
	    $atts_content = array();
	}
	if (is_array($atts_content)) {
	    $album_num = count($atts_content);
	}
	$about_element_size = '25';
	foreach ($defaults as $key => $values) {
	    if (isset($atts[$key])) {
		$$key = $atts[$key];
	    } else {
		$$key = $values;
	    }
	}
	$name = 'jobcareer_pb_about';
	$coloumn_class = 'column_' . $about_element_size;
	if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
	    $shortcode_element = 'shortcode_element_class';
	    $shortcode_view = 'cs-pbwp-shortcode';
	    $filter_element = 'ajax-drag';
	    $coloumn_class = '';
	}
	$content_texarea = isset($atts['content_texarea']) ? $atts['content_texarea'] : '';
	$cs_image_url = isset($atts['cs_image_about_url']) ? $atts['cs_image_about_url'] : '';
	$cs_text_color = isset($atts['cs_text_color']) ? $atts['cs_text_color'] : '';
	$cs_bg_color = isset($atts['cs_bg_color']) ? $atts['cs_bg_color'] : '';
	?>

	<div id="<?php echo esc_attr($name . $cs_counter) ?>_del" class="column  parentdelete <?php echo esc_attr($coloumn_class); ?> <?php echo esc_attr($shortcode_view); ?>" item="about" data="<?php echo jobcareer_element_size_data_array_index($about_element_size) ?>" >
	    <?php jobcareer_element_setting($name, $cs_counter, $about_element_size, '', 'play-circle'); ?>
	    <div class="cs-wrapp-class-<?php echo intval($cs_counter) ?> <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $cs_counter) ?>" data-shortcode-template="[<?php echo esc_attr(CS_SC_ABOUT); ?> {{attributes}}]{{content}}[/<?php echo esc_attr(CS_SC_ABOUT); ?>]" style="display: none;">
		<div class="cs-heading-area">
		    <h5><?php esc_html_e('About OPTIONS', 'jobcareer'); ?></h5>
		    <a href="javascript:removeoverlay('<?php echo esc_js($name . $cs_counter) ?>','<?php echo esc_js($filter_element); ?>')" class="cs-btnclose"><i class="icon-times"></i></a> </div>
		<div class="cs-pbwp-content">
		    <div class="cs-wrapp-clone cs-shortcode-wrapp">
			<?php
			if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
			    jobcareer_shortcode_element_size();
			}
			?>
			<?php
			$cs_opt_array = array(
			    'name' => esc_html__('Element Title', 'jobcareer'),
			    'desc' => '',
			    'hint_text' => esc_html__("Enter element title here.", "jobcareer"),
			    'echo' => true,
			    'field_params' => array(
				'std' => esc_html($cs_about_section_title),
				'id' => 'cs_about_section_title',
				'cust_name' => 'cs_about_section_title[]',
				'return' => true,
			    ),
			);

			$jobcareer_html_fields->cs_text_field($cs_opt_array);

			$cs_opt_array = array(
			    'name' => esc_html__('Button Url', 'jobcareer'),
			    'desc' => '',
			    'hint_text' => '',
			    'echo' => true,
			    'field_params' => array(
				'std' => esc_url($about_url),
				'id' => 'about_url[]',
				'cust_name' => 'about_url[]',
				'return' => true,
			    ),
			);

			$jobcareer_html_fields->cs_text_field($cs_opt_array);

			$cs_opt_array = array(
			    'name' => esc_html__('Button Text', 'jobcareer'),
			    'desc' => '',
			    'hint_text' => '',
			    'echo' => true,
			    'field_params' => array(
				'std' => esc_html($button_text),
				'id' => 'button_text',
				'cust_name' => 'button_text[]',
				'return' => true,
			    ),
			);

			$jobcareer_html_fields->cs_text_field($cs_opt_array);

			$cs_opt_array = array(
			    'name' => esc_html__('Title Color', 'jobcareer'),
			    'desc' => '',
			    'hint_text' => esc_html__("Set the title Text color.", 'jobcareer'),
			    'echo' => true,
			    'field_params' => array(
				'std' => esc_attr($cs_title_color),
				'cust_id' => '',
				'classes' => 'bg_color',
				'cust_name' => 'cs_title_color[]',
				'return' => true,
			    ),
			);

			$jobcareer_html_fields->cs_text_field($cs_opt_array);


			$cs_opt_array = array(
			    'name' => esc_html__('Button Text Color', 'jobcareer'),
			    'desc' => '',
			    'hint_text' => esc_html__("Set the button Text color.", 'jobcareer'),
			    'echo' => true,
			    'field_params' => array(
				'std' => esc_attr($cs_text_color),
				'cust_id' => '',
				'classes' => 'bg_color',
				'cust_name' => 'cs_text_color[]',
				'return' => true,
			    ),
			);

			$jobcareer_html_fields->cs_text_field($cs_opt_array);

			$cs_opt_array = array(
			    'name' => esc_html__('Button Background Color', 'jobcareer'),
			    'desc' => '',
			    'hint_text' => esc_html__("Set the BG color for your about us.", 'jobcareer'),
			    'echo' => true,
			    'field_params' => array(
				'std' => esc_attr($cs_bg_color),
				'cust_id' => '',
				'classes' => 'bg_color',
				'cust_name' => 'cs_bg_color[]',
				'return' => true,
			    ),
			);

			$jobcareer_html_fields->cs_text_field($cs_opt_array);

			$cs_opt_array = array(
			    'name' => esc_html__('Content Color', 'jobcareer'),
			    'desc' => '',
			    'hint_text' => esc_html__("Set the content color.", 'jobcareer'),
			    'echo' => true,
			    'field_params' => array(
				'std' => esc_attr($cs_content_color),
				'cust_id' => '',
				'classes' => 'bg_color',
				'cust_name' => 'cs_content_color[]',
				'return' => true,
			    ),
			);

			$jobcareer_html_fields->cs_text_field($cs_opt_array);


			$cs_opt_array = array(
			    'name' => esc_html__('Style', 'jobcareer'),
			    'desc' => '',
			    'hint_text' => esc_html__("Please select the about info style.", 'jobcareer'),
			    'echo' => true,
			    'field_params' => array(
				'std' => $cs_about_info_style,
				'id' => '',
				'cust_name' => 'cs_about_info_style[]',
				'classes' => 'dropdown chosen-select',
				'options' => array(
				    '' => esc_html__('Default', 'jobcareer'),
				    'modern' => esc_html__('Modern', 'jobcareer'),
				    'classic' => esc_html__('Classic', 'jobcareer'),
				),
				'return' => true,
			    ),
			);
			$jobcareer_html_fields->cs_select_field($cs_opt_array);
			 $rand_id = rand(1111111, 9999999);
			?>
			<div class="cs-sh-icon_box-icon-area" style="display:<?php echo esc_html($cs_icon_box_icon_type != 'image' ? 'block' : 'none') ?>;">
			    <div class="form-elements" id="cs_infobox_<?php echo esc_attr($rand_id); ?>">
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
				    <label><?php esc_html_e('Icon Box Icon', 'jobcareer'); ?></label>
				    <?php
				    if (function_exists('jobcareer_tooltip_text')) {
					echo jobcareer_tooltip_text(esc_html__('Select the Icons you would like to show with your accordion title.', 'jobcareer'));
				    }
				    ?>
				</div>
				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
				    <?php jobcareer_fontawsome_icons_box($cs_icon_box_icon, $rand_id, 'cs_icon_box_icon'); ?>
				    <p></p>
				</div>
			    </div>
			    <?php
			    // $jobcareer_html_fields->cs_text_field($cs_opt_array);

			    $cs_opt_array = array(
				'name' => esc_html__('Icon Color', 'jobcareer'),
				'desc' => '',
				'hint_text' => esc_html__('Set the position of icon_box image here', 'jobcareer'),
				'echo' => true,
				'field_params' => array(
				    'std' => esc_html($cs_icon_box_icon_color),
				    'id' => 'cs_icon_box_icon_color',
				    'cust_name' => 'cs_icon_box_icon_color[]',
				    'classes' => 'bg_color',
				    'return' => true,
				),
			    );

			    //$jobcareer_html_fields->cs_text_field($cs_opt_array);
			    ?>
			</div>  
			<?php
			$cs_opt_array = array(
			    'name' => esc_html__('Content', 'jobcareer'),
			    'desc' => '',
			    'hint_text' => '',
			    'echo' => true,
			    'field_params' => array(
				'std' => esc_html($content_texarea),
				'id' => 'content_texarea',
				'cust_name' => 'content_texarea[]',
				'cs_editor' => true,
				'return' => true,
			    ),
			);

			$jobcareer_html_fields->cs_textarea_field($cs_opt_array);


			$cs_opt_array = array(
			    'std' => $cs_image_url,
			    'id' => 'image_about_url',
			    'name' => esc_html__('Image url', 'jobcareer'),
			    'desc' => '',
			    'hint_text' => esc_html__('Select image from media gallery.', 'jobcareer'),
			    'echo' => true,
			    'array' => true,
			    'field_params' => array(
				'std' => $cs_image_about_url,
				'cust_id' => '',
				'id' => 'image_about_url',
				'return' => true,
				'array' => true,
				'array_txt' => false,
			    ),
			);
			$jobcareer_html_fields->cs_upload_file_field($cs_opt_array);
			?>
		    </div>
			<?php if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') { ?>
	    	    <ul class="form-elements insert-bg">
	    		<li class="to-field"> <a class="insert-btn cs-main-btn" onclick="javascript:Shortcode_tab_insert_editor('<?php echo esc_js(str_replace('jobcareer_pb_', '', $name)); ?>', '<?php echo esc_js($name . $cs_counter) ?>', '<?php echo esc_js($filter_element); ?>')" ><?php esc_html_e('Insert', 'jobcareer'); ?></a> </li>
	    	    </ul>
	    	    <div id="results-shortocde"></div>
	<?php } else { ?>
	    <?php
	    $cs_opt_array = array(
		'std' => 'about',
		'id' => '',
		'before' => '',
		'after' => '',
		'classes' => '',
		'extra_atr' => '',
		'cust_id' => '',
		'cust_name' => 'cs_orderby[]',
		'return' => true,
		'required' => false
	    );
	    echo jobcareer_special_char($jobcareer_form_fields->cs_form_hidden_render($cs_opt_array));
	    ?>
			<?php
			$cs_opt_array = array(
			    'name' => '',
			    'desc' => '',
			    'hint_text' => '',
			    'echo' => true,
			    'field_params' => array(
				'std' => esc_html__('Save', 'jobcareer'),
				'cust_id' => '',
				'cust_type' => 'button',
				'classes' => 'cs-admin-btn',
				'cust_name' => '',
				'extra_atr' => 'onclick="javascript:_removerlay(jQuery(this))"',
				'return' => true,
			    ),
			);

			$jobcareer_html_fields->cs_text_field($cs_opt_array);
			?>               
		    <?php } ?>
		    <script>
			/* modern selection box function */
			jQuery(document).ready(function ($) {
			    chosen_selectionbox();
			    popup_over();
			});
			/* modern selection box function */
		    </script>
		</div>
	    </div>
	</div>
	<?php
	if ($die <> 1) {
	    die();
	}
    }

    add_action('wp_ajax_jobcareer_pb_about', 'jobcareer_pb_about');
}