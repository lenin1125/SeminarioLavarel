<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts Externos</title>
    <!-- Bootstrap 5 CSS CDN para diseño rápido -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome para iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container py-5">
        <!-- Encabezado de la página -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold text-dark">Noticias Sincronizadas vía API</h1>
                <p class="text-muted mb-0">Estos datos están siendo consumidos en tiempo real desde un servicio externo REST de pruebas (JSONPlaceholder).</p>
            </div>
            <a href="{{ route('zapatos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver a Zapatos
            </a>
        </div>

        <!-- Alerta en caso de error en la API externa -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tabla de Contenido -->
        <div class="card shadow border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 80px;" class="ps-4">ID</th>
                                <th>Título del Post</th>
                                <th class="pe-4">Contenido del Mensaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($posts as $post)
                            <tr>
                                <td class="ps-4"><span class="badge bg-secondary">{{ $post['id'] }}</span></td>
                                <td class="fw-bold text-primary">{{ ucfirst($post['title']) }}</td>
                                <td class="text-muted pe-4">{{ ucfirst($post['body']) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 d-block text-secondary"></i>
                                    No se encontraron posts disponibles en este momento.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>