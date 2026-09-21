<?php

declare(strict_types=1);

final class Producto
{
    public function __construct(
        private PDO $conexion,
        private string $codigo,
        private string $nombre,
        private float $precio,
        private int $stock
    ) {
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getPrecio(): float
    {
        return $this->precio;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function hayStock(int $cantidad): bool
    {
        return $cantidad > 0 && $cantidad <= $this->stock;
    }

    public function realizarVenta(int $cantidad): array
    {
        $stockAnterior = $this->stock;

        if (!$this->hayStock($cantidad)) {
            $this->registrarIntento(
                $cantidad,
                'Stock insuficiente',
                $stockAnterior,
                $stockAnterior
            );

            return [
                'exito' => false,
                'mensaje' => 'Venta rechazada por falta de existencias.',
            ];
        }

        $this->conexion->beginTransaction();

        try {
            $sentencia = $this->conexion->prepare(
                'UPDATE productos
                 SET stock = stock - :cantidad
                 WHERE codigo = :codigo AND stock >= :cantidad'
            );
            $sentencia->execute([
                ':cantidad' => $cantidad,
                ':codigo' => $this->codigo,
            ]);

            if ($sentencia->rowCount() !== 1) {
                $this->conexion->rollBack();

                return [
                    'exito' => false,
                    'mensaje' => 'El stock cambió antes de completar la venta.',
                ];
            }

            $this->stock -= $cantidad;
            $this->registrarIntento(
                $cantidad,
                'Venta realizada',
                $stockAnterior,
                $this->stock
            );
            $this->conexion->commit();

            return [
                'exito' => true,
                'mensaje' => 'Venta realizada correctamente.',
            ];
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }

    public static function buscarPorCodigo(PDO $conexion, string $codigo): ?self
    {
        $sentencia = $conexion->prepare(
            'SELECT codigo, nombre, precio, stock
             FROM productos
             WHERE codigo = :codigo'
        );
        $sentencia->execute([':codigo' => strtoupper(trim($codigo))]);
        $datos = $sentencia->fetch();

        return $datos ? self::desdeFila($conexion, $datos) : null;
    }

    public static function obtenerTodos(PDO $conexion): array
    {
        $filas = $conexion
            ->query('SELECT codigo, nombre, precio, stock FROM productos ORDER BY codigo')
            ->fetchAll();

        return array_map(
            fn (array $fila): self => self::desdeFila($conexion, $fila),
            $filas
        );
    }

    public static function obtenerHistorial(PDO $conexion): array
    {
        return $conexion
            ->query(
                'SELECT codigo, producto, cantidad, resultado,
                        stock_anterior, stock_final, creado_en
                 FROM intentos_venta
                 ORDER BY id DESC
                 LIMIT 5'
            )
            ->fetchAll();
    }

    private static function desdeFila(PDO $conexion, array $fila): self
    {
        return new self(
            $conexion,
            (string) $fila['codigo'],
            (string) $fila['nombre'],
            (float) $fila['precio'],
            (int) $fila['stock']
        );
    }

    private function registrarIntento(
        int $cantidad,
        string $resultado,
        int $stockAnterior,
        int $stockFinal
    ): void {
        $sentencia = $this->conexion->prepare(
            'INSERT INTO intentos_venta
                (codigo, producto, cantidad, resultado, stock_anterior, stock_final)
             VALUES
                (:codigo, :producto, :cantidad, :resultado, :anterior, :final)'
        );
        $sentencia->execute([
            ':codigo' => $this->codigo,
            ':producto' => $this->nombre,
            ':cantidad' => $cantidad,
            ':resultado' => $resultado,
            ':anterior' => $stockAnterior,
            ':final' => $stockFinal,
        ]);
    }
}

