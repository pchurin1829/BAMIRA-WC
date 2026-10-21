<?php
/** BAMIRA theme. Catalog sales gate lives separately in the plugin. */
add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'custom-logo' );
} );
add_action( 'wp_enqueue_scripts', function () {
    $css_file = get_stylesheet_directory() . '/style.css';
    $version  = file_exists( $css_file ) ? (string) filemtime( $css_file ) : '1.0.0';
    wp_enqueue_style( 'bamira', get_stylesheet_uri(), array(), $version );
} );

/* Colecciones del catálogo: ancla de portada, título y SKUs. */
function bamira_catalog_collections() {
    return array(
        'bikinis'    => array(
            'title' => 'Bikinis',
            'skus'  => array( 'BAM-TOP-MILE', 'BAM-TOP-GIGI', 'BAM-VEDETINA', 'BAM-COLALESS' ),
        ),
        'activewear' => array(
            'title' => 'Activewear',
            'skus'  => array( 'BAM-TOP-DEP', 'BAM-CALZA-DEP', 'BAM-SHORT-BREN', 'BAM-SHORT-KAI' ),
        ),
    );
}
function bamira_collection_anchor( $sku ) {
    foreach ( bamira_catalog_collections() as $anchor => $collection ) {
        if ( in_array( $sku, $collection['skus'], true ) ) { return $anchor; }
    }
    return '';
}
function bamira_color_swatches() {
    return array(
        'negro'           => '#202024',
        'bordo-frambuesa' => '#781e3e',
        'azul'            => '#243f77',
        'petroleo'        => '#235957',
        'chocolate'       => '#54392e',
    );
}
function bamira_product_url( $product ) {
    if ( ! $product ) { return home_url( '/' ); }
    if ( 'draft' === $product->get_status() ) { return get_preview_post_link( $product->get_id() ); }
    return get_permalink( $product->get_id() );
}
function bamira_collection_url( $product ) {
    $anchor = bamira_collection_anchor( $product ? $product->get_sku() : '' );
    return $anchor ? home_url( '/#' . $anchor ) : home_url( '/' );
}

function bamira_collection( $title, $anchor, $skus ) {
    if ( ! function_exists( 'wc_get_product_id_by_sku' ) ) { return; }
    $preview = current_user_can( 'manage_woocommerce' );
    $swatches = bamira_color_swatches();
    echo '<section class="section" id="' . esc_attr( $anchor ) . '"><div class="wrap"><div class="section-head"><div><span class="eyebrow">La colección</span><h2>' . esc_html( $title ) . '</h2></div><p>Talles 1 · 2 · 3</p></div><div class="grid">';
    $count = 0;
    foreach ( $skus as $sku ) {
        $p = wc_get_product( wc_get_product_id_by_sku( $sku ) );
        if ( ! $p || ( 'publish' !== $p->get_status() && ! ( $preview && 'draft' === $p->get_status() ) ) ) { continue; }
        ++$count;
        $url = bamira_product_url( $p );
        echo '<article class="product-card"><a class="product-art" href="' . esc_url( $url ) . '" aria-label="' . esc_attr( 'Ver la ficha de ' . $p->get_name() ) . '">';
        if ( $p->get_image_id() ) { echo wp_get_attachment_image( $p->get_image_id(), 'woocommerce_thumbnail' ); }
        else { echo '<span aria-hidden="true">B.</span><small>Imagen próximamente</small>'; }
        echo '</a><h3><a href="' . esc_url( $url ) . '">' . esc_html( $p->get_name() ) . '</a></h3><div class="colors" aria-label="Colores previstos">';
        $names = wc_get_product_terms( $p->get_id(), 'pa_color-diseno', array( 'fields' => 'names' ) );
        foreach ( $names as $name ) {
            $hex = $swatches[sanitize_title($name)] ?? '#ddd';
            echo '<span class="swatch" role="img" aria-label="' . esc_attr($name) . '" title="' . esc_attr($name) . '" style="--swatch:' . esc_attr($hex) . '"></span>';
        }
        echo '</div><p>' . esc_html( implode( ' · ', $names ) ) . '</p><p>Talles 1, 2 y 3</p><p>Próximamente</p></article>';
    }
    if ( ! $count ) { echo '<p>Estamos preparando esta colección.</p>'; }
    echo '</div></div></section>';
}

// BAMIRA: plantilla de ficha para los ocho modelos iniciales.
add_filter('template_include', function ($template) {
    if (!is_singular('product') || !function_exists('wc_get_product')) {
        return $template;
    }

    $product = wc_get_product(get_queried_object_id());
    $skus = array();
    foreach (bamira_catalog_collections() as $collection) {
        $skus = array_merge($skus, $collection['skus']);
    }

    if ($product && in_array($product->get_sku(), $skus, true)) {
        $custom = get_theme_file_path('single-product.php');
        if (is_readable($custom)) {
            return $custom;
        }
    }

    return $template;
}, 99);
