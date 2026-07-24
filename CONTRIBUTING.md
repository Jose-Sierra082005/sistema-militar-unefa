# Guía de Contribución — SIAM

¡Gracias por tu interés en contribuir al **Sistema Integrado de Administración Militar (SIAM)**!  
Este documento describe el proceso estándar para proponer cambios al proyecto.

---

## 📋 Prerrequisitos

Antes de contribuir, asegúrate de tener instalado:

- **PHP 8.4+**
- **Composer 2.x**
- **Git**
- (Opcional) **Docker** para levantarlo sin instalar nada manualmente

---

## 🚀 Pasos para Contribuir

### 1. Fork y Clonar el repositorio

```bash
# Clona tu fork
git clone https://github.com/TU_USUARIO/sistema-militar-unefa.git
cd sistema-militar-unefa
```

### 2. Configurar el entorno local

```bash
# Instalar dependencias PHP
composer install

# Copiar el archivo de entorno
cp .env.example .env

# Generar clave de la aplicación
php artisan key:generate

# Crear la base de datos y ejecutar migraciones
php artisan migrate --seed

# Iniciar el servidor local
php artisan serve
```

### 3. Crear una rama descriptiva

Usa el prefijo correcto según el tipo de cambio:

```bash
# Nueva funcionalidad
git checkout -b feat/nombre-de-la-funcionalidad

# Corrección de bug
git checkout -b fix/descripcion-del-bug

# Mejora de documentación
git checkout -b docs/nombre-del-documento

# Mejora de seguridad
git checkout -b security/nombre-del-parche
```

### 4. Hacer los cambios y commitear

Sigue el estándar de **Conventional Commits**:

```bash
git add -A
git commit -m "tipo(alcance): descripción corta en presente

Descripción detallada de qué cambió y por qué (opcional).

- Bullet con cambio específico 1
- Bullet con cambio específico 2"
```

**Tipos válidos:** `feat`, `fix`, `docs`, `security`, `refactor`, `test`, `chore`

### 5. Verificar que las pruebas pasen

```bash
php artisan test
```

> ⛔ No se aceptarán Pull Requests que rompan pruebas existentes.

### 6. Subir la rama y abrir un Pull Request

```bash
git push origin nombre-de-tu-rama
```

Luego ve a GitHub y abre un **Pull Request** contra la rama `main`.  
Describe claramente: **qué cambiaste**, **por qué** y **cómo probarlo**.

---

## ✅ Criterios de Revisión

Un Pull Request será aprobado si:

- [ ] Las 48 pruebas automáticas pasan (GitHub Actions ✅)
- [ ] El código sigue las convenciones PSR-12 de PHP
- [ ] Los controladores usan `$request->validated()` para datos de entrada
- [ ] No hay credenciales ni secretos en el código
- [ ] El commit message sigue Conventional Commits
- [ ] Fue revisado y aprobado por al menos 1 colaborador

---

## 🔒 Reporte de Vulnerabilidades de Seguridad

Si encuentras una vulnerabilidad de seguridad, **no abras un Issue público**.  
Envía un reporte privado directamente al mantenedor del repositorio.

---

## 📝 Estilo de Código

- **PHP:** PSR-12, tipado estricto, DocBlocks en métodos públicos
- **Blade:** Sangría de 4 espacios, directivas `@` separadas de HTML
- **Commits:** Conventional Commits en español o inglés
- **Variables de entorno:** Siempre agregadas a `.env.example` (sin valor real)

---

*Mantenido por **Jose Sierra** — UNEFA Ingeniería de Sistemas*
