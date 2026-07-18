# 🛡️ SIAM — Sistema Web de Formación en Conocimientos Militares

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-v11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-Aiven_Cloud-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Docker-Deploy-2496ED?style=for-the-badge&logo=docker&logoColor=white" alt="Docker">
  <img src="https://img.shields.io/badge/Licencia-Académica-1b4332?style=for-the-badge" alt="Licencia">
</p>

---

## 📝 Descripción del Proyecto

**SIAM** es una plataforma educativa interactiva tipo LMS (Learning Management System) orientada a la formación cívico-militar de **estudiantes universitarios** en conocimientos militares y estrategias tácticas. La aplicación ofrece una experiencia gamificada de aprendizaje y un panel de gestión integral diseñado para instituciones de educación superior con componente militar.

El sistema se divide en dos grandes áreas funcionales:

### 👤 Portal del Estudiante (Gamificado)
* **Mapa de Cursos Estilo Duolingo**: Rutas de aprendizaje secuenciales (Módulos M1 a M7) donde las lecciones se desbloquean a medida que se aprueban las anteriores.
* **Lecciones Interactivas en HTML**: Visualización directa de contenidos didácticos cargados dinámicamente desde el servidor con soporte para recursos y manuales en PDF.
* **Quizzes Gamificados**: Evaluaciones rápidas con ganancia de puntos de experiencia (XP) y ascenso en los rangos militares del perfil del usuario.
* **Seguridad y Perfil**: Activación y desactivación de autenticación de doble factor (2FA), cambio de contraseña y soporte para autenticación con Google OAuth.

### 🛡️ Panel del Administrador (CMS Táctico)
* **Panel Principal**: Vista general del sistema con estadísticas rápidas de cursos, lecciones y progreso de los estudiantes.
* **Gestión CMS de Contenidos**: Editor visual completo para la creación y edición de cursos, lecciones (con vista previa en vivo en HTML y soporte de etiquetas seguras) y banco de preguntas para los cuestionarios.
* **Progreso Estudiantil**: Análisis estadístico del historial de evaluaciones por estudiante, incluyendo XP acumulado, precisión por lección y progreso por curso.
* **Configuración de Perfil**: Módulo de administración de las credenciales y datos del perfil del Comandante/Administrador del sistema.

---

## 👨‍💻 Autores y Desarrollo Académico

* **José Sierra** (C.I: 31.149.881) — Coordinador General y Desarrollador Backend Principal.
* **José Salcedo** (C.I: 31.559.727) — Analista de Sistemas, Diseñador UI/UX y Documentador.

* **Docente de la Asignatura:** MSc. Robert González
* **Institución:** Universidad Nacional Experimental Politécnica de la Fuerza Armada Nacional Bolivariana (UNEFA), Núcleo Falcón.

---

## 🏛️ Arquitectura y Modelado del Sistema

El sistema utiliza el patrón de diseño **Modelo-Vista-Controlador (MVC)** sobre Laravel, con una base de datos **MySQL** alojada en la nube mediante el servicio gestionado de **Aiven Cloud**, ideal para la persistencia robusta y el despliegue continuo en entornos de producción (Render).

### 1. Diagrama de Arquitectura de Capas y Despliegue
Este diagrama muestra el flujo de la petición del cliente y cómo interactúa con el servidor Laravel y la base de datos MySQL remota en Aiven Cloud, desplegado mediante Docker en Render:

```mermaid
graph TB
    subgraph CLIENT["🌐 CAPA DE PRESENTACIÓN (Cliente)"]
        direction LR
        BROWSER["Navegador Web<br/>(Chrome, Firefox, Safari)"]
    end

    subgraph LARAVEL["⚙️ CAPA DE APLICACIÓN (Laravel MVC — Servidor)"]
        direction TB
        ROUTER["Router<br/>routes/web.php"]
        MIDDLEWARE["Middleware de Seguridad<br/>Auth / RoleMiddleware / 2FA"]
        CONTROLLER["Controladores del Sistema<br/>CourseController · AuthController<br/>StudentPortalController"]
        MODEL["Modelos Eloquent<br/>User · Course · Lesson<br/>Question · Option · LessonCompletion"]
        VIEW["Vistas Blade + Tailwind CSS<br/>Layouts · Partials · Dashboard"]
    end

    subgraph DATA["🗄️ CAPA DE DATOS (MySQL — Aiven Cloud)"]
        direction TB
        DB["Motor MySQL 8.x"]
        T1[("users")]
        T2[("courses")]
        T3[("lessons")]
        T4[("questions")]
        T5[("options")]
        T6[("lesson_completions")]
    end

    subgraph DEPLOY["☁️ INFRAESTRUCTURA Y DESPLIEGUE (Producción)"]
        RENDER["Render Cloud<br/>(Docker Web Service)"]
        AIVEN["Aiven Cloud<br/>(MySQL Remoto — DBaaS)"]
    end

    BROWSER -->|"Petición HTTP"| ROUTER
    ROUTER --> MIDDLEWARE
    MIDDLEWARE --> CONTROLLER
    CONTROLLER <--> MODEL
    CONTROLLER --> VIEW
    VIEW -->|"Respuesta HTML"| BROWSER
    MODEL <-->|"Eloquent ORM / PDO MySQL"| DB
    DB --- T1 & T2 & T3 & T4 & T5 & T6
    LARAVEL -.->|"Despliegue de Contenedor"| RENDER
    DATA -.->|"Conexión SSL"| AIVEN
```

---

### 2. Diagrama de Casos de Uso del Sistema
El diagrama de casos de uso describe el alcance operativo de los dos perfiles principales:

```mermaid
graph LR
    ESTUDIANTE(["👤 Estudiante"])
    ADMIN(["🛡️ Administrador"])

    subgraph SISTEMA["Tactic Force — Plataforma de Formación Militar"]
        UC1["Iniciar Sesión<br/>(Cédula/Email/Google)"]
        UC2["Ver Dashboard / Mapa de Cursos"]
        UC3["Estudiar Lecciones y PDFs"]
        UC4["Presentar Quizzes Gamificados (XP)"]
        UC5["Gestionar Perfil y Activar 2FA"]
        UC6["Recuperar 2FA perdido vía OTP"]
        UC7["CMS: Gestionar Cursos, Lecciones y Preguntas"]
        UC8["Visualizar Progreso Estudiantil (XP y Precisión)"]
        UC9["Configurar Perfil Administrativo"]
    end

    ESTUDIANTE --> UC1
    ESTUDIANTE --> UC2
    ESTUDIANTE --> UC3
    ESTUDIANTE --> UC4
    ESTUDIANTE --> UC5
    ESTUDIANTE --> UC6

    ADMIN --> UC1
    ADMIN --> UC7
    ADMIN --> UC8
    ADMIN --> UC9
```

---

### 3. Diagrama de Secuencia — Autenticación Segura y Doble Factor (2FA)
Muestra el flujo cronológico de mensajes al iniciar sesión y validar el código de Google Authenticator:

```mermaid
sequenceDiagram
    actor U as Usuario
    participant B as Vista Login
    participant M as AuthController
    participant DB as Base de Datos MySQL

    U->>B: Ingresa cedula/correo y contrasena
    B->>M: POST /login
    M->>DB: Consulta registro del usuario
    DB-->>M: Retorna datos del usuario

    alt Credenciales validas
        M->>M: Valida hash de la contrasena
        M-->>B: Verifica estado 2FA
        alt 2FA habilitado
            M-->>B: Redirige a /two-factor/verify
            U->>B: Ingresa codigo TOTP de 6 digitos
            B->>M: POST /two-factor/verify
            M-->>B: Codigo validado correctamente
        end
        M-->>B: Auth login exitoso
        alt Rol admin
            M-->>B: Redirige al panel de control
        else Rol estudiante
            M-->>B: Redirige al portal estudiantil
        end
        B-->>U: Muestra Dashboard personalizado
    else Credenciales incorrectas
        M-->>B: Error de autenticacion
        B-->>U: Mensaje de credenciales invalidas
    end
```

---

### 4. Diagrama de Secuencia — Cuestionario y Progreso Académico
Representa el flujo de avance cuando un estudiante interactúa con el mapa Duolingo y completa una lección:

```mermaid
sequenceDiagram
    actor E as Estudiante
    participant V as Vista del Curso
    participant SPC as StudentPortalController
    participant DB as Base de Datos MySQL

    E->>V: Selecciona leccion activa en el mapa
    V->>SPC: GET /student/lessons/{id}
    SPC->>DB: SELECT lessons, questions y options
    DB-->>SPC: Datos de la leccion
    SPC-->>V: Renderiza leccion e inicia el quiz

    E->>V: Responde el quiz y envia formulario
    V->>SPC: POST /student/quiz/{lesson_id}/complete
    SPC->>SPC: Valida respuestas, calcula XP y accuracy_percent

    alt Primera vez completada
        SPC->>DB: INSERT lesson_completions (xp_earned, accuracy_percent)
        SPC->>DB: UPDATE users SET points = points + xp_earned
        SPC-->>V: Desbloquea siguiente leccion y muestra XP ganada y precision
    else Leccion ya completada (repaso)
        SPC-->>V: Muestra repaso sin sumar puntos
    end
```

---

### 5. Diagrama de Flujo — Login y Validación de 2FA
Muestra las decisiones lógicas tomadas por el controlador durante el flujo de inicio de sesión seguro:

```mermaid
flowchart TD
    A([INICIO]) --> B[/"Usuario ingresa cédula o correo y contraseña"/]
    B --> C{{"¿El usuario existe en la BD?"}}
    C -- NO --> E[/"Mostrar: Credenciales inválidas"/]
    E --> B

    C -- SÍ --> F{{"¿Coincide la contraseña?"}}
    F -- NO --> E

    F -- SÍ --> G{{"¿El usuario está activo?"}}
    G -- NO --> H[/"Mostrar: Cuenta inactiva. Contacte soporte"/]
    H --> Z([FIN])

    G -- SÍ --> I{{"¿Tiene 2FA activo?"}}
    I -- SÍ --> J[/"Redirigir a /two-factor/verify y solicitar código (6 dígitos)"/]
    J --> K{{"¿Código válido?"}}
    K -- NO --> L[/"Mostrar: Código incorrecto"/]
    L --> J
    K -- SÍ --> M[/"Iniciar Sesión (Auth::login)"/]
    I -- NO --> M

    M --> N{{"¿Rol del usuario?"}}
    N -- admin --> O[/"Redirigir a / (Dashboard Admin)"/]
    N -- student --> P[/"Redirigir a /student/dashboard"/]
    O & P --> Z
```

---

### 6. Diagrama de Flujo — Avance Temático y Gamificación
Representa el algoritmo secuencial del mapa de aprendizaje:

```mermaid
flowchart TD
    A([INICIO]) --> B{{"¿Tiene completada la lección anterior?"}}
    B -- NO, Bloqueada --> C[/"Mostrar lección bloqueada en el mapa"/]
    C --> Z([FIN])

    B -- SÍ --> D[/"Permitir acceso y cargar contenido HTML de la lección"/]
    D --> E[/"Mostrar quiz interactivo al final de la lección"/]
    E --> F[/"Estudiante responde y envía respuestas"/]

    F --> G[["Servidor valida respuestas del formulario"]]
    G --> H["Calcular puntaje y validar aciertos"]
    
    H --> I{{"¿Respuestas 100% correctas?"}}
    I -- NO --> J[/"Permitir reintento del quiz"/]
    J --> E

    I -- SÍ --> K["Registrar lección como completada (lesson_completions)"]
    K --> L["Sumar puntos de experiencia (XP) al perfil del estudiante"]
    L --> M["Actualizar rango militar si acumula XP necesaria"]
    M --> N["Desbloquear la siguiente lección en el mapa secuencial"]
    N --> Z
```

---

### 7. Esquema de la Base de Datos Real (MySQL — Aiven Cloud)
Las siguientes tablas representan el esquema real de la base de datos MySQL desplegada en Aiven Cloud, derivadas directamente de las migraciones de Laravel del proyecto:

```mermaid
erDiagram
    users {
        bigint id PK
        string google_id UK
        string name
        string cedula UK
        string email UK
        string password
        text two_factor_secret
        boolean two_factor_enabled
        string role
        int points
        text google_token
        text google_refresh_token
        timestamp email_verified_at
        string remember_token
        timestamp created_at
        timestamp updated_at
    }

    courses {
        bigint id PK
        string title
        text description
        string category
        string difficulty
        timestamp created_at
        timestamp updated_at
    }

    lessons {
        bigint id PK
        bigint course_id FK
        string title
        longtext content
        int order
        timestamp created_at
        timestamp updated_at
    }

    questions {
        bigint id PK
        bigint lesson_id FK
        text question_text
        int points
        timestamp created_at
        timestamp updated_at
    }

    options {
        bigint id PK
        bigint question_id FK
        text option_text
        boolean is_correct
        timestamp created_at
        timestamp updated_at
    }

    lesson_completions {
        bigint id PK
        bigint user_id FK
        bigint lesson_id FK
        smallint xp_earned
        tinyint accuracy_percent
        timestamp created_at
        timestamp updated_at
    }

    password_reset_otps {
        bigint id PK
        string email
        string otp
        timestamp expires_at
        timestamp created_at
    }

    users ||--o{ lesson_completions : "completa"
    courses ||--o{ lessons : "contiene"
    lessons ||--o{ questions : "tiene"
    lessons ||--o{ lesson_completions : "registrada_en"
    questions ||--o{ options : "tiene"
```

---

## 🛠️ Stack Tecnológico

| Componente | Tecnología | Versión | Función en el Proyecto |
|---|---|---|---|
| **Backend Core** | Laravel | v11.x | Framework principal MVC de la aplicación |
| **Lenguaje Servidor** | PHP | 8.4 | Lenguaje del lado del servidor utilizado |
| **Base de Datos** | MySQL | 8.x | Motor de base de datos relacional en la nube |
| **DBaaS Cloud** | Aiven Cloud | — | Hosting gestionado de MySQL (conexión SSL) |
| **Frontend Engine** | Blade | — | Motor de plantillas dinámicas de Laravel |
| **Maquetado y Estilos** | CSS / Tailwind CSS | — | Framework de estilos y responsive design militar |
| **Autenticación Externa** | Google OAuth | — | Login social seguro para usuarios registrados |
| **Doble Factor (2FA)** | Google Authenticator | — | Generación de tokens OTP y sincronización QR |
| **Contenedores** | Docker | — | Empaquetado del software para el despliegue |
| **Servidor de Producción** | Render Cloud | — | Hosting web del contenedor Docker en producción |
| **Suite de Pruebas** | PHPUnit / Feature Tests | 10.x | Pruebas de integración para autenticación, 2FA y CMS |

---

## 🔑 Acceso al Sistema (Demo)

El seeder del proyecto crea cuentas de prueba automáticamente al ejecutar `php artisan migrate --seed`. Las credenciales reales de producción **no se incluyen aquí por razones de seguridad** — deben ser configuradas por el administrador del sistema en el archivo `.env` y en el seeder correspondiente.

### 👤 Portal del Estudiante (`/login`)
* **Email**: La cuenta demo se define en `database/seeders/StudentPortalSeeder.php`
* **Contraseña**: Configurada en el seeder (ver archivo mencionado)
* **Inicio de sesión alternativo**: Google OAuth disponible

### 🛡️ Portal del Administrador (`/admin/login`)
* **Email**: Definido en `database/seeders/DatabaseSeeder.php`
* **Contraseña**: Configurada por el administrador del proyecto

> ⚠️ **Nota de seguridad:** Si vas a desplegar este proyecto, asegúrate de cambiar las credenciales del seeder y definir contraseñas robustas antes del primer deploy en producción.

---

## 🚀 Instalación y Ejecución Local

### Requisitos Previos
1. **PHP 8.4** instalado en el sistema.
2. **Composer 2.x** configurado en PATH.
3. **MySQL 8.x** (o una cuenta en [Aiven Cloud](https://aiven.io/) para la base de datos remota gratuita).
4. **Git** para control de código.

### Pasos de Configuración

```bash
# 1. Clonar el repositorio
git clone https://github.com/Jose-Sierra082005/sistema-militar-unefa.git
cd sistema-militar-unefa

# 2. Instalar las dependencias de Laravel
composer install

# 3. Copiar las variables de entorno
cp .env.example .env

# 4. Generar la clave única de encriptación
php artisan key:generate

# 5. Configurar la conexión MySQL en el archivo .env
#    Editar .env y completar: DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
#    (Usar credenciales del servicio MySQL en Aiven Cloud)

# 6. Correr las migraciones y poblar con datos iniciales (seeder)
php artisan migrate --seed

# 7. Iniciar el servidor local
php artisan serve
```

El portal de desarrollo estará accesible en: **[http://localhost:8000](http://localhost:8000)**

---

## 🧪 Pruebas y Calidad de Código

Para garantizar la estabilidad y consistencia de las nuevas funcionalidades (recuperación 2FA, CMS de Cursos, Autenticación), puedes ejecutar la suite de pruebas del proyecto:

```bash
# Ejecutar los feature tests
php artisan test

# Comprobar el estándar de código con Laravel Pint
./vendor/bin/pint --test
```

---

## 📖 Documentación Técnica Autogenerada

El código del proyecto se encuentra documentado internamente siguiendo el estándar **PHPDoc**. Puedes autogenerar el sitio web de la documentación técnica (clases, controladores y métodos) ejecutando el siguiente comando con Docker (sin necesidad de instalaciones locales adicionales):

```bash
# Ejecutar desde la raíz del proyecto para generar el sitio web estático en docs/api
docker run --rm -v "${PWD}:/data" phpdoc/phpdoc:3 run -d /data/app -t /data/docs/api --no-interaction
```

Una vez completado, abre el archivo `docs/api/index.html` en cualquier navegador web para explorar la estructura técnica detallada del sistema.

---

## 📄 Licencia y Propósito Académico

Esta aplicación es un **proyecto académico de carácter formativo y sin fines de lucro**, desarrollado para la **UNEFA Núcleo Falcón** en el marco de las materias de *Implantación de Sistemas* y *Metodología*. Todos los derechos sobre la marca **Tactic Force** y su diseño pertenecen a sus respectivos autores.
