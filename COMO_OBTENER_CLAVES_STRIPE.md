# 🔑 Cómo Obtener las Claves de Stripe

## 📋 Paso a Paso

### 1. Crear una Cuenta en Stripe (si no tienes una)

1. Ve a: https://stripe.com/
2. Haz clic en **"Start now"** o **"Sign up"**
3. Completa el registro con:
   - Email
   - Contraseña
   - Información de tu negocio

### 2. Acceder al Dashboard de Stripe

1. Inicia sesión en: https://dashboard.stripe.com/
2. Si es tu primera vez, Stripe te pedirá completar información de tu cuenta

### 3. Obtener las Claves de API

#### Paso 1: Ir a la sección de API Keys

1. En el menú lateral izquierdo, busca **"Developers"** (Desarrolladores)
2. Haz clic en **"Developers"**
3. En el submenú, haz clic en **"API keys"** (Claves de API)

#### Paso 2: Ver las Claves

Verás dos secciones:

**a) Publishable key (Clave Pública)**
- Esta es la que va en `STRIPE_KEY`
- Comienza con `pk_test_` (modo prueba) o `pk_live_` (producción)
- Es segura de compartir (se usa en el frontend)

**b) Secret key (Clave Secreta)**
- Esta es la que va en `STRIPE_SECRET`
- Comienza con `sk_test_` (modo prueba) o `sk_live_` (producción)
- ⚠️ **NUNCA la compartas** - define qué cuenta recibe el dinero
- Haz clic en **"Reveal test key"** o **"Reveal live key"** para verla

### 4. Modo de Prueba vs Producción

#### 🔵 Modo de Prueba (Testing) - Recomendado para empezar

- Las claves comienzan con `pk_test_` y `sk_test_`
- Los pagos son simulados (no se cobra dinero real)
- Perfecto para desarrollo y pruebas
- Puedes usar tarjetas de prueba como: `4242 4242 4242 4242`

#### 🟢 Modo de Producción (Live)

- Las claves comienzan con `pk_live_` y `sk_live_`
- Los pagos son REALES y el dinero se deposita en tu cuenta
- Solo úsalo cuando estés listo para recibir pagos reales
- Requiere verificación de identidad y cuenta bancaria

### 5. Obtener el Webhook Secret

#### Paso 1: Crear un Webhook Endpoint

1. En el menú lateral, ve a **"Developers"** → **"Webhooks"**
2. Haz clic en **"Add endpoint"** (Agregar endpoint)
3. Ingresa la URL de tu webhook:
   ```
   https://tu-dominio.com/api/stripe/webhook
   ```
   O para desarrollo local con Stripe CLI:
   ```
   https://tu-dominio.ngrok.io/api/stripe/webhook
   ```

#### Paso 2: Seleccionar Eventos

Selecciona estos eventos:
- ✅ `payment_intent.succeeded`
- ✅ `payment_intent.payment_failed`

#### Paso 3: Copiar el Signing Secret

1. Después de crear el endpoint, haz clic en él
2. En la sección **"Signing secret"**, haz clic en **"Reveal"**
3. Copia el secreto que comienza con `whsec_`
4. Esta es la que va en `STRIPE_WEBHOOK_SECRET`

### 6. Configurar en tu Proyecto

Una vez que tengas las claves, agrégalas a tu archivo `.env`:

```env
# Stripe Configuration - Modo de Prueba
# IMPORTANTE: Reemplaza con tus claves reales obtenidas del Dashboard de Stripe
STRIPE_KEY=pk_test_TU_CLAVE_PUBLICA_AQUI
STRIPE_SECRET=sk_test_TU_CLAVE_SECRETA_AQUI
STRIPE_WEBHOOK_SECRET=whsec_TU_WEBHOOK_SECRET_AQUI
```

**Ejemplo de formato (NO uses estos valores, son solo ejemplos):**
```env
# Reemplaza con tus claves reales de Stripe Dashboard
STRIPE_KEY=pk_test_TU_CLAVE_PUBLICA_DE_STRIPE_DASHBOARD
STRIPE_SECRET=sk_test_TU_CLAVE_SECRETA_DE_STRIPE_DASHBOARD
STRIPE_WEBHOOK_SECRET=whsec_TU_WEBHOOK_SECRET_DE_STRIPE_DASHBOARD
```

### 7. Verificar que Funciona

1. Guarda el archivo `.env`
2. Reinicia tu servidor Laravel si está corriendo
3. Prueba crear una suscripción desde tu aplicación
4. Revisa los logs en el Dashboard de Stripe → **"Developers"** → **"Logs"**

## 📸 Ubicación Visual en el Dashboard

```
Stripe Dashboard
├── Developers (Menú lateral)
│   ├── API keys ← Aquí están STRIPE_KEY y STRIPE_SECRET
│   ├── Webhooks ← Aquí está STRIPE_WEBHOOK_SECRET
│   └── Logs ← Para ver errores y eventos
```

## ⚠️ Importante

1. **Nunca compartas** tu `STRIPE_SECRET` (clave secreta)
2. **No subas** el archivo `.env` a repositorios públicos
3. Usa **modo de prueba** (`test`) durante desarrollo
4. Cambia a **modo de producción** (`live`) solo cuando estés listo
5. Las claves de prueba y producción son **diferentes**

## 🆘 Problemas Comunes

### "No puedo ver mi Secret Key"
- Haz clic en el botón **"Reveal test key"** o **"Reveal live key"**
- Asegúrate de estar en la pestaña correcta (Test mode / Live mode)

### "No encuentro la sección Developers"
- Verifica que hayas completado el registro inicial
- Intenta refrescar la página o cerrar sesión y volver a entrar

### "El webhook no funciona"
- Verifica que la URL sea accesible públicamente (no localhost)
- Usa ngrok o similar para desarrollo local
- Verifica que el `STRIPE_WEBHOOK_SECRET` sea correcto

## 📚 Recursos Adicionales

- Documentación oficial: https://stripe.com/docs/api
- Dashboard de Stripe: https://dashboard.stripe.com/
- Tarjetas de prueba: https://stripe.com/docs/testing

