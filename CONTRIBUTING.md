# Guía de Contribución — Sistema Web de Formación Militar

**Proyecto:** Aplicación Web para la Formación de Estudiantes Universitarios en Conocimientos Militares y Estrategias Tácticas  
**Institución:** UNEFA – Núcleo Falcón  
**Materias:** Implantación de Sistemas / Metodología  
**Autores:** José Sierra (C.I: 31.149.881) · José Salcedo (C.I: 31.559.727)  
**Tutor:** MSC. Robert González

---

## Índice

1. [Filosofía de Trabajo](#filosofía-de-trabajo)
2. [Modelo de Ramas (GitFlow Adaptado)](#modelo-de-ramas-gitflow-adaptado)
3. [Nomenclatura de Ramas](#nomenclatura-de-ramas)
4. [Estándar de Mensajes de Commit (Conventional Commits)](#estándar-de-mensajes-de-commit-conventional-commits)
5. [Flujo Completo de un Cambio](#flujo-completo-de-un-cambio)
6. [Reglas de Pull Request (PR)](#reglas-de-pull-request-pr)
7. [Reglas de la Rama `main` (Producción Blindada)](#reglas-de-la-rama-main-producción-blindada)

---

## Filosofía de Trabajo

Este repositorio sigue los principios de **GitFlow** adaptados a un proyecto de desarrollo académico de dos integrantes. La rama `main` representa el estado de **producción verificado y aprobado**. Ningún integrante puede modificarla directamente — todo cambio entra a través de un Pull Request revisado y con el pipeline de CI/CD en verde.

---

## Modelo de Ramas (GitFlow Adaptado)

```
main          ─── Producción (PROTEGIDA - No se permite push directo)
  └── develop ─── Integración (rama de trabajo principal del equipo)
        ├── feature/nombre-de-la-funcionalidad
        ├── fix/descripcion-del-error
        ├── docs/seccion-documentada
        └── chore/tarea-de-mantenimiento
```

| Rama | Propósito | ¿Merge a quién? |
|---|---|---|
| `main` | Código en producción, estable y verificado | — |
| `develop` | Integración activa del equipo | `main` (via PR) |
| `feature/*` | Desarrollo de nuevas funcionalidades | `develop` (via PR) |
| `fix/*` | Corrección de errores reportados | `develop` (via PR) |
| `docs/*` | Actualizaciones de documentación | `develop` (via PR) |
| `chore/*` | Configuraciones, dependencias, CI/CD | `develop` (via PR) |

---

## Nomenclatura de Ramas

Las ramas deben nombrarse en **kebab-case** (minúsculas con guiones), usando el prefijo correspondiente al tipo de trabajo y una descripción corta pero descriptiva.

### Formato
```
<tipo>/<descripcion-en-kebab-case>
```

### Tipos permitidos

| Prefijo | Cuándo usarlo | Ejemplo |
|---|---|---|
| `feature/` | Nueva funcionalidad del sistema | `feature/modulo-autenticacion` |
| `feature/` | Nueva vista o pantalla | `feature/dashboard-estudiante` |
| `feature/` | Nueva migración o modelo | `feature/tabla-resultados-evaluaciones` |
| `fix/` | Corrección de un bug identificado | `fix/error-login-cedula-invalida` |
| `fix/` | Reparación de rutas o middleware | `fix/middleware-rol-admin` |
| `docs/` | Documentación del README o CHANGELOG | `docs/actualizar-diagrama-arquitectura` |
| `chore/` | Configuración del entorno, CI, dependencias | `chore/configurar-github-actions` |
| `chore/` | Limpieza de código sin cambios lógicos | `chore/refactor-controlador-modulos` |

### Ejemplos de Nombres de Ramas Válidos
```bash
git checkout -b feature/crud-modulos-aprendizaje
git checkout -b feature/motor-evaluaciones-cuestionario
git checkout -b feature/panel-admin-usuarios
git checkout -b feature/progreso-secuencial-estudiante
git checkout -b fix/error-bitacora-ip-null
git checkout -b docs/readme-mermaid-arquitectura
git checkout -b chore/configurar-ci-pipeline
```

---

## Estándar de Mensajes de Commit (Conventional Commits)

Todos los commits **deben** seguir el estándar [Conventional Commits v1.0.0](https://www.conventionalcommits.org/).

### Formato

```
<tipo>(<alcance>): <descripción-corta-en-imperativo>

[Cuerpo opcional: explica el QUÉ y el POR QUÉ, no el cómo]

[Footer opcional: Closes #<número-de-issue>]
```

### Tipos de Commit Permitidos

| Tipo | Cuándo usarlo | Ejemplo |
|---|---|---|
| `feat` | Nueva funcionalidad o característica | `feat(auth): implementar inicio de sesión con cédula` |
| `fix` | Corrección de un bug | `fix(evaluacion): corregir cálculo de nota final` |
| `docs` | Cambios en documentación | `docs(readme): agregar diagrama de secuencia en Mermaid` |
| `style` | Cambios de formato, estilos CSS/Blade | `style(dashboard): ajustar paleta de colores militar` |
| `refactor` | Refactorización sin cambio funcional | `refactor(progreso): simplificar lógica de desbloqueo` |
| `test` | Añadir o corregir pruebas | `test(auth): agregar prueba de acceso denegado sin sesión` |
| `chore` | Tareas de mantenimiento | `chore(ci): configurar pipeline de GitHub Actions` |
| `migration` | Nueva migración de base de datos | `migration: crear tabla bitacora_accesos` |
| `seed` | Nuevo seeder de datos | `seed: agregar módulos iniciales de formación militar` |

### Ejemplos de Commits Válidos

```bash
git commit -m "feat(auth): implementar inicio de sesión con cédula y contraseña"
git commit -m "feat(modulos): crear CRUD de módulos de aprendizaje para el administrador"
git commit -m "feat(evaluacion): desarrollar motor de cuestionario de opción múltiple"
git commit -m "feat(progreso): implementar desbloqueo secuencial de módulos"
git commit -m "feat(bitacora): registrar acceso de usuarios en tabla bitacora_accesos"
git commit -m "fix(auth): corregir redirección tras login con rol estudiante"
git commit -m "migration: agregar tabla resultados_evaluaciones con clave foránea a usuarios"
git commit -m "docs(contributing): inicializar guía de contribución del proyecto"
git commit -m "chore(env): actualizar .env.example con variables de Aiven MySQL"
```

### Reglas Importantes
- La descripción corta **siempre en español**, en modo imperativo (ej. "implementar", "crear", "corregir" — no "implementado" ni "creando").
- La descripción corta debe tener **máximo 72 caracteres**.
- No se permiten commits directamente sobre `main` ni sobre `develop` sin PR.

---

## Flujo Completo de un Cambio

```bash
# 1. Partir siempre desde develop actualizado
git checkout develop
git pull origin develop

# 2. Crear rama de trabajo
git checkout -b feature/nombre-de-la-funcionalidad

# 3. Desarrollar y hacer commits frecuentes con el estándar
git add .
git commit -m "feat(modulos): crear controlador de módulos con rutas CRUD"

# 4. Subir rama al repositorio remoto
git push origin feature/nombre-de-la-funcionalidad

# 5. Abrir un Pull Request en GitHub de tu rama → develop
# 6. Solicitar revisión del compañero
# 7. Esperar que el pipeline de CI/CD esté en verde ✅
# 8. Fusionar (merge) sólo si se aprueba el PR y el pipeline pasa
```

---

## Reglas de Pull Request (PR)

1. **Título del PR**: Usar el mismo formato de Conventional Commits (ej. `feat(auth): implementar módulo de login`).
2. **Descripción del PR**: Describir brevemente qué se hizo, por qué, y cómo probarlo localmente.
3. **Review obligatorio**: Mínimo **1 aprobación del compañero** antes de poder fusionar.
4. **Pipeline en verde**: El CI/CD de GitHub Actions debe pasar **todos los checks** antes del merge.
5. **Sin merge directo a `main`**: Todos los cambios a producción entran a través de `develop` → PR → `main`.

---

## Reglas de la Rama `main` (Producción Blindada)

La rama `main` tiene las siguientes protecciones configuradas directamente en GitHub:

- ✅ **Require a pull request before merging** — No push directo permitido.
- ✅ **Require approvals: 1** — Al menos un par debe aprobar el PR.
- ✅ **Require status checks to pass** — El pipeline de CI/CD debe estar verde.
- ✅ **Include administrators** — Esta regla aplica para **todos**, incluyendo los dueños del repositorio.
- ✅ **Do not allow bypassing the above settings** — Nadie puede saltarse estas reglas.

> ⚠️ Intentar un `git push origin main` desde la terminal será **rechazado automáticamente** por GitHub.
