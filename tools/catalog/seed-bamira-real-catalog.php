<?php
/**
 * BAMIRA — Seed de Catálogo Real · Etapa 1.
 *
 * Alcance:
 *  - Asegura términos de talle ('1', '2', '3') en `pa_talle` sin alterar términos previos.
 *  - Asegura términos de color ('Negro', 'Bordo Frambuesa', 'Azul', 'Petróleo', 'Chocolate') en `pa_color-diseno`.
 *  - Asegura categorías padre ('Indumentaria deportiva', 'Urbano') y subcategorías requeridas.
 *  - Crea/actualiza 8 productos padre base en estado borrador (`draft`), tipo `variable`,
 *    configurados con atributos `pa_talle` y `pa_color-diseno` para variaciones.
 *  - Crea/actualiza 66 variaciones hijas (`WC_Product_Variation`) con stock individual asignado,
 *    gestión de inventario activa (`manage_stock = yes`), estado `instock`,
 *    SIN precios (precios vacíos) y SIN imágenes asignadas.
 *  - Totaliza y valida exactamente 427 unidades físicas de stock.
 *
 * Idempotencia:
 *  - Padres identificados unívocamente por SKU: `BAM-<MODELO>`.
 *  - Variaciones identificadas unívocamente por SKU: `BAM-<MODELO>-<COLOR>-T<TALLE>`.
 *  - Si un ítem ya existe, reporta EXISTS y actualiza inventario/atributos sin duplicar registros.
 *
 * Uso previsto (no ejecutar automáticamente; solo cuando se autorice):
 *   wp eval-file tools/catalog/seed-bamira-real-catalog.php
 *
 * @package Bamira
 * @version 1.1
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
// 1. Datos normativos del Catálogo Real — Etapa 1.
// ---------------------------------------------------------------------------

$bamira_real_categories = array(
	array(
		'parent'   => 'Indumentaria deportiva',
		'children' => array( 'Top deportivo', 'Calza deportiva', 'Short deportivo' ),
	),
	array(
		'parent'   => 'Urbano',
		'children' => array( 'Malla de baño' ),
	),
);

$bamira_real_talles = array( '1', '2', '3' );

$bamira_real_colores = array(
	'Negro'           => 'NEGRO',
	'Bordo Frambuesa' => 'BORDO-FRAMBUESA',
	'Azul'            => 'AZUL',
	'Petróleo'        => 'PETROLEO',
	'Chocolate'       => 'CHOCOLATE',
);

$bamira_real_catalog = array(
	array(
		'parent_sku' => 'BAM-TOP-GIGI',
		'name'       => 'Top Gigi',
		'parent_cat' => 'Urbano',
		'category'   => 'Malla de baño',
		'linea'      => 'Mujer',
		'uso'        => 'Baño',
		'talles'     => array( '1', '2', '3' ),
		'colores'    => array( 'Negro', 'Bordo Frambuesa', 'Azul', 'Petróleo' ),
		'variations' => array(
			array( 'color' => 'Negro',           'talle' => '1', 'stock' => 6 ),
			array( 'color' => 'Negro',           'talle' => '2', 'stock' => 8 ),
			array( 'color' => 'Negro',           'talle' => '3', 'stock' => 4 ),
			array( 'color' => 'Bordo Frambuesa', 'talle' => '1', 'stock' => 6 ),
			array( 'color' => 'Bordo Frambuesa', 'talle' => '2', 'stock' => 8 ),
			array( 'color' => 'Bordo Frambuesa', 'talle' => '3', 'stock' => 4 ),
			array( 'color' => 'Azul',            'talle' => '1', 'stock' => 6 ),
			array( 'color' => 'Azul',            'talle' => '2', 'stock' => 8 ),
			array( 'color' => 'Azul',            'talle' => '3', 'stock' => 4 ),
			array( 'color' => 'Petróleo',        'talle' => '1', 'stock' => 6 ),
			array( 'color' => 'Petróleo',        'talle' => '2', 'stock' => 8 ),
			array( 'color' => 'Petróleo',        'talle' => '3', 'stock' => 4 ),
		),
	),
	array(
		'parent_sku' => 'BAM-TOP-MILE',
		'name'       => 'Top Mile',
		'parent_cat' => 'Urbano',
		'category'   => 'Malla de baño',
		'linea'      => 'Mujer',
		'uso'        => 'Baño',
		'talles'     => array( '1', '2', '3' ),
		'colores'    => array( 'Negro', 'Bordo Frambuesa', 'Azul', 'Petróleo' ),
		'variations' => array(
			array( 'color' => 'Negro',           'talle' => '1', 'stock' => 6 ),
			array( 'color' => 'Negro',           'talle' => '2', 'stock' => 8 ),
			array( 'color' => 'Negro',           'talle' => '3', 'stock' => 4 ),
			array( 'color' => 'Bordo Frambuesa', 'talle' => '1', 'stock' => 6 ),
			array( 'color' => 'Bordo Frambuesa', 'talle' => '2', 'stock' => 8 ),
			array( 'color' => 'Bordo Frambuesa', 'talle' => '3', 'stock' => 4 ),
			array( 'color' => 'Azul',            'talle' => '1', 'stock' => 6 ),
			array( 'color' => 'Azul',            'talle' => '2', 'stock' => 8 ),
			array( 'color' => 'Azul',            'talle' => '3', 'stock' => 4 ),
			array( 'color' => 'Petróleo',        'talle' => '1', 'stock' => 6 ),
			array( 'color' => 'Petróleo',        'talle' => '2', 'stock' => 8 ),
			array( 'color' => 'Petróleo',        'talle' => '3', 'stock' => 4 ),
		),
	),
	array(
		'parent_sku' => 'BAM-VEDETINA',
		'name'       => 'Vedetina',
		'parent_cat' => 'Urbano',
		'category'   => 'Malla de baño',
		'linea'      => 'Mujer',
		'uso'        => 'Baño',
		'talles'     => array( '1', '2', '3' ),
		'colores'    => array( 'Negro', 'Bordo Frambuesa', 'Azul', 'Petróleo' ),
		'variations' => array(
			array( 'color' => 'Negro',           'talle' => '1', 'stock' => 8 ),
			array( 'color' => 'Negro',           'talle' => '2', 'stock' => 10 ),
			array( 'color' => 'Negro',           'talle' => '3', 'stock' => 6 ),
			array( 'color' => 'Bordo Frambuesa', 'talle' => '1', 'stock' => 6 ),
			array( 'color' => 'Bordo Frambuesa', 'talle' => '2', 'stock' => 8 ),
			array( 'color' => 'Bordo Frambuesa', 'talle' => '3', 'stock' => 4 ),
			array( 'color' => 'Azul',            'talle' => '1', 'stock' => 6 ),
			array( 'color' => 'Azul',            'talle' => '2', 'stock' => 8 ),
			array( 'color' => 'Azul',            'talle' => '3', 'stock' => 4 ),
			array( 'color' => 'Petróleo',        'talle' => '1', 'stock' => 6 ),
			array( 'color' => 'Petróleo',        'talle' => '2', 'stock' => 8 ),
			array( 'color' => 'Petróleo',        'talle' => '3', 'stock' => 4 ),
		),
	),
	array(
		'parent_sku' => 'BAM-COLALESS',
		'name'       => 'Colaless',
		'parent_cat' => 'Urbano',
		'category'   => 'Malla de baño',
		'linea'      => 'Mujer',
		'uso'        => 'Baño',
		'talles'     => array( '1', '2', '3' ),
		'colores'    => array( 'Negro', 'Bordo Frambuesa', 'Azul', 'Petróleo' ),
		'variations' => array(
			array( 'color' => 'Negro',           'talle' => '1', 'stock' => 10 ),
			array( 'color' => 'Negro',           'talle' => '2', 'stock' => 12 ),
			array( 'color' => 'Negro',           'talle' => '3', 'stock' => 6 ),
			array( 'color' => 'Bordo Frambuesa', 'talle' => '1', 'stock' => 8 ),
			array( 'color' => 'Bordo Frambuesa', 'talle' => '2', 'stock' => 10 ),
			array( 'color' => 'Bordo Frambuesa', 'talle' => '3', 'stock' => 6 ),
			array( 'color' => 'Azul',            'talle' => '1', 'stock' => 8 ),
			array( 'color' => 'Azul',            'talle' => '2', 'stock' => 10 ),
			array( 'color' => 'Azul',            'talle' => '3', 'stock' => 6 ),
			array( 'color' => 'Petróleo',        'talle' => '1', 'stock' => 8 ),
			array( 'color' => 'Petróleo',        'talle' => '2', 'stock' => 10 ),
			array( 'color' => 'Petróleo',        'talle' => '3', 'stock' => 6 ),
		),
	),
	array(
		'parent_sku' => 'BAM-TOP-DEP',
		'name'       => 'Top deportivo',
		'parent_cat' => 'Indumentaria deportiva',
		'category'   => 'Top deportivo',
		'linea'      => 'Mujer',
		'uso'        => 'Entrenamiento',
		'talles'     => array( '1', '2', '3' ),
		'colores'    => array( 'Negro', 'Chocolate' ),
		'variations' => array(
			array( 'color' => 'Negro',     'talle' => '1', 'stock' => 6 ),
			array( 'color' => 'Negro',     'talle' => '2', 'stock' => 8 ),
			array( 'color' => 'Negro',     'talle' => '3', 'stock' => 4 ),
			array( 'color' => 'Chocolate', 'talle' => '1', 'stock' => 4 ),
			array( 'color' => 'Chocolate', 'talle' => '2', 'stock' => 6 ),
			array( 'color' => 'Chocolate', 'talle' => '3', 'stock' => 4 ),
		),
	),
	array(
		'parent_sku' => 'BAM-CALZA-DEP',
		'name'       => 'Calza deportiva',
		'parent_cat' => 'Indumentaria deportiva',
		'category'   => 'Calza deportiva',
		'linea'      => 'Mujer',
		'uso'        => 'Entrenamiento',
		'talles'     => array( '1', '2', '3' ),
		'colores'    => array( 'Negro', 'Chocolate' ),
		'variations' => array(
			array( 'color' => 'Negro',     'talle' => '1', 'stock' => 6 ),
			array( 'color' => 'Negro',     'talle' => '2', 'stock' => 6 ),
			array( 'color' => 'Negro',     'talle' => '3', 'stock' => 4 ),
			array( 'color' => 'Chocolate', 'talle' => '1', 'stock' => 4 ),
			array( 'color' => 'Chocolate', 'talle' => '2', 'stock' => 5 ),
			array( 'color' => 'Chocolate', 'talle' => '3', 'stock' => 4 ),
		),
	),
	array(
		'parent_sku' => 'BAM-SHORT-BREN',
		'name'       => 'Short Bren',
		'parent_cat' => 'Indumentaria deportiva',
		'category'   => 'Short deportivo',
		'linea'      => 'Mujer',
		'uso'        => 'Entrenamiento',
		'talles'     => array( '1', '2', '3' ),
		'colores'    => array( 'Negro' ),
		'variations' => array(
			array( 'color' => 'Negro', 'talle' => '1', 'stock' => 8 ),
			array( 'color' => 'Negro', 'talle' => '2', 'stock' => 8 ),
			array( 'color' => 'Negro', 'talle' => '3', 'stock' => 6 ),
		),
	),
	array(
		'parent_sku' => 'BAM-SHORT-KAI',
		'name'       => 'Short Kai',
		'parent_cat' => 'Indumentaria deportiva',
		'category'   => 'Short deportivo',
		'linea'      => 'Mujer',
		'uso'        => 'Entrenamiento',
		'talles'     => array( '1', '2', '3' ),
		'colores'    => array( 'Negro' ),
		'variations' => array(
			array( 'color' => 'Negro', 'talle' => '1', 'stock' => 8 ),
			array( 'color' => 'Negro', 'talle' => '2', 'stock' => 8 ),
			array( 'color' => 'Negro', 'talle' => '3', 'stock' => 6 ),
		),
	),
);

// ---------------------------------------------------------------------------
// 2. Utilidades de logging y resolución.
// ---------------------------------------------------------------------------

$bamira_log_counts = array(
	'terms_created'      => 0,
	'terms_exists'       => 0,
	'categories_created' => 0,
	'categories_exists'  => 0,
	'products_created'   => 0,
	'products_exists'    => 0,
	'variations_created' => 0,
	'variations_exists'  => 0,
	'total_units'        => 0,
	'errors'             => 0,
);

if ( ! function_exists( 'bamira_real_log' ) ) {
	function bamira_real_log( $kind, $label, $status, $detail = '' ) {
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

function bamira_real_attr_id( $name_without_pa ) {
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

function bamira_real_term_slug( $term_name, $taxonomy ) {
	$term = get_term_by( 'name', $term_name, $taxonomy );
	if ( $term && ! is_wp_error( $term ) && isset( $term->slug ) ) {
		return $term->slug;
	}
	return '';
}

// ---------------------------------------------------------------------------
// 3. FASE 1 — Asegurar Categorías y Atributos/Términos globales.
// ---------------------------------------------------------------------------

// 3.1 Categorías
$category_map = array();
foreach ( $bamira_real_categories as $cat_group ) {
	$parent_name = $cat_group['parent'];
	$parent_id   = 0;
	$found = term_exists( $parent_name, 'product_cat' );
	if ( is_array( $found ) && isset( $found['term_id'] ) ) {
		$parent_id = (int) $found['term_id'];
		bamira_real_log( 'CATEGORY', $parent_name, 'EXISTS', 'id=' . $parent_id );
		++$bamira_log_counts['categories_exists'];
	} else {
		$ins = wp_insert_term( $parent_name, 'product_cat' );
		if ( is_wp_error( $ins ) ) {
			bamira_real_log( 'CATEGORY', $parent_name, 'ERROR', $ins->get_error_message() );
			++$bamira_log_counts['errors'];
			continue;
		}
		$parent_id = (int) $ins['term_id'];
		bamira_real_log( 'CATEGORY', $parent_name, 'CREATED', 'id=' . $parent_id );
		++$bamira_log_counts['categories_created'];
	}

	foreach ( $cat_group['children'] as $child_name ) {
		$child_id = 0;
		$found_child = term_exists( $child_name, 'product_cat', $parent_id );
		if ( is_array( $found_child ) && isset( $found_child['term_id'] ) ) {
			$child_id = (int) $found_child['term_id'];
			bamira_real_log( 'CATEGORY', $child_name . ' (' . $parent_name . ')', 'EXISTS', 'id=' . $child_id );
			++$bamira_log_counts['categories_exists'];
		} else {
			$ins_child = wp_insert_term( $child_name, 'product_cat', array( 'parent' => $parent_id ) );
			if ( is_wp_error( $ins_child ) ) {
				bamira_real_log( 'CATEGORY', $child_name . ' (' . $parent_name . ')', 'ERROR', $ins_child->get_error_message() );
				++$bamira_log_counts['errors'];
				continue;
			}
			$child_id = (int) $ins_child['term_id'];
			bamira_real_log( 'CATEGORY', $child_name . ' (' . $parent_name . ')', 'CREATED', 'id=' . $child_id );
			++$bamira_log_counts['categories_created'];
		}
		$category_map[ $parent_name . '|' . $child_name ] = $child_id;
	}
}

// 3.2 Términos de Talle (pa_talle)
$talle_tax = 'pa_talle';
if ( ! taxonomy_exists( $talle_tax ) ) {
	register_taxonomy( $talle_tax, array( 'product' ), array( 'hierarchical' => false ) );
}
$talle_slugs = array();
foreach ( $bamira_real_talles as $talle ) {
	$found = term_exists( $talle, $talle_tax );
	if ( is_array( $found ) && isset( $found['term_id'] ) ) {
		bamira_real_log( 'TERM', $talle_tax . ': ' . $talle, 'EXISTS' );
		++$bamira_log_counts['terms_exists'];
	} else {
		$ins = wp_insert_term( $talle, $talle_tax );
		if ( is_wp_error( $ins ) ) {
			bamira_real_log( 'TERM', $talle_tax . ': ' . $talle, 'ERROR', $ins->get_error_message() );
			++$bamira_log_counts['errors'];
		} else {
			bamira_real_log( 'TERM', $talle_tax . ': ' . $talle, 'CREATED' );
			++$bamira_log_counts['terms_created'];
		}
	}
	$talle_slugs[ $talle ] = bamira_real_term_slug( $talle, $talle_tax );
}

// 3.3 Términos de Color / Diseño (pa_color-diseno)
$color_tax = 'pa_color-diseno';
if ( ! taxonomy_exists( $color_tax ) ) {
	register_taxonomy( $color_tax, array( 'product' ), array( 'hierarchical' => false ) );
}
$color_slugs = array();
foreach ( $bamira_real_colores as $color_name => $color_code ) {
	$found = term_exists( $color_name, $color_tax );
	if ( is_array( $found ) && isset( $found['term_id'] ) ) {
		bamira_real_log( 'TERM', $color_tax . ': ' . $color_name, 'EXISTS' );
		++$bamira_log_counts['terms_exists'];
	} else {
		$ins = wp_insert_term( $color_name, $color_tax );
		if ( is_wp_error( $ins ) ) {
			bamira_real_log( 'TERM', $color_tax . ': ' . $color_name, 'ERROR', $ins->get_error_message() );
			++$bamira_log_counts['errors'];
		} else {
			bamira_real_log( 'TERM', $color_tax . ': ' . $color_name, 'CREATED' );
			++$bamira_log_counts['terms_created'];
		}
	}
	$color_slugs[ $color_name ] = bamira_real_term_slug( $color_name, $color_tax );
}

$attr_ids = array(
	'pa_talle'        => bamira_real_attr_id( 'talle' ),
	'pa_color-diseno' => bamira_real_attr_id( 'color-diseno' ),
	'pa_linea'        => bamira_real_attr_id( 'linea' ),
	'pa_uso'          => bamira_real_attr_id( 'uso' ),
);

// ---------------------------------------------------------------------------
// 4. FASE 2 & 3 — Procesar Productos Padre y Variaciones Hijas.
// ---------------------------------------------------------------------------

foreach ( $bamira_real_catalog as $p_data ) {
	$parent_sku = $p_data['parent_sku'];
	$cat_key    = $p_data['parent_cat'] . '|' . $p_data['category'];
	$child_cat  = isset( $category_map[ $cat_key ] ) ? $category_map[ $cat_key ] : 0;

	// Slugs para variación del padre
	$expected_talles = array();
	foreach ( $p_data['talles'] as $t ) {
		if ( isset( $talle_slugs[ $t ] ) ) {
			$expected_talles[] = $talle_slugs[ $t ];
		}
	}

	$expected_colores = array();
	foreach ( $p_data['colores'] as $c ) {
		if ( isset( $color_slugs[ $c ] ) ) {
			$expected_colores[] = $color_slugs[ $c ];
		}
	}

	// 4.1 Buscar producto padre por SKU
	$parent_id = wc_get_product_id_by_sku( $parent_sku );
	$parent    = null;

	if ( $parent_id > 0 ) {
		$parent = wc_get_product( $parent_id );
		bamira_real_log( 'PRODUCT', $p_data['name'] . ' [' . $parent_sku . ']', 'EXISTS', 'id=' . $parent_id . ' status=' . $parent->get_status() );
		++$bamira_log_counts['products_exists'];
	} else {
		$parent = new WC_Product_Variable();
		$parent->set_name( $p_data['name'] );
		$parent->set_slug( sanitize_title( $p_data['name'] ) );
		$parent->set_status( 'draft' );
		$parent->set_sku( $parent_sku );
		if ( $child_cat > 0 ) {
			$parent->set_category_ids( array( $child_cat ) );
		}

		$wc_attributes = array();

		// pa_talle para variaciones
		$attr_talle = new WC_Product_Attribute();
		$attr_talle->set_id( $attr_ids['pa_talle'] );
		$attr_talle->set_name( 'pa_talle' );
		$attr_talle->set_options( $expected_talles );
		$attr_talle->set_visible( true );
		$attr_talle->set_variation( true );
		$wc_attributes[] = $attr_talle;

		// pa_color-diseno para variaciones
		$attr_color = new WC_Product_Attribute();
		$attr_color->set_id( $attr_ids['pa_color-diseno'] );
		$attr_color->set_name( 'pa_color-diseno' );
		$attr_color->set_options( $expected_colores );
		$attr_color->set_visible( true );
		$attr_color->set_variation( true );
		$wc_attributes[] = $attr_color;

		// pa_linea informativo
		if ( $attr_ids['pa_linea'] > 0 && taxonomy_exists( 'pa_linea' ) ) {
			$linea_slug = bamira_real_term_slug( $p_data['linea'], 'pa_linea' );
			if ( '' !== $linea_slug ) {
				$attr_linea = new WC_Product_Attribute();
				$attr_linea->set_id( $attr_ids['pa_linea'] );
				$attr_linea->set_name( 'pa_linea' );
				$attr_linea->set_options( array( $linea_slug ) );
				$attr_linea->set_visible( true );
				$attr_linea->set_variation( false );
				$wc_attributes[] = $attr_linea;
			}
		}

		// pa_uso informativo
		if ( $attr_ids['pa_uso'] > 0 && taxonomy_exists( 'pa_uso' ) ) {
			$uso_slug = bamira_real_term_slug( $p_data['uso'], 'pa_uso' );
			if ( '' !== $uso_slug ) {
				$attr_uso = new WC_Product_Attribute();
				$attr_uso->set_id( $attr_ids['pa_uso'] );
				$attr_uso->set_name( 'pa_uso' );
				$attr_uso->set_options( array( $uso_slug ) );
				$attr_uso->set_visible( true );
				$attr_uso->set_variation( false );
				$wc_attributes[] = $attr_uso;
			}
		}

		$parent->set_attributes( $wc_attributes );
		$parent_id = $parent->save();

		if ( ! $parent_id || is_wp_error( $parent_id ) ) {
			$err = is_wp_error( $parent_id ) ? $parent_id->get_error_message() : 'save() sin ID';
			bamira_real_log( 'PRODUCT', $p_data['name'] . ' [' . $parent_sku . ']', 'ERROR', $err );
			++$bamira_log_counts['errors'];
			continue;
		}

		bamira_real_log( 'PRODUCT', $p_data['name'] . ' [' . $parent_sku . ']', 'CREATED', 'id=' . $parent_id . ' status=draft' );
		++$bamira_log_counts['products_created'];
	}

	// 4.2 Procesar Variaciones
	foreach ( $p_data['variations'] as $v_info ) {
		$v_color    = $v_info['color'];
		$v_talle    = $v_info['talle'];
		$v_stock    = (int) $v_info['stock'];
		$color_code = isset( $bamira_real_colores[ $v_color ] ) ? $bamira_real_colores[ $v_color ] : strtoupper( sanitize_title( $v_color ) );
		$var_sku    = sprintf( '%s-%s-T%s', $parent_sku, $color_code, $v_talle );

		$bamira_log_counts['total_units'] += $v_stock;

		$found_v_id = wc_get_product_id_by_sku( $var_sku );
		if ( $found_v_id > 0 ) {
			$found_v = wc_get_product( $found_v_id );
			$found_parent = ( $found_v && method_exists( $found_v, 'get_parent_id' ) ) ? (int) $found_v->get_parent_id() : 0;
			if ( $found_parent === (int) $parent_id ) {
				// Idempotencia segura: si la variación ya existe, se preserva el stock real actual
				// para evitar que una re-ejecución sobrescriba accidentalmente el inventario operativo.
				$current_stock = $found_v->get_stock_quantity();
				$stock_label   = ( null !== $current_stock ) ? (string) $current_stock : 'n/a';
				bamira_real_log( 'VARIATION', $var_sku, 'EXISTS', 'id=' . $found_v_id . ' stock_actual=' . $stock_label . ' (preservado)' );
				++$bamira_log_counts['variations_exists'];
			} else {
				bamira_real_log( 'VARIATION', $var_sku, 'ERROR', 'SKU colisiona con producto no variante o padre distinto (id=' . $found_v_id . ' padre=' . $found_parent . ')' );
				++$bamira_log_counts['errors'];
			}
			continue;
		}

		$variation = new WC_Product_Variation();
		$variation->set_parent_id( (int) $parent_id );
		$variation->set_sku( $var_sku );
		$variation->set_attributes( array(
			'pa_talle'        => $talle_slugs[ $v_talle ],
			'pa_color-diseno' => $color_slugs[ $v_color ],
		) );
		$variation->set_manage_stock( true );
		$variation->set_stock_quantity( $v_stock );
		$variation->set_stock_status( 'instock' );
		// Precios e imágenes se mantienen vacíos / no configurados por diseño

		$new_v_id = $variation->save();
		if ( ! $new_v_id || is_wp_error( $new_v_id ) ) {
			$err = is_wp_error( $new_v_id ) ? $new_v_id->get_error_message() : 'save() sin ID';
			bamira_real_log( 'VARIATION', $var_sku, 'ERROR', $err );
			++$bamira_log_counts['errors'];
			continue;
		}

		bamira_real_log( 'VARIATION', $var_sku, 'CREATED', 'id=' . $new_v_id . ' stock=' . $v_stock );
		++$bamira_log_counts['variations_created'];
	}
}

// ---------------------------------------------------------------------------
// 5. Resumen final y validación de consistencia.
// ---------------------------------------------------------------------------

$total_vars_processed = $bamira_log_counts['variations_created'] + $bamira_log_counts['variations_exists'];
$total_prods_processed = $bamira_log_counts['products_created'] + $bamira_log_counts['products_exists'];

$validation_ok = ( 8 === $total_prods_processed && 66 === $total_vars_processed && 427 === $bamira_log_counts['total_units'] && 0 === $bamira_log_counts['errors'] );

$summary = sprintf(
	"=========================================\n" .
	"BAMIRA — RESUMEN CATÁLOGO REAL · ETAPA 1\n" .
	"=========================================\n" .
	"Productos padre procesados: %d (esperados: 8)\n" .
	"Variaciones procesadas:     %d (esperadas: 66)\n" .
	"Unidades de stock totales:  %d (esperadas: 427)\n" .
	"Errores reportados:         %d\n" .
	"Estado de validación:       %s\n" .
	"=========================================",
	$total_prods_processed,
	$total_vars_processed,
	$bamira_log_counts['total_units'],
	$bamira_log_counts['errors'],
	$validation_ok ? 'ASSERT_OK (VÁLIDO)' : 'ASSERT_FAILED (REVISAR)'
);

if ( class_exists( 'WP_CLI' ) && function_exists( 'WP_CLI' ) ) {
	WP_CLI::line( $summary );
} else {
	echo $summary . "\n";
}
