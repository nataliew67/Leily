<?php

/**
 * Plugin Name:         Variation Images Gallery for WooCommerce
 * Plugin URI:          https://radiustheme.com
 * Description:         Variation Images Gallery for WooCommerce plugin allows to add UNLIMITED additional images for each variation of product.
 * Version:             2.3.2
 * Author:              RadiusTheme
 * Author URI:          https://radiustheme.com
 * Requires at least:   4.8
 * Tested up to:        6.2
 * WC requires at least:3.2
 * WC tested up to:     7.6.0
 * Domain Path:         /languages
 * Text Domain:         woo-product-variation-gallery.
 */
defined('ABSPATH') or die('Keep Silent');

// Define RTWPVG_PLUGIN_FILE.
if (!defined('RTWPVG_PLUGIN_FILE')) {
  define('RTWPVG_PLUGIN_FILE', __FILE__);
}

// Define RTWPVG_VERSION.
if (!defined('RTWPVG_VERSION')) {
  define('RTWPVG_VERSION', '2.3.2');
}

if (!class_exists('WooProductVariationGallery')) {
  require_once 'app/WooProductVariationGallery.php';
}
