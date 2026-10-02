<?php
/**
 * BAMIRA — Seed de productos base (CATÁLOGO-2B + CATÁLOGO-3).
 *
 * Alcance:
 *  - Asegura en el atributo global existente `pa_color-diseno` los 7 términos
 *    (Blanco, Negro, Verde aceituna, Azul, Marrón, Celeste + Naranja / Gris).
 *  - Crea 8 productos base como borrador (`draft`), tipo `variable`, SIN
 *    variaciones físicas, SIN precio/stock/imágenes/costo/proveedor.
 *
 * Lo que NO hace (por diseño):
 *  - No crea atributos, categorías ni ningún otro término: si falta una
 *    estructura fundamental (categoría, atributo, talle, línea, uso),
 *    informa ERROR y omite el producto afectado. Única excepción: el término
 *    "Naranja / Gris" en `pa_color-diseno`, que sí puede crear.
 *  - No crea variaciones (hijos Talle x Color). Se generarán después.
 *  - No modifica productos existentes de forma destructiva.
 *  - No elimina nada (ni productos ni términos).
 *
 * Idempotencia:
 *  - Cada producto se busca por SKU (`BAM-<TIPO>-BASE`) antes de crearlo.
 *    Si ya existe: EXISTS, sin duplicar ni modificar.
 *  - Puede ejecutarse N veces: segunda ejecución => created=0, errors=0.
 *
 * Uso previsto (NO ejecutar automáticamente):
 *   wp eval-file tools/catalog/seed-bamira-products.php
 *
 * Requiere entorno WordPress + WooCommerce cargado. Si se incluye sin ese
 * contexto, aborta de forma segura sin cambios.
 *
 * @package Bamira
 * @version 1.0
 */

// ---------------------------------------------------------------------------
// 0. Guardia de entorno: abortar si no hay WordPress/WooCommerce.
// ---------------------------------------------------------------------------

if ( ! defined( 'ABSPATH' ) ) {
	echo "ERROR: este script requiere entorno WordPress (ABSPATH no definido). Sin cambios.\n";
	return;
}

if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_product' ) || ! function_exists( 'wc_get_product_id_by_sku' ) ) {
	echo "ERROR: WooCommerce no disponible. Active WooCommerce e intente nuevamente. Sin cambios.\n";
	return;
}

if ( ! class_exists( 'WC_Product_Variable' ) || ! class_exists( 'WC_Product_Attribute' ) ) {
	echo "ERROR: clases de producto variable de WooCommerce no disponibles. Sin cambios.\n";
	return;
}

if ( ! function_exists( 'term_exists' ) || ! function_exists( 'wp_insert_term' ) || ! function_exists( 'get_term_by' ) ) {
	echo "ERROR: funciones de taxonomías de WordPress no disponibles. Sin cambios.\n";
	return;
}

if ( ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
	echo "ERROR: funciones de atributos de WooCommerce no disponibles. Sin cambios.\n";
	return;
}

// ---------------------------------------------------------------------------
// 1. Datos CATÁLOGO-2B + CATÁLOGO-3 (fuente única).
// ---------------------------------------------------------------------------

// Términos de Color / Diseño a asegurar. Los 6 primeros ya existen (EXISTS);
// "Naranja / Gris" se crea si falta. NO existe atributo separado "Estampado":
// lisos, combinaciones y estampados viven todos en pa_color-diseno.
$bamira_p_color_terms = array(
	'Blanco',
	'Negro',
	'Verde aceituna',
	'Azul',
	'Marrón',
	'Celeste',
	'Naranja / Gris',
);

$bamira_p_talles = array( 'XS', 'S', 'M', 'L' );

$bamira_p_products = array(
	array(
		'name'     => 'Malla deportiva base',
		'type'     => 'MDEP',
		'parent'   => 'Indumentaria deportiva',
		'category' => 'Malla deportiva',
		'uso'      => 'Entrenamiento',
		'linea'    => 'Mujer',
	),
	array(
		'name'     => 'Short deportivo base',
		'type'     => 'SHO',
		'parent'   => 'Indumentaria deportiva',
		'category' => 'Short deportivo',
		'uso'      => 'Entrenamiento',
		'linea'    => 'Unisex',
	),
	array(
		'name'     => 'Remera deportiva base',
		'type'     => 'REM',
		'parent'   => 'Indumentaria deportiva',
		'category' => 'Remera deportiva',
		'uso'      => 'Entrenamiento',
		'linea'    => 'Unisex',
	),
	array(
		'name'     => 'Musculosa deportiva base',
		'type'     => 'MUS',
		'parent'   => 'Indumentaria deportiva',
		'category' => 'Musculosa deportiva',
		'uso'      => 'Entrenamiento',
		'linea'    => 'Unisex',
	),
	array(
		'name'     => 'Top deportivo base',
		'type'     => 'TOP',
		'parent'   => 'Indumentaria deportiva',
		'category' => 'Top deportivo',
		'uso'      => 'Entrenamiento',
		'linea'    => 'Mujer',
	),
	array(
		'name'     => 'Calza corta base',
		'type'     => 'CALC',
		'parent'   => 'Indumentaria deportiva',
		'category' => 'Calza corta',
		'uso'      => 'Entrenamiento',
		'linea'    => 'Mujer',
	),
	array(
		'name'     => 'Calza larga base',
		'type'     => 'CALL',
		'parent'   => 'Indumentaria deportiva',
		'category' => 'Calza larga',
		'uso'      => 'Entrenamiento',
		'linea'    => 'Mujer',
	),
	array(
		'name'     => 'Malla de baño base',
		'type'     => 'MBA',
		'parent'   => 'Urbano',
		'category' => 'Malla de baño',
		'uso'      => 'Baño',
		'linea'    => 'Mujer',
	),
);

// ---------------------------------------------------------------------------
// 2. Utilidades de salida, conteo y resolución.
// ---------------------------------------------------------------------------

$bamira_p_created = 0;
$bamira_p_exists  = 0;
$bamira_p_errors  = 0;

if ( ! function_exists( 'bamira_products_log' ) ) {
	function bamira_products_log( $kind, $label, $status, $detail = '' ) {
		$line = sprintf( '[%s] %s ... %s', $kind, $label, $status );
		if ( '' !== $detail ) {
			$line .= ' (' . $detail . ')';
		}
		if ( class_exists( 'WP_CLI' ) && function_exists( 'WP_CLI' ) ) {
			WP_CLI::line( $line );
		} else {
			echo $line . "\n";
		}
	}
}

if ( ! function_exists( 'bamira_products_attr_id' ) ) {
	// Devuelve el attribute_id de un atributo global dado su nombre sin "pa_",
	// o 0 si el atributo no existe (no lo crea).
	function bamira_products_attr_id( $name_without_pa ) {
		$taxonomies = wc_get_attribute_taxonomies();
		if ( ! is_array( $taxonomies ) ) {
			return 0;
		}
		foreach ( $taxonomies as $tax ) {
			if ( isset( $tax->attribute_name ) && $tax->attribute_name === $name_without_pa ) {
				return (int) $tax->attribute_id;
			}
		}
		return 0;
	}
}

if ( ! function_exists( 'bamira_products_term_slug' ) ) {
	// Devuelve el slug de un término existente, o '' si no existe (no lo crea).
	function bamira_products_term_slug( $term_name, $taxonomy ) {
		$term = get_term_by( 'name', $term_name, $taxonomy );
		if ( $term && ! is_wp_error( $term ) && isset( $term->slug ) ) {
			return $term->slug;
		}
		return '';
	}
}

// ---------------------------------------------------------------------------
// 3. FASE A — Asegurar términos de pa_color-diseno (única creación permitida).
// ---------------------------------------------------------------------------

$color_tax = 'pa_color-diseno';

if ( ! taxonomy_exists( $color_tax ) || bamira_products_attr_id( 'color-diseno' ) <= 0 ) {
	bamira_products_log( 'TERM', $color_tax, 'ERROR', 'atributo global inexistente; no se crea nada' );
	++$bamira_p_errors;
} else {
	foreach ( $bamira_p_color_terms as $term_name ) {
		$label = $color_tax . ': ' . $term_name;
		$found = term_exists( $term_name, $color_tax );
		if ( is_array( $found ) && isset( $found['term_id'] ) ) {
			bamira_products_log( 'TERM', $label, 'EXISTS' );
			++$bamira_p_exists;
			continue;
		}
		$inserted = wp_insert_term( $term_name, $color_tax );
		if ( is_wp_error( $inserted ) ) {
			bamira_products_log( 'TERM', $label, 'ERROR', $inserted->get_error_message() );
			++$bamira_p_errors;
			continue;
		}
		bamira_products_log( 'TERM', $label, 'CREATED' );
		++$bamira_p_created;
	}
}

// ---------------------------------------------------------------------------
// 4. Verificación previa de estructuras fundamentales (no se crean aquí).
// ---------------------------------------------------------------------------

$bamira_p_prereq_ok = ( $bamira_p_errors === 0 );

$bamira_p_attr_ids = array(
	'pa_talle'        => bamira_products_attr_id( 'talle' ),
	'pa_color-diseno' => bamira_products_attr_id( 'color-diseno' ),
	'pa_linea'        => bamira_products_attr_id( 'linea' ),
	'pa_uso'          => bamira_products_attr_id( 'uso' ),
);

foreach ( $bamira_p_attr_ids as $tax => $attr_id ) {
	if ( $attr_id <= 0 || ! taxonomy_exists( $tax ) ) {
		bamira_products_log( 'PRODUCT', $tax, 'ERROR', 'atributo global faltante; los productos que lo requieran se omiten' );
		++$bamira_p_errors;
		$bamira_p_prereq_ok = false;
	}
}

// Resolver slugs de talles (deben existir; si falta alguno se informa por producto).
$bamira_p_talle_slugs = array();
foreach ( $bamira_p_talles as $talle ) {
	$slug = bamira_products_term_slug( $talle, 'pa_talle' );
	if ( '' === $slug ) {
		bamira_products_log( 'PRODUCT', 'pa_talle: ' . $talle, 'ERROR', 'término faltante; no se crea silenciosamente' );
		++$bamira_p_errors;
		$bamira_p_prereq_ok = false;
	} else {
		$bamira_p_talle_slugs[] = $slug;
	}
}

// Resolver slugs de colores (deben existir tras la Fase A).
$bamira_p_color_slugs = array();
foreach ( $bamira_p_color_terms as $color ) {
	$slug = bamira_products_term_slug( $color, $color_tax );
	if ( '' === $slug ) {
		bamira_products_log( 'PRODUCT', $color_tax . ': ' . $color, 'ERROR', 'término faltante tras Fase A' );
		++$bamira_p_errors;
		$bamira_p_prereq_ok = false;
	} else {
		$bamira_p_color_slugs[] = $slug;
	}
}

// ---------------------------------------------------------------------------
// 5. FASE B — Productos base (draft, variable, sin variaciones físicas).
// ---------------------------------------------------------------------------

foreach ( $bamira_p_products as $item ) {
	$sku   = 'BAM-' . $item['type'] . '-BASE';
	$label = $item['name'] . ' [' . $sku . ']';

	// 5.1. Idempotencia: buscar por SKU antes de crear.
	$existing_id = wc_get_product_id_by_sku( $sku );
	if ( $existing_id > 0 ) {
		$existing = wc_get_product( $existing_id );
		$detail   = 'id=' . (int) $existing_id;
		if ( $existing ) {
			$detail .= ' status=' . $existing->get_status() . ' type=' . $existing->get_type();
		}
		bamira_products_log( 'PRODUCT', $label, 'EXISTS', $detail );
		++$bamira_p_exists;
		continue;
	}

	// 5.2. Verificar categoría hija bajo su padre (no crear si falta).
	$parent_found = term_exists( $item['parent'], 'product_cat' );
	$parent_id    = ( is_array( $parent_found ) && isset( $parent_found['term_id'] ) ) ? (int) $parent_found['term_id'] : 0;
	if ( $parent_id <= 0 ) {
		bamira_products_log( 'PRODUCT', $label, 'ERROR', 'categoría padre faltante: ' . $item['parent'] );
		++$bamira_p_errors;
		continue;
	}
	$child_found = term_exists( $item['category'], 'product_cat', $parent_id );
	$child_id    = ( is_array( $child_found ) && isset( $child_found['term_id'] ) ) ? (int) $child_found['term_id'] : 0;
	if ( $child_id <= 0 ) {
		bamira_products_log( 'PRODUCT', $label, 'ERROR', 'subcategoría faltante: ' . $item['category'] );
		++$bamira_p_errors;
		continue;
	}

	// 5.3. Verificar atributos y términos de línea/uso (no crear si faltan).
	if ( ! $bamira_p_prereq_ok || count( $bamira_p_talle_slugs ) !== count( $bamira_p_talles ) || count( $bamira_p_color_slugs ) !== count( $bamira_p_color_terms ) ) {
		bamira_products_log( 'PRODUCT', $label, 'ERROR', 'prerrequisitos incompletos; omitido sin cambios' );
		++$bamira_p_errors;
		continue;
	}
	$linea_slug = bamira_products_term_slug( $item['linea'], 'pa_linea' );
	$uso_slug   = bamira_products_term_slug( $item['uso'], 'pa_uso' );
	if ( '' === $linea_slug || '' === $uso_slug ) {
		bamira_products_log( 'PRODUCT', $label, 'ERROR', 'término de Línea/Uso faltante' );
		++$bamira_p_errors;
		continue;
	}

	// 5.4. Crear producto variable en borrador, sin precio/stock/imágenes.
	$product = new WC_Product_Variable();
	$product->set_name( $item['name'] );
	$product->set_slug( sanitize_title( $item['name'] ) );
	$product->set_status( 'draft' );
	$product->set_sku( $sku );
	$product->set_category_ids( array( $child_id ) );

	$wc_attributes = array();

	$variation_map = array(
		'pa_talle'        => $bamira_p_talle_slugs,
		'pa_color-diseno' => $bamira_p_color_slugs,
	);
	foreach ( $variation_map as $tax => $slugs ) {
		$attribute = new WC_Product_Attribute();
		$attribute->set_id( $bamira_p_attr_ids[ $tax ] );
		$attribute->set_name( $tax );
		$attribute->set_options( $slugs );
		$attribute->set_visible( true );
		$attribute->set_variation( true );
		$wc_attributes[] = $attribute;
	}

	$novariation_map = array(
		'pa_linea' => array( $linea_slug ),
		'pa_uso'   => array( $uso_slug ),
	);
	foreach ( $novariation_map as $tax => $slugs ) {
		$attribute = new WC_Product_Attribute();
		$attribute->set_id( $bamira_p_attr_ids[ $tax ] );
		$attribute->set_name( $tax );
		$attribute->set_options( $slugs );
		$attribute->set_visible( true );
		$attribute->set_variation( false );
		$wc_attributes[] = $attribute;
	}

	$product->set_attributes( $wc_attributes );

	$new_id = $product->save();
	if ( ! $new_id || is_wp_error( $new_id ) ) {
		$message = is_wp_error( $new_id ) ? $new_id->get_error_message() : 'save() sin ID';
		bamira_products_log( 'PRODUCT', $label, 'ERROR', $message );
		++$bamira_p_errors;
		continue;
	}

	bamira_products_log( 'PRODUCT', $label, 'CREATED', 'id=' . (int) $new_id . ' status=draft type=variable' );
	++$bamira_p_created;
}

// ---------------------------------------------------------------------------
// 6. Resumen final.
// ---------------------------------------------------------------------------

$summary = sprintf( 'DONE. created=%d exists=%d errors=%d', $bamira_p_created, $bamira_p_exists, $bamira_p_errors );
if ( class_exists( 'WP_CLI' ) && function_exists( 'WP_CLI' ) ) {
	WP_CLI::line( $summary );
} else {
	echo $summary . "\n";
}
