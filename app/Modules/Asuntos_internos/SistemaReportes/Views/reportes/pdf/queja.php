<?php

$reporte =
    $datos['reporte']
    ?? [];


$direccionNotificacion =
    $datos['direccion_notificacion']
    ?? [];


$personal =
    $datos['personal']
    ?? [];


$unidades =
    $datos['unidades']
    ?? [];


$evidencias =
    $datos['evidencias']
    ?? [];


$tipoFolio =
    strtoupper(
        trim(
            (string) (
                $datos['tipo_folio']
                ?? ''
            )
        )
    );


/* =========================================================
   VALOR SEGURO
========================================================= */

$valor = static function (
    array $origen,
    string $campo
): string {

    $valor =
        $origen[$campo]
        ?? '';


    if (
        is_array($valor)
        || is_object($valor)
    ) {

        return '';
    }


    return trim(
        (string) $valor
    );
};


/* =========================================================
   FECHA
========================================================= */

$fecha = static function (
    mixed $valor
): string {

    $valor =
        trim(
            (string) (
                $valor
                ?? ''
            )
        );


    if (
        $valor === ''
    ) {

        return '';
    }


    $timestamp =
        strtotime(
            $valor
        );


    return $timestamp !== false
        ? date(
            'd/m/Y',
            $timestamp
        )
        : $valor;
};


/* =========================================================
   HORA
========================================================= */

$hora = static function (
    mixed $valor
): string {

    $valor =
        trim(
            (string) (
                $valor
                ?? ''
            )
        );


    if (
        $valor === ''
    ) {

        return '';
    }


    $timestamp =
        strtotime(
            $valor
        );


    return $timestamp !== false
        ? date(
            'H:i',
            $timestamp
        )
        : $valor;
};


/* =========================================================
   DIRECCIÓN DE LOS HECHOS
========================================================= */

$direccion = static function (
    array $origen
) use (
    $valor
): string {

    $partes =
        array_filter([
            $valor(
                $origen,
                'calle'
            ),

            $valor(
                $origen,
                'numero_exterior'
            ),

            $valor(
                $origen,
                'colonia'
            ),

            $valor(
                $origen,
                'municipio'
            ),

            $valor(
                $origen,
                'estado'
            ),
        ]);


    return implode(
        ', ',
        $partes
    );
};


/* =========================================================
   ESCAPAR TEXTO
========================================================= */

$linea = static function (
    string $texto
): string {

    return esc(
        $texto
    );
};


/* =========================================================
   QUEJA ANÓNIMA
========================================================= */

$esAnonima =
    (int) (
        $reporte['es_anonimo']
        ?? 0
    ) === 1;


$valorCiudadano = static function (
    string $valorCampo
) use (
    $esAnonima
): string {

    if (
        $esAnonima
    ) {

        return 'ANÓNIMO';
    }


    return $valorCampo;
};


/* =========================================================
   TIPO DE ASUNTO
========================================================= */

$tipoQueja =
    match ($tipoFolio) {

        'QJV' =>
            'QJV',

        'QJF' =>
            'QJF',

        'QJ' =>
            'QJ',

        default =>
            'OTRO',
    };


$marcaTipo = static function (
    string $tipo
) use (
    $tipoQueja
): string {

    return $tipoQueja === $tipo
        ? 'X'
        : ' ';
};


/* =========================================================
   NARRACIÓN
========================================================= */

$narracion =
    $valor(
        $reporte,
        'descripcion_hechos'
    );


$narracionLarga =
    mb_strlen(
        $narracion,
        'UTF-8'
    ) > 1200;


$narracionPrincipal =
    $narracionLarga
        ? mb_substr(
            $narracion,
            0,
            1200,
            'UTF-8'
        )
            . '... (continúa en anexo de narrativa)'
        : $narracion;


/* =========================================================
   PERSONAL
========================================================= */

$nombresPersonal = [];


foreach (
    $personal
    as $persona
) {

    $nombre =
        trim(
            (string) (
                $persona['nombre_snapshot']
                ?? ''
            )
        );


    if (
        $nombre !== ''
    ) {

        $nombresPersonal[] =
            $nombre;
    }
}


/* =========================================================
   UNIDADES
========================================================= */

$textoUnidades = [];


foreach (
    $unidades
    as $unidad
) {

    $componentes = [];


    $noEconomico =
        trim(
            (string) (
                $unidad['no_economico_snapshot']
                ?? ''
            )
        );


    $placas =
        trim(
            (string) (
                $unidad['placas_snapshot']
                ?? ''
            )
        );


    $marca =
        trim(
            (string) (
                $unidad['marca_snapshot']
                ?? ''
            )
        );


    $submarca =
        trim(
            (string) (
                $unidad['submarca_snapshot']
                ?? ''
            )
        );


    if (
        $noEconomico !== ''
    ) {

        $componentes[] =
            $noEconomico;
    }


    if (
        $placas !== ''
        && $placas !== $noEconomico
    ) {

        $componentes[] =
            '(' . $placas . ')';
    }


    if (
        $marca !== ''
        && !in_array(
            $marca,
            $componentes,
            true
        )
    ) {

        $componentes[] =
            $marca;
    }


    if (
        $submarca !== ''
        && $submarca !== $marca
        && !in_array(
            $submarca,
            $componentes,
            true
        )
    ) {

        $componentes[] =
            $submarca;
    }


    $texto =
        trim(
            implode(
                ' ',
                $componentes
            )
        );


    if (
        $texto !== ''
    ) {

        $textoUnidades[] =
            $texto;
    }
}


/* =========================================================
   EVIDENCIAS — DATOS GENERALES
========================================================= */

$textoEvidencias = [];

$tiposEvidencias = [];

$evidenciasFotograficas = [];


foreach (
    $evidencias
    as $evidencia
) {

    /* =====================================================
       NOMBRE
    ===================================================== */

    $nombreEvidencia =
        trim(
            (string) (
                $evidencia['nombre_original']
                ?? $evidencia['nombre_archivo']
                ?? ''
            )
        );


    if (
        $nombreEvidencia !== ''
    ) {

        $textoEvidencias[] =
            $nombreEvidencia;
    }


    /* =====================================================
       TIPO
    ===================================================== */

    $tipoEvidencia =
        strtoupper(
            trim(
                (string) (
                    $evidencia['tipo_evidencia']
                    ?? ''
                )
            )
        );


    $textoTipo =
        match ($tipoEvidencia) {

            'IMAGEN' =>
                'EVIDENCIA FOTOGRÁFICA',

            'VIDEO' =>
                'VIDEO',

            'DOCUMENTO' =>
                'DOCUMENTO',

            default =>
                '',
        };


    if (
        $textoTipo !== ''
    ) {

        $tiposEvidencias[] =
            $textoTipo;
    }


    /* =====================================================
       IMAGEN REAL
    ===================================================== */

    $dataUri =
        trim(
            (string) (
                $evidencia['data_uri']
                ?? ''
            )
        );


    if (
        $tipoEvidencia === 'IMAGEN'
        && $dataUri !== ''
    ) {

        $evidenciasFotograficas[] = [

            'nombre' =>
                $nombreEvidencia,

            'imagen' =>
                $dataUri,

        ];
    }
}


$textoEvidencias =
    array_values(
        array_unique(
            array_filter(
                $textoEvidencias
            )
        )
    );


$tiposEvidencias =
    array_values(
        array_unique(
            array_filter(
                $tiposEvidencias
            )
        )
    );


$tieneEvidencias =
    !empty(
        $evidencias
    );


$tieneEvidenciasFotograficas =
    !empty(
        $evidenciasFotograficas
    );


/* =========================================================
   CSS DEL PDF
========================================================= */

$rutaCss =
    FCPATH
    . 'assets/asuntos_internos/sistema_reportes/css/reportes/pdf/queja.css';


if (
    !is_file(
        $rutaCss
    )
) {

    throw new \RuntimeException(
        'No fue posible localizar el archivo CSS del PDF de queja: '
        . $rutaCss
    );
}


$cssPdf =
    file_get_contents(
        $rutaCss
    );


if (
    $cssPdf === false
) {

    throw new \RuntimeException(
        'No fue posible leer el archivo CSS del PDF de queja.'
    );
}

?>

<!doctype html>

<html lang="es">

<head>

    <meta charset="utf-8">

    <style>
    <?=$cssPdf ?>
    </style>

</head>


<body>


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <?php if (!empty($assets['header'])): ?>

    <div class="header">

        <img src="<?= esc($assets['header']) ?>" alt="">

    </div>

    <?php endif; ?>


    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <?php if (!empty($assets['footer'])): ?>

    <div class="footer">

        <img src="<?= esc($assets['footer']) ?>" alt="">

    </div>

    <?php endif; ?>


    <!-- =====================================================
         WATERMARK
    ====================================================== -->

    <?php if (!empty($assets['watermark'])): ?>

    <img class="watermark" src="<?= esc($assets['watermark']) ?>" alt="">

    <?php endif; ?>


    <!-- =====================================================
         TÍTULO
    ====================================================== -->

    <h1 class="title">
        Formato institucional de queja
    </h1>


    <!-- =====================================================
         AVISO DE PRIVACIDAD
    ====================================================== -->

    <div class="privacy">

        <strong>
            * AVISO DE PRIVACIDAD *
        </strong>

        La Comisaria General de Seguridad Ciudadana
        del Municipio de Nezahualc&oacute;yotl,
        con domicilio en Calle Caballo Bayo, S/N,
        casi esquina con Av. Chimalhuac&aacute;n,
        Colonia Benito Ju&aacute;rez,
        Nezahualc&oacute;yotl,
        Estado de M&eacute;xico,
        es responsable de recabar sus datos personales
        y sensibles,
        del uso que se le d&eacute; a los mismos
        y de su protecci&oacute;n.

        Los datos personales que se recaban en este formato
        son: nombre, direcci&oacute;n, tel&eacute;fono
        y correo electr&oacute;nico,
        que ser&aacute;n tratados con la finalidad de canalizar,
        atender y dar seguimiento a su queja,
        denuncia o inconformidad.

        Asimismo,
        esta Coordinaci&oacute;n de Asuntos Internos
        integra un expediente que documenta
        el procedimiento correspondiente
        y la informaci&oacute;n proporcionada
        ser&aacute; tratada con reserva y confidencialidad
        conforme a la normatividad aplicable.

    </div>


    <!-- =====================================================
         CONSENTIMIENTOS
    ====================================================== -->

    <div class="checks">

        <span class="check"></span>

        Consiento y autorizo que mis datos personales
        sean tratados conforme al presente aviso.

        <br>


        <span class="check"></span>

        Me comprometo a aportar las pruebas necesarias
        para la debida integraci&oacute;n de mi queja.

        <br>


        <span class="check"></span>

        Acepto y protesto que la informaci&oacute;n
        proporcionada es fiel y verdadera.

    </div>


    <!-- =====================================================
         DATOS GENERALES
    ====================================================== -->

    <table class="meta">

        <tr>

            <td class="label">
                Fecha:
            </td>

            <td class="line">
                <?= $linea(
                    $fecha(
                        $reporte['fecha_registro']
                        ?? ''
                    )
                ) ?>
            </td>


            <td class="label">
                Hora:
            </td>

            <td class="line">
                <?= $linea(
                    $hora(
                        $reporte['created_at']
                        ?? $reporte['hora_hechos']
                        ?? ''
                    )
                ) ?>
            </td>


            <td class="label">
                Folio:
            </td>

            <td class="line">
                <?= $linea(
                    $valor(
                        $reporte,
                        'folio'
                    )
                ) ?>
            </td>

        </tr>


        <tr>

            <td class="label">
                Tipo de asunto:
            </td>

            <td class="line" colspan="5">

                Queja
                (<?= $marcaTipo('QJ') ?>)

                &nbsp;&nbsp;

                Queja verbal
                (<?= $marcaTipo('QJV') ?>)

                &nbsp;&nbsp;

                Queja foránea
                (<?= $marcaTipo('QJF') ?>)

                &nbsp;&nbsp;

                Otro
                (<?= $marcaTipo('OTRO') ?>)

            </td>

        </tr>

    </table>


    <!-- =====================================================
         DATOS DEL CIUDADANO
    ====================================================== -->

    <div class="section-title">
        Datos del ciudadano(a)
    </div>


    <table class="grid">

        <tr>

            <td class="
                    label
                    ciudadano-col-label
                ">
                Nombre completo:
            </td>

            <td class="
                    line
                    ciudadano-col-valor
                " colspan="3">
                <?= $linea(
                    $valorCiudadano(
                        $valor(
                            $reporte,
                            'nombre_quejoso'
                        )
                    )
                ) ?>
            </td>


            <td class="label">
                Edad:
            </td>

            <td class="line">
                <?= $linea(
                    $valorCiudadano(
                        $valor(
                            $reporte,
                            'edad_quejoso'
                        )
                    )
                ) ?>
            </td>

        </tr>


        <tr>

            <td class="label">
                Calle:
            </td>

            <td class="line">

                <?php if ($esAnonima): ?>

                ANÓNIMO

                <?php else: ?>

                <?= $linea(
                        $valor(
                            $reporte,
                            'direccion_quejoso'
                        )
                    ) ?>

                <?php endif; ?>

            </td>


            <td class="label">
                Número:
            </td>

            <td class="line">

                <?php if ($esAnonima): ?>

                ANÓNIMO

                <?php endif; ?>

            </td>


            <td class="label">
                Teléfono:
            </td>

            <td class="line">
                <?= $linea(
                    $valorCiudadano(
                        $valor(
                            $reporte,
                            'telefono_quejoso'
                        )
                    )
                ) ?>
            </td>

        </tr>


        <tr>

            <td class="label">
                Correo electrónico:
            </td>

            <td class="line" colspan="5">
                <?= $linea(
                    $valorCiudadano(
                        $valor(
                            $reporte,
                            'correo_quejoso'
                        )
                    )
                ) ?>
            </td>

        </tr>

    </table>


    <!-- =====================================================
         DIRECCIÓN PARA NOTIFICACIÓN
    ====================================================== -->

    <div class="section-title">
        Dirección para notificación
    </div>


    <p class="small-note">

        Llenar cuando el domicilio proporcionado estuviera
        fuera de la jurisdicci&oacute;n del Municipio
        de Nezahualc&oacute;yotl
        o para proporcionar un domicilio dentro del territorio
        para seguimiento oportuno.

    </p>


    <table class="grid">

        <tr>

            <td class="label">
                Calle:
            </td>

            <td class="line">
                <?= $linea(
                    $valor(
                        $direccionNotificacion,
                        'calle'
                    )
                ) ?>
            </td>


            <td class="label">
                Número:
            </td>

            <td class="line">
                <?= $linea(
                    $valor(
                        $direccionNotificacion,
                        'numero_exterior'
                    )
                ) ?>
            </td>

        </tr>


        <tr>

            <td class="label">
                Entre qué calle:
            </td>

            <td class="line">
                <?= $linea(
                    $valor(
                        $direccionNotificacion,
                        'entre_calle'
                    )
                ) ?>
            </td>


            <td class="label">
                Y calle:
            </td>

            <td class="line">
                <?= $linea(
                    $valor(
                        $direccionNotificacion,
                        'y_calle'
                    )
                ) ?>
            </td>

        </tr>


        <tr>

            <td class="label">
                Colonia:
            </td>

            <td class="line">
                <?= $linea(
                    $valor(
                        $direccionNotificacion,
                        'colonia'
                    )
                ) ?>
            </td>


            <td class="label">
                Municipio / Entidad:
            </td>

            <td class="line">
                <?= $linea(
                    trim(
                        $valor(
                            $direccionNotificacion,
                            'municipio'
                        )
                        . ' '
                        . $valor(
                            $direccionNotificacion,
                            'estado'
                        )
                    )
                ) ?>
            </td>

        </tr>

    </table>


    <!-- =====================================================
         HECHOS
    ====================================================== -->

    <div class="section-title">
        Hechos que desea denunciar
    </div>


    <table class="grid">

        <tr>

            <td class="label">
                Lugar donde ocurrieron los hechos:
            </td>

            <td class="line" colspan="3">
                <?= $linea(
                    $direccion(
                        $reporte
                    )
                ) ?>
            </td>

        </tr>


        <tr>

            <td class="label">
                Fecha de los hechos:
            </td>

            <td class="line">
                <?= $linea(
                    $fecha(
                        $reporte['fecha_hechos']
                        ?? ''
                    )
                ) ?>
            </td>


            <td class="label">
                Hora aproximada:
            </td>

            <td class="line">
                <?= $linea(
                    $hora(
                        $reporte['hora_hechos']
                        ?? ''
                    )
                ) ?>
            </td>

        </tr>

    </table>


    <!-- =====================================================
         NARRACIÓN
    ====================================================== -->

    <div class="section-title">
        Narración de los hechos
    </div>


    <div class="narrative">
        <?= nl2br(
            $linea(
                $narracionPrincipal
            )
        ) ?>
    </div>


    <!-- =====================================================
         SERVIDORES PÚBLICOS
    ====================================================== -->

    <div class="section-title">
        Datos del o los servidores públicos
    </div>


    <table class="grid">

        <tr>

            <td class="label">
                Sabe qué unidad fue:
            </td>

            <td class="line">
                <?= $linea(
                    implode(
                        '; ',
                        $textoUnidades
                    )
                ) ?>
            </td>

        </tr>


        <tr>

            <td class="label">
                Nombres de servidores públicos:
            </td>

            <td class="line">
                <?= $linea(
                    implode(
                        '; ',
                        $nombresPersonal
                    )
                ) ?>
            </td>

        </tr>


        <tr>

            <td class="label">
                Cantidad de servidores involucrados:
            </td>

            <td class="line">
                <?= count($personal) > 0
                    ? (string) count($personal)
                    : '' ?>
            </td>

        </tr>


        <tr>

            <td class="label">
                Reconoce a los servidores públicos:
            </td>

            <td class="line"></td>

        </tr>

    </table>


    <!-- =====================================================
         PÁGINA 2
         PRUEBAS / EVIDENCIAS / FIRMA
    ====================================================== -->

    <div class="page-break"></div>


    <!-- =====================================================
         PRUEBAS
    ====================================================== -->

    <div class="section-title section-title--primera">
        Pruebas
    </div>


    <table class="grid">

        <tr>

            <td class="label">
                Tiene pruebas de los hechos denunciados:
            </td>

            <td class="line">
                <?= $tieneEvidencias
                    ? 'SÍ'
                    : 'NO' ?>
            </td>

        </tr>


        <tr>

            <td class="label">
                Tipo de prueba o evidencia:
            </td>

            <td class="line">

                <?php if (!empty($tiposEvidencias)): ?>

                <?= $linea(
                        implode(
                            '; ',
                            $tiposEvidencias
                        )
                    ) ?>

                <?php else: ?>

                NO SE CUENTA CON EVIDENCIAS

                <?php endif; ?>

            </td>

        </tr>


        <tr>

            <td class="label">
                Atendió su queja (unidad y nombre):
            </td>

            <td class="line"></td>

        </tr>

    </table>


    <!-- =====================================================
         EVIDENCIAS FOTOGRÁFICAS
    ====================================================== -->

    <div class="section-title">
        Evidencias fotográficas
    </div>


    <?php if ($tieneEvidenciasFotograficas): ?>

    <table class="evidencias-grid">

        <?php

            $filasEvidencias =
                array_chunk(
                    $evidenciasFotograficas,
                    2
                );

            ?>


        <?php foreach (
                $filasEvidencias
                as $filaIndice => $fila
            ): ?>

        <tr>

            <?php foreach (
                        $fila
                        as $indice => $evidencia
                    ): ?>

            <?php

                        $numeroEvidencia =
                            ($filaIndice * 2)
                            + $indice
                            + 1;

                        ?>

            <td class="evidencia-item">

                <div class="evidencia-imagen">

                    <img src="<?= esc(
                                        $evidencia['imagen']
                                    ) ?>" alt="">

                </div>


                <div class="evidencia-pie">

                    Evidencia <?= esc(
                                    (string) $numeroEvidencia
                                ) ?>


                    <?php if (
                                    trim(
                                        (string) (
                                            $evidencia['nombre']
                                            ?? ''
                                        )
                                    ) !== ''
                                ): ?>

                    <br>

                    <span>

                        <?= esc(
                                            $evidencia['nombre']
                                        ) ?>

                    </span>

                    <?php endif; ?>

                </div>

            </td>

            <?php endforeach; ?>


            <?php if (
                        count(
                            $fila
                        ) === 1
                    ): ?>

            <td class="evidencia-item evidencia-item--vacio"></td>

            <?php endif; ?>

        </tr>

        <?php endforeach; ?>

    </table>

    <?php else: ?>

    <div class="evidencias-vacio">

        <strong>
            NO SE CUENTA CON EVIDENCIAS FOTOGRÁFICAS.
        </strong>

    </div>

    <?php endif; ?>


    <!-- =====================================================
         FIRMA
    ====================================================== -->

    <div class="signature">

        <div class="respect">
            RESPETUOSAMENTE
        </div>


        <div class="signature-line"></div>


        <?php if ($esAnonima): ?>

        <div class="signature-name">
            ANÓNIMO
        </div>

        <?php elseif (
            $valor(
                $reporte,
                'nombre_quejoso'
            ) !== ''
        ): ?>

        <div class="signature-name">

            <?= $linea(
                    $valor(
                        $reporte,
                        'nombre_quejoso'
                    )
                ) ?>

        </div>

        <?php endif; ?>


        <div class="signature-caption">
            NOMBRE Y FIRMA DEL DENUNCIANTE
        </div>

    </div>


    <!-- =====================================================
         ANEXO DE NARRATIVA
    ====================================================== -->

    <?php if ($narracionLarga): ?>

    <div class="page-break"></div>


    <h1 class="title anexo-title">
        Anexo de narrativa
    </h1>


    <div class="narrative">

        <?= nl2br(
                $linea(
                    $narracion
                )
            ) ?>

    </div>


    <div class="signature">

        <div class="respect">
            RESPETUOSAMENTE
        </div>


        <div class="signature-line"></div>


        <?php if ($esAnonima): ?>

        <div class="signature-name">
            ANÓNIMO
        </div>

        <?php elseif (
                $valor(
                    $reporte,
                    'nombre_quejoso'
                ) !== ''
            ): ?>

        <div class="signature-name">

            <?= $linea(
                        $valor(
                            $reporte,
                            'nombre_quejoso'
                        )
                    ) ?>

        </div>

        <?php endif; ?>


        <div class="signature-caption">
            NOMBRE Y FIRMA DEL DENUNCIANTE
        </div>

    </div>

    <?php endif; ?>


</body>

</html>