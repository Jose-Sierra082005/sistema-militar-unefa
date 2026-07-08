<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\Option;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentPortalSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================
        // USUARIOS
        // =========================================================
        // Limpiar usuarios obsoletos o duplicados
        User::whereIn('email', [
            'estudiante@unefa.edu.ve',
            'programador082005@gmail.com',
            'programador082005@gmsil.com',
        ])->delete();

        // Obtener credenciales del administrador desde env o usar fallback seguro
        $adminEmail = env('ADMIN_EMAIL', 'admin@unefa.edu.ve');
        $adminPassword = env('ADMIN_PASSWORD', 'Admin123!');

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Comandante Sierra',
                'password' => $adminPassword,
                'role' => 'admin',
                'points' => 0,
                'two_factor_enabled' => false,
                'two_factor_secret' => null,
            ]
        );

        // Registrar los únicos dos estudiantes autorizados en producción
        User::firstOrCreate(
            ['email' => 'jose.unefa.asignaciones@gmail.com'],
            [
                'name' => 'jose sierra',
                'password' => 'Student123!',
                'role' => 'student',
                'points' => 135,
            ]
        );

        User::firstOrCreate(
            ['email' => 'josedanielsalcedoollarves@gmail.com'],
            [
                'name' => 'José Daniel Salcedo Ollarves',
                'password' => 'Student123!',
                'role' => 'student',
                'points' => 0,
            ]
        );

        $this->command->info('Usuarios autorizados inicializados.');

        // =========================================================
        // LIMPIAR DATOS PREVIOS
        // =========================================================
        Option::query()->delete();
        Question::query()->delete();
        LessonCompletion::query()->delete();
        Lesson::query()->delete();
        Course::query()->delete();

        $this->command->info('Datos previos eliminados.');

        // =========================================================
        // MODULO 1: Introduccion a la FANB
        // =========================================================
        $m1 = Course::create([
            'title' => 'Introduccion a la FANB',
            'description' => 'Conceptos generales de la Fuerza Armada Nacional Bolivariana, jerarquias militares, marco constitucional y el rol del Comandante en Jefe.',
            'category' => 'Doctrina General',
            'difficulty' => 'Basico',
        ]);

        $m1_l1 = Lesson::create([
            'title' => 'Conceptos Generales de la FANB',
            'course_id' => $m1->id,
            'content' => '<p>La <strong>Fuerza Armada Nacional Bolivariana (FANB)</strong> es la institucion armada que tiene como mision fundamental garantizar la independencia y soberania de la Nacion y asegurar la integridad del espacio geografico venezolano. Su fundamento se basa en la defensa militar, la cooperacion en el mantenimiento del orden interno y la participacion activa en el desarrollo nacional.</p>
<h3>Los Cinco Componentes de la FANB</h3>
<p>La FANB esta integrada por cinco componentes esenciales que trabajan de manera conjunta:</p>
<ul>
  <li><strong>El Ejercito Bolivariano.</strong></li>
  <li><strong>La Armada Bolivariana.</strong></li>
  <li><strong>La Aviacion Militar Bolivariana.</strong></li>
  <li><strong>La Guardia Nacional Bolivariana.</strong></li>
  <li><strong>La Milicia Bolivariana</strong> (como componente especial).</li>
</ul>
<h3>Marco Juridico</h3>
<p>Todo el accionar de la FANB se rige por la <strong>Constitucion de la Republica Bolivariana de Venezuela</strong> y la <strong>Ley Organica de la Fuerza Armada Nacional Bolivariana (LOFANB)</strong>.</p>',
            'order' => 1,
        ]);

        $this->seedQ($m1_l1, [
            ['text' => 'Cual es la mision fundamental de la Fuerza Armada Nacional Bolivariana (FANB)?', 'points' => 15, 'opts' => [
                ['Garantizar la independencia, soberania y asegurar la integridad del espacio geografico.', true],
                ['Administrar los recursos economicos del pais.', false],
                ['Exclusivamente el mantenimiento del orden publico en las ciudades.', false],
            ]],
            ['text' => 'Cuantos componentes principales integran la FANB, incluyendo su componente especial?', 'points' => 15, 'opts' => [
                ['5 componentes.', true],
                ['3 componentes.', false],
                ['4 componentes.', false],
            ]],
        ]);

        $m1_l2 = Lesson::create([
            'title' => 'El Rol del Comandante en Jefe',
            'course_id' => $m1->id,
            'content' => '<p>Dentro de la estructura de mando de la FANB, la maxima autoridad es el <strong>Comandante en Jefe</strong>. Segun la Constitucion venezolana, este cargo es ejercido exclusivamente por el <strong>Presidente de la Republica</strong>.</p>
<h3>Funciones Principales del Comandante en Jefe</h3>
<ul>
  <li>Ejercer el mando supremo de las operaciones militares.</li>
  <li>Fijar el contingente de la Fuerza Armada.</li>
  <li>Dirigir las promociones y ascensos de los oficiales de los mas altos grados (Generales y Almirantes).</li>
  <li>Declarar estados de excepcion y ordenar la movilizacion militar ante amenazas a la seguridad de la Nacion.</li>
</ul>
<h3>Cadena de Mando Inmediata</h3>
<p>Inmediatamente por debajo del Comandante en Jefe se encuentran el <strong>Ministro del Poder Popular para la Defensa</strong> y el <strong>Comando Estrategico Operacional (CEOFANB)</strong>, quienes se encargan de la administracion y la operatividad de las fuerzas, respectivamente.</p>',
            'order' => 2,
        ]);

        $this->seedQ($m1_l2, [
            ['text' => 'Segun la ley venezolana, quien ejerce el cargo de Comandante en Jefe de la FANB?', 'points' => 15, 'opts' => [
                ['El Presidente de la Republica.', true],
                ['El Ministro de la Defensa.', false],
                ['El General de mayor antiguedad.', false],
            ]],
            ['text' => 'Cual de las siguientes es una atribucion directa del Comandante en Jefe?', 'points' => 15, 'opts' => [
                ['Ejercer el mando supremo y dirigir los ascensos de los altos grados.', true],
                ['Comandar patrullajes urbanos diarios.', false],
                ['Juzgar delitos civiles.', false],
            ]],
        ]);

        $m1_l3 = Lesson::create([
            'title' => 'Jerarquias y Grados Militares',
            'course_id' => $m1->id,
            'content' => '<p>La FANB es una institucion estrictamente jerarquica, basada en la <strong>disciplina y la obediencia</strong>. Para organizar a su personal, existe un sistema de grados (para Oficiales) y jerarquias (para la Tropa).</p>
<h3>Categorias Principales del Personal Militar</h3>
<h4>1. Oficiales</h4>
<p>Son los profesionales egresados de las academias militares. Se dividen en tres niveles:</p>
<ul>
  <li><strong>Oficiales Subalternos:</strong> Tenientes y Capitanes.</li>
  <li><strong>Oficiales Superiores:</strong> Mayores, Tenientes Coroneles y Coroneles.</li>
  <li><strong>Oficiales Generales y Almirantes:</strong> Nivel mas alto. Grado maximo: <strong>General en Jefe</strong> (Ejercito, Aviacion, GNB) o <strong>Almirante en Jefe</strong> (Armada).</li>
</ul>
<h4>2. Tropa Profesional</h4>
<p>Son los <strong>Sargentos</strong> en sus distintas categorias. Soporte tecnico y tactico de las unidades militares.</p>
<h4>3. Tropa Alistada</h4>
<p>Son los jovenes que prestan el <strong>servicio militar</strong> (Soldados, Alistados).</p>
<h3>Pilar de la Cohesion Militar</h3>
<p>El respeto a la jerarquia y la subordinacion al superior son los pilares fundamentales que mantienen la cohesion y el orden dentro de todos los recintos militares.</p>',
            'order' => 3,
        ]);

        $this->seedQ($m1_l3, [
            ['text' => 'Cual es el grado militar maximo dentro de la FANB para un oficial de comando del Ejercito?', 'points' => 15, 'opts' => [
                ['General en Jefe.', true],
                ['General de Brigada.', false],
                ['Coronel.', false],
            ]],
            ['text' => 'En que categorias principales se divide el personal militar de la FANB?', 'points' => 15, 'opts' => [
                ['Oficiales, Tropa Profesional y Tropa Alistada.', true],
                ['Directores, Supervisores y Empleados.', false],
                ['Generales, Comisarios y Sargentos.', false],
            ]],
        ]);

        $this->command->info('Modulo 1: Introduccion a la FANB - 3 lecciones creadas.');

        // =========================================================
        // MODULO 2: Ejercito Bolivariano
        // =========================================================
        $m2 = Course::create([
            'title' => 'Ejercito Bolivariano',
            'description' => 'El componente terrestre de la FANB: historia, organizacion, unidades de combate y especialidades militares del Ejercito Bolivariano de Venezuela.',
            'category' => 'Operaciones Terrestres',
            'difficulty' => 'Basico',
        ]);

        // --- Seccion 1: Historia, Origen y Mision ---
        $m2_l1 = Lesson::create([
            'title' => 'Historia, Origen y Mision del Ejercito Bolivariano',
            'course_id' => $m2->id,
            'content' => '<p>El <strong>Ejercito Bolivariano</strong> es el componente terrestre de la Fuerza Armada Nacional Bolivariana (FANB) y es considerado el componente <em>"forjador de libertades"</em>. Sus raices historicas mas profundas se encuentran en la <strong>Guerra de Independencia de Venezuela</strong>, consolidando su victoria definitiva en la historica <strong>Batalla de Carabobo el 24 de junio de 1821</strong>, fecha en la que actualmente se celebra el <strong>Dia del Ejercito</strong>.</p>
<h3>Mision Fundamental</h3>
<p>La mision fundamental del Ejercito Bolivariano es <strong>garantizar la defensa terrestre del pais</strong>, asegurar la integridad territorial y participar activamente en el desarrollo nacional. Para ello, planifica, ejecuta y controla las operaciones militares terrestres, operando en diversos ambientes geograficos:</p>
<ul>
  <li>Llanos</li>
  <li>Montanas</li>
  <li>Selvas</li>
  <li>Zonas urbanas</li>
</ul>',
            'order' => 1,
        ]);

        $this->seedQ($m2_l1, [
            ['text' => 'Que evento historico marco la consolidacion de la independencia y se celebra como el Dia del Ejercito Bolivariano?', 'points' => 15, 'opts' => [
                ['La Batalla de Carabobo el 24 de junio de 1821.', true],
                ['La Batalla de Pichincha.', false],
                ['La firma del Acta de la Independencia en 1811.', false],
            ]],
            ['text' => 'Cual es el principal ambito de operacion del Ejercito Bolivariano?', 'points' => 15, 'opts' => [
                ['La defensa terrestre y operaciones en el territorio continental.', true],
                ['El espacio aereo y maritimo.', false],
                ['La defensa de las costas marinas.', false],
            ]],
        ]);

        // --- Seccion 2: Organizacion y Unidades Militares ---
        $m2_l2 = Lesson::create([
            'title' => 'Organizacion y Unidades Militares',
            'course_id' => $m2->id,
            'content' => '<p>Para ser altamente efectivo, el Ejercito Bolivariano tiene una <strong>estructura organizativa escalonada</strong> que le permite desplegarse por todo el territorio nacional. Se organiza en distintas unidades de combate y apoyo, desde las mas grandes hasta las mas pequenas:</p>
<h3>Escalon de Unidades</h3>
<ul>
  <li>
    <strong>Divisiones:</strong> Son las grandes unidades operativas. Existen diferentes tipos segun su especialidad o ubicacion geografica:
    <ul>
      <li>Division de Infanteria</li>
      <li>Division Blindada</li>
      <li>Division de Selva</li>
      <li>Division de Paracaidistas</li>
    </ul>
  </li>
  <li><strong>Brigadas:</strong> Son las unidades tacticas fundamentales, integradas por un conjunto de batallones.</li>
  <li><strong>Batallones:</strong> Son las unidades operativas basicas, generalmente especializadas en una tarea especifica (batallon de tanques, batallon de francotiradores, etc.).</li>
  <li><strong>Companias, Pelotones y Escuadras:</strong> Son las subdivisiones mas pequenas, conformadas por grupos reducidos de soldados bajo el mando de oficiales subalternos o sargentos, disenadas para misiones muy especificas y agiles.</li>
</ul>',
            'order' => 2,
        ]);

        $this->seedQ($m2_l2, [
            ['text' => 'Cual de las siguientes es considerada una "Gran Unidad Operativa" dentro del Ejercito, que a su vez agrupa varias Brigadas?', 'points' => 15, 'opts' => [
                ['La Division.', true],
                ['El Batallon.', false],
                ['El Peloton.', false],
            ]],
            ['text' => 'Como se llama la unidad tactica basica que compone a las Brigadas y se especializa en tareas especificas?', 'points' => 15, 'opts' => [
                ['El Batallon.', true],
                ['La Escuadra.', false],
                ['El Comando Central.', false],
            ]],
        ]);

        // --- Seccion 3: Las Armas (Especialidades) del Ejercito ---
        $m2_l3 = Lesson::create([
            'title' => 'Las Armas y Especialidades del Ejercito',
            'course_id' => $m2->id,
            'content' => '<p>Dentro del Ejercito Bolivariano, el personal militar se especializa en diferentes ramas tecnicas y tacticas conocidas como <strong>"Armas"</strong>. Cada Arma tiene una funcion especifica en el campo de batalla, trabajando en conjunto (<em>armas combinadas</em>) para lograr la victoria:</p>
<h3>Las Armas del Ejercito</h3>
<ul>
  <li>
    <strong>Infanteria:</strong> Es la fuerza de combate a pie o mecanizada. Son los soldados que <strong>ocupan y defienden el terreno</strong> frente a frente.
  </li>
  <li>
    <strong>Caballeria y Blindados:</strong> Es la fuerza de choque y movilidad. <strong>Operan los tanques de guerra y vehiculos blindados de combate</strong>, proporcionando potencia de fuego pesado y rapidez.
  </li>
  <li>
    <strong>Artilleria:</strong> Es el arma de apoyo de fuego a gran distancia. Utilizan <strong>canones, obuses y sistemas de lanzacohetes multiples</strong> para destruir objetivos lejanos antes de que la infanteria avance.
  </li>
  <li>
    <strong>Ingenieria:</strong> Se encargan de la <strong>movilidad militar</strong> (construccion de puentes, limpieza de minas) y <strong>contramovilidad</strong> (destruccion de rutas enemigas, fortificaciones).
  </li>
  <li>
    <strong>Comunicaciones:</strong> Garantizan que las <strong>ordenes e informacion fluyan de forma segura</strong> entre los comandantes y las tropas en el terreno.
  </li>
</ul>',
            'order' => 3,
        ]);

        $this->seedQ($m2_l3, [
            ['text' => 'En un escenario de combate, que "Arma" del Ejercito es responsable de operar los tanques y vehiculos blindados?', 'points' => 15, 'opts' => [
                ['La Caballeria y Blindados.', true],
                ['La Artilleria.', false],
                ['La Ingenieria.', false],
            ]],
            ['text' => 'Cual es la funcion principal del arma de Ingenieria Militar?', 'points' => 15, 'opts' => [
                ['Proveer apoyo de construccion, despejar obstaculos (movilidad) y construir defensas.', true],
                ['Realizar operaciones aereas y rescates.', false],
                ['Atender a los soldados heridos en el hospital militar.', false],
            ]],
        ]);

        $this->command->info('Modulo 2: Ejercito Bolivariano - 3 lecciones creadas.');

        // =========================================================
        // MODULO 3: Armada Bolivariana
        // =========================================================
        $m3 = Course::create([
            'title' => 'Armada Bolivariana',
            'description' => 'El componente naval de la FANB: historia, comandos operativos, grados navales y mision de la Armada Bolivariana de Venezuela.',
            'category' => 'Operaciones Navales',
            'difficulty' => 'Basico',
        ]);

        $m3_l1 = Lesson::create([
            'title' => 'Historia, Origen y Mision de la Armada Bolivariana',
            'course_id' => $m3->id,
            'content' => '<p>La <strong>Armada Bolivariana</strong> es el componente naval de la Fuerza Armada Nacional Bolivariana (FANB). Su genesis se remonta a los primeros anos de la lucha por la independencia, pero su victoria mas trascendental ocurrio en la <strong>Batalla Naval del Lago de Maracaibo el 24 de julio de 1823</strong>. Esta batalla sello definitivamente la independencia de Venezuela frente al imperio espanol y, en honor a ella, cada 24 de julio se celebra el <strong>Dia de la Armada</strong>.</p><h3>Mision Fundamental</h3><p>La mision fundamental de la Armada es <strong>asegurar la defensa naval</strong> y el cumplimiento de la Constitucion y las leyes en los espacios acuaticos del pais. Esto incluye:</p><ul><li>El <strong>Mar Caribe</strong> y el <strong>Oceano Atlantico</strong> (mar territorial y zona economica exclusiva).</li><li>Los <strong>espacios fluviales</strong> (rios) del territorio nacional.</li><li>Los <strong>espacios lacustres</strong> (lagos) del territorio nacional.</li></ul>',
            'order' => 1,
        ]);

        $this->seedQ($m3_l1, [
            ['text' => 'Que batalla historica sello la independencia de Venezuela por via maritima y se conmemora como el Dia de la Armada?', 'points' => 15, 'opts' => [
                ['La Batalla Naval del Lago de Maracaibo el 24 de julio de 1823.', true],
                ['La Batalla de Ayacucho.', false],
                ['El Combate Naval de Los Frailes.', false],
            ]],
            ['text' => 'Ademas del mar territorial, que otros espacios geograficos debe defender y custodiar la Armada Bolivariana?', 'points' => 15, 'opts' => [
                ['Los espacios fluviales (rios) y lacustres (lagos).', true],
                ['Unicamente los puertos internacionales.', false],
                ['Los espacios aereos superiores a los 10.000 pies.', false],
            ]],
        ]);

        $m3_l2 = Lesson::create([
            'title' => 'Comandos y Organizacion Operativa de la Armada',
            'course_id' => $m3->id,
            'content' => '<p>Para abarcar y proteger eficientemente todos los espacios acuaticos, la Armada Bolivariana se divide en <strong>cuatro grandes comandos operativos</strong>:</p><ul><li><strong>Comando de la Escuadra:</strong> Nucleo principal de combate en el mar. Conformado por fragatas misilsticas, patrulleros oceanicos y submarinos.</li><li><strong>Cuerpo de Infanteria de Marina:</strong> Los <em>soldados del mar</em>. Fuerza anfibia especializada en desembarcos en costas y operaciones fluviales y selvaticas.</li><li><strong>Comando de Guardacostas:</strong> Seguridad maritima ciudadana, busqueda y salvamento, proteccion ambiental marina y lucha contra el contrabando y narcotrafico en aguas jurisdiccionales.</li><li><strong>Comando de la Aviacion Naval:</strong> Apoyo aereo a buques y a la Infanteria de Marina mediante aviones y helicopteros para exploracion, rescate y transporte.</li></ul>',
            'order' => 2,
        ]);

        $this->seedQ($m3_l2, [
            ['text' => 'Cual es el comando de la Armada especializado en realizar operaciones anfibias y desembarcos en las costas?', 'points' => 15, 'opts' => [
                ['El Cuerpo de Infanteria de Marina.', true],
                ['El Comando de la Escuadra.', false],
                ['El Comando de Guardacostas.', false],
            ]],
            ['text' => 'Que unidad de la Armada tiene entre sus misiones la busqueda y salvamento en el mar y la proteccion ambiental marina?', 'points' => 15, 'opts' => [
                ['El Comando de Guardacostas.', true],
                ['El Comando de la Aviacion Naval.', false],
                ['La Division de Blindados.', false],
            ]],
        ]);

        $m3_l3 = Lesson::create([
            'title' => 'Los Grados Navales y su Equivalencia',
            'course_id' => $m3->id,
            'content' => '<p>A diferencia del Ejercito, la Aviacion y la GNB, la Armada Bolivariana posee una <strong>nomenclatura unica y tradicional</strong> para sus grados militares. En lugar de "General" o "Coronel", el personal naval usa denominaciones asociadas al mar:</p><h3>Jerarquia Naval</h3><ul><li><strong>Almirantazgo (maximo):</strong> Almirante en Jefe (equiv. General en Jefe), Almirante, Vicealmirante, Contralmirante.</li><li><strong>Oficiales Superiores:</strong> Capitan de Navio (equiv. Coronel), Capitan de Fragata (equiv. Tte. Coronel), Capitan de Corbeta (equiv. Mayor).</li><li><strong>Oficiales Subalternos:</strong> Teniente de Navio, Teniente de Fragata, Alfrez de Navio.</li></ul><p>Esta tradicion naval se mantiene en la mayoria de las armadas del mundo y es un simbolo de identidad y orgullo para los marinos venezolanos.</p>',
            'order' => 3,
        ]);

        $this->seedQ($m3_l3, [
            ['text' => 'Cual es el grado maximo de la jerarquia naval en la Armada Bolivariana, equivalente al General en Jefe?', 'points' => 15, 'opts' => [
                ['Almirante en Jefe.', true],
                ['Capitan de Navio.', false],
                ['Contralmirante.', false],
            ]],
            ['text' => 'A que grado del Ejercito equivale un Capitan de Navio en la Armada Bolivariana?', 'points' => 15, 'opts' => [
                ['A un Coronel.', true],
                ['A un Sargento Mayor.', false],
                ['A un Teniente.', false],
            ]],
        ]);

        $this->command->info('Modulo 3: Armada Bolivariana - 3 lecciones creadas.');

        // =========================================================
        // MODULO 4: Aviacion Militar Bolivariana
        // =========================================================
        $m4 = Course::create([
            'title' => 'Aviacion Militar Bolivariana',
            'description' => 'El componente aereo de la FANB: historia, grupos aereos, defensa aeroespacial y mision de la Aviacion Militar Bolivariana de Venezuela.',
            'category' => 'Operaciones Aereas',
            'difficulty' => 'Intermedio',
        ]);

        // --- Seccion 1: Historia, Origen y Mision ---
        $m4_l1 = Lesson::create([
            'title' => 'Historia, Origen y Mision de la Aviacion Militar Bolivariana',
            'course_id' => $m4->id,
            'content' => '<p>La <strong>Aviacion Militar Bolivariana (AMB)</strong> es el componente de la FANB encargado de proteger el espacio aereo de Venezuela. Sus origenes se remontan al <strong>10 de diciembre de 1920</strong>, cuando se inauguro la <strong>Escuela de Aviacion Militar</strong> en la ciudad de <strong>Maracay, estado Aragua</strong>, conocida historicamente como la <em>"Cuna de la Aviacion Venezolana"</em>. El <strong>Dia de la Aviacion Militar Bolivariana</strong> se conmemora cada <strong>27 de noviembre</strong>.</p><h3>Mision Fundamental</h3><p>La mision fundamental de la AMB es <strong>asegurar la defensa y soberania del espacio aereo nacional</strong>. Esto lo logra mediante la planificacion, ejecucion y control de operaciones aeroespaciales, ademas de brindar:</p><ul><li>Apoyo aereo tactico y logistico a los demas componentes de la FANB (Ejercito, Armada y GNB) durante operaciones conjuntas.</li><li>Asistencia en casos de desastres naturales.</li></ul>',
            'order' => 1,
        ]);

        $this->seedQ($m4_l1, [
            ['text' => 'Que ciudad venezolana es conocida historicamente como la "Cuna de la Aviacion Venezolana"?', 'points' => 15, 'opts' => [
                ['Maracay.', true],
                ['Caracas.', false],
                ['Maracaibo.', false],
            ]],
            ['text' => 'Cual es la mision principal de la Aviacion Militar Bolivariana?', 'points' => 15, 'opts' => [
                ['Asegurar la defensa, control y soberania del espacio aereo nacional.', true],
                ['Administrar los aeropuertos comerciales del pais.', false],
                ['Defender las fronteras terrestres y los rios navegables.', false],
            ]],
        ]);

        // --- Seccion 2: Organizacion y Grupos Aereos ---
        $m4_l2 = Lesson::create([
            'title' => 'Organizacion y Grupos Aereos de la AMB',
            'course_id' => $m4->id,
            'content' => '<p>Para cumplir sus misiones, la Aviacion Militar Bolivariana se organiza en <strong>Grupos Aereos</strong> que operan desde Bases Aereas distribuidas estrategicamente por el territorio nacional. Se dividen segun la funcion de sus aeronaves:</p><ul><li><strong>Grupos Aereos de Caza:</strong> La punta de lanza de la defensa aerea. Operan aviones de alto rendimiento (Sukhoi Su-30MK2, F-16) para combate aire-aire, interceptacion de aeronaves hostiles y bombardeo de precision.</li><li><strong>Grupos Aereos de Transporte:</strong> Responsables de la logistica y movilidad tactica. Utilizan aviones de gran capacidad (C-130 Hercules, Y-8) para trasladar tropas, armamento, suministros y ayuda humanitaria.</li><li><strong>Grupos Aereos de Operaciones Especiales (Helicopteros):</strong> Operan aeronaves de ala rotatoria (Mi-17, Cougar) para rescates, transporte en zonas de dificil acceso y apoyo directo a tropas terrestres.</li><li><strong>Grupos Aereos de Entrenamiento:</strong> Dedicados exclusivamente a la formacion de futuros pilotos militares.</li></ul>',
            'order' => 2,
        ]);

        $this->seedQ($m4_l2, [
            ['text' => 'Como se denominan las principales unidades operativas en las que se organiza la Aviacion Militar Bolivariana?', 'points' => 15, 'opts' => [
                ['Grupos Aereos.', true],
                ['Batallones de Vuelo.', false],
                ['Divisiones Acorazadas.', false],
            ]],
            ['text' => 'Que tipo de Grupo Aereo esta equipado con aeronaves de alto rendimiento para la interceptacion y el combate aire-aire?', 'points' => 15, 'opts' => [
                ['Grupo Aereo de Caza.', true],
                ['Grupo Aereo de Transporte.', false],
                ['Grupo Aereo de Entrenamiento.', false],
            ]],
        ]);

        // --- Seccion 3: Defensa Aeroespacial Integral ---
        $m4_l3 = Lesson::create([
            'title' => 'El CODAI y la Defensa Aeroespacial Integral',
            'course_id' => $m4->id,
            'content' => '<p>El poder de la Aviacion Militar no reside unicamente en sus aviones, sino en su estrecha integracion con la defensa terrestre a traves del <strong>Comando de Defensa Aeroespacial Integral (CODAI)</strong>.</p><h3>Como funciona el CODAI</h3><p>Este sistema combina aeronaves interceptoras con una compleja <strong>red tecnologica en tierra</strong>. El CODAI opera:</p><ul><li><strong>Estaciones de radares de largo alcance</strong> distribuidas por el pais para monitorear el cielo las 24 horas del dia.</li><li><strong>Sistemas de misiles antiaereos</strong> en tierra listos para activarse ante cualquier amenaza.</li><li><strong>Aviones de caza de alerta rapida</strong> para interceptar, identificar o neutralizar amenazas.</li></ul><h3>Protocolo ante una Traza Hostil</h3><p>Si un radar detecta un vuelo ilicito (<em>"traza hostil"</em>, frecuentemente asociado al narcotrafico): los sistemas de misiles en tierra se ponen en alerta y la Aviacion Militar despliega inmediatamente aviones de caza para interceptar o neutralizar la amenaza antes de que vulnere la soberania nacional.</p>',
            'order' => 3,
        ]);

        $this->seedQ($m4_l3, [
            ['text' => 'Que significan las siglas CODAI, el sistema que trabaja en conjunto con la Aviacion Militar?', 'points' => 15, 'opts' => [
                ['Comando de Defensa Aeroespacial Integral.', true],
                ['Comando de Operaciones de Aeronaves Internas.', false],
                ['Centro Oficial de Despliegue Aereo Internacional.', false],
            ]],
            ['text' => 'Que sucede cuando la red de radares detecta una traza hostil o vuelo ilicito en el espacio aereo venezolano?', 'points' => 15, 'opts' => [
                ['Se activan las alarmas y se despachan aviones de caza para interceptar o neutralizar la amenaza.', true],
                ['Se espera a que la aeronave aterrice para interrogar al piloto.', false],
                ['Se notifica exclusivamente a la policia de transito.', false],
            ]],
        ]);

        $this->command->info('Modulo 4: Aviacion Militar Bolivariana - 3 lecciones creadas.');

        // =========================================================
        // MODULO 5: Guardia Nacional Bolivariana (GNB)
        // =========================================================
        $m5 = Course::create([
            'title' => 'Guardia Nacional Bolivariana (GNB)',
            'description' => 'El componente de orden interno de la FANB: historia, organizacion territorial, especialidades y mision de la Guardia Nacional Bolivariana.',
            'category' => 'Orden Interno',
            'difficulty' => 'Basico',
        ]);

        // --- Seccion 1: Historia, Origen y Mision ---
        $m5_l1 = Lesson::create([
            'title' => 'Historia, Origen y Mision de la GNB',
            'course_id' => $m5->id,
            'content' => '<p>La <strong>Guardia Nacional Bolivariana (GNB)</strong> fue fundada el <strong>4 de agosto de 1937</strong> por el entonces presidente de la Republica, el <strong>General en Jefe Eleazar Lopez Contreras</strong>. Fue creada bajo la necesidad de tener una fuerza profesionalizada que garantizara la seguridad publica y el orden en todo el territorio. Su lema institucional historico es <em>"El Honor es su divisa"</em>.</p><h3>Mision Especifica</h3><p>A diferencia del Ejercito, la Armada o la Aviacion, que se enfocan en la defensa ante amenazas externas, la mision especifica de la GNB es <strong>conducir las operaciones exigidas para el mantenimiento del orden interno del pais</strong>. Ademas, coopera activamente en el desarrollo de las operaciones militares y participa en funciones de:</p><ul><li>Policia administrativa.</li><li>Investigacion penal bajo la direccion de los entes competentes.</li></ul>',
            'order' => 1,
        ]);

        $this->seedQ($m5_l1, [
            ['text' => 'Que presidente venezolano fundo la Guardia Nacional el 4 de agosto de 1937?', 'points' => 15, 'opts' => [
                ['Eleazar Lopez Contreras.', true],
                ['Simon Bolivar.', false],
                ['Marcos Perez Jimenez.', false],
            ]],
            ['text' => 'Cual es la mision especifica y principal de la Guardia Nacional Bolivariana dentro de la FANB?', 'points' => 15, 'opts' => [
                ['El mantenimiento del orden interno del pais.', true],
                ['La defensa exclusiva del espacio aereo.', false],
                ['La construccion de infraestructura vial.', false],
            ]],
        ]);

        // --- Seccion 2: Organizacion y Despliegue Territorial ---
        $m5_l2 = Lesson::create([
            'title' => 'Organizacion y Despliegue Territorial de la GNB',
            'course_id' => $m5->id,
            'content' => '<p>Para garantizar el orden interno en cada rincon de Venezuela, la GNB posee un <strong>despliegue territorial muy extenso</strong>, organizado en unidades que se adaptan a la division politico-territorial del pais:</p><ul><li><strong>Comandos de Zona:</strong> Unidades operativas de mayor nivel jerarquico a nivel regional. Generalmente, existe un Comando de Zona por cada estado del pais (por ejemplo, el Comando de Zona N&deg; 43 corresponde al Distrito Capital).</li><li><strong>Destacamentos:</strong> Cada Comando de Zona agrupa varios Destacamentos. Son las unidades tacticas encargadas de un sector, municipio o eje carretero especifico.</li><li><strong>Companias y Pelotones:</strong> Unidades mas pequenas bajo los Destacamentos. Son las encargadas de ejecutar los patrullajes diarios, establecer los Puntos de Atencion al Ciudadano (PAC) —comunmente conocidos como alcabalas— y realizar el resguardo local.</li></ul>',
            'order' => 2,
        ]);

        $this->seedQ($m5_l2, [
            ['text' => 'Como se denominan las unidades operativas principales de la GNB asignadas a cada estado del pais?', 'points' => 15, 'opts' => [
                ['Comandos de Zona.', true],
                ['Brigadas de Selva.', false],
                ['Comisarias Regionales.', false],
            ]],
            ['text' => 'Que unidades tacticas agrupan a las companias y controlan un municipio o eje carretero especifico?', 'points' => 15, 'opts' => [
                ['Los Destacamentos.', true],
                ['Los Ministerios.', false],
                ['Los Grupos Aereos.', false],
            ]],
        ]);

        // --- Seccion 3: Especialidades y Servicios ---
        $m5_l3 = Lesson::create([
            'title' => 'Especialidades y Servicios de la GNB',
            'course_id' => $m5->id,
            'content' => '<p>La GNB cuenta con personal <strong>altamente especializado</strong> para abarcar diferentes areas criticas del Estado venezolano. Entre sus comandos y servicios mas importantes destacan:</p><ul><li><strong>Orden Publico:</strong> Unidades especializadas encargadas del control de multitudes y el restablecimiento de la paz ciudadana ante alteraciones graves.</li><li><strong>Comando Nacional Antiextorsion y Secuestro (CONAS):</strong> Unidad tactica de elite altamente capacitada en inteligencia y operaciones de rescate, dedicada exclusivamente a combatir la extorsion y el secuestro.</li><li><strong>Guarderia Ambiental:</strong> Servicio dedicado a la proteccion de recursos naturales, prevencion de la tala ilicita, la mineria ilegal y la conservacion de parques nacionales.</li><li><strong>Comando Nacional Antidrogas:</strong> Especializados en la deteccion, intercepcion y combate del trafico ilicito de sustancias estupefacientes y psicotropicas.</li><li><strong>Resguardo Nacional:</strong> Control aduanero y tributario en fronteras y puertos, evitando el contrabando.</li></ul>',
            'order' => 3,
        ]);

        $this->seedQ($m5_l3, [
            ['text' => 'Que significan las siglas CONAS, una de las unidades de elite de la GNB?', 'points' => 15, 'opts' => [
                ['Comando Nacional Antiextorsion y Secuestro.', true],
                ['Centro de Operaciones Navales y Aereas del Sur.', false],
                ['Cuerpo Nacional de Atencion Sanitaria.', false],
            ]],
            ['text' => 'Cual es el servicio de la GNB encargado de proteger los recursos naturales, parques nacionales y prevenir la mineria ilegal?', 'points' => 15, 'opts' => [
                ['La Guarderia Ambiental.', true],
                ['El Resguardo Nacional.', false],
                ['El Comando de Orden Publico.', false],
            ]],
        ]);

        $this->command->info('Modulo 5: Guardia Nacional Bolivariana (GNB) - 3 lecciones creadas.');

        // =========================================================
        // MODULO 6: Milicia Bolivariana
        // =========================================================
        $m6 = Course::create([
            'title' => 'Milicia Bolivariana',
            'description' => 'El componente especial de la FANB: naturaleza, organizacion territorial basada en las ADI y el principio de la Union Civico-Militar.',
            'category' => 'Defensa Popular',
            'difficulty' => 'Basico',
        ]);

        // --- Seccion 1: Naturaleza y Mision ---
        $m6_l1 = Lesson::create([
            'title' => 'Naturaleza y Mision de la Milicia Bolivariana',
            'course_id' => $m6->id,
            'content' => '<p>La <strong>Milicia Bolivariana</strong> es un cuerpo especial organizado por el Estado venezolano. Segun la <strong>Ley Organica de la Fuerza Armada Nacional Bolivariana (LOFANB)</strong>, tiene la categoria de <strong>"Componente Especial"</strong> de la FANB. Su creacion y consolidacion moderna fue impulsada para materializar el concepto de la <em>"defensa integral de la Nacion"</em>.</p><h3>Mision Fundamental</h3><p>La mision fundamental de la Milicia es <strong>registrar, organizar, equipar, adiestrar y adoctrinar al pueblo</strong> a objeto de contribuir con la seguridad de la Nacion en todos los niveles. A diferencia de los otros cuatro componentes (Ejercito, Armada, Aviacion y GNB), la Milicia <strong>no esta conformada por militares de carrera a tiempo completo</strong>, sino por <strong>ciudadanos voluntarios</strong> que se preparan para complementar y apoyar a la Fuerza Armada en caso de movilizacion o estado de excepcion.</p>',
            'order' => 1,
        ]);

        $this->seedQ($m6_l1, [
            ['text' => 'Segun la ley militar venezolana (LOFANB), que categoria tiene la Milicia Bolivariana dentro de la FANB?', 'points' => 15, 'opts' => [
                ['Un Componente Especial.', true],
                ['Un cuerpo policial de transito.', false],
                ['El primer componente regular.', false],
            ]],
            ['text' => 'Quienes conforman principalmente las filas de la Milicia Bolivariana?', 'points' => 15, 'opts' => [
                ['Ciudadanos voluntarios que se entrenan para apoyar la defensa del pais.', true],
                ['Oficiales asimilados de otros paises.', false],
                ['Unicamente jovenes menores de 18 anos.', false],
            ]],
        ]);

        // --- Seccion 2: Organizacion Territorial (Las ADI) ---
        $m6_l2 = Lesson::create([
            'title' => 'Organizacion Territorial: Las Areas de Defensa Integral (ADI)',
            'course_id' => $m6->id,
            'content' => '<p>Debido a que su objetivo es la defensa desde las comunidades, la Milicia Bolivariana no se organiza en batallones tradicionales, sino que tiene una <strong>estructura profundamente territorializada</strong> que abarca parroquias y municipios.</p><h3>Niveles de Organizacion</h3><ul><li><strong>Areas de Defensa Integral (ADI):</strong> El territorio nacional esta dividido en decenas de ADI. Cada una agrupa a los milicianos de esa jurisdiccion especifica.</li><li><strong>Unidades Populares de Defensa Integral (UPDI):</strong> A nivel mas micro, en los barrios, campos y urbanizaciones, la Milicia se organiza a traves de las UPDI.</li><li><strong>Cuadrillas de Defensa Integral:</strong> La subdivision mas pequena, que permite la defensa en bloques o manzanas especificas.</li></ul><p>Esta estructura busca que cada miliciano defienda su <strong>propio sector geografico</strong>, ya que es el terreno que mejor conoce.</p>',
            'order' => 2,
        ]);

        $this->seedQ($m6_l2, [
            ['text' => 'Que significan las siglas ADI, que representan la base de la organizacion territorial de la Milicia?', 'points' => 15, 'opts' => [
                ['Areas de Defensa Integral.', true],
                ['Asociacion de Despliegue Internacional.', false],
                ['Agrupacion de Destacamentos Internos.', false],
            ]],
            ['text' => 'A nivel de comunidades, barrios y urbanizaciones, como se agrupan los milicianos?', 'points' => 15, 'opts' => [
                ['En Unidades Populares de Defensa Integral (UPDI).', true],
                ['En Escuadrones de Vuelo.', false],
                ['En Flotas Maritimas Comunitarias.', false],
            ]],
        ]);

        // --- Seccion 3: La Union Civico-Militar ---
        $m6_l3 = Lesson::create([
            'title' => 'La Union Civico-Militar como Pilar Estrategico',
            'course_id' => $m6->id,
            'content' => '<p>El pilar ideologico y estrategico que sostiene a la Milicia Bolivariana es el principio de la <strong>Union Civico-Militar</strong>. Este concepto establece que la defensa de la soberania y la seguridad del pais <strong>no es responsabilidad exclusiva de los militares profesionales</strong>, sino que es un <strong>deber compartido con todos los ciudadanos civiles</strong>.</p><h3>Como se Prepara un Miliciano</h3><p>Los milicianos (que en su vida diaria son estudiantes, trabajadores, campesinos o profesionales) asisten periodicamente a las unidades militares, generalmente los fines de semana, para recibir instruccion en:</p><ul><li>Orden cerrado (formacion y desfile militar).</li><li>Manejo de armamento ligero.</li><li>Primeros auxilios.</li><li>Tacticas de resistencia territorial.</li></ul><p>Esta preparacion busca crear un <strong>puente solido entre la sociedad civil y las instituciones militares regulares</strong>.</p>',
            'order' => 3,
        ]);

        $this->seedQ($m6_l3, [
            ['text' => 'Cual es el principio estrategico fundamental que justifica la existencia de la Milicia Bolivariana?', 'points' => 15, 'opts' => [
                ['La Union Civico-Militar.', true],
                ['El aislamiento de las fuerzas armadas.', false],
                ['La privatizacion de la seguridad.', false],
            ]],
            ['text' => 'Cual es la responsabilidad principal de un miliciano durante su entrenamiento?', 'points' => 15, 'opts' => [
                ['Aprender tacticas de combate, orden cerrado y primeros auxilios para complementar a la FANB.', true],
                ['Construir infraestructura civil sin supervision.', false],
                ['Patrullar los mares internacionales.', false],
            ]],
        ]);

        $this->command->info('Modulo 6: Milicia Bolivariana - 3 lecciones creadas.');

        // =========================================================
        // MODULO 7: Inteligencia y Contrainteligencia Militar (DGCIM)
        // =========================================================
        $m7 = Course::create([
            'title' => 'Inteligencia y Contrainteligencia Militar (DGCIM)',
            'description' => 'El maximo organismo de inteligencia de la FANB: historia, diferencia entre inteligencia y contrainteligencia, y jurisdiccion de la DGCIM.',
            'category' => 'Inteligencia Militar',
            'difficulty' => 'Avanzado',
        ]);

        // --- Seccion 1: Historia, Evolucion y Mision ---
        $m7_l1 = Lesson::create([
            'title' => 'Historia, Evolucion y Mision de la DGCIM',
            'course_id' => $m7->id,
            'content' => '<p>La <strong>Direccion General de Contrainteligencia Militar (DGCIM)</strong> es el maximo organismo de inteligencia y contrainteligencia de la Fuerza Armada Nacional Bolivariana. Historicamente, esta institucion era conocida como la <strong>Direccion de Inteligencia Militar (DIM)</strong>. Sin embargo, evoluciono y cambio su denominacion para enfocar su doctrina primordialmente en la <em>"contrainteligencia"</em>.</p><h3>Mision Central</h3><p>Su mision central es <strong>descubrir, prevenir y neutralizar</strong> cualquier acto de:</p><ul><li>Espionaje</li><li>Sabotaje</li><li>Subversion</li><li>Traicion a la patria</li><li>Cualquier amenaza que ponga en riesgo la seguridad de la FANB, el personal militar, el material de guerra y la defensa integral de la Nacion.</li></ul><p>Responde directamente al <strong>Ministerio del Poder Popular para la Defensa</strong> y al <strong>Comandante en Jefe</strong>.</p>',
            'order' => 1,
        ]);

        $this->seedQ($m7_l1, [
            ['text' => 'Con que siglas era conocido historicamente este organismo militar antes de cambiar su enfoque a la contrainteligencia?', 'points' => 15, 'opts' => [
                ['DIM (Direccion de Inteligencia Militar).', true],
                ['SEBIN.', false],
                ['CICPC.', false],
            ]],
            ['text' => 'Cual es la mision principal de la DGCIM?', 'points' => 15, 'opts' => [
                ['Prevenir y neutralizar el espionaje, sabotaje y amenazas contra la FANB.', true],
                ['Administrar el presupuesto economico del Estado.', false],
                ['Investigar delitos comunes como el robo de vehiculos.', false],
            ]],
        ]);

        // --- Seccion 2: Inteligencia vs. Contrainteligencia ---
        $m7_l2 = Lesson::create([
            'title' => 'Inteligencia vs. Contrainteligencia: Conceptos Clave',
            'course_id' => $m7->id,
            'content' => '<p>Para comprender la labor de la DGCIM, es vital diferenciar dos conceptos operativos clave en el ambito militar:</p><h3>La Inteligencia</h3><p>Es la accion de <strong>buscar, recolectar, procesar y analizar informacion</strong> sobre las capacidades, debilidades y planes del enemigo o adversario. Es una accion de <em>"busqueda"</em> activa hacia el exterior.</p><h3>La Contrainteligencia</h3><p>Es el <em>"escudo"</em>. Es el conjunto de medidas que se toman para:</p><ul><li><strong>Proteger la informacion propia</strong> y al personal militar.</li><li><strong>Proteger las instalaciones</strong> de la FANB.</li><li>Evitar que el enemigo obtenga nuestra informacion clasificada (<em>espionaje</em>).</li><li>Evitar que logre infiltrarse en nuestras filas para destruir equipos (<em>sabotaje</em>) o alterar la moral de las tropas (<em>subversion</em>).</li></ul>',
            'order' => 2,
        ]);

        $this->seedQ($m7_l2, [
            ['text' => 'En el ambito militar, que se define como la recoleccion y analisis de informacion sobre las capacidades y planes del enemigo?', 'points' => 15, 'opts' => [
                ['La Inteligencia.', true],
                ['La Logistica.', false],
                ['La Contrainteligencia.', false],
            ]],
            ['text' => 'Cual es el objetivo principal de la "Contrainteligencia"?', 'points' => 15, 'opts' => [
                ['Proteger la informacion propia y evitar el espionaje y la infiltracion enemiga.', true],
                ['Publicar los planes de defensa en los periodicos nacionales.', false],
                ['Abastecer de alimentos a las unidades de combate.', false],
            ]],
        ]);

        // --- Seccion 3: Ambito de Accion y Jurisdiccion ---
        $m7_l3 = Lesson::create([
            'title' => 'Ambito de Accion y Jurisdiccion de la DGCIM',
            'course_id' => $m7->id,
            'content' => '<p>El ambito de accion de la DGCIM es <strong>estrictamente de caracter y jurisdiccion militar</strong>. Su trabajo se concentra dentro de los cuarteles, bases aereas, fuertes, bases navales y comandos de todo el pais.</p><h3>Responsabilidades de la DGCIM</h3><ul><li>Investigar al <strong>personal militar activo</strong> o en situacion de reserva.</li><li>Proteger las <strong>redes de comunicacion</strong> de la FANB.</li><li>Salvaguardar los <strong>planes estrategicos</strong> y el armamento de la FANB.</li></ul><h3>Casos Excepcionales con Civiles</h3><p>Aunque su naturaleza es militar, la DGCIM tiene la potestad de investigar a ciudadanos civiles <strong>unica y exclusivamente</strong> cuando estos se ven involucrados de forma directa en delitos de caracter militar, tales como:</p><ul><li>Espionaje a favor de potencias extranjeras.</li><li>Robo de armamento de guerra.</li><li>Ataques contra instalaciones de la Fuerza Armada.</li></ul>',
            'order' => 3,
        ]);

        $this->seedQ($m7_l3, [
            ['text' => 'Cual es el ambito principal de accion y jurisdiccion de la DGCIM?', 'points' => 15, 'opts' => [
                ['Estrictamente militar (proteccion de cuarteles, bases y personal de la FANB).', true],
                ['Los asuntos civiles y comerciales de los ciudadanos.', false],
                ['Exclusivamente la proteccion ambiental.', false],
            ]],
            ['text' => 'En que caso excepcional la DGCIM puede investigar a un ciudadano civil?', 'points' => 15, 'opts' => [
                ['Cuando esta involucrado en espionaje, robo de armas de guerra o ataques a instalaciones militares.', true],
                ['Cuando un civil comete una infraccion de transito.', false],
                ['Cuando un civil tiene deudas pendientes con una entidad bancaria.', false],
            ]],
        ]);

        $this->command->info('Modulo 7: Inteligencia y Contrainteligencia Militar (DGCIM) - 3 lecciones creadas.');

        // =========================================================
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info(' 7 modulos FANB sembrados exitosamente. ');
        $this->command->info('   TODOS LOS MODULOS COMPLETOS (M1-M7)  ');
        $this->command->info('   21 lecciones | 42 preguntas totales  ');
        $this->command->info('========================================');
    }

    /**
     * Siembra preguntas y opciones para una leccion.
     * Formato: opts es array de [texto, es_correcta]
     */
    private function seedQ(Lesson $lesson, array $questions): void
    {
        foreach ($questions as $qData) {
            $exists = Question::where('lesson_id', $lesson->id)
                ->where('question_text', $qData['text'])
                ->exists();
            if ($exists) {
                continue;
            }

            $q = Question::create([
                'lesson_id' => $lesson->id,
                'question_text' => $qData['text'],
                'points' => $qData['points'],
            ]);

            foreach ($qData['opts'] as [$text, $correct]) {
                Option::create([
                    'question_id' => $q->id,
                    'option_text' => $text,
                    'is_correct' => $correct,
                ]);
            }
        }
    }
}
