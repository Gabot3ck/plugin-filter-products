<?php
/**
 * Plugin Name: Filtro de Productos
 * Description: Un plugin para agregar filtros personalizados al Ecommerce Scanavini Perú en Woocommerce.
 * Version: 1.0
 * Author: Gabriel Castillo
 * License: MIT
 */

// Asegurarse de que WordPress está cargado
if (!defined('ABSPATH')) {
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

// Función para obtener las categorías hijas de la categoría actual y contar productos
function fcw_generar_menu_categorias($categoria_id) {
    $categorias = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => $categoria_id, // Solo obtener categorías hijas
    ));

    if (empty($categorias)) {
        return ''; // No mostrar el menú si no hay subcategorías
    }

    $output = '<div class="list-group">';
    foreach ($categorias as $categoria) {
        $url_categoria = get_term_link($categoria);
        $cantidad_productos = $categoria->count; // Obtener la cantidad de productos en la categoría
        $output .= '<a href="' . esc_url($url_categoria) . '" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">';
        $output .= esc_html($categoria->name);
        $output .= '<span class="badge bg-primary rounded-pill">' . esc_html($cantidad_productos) . '</span>';
        $output .= '</a>';
    }
    $output .= '</div>';

    return $output;
}

// Función para obtener los colores disponibles en la categoría actual y contar productos
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
                if (!isset($colores[$color->slug])) {
                    $colores[$color->slug] = array(
                        'name' => $color->name,
                        'count' => 0,
                    );
                }
                $colores[$color->slug]['count']++;
            }
        }
        wp_reset_postdata();
    }

    if (empty($colores)) {
        return ''; // No mostrar filtro si no hay colores disponibles
    }

    $output = '<div class="list-group">';
    foreach ($colores as $color_slug => $color_data) {
        $output .= '<a href="?filter_color=' . esc_attr($color_slug) . '" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">';
        $output .= esc_html($color_data['name']);
        $output .= '<span class="badge bg-primary rounded-pill">' . esc_html($color_data['count']) . '</span>';
        $output .= '</a>';
    }
    $output .= '</div>';

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

    $categoria_url = get_term_link($categoria_actual_id, 'product_cat');
    
    $output = '<div class="accordion" id="fcwAccordion">';
    
    // Acordeón para categorías
    if (!empty($menu_categorias)) {
        $output .= '<div class="accordion-item">';
        $output .= '<h2 class="accordion-header" id="fcwHeadingCat">';
        $output .= '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#fcwCollapseCat" aria-expanded="true" aria-controls="fcwCollapseCat">';
        $output .= 'Filtrar por Categoría';
        $output .= '</button>';
        $output .= '</h2>';
        $output .= '<div id="fcwCollapseCat" class="accordion-collapse collapse show" aria-labelledby="fcwHeadingCat" data-bs-parent="#fcwAccordion">';
        $output .= '<div class="accordion-body">';
        $output .= $menu_categorias;
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
    }
    
    // Acordeón para colores
    if (!empty($menu_colores)) {
        $output .= '<div class="accordion-item">';
        $output .= '<h2 class="accordion-header" id="fcwHeadingColor">';
        $output .= '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fcwCollapseColor" aria-expanded="false" aria-controls="fcwCollapseColor">';
        $output .= 'Filtrar por Color';
        $output .= '</button>';
        $output .= '</h2>';
        $output .= '<div id="fcwCollapseColor" class="accordion-collapse collapse" aria-labelledby="fcwHeadingColor" data-bs-parent="#fcwAccordion">';
        $output .= '<div class="accordion-body">';
        $output .= $menu_colores;
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
    }
    
    // Botón para limpiar filtros
    $output .= '<div class="mt-3">';
    $output .= '<a href="' . esc_url($categoria_url) . '" class="btn btn-danger w-100">Borrar Filtros</a>';
    $output .= '</div>';
    
    $output .= '</div>';

    return $output;
}
add_shortcode('filtro_categorias', 'fcw_shortcode_filtro_categorias');