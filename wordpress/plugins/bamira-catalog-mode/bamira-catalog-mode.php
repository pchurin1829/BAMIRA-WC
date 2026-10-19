<?php
/**
 * Plugin Name: BAMIRA — Catálogo en preparación
 * Description: Bloquea compras mientras se completa la información comercial. Desactivar al habilitar ventas.
 * Version: 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
add_filter( 'woocommerce_is_purchasable', '__return_false', 999 );
add_filter( 'woocommerce_variation_is_purchasable', '__return_false', 999 );
add_filter( 'woocommerce_add_to_cart_validation', '__return_false', 999 );
add_action( 'woocommerce_check_cart_items', function () {
    wc_add_notice( 'BAMIRA está preparando su colección. Las compras todavía no están habilitadas.', 'error' );
} );
add_filter( 'woocommerce_get_price_html', function () { return '<span class="bamira-coming-soon">Próximamente</span>'; }, 999 );
add_filter( 'woocommerce_get_availability_text', function () { return 'Disponibilidad a confirmar'; }, 999 );
add_action( 'admin_notices', function () {
    if ( current_user_can( 'manage_woocommerce' ) ) { echo '<div class="notice notice-info"><p>BAMIRA: modo catálogo activo. Compras deshabilitadas hasta confirmar precios, imágenes y stock.</p></div>'; }
} );
