<?php
/**
 * Plugin Name: Filtro de Productos
 * Description: Un plugin para agregar filtros personalizados al Ecommerce Scanavini Perú en Woocommerce.
 * Version: 1.0
 * Author: Gabriel Castillo
 * License: MIT
 */

// Evitar acceso directo al archivo
if (!defined('ABSPATH')) {
    exit;
}

// Cargar archivos de inclusión
require_once plugin_dir_path(__FILE__) . 'includes/helpers.php';
require_once plugin_dir_path(__FILE__) . 'includes/filter_categories.php';
require_once plugin_dir_path(__FILE__) . 'includes/filter_colors.php';

// Registrar el shortcode
function fcw_shortcode_filtro_categorias() {
    $current_category_id = fcw_get_current_category();
    
    if ($current_category_id == 0) {
        return ''; // No mostrar el filtro si no estamos en una categoría
    }

    $output = '<div class="accordion" id="fcwAccordion">';
    
    // Acordeón para categorías
    $menu_categories = fcw_create_menu_categories($current_category_id);
    if (!empty($menu_categories)) {
        $output .= '<div class="accordion-item">';
        $output .= '<h2 class="accordion-header" id="fcwHeadingCat">';
        $output .= '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#fcwCollapseCat" aria-expanded="true" aria-controls="fcwCollapseCat">';
        $output .= 'Filtrar por Categoría';
        $output .= '</button>';
        $output .= '</h2>';
        $output .= '<div id="fcwCollapseCat" class="accordion-collapse collapse show" aria-labelledby="fcwHeadingCat" data-bs-parent="#fcwAccordion">';
        $output .= '<div class="accordion-body">';
        $output .= $menu_categories;
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
    }
    
    // Acordeón para colores
    $menu_colores = fcw_create_menu_colors($current_category_id);
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
    $category_url = get_term_link($current_category_id, 'product_cat');
    $output .= '<div class="mt-3">';
    $output .= '<a href="' . esc_url($category_url) . '" class="btn btn-danger w-100">Borrar Filtros</a>';
    $output .= '</div>';
    
    $output .= '</div>';

    return $output;
}
add_shortcode('filtro_categorias', 'fcw_shortcode_filtro_categorias');