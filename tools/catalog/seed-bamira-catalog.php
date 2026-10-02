<?php
/**
 * BAMIRA — Seed de estructura del catálogo (v1).
 *
 * Inicializa UNICAMENTE la estructura global del catálogo WooCommerce:
 *  - Categorías/rubros (product_cat) y sus subcategorías.
 *  - Atributos globales (pa_*) y sus términos.
 *
 * Lo que NO hace (por diseño):
 *  - No crea productos ni variaciones.
 *  - No modifica productos existentes (incluidos los de prueba).
 *  - No elimina nada.
 *
 * Idempotencia:
 *  - Verifica existencia antes de crear (term_exists / wc_get_attribute_taxonomies).
 *  - Puede ejecutarse N veces y tras agregar nuevos valores al código:
 *    solo crea lo faltante e informa CREATED / EXISTS / ERROR por ítem.
 *
 * Uso previsto (NO ejecutar automáticamente):
 *   wp eval-file tools/catalog/seed-bamira-catalog.php
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

if ( ! function_exists( 'wc_create_attribute' ) || ! function_exists( 'wc_get_attribute_taxonomies' ) || ! class_exists( 'WooCommerce' ) ) {
	echo "ERROR: WooCommerce no disponible. Active WooCommerce e intente nuevamente. Sin cambios.\n";
	return;
}

if ( ! function_exists( 'term_exists' ) || ! function_exists( 'wp_insert_term' ) ) {
	echo "ERROR: funciones de taxonomías de WordPress no disponibles. Sin cambios.\n";
	return;
}

// ---------------------------------------------------------------------------
// 1. Datos del catálogo v1 (fuente única; agregar aquí futuros valores).
// ---------------------------------------------------------------------------

$bamira_categories = array(
	array( 'name' => 'Indumentaria deportiva', 'children' => array(
		'Malla deportiva',
		'Short deportivo',
		'Remera deportiva',
		'Musculosa deportiva',
		'Top deportivo',
		'Calza corta',
		'Calza larga',
	) ),
	array( 'name' => 'Urbano', 'children' => array(
		'Malla de baño',
	) ),
);

$bamira_attributes = array(
	array(
		'slug'  => 'pa_talle',
		'label' => 'Talle',
		'terms' => array( 'XS', 'S', 'M', 'L' ),
	),
	array(
		'slug'  => 'pa_color-diseno',
		'label' => 'Color / Diseño',
		'terms' => array( 'Blanco', 'Negro', 'Verde aceituna', 'Azul', 'Marrón', 'Celeste' ),
	),
	array(
		'slug'  => 'pa_linea',
		'label' => 'Línea',
		'terms' => array( 'Mujer', 'Hombre', 'Unisex' ),
	),
	array(
		'slug'  => 'pa_uso',
		'label' => 'Uso',
		'terms' => array( 'Entrenamiento', 'Competición', 'Baño', 'Casual' ),
	),
);

// ---------------------------------------------------------------------------
// 2. Utilidades de salida y conteo.
// ---------------------------------------------------------------------------

$bamira_created = 0;
$bamira_exists  = 0;
$bamira_errors  = 0;

function bamira_seed_log( $kind, $label, $status, $detail = '' ) {
	$line = sprintf( '[%s] %s ... %s', $kind, $label, $status );
	if ( '' !== $detail ) {
		$line .= ' (' . $detail . ')';
	}
	if ( function_exists( 'WP_CLI' ) && class_exists( 'WP_CLI' ) ) {
		WP_CLI::line( $line );
	} else {
		echo $line . "\n";
	}
}

// ---------------------------------------------------------------------------
// 3. Categorías (product_cat): padres + hijas, sin duplicar.
// ---------------------------------------------------------------------------

foreach ( $bamira_categories as $parent ) {
	$parent_name = $parent['name'];
	$parent_id   = 0;

	$found = term_exists( $parent_name, 'product_cat' );
	if ( is_array( $found ) && isset( $found['term_id'] ) ) {
		$parent_id = (int) $found['term_id'];
		bamira_seed_log( 'CATEGORY', $parent_name, 'EXISTS', 'id=' . $parent_id );
		++$bamira_exists;
	} else {
		$inserted = wp_insert_term( $parent_name, 'product_cat' );
		if ( is_wp_error( $inserted ) ) {
			bamira_seed_log( 'CATEGORY', $parent_name, 'ERROR', $inserted->get_error_message() );
			++$bamira_errors;
			continue; // Sin padre no se pueden crear sus hijas; seguir con el resto.
		}
		$parent_id = (int) $inserted['term_id'];
		bamira_seed_log( 'CATEGORY', $parent_name, 'CREATED', 'id=' . $parent_id );
		++$bamira_created;
	}

	foreach ( $parent['children'] as $child_name ) {
		$label = $child_name . ' (padre: ' . $parent_name . ')';

		// Buscar hija bajo el padre correcto para no confundir homónimos.
		$found_child = term_exists( $child_name, 'product_cat', $parent_id );
		if ( is_array( $found_child ) && isset( $found_child['term_id'] ) ) {
			bamira_seed_log( 'CATEGORY', $label, 'EXISTS', 'id=' . (int) $found_child['term_id'] );
			++$bamira_exists;
			continue;
		}

		// Fallback: si existe con el mismo nombre bajo otro padre, no duplicar.
		$found_any = term_exists( $child_name, 'product_cat' );
		if ( is_array( $found_any ) && isset( $found_any['term_id'] ) ) {
			bamira_seed_log( 'CATEGORY', $label, 'EXISTS', 'id=' . (int) $found_any['term_id'] . ' (padre distinto, no duplicado)' );
			++$bamira_exists;
			continue;
		}

		$inserted_child = wp_insert_term( $child_name, 'product_cat', array( 'parent' => $parent_id ) );
		if ( is_wp_error( $inserted_child ) ) {
			bamira_seed_log( 'CATEGORY', $label, 'ERROR', $inserted_child->get_error_message() );
			++$bamira_errors;
			continue;
		}
		bamira_seed_log( 'CATEGORY', $label, 'CREATED', 'id=' . (int) $inserted_child['term_id'] );
		++$bamira_created;
	}
}

// ---------------------------------------------------------------------------
// 4. Atributos globales + términos, sin duplicar.
// ---------------------------------------------------------------------------

foreach ( $bamira_attributes as $attr ) {
	$attr_slug  = $attr['slug']; // Con o sin prefijo pa_; normalizar a nombre sin prefijo para WooCommerce.
	$attr_name  = preg_replace( '/^pa_/', '', $attr_slug );
	$attr_label = $attr['label'];

	// ¿Existe ya el atributo? wc_get_attribute_taxonomies() devuelve objetos con attribute_name (sin pa_).
	$attribute_id = 0;
	$taxonomies   = wc_get_attribute_taxonomies();
	if ( is_array( $taxonomies ) ) {
		foreach ( $taxonomies as $tax ) {
			if ( isset( $tax->attribute_name ) && $tax->attribute_name === $attr_name ) {
				$attribute_id = (int) $tax->attribute_id;
				break;
			}
		}
	}

	if ( $attribute_id > 0 ) {
		bamira_seed_log( 'ATTRIBUTE', $attr_label . ' (' . $attr_slug . ')', 'EXISTS' );
		++$bamira_exists;
	} else {
		$new_id = wc_create_attribute( array(
			'name'    => $attr_label,
			'slug'    => $attr_name,
			'type'    => 'select',
			'orderby' => 'menu_order',
		) );
		if ( is_wp_error( $new_id ) ) {
			bamira_seed_log( 'ATTRIBUTE', $attr_label . ' (' . $attr_slug . ')', 'ERROR', $new_id->get_error_message() );
			++$bamira_errors;
			continue; // Sin atributo no se pueden crear sus términos; seguir con el resto.
		}
		$attribute_id = (int) $new_id;
		bamira_seed_log( 'ATTRIBUTE', $attr_label . ' (' . $attr_slug . ')', 'CREATED', 'id=' . $attribute_id );
		++$bamira_created;

		// Registrar la taxonomía en este mismo request para poder insertar términos.
		if ( function_exists( 'register_taxonomy' ) ) {
			register_taxonomy(
				$attr_slug,
				apply_filters( 'woocommerce_taxonomy_objects_' . $attr_slug, array( 'product' ) ),
				apply_filters( 'woocommerce_taxonomy_args_' . $attr_slug, array(
					'hierarchical' => false,
					'show_ui'      => false,
					'query_var'    => true,
					'rewrite'      => false,
				) )
			);
		}
		delete_transient( 'wc_attribute_taxonomies' );
	}

	foreach ( $attr['terms'] as $term_name ) {
		$label = $attr_slug . ': ' . $term_name;
		$found = term_exists( $term_name, $attr_slug );
		if ( is_array( $found ) && isset( $found['term_id'] ) ) {
			bamira_seed_log( 'TERM', $label, 'EXISTS' );
			++$bamira_exists;
			continue;
		}
		$inserted = wp_insert_term( $term_name, $attr_slug );
		if ( is_wp_error( $inserted ) ) {
			bamira_seed_log( 'TERM', $label, 'ERROR', $inserted->get_error_message() );
			++$bamira_errors;
			continue;
		}
		bamira_seed_log( 'TERM', $label, 'CREATED' );
		++$bamira_created;
	}
}

// ---------------------------------------------------------------------------
// 5. Resumen final.
// ---------------------------------------------------------------------------

$summary = sprintf( 'DONE. created=%d exists=%d errors=%d', $bamira_created, $bamira_exists, $bamira_errors );
if ( class_exists( 'WP_CLI' ) && function_exists( 'WP_CLI' ) ) {
	WP_CLI::line( $summary );
} else {
	echo $summary . "\n";
}
