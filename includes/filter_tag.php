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
            $product_tags = wp_get_post_terms($product_id, 'product_tag');
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
        return '';
    }

    // Obtener el tag actualmente seleccionado
    $current_tag = isset($_GET['filter_tag']) ? sanitize_text_field($_GET['filter_tag']) : '';

    $output = '<div class="list-group">';
    foreach ($tags as $tag_slug => $tag_data) {
        $is_active = $current_tag === $tag_slug ? ' active' : '';
        $output .= sprintf(
            '<a href="%s" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center%s">',
            add_query_arg('filter_tag', $tag_slug),
            $is_active
        );
        $output .= esc_html($tag_data['name']);
        $output .= '<span class="badge rounded-pill">' . esc_html($tag_data['count']) . '</span>';
        $output .= '</a>';
    }
    $output .= '</div>';

    return $output;
}

// Función para modificar la consulta principal de WooCommerce
function fcw_filter_products_by_tag($q) {
    if (!$q->is_main_query() || !is_shop() && !is_product_category()) {
        return;
    }

    if (isset($_GET['filter_tag']) && !empty($_GET['filter_tag'])) {
        $tax_query = $q->get('tax_query');
        if (!is_array($tax_query)) {
            $tax_query = array();
        }
        
        $tax_query[] = array(
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_GET['filter_tag']),
        );
        
        $q->set('tax_query', $tax_query);
    }
}
add_action('woocommerce_product_query', 'fcw_filter_products_by_tag');
