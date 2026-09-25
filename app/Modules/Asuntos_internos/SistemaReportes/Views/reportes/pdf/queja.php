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
    $datos['tipo_folio']
    ?? '';

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
        ? date('d/m/Y', $timestamp)
        : $valor;
};

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
        ? date('H:i', $timestamp)
        : $valor;
};

$direccion = static function (
    array $origen
) use (
    $valor
): string {

    $partes =
        array_filter([
            $valor($origen, 'calle'),
            $valor($origen, 'numero_exterior'),
            $valor($origen, 'colonia'),
            $valor($origen, 'municipio'),
            $valor($origen, 'estado'),
        ]);

    return implode(
        ', ',
        $partes
    );
};

$linea = static function (
    string $texto
): string {

    return esc(
        $texto
    );
};

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
            . '... (continua en anexo de narrativa)'
        : $narracion;

$nombresPersonal =
    [];

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

$textoUnidades =
    [];

foreach (
    $unidades
    as $unidad
) {

    $texto =
        trim(
            implode(
                ' ',
                array_filter([
                    $unidad['no_economico_snapshot']
                    ?? '',
                    $unidad['placas_snapshot']
                        ? '(' . $unidad['placas_snapshot'] . ')'
                        : '',
                    $unidad['marca_snapshot']
                    ?? '',
                    $unidad['submarca_snapshot']
                    ?? '',
                ])
            )
        );

    if (
        $texto !== ''
    ) {

        $textoUnidades[] =
            $texto;
    }
}

$textoEvidencias =
    [];

foreach (
    $evidencias
    as $evidencia
) {

    $textoEvidencias[] =
        trim(
            (string) (
                $evidencia['nombre_original']
                ?? $evidencia['nombre_archivo']
                ?? ''
            )
        );
}

$textoEvidencias =
    array_values(
        array_filter(
            $textoEvidencias
        )
    );
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 118px 42px 76px 42px;
        }

        body {
            color: #172033;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.32;
        }

        .header,
        .footer {
            left: -42px;
            position: fixed;
            right: -42px;
        }

        .header {
            top: -108px;
        }

        .footer {
            bottom: -68px;
        }

        .header img,
        .footer img {
            width: 100%;
        }

        .watermark {
            bottom: 35px;
            opacity: .19;
            position: fixed;
            right: -78px;
            width: 450px;
            z-index: -1;
        }

        .title {
            color: #7d1934;
            font-size: 15px;
            font-weight: bold;
            margin: 0 0 8px 0;
            text-align: center;
            text-transform: uppercase;
        }

        .privacy {
            border: 1px solid #9d9d9d;
            font-size: 7.3px;
            padding: 6px 8px;
            text-align: justify;
        }

        .privacy strong {
            color: #7d1934;
        }

        .checks {
            font-size: 7.5px;
            margin: 6px 0;
        }

        .check {
            border: 1px solid #333;
            display: inline-block;
            height: 7px;
            margin-right: 4px;
            width: 7px;
        }

        .meta {
            margin-top: 7px;
            width: 100%;
        }

        .meta td {
            padding: 2px 4px;
            vertical-align: bottom;
        }

        .label {
            font-weight: bold;
            white-space: nowrap;
        }

        .line {
            border-bottom: 1px solid #333;
            min-height: 13px;
        }

        .section-title {
            background: #7d1934;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            margin-top: 8px;
            padding: 4px 7px;
            text-transform: uppercase;
        }

        .grid {
            border-collapse: collapse;
            width: 100%;
        }

        .grid td {
            padding: 3px 4px;
            vertical-align: bottom;
        }

        .small-note {
            font-size: 7.2px;
            font-style: italic;
            margin: 4px 0;
            text-align: justify;
        }

        .narrative {
            border: 1px solid #777;
            min-height: 150px;
            padding: 7px;
            text-align: justify;
            white-space: pre-wrap;
        }

        .list {
            border: 1px solid #bbb;
            margin: 0;
            min-height: 18px;
            padding: 5px 7px;
        }

        .signature {
            margin: 26px auto 0 auto;
            text-align: center;
            width: 58%;
        }

        .signature-line {
            border-top: 1px solid #333;
            height: 8px;
        }

        .respect {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 3px;
            margin-bottom: 18px;
            text-align: center;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <?php if (!empty($assets['header'])): ?>
        <div class="header">
            <img src="<?= esc($assets['header']) ?>" alt="">
        </div>
    <?php endif; ?>

    <?php if (!empty($assets['footer'])): ?>
        <div class="footer">
            <img src="<?= esc($assets['footer']) ?>" alt="">
        </div>
    <?php endif; ?>

    <?php if (!empty($assets['watermark'])): ?>
        <img class="watermark" src="<?= esc($assets['watermark']) ?>" alt="">
    <?php endif; ?>

    <h1 class="title">Formato institucional de queja</h1>

    <div class="privacy">
        <strong>* AVISO DE PRIVACIDAD *</strong>
        La Direcci&oacute;n General de Seguridad Ciudadana del Municipio de Nezahualc&oacute;yotl,
        con domicilio en Calle Caballo Bayo, S/N, casi esquina con Av. Chimalhuac&aacute;n,
        Colonia Benito Ju&aacute;rez, Nezahualc&oacute;yotl, Estado de M&eacute;xico, es responsable
        de recabar sus datos personales y sensibles, del uso que se le d&eacute; a los mismos y de su
        protecci&oacute;n. Los datos personales que se recaban en este formato son: nombre, direcci&oacute;n,
        tel&eacute;fono y correo electr&oacute;nico, que ser&aacute;n tratados con la finalidad de canalizar,
        atender y dar seguimiento a su queja, denuncia o inconformidad. Asimismo, esta Coordinaci&oacute;n
        de Asuntos Internos integra un expediente que documenta el procedimiento correspondiente y la
        informaci&oacute;n proporcionada ser&aacute; tratada con reserva y confidencialidad conforme a la
        normatividad aplicable.
    </div>

    <div class="checks">
        <span class="check"></span> Consiento y autorizo que mis datos personales sean tratados conforme al presente aviso.
        <br>
        <span class="check"></span> Me comprometo a aportar las pruebas necesarias para la debida integraci&oacute;n de mi queja.
        <br>
        <span class="check"></span> Acepto y protesto que la informaci&oacute;n proporcionada es fiel y verdadera.
    </div>

    <table class="meta">
        <tr>
            <td class="label">Fecha:</td>
            <td class="line"><?= $linea($fecha($reporte['fecha_registro'] ?? '')) ?></td>
            <td class="label">Hora:</td>
            <td class="line"><?= $linea($hora($reporte['created_at'] ?? $reporte['hora_hechos'] ?? '')) ?></td>
            <td class="label">Folio:</td>
            <td class="line"><?= $linea($valor($reporte, 'folio')) ?></td>
        </tr>
        <tr>
            <td class="label">Tipo de asunto:</td>
            <td class="line" colspan="5">Queja (X) &nbsp;&nbsp; Felicitaci&oacute;n ( ) &nbsp;&nbsp; Recomendaci&oacute;n ( ) &nbsp;&nbsp; Otro ( ) &nbsp;&nbsp; <?= $linea($tipoFolio) ?></td>
        </tr>
    </table>

    <div class="section-title">Datos del ciudadano(a)</div>
    <table class="grid">
        <tr>
            <td class="label">Nombre completo:</td>
            <td class="line" colspan="3"><?= (int) ($reporte['es_anonimo'] ?? 0) === 1 ? '' : $linea($valor($reporte, 'nombre_quejoso')) ?></td>
            <td class="label">Edad:</td>
            <td class="line"><?= $linea($valor($reporte, 'edad_quejoso')) ?></td>
        </tr>
        <tr>
            <td class="label">Calle:</td>
            <td class="line"><?= $linea($valor($reporte, 'direccion_quejoso')) ?></td>
            <td class="label">Numero:</td>
            <td class="line"></td>
            <td class="label">Telefono:</td>
            <td class="line"><?= $linea($valor($reporte, 'telefono_quejoso')) ?></td>
        </tr>
        <tr>
            <td class="label">Correo electronico:</td>
            <td class="line" colspan="5"><?= $linea($valor($reporte, 'correo_quejoso')) ?></td>
        </tr>
    </table>

    <div class="section-title">Direccion para notificacion</div>
    <p class="small-note">
        Llenar cuando el domicilio proporcionado estuviera fuera de la jurisdicci&oacute;n del Municipio de Nezahualc&oacute;yotl
        o para proporcionar un domicilio dentro del territorio para seguimiento oportuno.
    </p>
    <table class="grid">
        <tr>
            <td class="label">Calle:</td>
            <td class="line"><?= $linea($valor($direccionNotificacion, 'calle')) ?></td>
            <td class="label">Numero:</td>
            <td class="line"><?= $linea($valor($direccionNotificacion, 'numero_exterior')) ?></td>
        </tr>
        <tr>
            <td class="label">Entre que calle:</td>
            <td class="line"><?= $linea($valor($direccionNotificacion, 'entre_calle')) ?></td>
            <td class="label">Y calle:</td>
            <td class="line"><?= $linea($valor($direccionNotificacion, 'y_calle')) ?></td>
        </tr>
        <tr>
            <td class="label">Colonia:</td>
            <td class="line"><?= $linea($valor($direccionNotificacion, 'colonia')) ?></td>
            <td class="label">Municipio / Entidad:</td>
            <td class="line"><?= $linea(trim($valor($direccionNotificacion, 'municipio') . ' ' . $valor($direccionNotificacion, 'estado'))) ?></td>
        </tr>
    </table>

    <div class="section-title">Hechos que desea denunciar</div>
    <table class="grid">
        <tr>
            <td class="label">Lugar donde ocurrieron los hechos:</td>
            <td class="line" colspan="3"><?= $linea($direccion($reporte)) ?></td>
        </tr>
        <tr>
            <td class="label">Fecha de los hechos:</td>
            <td class="line"><?= $linea($fecha($reporte['fecha_hechos'] ?? '')) ?></td>
            <td class="label">Hora aproximada:</td>
            <td class="line"><?= $linea($hora($reporte['hora_hechos'] ?? '')) ?></td>
        </tr>
    </table>

    <div class="section-title">Narracion de los hechos</div>
    <div class="narrative"><?= nl2br($linea($narracionPrincipal)) ?></div>

    <div class="section-title">Datos del o los servidores publicos</div>
    <table class="grid">
        <tr>
            <td class="label">Sabe que unidad fue:</td>
            <td class="line"><?= $linea(implode('; ', $textoUnidades)) ?></td>
        </tr>
        <tr>
            <td class="label">Nombres de servidores publicos:</td>
            <td class="line"><?= $linea(implode('; ', $nombresPersonal)) ?></td>
        </tr>
        <tr>
            <td class="label">Cantidad de servidores involucrados:</td>
            <td class="line"><?= count($personal) > 0 ? (string) count($personal) : '' ?></td>
        </tr>
        <tr>
            <td class="label">Reconoce a los servidores publicos:</td>
            <td class="line"></td>
        </tr>
    </table>

    <div class="section-title">Pruebas</div>
    <table class="grid">
        <tr>
            <td class="label">Tiene pruebas de los hechos denunciados:</td>
            <td class="line"><?= count($evidencias) > 0 ? 'Si' : '' ?></td>
        </tr>
        <tr>
            <td class="label">Tipo de prueba o evidencia:</td>
            <td class="line"><?= $linea(implode('; ', $textoEvidencias)) ?></td>
        </tr>
        <tr>
            <td class="label">Atendio su queja (unidad y nombre):</td>
            <td class="line"></td>
        </tr>
    </table>

    <div class="signature">
        <div class="respect">RESPETUOSAMENTE</div>
        <div class="signature-line"></div>
        NOMBRE Y FIRMA DEL DENUNCIANTE
        <?php if ((int) ($reporte['es_anonimo'] ?? 0) !== 1 && $valor($reporte, 'nombre_quejoso') !== ''): ?>
            <br><?= $linea($valor($reporte, 'nombre_quejoso')) ?>
        <?php endif; ?>
    </div>

    <?php if ($narracionLarga): ?>
        <div class="page-break"></div>
        <h1 class="title">Anexo de narrativa</h1>
        <div class="narrative"><?= nl2br($linea($narracion)) ?></div>

        <div class="signature">
            <div class="respect">RESPETUOSAMENTE</div>
            <div class="signature-line"></div>
            NOMBRE Y FIRMA DEL DENUNCIANTE
        </div>
    <?php endif; ?>
</body>
</html>
