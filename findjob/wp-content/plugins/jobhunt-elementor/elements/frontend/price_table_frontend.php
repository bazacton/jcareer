<?php

class Jobhunt_PriceTable_Frontend
{

    public function render($settings)
    {
        global $pricetable_style , $jobcareer_multi_price_table_class , $column_class , $testimonial_text_color , $jobcareer_multi_price_table_section_title , $post , $cs_multi_price_col;
        $randomid = rand(10000 , 99999);

        $defaults = array(
            'column_size' => '' ,
            'jobcareer_multi_price_table_section_title' => '' ,
            'pricetable_style' => '' ,
            'cs_multi_price_col' => '' ,
            'pricetable_item' => array() ,
        );
        extract(shortcode_atts($defaults , $settings));
        $column_class = jobcareer_custom_column_class($column_size);

        $html = '';
        if (isset($jobcareer_multi_price_table_section_title) and $jobcareer_multi_price_table_section_title <> '') {
            $html .= '<div class="cs-element-title">';
            $html .= '<h2>' . esc_attr($jobcareer_multi_price_table_section_title) . '</h2>';
            $html .= '</div>';
        }
        $html .= '<ul class="cs-pricetable">';
        $html .= $this->render_pricetable_items_array($pricetable_item);
        $html .= '</ul>';

        echo '<div class="' . $column_class . '"> ' . $html . '</div>';
    }


    public function render_pricetable_items_array($pricetable_item_array)
    {
        $testimonial_item_response = '';
        if (!empty($pricetable_item_array)) {
            foreach ($pricetable_item_array as $pricetable_item) {
                $pricetable_item_response .= $this->render_pricetable_item($pricetable_item);
            }
        }
        return $pricetable_item_response;
    }

    public function render_pricetable_item($atts)
    {
        global $pricetable_style , $post , $cs_multi_price_col;
        $col_class = '';

        if (isset($cs_multi_price_col) && $cs_multi_price_col != '') {
            $number_col = 12 / $cs_multi_price_col;
            $number_col_sm = 12;
            $number_col_xs = 12;
            if ($number_col == 2) {
                $number_col_sm = 4;
                $number_col_xs = 6;
            }
            if ($number_col == 3) {
                $number_col_sm = 6;
                $number_col_xs = 12;
            }
            if ($number_col == 4) {
                $number_col_sm = 6;
                $number_col_xs = 12;
            }
            if ($number_col == 6) {
                $number_col_sm = 12;
                $number_col_xs = 12;
            }
            $col_class = 'col-lg-' . $number_col . ' col-md-' . $number_col . ' col-sm-' . $number_col_sm . ' col-xs-' . $number_col_xs . '';
        }

        $defaults = array(
            'multi_price_table_text' => '' ,
            'multi_price_table_currency' => '' ,
            'multi_price_table_time_duration' => '' ,
            'multi_pricetable_price' => '' ,
            'multi_price_table_button_text' => '' ,
            'multi_price_table_title_color' => '' ,
            'multi_price_table_button_color' => '' ,
            'pricing_features' => '' ,
            'pricetable_featured' => '' ,
            'multi_price_table_button_color_bg' => '' ,
            'multi_price_table_button_column_color' => '' ,
            'multi_price_table_column_bgcolor' => '' ,
            'button_link' => '' ,
        );
        extract(shortcode_atts($defaults , $atts));


        if (empty($button_link) || $button_link == '#') {
            $button_link = 'javascript:void()';
        } else {
            $button_link = esc_url($button_link);
        }
        $html = '';
        $pricing_features = isset($pricing_features) ? $pricing_features : '';
        $bg_color = "";
        $column_bg_color = "";
        if (isset($multi_price_table_column_bgcolor) && $multi_price_table_column_bgcolor <> '') {
            $column_bg_color = esc_attr($multi_price_table_column_bgcolor);
        }
        $featured_cell = "";
        if (isset($pricetable_featured) && $pricetable_featured == 'Yes') {
            $featured_cell = "active";
        }
        $html .= '<li class="' . $col_class . '">';
        $html .= '<div style="background-color:#fff;" class="pricetable-holder ' . esc_attr($featured_cell) . '">';
        $html .= '<h2 style="color:' . esc_attr($multi_price_table_title_color) . ' !important; background:' . esc_attr($column_bg_color) . ';">' . esc_attr($multi_price_table_text) . '</h2>';
        $html .= '<div class="price-holder">';
        $html .= '<div class="cs-price">';
        $html .= '<span><em>' . esc_attr($multi_price_table_currency) . '' . esc_attr($multi_pricetable_price) . '</em><small>' . esc_attr($multi_price_table_time_duration) . '</small></span>';
        $html .= '<p>' . $pricing_features . '</p>';
        $html .= '</div>';
        $html .= '<a style="background-color:' . $multi_price_table_button_column_color . ' !important; color:' . $multi_price_table_button_color . ' !important" class="cs-bgcolor cs-button" href="' . $button_link . '">' . esc_attr($multi_price_table_button_text) . '</a>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</li>';

        return $html;
    }

}

