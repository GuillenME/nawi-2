# Guía para Testear la Descarga de APK

## 📋 Requisitos Previos

1. Asegúrate de que Laragon esté corriendo
2. Verifica que tu aplicación esté accesible (normalmente en `http://nawi-2.test` o `http://localhost:8000`)

## 🧪 Métodos de Prueba

### 1. Prueba Manual en el Navegador

#### Paso 1: Crear un archivo APK de prueba
```bash
# Crea un archivo de prueba (puedes usar cualquier archivo temporal)
echo "APK de prueba" > public/apk/app.apk
```

O simplemente copia cualquier archivo APK real a:
```
public/apk/app.apk
```

#### Paso 2: Probar las rutas directamente

**Ruta principal de descarga:**
- Abre en tu navegador: `http://nawi-2.test/download/apk`
- O: `http://localhost:8000/download/apk`
- Deberías ver que se descarga el archivo

**Ruta de información:**
- Abre: `http://nawi-2.test/api/apk/info`
- Deberías ver un JSON con información del APK

**Ruta con nombre personalizado:**
- Si tienes un archivo llamado `mi-app.apk` en `public/apk/`
- Abre: `http://nawi-2.test/download/apk/mi-app.apk`

#### Paso 3: Probar desde las páginas web

1. **Página de Login:**
   - Ve a: `http://nawi-2.test/login`
   - Busca el botón "Descargar App Android"
   - Haz clic y verifica que descarga

2. **Página de Inicio:**
   - Ve a: `http://nawi-2.test/`
   - Busca el botón "Descargar App" en la sección de botones principales
   - Haz clic y verifica que descarga

3. **Menú de Navegación:**
   - En cualquier página (sin estar autenticado)
   - Busca el enlace "Descargar App" en el menú superior
   - Haz clic y verifica que descarga

### 2. Prueba con cURL (Línea de Comandos)

```bash
# Probar la descarga del APK
curl -I http://nawi-2.test/download/apk

# Probar la información del APK
curl http://nawi-2.test/api/apk/info

# Probar descarga con nombre personalizado
curl -I http://nawi-2.test/download/apk/app.apk
```

### 3. Prueba con PHPUnit (Tests Automatizados)

```bash
# Ejecutar los tests
php artisan test --filter ApkDownloadTest

# O ejecutar todos los tests
php artisan test
```

### 4. Prueba desde un Dispositivo Móvil

1. Asegúrate de que tu servidor sea accesible desde tu red local
2. Obtén la IP de tu máquina (ej: `192.168.1.100`)
3. Desde tu dispositivo móvil, abre: `http://192.168.1.100/download/apk`
4. El dispositivo debería detectar que es un APK y ofrecer instalarlo

## ✅ Checklist de Verificación

- [ ] La ruta `/download/apk` funciona
- [ ] La ruta `/api/apk/info` retorna información correcta
- [ ] El botón en la página de login funciona
- [ ] El botón en la página de inicio funciona
- [ ] El enlace en el menú de navegación funciona
- [ ] Los archivos se descargan con el nombre correcto (`nawi-app.apk`)
- [ ] El Content-Type es correcto (`application/vnd.android.package-archive`)
- [ ] Se muestra error 404 cuando el archivo no existe
- [ ] Se rechazan archivos sin extensión `.apk`

## 🐛 Solución de Problemas

### Error 404: APK no encontrado
- Verifica que el archivo existe en `public/apk/app.apk`
- Verifica los permisos del archivo
- Asegúrate de que Laragon esté corriendo

### Error 500: Error del servidor
- Revisa los logs en `storage/logs/laravel.log`
- Verifica que el controlador `ApkController` existe
- Verifica que las rutas estén registradas: `php artisan route:list --name=download.apk`

### El archivo no se descarga
- Verifica que el Content-Type sea correcto
- Prueba en modo incógnito del navegador
- Verifica que no haya bloqueadores de descarga activos

## 📱 Prueba en Dispositivo Android Real

1. Comparte la URL de descarga (ej: `http://tu-servidor.com/download/apk`)
2. Abre la URL en el navegador del dispositivo
3. El sistema Android debería detectar automáticamente que es un APK
4. Permite la instalación desde "Fuentes desconocidas" si es necesario
5. Instala la aplicación

