<?php
// Función para obtener las categorías hijas, hermanas y padres de la categoría actual
function fcw_create_menu_categories($category_id) {
    if (!$category_id) {
        return ''; // Evita errores si no hay una categoría seleccionada
    }

    // Obtener la categoría actual
    $current_category = get_term($category_id, 'product_cat');
    if (!$current_category || is_wp_error($current_category)) {
        return '';
    }

    $output = '<div class="list-group">';

    // 1️⃣ **Mostrar las categorías hijas de la categoría actual**
    $child_categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => $category_id,
    ));
    if (!empty($child_categories)) {
        $output .= '<h5 class="fw-bold mt-2">Subcategorías</h5>';
        foreach ($child_categories as $category) {
            $output .= fcw_generate_category_link($category);
        }
    }

    // 2️⃣ **Mostrar las categorías hermanas** (las que comparten el mismo padre)
    if ($current_category->parent != 0) { // Solo si la categoría actual tiene padre
        $sibling_categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'parent' => $current_category->parent,
        ));
        if (!empty($sibling_categories)) {
            $output .= '<h5 class="fw-bold mt-3">Categorías Relacionadas</h5>';
            foreach ($sibling_categories as $category) {
                if ($category->term_id !== $category_id) { // Evitar incluir la categoría actual
                    $output .= fcw_generate_category_link($category);
                }
            }
        }
    }

    // 3️⃣ **Mostrar las categorías principales** (sin padre, solo las más generales)
    $parent_categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => 0,
    ));
    if (!empty($parent_categories)) {
        $output .= '<h5 class="fw-bold mt-3">Categorías Principales</h5>';
        foreach ($parent_categories as $category) {
            $output .= fcw_generate_category_link($category);
        }
    }

    $output .= '</div>';
    return $output;
}

// Función auxiliar para generar los enlaces de categoría
function fcw_generate_category_link($category) {
    $url_category = get_term_link($category);
    $quantity_products = $category->count;
    return '<a href="' . esc_url($url_category) . '" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">' .
        esc_html($category->name) .
        '<span class="badge bg-primary rounded-pill">' . esc_html($quantity_products) . '</span>' .
        '</a>';
}
