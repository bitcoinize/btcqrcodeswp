<?php
/**
 * Plugin Name:       Bitcoin QRCodes for Wordpress
 * Plugin URI:        https://bitcoinize.com
 * Description:       [secure_qrcode address="…" type="onchain|lightning|liquid|silent" size="50-200"] – QR com qrious, recorte automático, logo central, <img> responsivo e labels.
 * Version:           1.5.7
 * Author:            Bitcoinize.com
 * Author URI:        https://bitcoinize.com
 * Text Domain:       bitcoin-qrcodes-wp
 */

namespace Bitcoin_QRCodes_WP;
defined('ABSPATH') || exit;

class Plugin {

    private static $types = [
        'onchain'   => 'onchain.jpg',
        'lightning' => 'lightning.jpg',
        'liquid'    => 'liquid.jpg',
        'silent'    => 'silent.jpg',
    ];

    private static $type_labels = [
        'onchain'   => 'Bitcoin Onchain',
        'lightning' => 'Lightning Address',
        'liquid'    => 'Liquid Address',
        'silent'    => 'Silent Payment',
    ];

    public static function init() {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_shortcode('secure_qrcode',   [__CLASS__, 'shortcode']);
    }

    public static function enqueue_assets() {
        $base_url  = plugin_dir_url(__FILE__);
        $base_path = plugin_dir_path(__FILE__);

        // Qrious local ou fallback CDN
        if (file_exists($base_path . 'js/qrious.min.js')) {
            wp_enqueue_script('qrious', $base_url . 'js/qrious.min.js', [], '4.0.2', true);
        } else {
            wp_enqueue_script('qrious', 'https://unpkg.com/qrious@4.0.2/dist/qrious.min.js', [], '4.0.2', true);
        }

        // JS que recorta e exporta para <img>
        wp_enqueue_script(
            'btc-qrcodes-wp',
            $base_url . 'js/btcqrcodes-img.js',
            ['qrious'],
            '1.5.0',
            true
        );

        // CSS responsivo
        wp_enqueue_style(
            'btc-qrcodes-css',
            $base_url . 'css/btcqrcodes.css',
            [],
            '1.5.7'
        );
    }

    public static function shortcode($atts) {
        $atts = shortcode_atts([
            'address' => '',
            'type'    => 'onchain',
            'size'    => 200,
        ], $atts, 'secure_qrcode');

        $address = sanitize_text_field($atts['address']);
        if ($address === '') {
            return '';
        }

        $type = sanitize_key($atts['type']);
        if (!isset(self::$types[$type])) {
            $type = 'onchain';
        }

        // tamanho base de geração (px)
        $size = max(50, min((int) $atts['size'], 200));

        $logo_url = plugin_dir_url(__FILE__) . 'img/' . self::$types[$type];
        $uniq     = esc_attr(wp_unique_id('secure_qr_'));

        // IMG placeholder
        $img = sprintf(
            '<img id="%1$s" class="btcqr-img secure_qr_img" src="" alt="%2$s" ' .
            'data-address="%2$s" data-logo="%4$s" data-size="%3$d" width="%3$d" height="%3$d" ' .
            'aria-label="QR code for %2$s" />',
            $uniq,
            esc_attr($address),
            esc_attr($size),
            esc_url($logo_url)
        );

        // Labels
        $type_label = self::$type_labels[$type] ?? 'Bitcoin Onchain';
        $addr_txt   = (mb_strlen($address) > 150)
            ? mb_substr($address, 0, 75) . ' ... ' . mb_substr($address, -75)
            : $address;

        $label_block = '<div class="btcqr-label">'
                     . '<div class="btcqr-type">'.esc_html($type_label).'</div>'
                     . '<div class="btcqr-address">'.esc_html($addr_txt).'</div>'
                     . '</div>';

        return '<div class="btcqr-wrap">'.$img.$label_block.'</div>';
    }
}

Plugin::init();
