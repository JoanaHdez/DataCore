<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations and Seeds directories.
     */
    public string $filesPath =
    APPPATH
        . 'Database'
        . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to use if no other is specified.
     */
    public string $defaultGroup =
    'default';

    /**
     * The default database connection.
     *
     * Esta conexión continúa siendo utilizada por el proyecto
     * existente. Los valores reales se sobrescriben desde .env.
     *
     * @var array<string, mixed>
     */
    public array $default = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => '',
        'password'     => '',
        'database'     => '',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
        'foundRows'    => false,

        'dateFormat' => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    /**
     * DataCore.
     *
     * Base de datos propia de la plataforma.
     *
     * Sistema de Reportes de Asuntos Internos utiliza aquí:
     *
     * - usuarios locales
     * - roles
     * - permisos
     * - reportes
     * - personal relacionado
     * - unidades relacionadas
     * - evidencias
     * - seguimientos
     * - historial
     * - auditoría
     *
     * IMPORTANTE:
     *
     * Esta conexión es independiente de "default".
     *
     * Los valores reales de:
     *
     * - hostname
     * - database
     * - username
     * - password
     * - port
     *
     * se sobrescriben desde .env mediante database.datacore.*
     *
     * @var array<string, mixed>
     */
    public array $datacore = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => '',
        'password'     => '',
        'database'     => '',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_unicode_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
        'foundRows'    => false,

        'dateFormat' => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    /**
     * Plantilla General.
     *
     * Fuente externa utilizada para:
     *
     * - autenticación
     * - nómina
     * - nombre
     * - área
     * - turno
     * - PERSCOD
     * - fotografía
     * - identificación del personal
     *
     * La contraseña/CURP se consulta únicamente para
     * autenticación y autorización. No se almacena en DataCore.
     *
     * Esta conexión continúa utilizando el mismo servidor
     * de la conexión default.
     *
     * @var array<string, mixed>
     */
    public array $plantilla = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => '',
        'password'     => '',
        'database'     => '',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'latin1',
        'DBCollat'     => 'latin1_swedish_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
        'foundRows'    => false,

        'dateFormat' => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    /**
     * Parque Vehicular.
     *
     * Fuente externa utilizada para consultar las unidades
     * disponibles en:
     *
     * puestasyremisiones.parque_vehicular
     *
     * Esta conexión continúa utilizando el mismo servidor
     * de la conexión default.
     *
     * @var array<string, mixed>
     */
    public array $unidades = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => '',
        'password'     => '',
        'database'     => '',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8',
        'DBCollat'     => 'utf8_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
        'foundRows'    => false,

        'dateFormat' => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    /**
     * Base territorial de Prevención del Delito.
     *
     * Fuente externa utilizada para obtener información
     * territorial a partir de coordenadas.
     *
     * Datos disponibles:
     *
     * - sector
     * - cuadrante
     * - ID de cuadra / calle
     * - calle
     * - entre calle
     * - y calle
     * - colonia
     *
     * Procedimiento utilizado:
     *
     * getDireccionData(longitud, latitud)
     *
     * IMPORTANTE:
     *
     * Esta conexión utiliza un servidor distinto al de
     * las demás conexiones y mantiene su configuración propia.
     *
     * @var array<string, mixed>
     */
    public array $territorio = [
        'DSN'          => '',

        'hostname'     => '172.16.28.215',

        'username'     => 'sergio.mendez',

        'password'     => '#$Coordin@cion.C3PyC@23',

        'database'     => 'prevencion_delito',

        'DBDriver'     => 'MySQLi',

        'DBPrefix'     => '',

        'pConnect'     => false,

        'DBDebug'      => true,

        'charset'      => 'utf8mb4',

        'DBCollat'     => 'utf8mb4_general_ci',

        'swapPre'      => '',

        'encrypt'      => false,

        'compress'     => false,

        'strictOn'     => false,

        'failover'     => [],

        'port'         => 3307,

        'numberNative' => false,

        'foundRows'    => false,

        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    /**
     * This database connection is used when running PHPUnit database tests.
     *
     * @var array<string, mixed>
     */
    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => '',
        'swapPre'     => '',
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
        'synchronous' => null,

        'dateFormat'  => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    /* =========================================================
       CONSTRUCTOR
    ========================================================= */

    public function __construct()
    {
        parent::__construct();

        /*
         * =========================================================
         * CONEXIONES ADICIONALES
         * =========================================================
         *
         * DATACORE:
         *
         * Ya NO se construye desde default.
         *
         * Su servidor, base, usuario, contraseña y puerto
         * se obtienen de:
         *
         * database.datacore.*
         *
         * definido en .env.
         *
         *
         * PLANTILLA GENERAL y PARQUE VEHICULAR:
         *
         * continúan en el mismo servidor MySQL que la
         * conexión default.
         *
         * Por eso reutilizan:
         *
         * - hostname
         * - username
         * - password
         * - puerto
         * - driver
         *
         * y únicamente cambian la base de datos.
         *
         *
         * TERRITORIO:
         *
         * mantiene su conexión independiente.
         */

        /* =====================================================
           PLANTILLA GENERAL
        ===================================================== */

        $this->plantilla =
            array_replace(
                $this->default,
                [
                    'hostname' =>
                    env(
                        'database.plantilla.hostname',
                        $this->default['hostname']
                    ),

                    'database' =>
                    env(
                        'database.plantilla.database',
                        'plantilla_general'
                    ),

                    'username' =>
                    env(
                        'database.plantilla.username',
                        $this->default['username']
                    ),

                    'password' =>
                    env(
                        'database.plantilla.password',
                        $this->default['password']
                    ),

                    'DBDriver' =>
                    env(
                        'database.plantilla.DBDriver',
                        $this->default['DBDriver']
                    ),

                    'port' =>
                    (int) env(
                        'database.plantilla.port',
                        $this->default['port']
                    ),

                    'DBPrefix' =>
                    env(
                        'database.plantilla.DBPrefix',
                        $this->default['DBPrefix']
                    ),

                    'charset' =>
                    env(
                        'database.plantilla.charset',
                        'latin1'
                    ),

                    'DBCollat' =>
                    env(
                        'database.plantilla.DBCollat',
                        'latin1_swedish_ci'
                    ),
                ]
            );

        /* =====================================================
           PARQUE VEHICULAR
        ===================================================== */

        $this->unidades =
            array_replace(
                $this->default,
                [
                    'hostname' =>
                    env(
                        'database.unidades.hostname',
                        $this->default['hostname']
                    ),

                    'database' =>
                    env(
                        'database.unidades.database',
                        'puestasyremisiones'
                    ),

                    'username' =>
                    env(
                        'database.unidades.username',
                        $this->default['username']
                    ),

                    'password' =>
                    env(
                        'database.unidades.password',
                        $this->default['password']
                    ),

                    'DBDriver' =>
                    env(
                        'database.unidades.DBDriver',
                        $this->default['DBDriver']
                    ),

                    'port' =>
                    (int) env(
                        'database.unidades.port',
                        $this->default['port']
                    ),

                    'DBPrefix' =>
                    env(
                        'database.unidades.DBPrefix',
                        $this->default['DBPrefix']
                    ),

                    'charset' =>
                    env(
                        'database.unidades.charset',
                        'utf8'
                    ),

                    'DBCollat' =>
                    env(
                        'database.unidades.DBCollat',
                        'utf8_general_ci'
                    ),
                ]
            );

        /* =====================================================
           TERRITORIO
        ===================================================== */

        $this->territorio =
            $this->unidades;



        /*
         * =====================================================
         * TERRITORIO
         * =====================================================
         *
         * NO utilizamos array_replace($this->default).
         *
         * La propiedad $territorio ya contiene:
         *
         * - servidor propio
         * - usuario propio
         * - contraseña propia
         * - puerto 3307
         * - base prevencion_delito
         *
         * Por lo tanto se utiliza directamente mediante:
         *
         * \Config\Database::connect('territorio')
         */

        /* =====================================================
           TESTS
        ===================================================== */

        if (
            ENVIRONMENT === 'testing'
        ) {
            $this->defaultGroup =
                'tests';
        }
    }
}
