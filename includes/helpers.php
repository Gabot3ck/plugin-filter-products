<?php
// Función para obtener la categoría actual
function fcw_get_current_category() {
    if (is_tax('product_cat')) {
        $term = get_queried_object();
        return $term; // Devolver el objeto completo de la categoría
    }
    return null; // Devolver null si no estamos en una categoría
}