<img src="https://img.shields.io/badge/Laravel-v13.12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"> <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"> <img src="https://img.shields.io/badge/MySQL-Aiven_Cloud-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"> <img src="https://img.shields.io/badge/CI%2FCD-GitHub_Actions-2088FF?style=for-the-badge&logo=github-actions&logoColor=white" alt="GitHub Actions"> <img src="https://img.shields.io/badge/Licencia-Académica-1b4332?style=for-the-badge" alt="Licencia">

---

# 🎖️ Sistema Web de Formación en Conocimientos Militares

**Aplicación Web para la Formación de Estudiantes Universitarios en Conocimientos Militares y Estrategias Tácticas**

> Proyecto académico desarrollado en el marco de las materias **Implantación de Sistemas** y **Metodología** — Ingeniería de Sistemas, UNEFA Núcleo Falcón.

---

## 📋 Descripción del Sistema

Plataforma educativa tipo LMS (Learning Management System) desarrollada con el framework PHP **Laravel**, orientada a centralizar y estructurar la instrucción cívico-militar para estudiantes universitarios. El sistema permite:

- 📚 **Gestión de Módulos de Aprendizaje**: Creación, edición y publicación de lecciones temáticas con soporte para archivos PDF.
- 🔐 **Autenticación y Control de Acceso por Roles**: Módulo de login seguro con roles diferenciados (`admin` / `estudiante`) y bitácora de auditoría.
- 📝 **Motor de Evaluaciones**: Cuestionarios dinámicos de opción múltiple (A, B, C, D) con calificación automática del servidor.
- 📈 **Seguimiento de Progreso Secuencial**: Sistema de desbloqueo progresivo que habilita el siguiente módulo sólo si el anterior fue aprobado.
- 🛡️ **Registro de Auditoría (Bitácora)**: Trazabilidad de todos los accesos, sesiones e intentos de autenticación por usuario e IP.

---

## 👨‍💻 Autores

| Nombre | Cédula | Rol en el Proyecto |
|---|---|---|
| José Sierra | C.I: 31.149.881 | Coordinador General y Desarrollador Principal (Laravel/Backend) |
| José Salcedo | C.I: 31.559.727 | Analista, Documentador y Diseñador de Interfaz (UI/UX) |

**Tutor Académico:** MSC. Robert González

---

## 🏗️ Arquitectura del Sistema

El sistema sigue el patrón **MVC (Modelo-Vista-Controlador)** de Laravel con una conexión a base de datos MySQL en la nube (Aiven Cloud) y despliegue en Render.

### Diagrama de Arquitectura de Capas MVC

```mermaid
graph TB
    subgraph CLIENT["🌐 CAPA DE PRESENTACIÓN (Cliente)"]
        direction LR
        BROWSER["Navegador Web<br/>(Chrome, Firefox)"]
    end

    subgraph LARAVEL["⚙️ CAPA DE APLICACIÓN (Laravel MVC — Servidor)"]
        direction TB
        ROUTER["Router<br/>routes/web.php"]
        MIDDLEWARE["Middleware<br/>Auth / RoleCheck"]
        CONTROLLER["Controladores<br/>ModuloController<br/>EvaluacionController<br/>UsuarioController"]
        MODEL["Modelos Eloquent<br/>Usuario · Modulo<br/>Pregunta · Resultado<br/>Progreso · Bitacora"]
        VIEW["Vistas Blade<br/>Layouts · Partials<br/>Dashboard · Módulos<br/>Evaluaciones"]
    end

    subgraph DATA["🗄️ CAPA DE DATOS (MySQL — Aiven Cloud)"]
        direction TB
        DB["Base de Datos MySQL"]
        T1[("usuarios")]
        T2[("modulos")]
        T3[("preguntas")]
        T4[("resultados_evaluaciones")]
        T5[("progresos")]
        T6[("bitacora_accesos")]
    end

    subgraph DEPLOY["☁️ DESPLIEGUE (Producción)"]
        RENDER["Render<br/>(Hosting Web Gratuito)"]
        AIVEN["Aiven Cloud<br/>(MySQL Remoto)"]
    end

    BROWSER -->|"HTTP Request"| ROUTER
    ROUTER --> MIDDLEWARE
    MIDDLEWARE --> CONTROLLER
    CONTROLLER <--> MODEL
    CONTROLLER --> VIEW
    VIEW -->|"HTML Response"| BROWSER
    MODEL <-->|"Eloquent ORM / PDO"| DB
    DB --- T1 & T2 & T3 & T4 & T5 & T6
    LARAVEL -.->|"Deploy Automático"| RENDER
    DATA -.->|"Conexión SSL"| AIVEN
```

---

### Diagrama de Casos de Uso

```mermaid
graph LR
    ESTUDIANTE(["👤 Estudiante"])
    ADMIN(["🛡️ Administrador"])

    subgraph SISTEMA["Sistema de Formación Militar"]
        UC1["Iniciar Sesión"]
        UC2["Ver Dashboard / Progreso"]
        UC3["Leer Módulo de Aprendizaje"]
        UC4["Presentar Evaluación"]
        UC5["Ver Historial de Calificaciones"]
        UC6["Gestionar Usuarios"]
        UC7["Gestionar Módulos y Contenido"]
        UC8["Gestionar Banco de Preguntas"]
        UC9["Consultar Bitácora de Accesos"]
    end

    ESTUDIANTE --> UC1
    ESTUDIANTE --> UC2
    ESTUDIANTE --> UC3
    ESTUDIANTE --> UC4
    ESTUDIANTE --> UC5

    ADMIN --> UC1
    ADMIN --> UC6
    ADMIN --> UC7
    ADMIN --> UC8
    ADMIN --> UC9
```

---

### Diagrama de Secuencia — Flujo de Login y Acceso al Sistema

```mermaid
sequenceDiagram
    actor U as Usuario (Estudiante/Admin)
    participant B as Blade (Vista Login)
    participant M as AuthController
    participant DB as Base de Datos MySQL
    participant BIT as Tabla bitacora_accesos

    U->>B: Ingresa cédula/correo y contraseña
    B->>M: POST /login (credentials)
    M->>DB: SELECT * FROM usuarios WHERE email = ?
    DB-->>M: Retorna registro del usuario

    alt Credenciales Correctas
        M->>M: Hash::check(password, hash)
        M->>BIT: INSERT acceso_exitoso + IP + timestamp
        M-->>B: Redirect → /dashboard (según rol)
        B-->>U: Vista Dashboard Personalizada
    else Credenciales Incorrectas
        M->>BIT: INSERT intento_fallido + IP + timestamp
        M-->>B: Error "Credenciales inválidas"
        B-->>U: Formulario de Login con mensaje de error
    end
```

---

### Diagrama de Secuencia — Flujo de Evaluación y Actualización de Progreso

```mermaid
sequenceDiagram
    actor E as Estudiante
    participant V as Vista Evaluación (Blade)
    participant EC as EvaluacionController
    participant PC as ProgresoController
    participant DB as Base de Datos MySQL

    E->>V: Accede al módulo → click "Presentar Evaluación"
    V->>EC: GET /evaluacion/{modulo_id}
    EC->>DB: SELECT preguntas WHERE modulo_id = ?
    DB-->>EC: Lista de preguntas del módulo
    EC-->>V: Renderiza cuestionario con las preguntas

    E->>V: Selecciona respuestas y envía el formulario
    V->>EC: POST /evaluacion/{modulo_id}/calificar (respuestas[])

    EC->>EC: Calcula nota comparando respuestas con respuesta_correcta
    EC->>DB: INSERT INTO resultados_evaluaciones (nota, status)

    alt Nota >= 60% (Aprobado)
        EC->>PC: Actualizar progreso del módulo a completado = 1
        PC->>DB: UPDATE progresos SET completado = 1 WHERE usuario_id AND modulo_id
        EC-->>V: Redirect → /dashboard con mensaje "¡Módulo Aprobado!"
    else Nota < 60% (Reprobado)
        EC-->>V: Redirect → /evaluacion con mensaje "Reprobado — Puedes reintentar"
    end
```

---

### Diagrama de Flujo — Algoritmo de Login Seguro con Bitácora

```mermaid
flowchart TD
    A([INICIO]) --> B[/"Usuario ingresa cédula o correo y contraseña"/]
    B --> C{{"¿El correo/cédula existe en la BD?"}}
    C -- NO --> D["Registrar intento fallido en bitacora_accesos<br/>(accion: intento_fallido, IP, timestamp)"]
    D --> E[/"Mostrar: Credenciales inválidas"/]
    E --> B

    C -- SÍ --> F{{"¿Coincide la contraseña con el hash?"}}
    F -- NO --> D

    F -- SÍ --> G{{"¿El usuario está activo en la BD?"}}
    G -- NO --> H[/"Mostrar: Cuenta suspendida. Contacte al administrador"/]
    H --> Z([FIN])

    G -- SÍ --> I["Registrar acceso exitoso en bitacora_accesos<br/>(accion: ingreso_exitoso, IP, timestamp)"]
    I --> J[["Iniciar sesión con Auth::login(usuario)"]]
    J --> K{{"¿Rol del usuario?"}}
    K -- admin --> L[/"Redirigir: /admin/dashboard"/]
    K -- estudiante --> M[/"Redirigir: /dashboard"/]
    L & M --> Z
```

---

### Diagrama de Flujo — Presentación de Evaluación y Desbloqueo Secuencial

```mermaid
flowchart TD
    A([INICIO]) --> B{{"¿El estudiante tiene acceso al módulo actual?"}}
    B -- NO, Bloqueado --> C[/"Mostrar: Módulo bloqueado.<br/>Aprueba el módulo anterior."/]
    C --> Z([FIN])

    B -- SÍ --> D[/"Cargar preguntas del banco de preguntas del módulo"/]
    D --> E[/"Mostrar cuestionario al estudiante con opciones A, B, C, D"/]
    E --> F[/"Estudiante selecciona respuestas y envía el formulario"/]

    F --> G[["EvaluacionController evalúa las respuestas en el servidor"]]
    G --> H["Calcular nota: (respuestas_correctas / total_preguntas) × 20"]
    H --> I["Registrar en resultados_evaluaciones<br/>(nota, status, intentos, fecha)"]

    I --> J{{"¿Nota >= 12 puntos? (60%)"}}
    J -- NO, Reprobado --> K["status = 'Reprobado'"]
    K --> L[/"Mostrar nota obtenida y permitir nuevo intento"/]
    L --> E

    J -- SÍ, Aprobado --> M["status = 'Aprobado'"]
    M --> N["UPDATE progresos SET completado = 1 (módulo actual)"]
    N --> O{{"¿Existe un módulo siguiente?"}}
    O -- SÍ --> P["Crear/actualizar registro en progresos<br/>para el siguiente módulo (completado = 0)"]
    P --> Q[/"Mostrar: ¡Módulo Aprobado! Siguiente módulo desbloqueado."/]
    O -- NO --> R[/"Mostrar: ¡Felicitaciones! Has completado todos los módulos del curso."/]
    Q & R --> Z
```

---

## 🗄️ Esquema de la Base de Datos (6 Tablas)

```mermaid
erDiagram
    usuarios {
        bigint id PK
        string nombre
        string cedula UK
        string email UK
        string password
        enum rol
        enum estado
        timestamp email_verified_at
        timestamp created_at
        timestamp updated_at
    }

    modulos {
        bigint id PK
        string titulo
        text contenido
        string pdf_path
        int orden UK
        enum estado
        timestamp created_at
        timestamp updated_at
    }

    preguntas {
        bigint id PK
        bigint modulo_id FK
        text enunciado
        string opcion_a
        string opcion_b
        string opcion_c
        string opcion_d
        char respuesta_correcta
        decimal puntaje
        timestamp created_at
        timestamp updated_at
    }

    resultados_evaluaciones {
        bigint id PK
        bigint usuario_id FK
        bigint modulo_id FK
        decimal nota_obtenida
        enum estado
        int intentos
        timestamp fecha_evaluacion
        timestamp created_at
        timestamp updated_at
    }

    progresos {
        bigint id PK
        bigint usuario_id FK
        bigint modulo_id FK
        tinyint completado
        timestamp created_at
        timestamp updated_at
    }

    bitacora_accesos {
        bigint id PK
        bigint usuario_id FK
        string accion
        string ip_address
        text user_agent
        timestamp created_at
    }

    usuarios ||--o{ resultados_evaluaciones : "tiene"
    usuarios ||--o{ progresos : "registra"
    usuarios ||--o{ bitacora_accesos : "genera"
    modulos ||--o{ preguntas : "contiene"
    modulos ||--o{ resultados_evaluaciones : "pertenece_a"
    modulos ||--o{ progresos : "tiene_progreso"
```

---

## 🚀 Instalación Local

### Requisitos Previos
- PHP >= 8.2
- Composer >= 2.x
- MySQL (local o cuenta en [Aiven](https://aiven.io/) para MySQL remoto gratuito)
- Laragon o XAMPP (entorno local recomendado en Windows)
- Git

### Pasos de Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/[tu-usuario]/sistema-militar-unefa.git
cd sistema-militar-unefa

# 2. Instalar dependencias de PHP
composer install

# 3. Copiar el archivo de variables de entorno
cp .env.example .env

# 4. Generar la clave de la aplicación
php artisan key:generate

# 5. Configurar la base de datos en .env
#    Editar .env y completar las credenciales DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 6. Ejecutar las migraciones
php artisan migrate

# 7. (Opcional) Poblar la base de datos con datos de prueba
php artisan db:seed

# 8. Iniciar el servidor de desarrollo
php artisan serve
```

Acceder al sistema en: **http://localhost:8000**

---

## 🧪 Ejecutar las Pruebas

```bash
# Ejecutar toda la suite de pruebas
php artisan test

# Ejecutar con reporte detallado
php artisan test --verbose

# Verificar estilo de código (Linter)
./vendor/bin/pint --test
```

---

## 🛠️ Stack Tecnológico

| Tecnología | Versión | Función en el Proyecto |
|---|---|---|
| Laravel | v13.12 | Framework MVC principal del backend |
| PHP | 8.4 | Lenguaje de programación del servidor |
| MySQL | 8.x | Motor de base de datos relacional |
| Aiven Cloud | — | Hosting DBaaS gratuito para MySQL en la nube |
| Blade | — | Motor de plantillas para las vistas dinámicas |
| Bootstrap / Tailwind CSS | — | Frameworks de diseño CSS responsive |
| HTML5 / CSS3 / JS | — | Estructura, estilos e interactividad del cliente |
| Composer | 2.x | Gestor de dependencias PHP |
| PHPUnit | 12.5 | Suite de pruebas automatizadas |
| Laravel Pint | — | Linter y formateador de código PHP |
| Git | — | Control de versiones distribuido |
| GitHub | — | Repositorio remoto y colaboración |
| GitHub Actions | — | Pipeline CI/CD automático |
| Render | — | Plataforma de hosting para despliegue en producción |
| Laragon / XAMPP | — | Entorno de desarrollo local en Windows |

---

## 📁 Estructura del Proyecto

```
sistema-militar-unefa/
├── .github/
│   └── workflows/
│       └── ci.yml              # Pipeline de CI/CD (GitHub Actions)
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Lógica de negocio (MVC - Controladores)
│   │   └── Middleware/         # Verificación de roles y autenticación
│   └── Models/                 # Modelos Eloquent (6 tablas)
├── database/
│   └── migrations/             # Migraciones de base de datos
├── resources/
│   └── views/                  # Vistas Blade (Frontend)
├── routes/
│   └── web.php                 # Definición de rutas HTTP
├── .env.example                # Plantilla de variables de entorno
├── .gitignore                  # Exclusiones del control de versiones
├── CHANGELOG.md                # Bitácora de cambios del proyecto
├── CONTRIBUTING.md             # Guía de contribución y estándares
├── composer.json               # Dependencias del proyecto PHP
└── README.md                   # Documentación principal (este archivo)
```

---

## 🔄 Flujo de Desarrollo (GitFlow)

Ver [CONTRIBUTING.md](./CONTRIBUTING.md) para la guía completa de ramas, commits y Pull Requests.

```
main ←── develop ←── feature/* / fix/* / docs/* / chore/*
```

> 🔒 La rama `main` está **protegida**. Ningún desarrollador puede hacer push directo. Todo cambio requiere un Pull Request con al menos **1 aprobación** del equipo y el pipeline de CI/CD en **verde**.

---

## 📄 Licencia

Proyecto académico desarrollado exclusivamente con fines educativos para la **UNEFA Núcleo Falcón** en el marco de las materias de Implantación de Sistemas y Metodología del semestre 2026-I.
