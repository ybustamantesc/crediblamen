<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>FAF - Análisis Financiero</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size:12px; }
        .header { display:flex; align-items:center; margin-bottom:10px; }
        .logo { width:120px; }
        .title { flex:1; text-align:center; }
        .section { margin-top:10px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #aaa; padding:6px; }
        th { background:#f0f0f0; }
        .right { text-align:right; }
        .small { font-size:11px; }
        .no-border td, .no-border th { border: none; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <?php if (file_exists(FCPATH . 'public/img/logo.jpg')): ?>
                <img src="<?php echo FCPATH . 'public/img/logo.jpg'; ?>" style="width:120px" />
            <?php endif; ?>
        </div>
        <div class="title">
            <h2>Formato FAF - Análisis Financiero (<?php echo strtoupper($tipo); ?>)</h2>
            <div class="small">Generado: <?php echo isset($generated_at)?$generated_at:date('d/m/Y H:i'); ?></div>
        </div>
    </div>

    <div class="section">
        <table>
            <tr>
                <th>Cliente</th>
                <th>Documento</th>
                <th>Código</th>
            </tr>
            <tr>
                <td><?php echo isset($faf['cliente'])?htmlspecialchars($faf['cliente']): (isset($solicitud->nombres)? htmlspecialchars($solicitud->nombres . ' ' . ($solicitud->apellidos?:'')) : ''); ?></td>
                <td><?php echo isset($faf['documento'])?htmlspecialchars($faf['documento']): (isset($solicitud->numero_doc)?htmlspecialchars($solicitud->numero_doc):''); ?></td>
                <td><?php echo isset($faf['codigo'])?htmlspecialchars($faf['codigo']): ('SOL-' . str_pad($solicitud->idsolicitud,4,'0',STR_PAD_LEFT)); ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table>
            <tr>
                <th>Monto solicitado</th>
                <th>Plazo (meses)</th>
                <th>Tipo crédito</th>
            </tr>
            <tr>
                <td class="right"><?php echo isset($faf['monto_solicitado'])?number_format((float)$faf['monto_solicitado'],2,',','.'):(isset($solicitud->monto_solicitado)?number_format((float)$solicitud->monto_solicitado,2,',','.'):''); ?></td>
                <td class="right"><?php echo isset($faf['plazo'])?htmlspecialchars($faf['plazo']):(isset($solicitud->plazo_meses)?htmlspecialchars($solicitud->plazo_meses):''); ?></td>
                <td><?php echo isset($faf['tipo_credito'])?htmlspecialchars($faf['tipo_credito']):(isset($solicitud->tipo_credito)?htmlspecialchars($solicitud->tipo_credito):''); ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table>
            <tr>
                <th>Empresa / Centro trabajo</th>
                <th>Cargo / Puesto</th>
                <th>Teléfono trabajo</th>
            </tr>
            <tr>
                <td><?php echo isset($faf['empresa'])?htmlspecialchars($faf['empresa']): (isset($solicitud->nombre_empresa)?htmlspecialchars($solicitud->nombre_empresa):''); ?></td>
                <td><?php echo isset($faf['cargo'])?htmlspecialchars($faf['cargo']): (isset($solicitud->cargo_puesto)?htmlspecialchars($solicitud->cargo_puesto):''); ?></td>
                <td><?php echo isset($faf['telefono_trabajo'])?htmlspecialchars($faf['telefono_trabajo']): (isset($solicitud->telefono_trabajo)?htmlspecialchars($solicitud->telefono_trabajo):''); ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table>
            <tr>
                <th>Ingreso mensual neto</th>
                <th>Otros ingresos</th>
                <th>Gastos personales</th>
            </tr>
            <tr>
                <td class="right"><?php echo isset($faf['ingreso_mensual'])?number_format((float)$faf['ingreso_mensual'],2,',','.'):(isset($solicitud->ingreso_mensual_neto)?number_format((float)$solicitud->ingreso_mensual_neto,2,',','.'):''); ?></td>
                <td class="right"><?php echo isset($faf['otros_ingresos'])?number_format((float)$faf['otros_ingresos'],2,',','.'):'0.00'; ?></td>
                <td class="right"><?php echo isset($faf['gastos_personales'])?number_format((float)$faf['gastos_personales'],2,',','.'):'0.00'; ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table>
            <tr>
                <th>Ingresos netos</th>
                <th>Capacidad de pago</th>
                <th>Observaciones</th>
            </tr>
            <tr>
                <td class="right"><?php echo isset($faf['ingresos_netos'])?number_format((float)$faf['ingresos_netos'],2,',','.'):'0.00'; ?></td>
                <td class="right"><?php echo isset($faf['capacidad_pago'])?number_format((float)$faf['capacidad_pago'],2,',','.'): '0.00'; ?></td>
                <td><?php echo isset($faf['observaciones'])?nl2br(htmlspecialchars($faf['observaciones'])):''; ?></td>
            </tr>
        </table>
    </div>

    <div style="position:fixed; bottom:10px; width:100%; text-align:center; font-size:11px;">
        <span>Documento generado por el sistema</span>
    </div>

</body>
</html>