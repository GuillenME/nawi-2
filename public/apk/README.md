# Carpeta para archivos APK

Esta carpeta está destinada a almacenar los archivos APK de la aplicación móvil.

## Instrucciones

1. **Subir el APK**: Coloca tu archivo APK en esta carpeta con el nombre `app.apk`
   - Ejemplo: `public/apk/app.apk`

2. **Descargar el APK**: Los usuarios pueden descargar el APK accediendo a:
   - URL principal: `/download/apk` o `https://tudominio.com/download/apk`
   - URL con nombre personalizado: `/download/apk/nombre-del-archivo.apk`

3. **Información del APK**: Para obtener información sobre el APK (tamaño, fecha de modificación, etc.):
   - Endpoint: `/api/apk/info`

## Notas

- El archivo principal debe llamarse `app.apk` para usar la ruta principal
- Puedes tener múltiples versiones del APK con diferentes nombres
- Los archivos APK deben tener la extensión `.apk`

