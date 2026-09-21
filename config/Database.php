<?php

declare(strict_types=1);

final class Database
{
    private static ?PDO $conexion = null;

    public static function conectar(): PDO
    {
        if (self::$conexion !== null) {
            return self::$conexion;
        }

        $directorio = dirname(__DIR__) . '/data';

        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        self::$conexion = new PDO('sqlite:' . $directorio . '/ventas.sqlite');
        self::$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        self::crearEstructura(self::$conexion);

        return self::$conexion;
    }

    private static function crearEstructura(PDO $conexion): void
    {
        $conexion->exec(
            'CREATE TABLE IF NOT EXISTS productos (
                codigo TEXT PRIMARY KEY,
                nombre TEXT NOT NULL,
                precio REAL NOT NULL CHECK (precio >= 0),
                stock INTEGER NOT NULL CHECK (stock >= 0)
            )'
        );

        $conexion->exec(
            'CREATE TABLE IF NOT EXISTS intentos_venta (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                codigo TEXT NOT NULL,
                producto TEXT NOT NULL,
                cantidad INTEGER NOT NULL,
                resultado TEXT NOT NULL,
                stock_anterior INTEGER NOT NULL,
                stock_final INTEGER NOT NULL,
                creado_en TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )'
        );

        $cantidad = (int) $conexion
            ->query('SELECT COUNT(*) FROM productos')
            ->fetchColumn();

        if ($cantidad === 0) {
            $sentencia = $conexion->prepare(
                'INSERT INTO productos (codigo, nombre, precio, stock)
                 VALUES (:codigo, :nombre, :precio, :stock)'
            );

            $productos = [
                ['P001', 'Teclado mecánico', 45.90, 10],
                ['P002', 'Mouse inalámbrico', 22.50, 4],
            ];

            foreach ($productos as [$codigo, $nombre, $precio, $stock]) {
                $sentencia->execute([
                    ':codigo' => $codigo,
                    ':nombre' => $nombre,
                    ':precio' => $precio,
                    ':stock' => $stock,
                ]);
            }
        }
    }
}

