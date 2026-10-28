<?php

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* ==========================================================
   LUNARMC - AGREGAR PRODUCTO AL CARRITO
   POST + VALIDACIÓN + PROTECCIÓN CSRF
   ========================================================== */


/* ==========================================================
   RESPUESTA JSON
   ========================================================== */

function responderJson(
    array $datos,
    int $estado = 200
): void {

    http_response_code($estado);

    header(
        'Content-Type: application/json; charset=utf-8'
    );

    echo json_encode(
        $datos,
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
    );

    exit;

}


/* ==========================================================
   DETECTAR PETICIÓN AJAX
   ========================================================== */

$es_ajax =
    (
        isset($_POST['ajax']) &&
        $_POST['ajax'] === '1'
    ) ||
    (
        isset(
            $_SERVER['HTTP_X_REQUESTED_WITH']
        ) &&
        strtolower(
            (string)
            $_SERVER['HTTP_X_REQUESTED_WITH']
        ) === 'xmlhttprequest'
    );


/* ==========================================================
   SOLO SE PERMITE POST
   ========================================================== */

if (
    !isset($_SERVER['REQUEST_METHOD']) ||
    $_SERVER['REQUEST_METHOD'] !== 'POST'
) {

    if ($es_ajax) {

        responderJson(
            [
                'ok' => false,
                'mensaje' =>
                    'Método no permitido.'
            ],
            405
        );

    }


    http_response_code(405);

    header(
        'Allow: POST'
    );

    exit(
        'Método no permitido.'
    );

}


/* ==========================================================
   VALIDAR TOKEN CSRF
   ========================================================== */

$csrf_recibido =
    isset($_POST['csrf_token'])
        ? (string)
            $_POST['csrf_token']
        : '';


$csrf_sesion =
    isset($_SESSION['csrf_token'])
        ? (string)
            $_SESSION['csrf_token']
        : '';


if (
    $csrf_recibido === '' ||
    $csrf_sesion === '' ||
    !hash_equals(
        $csrf_sesion,
        $csrf_recibido
    )
) {

    if ($es_ajax) {

        responderJson(
            [
                'ok' => false,

                'mensaje' =>
                    'La solicitud de seguridad no es válida. Recargá la página e intentá otra vez.'
            ],
            403
        );

    }


    http_response_code(403);

    exit(
        'Solicitud no válida.'
    );

}


/* Imágenes del catálogo visual: los precios y nombres vienen de MySQL. */
$imagenes = [
 'sun'=>'sun rango.png', 'venus'=>'rango venus.png',
 'venus_plus'=>'rango venus +.png', 'nova'=>'rango nova.png',
 'lunar'=>'rango lunar.png', 'summer'=>'rango summer.png',
 'tags'=>'imagen de tags.png', 'kill_effects'=>'imagen de kill efects.png',
 'llave_todo_o_nada'=>'llave todo o nada.png'
];

/* ==========================================================
   PRODUCTO RECIBIDO POR POST
   ========================================================== */

$id =
    isset($_POST['producto'])
        ? trim(
            (string)
            $_POST['producto']
        )
        : '';


/* ==========================================================
   VALIDAR FORMATO DEL ID
   ========================================================== */

$estructura_valida =
    $id !== '' &&
    preg_match(
        '/^[a-z0-9_]+$/D',
        $id
    ) === 1;


/* ==========================================================
   VALIDAR CONTRA PRODUCTOS REALES
   ========================================================== */

if (
    !$estructura_valida ||
    !isset($imagenes[$id])
) {

    if ($es_ajax) {

        responderJson(
            [
                'ok' =>
                    false,

                'mensaje' =>
                    'Producto no válido.'
            ],
            400
        );

    }


    http_response_code(400);

    exit(
        'Producto no válido.'
    );

}


/* Precio, nombre y estado se obtienen de la base de datos mediante PDO. */
try {
    require_once __DIR__ . '/includes/database.php';
    $stmt = $pdo->prepare('SELECT nombre, precio FROM productos WHERE codigo = :codigo AND activo = 1 LIMIT 1');
    $stmt->execute([':codigo' => $id]);
    $producto_db = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$producto_db || !is_numeric($producto_db['precio']) || (float)$producto_db['precio'] < 0) {
        if ($es_ajax) responderJson(['ok' => false, 'mensaje' => 'Producto no disponible.'], 404);
        http_response_code(404);
        exit('Producto no disponible.');
    }
} catch (PDOException $e) {
    error_log('LunarMC carrito: ' . $e->getMessage());
    if ($es_ajax) responderJson(['ok' => false, 'mensaje' => 'No se pudo consultar el catálogo.'], 500);
    http_response_code(500);
    exit('No se pudo consultar el catálogo.');
}

/* ==========================================================
   CREAR CARRITO SI NO EXISTE
   ========================================================== */

if (
    !isset($_SESSION['carrito']) ||
    !is_array(
        $_SESSION['carrito']
    )
) {

    $_SESSION['carrito'] = [];

}


/* ==========================================================
   AGREGAR PRODUCTO
   ========================================================== */

if (
    isset(
        $_SESSION['carrito'][$id]
    )
) {
    $_SESSION['carrito'][$id]['nombre'] = $producto_db['nombre'];
    $_SESSION['carrito'][$id]['precio'] = (float)$producto_db['precio'];
    $_SESSION['carrito'][$id]['imagen'] = $imagenes[$id];


    $_SESSION['carrito'][$id]['cantidad'] =

        (int) (
            $_SESSION['carrito'][$id]['cantidad']
            ?? 1
        ) + 1;

} else {

    $_SESSION['carrito'][$id] = [

        'nombre' =>
            $producto_db['nombre'],

        'precio' =>
            (float)$producto_db['precio'],

        'imagen' =>
            $imagenes[$id],

        'cantidad' =>
            1

    ];

}


/* ==========================================================
   PREPARAR DATOS DEL CARRITO
   ========================================================== */

$items = [];

$total = 0;

$cantidad_total = 0;


/* ==========================================================
   RECORRER CARRITO
   ========================================================== */

foreach (
    $_SESSION['carrito']
    as $producto_id => $producto
) {

    $precio =
        (float) (
            $producto['precio']
            ?? 0
        );


    $cantidad =
        (int) (
            $producto['cantidad']
            ?? 1
        );


    if ($cantidad < 1) {
        $cantidad = 1;
    }


    $subtotal =
        $precio *
        $cantidad;


    $total +=
        $subtotal;


    $cantidad_total +=
        $cantidad;


    $items[] = [

        'id' =>
            (string)
            $producto_id,


        'nombre' =>
            (string) (
                $producto['nombre']
                ?? 'Producto'
            ),


        'precio' =>
            $precio,


        'cantidad' =>
            $cantidad,


        'subtotal' =>
            $subtotal,


        'imagen' =>
            (string) (
                $producto['imagen']
                ?? ''
            )

    ];

}


/* ==========================================================
   RESPUESTA PARA EL MINI-CARRITO
   ========================================================== */

if ($es_ajax) {

    responderJson([

        'ok' =>
            true,


        'producto_agregado' =>
            $id,


        'cantidad_total' =>
            $cantidad_total,


        'total' =>
            $total,


        'items' =>
            $items

    ]);

}


/* ==========================================================
   SIN AJAX
   REDIRECCIÓN NORMAL AL CARRITO
   ========================================================== */

header(
    'Location: carrito.php',
    true,
    303
);

exit;