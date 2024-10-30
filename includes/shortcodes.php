<?php 
//shortcode
add_shortcode('c4d-woo-filter', 'c4d_woo_filter_main_shortcode');
add_shortcode('c4d-woo-filter-soft', 'c4d_woo_filter_shortcode_soft');
add_shortcode('c4d-woo-filter-price', 'c4d_woo_filter_shortcode_price');
add_shortcode('c4d-woo-filter-tax', 'c4d_woo_filter_shortcode_tax');
add_shortcode('c4d-woo-filter-tag', 'c4d_woo_filter_shortcode_tag');

// filter additions for woocommerce shortcode
add_action('woocommerce_shortcode_products_query', 'c4d_woo_filter_shortcode_only_queries');

function c4d_woo_filter_shortcode_only_queries($queries) {
    global $wp_query;
    $orderby_value = isset( $_GET['shortcode_orderby'] ) ? wc_clean( (string) wp_unslash( $_GET['shortcode_orderby'] ) ) : '';
    if ($orderby_value) {
        if ($orderby_value == 'price') {
            $queries['orderby'] = 'meta_value_num';
            $queries['order'] = 'ASC';
            $queries['meta_key'] = '_price';
        } else if ($orderby_value == 'price-desc') {
            $queries['orderby'] = 'meta_value_num';
            $queries['order'] = 'DESC';
            $queries['meta_key'] = '_price';
        } else {
            $order = WC()->query->get_catalog_ordering_args($orderby_value, $queries['order']);
            $queries['orderby']        = $order['orderby'];
            $queries['order']          = $order['order'];
            if ( $order['meta_key'] ) {
                $queries['meta_key']       = $order['meta_key'];
            }
        }
    }
    
    $tax_query = array();

    foreach ( WC_Query::get_layered_nav_chosen_attributes() as $taxonomy => $data ) {
        $tax_query[] = array(
            'taxonomy'         => $taxonomy,
            'field'            => 'slug',
            'terms'            => $data['terms'],
            'operator'         => 'and' === $data['query_type'] ? 'AND' : 'IN',
            'include_children' => false,
        );
    }

    $queries['tax_query'] = array_merge($queries['tax_query'], $tax_query);
    
    return $queries;
}

function c4d_woo_filter_main_shortcode($args) {
    $shortcodes = array('sort', 'price', 'tax', 'tag');
    $html = '<div class="c4d-woo-filter c4w-woo-filter-main">';
    foreach ($shortcodes as $shortcode) {
        $function = 'c4d_woo_filter_template_'.$shortcode;
        if (function_exists($function)) {
            $html .= $function();    
        }
    }
    $html .= '</div>';
    return $html;
}

function c4d_woo_filter_shortcode_soft($args) {
    $args = is_array($args) ? $args : array();
    return c4d_woo_filter_template_sort($args);
}

function c4d_woo_filter_shortcode_price($args) {
    $args = is_array($args) ? $args : array();
    return c4d_woo_filter_template_price($args);
}

function c4d_woo_filter_shortcode_tax($args) {
    $args = is_array($args) ? $args : array();
	return c4d_woo_filter_template_tax($args);
}

function c4d_woo_filter_shortcode_tag($args) {
    $args = is_array($args) ? $args : array();
    return c4d_woo_filter_template_tag($args);
}

function c4d_woo_filter_template_tag($shortcodeOptions = array()) {
	$attr = array_merge(array('class' => 'c4d-woo-filter-template-tag'), (is_array($shortcodeOptions) ? $shortcodeOptions : array()));
    $html = '<div class="'.esc_attr($attr['class']).'"><div class="tagcloud">';
    ob_start();
    wp_tag_cloud( array(
        'taxonomy' => 'product_tag'));
    $html .= ob_get_contents();
    ob_end_clean();
    $html .= '</div></div>';
    return $html;
}
function c4d_woo_filter_template_tax($shortcodeOptions = array()) {
    global $c4d_plugin_manager;
    if (!function_exists('wc_get_attribute_taxonomies')) return;
    $attr = array_merge(array('class' => 'c4d-woo-filter-template-tax'), (is_array($shortcodeOptions) ? $shortcodeOptions : array()));
    $taxs = wc_get_attribute_taxonomies();
    $html = '';
    $selectTaxs = (isset($attr['tax']) && $attr['tax'] != '') ? array_map('trim', explode(',', $attr['tax'])) : array();

	foreach ($taxs as $tax) {
		if (count($selectTaxs) > 0 && !in_array($tax->attribute_name, $selectTaxs)) {
			continue;	
		}
		$terms = get_terms( wc_attribute_taxonomy_name($tax->attribute_name), 'orderby=name&hide_empty=0');
        $html .= '<ul class="'.esc_attr($attr['class']).'" data-tax="'.$tax->attribute_name.'">';
        
        foreach ($terms as $key => $term) {
            $html .= '<li>';
            $html .= '<a data-filter-name="'.esc_attr('filter_' . $tax->attribute_name).'" data-filter-value="'.esc_attr($term->slug).'">';

            if ($tax->attribute_name == 'color' && isset($c4d_plugin_manager['c4d-woo-filter-color-'. $term->slug])) {
                $color = $c4d_plugin_manager['c4d-woo-filter-color-'. $term->slug];
                if (is_array($color) && $color['color'] != '') {
                    $html .= '<span class="color" style= "background: '.$color['color'].'" data-color="'.$color['color'].'"></span>';    
                }
            }
            $html .= $term->name;
            $html .= '</a>';
            $html .= '</li>';
        }
        $html .= '</ul>';
    } 
    return $html;
}
function c4d_woo_filter_template_price($shortcodeOptions = array()) {
	$attr = array_merge(array('class' => 'c4d-woo-filter-template-price'), (is_array($shortcodeOptions) ? $shortcodeOptions : array()));
    $prices = array(
        'default' => esc_html__('Default', 'c4d-woo-filter'),
        'step' => 10,
        'symbol' => get_woocommerce_currency_symbol()
    );
    $step = $prices['step'];
    $symbol = $prices['symbol'];

    $html = '<ul class="'.esc_attr($attr['class']).'">';
    $html .= '<li><a href="'.esc_url( add_query_arg(array())).'">'.esc_html__('All', 'c4d-woo-filter').'</a></li>';
    for ($i = 0; $i < 5; $i++) {
        $min = round($i * $step, 2);
        $max = $min + $step;
        $text = $symbol.$min . ' - ' . $symbol.$max;
        if ($i == 4) {
            $text = $symbol.$min . "+";
            $html .= '<li><a data-min-price="'.esc_attr($min).'" >'.$text.'</a></li>';
        } else {
            $html .= '<li><a data-min-price="'.esc_attr($min).'" data-max-price="'.esc_attr($max).'">'.$text.'</a></li>';
        }
    }
    $html .= '</ul>';
    return $html;
}

function c4d_woo_filter_template_sort($shortcodeOptions = array()) {
    $orderName = is_product_category() ? 'orderby' : 'shortcode_orderby';
    $attr = array_merge(array('class' => 'c4d-woo-filter-template-sort'), (is_array($shortcodeOptions) ? $shortcodeOptions : array()));
    $sorts = array(
        'default' => esc_html__('Default', 'c4d-woo-filter'),
        'popularity' => esc_html__('Popularity', 'c4d-woo-filter'),
        'date' => esc_html__('Newness', 'c4d-woo-filter'),
        'rate'=> esc_html__('Average', 'c4d-woo-filter'),
        'price'=> esc_html__('Price: Low to High', 'c4d-woo-filter'),
        'price-desc' => esc_html__('Price: High to Low', 'c4d-woo-filter'),
    );
    $html = '<ul class="'.esc_attr($attr['class']).'">';
    foreach ($sorts as $key => $value) {
        $html .= '<li><a data-filter-name="'.esc_attr($orderName).'" data-filter-value="'.esc_attr($key).'">'.$value.'</a></li>';
    }
    $html .= '</ul>';
    return $html;
}