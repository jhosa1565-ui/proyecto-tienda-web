<?php
// Incluimos el archivo de conexión
require_once 'config/conexion.php';

// Consulta SQL uniendo las tablas de productos, categorías y marcas
$sql = "SELECT 
            p.id,
            p.codigo_barras,
            p.nombre AS producto,
            p.descripcion,
            p.precio_compra,
            p.precio_venta,
            p.stock,
            p.stock_minimo,
            p.fecha_elab,
            p.paga_iva,
            p.imagen,
            p.estado,
            c.nombre AS categoria,
            m.nombre AS marca
        FROM productos p
        INNER JOIN categorias c ON p.categoria_id = c.id
        INNER JOIN marcas m ON p.marca_id = m.id
        ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$productos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Mi Tienda</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #ffffff;
        }
        .product-img {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .table hover tbody tr:hover {
            background-color: #f1f5f9;
        }
    </style>
</head>
<body>

    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="#"><i class="fa-solid fa-store me-2"></i>Sistema Tienda</a>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container-fluid px-4 my-4">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="fw-bold mb-0 text-dark">Catálogo de Productos</h3>
                <p class="text-muted small mb-0">Gestión e inventario general de la tienda</p>
            </div>
            <button class="btn btn-primary shadow-sm"><i class="fa-solid fa-plus me-1"></i> Nuevo Producto</button>
        </div>

        <!-- Tarjeta del DataTable -->
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header card-header-custom py-3 rounded-top">
                <h5 class="card-title mb-0 fs-6"><i class="fa-solid fa-boxes-stacked me-2"></i>Listado Registrar de Productos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaProductos" class="table table-hover align-middle w-100" style="font-size: 0.9rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Imagen</th>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Marca</th>
                                <th class="text-end">P. Compra</th>
                                <th class="text-end">P. Venta</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">IVA</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $row): ?>
                                <tr>
                                    <!-- Imagen -->
                                    <td class="text-center">
                                        <?php 
                                            $ruta_imagen = "img/" . $row['imagen'];
                                            if (!file_exists($ruta_imagen) || empty($row['imagen'])) {
                                                $ruta_imagen = "img/default.png";
                                            }
                                        ?>
                                        <img src="<?= htmlspecialchars($ruta_imagen); ?>" alt="Producto" class="product-img shadow-sm">
                                    </td>

                                    <!-- Código y Nombre -->
                                    <td class="fw-bold text-secondary"><?= htmlspecialchars($row['codigo_barras']); ?></td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($row['producto']); ?></div>
                                        <small class="text-muted d-block text-truncate" style="max-width: 180px;"><?= htmlspecialchars($row['descripcion']); ?></small>
                                    </td>

                                    <!-- Categoría y Marca -->
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['categoria']); ?></span></td>
                                    <td><span class="badge bg-secondary text-white"><?= htmlspecialchars($row['marca']); ?></span></td>

                                    <!-- Precios -->
                                    <td class="text-end">$<?= number_format($row['precio_compra'], 2); ?></td>
                                    <td class="text-end fw-bold text-success">$<?= number_format($row['precio_venta'], 2); ?></td>

                                    <!-- Stock con alerta visual si está en stock mínimo -->
                                    <td class="text-center">
                                        <?php if ($row['stock'] <= $row['stock_minimo']): ?>
                                            <span class="badge bg-danger" title="Stock por debajo del mínimo (<?= $row['stock_minimo'] ?>)">
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i><?= $row['stock']; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-info text-dark"><?= $row['stock']; ?></span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Paga IVA -->
                                    <td class="text-center">
                                        <?php if ($row['paga_iva'] == 1): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-solid fa-check me-1"></i>15%</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">0%</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Estado -->
                                    <td class="text-center">
                                        <?php 
                                            switch($row['estado']) {
                                                case 'activo':
                                                    echo '<span class="badge bg-success">Activo</span>';
                                                    break;
                                                case 'inagotable':
                                                    echo '<span class="badge bg-primary">Inagotable</span>';
                                                    break;
                                                case 'suspendido':
                                                    echo '<span class="badge bg-warning text-dark">Suspendido</span>';
                                                    break;
                                            }
                                        ?>
                                    </td>

                                    <!-- Acciones -->
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" title="Editar"><i class="fa-solid fa-pen-to-square"></i></button>
                                            <button type="button" class="btn btn-outline-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- JavaScripts: jQuery, Bootstrap y DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <!-- Inicialización de DataTable en español -->
    <script>
        $(document).ready(function() {
            $('#tablaProductos').DataTable({
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [[1, 'asc']] // Ordenar por código de barras por defecto
            });
        });
    </script>
</body>
</html>