<?php

namespace Rtwpvg\Controllers;

use Rtwpvg\Helpers\Functions;

class ThemeSupport
{
    /**
     * ThemeSupport constructor.
     * Add Theme Support for different theme
     */
    public function __construct() {
        add_action('init', array($this, 'add_theme_support'), 200); 
        
        // Flatsome Theme Custom Layout Support
        add_filter( 'wc_get_template_part', array($this, 'rtwpvg_gallery_template_part_override'), 30, 2 );

        add_action( 'after_setup_theme', [ $this, 'after_setup_theme' ] );
    }

    function after_setup_theme() {
        if ( function_exists( 'woostify_is_woocommerce_activated' ) ) {
            remove_action('woocommerce_before_single_product_summary', 'woostify_single_product_gallery_image_slide', 30);
            remove_action('woocommerce_before_single_product_summary', 'woostify_single_product_gallery_thumb_slide', 40);
            add_action( 'woocommerce_before_single_product_summary', [ __CLASS__, 'woocommerce_show_product_images'], 22 );
        }
		// Astra Pro Addons Theme Support
        if ( defined('ASTRA_EXT_FILE') ) {
            add_filter( 'astra_addon_override_single_product_layout', '__return_false' );
        }

    }

	public static function woocommerce_show_product_images() {
		Functions::get_template( 'product-images' );
	}


    function add_theme_support() {
        // if ( function_exists( 'woostify_is_woocommerce_activated' ) ) {
        //     self::remove_wc_default_template();
        // }
        // Electro Theme remove extra gallery
        if (apply_filters('rtwpvg_add_electro_theme_support', true)) {
            remove_action('woocommerce_before_single_product_summary', 'electro_show_product_images', 20);
        }

    } 

    /*
    public static function remove_wc_default_template() {
		if ( apply_filters( 'rtwpvg_remove_wc_default_template', true ) ) {
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 10 );
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
			remove_action( 'woocommerce_before_single_product_summary_product_images', 'woocommerce_show_product_thumbnails', 20 );
		}
	}
    */
	 
    function rtwpvg_gallery_template_part_override( $template, $template_name ) {
        
        $old_template = $template;
        
        // Disable gallery on specific product
        
        if ( apply_filters( 'disable_woo_variation_gallery', false ) ) {
            return $old_template;
        } 
        
        if ( $template_name == 'single-product/product-image' ) {
            $template = rtwpvg()->locate_template('product-images');
        }
        
        if ( $template_name == 'single-product/product-thumbnails' ) {
            $template = rtwpvg()->locate_template('product-thumbnails');
        }
        
        return apply_filters( 'rtwpvg_gallery_template_part_override_location', $template, $template_name, $old_template );
    } 
} 