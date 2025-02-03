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
        return ''; // No mostrar el menú si no hay subcategorías
    }

    $output = '<ul class="dropdown-menu">';
    foreach ($categorias as $categoria) {
        $url_categoria = get_term_link($categoria);
        $output .= '<li><a class="dropdown-item" href="' . esc_url($url_categoria) . '">' . esc_html($categoria->name) . '</a></li>';
    }
    $output .= '</ul>';

    return $output;
}

// Función para obtener los colores disponibles en la categoría actual
function fcw_obtener_colores_disponibles($categoria_id) {
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => $categoria_id,
            ),
        ),
    );
    $query = new WP_Query($args);
    $colores = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $producto_id = get_the_ID();
            $producto_colores = wp_get_post_terms($producto_id, 'pa_color');
            foreach ($producto_colores as $color) {
                $colores[$color->slug] = $color->name;
            }
        }
        wp_reset_postdata();
    }

    if (empty($colores)) {
        return ''; // No mostrar filtro si no hay colores disponibles
    }

    $output = '<ul class="dropdown-menu">';
    foreach ($colores as $color_slug => $color_nombre) {
        $output .= '<li><a class="dropdown-item" href="?filter_color=' . esc_attr($color_slug) . '">' . esc_html($color_nombre) . '</a></li>';
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

    $menu_categorias = fcw_generar_menu_categorias($categoria_actual_id);
    $menu_colores = fcw_obtener_colores_disponibles($categoria_actual_id);
    
    if (empty($menu_categorias) && empty($menu_colores)) {
        return ''; // Ocultar completamente si no hay filtros disponibles
    }

    $output = '<div class="dropdown">';
    if (!empty($menu_categorias)) {
        $output .= '<button class="btn btn-primary dropdown-toggle" type="button" id="fcwDropdownCat" data-bs-toggle="dropdown" aria-expanded="false">Filtrar por Categoría</button>';
        $output .= $menu_categorias;
    }
    if (!empty($menu_colores)) {
        $output .= '<button class="btn btn-secondary dropdown-toggle" type="button" id="fcwDropdownColor" data-bs-toggle="dropdown" aria-expanded="false">Filtrar por Color</button>';
        $output .= $menu_colores;
    }
    $output .= '</div>';

    return $output;
}
add_shortcode('filtro_categorias', 'fcw_shortcode_filtro_categorias');
