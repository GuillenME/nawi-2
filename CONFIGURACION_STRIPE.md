# Configuración de Stripe para Pagos

## 📋 ¿Dónde se define la cuenta que recibe el dinero?

La cuenta de Stripe que recibirá los pagos se define mediante las **credenciales de API (claves)** que configures en tu archivo `.env`. Las claves de Stripe están asociadas directamente a una cuenta de Stripe específica.

## 🔑 Configuración de Credenciales

### 1. Obtener las claves de Stripe

1. Inicia sesión en tu cuenta de Stripe: https://dashboard.stripe.com/
2. Ve a **Developers** → **API keys**
3. Copia las siguientes claves:
   - **Publishable key** (clave pública) → `STRIPE_KEY`
   - **Secret key** (clave secreta) → `STRIPE_SECRET`

### 2. Configurar en el archivo `.env`

Agrega las siguientes variables a tu archivo `.env`:

```env
# Stripe Configuration
# IMPORTANTE: Reemplaza los valores de ejemplo con tus claves reales de Stripe
STRIPE_KEY=pk_test_TU_CLAVE_PUBLICA_AQUI
STRIPE_SECRET=sk_test_TU_CLAVE_SECRETA_AQUI
STRIPE_WEBHOOK_SECRET=whsec_TU_WEBHOOK_SECRET_AQUI
```

> **📖 ¿No sabes dónde obtener estas claves?** 
> 
> Lee el archivo `COMO_OBTENER_CLAVES_STRIPE.md` para una guía paso a paso con capturas y explicaciones detalladas.
> 
> **Resumen rápido:**
> 1. Ve a https://dashboard.stripe.com/
> 2. **Developers** → **API keys** (para STRIPE_KEY y STRIPE_SECRET)
> 3. **Developers** → **Webhooks** → Crear endpoint → Copiar "Signing secret" (para STRIPE_WEBHOOK_SECRET)

### 3. Modo de Prueba vs Producción

#### Modo de Prueba (Testing)
- Las claves comienzan con `pk_test_` y `sk_test_`
- Los pagos son simulados, no se cobra dinero real
- Útil para desarrollo y pruebas

#### Modo de Producción
- Las claves comienzan con `pk_live_` y `sk_live_`
- Los pagos son reales y el dinero se deposita en tu cuenta
- **IMPORTANTE**: Solo usa claves de producción cuando estés listo para recibir pagos reales

## 💰 ¿Cómo funciona?

1. **La cuenta que recibe el dinero** es la cuenta de Stripe asociada a las claves que configuraste
2. Cuando un taxista paga, el dinero se deposita automáticamente en la cuenta de Stripe configurada
3. Desde el Dashboard de Stripe puedes:
   - Ver todos los pagos recibidos
   - Transferir el dinero a tu cuenta bancaria
   - Configurar transferencias automáticas

## 🔐 Seguridad

- **NUNCA** compartas tus claves secretas (`STRIPE_SECRET`)
- **NUNCA** subas el archivo `.env` a repositorios públicos
- Usa claves de prueba durante el desarrollo
- Cambia a claves de producción solo cuando estés listo

## 📝 Configuración de Webhooks

Para recibir notificaciones automáticas cuando se complete un pago:

1. Ve a **Developers** → **Webhooks** en tu Dashboard de Stripe
2. Crea un nuevo endpoint: `https://tu-dominio.com/api/stripe/webhook`
3. Selecciona los eventos:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
4. Copia el **Signing secret** y agrégalo como `STRIPE_WEBHOOK_SECRET` en tu `.env`

## 🏦 Transferencia de Fondos

Para transferir el dinero de Stripe a tu cuenta bancaria:

1. Ve a **Payments** → **Balance** en el Dashboard de Stripe
2. Configura tu cuenta bancaria en **Settings** → **Bank accounts and scheduling**
3. Configura transferencias automáticas o manuales según prefieras

## ⚠️ Importante

- Las claves de **prueba** y **producción** son diferentes
- Cada cuenta de Stripe tiene sus propias claves únicas
- El dinero se deposita en la cuenta asociada a las claves que uses
- Verifica que estés usando las claves correctas antes de ir a producción

