<?php

$CATEGORIAS = array(
    'cabello' => array(
        'nombre' => 'Cabello',
        'descripcion' => 'Productos para el cuidado del cabello',
        'icono' => '💇'
    ),
    'unas' => array(
        'nombre' => 'Uñas',
        'descripcion' => 'Productos para las uñas',
        'icono' => '💅'
    ),
    'facial' => array(
        'nombre' => 'Facial',
        'descripcion' => 'Productos para el cuidado facial',
        'icono' => '✨'
    ),
    'maquillaje' => array(
        'nombre' => 'Maquillaje',
        'descripcion' => 'Productos de maquillaje',
        'icono' => '💄'
    )
);

function obtenerCategorias() {
    global $CATEGORIAS;
    return $CATEGORIAS;
}

function validarCategoria($categoria) {
    global $CATEGORIAS;
    return isset($CATEGORIAS[strtolower($categoria)]);
}

function obtenerNombreCategoria($categoria) {
    global $CATEGORIAS;
    $cat = strtolower($categoria);
    return isset($CATEGORIAS[$cat]) ? $CATEGORIAS[$cat]['nombre'] : '';
}
?>
