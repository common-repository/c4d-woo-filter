<?php
/*
Plugin Name: C4D Woo Filter
Plugin URI: http://coffee4dev.com/c4d-woo-filter
Description: C4D Woo Filter - create filter by ajax for WooCommerce category.
Author: Coffee4dev.com
Author URI: http://coffee4dev.com/
Text Domain: c4d-woo-filter
Version: 1.0.7
*/

define('C4DWOOFILTER_PLUGIN_URI', plugins_url('', __FILE__));

add_action( 'admin_enqueue_scripts', 'c4d_woo_filter_load_scripts_admin' );
add_action( 'wp_enqueue_scripts', 'c4d_woo_filter_load_scripts_site');
add_action( 'admin_enqueue_scripts', 'c4d_woo_filter_load_scripts_admin');
add_action( 'c4d-plugin-manager-section', 'c4d_woo_filter_section_options', 1000);
add_filter( 'plugin_row_meta', 'c4d_woo_filter_plugin_row_meta', 10, 2 );
add_filter( 'body_class', 'c4d_woo_filter_body_class');

function c4d_woo_filter_load_scripts_site() {
   wp_enqueue_script( 'c4d-woo-filter-site-js', C4DWOOFILTER_PLUGIN_URI . '/assets/default.js', array( 'jquery' ), false, true );
   wp_enqueue_style( 'c4d-woo-filter-site-style', C4DWOOFILTER_PLUGIN_URI.'/assets/default.css' );
}

function c4d_woo_filter_load_scripts_admin($hook) {
    if (in_array($hook, array('post.php'))) {
        wp_enqueue_script( 'c4d-woo-filter-admin-js', C4DWOOFILTER_PLUGIN_URI . '/assets/admin.js' );
        wp_enqueue_style( 'c4d-woo-filter-admin-style', C4DWOOFILTER_PLUGIN_URI.'/assets/admin.css' );
    }
}

function c4d_woo_filter_body_class($classes) {
    global $c4d_plugin_manager;
    if (isset($c4d_plugin_manager['c4d-woo-filter-prefix-class'])) {
        $classes = array_merge($classes, array( $c4d_plugin_manager['c4d-woo-filter-prefix-class'] ));
    }
    if (isset($c4d_plugin_manager['c4d-woo-filter-load-more']) && $c4d_plugin_manager['c4d-woo-filter-load-more'] != 'off' || isset($_GET['loadmore'])) {
        $addClass = array('c4d-woo-filter-load-more-active');
        if(isset($_GET['loadmore']) && $_GET['loadmore'] != '') {
            $addClass[] = 'c4d-woo-filter-load-more-'.esc_attr($_GET['loadmore']);
        } else if (isset($c4d_plugin_manager['c4d-woo-filter-load-more'])) {
            $addClass[] = 'c4d-woo-filter-load-more-'.$c4d_plugin_manager['c4d-woo-filter-load-more'];
        }

        $classes = array_merge($classes, $addClass);

    }
    return $classes;
}

include_once (dirname(__FILE__). '/includes/hook.php');
include_once (dirname(__FILE__). '/includes/shortcodes.php');
include_once (dirname(__FILE__). '/includes/widgets.php');

function c4d_woo_filter_section_options(){
    $opt_name = 'c4d_plugin_manager';
    $fileds = array(
        array(
            'id'       => 'c4d-woo-filter-prefix-class',
            'type'     => 'text',
            'title'    => esc_html__('Prefix Class', 'c4d-woo-filter'),
            'subtitle' => esc_html__('Insert Prefix Class For Style', 'c4d-woo-filter'),
            'default'  => 'c4d-woo-filter-prefix-class'
        ),
        array(
            'id'       => 'c4d-woo-filter-load-more',
            'type'     => 'button_set',
            'title'    => esc_html__('Load More', 'c4d-woo-filter'),
            'subtitle' => esc_html__('Load More', 'c4d-woo-filter'),
            'options' => array(
                'off' => esc_html__('Off', 'c4d-woo-filter'),
                'loadmore' => esc_html__('Load More', 'c4d-woo-filter'),
                'scroll' => esc_html__('Scroll Load', 'c4d-woo-filter')
             ),
            'default'  => 'off'
        ),
    );

    Redux::setSection( $opt_name, array(
        'title'            => esc_html__( 'Woo Filter', 'c4d-woo-filter' ),
        'id'               => 'section-c4d-woo-filter',
        'desc'             => '',
        'customizer_width' => '400px',
        'icon'             => 'el el-home',
        'fields'           => $fileds
    ));
}

function c4d_woo_filter_plugin_row_meta($links, $file) {
    return $links;
}
