<?php

declare(strict_types=1);

/** @var Producto[] $productos */
/** @var array[] $historial */
/** @var array|null $notificacion */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema básico de ventas</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css">
</head>
<body class="has-background-light">
<section class="hero is-link">
    <div class="hero-body">
        <div class="container">
            <p class="title">Sistema básico de ventas</p>
            <p class="subtitle">Ejemplo de POO aplicado al patrón MVC</p>
        </div>
    </div>
</section>

<main class="section">
    <div class="container">
        <?php if ($notificacion !== null): ?>
            <div class="notification <?= $notificacion['exito'] ? 'is-success' : 'is-danger' ?>">
                <?= htmlspecialchars($notificacion['mensaje']) ?>
            </div>
        <?php endif; ?>

        <div class="columns is-variable is-6">
            <div class="column is-5">
                <div class="box">
                    <h2 class="title is-4">Registrar venta</h2>
                    <form method="post">
                        <div class="field">
                            <label class="label" for="codigo">Código del producto</label>

                            <div class="control">
                                <input
                                    class="input"
                                    id="codigo"
                                    name="codigo"
                                    type="text"
                                    list="lista-productos"
                                    placeholder="Escriba o seleccione un producto"
                                    required
                                >

                                <datalist id="lista-productos">
                                    <?php foreach ($productos as $producto): ?>
                                        <option
                                            value="<?= htmlspecialchars($producto->getCodigo()) ?>"
                                            label="<?= htmlspecialchars($producto->getNombre()) ?>"
                                        >
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                        </div>

                        <div class="field">
                            <label class="label" for="cantidad">Cantidad</label>
                            <div class="control">
                                <input class="input" id="cantidad" name="cantidad"
                                       type="number" min="1" required>
                            </div>
                        </div>

                        <button class="button is-link is-fullwidth" type="submit">
                            Realizar venta
                        </button>
                    </form>
                </div>
            </div>

            <div class="column is-7">
                <div class="box">
                    <h2 class="title is-4">Productos disponibles</h2>
                    <div class="table-container">
                        <table class="table is-fullwidth is-striped is-hoverable">
                            <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th class="has-text-right">Precio</th>
                                <th class="has-text-centered">Stock</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td><?= htmlspecialchars($producto->getCodigo()) ?></td>
                                    <td><?= htmlspecialchars($producto->getNombre()) ?></td>
                                    <td class="has-text-right">
                                        $<?= number_format($producto->getPrecio(), 2) ?>
                                    </td>
                                    <td class="has-text-centered"><?= $producto->getStock() ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="box mt-5">
            <h2 class="title is-4">Últimos intentos de venta</h2>
            <?php if ($historial === []): ?>
                <p class="has-text-grey">Todavía no se han registrado ventas.</p>
            <?php else: ?>
                <div class="table-container">
                    <table class="table is-fullwidth is-striped">
                        <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="has-text-centered">Cantidad</th>
                            <th>Resultado</th>
                            <th class="has-text-centered">Stock anterior</th>
                            <th class="has-text-centered">Stock final</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($historial as $intento): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($intento['producto']) ?>
                                    <span class="tag is-light">
                                        <?= htmlspecialchars($intento['codigo']) ?>
                                    </span>
                                </td>
                                <td class="has-text-centered"><?= (int) $intento['cantidad'] ?></td>
                                <td>
                                    <span class="tag <?= $intento['resultado'] === 'Venta realizada'
                                        ? 'is-success' : 'is-danger' ?>">
                                        <?= htmlspecialchars($intento['resultado']) ?>
                                    </span>
                                </td>
                                <td class="has-text-centered"><?= (int) $intento['stock_anterior'] ?></td>
                                <td class="has-text-centered"><?= (int) $intento['stock_final'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
</body>
</html>

