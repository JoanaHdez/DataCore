<?php

namespace App\Modules\Asuntos_internos\GestionExcel\Controllers;

use App\Controllers\BaseController;
use App\Modules\Asuntos_internos\GestionExcel\Models\ArchivoModel;
use App\Modules\Asuntos_internos\GestionExcel\Models\BitacoraModel;
use App\Modules\Asuntos_internos\GestionExcel\Services\ExcelDateFormatService;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
use Throwable;
use ZipArchive;

class Archivos_Controller extends BaseController
{
    protected ArchivoModel $archivoModel;
    protected BitacoraModel $bitacoraModel;

    private string $rutaOriginales;
    private string $rutaProcesados;

    public function __construct()
    {
        $this->archivoModel = new ArchivoModel();
        $this->bitacoraModel = new BitacoraModel();

        /*
         * Estas carpetas ahora solamente se usan de manera temporal
         * mientras el archivo se valida y procesa.
         */
        $this->rutaOriginales = WRITEPATH
            . 'asuntos-internos/originales/';

        $this->rutaProcesados = WRITEPATH
            . 'asuntos-internos/procesados/';

        $this->crearDirectorio($this->rutaOriginales);
        $this->crearDirectorio($this->rutaProcesados);
    }

    public function index()
    {
        return view(
            'App\Modules\Asuntos_internos\GestionExcel\Views\archivos\index',
            [
                'archivos' => $this->obtenerArchivosProcesados(),
                'js' => [
                    'main.js',
                    'historial.js',
                ],
            ]
        );
    }

    public function procesar()
    {
        $archivo = $this->request->getFile('archivo_excel');

        if (! $archivo || ! $archivo->isValid()) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Selecciona un archivo de Excel válido.'
                );
        }

        if ($archivo->hasMoved()) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'El archivo ya fue procesado anteriormente.'
                );
        }

        $extension = strtolower(
            $archivo->getClientExtension()
        );

        if (! in_array($extension, ['xlsx', 'xlsm'], true)) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Por seguridad, esta versión acepta archivos .xlsx y .xlsm.'
                );
        }

        if ($archivo->getSize() > 50 * 1024 * 1024) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'El archivo no puede superar los 50 MB.'
                );
        }

        $nombreOriginal = $archivo->getClientName();
        $tipoMime = $archivo->getClientMimeType();

        $nombreOriginalSeguro = bin2hex(
            random_bytes(16)
        ) . '.' . $extension;

        $nombreProcesadoSeguro = bin2hex(
            random_bytes(16)
        ) . '.' . $extension;

        $rutaOriginal = $this->rutaOriginales
            . $nombreOriginalSeguro;

        $rutaProcesada = $this->rutaProcesados
            . $nombreProcesadoSeguro;

        try {
            $archivo->move(
                $this->rutaOriginales,
                $nombreOriginalSeguro
            );

            if (! $this->esExcelValido($rutaOriginal)) {
                $this->eliminarArchivoTemporal($rutaOriginal);

                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'El archivo no contiene una estructura válida de Excel.'
                    );
            }

            $servicio = new ExcelDateFormatService();

            $resultado = $servicio->procesar(
                $rutaOriginal,
                $rutaProcesada
            );

            if (! is_file($rutaProcesada)) {
                throw new RuntimeException(
                    'El archivo procesado no fue generado.'
                );
            }

            $contenidoExcel = file_get_contents(
                $rutaProcesada
            );

            if ($contenidoExcel === false) {
                throw new RuntimeException(
                    'No fue posible leer el archivo procesado.'
                );
            }

            $tamano = filesize($rutaProcesada);

            if ($tamano === false) {
                $tamano = strlen($contenidoExcel);
            }

            $fechaProcesamiento = date('Y-m-d H:i:s');

            $conexion = db_connect();
            $conexion->transStart();

            $idArchivo = $this->archivoModel->insert(
                [
                    'nombre_original' => $nombreOriginal,
                    'extension' => $extension,
                    'tipo_mime' => $tipoMime
                        ?: $this->obtenerTipoMime($extension),
                    'tamano' => $tamano,
                    'archivo_excel' => $contenidoExcel,
                    'total_fechas_modificadas' =>
                        (int) (
                            $resultado['fechas_modificadas']
                            ?? 0
                        ),
                    'estado' => 'completado',
                    'mensaje_error' => null,
                    'fecha_procesamiento' =>
                        $fechaProcesamiento,
                    'created_at' => $fechaProcesamiento,
                ],
                true
            );

            if (! $idArchivo) {
                throw new RuntimeException(
                    'No fue posible registrar el archivo en la base de datos.'
                );
            }

            $this->bitacoraModel->insert([
                'id_archivo' => $idArchivo,
                'accion' => 'PROCESAMIENTO_COMPLETADO',
                'descripcion' =>
                    'El archivo fue procesado y almacenado correctamente.',
                'nivel' => 'INFO',
                'fecha_evento' => $fechaProcesamiento,
            ]);

            $conexion->transComplete();

            if (! $conexion->transStatus()) {
                throw new RuntimeException(
                    'No fue posible completar el registro en la base de datos.'
                );
            }

            /*
             * Los archivos físicos son temporales.
             * El archivo final ya quedó almacenado en la base.
             */
            $this->eliminarArchivoTemporal($rutaOriginal);
            $this->eliminarArchivoTemporal($rutaProcesada);

            return redirect()
                ->to(
                    base_url(
                        'asuntos-internos/archivos'
                    )
                )
                ->with(
                    'success',
                    'El archivo se procesó correctamente.'
                );
        } catch (Throwable $e) {
            $this->eliminarArchivoTemporal($rutaOriginal);
            $this->eliminarArchivoTemporal($rutaProcesada);

            log_message(
                'error',
                'Error procesando Excel: {mensaje}',
                [
                    'mensaje' => $e->getMessage(),
                ]
            );

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'No fue posible procesar el archivo: '
                        . $e->getMessage()
                );
        }
    }

    public function descargar(int $idArchivo)
    {
        $archivo = $this->archivoModel->find($idArchivo);

        if (
            ! $archivo
            || ($archivo['estado'] ?? '') === 'eliminado'
        ) {
            throw PageNotFoundException::forPageNotFound(
                'No se encontró el archivo solicitado.'
            );
        }

        $contenido = $archivo['archivo_excel'] ?? null;

        if (
            $contenido === null
            || $contenido === ''
        ) {
            throw PageNotFoundException::forPageNotFound(
                'El archivo no contiene información para descargar.'
            );
        }

        $nombreDescarga = $this->crearNombreDescarga(
            $archivo
        );

        try {
            $this->bitacoraModel->insert([
                'id_archivo' => $idArchivo,
                'accion' => 'DESCARGA',
                'descripcion' =>
                    'El archivo fue descargado.',
                'nivel' => 'INFO',
                'fecha_evento' => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $e) {
            /*
             * Una falla en la bitácora no debe impedir
             * que el archivo pueda descargarse.
             */
            log_message(
                'error',
                'No se pudo registrar la descarga: {mensaje}',
                [
                    'mensaje' => $e->getMessage(),
                ]
            );
        }

        return $this->response
            ->setHeader(
                'Content-Type',
                $archivo['tipo_mime']
                    ?: $this->obtenerTipoMime(
                        $archivo['extension']
                    )
            )
            ->setHeader(
                'Content-Disposition',
                'attachment; filename="'
                    . addslashes($nombreDescarga)
                    . '"'
            )
            ->setHeader(
                'Content-Length',
                (string) strlen($contenido)
            )
            ->setBody($contenido);
    }

    public function eliminar(int $idArchivo)
    {
        $archivo = $this->archivoModel->find($idArchivo);

        if (! $archivo) {
            return redirect()
                ->to(
                    base_url(
                        'asuntos-internos/archivos'
                    )
                )
                ->with(
                    'error',
                    'No se encontró el archivo que intentas eliminar.'
                );
        }

        $nombreOriginal = $archivo['nombre_original']
            ?? 'Archivo sin nombre';

        try {
            /*
             * La tabla bitacora tiene relación con archivos.
             * Si la llave foránea utiliza ON DELETE CASCADE,
             * las entradas de bitácora también serán eliminadas.
             */
            if (! $this->archivoModel->delete($idArchivo)) {
                throw new RuntimeException(
                    'No fue posible eliminar el registro.'
                );
            }

            return redirect()
                ->to(
                    base_url(
                        'asuntos-internos/archivos'
                    )
                )
                ->with(
                    'delete_success',
                    [
                        'titulo' =>
                            'Archivo eliminado correctamente',
                        'archivo' => $nombreOriginal,
                        'mensaje' =>
                            'La eliminación se completó de forma permanente.',
                    ]
                );
        } catch (Throwable $e) {
            log_message(
                'error',
                'Error eliminando archivo: {mensaje}',
                [
                    'mensaje' => $e->getMessage(),
                ]
            );

            return redirect()
                ->to(
                    base_url(
                        'asuntos-internos/archivos'
                    )
                )
                ->with(
                    'error',
                    'No fue posible eliminar el archivo.'
                );
        }
    }

    private function obtenerArchivosProcesados(): array
    {
        $registros = $this->archivoModel
            ->orderBy('fecha_procesamiento', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return array_map(
            function (array $archivo): array {
                /*
                 * Se conservan algunos nombres usados anteriormente
                 * por la vista para no romperla inmediatamente.
                 */
                $archivo['fechas_modificadas'] =
                    (int) (
                        $archivo['total_fechas_modificadas']
                        ?? 0
                    );

                $archivo['archivo_fisico'] =
                    (string) $archivo['id_archivo'];

                $archivo['nombre_descarga'] =
                    $this->crearNombreDescarga($archivo);

                return $archivo;
            },
            $registros
        );
    }

    private function crearNombreDescarga(
        array $archivo
    ): string {
        $nombreOriginal = $archivo['nombre_original']
            ?? 'archivo.xlsx';

        $nombreBase = pathinfo(
            $nombreOriginal,
            PATHINFO_FILENAME
        );

        $extension = $archivo['extension']
            ?? pathinfo(
                $nombreOriginal,
                PATHINFO_EXTENSION
            );

        $fecha = $archivo['fecha_procesamiento']
            ?? $archivo['created_at']
            ?? date('Y-m-d H:i:s');

        $timestamp = strtotime($fecha);

        $fechaFormateada = $timestamp !== false
            ? date('Y-m-d', $timestamp)
            : date('Y-m-d');

        return $nombreBase
            . '_'
            . $fechaFormateada
            . '.'
            . $extension;
    }

    private function esExcelValido(string $ruta): bool
    {
        $zip = new ZipArchive();

        if ($zip->open($ruta) !== true) {
            return false;
        }

        try {
            return $zip->locateName(
                '[Content_Types].xml'
            ) !== false
                && $zip->locateName(
                    'xl/workbook.xml'
                ) !== false
                && $zip->locateName(
                    'xl/styles.xml'
                ) !== false;
        } finally {
            $zip->close();
        }
    }

    private function obtenerTipoMime(
        string $extension
    ): string {
        return match (strtolower($extension)) {
            'xlsm' =>
                'application/vnd.ms-excel.sheet.macroEnabled.12',

            default =>
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        };
    }

    private function crearDirectorio(string $ruta): void
    {
        if (is_dir($ruta)) {
            return;
        }

        if (
            ! mkdir($ruta, 0775, true)
            && ! is_dir($ruta)
        ) {
            throw new RuntimeException(
                'No fue posible crear el directorio: '
                    . $ruta
            );
        }
    }

    private function eliminarArchivoTemporal(
        string $ruta
    ): void {
        if (is_file($ruta)) {
            @unlink($ruta);
        }
    }
}
