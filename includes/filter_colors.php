<?php
// Función para obtener los colors disponibles en la categoría actual y contar productos
function fcw_create_menu_colors($categoria_id) {
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
    $colors = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $product_id = get_the_ID();
            $product_colors = wp_get_post_terms($product_id, 'pa_color');
            foreach ($product_colors as $color) {
                if (!isset($colors[$color->slug])) {
                    $colors[$color->slug] = array(
                        'name' => $color->name,
                        'count' => 0,
                    );
                }
                $colors[$color->slug]['count']++;
            }
        }
        wp_reset_postdata();
    }

    if (empty($colors)) {
        return ''; // No mostrar filtro si no hay colors disponibles
    }

    $output = '<div class="list-group">';
    foreach ($colors as $color_slug => $color_data) {
        $output .= '<a href="?filter_color=' . esc_attr($color_slug) . '" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">';
        $output .= esc_html($color_data['name']);
        $output .= '<span class="badge rounded-pill">' . esc_html($color_data['count']) . '</span>';
        $output .= '</a>';
    }
    $output .= '</div>';

    return $output;
}