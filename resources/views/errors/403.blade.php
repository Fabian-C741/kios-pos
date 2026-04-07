<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado - Kiosco POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fc; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .error-card { border: none; border-radius: 1rem; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); max-width: 500px; width: 100%; text-align: center; overflow: hidden;}
        .error-header { background: #f6c23e; color: white; padding: 2rem; }
        .error-code { font-size: 5rem; font-weight: 800; line-height: 1; margin-bottom: 0; }
        .error-body { padding: 3rem 2rem; background: white; }
    </style>
</head>
<body>
    <div class="card error-card">
        <div class="error-header">
            <i class="bi bi-shield-lock" style="font-size: 3rem; opacity: 0.8;"></i>
            <h1 class="error-code mt-2">403</h1>
        </div>
        <div class="error-body">
            <h4 class="fw-bold text-gray-800 mb-3">Acceso Restringido</h4>
            <p class="text-muted mb-4 pb-2">No tenés los permisos administrativos necesarios para ingresar a esta sección (bóveda, usuarios o configuración).</p>
            <a href="{{ url('/') }}" class="btn btn-warning text-dark btn-lg rounded-pill px-5 shadow-sm fw-bold">
                <i class="bi bi-shield-check me-2"></i> Volver Seguro
            </a>
        </div>
    </div>
</body>
</html>
