# 📡 Respuestas de la API - Qué envía cada endpoint

## 🚗 Endpoints de Viajes

### 1. GET /api/taxista/viajes-disponibles

**Descripción:** Obtiene la lista de viajes disponibles para el taxista autenticado

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": "uuid-del-viaje",                    // ✅ SIEMPRE presente, nunca null
      "pasajero_id": "uuid-del-pasajero",
      "pasajero": {
        "nombre": "Juan",
        "apellido": "Pérez",
        "email": "juan@example.com"
      } | null,
      "taxista": {
        "id": "uuid-del-taxista",
        "nombre": "Carlos",
        "apellido": "González",
        "numero_taxi": "TAX-123",
        "taxi": {
          "id": "uuid-del-taxi",
          "marca": "Toyota",
          "modelo": "Corolla",
          "numero_taxi": "TAX-123"
        }
      } | null,
      "latitud_origen": 16.867,
      "longitud_origen": -92.094,
      "direccion_origen": "Calle Principal 123",
      "latitud_destino": 16.900,
      "longitud_destino": -92.100,
      "direccion_destino": "Avenida Central 456",
      "estado": "solicitado",
      "fecha_creacion": "2024-01-15T10:30:00Z",
      "tiempo_limite_aceptacion": "2024-01-15T10:35:00Z" | null,
      "tarifa": 150.50 | null
    }
  ]
}
```

**Características importantes:**
- ✅ El campo `id` SIEMPRE está presente y nunca es null
- ✅ Solo se incluyen viajes con estado "solicitado"
- ✅ Solo se incluyen viajes que no han expirado
- ✅ Solo se incluyen viajes asignados al taxista o disponibles para todos

---

### 2. POST /api/taxista/aceptar-viaje/{viajeId}

**Descripción:** Acepta un viaje solicitado

**Parámetros:**
- `viajeId` (en URL): UUID del viaje
- `tarifa` (opcional en body): Número entre 0 y 9999.99

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "message": "Viaje aceptado exitosamente",
  "data": {
    "id": "uuid-del-viaje",                      // ✅ SIEMPRE presente
    "pasajero_id": "uuid-del-pasajero",
    "taxista_id": "uuid-del-taxista",
    "latitud_origen": 16.867,
    "longitud_origen": -92.094,
    "direccion_origen": "Calle Principal 123",
    "latitud_destino": 16.900,
    "longitud_destino": -92.100,
    "direccion_destino": "Avenida Central 456",
    "estado": "aceptado",
    "fecha_creacion": "2024-01-15T10:30:00Z",
    "fecha_aceptacion": "2024-01-15T10:35:00Z",
    "fecha_completado": null,
    "tiempo_limite_aceptacion": "2024-01-15T10:35:00Z" | null,
    "tarifa": 150.50 | null,
    "calificacion": 5.0 | null,
    "comentario": "Excelente servicio" | null,
    "pasajero": {
      "id": "uuid-del-usuario",
      "nombre": "Juan",
      "apellido": "Pérez",
      "email": "juan@example.com",
      "id_rol": "uuid-del-rol",
      "telefono": "1234567890" | null,
      "foto": "https://..." | null,
      "tipo": "pasajero"
    } | null,
    "taxista": {
      "id": "uuid-del-usuario",
      "nombre": "Carlos",
      "apellido": "González",
      "email": "carlos@example.com",
      "id_rol": "uuid-del-rol",
      "telefono": "9876543210" | null,
      "foto": "https://..." | null,
      "tipo": "taxista"
    } | null
  }
}
```

**Errores posibles:**
- `400`: ID del viaje no válido
- `403`: Usuario no es un taxista
- `404`: Viaje no encontrado
- `422`: Viaje no disponible (ya aceptado, cancelado, etc.)

---

### 3. POST /api/taxista/rechazar-viaje/{viajeId}

**Descripción:** Rechaza un viaje solicitado (solo viajes dirigidos específicamente al taxista)

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "message": "Viaje rechazado exitosamente"
}
```

**Errores posibles:**
- `403`: Usuario no es un taxista
- `404`: Viaje no encontrado
- `422`: Viaje no disponible o no dirigido al taxista

---

### 4. GET /api/taxista/mis-viajes

**Descripción:** Obtiene todos los viajes del taxista autenticado

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": "uuid-del-viaje",
      "pasajero_id": "uuid-del-pasajero",
      "taxista_id": "uuid-del-taxista",
      "latitud_origen": 16.867,
      "longitud_origen": -92.094,
      "direccion_origen": "Calle Principal 123",
      "latitud_destino": 16.900,
      "longitud_destino": -92.100,
      "direccion_destino": "Avenida Central 456",
      "estado": "completado",
      "fecha_creacion": "2024-01-15T10:30:00Z",
      "fecha_aceptacion": "2024-01-15T10:35:00Z",
      "fecha_completado": "2024-01-15T11:00:00Z",
      "tiempo_limite_aceptacion": "2024-01-15T10:35:00Z" | null,
      "tarifa": 150.50 | null,
      "calificacion": 5.0 | null,
      "comentario": "Excelente servicio" | null,
      "pasajero": {
        "id": "uuid-del-usuario",
        "nombre": "Juan",
        "apellido": "Pérez",
        "email": "juan@example.com",
        "id_rol": "uuid-del-rol",
        "telefono": "1234567890" | null,
        "foto": "https://..." | null,
        "tipo": "pasajero"
      } | null,
      "taxista": {
        "id": "uuid-del-usuario",
        "nombre": "Carlos",
        "apellido": "González",
        "email": "carlos@example.com",
        "id_rol": "uuid-del-rol",
        "telefono": "9876543210" | null,
        "foto": "https://..." | null,
        "tipo": "taxista"
      } | null
    }
  ]
}
```

---

### 5. POST /api/pasajero/crear-viaje

**Descripción:** Crea un nuevo viaje (con o sin taxista específico)

**Request Body:**
```json
{
  "salida": {
    "lat": 16.867,
    "lon": -92.094
  },
  "destino": {
    "lat": 16.900,
    "lon": -92.100
  },
  "id_taxista": "uuid-del-taxista" | null,  // Opcional
  "tiempo_limite_minutos": 5                // Opcional (1-30, por defecto 5)
}
```

**Respuesta exitosa (201):**
```json
{
  "success": true,
  "message": "Viaje creado exitosamente",
  "data": {
    "id": "uuid-del-viaje",                 // ✅ SIEMPRE presente (corregido)
    "id_pasajero": "uuid-del-pasajero",
    "id_taxista": "uuid-del-taxista" | null,
    "id_taxi": "uuid-del-taxi" | null,
    "latitud_origen": 16.867,
    "longitud_origen": -92.094,
    "direccion_origen": "Origen",
    "latitud_destino": 16.900,
    "longitud_destino": -92.100,
    "direccion_destino": "Destino",
    "estado": "solicitado",
    "fecha_aceptacion": null,
    "fecha_completado": null,
    "tiempo_limite_aceptacion": "2024-01-15T10:35:00Z",
    "tarifa": null,
    "created_at": "2024-01-15T10:30:00Z",
    "updated_at": "2024-01-15T10:30:00Z",
    "pasajero": {
      "id": "uuid-del-pasajero",
      "nombre": "Juan",
      "apellido": "Pérez",
      "email": "juan@example.com"
    },
    "taxista": {
      "id": "uuid-del-taxista",
      "nombre": "Carlos",
      "apellido": "González",
      "email": "carlos@example.com",
      "numero_taxi": "TAX-123",
      "taxi": {
        "id": "uuid-del-taxi",
        "marca": "Toyota",
        "modelo": "Corolla",
        "numero_taxi": "TAX-123"
      }
    } | null
  }
}
```

---

### 6. GET /api/viaje/estado/{viajeId}

**Descripción:** Obtiene el estado actual de un viaje

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "data": {
    "id": "uuid-del-viaje",
    "estado": "aceptado",
    "id_pasajero": "uuid-del-pasajero",
    "pasajero": {
      "nombre": "Juan",
      "apellido": "Pérez",
      "email": "juan@example.com"
    } | null,
    "id_taxista": "uuid-del-taxista" | null,
    "taxista": {
      "id": "uuid-del-taxista",
      "nombre": "Carlos",
      "apellido": "González",
      "numero_taxi": "TAX-123",
      "taxi": {
        "id": "uuid-del-taxi",
        "marca": "Toyota",
        "modelo": "Corolla",
        "numero_taxi": "TAX-123"
      }
    } | null,
    "salida": {
      "lat": 16.867,
      "lon": -92.094
    },
    "destino": {
      "lat": 16.900,
      "lon": -92.100
    },
    "timestamp": 1705315800000
  }
}
```

---

### 7. POST /api/viaje/actualizar-ubicacion/{viajeId}

**Descripción:** Actualiza la ubicación del taxista durante un viaje

**Request Body:**
```json
{
  "lat": 16.875,
  "lon": -92.095
}
```

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "message": "Ubicación actualizada exitosamente"
}
```

---

## 📋 Resumen de Campos Importantes

### Campo `id` del viaje:
- ✅ **SIEMPRE presente** en todas las respuestas que incluyen viajes
- ✅ **Nunca es null** (se filtran los viajes sin ID válido)
- ✅ **Nombre exacto:** `"id"` (no `id_viaje` u otro)
- ✅ **Tipo:** String (UUID)

### Estados posibles de un viaje:
- `"solicitado"` - Viaje recién creado, esperando aceptación
- `"aceptado"` - Viaje aceptado por un taxista
- `"en_progreso"` - Viaje en curso
- `"completado"` - Viaje finalizado
- `"cancelado"` - Viaje cancelado
- `"rechazado"` - Viaje rechazado por el taxista

### Validaciones implementadas:
1. ✅ Filtro en base de datos: `whereNotNull('id')`
2. ✅ Filtro en colección: Solo viajes con ID válido
3. ✅ Validación en aceptar viaje: Mensaje claro si ID no válido
4. ✅ Re-indexación del array: `->values()` para índices secuenciales

---

## 🔒 Autenticación

Todos los endpoints requieren autenticación mediante Bearer Token:
```
Authorization: Bearer {token}
```

---

## ⚠️ Notas Importantes

1. **Campo `id`:** Después de las correcciones, el campo `id` SIEMPRE está presente y nunca es null en:
   - `/api/taxista/viajes-disponibles`
   - `/api/pasajero/crear-viaje`
   - `/api/taxista/aceptar-viaje/{viajeId}`

2. **Filtrado automático:** Los viajes sin ID válido se filtran automáticamente antes de enviarse.

3. **Mensajes de error:** Si se intenta aceptar un viaje sin ID válido, se recibe un mensaje claro:
   ```json
   {
     "success": false,
     "message": "ID del viaje no válido. El viaje no tiene un ID válido."
   }
   ```

