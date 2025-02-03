<?php
// Función para obtener la categoría actual
function fcw_get_current_category() {
    if (is_tax('product_cat')) {
        $term = get_queried_object();
        return $term ? $term->term_id : 0;
    }
    return 0;
}