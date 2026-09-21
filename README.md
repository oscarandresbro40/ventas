# Sistema básico de ventas con POO y MVC

Aplicación académica desarrollada en PHP para registrar ventas y comprobar la disponibilidad de productos. El proyecto aplica programación orientada a objetos y separa sus responsabilidades mediante el patrón Modelo Vista Controlador.

## Enlaces del proyecto

- Aplicación publicada: [https://clients.citricstudio.com/tarea_uees_ventas/](https://clients.citricstudio.com/tarea_uees_ventas/)
- Repositorio: [https://github.com/oscarandresbro40/ventas](https://github.com/oscarandresbro40/ventas)

## Objetivo

El sistema permite seleccionar un producto, indicar la cantidad que se desea comprar y realizar la venta únicamente cuando existe stock suficiente. Si la cantidad solicitada supera las existencias, la operación se rechaza y el stock permanece sin cambios.

## Funcionalidades

- Consulta de los productos disponibles.
- Selección o ingreso del código del producto.
- Registro de una venta válida.
- Validación de cantidades mayores que cero.
- Rechazo de ventas con stock insuficiente.
- Actualización del stock después de una venta válida.
- Historial de los últimos cinco intentos de venta.
- Registro del stock anterior y final de cada operación.
- Mensajes visuales para operaciones exitosas y rechazadas.

## Tecnologías utilizadas

- PHP 8.1 o superior.
- SQLite mediante PDO.
- HTML5.
- Bulma CSS.
- Patrón Modelo Vista Controlador.

## Estructura del proyecto

```text
ventas/
├── app/
│   ├── Controllers/
│   │   └── VentaController.php
│   ├── Models/
│   │   └── Producto.php
│   └── Views/
│       └── ventas/
│           └── index.php
├── config/
│   └── Database.php
├── data/
│   └── ventas.sqlite
├── index.php
└── README.md
```

El archivo `data/ventas.sqlite` se genera automáticamente durante la primera ejecución y no debe almacenarse en el repositorio.

## Organización MVC

### Modelo

La clase `Producto` representa los datos y las reglas de negocio. Sus responsabilidades principales son:

- Mantener encapsulados el código, nombre, precio y stock.
- Consultar si existe una cantidad disponible.
- Realizar una venta válida.
- Evitar que el stock se modifique cuando las unidades no alcanzan.
- Buscar productos y consultar el historial de operaciones.

### Vista

El archivo `app/Views/ventas/index.php` presenta los productos, recibe el código y la cantidad, y muestra el resultado de la operación. La Vista no modifica directamente el stock.

### Controlador

La clase `VentaController` recibe la solicitud del formulario, valida los datos básicos, busca el producto y solicita al Modelo que procese la venta. Después envía la información actualizada a la Vista.

## Reglas de negocio

1. La cantidad solicitada debe ser un número entero mayor que cero.
2. El código ingresado debe corresponder a un producto existente.
3. La venta solo puede realizarse cuando la cantidad solicitada es menor o igual al stock.
4. Una venta válida descuenta las unidades vendidas.
5. Una venta rechazada no modifica las existencias.
6. Cada intento registra el producto, la cantidad, el resultado, el stock anterior y el stock final.

## Base de datos

La aplicación utiliza SQLite y crea las tablas automáticamente.

### Tabla productos

| Campo | Tipo | Descripción |
| --- | --- | --- |
| `codigo` | TEXT | Código único del producto |
| `nombre` | TEXT | Nombre del producto |
| `precio` | REAL | Precio unitario |
| `stock` | INTEGER | Unidades disponibles |

### Tabla intentos venta

| Campo | Tipo | Descripción |
| --- | --- | --- |
| `id` | INTEGER | Identificador autoincremental |
| `codigo` | TEXT | Código del producto |
| `producto` | TEXT | Nombre del producto |
| `cantidad` | INTEGER | Cantidad solicitada |
| `resultado` | TEXT | Venta realizada o stock insuficiente |
| `stock_anterior` | INTEGER | Existencias antes del intento |
| `stock_final` | INTEGER | Existencias después del intento |
| `creado_en` | TEXT | Fecha y hora del registro |

La instalación inicial agrega estos productos de prueba:

| Código | Producto | Precio | Stock inicial |
| --- | --- | ---: | ---: |
| P001 | Teclado mecánico | $45.90 | 10 |
| P002 | Mouse inalámbrico | $22.50 | 4 |

## Requisitos

- PHP 8.1 o superior.
- Extensión `pdo_sqlite` habilitada.
- Servidor web local como Laragon, XAMPP o el servidor integrado de PHP.
- Permisos de escritura para crear `data/ventas.sqlite`.

## Instalación

### Con Git y Laragon

```powershell
cd C:\laragon\www
git clone https://github.com/oscarandresbro40/ventas.git
cd ventas
```

Inicie Apache desde Laragon y abra una de estas direcciones, según su configuración:

- `http://localhost/ventas/`
- `http://ventas.test`

### Con el servidor integrado de PHP

Desde la carpeta del proyecto ejecute:

```powershell
php -S localhost:8000
```

Después abra `http://localhost:8000`.

## Pruebas manuales

### Venta válida

1. Seleccione `P001 - Teclado mecánico`.
2. Ingrese la cantidad `1`.
3. Presione **Realizar venta**.

Resultado esperado:

- Se muestra el mensaje `Venta realizada correctamente`.
- El stock cambia de 10 a 9.
- El intento aparece como `Venta realizada`.

### Venta con stock insuficiente

1. Seleccione `P002 - Mouse inalámbrico`.
2. Ingrese la cantidad `7`.
3. Presione **Realizar venta**.

Resultado esperado:

- Se muestra el mensaje `Venta rechazada por falta de existencias`.
- El stock permanece en 4.
- El intento aparece como `Stock insuficiente`.

## Flujo de una venta

1. El usuario selecciona el producto e ingresa la cantidad en la Vista.
2. La Vista envía los datos mediante una solicitud `POST`.
3. El Controlador valida los datos y localiza el producto.
4. El Modelo comprueba el stock y procesa o rechaza la venta.
5. El Controlador obtiene el resultado.
6. La Vista muestra el mensaje, los productos y el historial actualizado.

## Autor

Oscar Bernal  
Carrera de Ciencias de la Computación  
Universidad Espíritu Santo

Proyecto académico desarrollado en 2026.
