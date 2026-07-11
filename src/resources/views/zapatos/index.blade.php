<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Calzado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Módulo de Gestión de Calzado</h1>
            <!-- Botón para ir al formulario de creación -->
            <a href="{{ route('zapatos.create') }}" class="btn btn-success">+ Agregar Nuevo Zapato</a>
        </div>

        <!-- Mensajes de éxito Flash del Sistema -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Bloque 6: Tarjetas de Estadísticas (Agregaciones) -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white text-center p-3 shadow-sm">
                    <h5>Total Zapatos</h5>
                    <h3>{{ $zapatos->count() }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white text-center p-3 shadow-sm">
                    <h5>Zapatos Activos</h5>
                    <h3>{{ $zapatos->where('activo', true)->count() }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-secondary text-white text-center p-3 shadow-sm">
                    <h5>Zapatos Inactivos</h5>
                    <h3>{{ $zapatos->where('activo', false)->count() }}</h3>
                </div>
            </div>
        </div>

        <!-- Filtros de Búsqueda (Bloque 4) -->
        <div class="card p-3 mb-4 shadow-sm">
            <form method="GET" action="{{ route('zapatos.index') }}" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre..." value="{{ request('buscar') }}">
                </div>
                <div class="col-md-4">
                    <select name="estado" class="form-select">
                        <option value="">-- Filtrar por Estado --</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-dark w-100">Aplicar Filtros</button>
                </div>
            </form>
        </div>

        <!-- Tabla de Registros -->
        <div class="card shadow-sm p-3">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Registrado Por</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($zapatos as $zapato)
                    <tr>
                        <td>{{ $zapato->id }}</td>
                        <td><strong>{{ $zapato->nombre }}</strong></td>
                        <td>{{ $zapato->descripcion }}</td>
                        <td>${{ number_format($zapato->precio, 2) }}</td>
                        <td>
                            <span class="badge {{ $zapato->activo ? 'bg-success' : 'bg-danger' }}">
                                {{ $zapato->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>{{ $zapato->user->name ?? 'N/A' }}</td>
                        <!-- Bloque 7: Botones CRUD de interacción en línea -->
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('zapatos.edit', $zapato->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                
                                <form action="{{ route('zapatos.destroy', $zapato->id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este calzado?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>