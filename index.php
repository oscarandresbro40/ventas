<?php

declare(strict_types=1);

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/app/Models/Producto.php';
require_once __DIR__ . '/app/Controllers/VentaController.php';

$conexion = Database::conectar();
$controlador = new VentaController($conexion);
$controlador->index();

