<?php
/**
 * BAMIRA — Seed de variaciones iniciales (CATÁLOGO-4: Talle × Color).
 *
 * Alcance:
 *  - Asegura en `pa_color-diseno` los términos Gris y Verde (idempotente).
 *    Blanco y Negro deben existir (los crea CATÁLOGO-2); no toca los demás
 *    términos globales (Verde aceituna, Azul, Marrón, Celeste, Naranja / Gris).
 *  - Verifica/ajusta en cada producto padre sus atributos de variación
 *    (pa_talle + pa_color-diseno con los valores CATÁLOGO-4) preservando
 *    pa_linea / pa_uso como no-variables.
 *  - Crea las variaciones Talle × Color (16 por producto, 80 en total) como
 *    WC_Product_Variation SIN precio, SIN stock, SIN imágenes.
 *
 * Lo que NO hace (por diseño):
 *  - No crea talles (los crea CATÁLOGO-2): talle faltante => ERROR.
 *  - No crea productos padre (los crea CATÁLOGO-3): padre faltante => ERROR.
 *  - No publica nada: los padres permanecen en draft.
 *  - No elimina nada (ni productos, ni variaciones, ni términos).
 *  - No modifica precios, stock, ni el status de productos existentes.
 *
 * Idempotencia:
 *  - Cada variación se busca por SKU antes de crearla:
 *    existe en el padre esperado => EXISTS; existe en otro padre => ERROR;
 *    no existe => CREATED.
 *  - Puede ejecutarse N veces: segunda ejecución => todo EXISTS, errors=0.
 *
 * Uso previsto (NO ejecutar automáticamente):
 *   wp eval-file tools/catalog/seed-bamira-variations.php
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

if ( ! class_exists( 'WC_Product_Variable' ) || ! class_exists( 'WC_Product_Variation' ) || ! class_exists( 'WC_Product_Attribute' ) ) {
	echo "ERROR: clases de producto de WooCommerce no disponibles. Sin cambios.\n";
	return;
}

if ( ! function_exists( 'term_exists' ) || ! function_exists( 'wp_insert_term' ) || ! function_exists( 'get_term_by' ) || ! function_exists( 'taxonomy_exists' ) ) {
	echo "ERROR: funciones de taxonomías de WordPress no disponibles. Sin cambios.\n";
	return;
}

if ( ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
	echo "ERROR: funciones de atributos de WooCommerce no disponibles. Sin cambios.\n";
	return;
}

// ---------------------------------------------------------------------------
// 1. Datos CATÁLOGO-4 (fuente única y centralizada).
// ---------------------------------------------------------------------------

// Talles: deben existir (CATÁLOGO-2). No se crean aquí.
$bamira_v_talles = array( 'XS', 'S', 'M', 'L' );

// Colores CATÁLOGO-4: nombre visible => sigla SKU. Blanco y Negro deben
// existir; Gris y Verde se aseguran idempotentemente. Otros términos
// globales (Verde aceituna, Azul, Marrón, Celeste, Naranja / Gris) coexisten
// y no se tocan; simplemente no se usan en esta matriz inicial.
$bamira_v_colores = array(
	'Blanco' => 'BLA',
	'Negro'  => 'NEG',
	'Gris'   => 'GRI',
	'Verde'  => 'VER',
);

$bamira_v_asegurables = array( 'Gris', 'Verde' );

// Productos incluidos en CATÁLOGO-4 (los 3 restantes de CATÁLOGO-3 quedan fuera).
// La matriz por producto es configurable: para habilitar/deshabilitar
// combinaciones concretas basta editar 'talles'/'colores' de cada entrada,
// sin reescribir la lógica general.
$bamira_v_products = array(
	array( 'type' => 'TOP',  'sku' => 'BAM-TOP-BASE',  'talles' => array( 'XS', 'S', 'M', 'L' ), 'colores' => array( 'Blanco', 'Negro', 'Gris', 'Verde' ) ),
	array( 'type' => 'SHO',  'sku' => 'BAM-SHO-BASE',  'talles' => array( 'XS', 'S', 'M', 'L' ), 'colores' => array( 'Blanco', 'Negro', 'Gris', 'Verde' ) ),
	array( 'type' => 'CALL', 'sku' => 'BAM-CALL-BASE', 'talles' => array( 'XS', 'S', 'M', 'L' ), 'colores' => array( 'Blanco', 'Negro', 'Gris', 'Verde' ) ),
	array( 'type' => 'CALC', 'sku' => 'BAM-CALC-BASE', 'talles' => array( 'XS', 'S', 'M', 'L' ), 'colores' => array( 'Blanco', 'Negro', 'Gris', 'Verde' ) ),
	array( 'type' => 'REM',  'sku' => 'BAM-REM-BASE',  'talles' => array( 'XS', 'S', 'M', 'L' ), 'colores' => array( 'Blanco', 'Negro', 'Gris', 'Verde' ) ),
);

// ---------------------------------------------------------------------------
// 2. Utilidades de salida, conteo y resolución.
// ---------------------------------------------------------------------------

$bamira_v_terms_created = 0;
$bamira_v_products_checked = 0;
$bamira_v_created = 0;
$bamira_v_exists  = 0;
$bamira_v_errors  = 0;

if ( ! function_exists( 'bamira_variations_log' ) ) {
	function bamira_variations_log( $kind, $label, $status, $detail = '' ) {
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

if ( ! function_exists( 'bamira_variations_attr_id' ) ) {
	// attribute_id de un atributo global (nombre sin "pa_"), o 0 si no existe.
	function bamira_variations_attr_id( $name_without_pa ) {
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

if ( ! function_exists( 'bamira_variations_term_slug' ) ) {
	// Slug de un término existente, o '' si no existe (no lo crea).
	function bamira_variations_term_slug( $term_name, $taxonomy ) {
		$term = get_term_by( 'name', $term_name, $taxonomy );
		if ( $term && ! is_wp_error( $term ) && isset( $term->slug ) ) {
			return $term->slug;
		}
		return '';
	}
}

if ( ! function_exists( 'bamira_variations_same_options' ) ) {
	// Compara opciones de atributo sin importar el orden.
	function bamira_variations_same_options( $a, $b ) {
		if ( ! is_array( $a ) || ! is_array( $b ) || count( $a ) !== count( $b ) ) {
			return false;
		}
		$sa = $a;
		$sb = $b;
		sort( $sa );
		sort( $sb );
		return $sa === $sb;
	}
}

// ---------------------------------------------------------------------------
// 3. FASE C — Asegurar Gris y Verde en pa_color-diseno (única creación).
// ---------------------------------------------------------------------------

$color_tax = 'pa_color-diseno';
$bamira_v_color_ok = true;

if ( ! taxonomy_exists( $color_tax ) || bamira_variations_attr_id( 'color-diseno' ) <= 0 ) {
	bamira_variations_log( 'TERM', $color_tax, 'ERROR', 'atributo global inexistente; no se crea nada' );
	++$bamira_v_errors;
	$bamira_v_color_ok = false;
} else {
	foreach ( $bamira_v_colores as $color_name => $color_code ) {
		$must_create = in_array( $color_name, $bamira_v_asegurables, true );
		$found = term_exists( $color_name, $color_tax );
		if ( is_array( $found ) && isset( $found['term_id'] ) ) {
			bamira_variations_log( 'TERM', $color_name, 'EXISTS' );
			continue;
		}
		if ( ! $must_create ) {
			// Blanco/Negro los crea CATÁLOGO-2: no crearlos silenciosamente.
			bamira_variations_log( 'TERM', $color_name, 'ERROR', 'término base faltante; no se crea silenciosamente' );
			++$bamira_v_errors;
			$bamira_v_color_ok = false;
			continue;
		}
		$inserted = wp_insert_term( $color_name, $color_tax );
		if ( is_wp_error( $inserted ) ) {
			bamira_variations_log( 'TERM', $color_name, 'ERROR', $inserted->get_error_message() );
			++$bamira_v_errors;
			$bamira_v_color_ok = false;
			continue;
		}
		bamira_variations_log( 'TERM', $color_name, 'CREATED' );
		++$bamira_v_terms_created;
	}
}

// Resolver slugs de talles y colores (verificación, sin crear).
$bamira_v_talle_slugs = array();
foreach ( $bamira_v_talles as $talle ) {
	$slug = bamira_variations_term_slug( $talle, 'pa_talle' );
	if ( '' === $slug ) {
		bamira_variations_log( 'TERM', 'pa_talle: ' . $talle, 'ERROR', 'término faltante (CATÁLOGO-2); no se crea' );
		++$bamira_v_errors;
	} else {
		$bamira_v_talle_slugs[ $talle ] = $slug;
	}
}

$bamira_v_color_slugs = array();
foreach ( $bamira_v_colores as $color_name => $color_code ) {
	$slug = bamira_variations_term_slug( $color_name, $color_tax );
	if ( '' === $slug ) {
		bamira_variations_log( 'TERM', $color_tax . ': ' . $color_name, 'ERROR', 'término no disponible' );
		++$bamira_v_errors;
		$bamira_v_color_ok = false;
	} else {
		$bamira_v_color_slugs[ $color_name ] = $slug;
	}
}

$bamira_v_attr_ids = array(
	'pa_talle'        => bamira_variations_attr_id( 'talle' ),
	'pa_color-diseno' => bamira_variations_attr_id( 'color-diseno' ),
	'pa_linea'        => bamira_variations_attr_id( 'linea' ),
	'pa_uso'          => bamira_variations_attr_id( 'uso' ),
);
foreach ( $bamira_v_attr_ids as $tax => $attr_id ) {
	if ( $attr_id <= 0 || ! taxonomy_exists( $tax ) ) {
		bamira_variations_log( 'PRODUCT', $tax, 'ERROR', 'atributo global faltante' );
		++$bamira_v_errors;
	}
}

// ---------------------------------------------------------------------------
// 4. FASES D–G — Por producto: verificar padre, sincronizar atributos, crear
//    variaciones Talle × Color según su matriz configurable.
// ---------------------------------------------------------------------------

foreach ( $bamira_v_products as $item ) {
	$parent_sku = $item['sku'];
	++$bamira_v_products_checked;

	// 4.1. El padre debe existir (lo crea CATÁLOGO-3); si falta, ERROR.
	$parent_id = wc_get_product_id_by_sku( $parent_sku );
	if ( $parent_id <= 0 ) {
		bamira_variations_log( 'PRODUCT', $parent_sku, 'ERROR', 'producto padre inexistente; lo crea CATÁLOGO-3' );
		++$bamira_v_errors;
		continue;
	}
	$parent = wc_get_product( $parent_id );
	if ( ! $parent || ! $parent->is_type( 'variable' ) ) {
		bamira_variations_log( 'PRODUCT', $parent_sku, 'ERROR', 'el padre no es un producto variable' );
		++$bamira_v_errors;
		continue;
	}

	// 4.2. FASE G — Atributos del padre: pa_talle + pa_color-diseno de la
	// matriz del producto (variation=true, visible=true); pa_linea / pa_uso
	// se preservan tal cual con variation=false. No se tocan términos
	// globales: solo la disponibilidad para variaciones de este padre.
	$expected_talle = array();
	foreach ( $item['talles'] as $talle ) {
		if ( isset( $bamira_v_talle_slugs[ $talle ] ) ) {
			$expected_talle[] = $bamira_v_talle_slugs[ $talle ];
		}
	}
	$expected_color = array();
	foreach ( $item['colores'] as $color_name ) {
		if ( isset( $bamira_v_color_slugs[ $color_name ] ) ) {
			$expected_color[] = $bamira_v_color_slugs[ $color_name ];
		}
	}
	if ( count( $expected_talle ) !== count( $item['talles'] ) || count( $expected_color ) !== count( $item['colores'] ) ) {
		bamira_variations_log( 'PRODUCT', $parent_sku, 'ERROR', 'términos de la matriz no disponibles; omitido' );
		++$bamira_v_errors;
		continue;
	}

	$current_attrs = $parent->get_attributes();
	$needs_sync    = false;

	foreach ( array( 'pa_talle' => $expected_talle, 'pa_color-diseno' => $expected_color ) as $tax => $expected ) {
		if ( ! isset( $current_attrs[ $tax ] ) ) {
			$needs_sync = true;
			break;
		}
		$current = $current_attrs[ $tax ];
		if ( ! $current->get_variation() || ! bamira_variations_same_options( $current->get_options(), $expected ) ) {
			$needs_sync = true;
			break;
		}
	}
	foreach ( array( 'pa_linea', 'pa_uso' ) as $tax ) {
		if ( ! isset( $current_attrs[ $tax ] ) ) {
			bamira_variations_log( 'PRODUCT', $parent_sku, 'ERROR', 'atributo ' . $tax . ' ausente en el padre (lo asigna CATÁLOGO-3)' );
			++$bamira_v_errors;
			continue 2; // Omitir este producto sin cambios.
		}
		if ( $current_attrs[ $tax ]->get_variation() ) {
			$needs_sync = true;
		}
	}

	if ( $needs_sync ) {
		$new_attributes = array();
		foreach ( array( 'pa_talle' => $expected_talle, 'pa_color-diseno' => $expected_color ) as $tax => $expected ) {
			$attribute = new WC_Product_Attribute();
			$attribute->set_id( $bamira_v_attr_ids[ $tax ] );
			$attribute->set_name( $tax );
			$attribute->set_options( $expected );
			$attribute->set_visible( true );
			$attribute->set_variation( true );
			$new_attributes[ $tax ] = $attribute;
		}
		foreach ( array( 'pa_linea', 'pa_uso' ) as $tax ) {
			$kept = clone $current_attrs[ $tax ];
			$kept->set_variation( false );
			$new_attributes[ $tax ] = $kept;
		}
		// Solo atributos: no se toca nombre, SKU, status (draft), ni categorías.
		$parent->set_attributes( $new_attributes );
		$parent->save();
		bamira_variations_log( 'PRODUCT', $parent_sku, 'EXISTS', 'id=' . (int) $parent_id . ' status=' . $parent->get_status() . ' attrs synced' );
	} else {
		bamira_variations_log( 'PRODUCT', $parent_sku, 'EXISTS', 'id=' . (int) $parent_id . ' status=' . $parent->get_status() );
	}

	// 4.3. FASES D–F — Variaciones según la matriz del producto.
	foreach ( $item['colores'] as $color_name ) {
		$color_code = $bamira_v_colores[ $color_name ];
		foreach ( $item['talles'] as $talle ) {
			$sku   = sprintf( 'BAM-%s-BASE-%s-%s', $item['type'], $color_code, $talle );
			$found_id = wc_get_product_id_by_sku( $sku );

			if ( $found_id > 0 ) {
				$found = wc_get_product( $found_id );
				$found_parent = ( $found && method_exists( $found, 'get_parent_id' ) ) ? (int) $found->get_parent_id() : 0;
				if ( $found_parent === (int) $parent_id ) {
					bamira_variations_log( 'VARIATION', $sku, 'EXISTS' );
					++$bamira_v_exists;
				} else {
					bamira_variations_log( 'VARIATION', $sku, 'ERROR', 'SKU en otro producto (id=' . (int) $found_id . ' padre=' . $found_parent . ')' );
					++$bamira_v_errors;
				}
				continue;
			}

			$variation = new WC_Product_Variation();
			$variation->set_parent_id( (int) $parent_id );
			$variation->set_sku( $sku );
			$variation->set_attributes( array(
				'pa_talle'        => $bamira_v_talle_slugs[ $talle ],
				'pa_color-diseno' => $bamira_v_color_slugs[ $color_name ],
			) );
			// Sin precio, sin stock, sin imagen, sin backorders explícitos:
			// se dejan los valores por defecto. El padre sigue en draft,
			// por lo que nada se publica ni es visible en la tienda.
			$new_id = $variation->save();
			if ( ! $new_id || is_wp_error( $new_id ) ) {
				$message = is_wp_error( $new_id ) ? $new_id->get_error_message() : 'save() sin ID';
				bamira_variations_log( 'VARIATION', $sku, 'ERROR', $message );
				++$bamira_v_errors;
				continue;
			}
			bamira_variations_log( 'VARIATION', $sku, 'CREATED', 'id=' . (int) $new_id );
			++$bamira_v_created;
		}
	}
}

// ---------------------------------------------------------------------------
// 5. Resumen final.
// ---------------------------------------------------------------------------

$summary = sprintf(
	"DONE.\nterms_created=%d\nproducts_checked=%d\nvariations_created=%d\nvariations_exists=%d\nerrors=%d",
	$bamira_v_terms_created,
	$bamira_v_products_checked,
	$bamira_v_created,
	$bamira_v_exists,
	$bamira_v_errors
);
if ( class_exists( 'WP_CLI' ) && function_exists( 'WP_CLI' ) ) {
	WP_CLI::line( $summary );
} else {
	echo $summary . "\n";
}
