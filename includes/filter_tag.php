<?php
// Función para obtener las etiquetas disponibles en la categoría actual y contar productos
function fcw_create_menu_tags($category_id) {
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
    $tags = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $product_id = get_the_ID();
            $product_tags = wp_get_post_terms($product_id, 'product_tag'); // Obtener las etiquetas del producto
            foreach ($product_tags as $tag) {
                if (!isset($tags[$tag->slug])) {
                    $tags[$tag->slug] = array(
                        'name' => $tag->name,
                        'count' => 0,
                    );
                }
                $tags[$tag->slug]['count']++;
            }
        }
        wp_reset_postdata();
    }

    if (empty($tags)) {
        return ''; // No mostrar filtro si no hay etiquetas disponibles
    }

    $output = '<div class="list-group">';
    foreach ($tags as $tag_slug => $tag_data) {
        $output .= '<a href="?filter_tag=' . esc_attr($tag_slug) . '" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">';
        $output .= esc_html($tag_data['name']);
        $output .= '<span class="badge rounded-pill">' . esc_html($tag_data['count']) . '</span>';
        $output .= '</a>';
    }
    $output .= '</div>';

    return $output;
}