<?php
/**
 * Plugin Name: Filtro de Productos
 * Description: Un plugin para agregar filtros personalizados al Ecommerce Scanavini Perú en Woocommerce.
 * Version: 1.0
 * Author: Gabriel Castillo
 * License: MIT
 */

// Asegurarse de que WordPress está cargado
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Función para obtener la categoría actual
function fcw_obtener_categoria_actual() {
    if (is_tax('product_cat')) {
        $term = get_queried_object();
        return $term ? $term->term_id : 0;
    }
    return 0;
}

// Función para obtener las categorías hermanas e hijas
function fcw_generar_menu_categorias($categoria_id) {
    $term = get_term($categoria_id, 'product_cat');
    if (!$term) {
        return '';
    }

    $parent_id = $term->parent ? $term->parent : $categoria_id;
    $categorias = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => $parent_id,
    ));

    if (empty($categorias)) {
        return '';
    }

    $output = '<ul class="dropdown-menu">';
    foreach ($categorias as $categoria) {
        $url_categoria = get_term_link($categoria);
        $output .= '<li><a class="dropdown-item" href="' . esc_url($url_categoria) . '">' . esc_html($categoria->name) . '</a></li>';
    }
    $output .= '</ul>';

    return $output;
}

// Registrar el shortcode
function fcw_shortcode_filtro_categorias() {
    $categoria_actual_id = fcw_obtener_categoria_actual();
    
    if ($categoria_actual_id == 0) {
        return ''; // No mostrar el filtro si no estamos en una categoría
    }

    $output = '<div class="dropdown">';
    $output .= '<button class="btn btn-primary dropdown-toggle" type="button" id="fcwDropdown" data-bs-toggle="dropdown" aria-expanded="false">Filtrar por Categoría</button>';
    $output .= fcw_generar_menu_categorias($categoria_actual_id);
    $output .= '</div>';

    return $output;
}
add_shortcode('filtro_categorias', 'fcw_shortcode_filtro_categorias');