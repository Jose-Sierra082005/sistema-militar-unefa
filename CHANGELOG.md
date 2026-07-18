# Bitácora de Cambios — SIAM (Conventional Commits)

Todas las modificaciones del repositorio se registran bajo el estándar estricto de **Conventional Commits** y vinculadas a su respectiva tarea del plan de desarrollo.

---

## [1.2.0] — 2026-07-06

### 🚀 Nuevas Características (Feat)
* `feat: implementar bloques try/catch y logs defensivos en controladores críticos (Tarea #27)`
  * Manejo elegante de excepciones en `login`, `sendTwoFactorRecoverOtp` y `completeQuiz`.
  * Logs estructurados bajo el formato `[INFO]/[ERROR] [YYYY-MM-DD]` en archivos del sistema.
* `feat: recuperación de Google Authenticator cuando se pierde el 2FA (Tarea #17)`
  * Implementación del flujo de restablecimiento OTP enviado por correo electrónico.

### 🐛 Corrección de Errores (Fix)
* `fix: corregir login admin con contraseña y fallback en producción (Tarea #20)`
  * Solución a la anomalía de doble hasheo en seeders del administrador.
* `fix: corregir verificación del QR al restablecer Google Authenticator (Tarea #18)`
  * Sincronización robusta mediante el guardado del secreto temporal en sesión para evitar regeneraciones al fallar.
* `fix: eliminar BOM UTF-8 que rompía AuthController en producción (Tarea #16)`
  * Remoción de bytes invisibles generados por scripts en Windows que causaban fallos en servidores Linux (Render).

### 🧪 Pruebas Automatizadas (Test)
* `test: crear pruebas unitarias de rango académico y normalización de cédula (Tarea #26)`
  * Cobertura para comprobar los algoritmos de gamificación (rangos por XP) y limpieza de cadenas.
* `refactor: limpiar pruebas obsoletas del portal estudiantil (Tarea #24)`
  * Eliminación de tests de vistas obsoletas de cadetes que fueron removidas durante el saneamiento del código.

### 📝 Documentación e Estilos (Docs)
* `docs: agregar PHPDoc a controladores y sección de autogeneración en README (Tarea #28)`
  * Documentación autodeclarativa en clases `AuthController`, `StudentPortalController` y `CourseController`.
  * Inclusión del comando Docker para autogenerar la documentación técnica estática.
* `docs: corregir diagramas Mermaid, ocultar credenciales reales y ajustar descripcion en el README (Tarea #23)`
  * Remoción de emails y claves por defecto en texto plano para asegurar la privacidad del repositorio.
  * Corrección de caracteres en los flujos Mermaid para su correcta renderización en GitHub.
* `docs: corregir base de datos a MySQL (Aiven Cloud) en el README (Tarea #22)`
  * Actualización de la infraestructura técnica y diagrama de arquitectura de capas del sistema.
* `docs: restaurar README.md de Tactic Force con diagramas Mermaid actualizados (Tarea #21)`
  * Reconstrucción del archivo borrado de documentación principal.

### ⚙️ Refactorizaciones y Rebranding (Refactor)
* `refactor: hacer opcional el campo de cédula en el perfil de estudiantes y administradores (Tarea #32)`
  * La cédula ya no es obligatoria al actualizar perfiles de usuario si no se ha registrado previamente.
* `refactor: implementar accesores y métodos de utilidad en modelo User y Blade (Tarea #25)`
  * Centralización del algoritmo de rango militar (`$user->rank`) y saneamiento en vistas.
* `rebrand: renombrar la aplicación a Tactic Force en todo el sistema (Tarea #15)`
  * Reemplazo de todas las marcas comerciales del "Sistema Militar" en vistas, emails y servicios del sistema.
