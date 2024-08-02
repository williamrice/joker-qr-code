<?php
/*
Plugin Name: Joker QR Code
Description: A plugin to generate QR codes for Directorist listings.
Version: 1.0
Author: Joker Business Solutions
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define the plugin path
define('JOKER_QR_CODE_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include the QR code library
require_once JOKER_QR_CODE_PLUGIN_DIR . 'includes/phpqrcode/phpqrcode.php';

// Hook into the 'directorist_single_listing_after_title' action
add_action('directorist_single_listing_after_title', 'joker_qr_code_display', 10, 1);

/**
 * Function to display the QR code.
 */
function joker_qr_code_display($listing_id)
{
    // if current path doesn't have a url parameter 'kiosk=true' then return
    if (!isset($_GET['kiosk']) || $_GET['kiosk'] !== 'true') {
        return;
    }
    $content = '';

    $listing = get_post($listing_id);

    if (!$listing) {
        return;
    }

    $website_url = get_post_meta($listing_id, '_website', true);
    error_log('website_url: ' . $website_url);

    if ($website_url) {
        $content = $website_url;
    } else {
        return;
    }

    // Generate QR code
    ob_start();
    QRcode::png($content, null, QR_ECLEVEL_H, 4);
    $qr_code = ob_get_contents();
    ob_end_clean();

    // Display QR code
    echo '<div class="joker-qr-code">';
    echo '<h3>Scan the QR code for more information about this listing</h3>';
    echo '<img src="data:image/png;base64,' . base64_encode($qr_code) . '" alt="QR Code" />';
    echo '</div>';
}