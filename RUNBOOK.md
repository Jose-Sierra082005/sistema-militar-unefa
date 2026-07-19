# RUNBOOK — SIAM (Sistema Integrado de Administración Militar)

> **Propósito:** Guía operativa Doc-as-Code para diagnóstico, respuesta a incidentes y recuperación ante desastres.
> **Audiencia:** Operadores (L1), Desarrolladores (L2) y Arquitectos (L3).
> **Versión:** 1.0 — Avance #6 (Seguridad, Observabilidad y Soporte)
> **Mantenimiento:** Actualizar tras cada cambio significativo de arquitectura.

---

## FASE 1 — DIAGNÓSTICO DEL SISTEMA

### 1.1 Verificación de Estado (Health Check)

El sistema expone un endpoint de salud que puede verificarse en cualquier momento sin autenticación:

```bash
# Producción (Render)
curl -s https://sistema-militar-unefa.onrender.com/health | python -m json.tool

# Respuesta esperada (sistema saludable):
# {
#   "status": "ok",
#   "timestamp": "2026-07-19T02:00:00.000000Z",
#   "database": "connected"
# }
```

**Interpretación de respuestas:**

| Estado HTTP | `status` | Acción |
|---|---|---|
| `200 OK` | `"ok"` | ✅ Sistema operativo — no requiere acción |
| `200 OK` | `"degraded"` | ⚠️ Escalar a L2 — posible problema de BD |
| `500 / 503` | (cualquier error) | 🔴 Escalar inmediatamente a L2 |
| Sin respuesta (timeout) | — | 🔴 Render caído — escalar a L3 |

### 1.2 Revisión de Logs en Producción

Los logs se escriben en formato **JSON estructurado** en `storage/logs/siam.json.log`. En Render, acceder a través de:

1. **Dashboard de Render:** `https://dashboard.render.com` → seleccionar el servicio → pestaña **Logs**.
2. **CLI de Render** (si está configurado):
   ```bash
   render logs --service <SERVICE_ID> --tail
   ```

**Anatomía de un log JSON de SIAM:**

```json
{
  "datetime": "2026-07-19T02:00:00.000000+00:00",
  "message": "Intento de acceso a lección bloqueada.",
  "context": {
    "correlation_id": "550e8400-e29b-41d4-a716-446655440000",
    "action": "student.lesson.blocked",
    "lesson_id": 12,
    "user_id": 7,
    "request_method": "GET",
    "request_url": "https://sistema-militar-unefa.onrender.com/student/lesson/12",
    "user_ip": "186.x.x.x"
  },
  "level": "WARNING",
  "level_name": "WARNING",
  "channel": "local"
}
```

### 1.3 Búsqueda de un Incidente por Correlation ID

Cuando un usuario reporta un error con su **Código de Incidente (UUID)**, el equipo de soporte debe:

```bash
# En servidor o en logs descargados de Render:
grep "550e8400-e29b-41d4-a716-446655440000" storage/logs/siam.json.log

# O en formato legible (requiere jq):
cat storage/logs/siam.json.log | jq 'select(.context.correlation_id == "550e8400-e29b-41d4-a716-446655440000")'
```

Esto mostrará **exactamente** qué ocurrió en esa petición: URL, método, usuario, IP y el stack trace interno (que el usuario nunca ve).

### 1.4 Comandos de Diagnóstico Laravel

```bash
# Ver configuración activa (verificar variables de entorno)
php artisan config:show app

# Verificar conexión a la base de datos
php artisan db:show

# Listar todas las rutas registradas
php artisan route:list

# Ejecutar suite completa de pruebas
php artisan test

# Verificar el estado de las migraciones
php artisan migrate:status
```

---

## FASE 2 — PROTOCOLO DE INCIDENTES

### Niveles de Escalada

| Nivel | Actor | Responsabilidad | SLA |
|---|---|---|---|
| **L1** | Operador / Soporte | Diagnóstico inicial, reinicio si aplica | < 15 min |
| **L2** | Desarrollador | Análisis de logs, hotfix, rollback | < 2 horas |
| **L3** | Arquitecto / DBA | Fallo de infraestructura, pérdida de datos | < 8 horas |

---

### L1 — Protocolo del Operador (0-15 min)

**Trigger:** Usuario reporta error o el health check falla.

**Pasos:**

1. **Verificar el Health Check** (ver sección 1.1).
2. **Comprobar el estado del servicio en Render:**
   - `https://dashboard.render.com` → verificar que el servicio está en estado `Live`.
3. **Si el servicio muestra "Failed":**
   - Hacer clic en **"Manual Deploy"** para desplegar la última versión conocida como buena.
4. **Registrar el incidente:**
   - Anotar: hora del inicio, síntoma reportado, Correlation ID si está disponible.
5. **Si el problema persiste tras el re-deploy:** → Escalar a L2.

---

### L2 — Protocolo del Desarrollador (15 min - 2 horas)

**Trigger:** L1 escala o el CI/CD pipeline falla.

**Pasos:**

1. **Localizar el incidente en los logs:**
   ```bash
   # Con el Correlation ID del usuario:
   grep "<CORRELATION_ID>" storage/logs/siam.json.log | jq '.'
   
   # O ver los últimos 50 errores:
   cat storage/logs/siam.json.log | jq 'select(.level == "ERROR")' | tail -50
   ```

2. **Verificar estado de la base de datos (Aiven MySQL):**
   - Acceder a `https://console.aiven.io` → verificar que el servicio `siam-mysql` está `Running`.
   - Verificar conexiones activas y límites.

3. **Rollback de emergencia en Render:**
   - `https://dashboard.render.com` → pestaña **"Deploys"** → seleccionar el último deploy exitoso → **"Rollback"**.

4. **Si es un bug de código:**
   ```bash
   # Crear rama de hotfix
   git checkout -b hotfix/descripcion-del-bug
   # Aplicar el fix
   # Ejecutar pruebas
   php artisan test
   # Hacer commit y push para que CD desplegue automáticamente
   git push origin hotfix/descripcion-del-bug
   ```

5. **Limpiar cachés si hay inconsistencias de configuración:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

6. **Si el problema persiste:** → Escalar a L3.

---

### L3 — Protocolo del Arquitecto (2 horas - 8 horas)

**Trigger:** Fallo de infraestructura, pérdida de datos, brecha de seguridad.

**Pasos:**

1. **Activar modo de mantenimiento** (si Laravel está accesible):
   ```bash
   php artisan down --render="errors/503" --secret="<TOKEN_SECRETO>"
   ```

2. **Fallo completo de la base de datos:**
   - Acceder a `https://console.aiven.io` → restaurar desde el backup más reciente.
   - Actualizar `DATABASE_URL` en las variables de entorno de Render si el host cambió.
   - Ejecutar migraciones pendientes:
     ```bash
     php artisan migrate --force
     ```

3. **Brecha de seguridad confirmada:**
   - Revocar inmediatamente todas las sesiones activas:
     ```bash
     # Regenerar APP_KEY (invalida todas las sesiones y cookies cifradas)
     php artisan key:generate --force
     ```
   - Rotar `DB_PASSWORD`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_2FA_SECRET_KEY` en Render.
   - Revisar logs para determinar el alcance de la brecha.

4. **Restaurar desde cero** (escenario catastrófico):
   - Ver Fase 3 — Recuperación ante Desastres.

5. **Levantar el modo de mantenimiento:**
   ```bash
   php artisan up
   ```

---

## FASE 3 — RECUPERACIÓN ANTE DESASTRES (Regla 3-2-1)

### Política de Backup 3-2-1

El sistema sigue la regla **3-2-1** para garantizar la recuperabilidad ante cualquier escenario:

| Copia | Medio | Ubicación | Frecuencia |
|---|---|---|---|
| **Copia 1** | Base de datos en Aiven Cloud (MySQL) | `aiven.io` | Continua (réplica activa) |
| **Copia 2** | Backup automático de Aiven | `aiven.io` — política de retención 7 días | Diaria |
| **Copia 3** | Exportación manual del desarrollador | Equipo local / USB cifrado | Semanal (antes de cada avance) |

### Procedimiento de Backup Manual (Copia 3)

Ejecutar **antes de cada avance** o cambio importante:

```bash
# Exportar la base de datos desde el servidor Aiven
mysqldump --host=<AIVEN_HOST> \
          --port=<AIVEN_PORT> \
          --user=<AIVEN_USER> \
          --password=<AIVEN_PASSWORD> \
          --ssl-ca=ca.pem \
          <NOMBRE_DB> > backup_siam_$(date +%Y%m%d_%H%M%S).sql

# Comprimir y encriptar el backup
zip -e backup_siam_$(date +%Y%m%d).zip backup_siam_*.sql
```

Guardar el archivo `.zip` encriptado en **dos ubicaciones físicas distintas** (ej: equipo del desarrollador + USB).

---

### Procedimiento de Restauración Completa (Re-deploy desde Cero)

En caso de pérdida total del entorno de producción:

**Paso 1 — Clonar el repositorio:**
```bash
git clone https://github.com/Jose-Sierra082005/sistema-militar-unefa.git
cd sistema-militar-unefa
```

**Paso 2 — Instalar dependencias:**
```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
```

**Paso 3 — Configurar variables de entorno:**
- En Render: ir a **Environment** → añadir todas las variables de `.env.example` con los valores de producción:
  - `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`
  - `DATABASE_URL` (Aiven)
  - `MAIL_*`, `GOOGLE_*`, `GOOGLE_2FA_*`

**Paso 4 — Ejecutar migraciones y seeders:**
```bash
# ADVERTENCIA: Esto elimina todos los datos existentes. Solo usar si la BD está vacía.
php artisan migrate:fresh --seed --force
```

**Paso 5 — Si hay backup de BD, restaurar en lugar del seed:**
```bash
mysql --host=<HOST> --port=<PORT> --user=<USER> --password <NOMBRE_DB> < backup_siam_YYYYMMDD.sql
```

**Paso 6 — Verificar el despliegue:**
```bash
curl -s https://sistema-militar-unefa.onrender.com/health
# Debe retornar: {"status":"ok",...}
```

**Paso 7 — Ejecutar suite de pruebas de aceptación:**
```bash
php artisan test --filter="HealthCheckTest|CorrelationIdTest"
```

---

## Contacto y Escalada

| Rol | Responsable | Contacto |
|---|---|---|
| Desarrollador Principal | Jose Sierra | GitHub: @Jose-Sierra082005 |
| Plataforma Cloud | Render.com | https://dashboard.render.com |
| Base de Datos | Aiven Cloud | https://console.aiven.io |

---

*Última actualización: Julio 2026 — Avance #6 (Seguridad, Observabilidad y Soporte)*
