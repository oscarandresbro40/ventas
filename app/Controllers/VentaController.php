<?php

declare(strict_types=1);

final class VentaController
{
    public function __construct(private PDO $conexion)
    {
    }

    public function index(): void
    {
        $notificacion = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $notificacion = $this->procesarVenta();
        }

        $productos = Producto::obtenerTodos($this->conexion);
        $historial = Producto::obtenerHistorial($this->conexion);

        require dirname(__DIR__) . '/Views/ventas/index.php';
    }

    private function procesarVenta(): array
    {
        $codigo = strtoupper(trim((string) ($_POST['codigo'] ?? '')));
        $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);

        if ($codigo === '' || $cantidad === false || $cantidad < 1) {
            return [
                'exito' => false,
                'mensaje' => 'Ingrese un código y una cantidad válida.',
            ];
        }

        $producto = Producto::buscarPorCodigo($this->conexion, $codigo);

        if ($producto === null) {
            return [
                'exito' => false,
                'mensaje' => 'No existe un producto con ese código.',
            ];
        }

        return $producto->realizarVenta((int) $cantidad);
    }
}

