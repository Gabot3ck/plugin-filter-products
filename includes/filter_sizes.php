<?php
// Función para obtener las medidas disponibles en la categoría actual y contar productos
function fcw_create_menu_sizes($category_id) {
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => $category_id,
            ),
        ),
    );
    $query = new WP_Query($args);
    $sizes = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $product_id = get_the_ID();
            $product_sizes = wp_get_post_terms($product_id, 'pa_medida'); // Usar 'pa_medida' para atributos de WooCommerce
            foreach ($product_sizes as $size) {
                if (!isset($sizes[$size->slug])) {
                    $sizes[$size->slug] = array(
                        'name' => $size->name,
                        'count' => 0,
                        'numeric_value' => floatval(preg_replace('/[^0-9.]/', '', $size->name)), // Extrae el valor numérico
                    );
                }
                $sizes[$size->slug]['count']++;
            }
        }
        wp_reset_postdata();
    }

    if (empty($sizes)) {
        return ''; // No mostrar filtro si no hay medidas disponibles
    }

    // Ordenar las medidas por su valor numérico
    uasort($sizes, function($a, $b) {
        return $a['numeric_value'] <=> $b['numeric_value'];
    });

    // Generar la estructura del filtro ordenado
    $output = '<div class="list-group">';
    foreach ($sizes as $size_slug => $size_data) {
        $output .= '<a href="?filter_medida=' . esc_attr($size_slug) . '" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">';
        $output .= esc_html($size_data['name']);
        $output .= '<span class="badge bg-primary rounded-pill">' . esc_html($size_data['count']) . '</span>';
        $output .= '</a>';
    }
    $output .= '</div>';

    return $output;
}
