<?php
/**
 * Plugin Name: Filtro de Productos
 * Description: Un plugin para agregar filtros personalizados al Ecommerce Scanavini Perú en Woocommerce.
 * Version: 1.0
 * Author: Gabriel Castillo
 * License: MIT
 */

// Asegurarse de que WordPress está cargado
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Registrar el shortcode
function fcw_shortcode_filtro_categorias() {
  // Obtener todas las categorías de productos
  $categorias = get_terms(array(
      'taxonomy' => 'product_cat',
      'hide_empty' => true,
  ));

  // Si no hay categorías, salir
  if (empty($categorias)) {
      return 'No se encontraron categorías.';
  }

  // Generar el formulario de filtro
  $output = '<form id="fcw-filtro-categorias" action="" method="get">';
  $output .= '<select name="fcw_categoria" id="fcw-categoria">';
  $output .= '<option value="">Selecciona una categoría</option>';

  foreach ($categorias as $categoria) {
      $url_categoria = get_term_link($categoria);
      $output .= '<option value="' . esc_url($url_categoria) . '">' . esc_html($categoria->name) . '</option>';
  }

  $output .= '</select>';
  $output .= '<button type="submit">Filtrar</button>';
  $output .= '</form>';

  // Script para redireccionar al seleccionar una categoría
  $output .= '<script>
      document.getElementById("fcw-categoria").addEventListener("change", function() {
          if (this.value) {
              window.location.href = this.value;
          }
      });
  </script>';

  return $output;
}
add_shortcode('filtro_categorias', 'fcw_shortcode_filtro_categorias');




