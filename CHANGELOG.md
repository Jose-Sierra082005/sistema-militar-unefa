# Bitácora de Cambios (Changelog)

Todos los cambios notables a este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es/1.0.0/), y este proyecto sigue el estándar [Semantic Versioning](https://semver.org/lang/es/).

---

## [Unreleased]

> Cambios en progreso que aún no forman parte de un release oficial.

---

## [0.1.0-alpha] — 2026-05-30

### Inicialización del Repositorio — Avance #3: La Táctica

#### Added (Agregado)
- `feat(init)`: Inicialización del proyecto con Laravel Framework v13.12.0 sobre PHP 8.4.0.
- `chore(gitignore)`: Configuración del archivo `.gitignore` adaptado a Laravel con exclusiones para archivos de documentación académica del Avance 2 (`.pdf`, `.docx`, `.drawio`, `.html`) y directorios de IDE (`.vscode/`).
- `chore(env)`: Creación del archivo `.env.example` documentado con las variables de entorno necesarias para conectar con la base de datos MySQL en Aiven Cloud.
- `docs(contributing)`: Creación del manual de contribución `CONTRIBUTING.md` que define:
  - Modelo de ramas GitFlow adaptado al proyecto (5 tipos de ramas).
  - Nomenclatura de ramas en kebab-case con prefijos estándar.
  - Estándar de mensajes de commit Conventional Commits v1.0.0 con tabla de tipos y ejemplos en español.
  - Flujo completo de trabajo para un cambio desde la rama `develop`.
  - Reglas de Pull Request y de la rama `main` protegida.
- `chore(ci)`: Configuración del pipeline de integración continua `.github/workflows/ci.yml` que ejecuta automáticamente en cada PR:
  1. Descarga del código.
  2. Configuración de PHP 8.4 con extensiones necesarias.
  3. Copia de `.env.example` a `.env` y generación de clave de aplicación.
  4. Instalación de dependencias con Composer.
  5. Ejecución de la suite de pruebas base de Laravel con `php artisan test`.
- `docs(readme)`: Creación del `README.md` principal del repositorio con:
  - Descripción del sistema y contexto académico.
  - Arquitectura del sistema completa en Mermaid.js (Diagrama de Arquitectura de Capas MVC, Diagrama de Casos de Uso, Diagrama de Secuencia de Login y Evaluación, Diagrama de Flujo del sistema).
  - Esquema de la base de datos relacional (6 tablas) en Mermaid.js (Entity-Relationship).
  - Instrucciones de instalación local y configuración del entorno.
  - Tabla del stack tecnológico completo con versiones.
  - Autores del proyecto.

#### Structure (Estructura del Proyecto)
- Estructura MVC de Laravel inicializada: `app/`, `routes/`, `resources/views/`, `database/migrations/`, `public/`.
- Migraciones base de Laravel ejecutadas en SQLite local para el entorno de desarrollo inicial.
- Suite de pruebas base (PHPUnit v12.5.28) instalada y operativa.

---

*Formato: `[Versión] — YYYY-MM-DD` | Tipos: `Added`, `Changed`, `Deprecated`, `Removed`, `Fixed`, `Security`*
