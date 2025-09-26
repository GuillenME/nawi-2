@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">🚕 Panel de Taxista</h3>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-id-card fa-3x text-warning mb-3"></i>
                                    <h5>Matrícula</h5>
                                    <p id="matricula-status" class="text-muted">No subida</p>
                                    <button class="btn btn-warning" onclick="showMatriculaForm()">
                                        <i class="fas fa-upload"></i> Subir Matrícula
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-id-badge fa-3x text-warning mb-3"></i>
                                    <h5>Licencia</h5>
                                    <p id="licencia-status" class="text-muted">No subida</p>
                                    <button class="btn btn-warning" onclick="showLicenciaForm()">
                                        <i class="fas fa-upload"></i> Subir Licencia
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>Información del Taxista</h5>
                        <div id="taxista-info" class="alert alert-info">
                            <i class="fas fa-spinner fa-spin"></i> Cargando información...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para subir matrícula -->
<div class="modal fade" id="matriculaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subir Matrícula</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="matriculaForm">
                    <div class="form-group mb-3">
                        <label for="matricula-url" class="form-label">URL de la imagen *</label>
                        <input type="url" class="form-control" id="matricula-url" name="url" required
                               placeholder="https://example.com/mi-matricula.jpg">
                        <small class="form-text text-muted">Sube tu imagen a un servicio como Imgur y pega la URL aquí</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="uploadMatricula()">Subir Matrícula</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para subir licencia -->
<div class="modal fade" id="licenciaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Subir Licencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="licenciaForm">
                    <div class="form-group mb-3">
                        <label for="licencia-url" class="form-label">URL de la imagen *</label>
                        <input type="url" class="form-control" id="licencia-url" name="url" required
                               placeholder="https://example.com/mi-licencia.jpg">
                        <small class="form-text text-muted">Sube tu imagen a un servicio como Imgur y pega la URL aquí</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="uploadLicencia()">Subir Licencia</button>
            </div>
        </div>
    </div>
</div>

<script>
// Cargar información del taxista al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    loadTaxistaInfo();
    loadDocuments();
});

function loadTaxistaInfo() {
    const token = localStorage.getItem('access_token');

    fetch('/api/taxista/me', {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const taxista = data.data;
            document.getElementById('taxista-info').innerHTML = `
                <strong>Nombre:</strong> ${taxista.usuario.nombre} ${taxista.usuario.apellido}<br>
                <strong>Email:</strong> ${taxista.usuario.email}<br>
                <strong>Teléfono:</strong> ${taxista.usuario.telefono}<br>
                <strong>Rol:</strong> ${taxista.usuario.rol.nombre}
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('taxista-info').innerHTML = 'Error al cargar información';
    });
}

function loadDocuments() {
    const token = localStorage.getItem('access_token');

    fetch('/api/taxista/documents', {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const documents = data.data;

            // Actualizar estado de matrícula
            if (documents.matricula) {
                document.getElementById('matricula-status').innerHTML =
                    `<span class="badge bg-warning">${documents.matricula.estatus.nombre}</span>`;
            }

            // Actualizar estado de licencia
            if (documents.licencia) {
                document.getElementById('licencia-status').innerHTML =
                    `<span class="badge bg-warning">${documents.licencia.estatus.nombre}</span>`;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function showMatriculaForm() {
    const modal = new bootstrap.Modal(document.getElementById('matriculaModal'));
    modal.show();
}

function showLicenciaForm() {
    const modal = new bootstrap.Modal(document.getElementById('licenciaModal'));
    modal.show();
}

function uploadMatricula() {
    const url = document.getElementById('matricula-url').value;
    const token = localStorage.getItem('access_token');

    fetch('/api/taxista/upload-matricula', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ url: url })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Matrícula subida exitosamente');
            bootstrap.Modal.getInstance(document.getElementById('matriculaModal')).hide();
            loadDocuments();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error al subir matrícula: ' + error.message);
    });
}

function uploadLicencia() {
    const url = document.getElementById('licencia-url').value;
    const token = localStorage.getItem('access_token');

    fetch('/api/taxista/upload-licencia', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ url: url })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Licencia subida exitosamente');
            bootstrap.Modal.getInstance(document.getElementById('licenciaModal')).hide();
            loadDocuments();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error al subir licencia: ' + error.message);
    });
}
</script>
@endsection
