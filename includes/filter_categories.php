<?php
// Función para obtener las categorías hijas de la categoría actual y contar productos
function fcw_create_menu_categories($category_id) {
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => $category_id, // Solo obtener categorías hijas
    ));

    if (empty($categories)) {
        return ''; // No mostrar el menú si no hay subcategorías
    }

    $output = '<div class="list-group">';
    foreach ($categories as $category) {
        $url_category = get_term_link($category);
        $quantity_products = $category->count; // Obtener la cantidad de productos en la categoría
        $output .= '<a href="' . esc_url($url_category) . '" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">';
        $output .= esc_html($category->name);
        $output .= '<span class="badge bg-primary rounded-pill">' . esc_html($quantity_products) . '</span>';
        $output .= '</a>';
    }
    $output .= '</div>';

    return $output;
}