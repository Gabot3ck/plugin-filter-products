<?php
/**
 * Plugin Name: Filtro de Productos
 * Description: Un plugin para agregar filtros personalizados al Ecommerce Scanavini Perú en WooCommerce.
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
require_once plugin_dir_path(__FILE__) . 'includes/filter_sizes.php';

// Registrar el shortcode
function fcw_shortcode_filter_categories() {
    $current_category = fcw_get_current_category();
    
    // Si no estamos en una categoría, no mostrar el filtro
    if (!$current_category) {
        return '';
    }

    // Generar el HTML del acordeón
    $output = '<div class="accordion" id="fcwAccordion">';
    
    // Acordeón para categorías
    $menu_categories = fcw_create_menu_categories($current_category->term_id);
    if (!empty($menu_categories)) {
        $output .= '<div class="accordion-item">';
        $output .= '<h2 class="accordion-header" id="fcwHeadingCat">';
        $output .= '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#fcwCollapseCat" aria-expanded="true" aria-controls="fcwCollapseCat">';
        $output .= esc_html__('Filtrar por Categoría', 'fcw'); // Texto traducible
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
    $menu_colores = fcw_create_menu_colors($current_category->term_id);
    if (!empty($menu_colores)) {
        $output .= '<div class="accordion-item">';
        $output .= '<h2 class="accordion-header" id="fcwHeadingColor">';
        $output .= '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fcwCollapseColor" aria-expanded="false" aria-controls="fcwCollapseColor">';
        $output .= esc_html__('Filtrar por Color', 'fcw'); // Texto traducible
        $output .= '</button>';
        $output .= '</h2>';
        $output .= '<div id="fcwCollapseColor" class="accordion-collapse collapse" aria-labelledby="fcwHeadingColor" data-bs-parent="#fcwAccordion">';
        $output .= '<div class="accordion-body">';
        $output .= $menu_colores;
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
    }

    // Acordeón para medidas
    $menu_sizes = fcw_create_menu_sizes($current_category->term_id);
    if (!empty($menu_sizes)) {
        $output .= '<div class="accordion-item">';
        $output .= '<h2 class="accordion-header" id="fcwHeadingSize">';
        $output .= '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fcwCollapseSize" aria-expanded="false" aria-controls="fcwCollapseSize">';
        $output .= esc_html__('Filtrar por Medida', 'fcw'); // Texto traducible
        $output .= '</button>';
        $output .= '</h2>';
        $output .= '<div id="fcwCollapseSize" class="accordion-collapse collapse" aria-labelledby="fcwHeadingSize" data-bs-parent="#fcwAccordion">';
        $output .= '<div class="accordion-body">';
        $output .= $menu_sizes;
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
    }

    $output .= '</div>'; // Cerrar el contenedor del acordeón

    return $output;
}
add_shortcode('filtro_categorias', 'fcw_shortcode_filter_categories');