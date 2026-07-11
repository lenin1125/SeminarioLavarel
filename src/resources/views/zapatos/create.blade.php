<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Zapato</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow-sm p-4">
            <h2 class="mb-4 text-center">Registrar Nuevo Zapato</h2>

            <!-- Bloque 5: Alertas de errores de validación -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Bloque 5: Formulario con método POST y directiva @csrf -->
            <form action="{{ route('zapatos.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nombre del Calzado</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Precio ($)</label>
                    <input type="number" name="precio" step="0.01" class="form-control" value="{{ old('precio') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Estado de Disponibilidad</label>
                    <select name="activo" class="form-select" required>
                        <option value="1" {{ old('activo') == '1' ? 'selected' : '' }}>Activo (Disponible)</option>
                        <option value="0" {{ old('activo') == '0' ? 'selected' : '' }}>Inactivo (Agotado)</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('zapatos.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-success">Guardar Zapato</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>