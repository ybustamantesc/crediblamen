<?php
// application/views/solicitudes/print_solicitud_pdf.php
// Plantilla temporal: cuerpo en blanco. Pie con usuario que generó, fecha e id de la solicitud.
// Variables esperadas: $solicitud (obj)
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"/>
  <title>Solicitud Inicial - <?php echo isset($solicitud->idsolicitud)?'#'.$solicitud->idsolicitud:''; ?></title>
  <style>
    @page { margin: 10mm 12mm; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#222; margin:0; padding:0 0 18mm 0; }
    .header-dark { background:#0b3d91; color:#fff; padding:14px 16px; }
    .header-inner { width:100%; }
    .title { font-size:18px; font-weight:700; letter-spacing:1px; margin:6px 0 8px 0; }
    .sub { font-size:11px; color:#ddd; margin-bottom:8px; }
    .logo { float:right; }
    .clear { clear:both; }

    .box-giro { background:#fff; padding:8px; border:1px solid #ddd; margin-top:8px; min-height:28px; }
    .giro-label { color:#fff; font-weight:600; margin-bottom:6px; display:block; }

    .section-title { margin-top:8px; font-size:12px; font-weight:700; color:#333; border-bottom:1px solid #cfcfcf; padding-bottom:6px; }

    .grid { width:100%; }
    .row { display:block; margin-bottom:6px; }
    .col-60 { width:60%; display:inline-block; vertical-align:top; }
    .col-40 { width:38%; display:inline-block; vertical-align:top; }

    .field-inline { display:inline-block; margin-right:10px; font-size:11px; }
    .field-label { font-weight:600; color:#222; }

    .guarantees { font-size:11px; margin-top:6px; }
    .chk { display:inline-block; margin-right:12px; }

    /* Footer: render only at document end */
    footer { display:block; position:static; clear:both; font-size:10px; color:#444; text-align:left; border-top:1px solid #eee; padding-top:6px; margin:8px 12mm 6mm 12mm; }
    .footer-item { display:inline-block; margin-right:18px; }

    /* small adjustments for dompdf compatibility */
    img.logo-img { max-height:60px; }
  </style>
  <?php
    // prepare logo data URI for more reliable embedding in dompdf
    $logo_uri = '';
    $possible_logos = [
      FCPATH . 'public/img/logo.png',
      FCPATH . 'public/img/logo.jpg',
      FCPATH . 'public/img/credi_socios_logo.png',
      FCPATH . 'public/img/credi_socios_logo.jpg'
    ];
    foreach ($possible_logos as $p) {
      if (file_exists($p)) {
        $m = mime_content_type($p);
        $data = base64_encode(file_get_contents($p));
        $logo_uri = 'data:' . $m . ';base64,' . $data;
        break;
      }
    }
  ?>
</head>
<body>

  <?php
    // Mantener la misma regla del formulario core:
    // Si Destino Conami es Personales (Asalariados), ocultar bloques de negocio.
    // En caso contrario, mostrar bloques de negocio.
    $rubro_raw = (string)($solicitud->rubro_credito ?? '');
    
    // Normalizar: convertir a minúsculas, remover acentos, limpiar espacios
    $rubro_norm = strtolower(trim(preg_replace('/\s+/', ' ', strtr($rubro_raw, array(
      'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N',
      'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n'
    )))));
    
    // Detectar si es "Personales (Asalariados)" o variaciones similares
    $is_asalariado_conami = (strpos($rubro_norm, 'personales') !== false && strpos($rubro_norm, 'asalariad') !== false);
    
    // Debug (HTML comment, no se ve en PDF):
    // echo '<!-- DEBUG: rubro_raw="'.htmlspecialchars($rubro_raw).'", normalized="'.htmlspecialchars($rubro_norm).'", is_asalariado='.$is_asalariado_conami.' -->';
  ?>

  <div class="header-dark">
    <div class="header-inner">
      <div style="float:left; max-width:65%">
        <div class="title">FORMATO DE SOLICITUD INICIAL DE CRÉDITO</div>
        <div class="sub">INFORMACIÓN DEL CRÉDITO SOLICITADO</div>
      </div>
      <div class="logo" style="float:right; margin-left:10px;">
        <?php if (!empty($logo_uri)): ?>
          <img class="logo-img" src="<?php echo $logo_uri; ?>" alt="logo" />
        <?php else: ?>
          <?php
            // fallback to filesystem path if data uri not available
            $logo_file = '';
            foreach (array(FCPATH . 'public/img/logo.png', FCPATH . 'public/img/logo.jpg', FCPATH . 'public/img/credi_socios_logo.png', FCPATH . 'public/img/credi_socios_logo.jpg') as $lf) {
              if (file_exists($lf)) { $logo_file = $lf; break; }
            }
            if (!empty($logo_file)) echo '<img class="logo-img" src="' . htmlspecialchars($logo_file) . '" alt="logo">';
          ?>
        <?php endif; ?>
      </div>
      <div class="clear"></div>
    </div>
  </div>

      <!-- sección 3 movida abajo para mantener orden lógico (1,2,3,4...) -->
  <div style="padding:12px 6px;">
    <!-- Giro del Negocio -->
    <div style="display:block; margin-bottom:6px; color:#333; font-weight:700;">Giro del Negocio &nbsp;&nbsp; <span style="font-weight:400; margin-left:12px;">Nuevo</span> <span style="display:inline-block; width:16px; height:14px; border:1px solid #0b3d91; margin:0 8px; vertical-align:middle; text-align:center;"><?php echo (isset($solicitud->es_nuevo) && ($solicitud->es_nuevo=='1' || $solicitud->es_nuevo==1)) ? 'X' : ''; ?></span> <span style="font-weight:400; margin-left:4px;">Renovación</span> <span style="display:inline-block; width:16px; height:14px; border:1px solid #0b3d91; margin:0 8px; vertical-align:middle; text-align:center;"><?php echo (isset($solicitud->es_renovacion) && ($solicitud->es_renovacion=='1' || $solicitud->es_renovacion==1)) ? 'X' : ''; ?></span></div>
    <div class="box-giro"><?php echo nl2br(htmlspecialchars($solicitud->giro_negocio ?? '')); ?></div>

    <!-- Información del crédito -->
    <div class="section-title">INFORMACIÓN DEL CRÉDITO SOLICITADO</div>
    <div style="margin-top:8px;">
      <div class="row">
        <div class="col-60"><span class="field-label">Monto solicitado: U$</span> <span><?php echo htmlspecialchars(number_format((float)($solicitud->monto_solicitado ?? 0), 2, '.', ',')); ?></span></div>
        <div class="col-40"><span class="field-label">Plazo:</span> <span><?php echo htmlspecialchars($solicitud->plazo_meses ?? ''); ?> meses</span></div>
      </div>
      <div class="row">
        <div class="col-60"><span class="field-label">Ruta (Asesor):</span> <span><?php echo htmlspecialchars($solicitud->nombre_asesor ?? $solicitud->asesor ?? $solicitud->ruta ?? $solicitud->nombre_promotor ?? ''); ?></span></div>
      </div>
      <div class="row">
        <div class="col-60"><span class="field-label">Frecuencia:</span> <span><?php echo htmlspecialchars($solicitud->frecuencia ?? ''); ?></span></div>
        <div class="col-40"><span class="field-label">Tasa de interés a cobrar:</span> <span>
          <?php
            $raw = $solicitud->tasa_interes ?? '';
            $t = '';
            if ($raw !== null && $raw !== '') {
                if (is_numeric($raw)) { $tv = (float)$raw; if (abs($tv) <= 1) $tv = $tv * 100; $t = rtrim(rtrim(number_format($tv,2,'.',''),'0'),'.'); }
            }
            echo htmlspecialchars($t !== '' ? $t . ' %' : '');
          ?>
        </span></div>
      </div>
      <div class="row">
        <div class="col-60"><span class="field-label">Tipo de Crédito:</span> <span><?php echo htmlspecialchars($solicitud->tipo_credito ?? ''); ?></span></div>
        <div class="col-40"><span class="field-label">Destino Conami:</span> <span><?php echo htmlspecialchars($solicitud->rubro_credito ?? ''); ?></span></div>
      </div>
      <div class="row" style="margin-top:6px;">
        <div class="col-60"><span class="field-label">Destino:</span> <span><?php echo htmlspecialchars($solicitud->destino_credito ?? ''); ?></span></div>
      </div>
      <div class="row">
        <div class="col-60">
          <span class="field-label">Promedio de cuota estimada: U$</span>
          <span>
            <?php 
            $cuota_mensual = (float)($solicitud->cuota_estimado ?? $solicitud->cuota_estim_estimada ?? 0);
            $cuota_quincenal = $cuota_mensual / 2;
            echo htmlspecialchars(number_format($cuota_mensual,2,'.',','));
            ?>
            - Quincenal: U$ <?php echo htmlspecialchars(number_format($cuota_quincenal,2,'.',',')); ?>
          </span>
        </div>
      </div>

      <div class="row guarantees">
        <div class="field-label">Garantía ofrecida:</div>
        <div class="guarantees">
          <span class="chk"><?php echo (!empty($solicitud->garantia_hipotecaria) ? '[X]' : '[ ]'); ?> Hipotecaria</span>
          <span class="chk"><?php echo (!empty($solicitud->garantia_prendaria) ? '[X]' : '[ ]'); ?> Prendaria</span>
          <span class="chk"><?php echo (!empty($solicitud->garantia_fiador) ? '[X]' : '[ ]'); ?> Fiador</span>
          <span class="chk"><?php echo (!empty($solicitud->garantia_otra) ? '[X]' : '[ ]'); ?> Otra</span>
        </div>
      </div>

    </div>
  </div>

  
      <!-- 1. DATOS GENERALES DEL CLIENTE -->
      <div style="margin:14px 6px 6px 6px;">
          <div style="background:#0b3d91;color:#fff;padding:8px 10px;font-weight:700;font-size:13px;">DATOS GENERALES DEL CLIENTE</div>
        <div style="border:1px solid #e6e6e6;padding:10px;font-size:11px;">
          <div style="margin-bottom:6px;"><strong>Fecha de solicitud:</strong> <?php echo htmlspecialchars(isset($solicitud->fecha_solicitud) ? date('d/m/Y', strtotime($solicitud->fecha_solicitud)) : ''); ?></div>
          <div style="margin-bottom:6px;"><strong>Nombre completo:</strong> <?php echo htmlspecialchars(trim((isset($solicitud->nombre_completo) && $solicitud->nombre_completo!='') ? $solicitud->nombre_completo : trim((($solicitud->apellidos ?? '') . ' ' . ($solicitud->nombres ?? ''))))); ?></div>
          <div style="margin-bottom:6px;"><strong>Cédula de identidad:</strong> <?php echo htmlspecialchars($solicitud->numero_doc ?? ''); ?></div>
          <div style="margin-bottom:6px;"><strong>Fecha de nacimiento:</strong> <?php echo htmlspecialchars(isset($solicitud->fecha_nacimiento) && $solicitud->fecha_nacimiento ? date('d/m/Y', strtotime($solicitud->fecha_nacimiento)) : ''); ?> &nbsp; <strong>Edad:</strong> <?php echo htmlspecialchars($solicitud->edad ?? ''); ?> años</div>
          <div style="margin-bottom:6px;"><strong>Estado civil:</strong> <?php echo htmlspecialchars($solicitud->estado_civil ?? ''); ?> &nbsp; 
            <?php /* checkboxes visual */ ?>
          </div>
          <div style="margin-bottom:6px;"><strong>Nombre del cónyuge o pareja:</strong> <?php echo htmlspecialchars($solicitud->nombre_conyuge ?? ''); ?></div>
          <div style="margin-bottom:6px;"><strong>Cédula del cónyuge o pareja:</strong> <?php echo htmlspecialchars($solicitud->dni_conyuge ?? ''); ?></div>
          <div style="margin-bottom:6px;"><strong>Ocupación del cónyuge o pareja:</strong> <?php echo htmlspecialchars($solicitud->ocupacion_conyuge ?? ''); ?></div>
          <div style="margin-bottom:6px;"><strong>Ingresos del cónyuge:</strong> <?php echo htmlspecialchars($solicitud->ingresos_conyuge ?? ''); ?></div>
          <div style="margin-bottom:6px;"><strong>Teléfono del cónyuge o pareja:</strong> <?php echo htmlspecialchars($solicitud->telefono_conyuge ?? ''); ?> &nbsp; <strong>Número de dependientes:</strong> <?php echo htmlspecialchars($solicitud->numero_dependientes ?? ''); ?></div>
          <div style="margin-bottom:6px;"><strong>Teléfono(s) del solicitante:</strong> <?php echo htmlspecialchars($solicitud->telefono ?? ''); ?></div>
          <div style="margin-bottom:8px;"><strong>Dirección exacta de domicilio:</strong><div style="border-top:1px solid #ddd;padding-top:6px;margin-top:6px;min-height:28px;"><?php echo nl2br(htmlspecialchars($solicitud->direccion ?? '')); ?></div></div>

          <div style="margin-bottom:6px;"><strong>Tiempo de residir en la vivienda:</strong> <?php echo htmlspecialchars($solicitud->tiempo_residir_anios ?? ''); ?> años / <?php echo htmlspecialchars($solicitud->tiempo_residir_meses ?? ''); ?> meses</div>
          <div style="margin-bottom:6px;"><strong>Condición de vivienda:</strong> 
            <span style="margin-left:6px;"><?php echo (!empty($solicitud->condicion_vivienda) && $solicitud->condicion_vivienda=='Propia') ? '[X] Propia' : '[ ] Propia'; ?></span>
            <span style="margin-left:8px;"><?php echo (!empty($solicitud->condicion_vivienda) && $solicitud->condicion_vivienda=='Familiar') ? '[X] Familiar' : '[ ] Familiar'; ?></span>
            <span style="margin-left:8px;"><?php echo (!empty($solicitud->condicion_vivienda) && $solicitud->condicion_vivienda=='Alquilada') ? '[X] Alquilada' : '[ ] Alquilada'; ?></span>
            <span style="margin-left:8px;"><?php echo (!empty($solicitud->condicion_vivienda) && $solicitud->condicion_vivienda=='Otra') ? '[X] Otra' : '[ ] Otra'; ?></span>
          </div>
        </div>
        </div>

      <!-- 2. INFORMACIÓN LABORAL (CLIENTE ASALARIADO) -->
      <?php if ($is_asalariado_conami): ?>
      <div style="margin:14px 6px 6px 6px;">
          <div style="background:#0b3d91;color:#fff;padding:8px 10px;font-weight:700;font-size:13px;">INFORMACIÓN LABORAL (CLIENTE ASALARIADO)</div>
        <div style="border:1px solid #e6e6e6;padding:10px;font-size:11px;margin-top:6px;">
          <div style="margin-bottom:6px;"><strong>Nombre de la empresa:</strong> <?php echo htmlspecialchars($solicitud->nombre_empresa ?? ''); ?></div>
          <div style="margin-bottom:6px;"><strong>Dirección de la empresa:</strong> <?php echo nl2br(htmlspecialchars($solicitud->direccion_empresa ?? '')); ?></div>
          <div style="margin-bottom:6px;"><strong>Teléfono de la empresa:</strong> <?php echo htmlspecialchars($solicitud->telefono_empresa ?? ''); ?></div>
          <div style="margin-bottom:6px;"><strong>Cargo / puesto:</strong> <?php echo htmlspecialchars($solicitud->cargo_puesto ?? ''); ?> &nbsp; <strong>Tiempo en el empleo actual:</strong> <?php echo htmlspecialchars($solicitud->tiempo_empleo_anios ?? ''); ?> años / <?php echo htmlspecialchars($solicitud->tiempo_empleo_meses ?? ''); ?> meses</div>
          <div style="margin-bottom:6px;"><strong>Tipo de contrato:</strong>
            <span style="margin-left:8px;"><?php echo (!empty($solicitud->tipo_contrato_permanente) ? '[X] Permanente' : '[ ] Permanente'); ?></span>
            <span style="margin-left:8px;"><?php echo (!empty($solicitud->tipo_contrato_temporal) ? '[X] Temporal' : '[ ] Temporal'); ?></span>
            <span style="margin-left:8px;"><?php echo (!empty($solicitud->tipo_contrato_otro) ? '[X] Otro' : '[ ] Otro'); ?></span>
          </div>
          <div style="margin-bottom:6px;"><strong>Ingreso mensual neto:</strong> C$ <?php echo htmlspecialchars(number_format((float)($solicitud->ingreso_mensual_neto ?? 0), 2, '.', ',')); ?></div>
          <div style="margin-bottom:6px;"><strong>Deducciones (INSS, IR):</strong> <?php echo htmlspecialchars($solicitud->deducciones ?? ''); ?></div>
        </div>
      </div>
      <?php endif; ?>

        <?php if (!$is_asalariado_conami): ?>
        <!-- INFORMACIÓN DEL NEGOCIO (CLIENTE COMERCIANTE O EMPRESARIO) -->
        <div style="margin:14px 6px 6px 6px;">
          <div style="background:#0b3d91;color:#fff;padding:8px 10px;font-weight:700;font-size:13px;">INFORMACIÓN DEL NEGOCIO (CLIENTE COMERCIANTE O EMPRESARIO)</div>
          <div style="border:1px solid #e6e6e6;padding:10px;font-size:11px;margin-top:6px;">
            <div style="margin-bottom:6px;"><strong>Nombre del negocio:</strong> <?php echo htmlspecialchars($solicitud->nombre_negocio ?? ''); ?></div>
            <div style="margin-bottom:6px;"><strong>Actividad económica principal:</strong> <?php echo htmlspecialchars($solicitud->actividad_economica ?? ''); ?></div>
            <div style="margin-bottom:6px;"><strong>Ubicación del negocio:</strong> <?php echo nl2br(htmlspecialchars($solicitud->ubicacion_negocio ?? '')); ?></div>
            <div style="margin-bottom:6px;"><strong>Teléfono del negocio:</strong> <?php echo htmlspecialchars($solicitud->telefono_negocio ?? ''); ?></div>
            <div style="margin-bottom:6px;"><strong>Tiempo de operación:</strong> <?php echo htmlspecialchars($solicitud->tiempo_operacion_anios ?? ''); ?> años / <?php echo htmlspecialchars($solicitud->tiempo_operacion_meses ?? ''); ?> meses &nbsp; <strong>Local:</strong>
              <span style="margin-left:8px;"><?php echo (!empty($solicitud->local_condicion) && $solicitud->local_condicion=='Propio') ? '[X] Propio' : '[ ] Propio'; ?></span>
              <span style="margin-left:8px;"><?php echo (!empty($solicitud->local_condicion) && $solicitud->local_condicion=='Alquilado') ? '[X] Alquilado' : '[ ] Alquilado'; ?></span>
              <span style="margin-left:8px;"><?php echo (!empty($solicitud->local_condicion) && $solicitud->local_condicion=='Otro') ? '[X] Otro' : '[ ] Otro'; ?></span>
            </div>

            <div style="margin-top:8px; font-weight:600;">Ingresos y Ventas</div>
            <?php
              $bmask = intval($solicitud->ventas_dias_buenos_mask ?? 0);
              $mmask = intval($solicitud->ventas_dias_malos_mask ?? 0);
              // dias: L M M J V S D (posiciones 0..6)
            ?>
            <div style="margin-bottom:6px;"><strong>Ventas en días buenos: C$</strong> <?php echo htmlspecialchars(number_format((float)($solicitud->ventas_en_dias_buenos ?? 0),2,'.',',')); ?>
              &nbsp;&nbsp; <strong>Ventas en días malos: C$</strong> <?php echo htmlspecialchars(number_format((float)($solicitud->ventas_en_dias_malos ?? 0),2,'.',',')); ?>
            </div>
            <div style="margin-bottom:6px;">
              <div style="display:inline-block; width:48%; vertical-align:top;"><strong>Ventas en días buenos:</strong>
                <div style="margin-top:6px;">
                  <span style="display:inline-block; width:22px; text-align:center;">L</span>
                  <span style="display:inline-block; width:22px; text-align:center;">M</span>
                  <span style="display:inline-block; width:22px; text-align:center;">M</span>
                  <span style="display:inline-block; width:22px; text-align:center;">J</span>
                  <span style="display:inline-block; width:22px; text-align:center;">V</span>
                  <span style="display:inline-block; width:22px; text-align:center;">S</span>
                  <span style="display:inline-block; width:22px; text-align:center;">D</span>
                </div>
                <div style="margin-top:4px;">
                    <?php for($i=0;$i<7;$i++): ?>
                      <span style="display:inline-block; width:22px; text-align:center; border:1px solid #0b3d91; padding:2px; margin-right:2px;"><?php echo ((($bmask>>$i)&1) ? 'X' : ''); ?></span>
                    <?php endfor; ?>
                  </div>
              </div>
              <div style="display:inline-block; width:48%; vertical-align:top;"><strong>Ventas en días malos:</strong>
                <div style="margin-top:6px;">
                  <div>
                    <span style="display:inline-block; width:22px; text-align:center;">L</span>
                    <span style="display:inline-block; width:22px; text-align:center;">M</span>
                    <span style="display:inline-block; width:22px; text-align:center;">M</span>
                    <span style="display:inline-block; width:22px; text-align:center;">J</span>
                    <span style="display:inline-block; width:22px; text-align:center;">V</span>
                    <span style="display:inline-block; width:22px; text-align:center;">S</span>
                    <span style="display:inline-block; width:22px; text-align:center;">D</span>
                  </div>
                  <div style="margin-top:4px;">
                    <?php for($i=0;$i<7;$i++): ?>
                      <span style="display:inline-block; width:22px; text-align:center; border:1px solid #0b3d91; padding:2px; margin-right:2px;"><?php echo ((($mmask>>$i)&1) ? 'X' : ''); ?></span>
                    <?php endfor; ?>
                  </div>
                </div>
              </div>
            </div>

            <div style="margin-bottom:6px;"><strong>Ventas promedio mensual:</strong> C$ <?php echo htmlspecialchars(number_format((float)($solicitud->ventas_promedio_mensual ?? 0),2,'.',',')); ?> &nbsp;&nbsp; <strong>Margen comercial (%) Actividad Principal:</strong> <?php echo htmlspecialchars($solicitud->margen_comercial ?? ''); ?> %</div>

            <!-- Otros ingresos repetibles (3) -->
            <div style="margin-top:8px;">
              <div style="margin-bottom:8px;"><strong>Otros ingresos:</strong> C$ <?php echo htmlspecialchars(number_format((float)($solicitud->otros_ingresos_1_monto ?? 0),2,'.',',')); ?> &nbsp; <strong>Margen comercial</strong> <?php echo htmlspecialchars($solicitud->otros_ingresos_1_margen ?? ''); ?> %
                <div style="border-top:1px solid #ddd;padding-top:6px;margin-top:6px;min-height:28px;">Detallar: <?php echo nl2br(htmlspecialchars($solicitud->otros_ingresos_1_detalle ?? '')); ?></div>
              </div>
              <div style="margin-bottom:8px;"><strong>Otros ingresos:</strong> C$ <?php echo htmlspecialchars(number_format((float)($solicitud->otros_ingresos_2_monto ?? 0),2,'.',',')); ?> &nbsp; <strong>Margen comercial</strong> <?php echo htmlspecialchars($solicitud->otros_ingresos_2_margen ?? ''); ?> %
                <div style="border-top:1px solid #ddd;padding-top:6px;margin-top:6px;min-height:28px;">Detallar: <?php echo nl2br(htmlspecialchars($solicitud->otros_ingresos_2_detalle ?? '')); ?></div>
              </div>
              <div style="margin-bottom:8px;"><strong>Otros ingresos:</strong> C$ <?php echo htmlspecialchars(number_format((float)($solicitud->otros_ingresos_3_monto ?? 0),2,'.',',')); ?> &nbsp; <strong>Margen comercial</strong> <?php echo htmlspecialchars($solicitud->otros_ingresos_3_margen ?? ''); ?> %
                <div style="border-top:1px solid #ddd;padding-top:6px;margin-top:6px;min-height:28px;">Detallar: <?php echo nl2br(htmlspecialchars($solicitud->otros_ingresos_3_detalle ?? '')); ?></div>
              </div>
            </div>

          </div>
        </div>

        <!-- 4. ESTRUCTURA FINANCIERA, DETALLE DEL INVENTARIO Y GASTOS FIJOS -->
      <div style="margin:14px 6px 6px 6px;">
        <div style="background:#0b3d91;color:#fff;padding:8px 10px;font-weight:700;font-size:13px;">Estructura Financiera del Negocio</div>
        <div style="border:1px solid #e6e6e6;padding:10px;font-size:11px;margin-top:6px;">
          <div style="margin-bottom:6px;"><strong>Cuentas por cobrar:</strong> C$ <?php echo htmlspecialchars(number_format((float)($solicitud->cuentas_por_cobrar ?? 0),2,'.',',')); ?> &nbsp;&nbsp; <strong>Ventas al Crédito:</strong> C$ <?php echo htmlspecialchars(number_format((float)($solicitud->ventas_al_credito ?? 0),2,'.',',')); ?> &nbsp;&nbsp; <strong>Caja (efectivo):</strong> C$ <?php echo htmlspecialchars(number_format((float)($solicitud->caja_efectivo ?? 0),2,'.',',')); ?> &nbsp;&nbsp; <strong>Banco:</strong> C$ <?php echo htmlspecialchars(number_format((float)($solicitud->saldo_banco ?? 0),2,'.',',')); ?></div>
        </div>
      </div>

      <div style="margin:8px 6px 6px 6px;">
      <div style="background:#0b3d91;color:#fff;padding:8px 10px;font-weight:700;font-size:13px;">Detalle del Inventario</div>
        <div style="border:1px solid #e6e6e6;padding:10px;font-size:11px;margin-top:6px;">
          <div style="margin-bottom:6px; font-weight:600;">Producto / Detalle:</div>
          <div style="margin-bottom:6px;"><?php echo nl2br(htmlspecialchars($solicitud->detalle_inventario ?? '')); ?></div>
          <div style="margin-bottom:6px; font-weight:600;">Monto total del inventario: C$ <?php echo htmlspecialchars(number_format((float)($solicitud->monto_total_inventario ?? 0),2,'.',',')); ?></div>
          <div style="margin-bottom:6px; font-weight:600;">Gastos Personales: C$ <?php echo htmlspecialchars(number_format((float)($solicitud->gastos_personales ?? 0),2,'.',',')); ?></div>
          <div style="margin-bottom:6px; font-weight:600;">Gastos Transporte: C$ <?php echo htmlspecialchars(number_format((float)($solicitud->gastos_transporte ?? 0),2,'.',',')); ?></div>
          <div style="border-bottom:1px solid #ddd; height:12px; margin:6px 0;"></div>
              <?php if (!empty($propuestas) && is_array($propuestas)): ?>
                <?php foreach ($propuestas as $p): ?>
                  <div style="margin-bottom:6px;">
                    <?php
                      $parts = array();
                      if (isset($p->nombre)) $parts[] = $p->nombre;
                      if (isset($p->clasificacion)) $parts[] = '(' . $p->clasificacion . ')';
                      $meta = array();
                      if (isset($p->tasa_mensual)) $meta[] = 'Tasa: ' . rtrim(rtrim(number_format((float)$p->tasa_mensual * ( ($p->tasa_mensual>1)?1:100), 2, '.', ''), '0'), '.');
                      if (isset($p->comision_desembolso)) $meta[] = 'Comisión: ' . rtrim(rtrim(number_format((float)$p->comision_desembolso * ( ($p->comision_desembolso>1)?1:100), 2, '.', ''), '0'), '.');
                      if (isset($p->plazo_max)) $meta[] = 'Plazo: ' . $p->plazo_max;
                      if (!empty($meta)) $parts[] = '[' . implode(' | ', $meta) . ']';
                      echo htmlspecialchars(implode(' ', $parts));
                    ?>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <?php if (!empty($solicitud->producto_lines) && is_array($solicitud->producto_lines)): ?>
                  <?php foreach ($solicitud->producto_lines as $pl): ?>
                    <div style="margin-bottom:6px;"><?php echo htmlspecialchars($pl); ?></div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div style="border-bottom:1px solid #ddd; height:12px; margin:6px 0;"></div>
                  <div style="border-bottom:1px solid #ddd; height:12px; margin:6px 0;"></div>
                <?php endif; ?>
              <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>

      <div style="margin:8px 6px 6px 6px;">
        <div style="background:#0b3d91;color:#fff;padding:8px 10px;font-weight:700;font-size:13px;">Gastos Fijos y Operativos</div>
        <div style="border:1px solid #e6e6e6;padding:10px;font-size:11px;margin-top:6px;">
          <div style="margin-bottom:6px;"><strong>Pago de alquiler local:</strong> C$ <?php echo htmlspecialchars(number_format((float)($solicitud->gasto_alquiler ?? 0),2,'.',',')); ?><?php if (!$is_asalariado_conami): ?> &nbsp;&nbsp; <strong>Pago de trabajadores:</strong> C$ <?php echo htmlspecialchars(number_format((float)($solicitud->gasto_trabajadores ?? 0),2,'.',',')); ?><?php endif; ?></div>
          <div style="margin-bottom:6px;"><strong>Energía eléctrica:</strong> C$ <?php echo htmlspecialchars(number_format((float)($solicitud->gasto_energia ?? 0),2,'.',',')); ?> &nbsp;&nbsp; <strong>Agua potable:</strong> C$ <?php echo htmlspecialchars(number_format((float)($solicitud->gasto_agua ?? 0),2,'.',',')); ?></div>
          <div style="margin-bottom:6px;"><strong>Internet / Telefonía:</strong> C$ <?php echo htmlspecialchars(number_format((float)($solicitud->gasto_internet ?? 0),2,'.',',')); ?><?php if (!$is_asalariado_conami): ?> &nbsp;&nbsp; <strong>Número de empleados:</strong> <?php echo htmlspecialchars($solicitud->numero_empleados ?? ''); ?><?php endif; ?> &nbsp;&nbsp; <strong>Otros gastos:</strong> C$ <?php echo htmlspecialchars(number_format((float)($solicitud->otros_gastos ?? 0),2,'.',',')); ?></div>
        </div>
      </div>

      <!-- 4. DECLARACIÓN DEL CLIENTE -->
      <div style="margin:14px 6px 6px 6px;">
          <div style="background:#0b3d91;color:#fff;padding:8px 10px;font-weight:700;font-size:13px;">DECLARACIÓN DEL CLIENTE</div>
        <div style="border:1px solid #e6e6e6;padding:10px;font-size:11px;margin-top:6px;">
          <div style="margin-bottom:8px; text-align:justify;">
            Declaro que la información proporcionada es verídica y autorizo a Crediblame S.A. a verificar mis datos en las fuentes necesarias para fines de análisis crediticio y cumplimiento regulatorio, así mismo acepto y autorizo el cobro por cargo de comisión por desembolso del <strong><?php echo htmlspecialchars((float)($solicitud->comision_desembolso ?? '')); ?>%</strong>.
          </div>
          <div style="margin-top:6px;">
            <div style="display:inline-block; width:48%; vertical-align:top;"><strong>Firma del solicitante:</strong> ________________________</div>
            <div style="display:inline-block; width:24%; vertical-align:top;"><strong>Fecha:</strong> __/__/____</div>
            <div style="display:inline-block; width:24%; vertical-align:top; text-align:right;"><strong>DDC - Investigación de campo</strong></div>
          </div>
        </div>
      </div>

      <!-- 5. USO INTERNO (PROMOTOR / MICROFINANCIERA) -->
      <div style="margin:14px 6px 30px 6px;">
          <div style="background:#0b3d91;color:#fff;padding:8px 10px;font-weight:700;font-size:13px;">USO INTERNO (PROMOTOR / MICROFINANCIERA)</div>
        <div style="border:1px solid #e6e6e6;padding:10px;font-size:11px;margin-top:6px;">
            <div style="margin-bottom:6px;"><strong>Nombre del promotor:</strong> <?php echo htmlspecialchars($solicitud->nombre_promotor ?? ''); ?></div>
          <div style="margin-bottom:6px;"><strong>Observaciones del promotor:</strong></div>
          <div style="display:inline-block; width:48%; vertical-align:top; border:1px solid #ddd; min-height:80px; padding:6px;">
            <?php echo nl2br(htmlspecialchars($solicitud->observaciones_promotor ?? '')); ?>
          </div>
          <div style="display:inline-block; width:48%; vertical-align:top; border:1px solid #ddd; min-height:80px; padding:6px; margin-left:4%;">
            <div style="margin-bottom:8px;"><strong>Fecha de recepción de solicitud:</strong> <?php
              $fr = '';
              if (!empty($solicitud->fecha_recepcion_solicitud)) { $fr = $solicitud->fecha_recepcion_solicitud; }
              elseif (!empty($solicitud->fecha_recepcion)) { $fr = $solicitud->fecha_recepcion; }
              echo htmlspecialchars($fr ? date('d/m/Y', strtotime($fr)) : '');
            ?></div>
            <div style="min-height:40px;"></div>
          </div>
        </div>
      </div>

    
