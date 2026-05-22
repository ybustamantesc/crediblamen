<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Perfil Integral - <?php echo isset($perfil->solicitud_id)?$perfil->solicitud_id:'-'; ?></title>
    <style>
        @page { margin: 12mm 10mm; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size:11px; color:#222; margin:0; padding:0; line-height:1.12; }
        .header-dark { background:#0b3d91; color:#fff; padding:10px 12px; }
        .header-inner { width:100%; }
        .title { font-size:16px; font-weight:700; letter-spacing:1px; margin:4px 0 6px 0; }
        .sub { font-size:10px; color:#ddd; margin-bottom:6px; }
        .logo { float:right; }
        .clear { clear:both; }
        .box-empty { min-height:320px; padding:12px; }
        .section { clear: both; margin-top: 10px; page-break-inside: avoid; }
        /* Footer: render at end without forcing a page break; reduced spacing */
        footer { display:block; position:static; clear:both; font-size:10px; color:#444; text-align:left; border-top:1px solid #eee; padding-top:4px; margin:6px 10mm 8mm 10mm; }
        img.logo-img { max-height:50px; }
        /* Compact table rules: override inline paddings when generating PDF */
        table, td, th { font-family: DejaVu Sans, Arial, sans-serif; font-size:11px; line-height:1.12; }
        table td, table th { padding:4px 6px !important; vertical-align:middle !important; }
        /* Make header bar smaller */
        .header-dark .title { font-size:15px; }
    </style>
    <?php
    // prepare logo data URI (same preference as other templates)
    $logo_uri = '';
    $logo_paths = array(
        FCPATH . 'public/img/logo.png',
        FCPATH . 'public/img/logo.jpg',
        FCPATH . 'public/img/credi_socios_logo.png',
        FCPATH . 'public/img/credi_socios_logo.jpg'
    );
    foreach ($logo_paths as $p) {
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

    <?php $generated_at = date('d/m/Y H:i'); ?>
    <?php
      $generated_by = 'Usuario';
      try {
          if (isset($this->ion_auth) && method_exists($this->ion_auth, 'user')) {
              $u = $this->ion_auth->user()->row();
              if (!empty($u->username)) $generated_by = $u->username;
              elseif (!empty($u->first_name) || !empty($u->last_name)) $generated_by = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: $generated_by;
          } elseif (isset($this->session)) {
              $uname = $this->session->userdata('username') ?: $this->session->userdata('user_login') ?: $this->session->userdata('email');
              if (!empty($uname)) $generated_by = $uname;
          }
      } catch (Exception $e) {}
      $sol_id = isset($perfil->solicitud_id) ? intval($perfil->solicitud_id) : '';
      // Prepare fallbacks: prefer perfil values, then solicitud fields with common alternative names
      $pf = function($keys, $default = '') use ($perfil, $solicitud) {
          if (!is_array($keys)) $keys = array($keys);
          foreach ($keys as $k) {
              if (!empty($perfil) && isset($perfil->{$k}) && $perfil->{$k} !== null && $perfil->{$k} !== '') return $perfil->{$k};
              if (!empty($solicitud) && isset($solicitud->{$k}) && $solicitud->{$k} !== null && $solicitud->{$k} !== '') return $solicitud->{$k};
          }
          return $default;
      };
      $p_nombre = $pf(array('nombre','primer_nombre','nombres','first_name'));
      $p_segundo_nombre = $pf(array('segundo_nombre','middle_name'));
      $p_primer_apellido = $pf(array('primer_apellido','apellidos','apellido1','last_name'));
      $p_segundo_apellido = $pf(array('segundo_apellido','apellido2'));
      $p_direccion = $pf(array('direccion','direccion_centro_trabajo','direccion_centro','domicilio'));
      $p_telefono = $pf(array('telefono','telefono_domicilio','telefono_fijo'));
      $p_celular = $pf(array('celular','celular_personal','movil'));
      $p_email = $pf(array('email','correo_electronico','correo'));
      $p_profesion = $pf(array('profesion','cargo','ocupacion','ocupacion_actual'));
      $p_ocupacion_actual = $pf(array('ocupacion_actual','ocupacion'));
      $p_nombre_centro_trabajo = $pf(array('nombre_centro_trabajo','empresa','nombre_empresa'));
      $p_direccion_centro_trabajo = $pf(array('direccion_centro_trabajo','direccion_centro','direccion'));
      $p_telefono_centro_trabajo = $pf(array('telefono_centro_trabajo','telefono','telefono_domicilio'));
      $p_fax_centro_trabajo = $pf(array('fax_centro_trabajo','fax'));
      $p_email_centro_trabajo = $pf(array('email_centro_trabajo','correo_electronico_centro_trabajo','email_centro'));
      $p_nombre_conyuge = $pf(array('nombre_conyuge','conyuge_nombre','conyuge_primer_nombre','nombre_conyuge_completo'));
    ?>

    <div class="header-dark">
        <div class="header-inner">
            <div style="float:left; max-width:75%">
                <div class="title">PERFIL INTEGRAL</div>
                <div class="sub">Plantilla en blanco — contenido a reestructurar</div>
            </div>
            <div class="logo">
                <?php if (!empty($logo_uri)): ?>
                    <img class="logo-img" src="<?php echo $logo_uri; ?>" alt="logo">
                <?php endif; ?>
            </div>
            <div class="clear"></div>
        </div>
    </div>

    <div class="container" style="max-width:820px; margin:14px auto;">

        <!-- Sección: Datos personales del cliente (compacta y legible para A4) -->
        <div style="border:1px solid #dcdcdc; border-radius:4px; overflow:hidden;">
            <div style="background:#0b3d91; color:#fff; padding:8px 12px; font-weight:700; font-size:13px;">Datos personales del cliente</div>
            <div style="padding:10px 12px; background:#fff;">
                <table style="width:100%; border-collapse:collapse; font-family: DejaVu Sans, Arial, sans-serif; font-size:11px; color:#222;">
                    <tr>
                        <td style="width:16%; padding:4px 6px;"><strong>Nombres:</strong></td>
                        <td style="width:34%; padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo htmlspecialchars($p_nombre); ?></td>
                        <td style="width:16%; padding:4px 6px;"><strong>Apellidos:</strong></td>
                        <td style="width:34%; padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo htmlspecialchars($p_primer_apellido); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;">&nbsp;</td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">&nbsp;</td>
                        <td style="padding:4px 6px;">&nbsp;</td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>Dirección del domicilio:</strong></td>
                        <td colspan="3" style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo nl2br(htmlspecialchars($p_direccion)); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>N° teléfono del domicilio:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo htmlspecialchars($p_telefono ?: ''); ?></td>
                        <td style="padding:4px 6px;"><strong>N° de celular:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo htmlspecialchars($p_celular); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>Correo electrónico personal:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo htmlspecialchars($p_email ?: ''); ?></td>
                        <td style="padding:4px 6px;"><strong>Profesión:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo htmlspecialchars($p_profesion ?: ''); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>Ocupación actual:</strong></td>
                        <td colspan="3" style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo htmlspecialchars($p_ocupacion_actual); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>Nombre del centro de trabajo:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo htmlspecialchars($p_nombre_centro_trabajo); ?></td>
                        <td style="padding:4px 6px;"><strong>Dirección del centro de trabajo:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo nl2br(htmlspecialchars($p_direccion_centro_trabajo)); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>Correo electrónico del centro de trabajo:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo htmlspecialchars($p_email_centro_trabajo ?: ''); ?></td>
                        <td style="padding:4px 6px;"><strong>Sitio WEB:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo htmlspecialchars($pf(array('sitio_web_centro_trabajo','sitio_web')) ?: ''); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>N° de teléfono del centro de trabajo:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo htmlspecialchars($p_telefono_centro_trabajo ?: ''); ?></td>
                        <td style="padding:4px 6px;"><strong>N° de fax del centro de trabajo:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo htmlspecialchars($p_fax_centro_trabajo ?: ''); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>Apartado postal del centro de trabajo:</strong></td>
                        <td colspan="3" style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo htmlspecialchars($pf(array('conyuge_apartado_postal')) ?: ''); ?></td>
                    </tr>
                    <tr>
                        <td  style="padding:4px 6px;"><strong>Ingreso mensual equivalente a:</strong></td>
                        <td colspan="3" style="padding:4px 6px;"> <strong>Dólares (US$):</strong> 
                        <?php echo isset($perfil->ingreso_mensual_usd)?htmlspecialchars($perfil->ingreso_mensual_usd):''; ?> &nbsp;&nbsp; <strong>Córdobas (C$):</strong> <?php echo isset($perfil->ingreso_mensual_cordobas)?htmlspecialchars($perfil->ingreso_mensual_cordobas):''; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Sección: Documentos y registro -->
        <div style="border:1px solid #dcdcdc; border-radius:4px; overflow:hidden; margin-top:12px;">
            <div style="background:#0b3d91; color:#fff; padding:6px 10px; font-weight:700; font-size:13px;">Documentos y registro</div>
            <div style="padding:8px 10px; background:#fff;">
                <table style="width:100%; border-collapse:collapse; font-size:11px; font-family:DejaVu Sans, Arial, sans-serif; color:#222;">
                    <tr>
                        <td style="width:20%; padding:4px 6px;"><strong>Tipo documento:</strong></td>
                        <td style="width:30%; padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo htmlspecialchars($pf(array('tipo_documento'))); ?></td>
                        <td style="width:20%; padding:4px 6px;"><strong>N° Documento:</strong></td>
                        <td style="width:30%; padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo htmlspecialchars($pf(array('numero_documento','numero_doc'))); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>N° de registro de Cedula:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo htmlspecialchars($pf(array('numero_registro'))); ?></td>
                        <td style="padding:4px 6px;"><strong>País emisión:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo htmlspecialchars($pf(array('pais_emision_documento'))); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>Fecha emisión:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo htmlspecialchars($pf(array('fecha_emision_documento'))); ?></td>
                        <td style="padding:4px 6px;"><strong>Fecha vencimiento:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo htmlspecialchars($pf(array('fecha_vencimiento_documento'))); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>Doc1 - N°</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo htmlspecialchars($pf(array('documento_legal_1_numero'))); ?></td>
                        <td style="padding:4px 6px;"><strong>Doc1 - Fecha emisión</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo htmlspecialchars($pf(array('documento_legal_1_fecha_emision'))); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>Doc2 - N°</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo htmlspecialchars($pf(array('documento_legal_2_numero'))); ?></td>
                        <td style="padding:4px 6px;"><strong>Doc2 - Fecha emisión</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo htmlspecialchars($pf(array('documento_legal_2_fecha_emision'))); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Sección: Datos de la actividad económica, empleo u ocupación del cliente -->
        <div style="border:1px solid #dcdcdc; border-radius:4px; overflow:hidden; margin-top:12px;">
            <div style="background:#0b3d91; color:#fff; padding:6px 10px; font-weight:700; font-size:13px;">Datos de la actividad económica, empleo u ocupación del cliente</div>
            <div style="padding:8px 10px; background:#fff;">
                <table style="width:100%; border-collapse:collapse; font-size:11px; font-family:DejaVu Sans, Arial, sans-serif; color:#222;">
                    <tr>
                        <td style="width:14%; padding:4px 6px; vertical-align:middle;"><strong>Categoría:</strong></td>
                        <td style="width:86%; padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php $cat = isset($perfil->categoria_empleo)?$perfil->categoria_empleo:(isset($perfil->categoria)?$perfil->categoria:''); ?>
                            <span style="margin-right:10px;">[<?php echo ($cat === 'Empleado' ? '✓' : ' '); ?>] Empleado</span>
                            <span style="margin-right:10px;">[<?php echo ($cat === 'Negocio propio' || !empty($perfil->negocio_prop)?'✓':' '); ?>] Negocio propio</span>
                            <span style="margin-right:10px;">[<?php echo ($cat === 'Estudiante' ? '✓' : ' '); ?>] Estudiante</span>
                            <span style="margin-right:10px;">[<?php echo ($cat === 'Ama de casa' ? '✓' : ' '); ?>] Ama de casa</span>
                            <span style="margin-right:10px;">[<?php echo ($cat === 'Jubilado' ? '✓' : ' '); ?>] Jubilado</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px; vertical-align:middle;"><strong>Ocupación:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo isset($perfil->ocupacion)?htmlspecialchars($perfil->ocupacion):''; ?>
                            &nbsp;&nbsp;&nbsp;<strong>Profesión u oficio:</strong> <?php echo isset($perfil->profesion)?htmlspecialchars($perfil->profesion):''; ?>
                            &nbsp;&nbsp;&nbsp;<strong>Antigüedad:</strong> <?php echo isset($perfil->antiguedad)?htmlspecialchars($perfil->antiguedad):''; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px; vertical-align:middle;"><strong>Nombre del centro de trabajo:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php
                                // Fallbacks: perfil->nombre_centro_trabajo, perfil->empresa, perfil->nombre_empresa, solicitud->empresa/nombre_empresa
                                $nombre_centro = '';
                                if (!empty($perfil->nombre_centro_trabajo)) $nombre_centro = $perfil->nombre_centro_trabajo;
                                elseif (!empty($perfil->empresa)) $nombre_centro = $perfil->empresa;
                                elseif (!empty($perfil->nombre_empresa)) $nombre_centro = $perfil->nombre_empresa;
                                elseif (!empty($solicitud) && !empty($solicitud->empresa)) $nombre_centro = $solicitud->empresa;
                                elseif (!empty($solicitud) && !empty($solicitud->nombre_empresa)) $nombre_centro = $solicitud->nombre_empresa;
                            ?>
                            <?php echo htmlspecialchars($nombre_centro); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px; vertical-align:middle;"><strong>Dirección centro de trabajo:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php
                                // Fallbacks for address
                                $dir_centro = '';
                                if (!empty($perfil->direccion_centro_trabajo)) $dir_centro = $perfil->direccion_centro_trabajo;
                                elseif (!empty($perfil->direccion_centro)) $dir_centro = $perfil->direccion_centro;
                                elseif (!empty($perfil->direccion)) $dir_centro = $perfil->direccion;
                                elseif (!empty($solicitud) && !empty($solicitud->direccion_centro_trabajo)) $dir_centro = $solicitud->direccion_centro_trabajo;
                                elseif (!empty($solicitud) && !empty($solicitud->direccion)) $dir_centro = $solicitud->direccion;
                            ?>
                            <?php echo nl2br(htmlspecialchars($dir_centro)); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px; vertical-align:middle;"><strong>N° teléfono centro de trabajo:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo isset($perfil->telefono_centro_trabajo)?htmlspecialchars($perfil->telefono_centro_trabajo):(isset($perfil->telefono)?htmlspecialchars($perfil->telefono):''); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px; vertical-align:middle;"><strong>N° fax centro de trabajo:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo isset($perfil->fax_centro_trabajo)?htmlspecialchars($perfil->fax_centro_trabajo):(isset($solicitud->fax_centro_trabajo)?htmlspecialchars($solicitud->fax_centro_trabajo):''); ?>
                        </td>
                        <td style="padding:4px 6px; vertical-align:middle;"><strong>Apartado postal:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo isset($perfil->apartado_postal)?htmlspecialchars($perfil->apartado_postal):(isset($solicitud->apartado_postal)?htmlspecialchars($solicitud->apartado_postal):''); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:6px 6px; vertical-align:middle;"><strong>Cobertura (negocio propio):</strong></td>
                        <td style="padding:6px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php
                                // Map zona_cobertura or cobertura_* flags to checkbox display
                                $zona = '';
                                if (!empty($perfil->zona_cobertura)) $zona = $perfil->zona_cobertura;
                                elseif (!empty($solicitud) && !empty($solicitud->zona_cobertura)) $zona = $solicitud->zona_cobertura;
                                // also support older boolean flags on perfil
                                $intern = !empty($perfil->cobertura_internacional) || (!empty($zona) && strtolower($zona)=='internacional');
                                $regional = !empty($perfil->cobertura_regional) || (!empty($zona) && strtolower($zona)=='regional');
                                $nacional = !empty($perfil->cobertura_nacional) || (!empty($zona) && strtolower($zona)=='nacional');
                                $local = !empty($perfil->cobertura_local) || (!empty($zona) && strtolower($zona)=='local');
                            ?>
                            <span style="margin-right:12px;">[<?php echo ($intern ? '✓' : ' '); ?>] Zona internacional</span>
                            <span style="margin-right:12px;">[<?php echo ($regional ? '✓' : ' '); ?>] Zona regional C.A.</span>
                            <span style="margin-right:12px;">[<?php echo ($nacional ? '✓' : ' '); ?>] Zona nacional</span>
                            <span style="margin-right:12px;">[<?php echo ($local ? '✓' : ' '); ?>] Zona local</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:6px 6px; vertical-align:middle;"><strong>Ingreso mensual equivalente a:</strong></td>
                        <td style="padding:6px 6px;">
                            <strong>Dólares (US$):</strong> <?php echo isset($perfil->ingreso_mensual_usd)?htmlspecialchars($perfil->ingreso_mensual_usd):''; ?>
                            &nbsp;&nbsp;&nbsp;
                            <strong>Córdobas (C$):</strong> <?php echo isset($perfil->ingreso_mensual_cordobas)?htmlspecialchars($perfil->ingreso_mensual_cordobas):''; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Sección: Datos del cónyuge o unión de hecho -->
        <div style="border:1px solid #dcdcdc; border-radius:4px; overflow:hidden; margin-top:12px;">
            <div style="background:#0b3d91; color:#fff; padding:6px 10px; font-weight:700; font-size:13px;">Datos del cónyuge o en unión de hecho estable del cliente</div>
            <div style="padding:8px 10px; background:#fff;">
                <table style="width:100%; border-collapse:collapse; font-size:11px; font-family:DejaVu Sans, Arial, sans-serif; color:#222;">
                    <tr>
                        <td style="width:16%; padding:4px 6px;"><strong>Nombre completo del cónyuge:</strong></td>
                        <td colspan="3" style="width:84%; padding:4px 6px; border-bottom:1px solid #e6e6e6;">
                            <?php echo htmlspecialchars($p_nombre_conyuge); ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>Dirección del domicilio:</strong></td>
                        <td colspan="3" style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo isset($perfil->conyuge_direccion)?nl2br(htmlspecialchars($perfil->conyuge_direccion)):''; ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>N° teléfono del domicilio:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo isset($perfil->conyuge_telefono_domicilio)?htmlspecialchars($perfil->conyuge_telefono_domicilio):'NA'; ?></td>
                        <td style="padding:4px 6px;"><strong>N° de celular:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo isset($perfil->conyuge_celular)?htmlspecialchars($perfil->conyuge_celular):''; ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>Correo electrónico personal:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo isset($perfil->conyuge_email_personal)?htmlspecialchars($perfil->conyuge_email_personal):'NA'; ?></td>
                        <td style="padding:4px 6px;"><strong>Profesión:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo isset($perfil->conyuge_profesion)?htmlspecialchars($perfil->conyuge_profesion):'NA'; ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>Ocupación actual:</strong></td>
                        <td colspan="3" style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo isset($perfil->conyuge_ocupacion_actual)?htmlspecialchars($perfil->conyuge_ocupacion_actual):''; ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>Nombre del centro de trabajo:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo isset($perfil->conyuge_nombre_centro_trabajo)?htmlspecialchars($perfil->conyuge_nombre_centro_trabajo):''; ?></td>
                        <td style="padding:4px 6px;"><strong>Dirección del centro de trabajo:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo isset($perfil->conyuge_direccion_centro_trabajo)?nl2br(htmlspecialchars($perfil->conyuge_direccion_centro_trabajo)):''; ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>Correo electrónico del centro de trabajo:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo isset($perfil->conyuge_email_centro_trabajo)?htmlspecialchars($perfil->conyuge_email_centro_trabajo):'NA'; ?></td>
                        <td style="padding:4px 6px;"><strong>Sitio WEB:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo isset($perfil->conyuge_sitio_web)?htmlspecialchars($perfil->conyuge_sitio_web):'NA'; ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>N° de teléfono del centro de trabajo:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo isset($perfil->conyuge_telefono_centro_trabajo)?htmlspecialchars($perfil->conyuge_telefono_centro_trabajo):'NA'; ?></td>
                        <td style="padding:4px 6px;"><strong>N° de fax del centro de trabajo:</strong></td>
                        <td style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo isset($perfil->conyuge_fax_centro_trabajo)?htmlspecialchars($perfil->conyuge_fax_centro_trabajo):'NA'; ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>Apartado postal del centro de trabajo:</strong></td>
                        <td colspan="3" style="padding:4px 6px; border-bottom:1px solid #e6e6e6;"><?php echo isset($perfil->conyuge_apartado_postal)?htmlspecialchars($perfil->conyuge_apartado_postal):'NA'; ?></td>
                    </tr>
                    <tr>
                        <td style="padding:4px 6px;"><strong>Ingreso mensual equivalente a:</strong></td>
                        <td colspan="3" style="padding:4px 6px;"> <strong>Dólares (US$):</strong> <?php echo isset($perfil->conyuge_ingreso_usd)?htmlspecialchars($perfil->conyuge_ingreso_usd):''; ?> &nbsp;&nbsp; <strong>Córdobas (C$):</strong> <?php echo isset($perfil->conyuge_ingreso_cordobas)?htmlspecialchars($perfil->conyuge_ingreso_cordobas):''; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Documentos legales doc1/doc2 section removed per user preference -->

    </div>

        <!-- Sección: Información acerca de la relación de negocios con Crediblamen S.A (diseño compacto) -->
        <div class="section" style="max-width:820px; margin:18px auto 10px; font-size:10.5px; clear:both; page-break-inside:avoid;">
            <div style="border:1px solid #cfcfcf; border-radius:3px; overflow:hidden;">
                <div style="background:#0b3d91; color:#fff; padding:6px 10px; font-weight:700; font-size:12px;">Información acerca de la relación de negocios con Crediblamen S.A</div>
                <div style="padding:8px 10px; background:#fff;">
                    <div style="margin-bottom:6px; color:#333;">(Debe llenarse para cada una de las relaciones de negocios que tienen el cliente, si tiene más de una utilice el anexo)</div>

                    <!-- Primero: Tipo de relación (columna derecha original) mostrado en bloque superior -->
                    <div style="margin-bottom:8px;">
                        <?php
                            // Normalize stored tipo_relacion: accept array, JSON string or serialized
                            $r = [];
                            if (isset($perfil->tipo_relacion) && $perfil->tipo_relacion !== null) {
                                if (is_array($perfil->tipo_relacion)) {
                                    $r = $perfil->tipo_relacion;
                                } elseif (is_string($perfil->tipo_relacion)) {
                                    $try = @json_decode($perfil->tipo_relacion, true);
                                    if (is_array($try)) $r = $try;
                                    else {
                                        // try unserialize
                                        $try2 = @unserialize($perfil->tipo_relacion);
                                        if (is_array($try2)) $r = $try2;
                                        else {
                                            // fallback: comma-separated
                                            $r = array_filter(array_map('trim', explode(',', $perfil->tipo_relacion)));
                                        }
                                    }
                                }
                            }
                        ?>
                        <div style="font-weight:700; margin-bottom:6px;">Tipo de relación de negocios con Crediblamen S.A:</div>
                        <div style="font-size:10.5px; line-height:18px;">
                            <div><span style="display:inline-block;width:18px;height:18px;border:1px solid #333;margin-right:10px;line-height:18px;text-align:center;font-size:12px;vertical-align:middle;color:#0b3d91;font-weight:700;border-radius:2px;">
                                <?php echo in_array('Compra y venta de bienes inmobiliarios',$r)?'✓':''; ?></span>Compra y venta de bienes inmobiliarios</div>
                            <div><span style="display:inline-block;width:18px;height:18px;border:1px solid #333;margin-right:10px;line-height:18px;text-align:center;font-size:12px;vertical-align:middle;color:#0b3d91;font-weight:700;border-radius:2px;">
                                <?php echo in_array('Administración de dinero, valores u otros activos',$r)?'✓':''; ?></span>Administración de dinero, valores u otros activos</div>
                            <div><span style="display:inline-block;width:18px;height:18px;border:1px solid #333;margin-right:10px;line-height:18px;text-align:center;font-size:12px;vertical-align:middle;color:#0b3d91;font-weight:700;border-radius:2px;">
                                <?php echo in_array('Otorgamiento de microcréditos a personas naturales y jurídicas',$r)?'✓':''; ?></span>Otorgamiento de microcréditos a personas naturales y jurídicas</div>
                            <div><span style="display:inline-block;width:18px;height:18px;border:1px solid #333;margin-right:10px;line-height:18px;text-align:center;font-size:12px;vertical-align:middle;color:#0b3d91;font-weight:700;border-radius:2px;">
                                <?php echo in_array('Organización de contribuciones para la creación, operación o administración',$r)?'✓':''; ?></span>Organización de contribuciones para la creación, operación o administración</div>
                            <div><span style="display:inline-block;width:18px;height:18px;border:1px solid #333;margin-right:10px;line-height:18px;text-align:center;font-size:12px;vertical-align:middle;color:#0b3d91;font-weight:700;border-radius:2px;">
                                <?php echo in_array('Creación, operación o administración de personas jurídicas u otras',$r)?'✓':''; ?></span>Creación, operación o administración de personas jurídicas u otras</div>
                            <div style="margin-top:6px;"><span style="display:inline-block;width:18px;height:18px;border:1px solid #333;margin-right:10px;line-height:18px;text-align:center;font-size:12px;vertical-align:middle;color:#0b3d91;font-weight:700;border-radius:2px;">
                                <?php echo in_array('Otro',$r)?'✓':''; ?></span>Otro (indique): <strong><?php echo isset($perfil->tipo_relacion_otro)?htmlspecialchars($perfil->tipo_relacion_otro):''; ?></strong></div>
                        </div>
                    </div>

                    <!-- Segundo: Origen de los fondos mostrado en bloque inferior para evitar choque -->
                    <div style="border-top:1px solid #ddd; padding-top:8px; margin-top:6px;">
                        <div style="font-weight:700; margin-bottom:6px;">Origen de los fondos y activos vinculados a la relación de negocios:</div>
                        <?php
                            // Normalize stored origen_fondos: accept array, JSON string or serialized
                            $o = [];
                            if (isset($perfil->origen_fondos) && $perfil->origen_fondos !== null) {
                                if (is_array($perfil->origen_fondos)) {
                                    $o = $perfil->origen_fondos;
                                } elseif (is_string($perfil->origen_fondos)) {
                                    $try = @json_decode($perfil->origen_fondos, true);
                                    if (is_array($try)) $o = $try;
                                    else {
                                        $try2 = @unserialize($perfil->origen_fondos);
                                        if (is_array($try2)) $o = $try2;
                                        else $o = array_filter(array_map('trim', explode(',', $perfil->origen_fondos)));
                                    }
                                }
                            }
                        ?>
                        <table style="width:100%; font-size:10.5px; border-collapse:collapse;">
                            <tr>
                                <td style="width:50%; vertical-align:top; padding:2px 6px;">
                                    <div style="line-height:18px;"><span style="display:inline-block;width:18px;height:18px;border:1px solid #333;margin-right:8px;line-height:18px;text-align:center;font-size:12px;vertical-align:middle;color:#0b3d91;font-weight:700;border-radius:2px;">
                                        <?php echo in_array('Préstamo',$o)?'✓':''; ?></span>Préstamo</div>
                                    <div style="line-height:18px;"><span style="display:inline-block;width:18px;height:18px;border:1px solid #333;margin-right:8px;line-height:18px;text-align:center;font-size:12px;vertical-align:middle;color:#0b3d91;font-weight:700;border-radius:2px;">
                                        <?php echo in_array('Venta de activos',$o)?'✓':''; ?></span>Venta de activos</div>
                                    <div style="line-height:18px;"><span style="display:inline-block;width:18px;height:18px;border:1px solid #333;margin-right:8px;line-height:18px;text-align:center;font-size:12px;vertical-align:middle;color:#0b3d91;font-weight:700;border-radius:2px;">
                                        <?php echo in_array('Ahorro',$o)?'✓':''; ?></span>Ahorro</div>
                                    <div style="line-height:18px;"><span style="display:inline-block;width:18px;height:18px;border:1px solid #333;margin-right:8px;line-height:18px;text-align:center;font-size:12px;vertical-align:middle;color:#0b3d91;font-weight:700;border-radius:2px;">
                                        <?php echo in_array('Transferencia de fondos',$o)?'✓':''; ?></span>Transferencia de fondos</div>
                                </td>
                                <td style="width:50%; vertical-align:top; padding:2px 6px;">
                                    <div style="line-height:18px;"><span style="display:inline-block;width:18px;height:18px;border:1px solid #333;margin-right:8px;line-height:18px;text-align:center;font-size:12px;vertical-align:middle;color:#0b3d91;font-weight:700;border-radius:2px;">
                                        <?php echo in_array('salarios',$o)?'✓':''; ?></span>salarios</div>
                                    <div style="line-height:18px;"><span style="display:inline-block;width:18px;height:18px;border:1px solid #333;margin-right:8px;line-height:18px;text-align:center;font-size:12px;vertical-align:middle;color:#0b3d91;font-weight:700;border-radius:2px;">
                                        <?php echo in_array('Negocios',$o)?'✓':''; ?></span>Negocios</div>
                                    <div style="line-height:18px;"><span style="display:inline-block;width:18px;height:18px;border:1px solid #333;margin-right:8px;line-height:18px;text-align:center;font-size:12px;vertical-align:middle;color:#0b3d91;font-weight:700;border-radius:2px;">
                                        <?php echo in_array('Remesas',$o)?'✓':''; ?></span>Remesas</div>
                                    <div style="line-height:18px;"><span style="display:inline-block;width:18px;height:18px;border:1px solid #333;margin-right:8px;line-height:18px;text-align:center;font-size:12px;vertical-align:middle;color:#0b3d91;font-weight:700;border-radius:2px;">
                                        <?php echo in_array('Herencias',$o)?'✓':''; ?></span>Herencias</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:2px 6px;">
                                    <div style="line-height:18px;"><span style="display:inline-block;width:14px;height:14px;border:1px solid #333;margin-right:6px;"><?php echo in_array('Donación',$o)?'✓':''; ?></span>Donación</div>
                                </td>
                                <td style="padding:2px 6px;">
                                    <div style="line-height:18px;"><span style="display:inline-block;width:14px;height:14px;border:1px solid #333;margin-right:6px;"><?php echo in_array('Dividendos',$o)?'✓':''; ?></span>Dividendos</div>
                                </td>
                            </tr>
                        </table>

                        <div style="margin-top:8px;">
                            <strong>Otros (explicar):</strong>
                            <div style="min-height:18px; border-bottom:1px solid #000; margin-top:4px;">&nbsp; <?php echo isset($perfil->origen_otros)?htmlspecialchars($perfil->origen_otros):''; ?></div>
                        </div>
                    </div>

                    <!-- Propósito -->
                    <div style="margin-top:10px;">
                        <strong>Propósito de la relación (explicar):</strong>
                        <?php
                            $prop = '';
                            if (!empty($perfil->proposito_relacion)) $prop = $perfil->proposito_relacion;
                            elseif (!empty($solicitud) && !empty($solicitud->proposito_relacion)) $prop = $solicitud->proposito_relacion;
                            elseif (!empty($solicitud) && !empty($solicitud->proposito)) $prop = $solicitud->proposito;
                        ?>
                        <div style="border-top:1px solid #000; margin-top:6px; padding-top:6px; min-height:18px;">&nbsp; <?php echo htmlspecialchars($prop); ?></div>
                    </div>

                    <!-- Actividad esperada: tabla compacta o texto libre -->
                    <div style="margin-top:14px;">
                        <div style="font-weight:700; margin-bottom:6px;">Actividad esperada</div>
                        <?php
                            // Prefer JSON field 'actividad_esperada_json' if present, otherwise plain text
                            $act_json = null;
                            if (!empty($perfil->actividad_esperada_json)) $act_json = $perfil->actividad_esperada_json;
                            elseif (!empty($perfil->actividad_esperada)) $act_json = $perfil->actividad_esperada;
                            elseif (!empty($solicitud) && !empty($solicitud->actividad_esperada_json)) $act_json = $solicitud->actividad_esperada_json;
                            elseif (!empty($solicitud) && !empty($solicitud->actividad_esperada)) $act_json = $solicitud->actividad_esperada;

                            $rows = [];
                            if (!empty($act_json)) {
                                $dec = @json_decode($act_json, true);
                                if (is_array($dec)) $rows = $dec;
                                else {
                                    $try = @unserialize($act_json);
                                    if (is_array($try)) $rows = $try;
                                }
                            }
                        ?>
                        <?php if (!empty($rows) && is_array($rows)): ?>
                            <table style="width:100%; border-collapse:collapse; font-size:10px; margin-bottom:8px;">
                                <thead>
                                    <tr>
                                        <th style="border:1px solid #000; padding:6px; text-align:left; background:#f0f0f0;">Número de transacciones</th>
                                        <th style="border:1px solid #000; padding:6px; text-align:left; background:#f0f0f0;">Monto promedio</th>
                                        <th style="border:1px solid #000; padding:6px; text-align:left; background:#f0f0f0;">Periodo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $r):
                                        $n = isset($r['numero_transacciones']) ? $r['numero_transacciones'] : (isset($r['actividad_fila']) ? $r['actividad_fila'] : '');
                                        $m = isset($r['monto_promedio']) ? $r['monto_promedio'] : (isset($r['monto']) ? $r['monto'] : '');
                                        $p = isset($r['periodo']) ? $r['periodo'] : (isset($r['period']) ? $r['period'] : '');
                                    ?>
                                    <tr>
                                        <td style="border:1px solid #000; padding:6px; min-height:20px;"><?php echo htmlspecialchars($n); ?></td>
                                        <td style="border:1px solid #000; padding:6px; min-height:20px;"><?php echo htmlspecialchars($m); ?></td>
                                        <td style="border:1px solid #000; padding:6px; min-height:20px;"><?php echo htmlspecialchars($p); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <?php
                                // fallback: render plain text if no structured rows
                                $act_text = '';
                                if (!empty($perfil->actividad_esperada)) $act_text = $perfil->actividad_esperada;
                                elseif (!empty($solicitud) && !empty($solicitud->actividad_esperada)) $act_text = $solicitud->actividad_esperada;
                            ?>
                            <div style="padding:6px 8px; background:#fff; border:1px solid #eee; min-height:40px; white-space:pre-wrap;"><?php echo nl2br(htmlspecialchars($act_text)); ?></div>
                        <?php endif; ?>
                    </div>

                    

                    <!-- Declaración de veracidad y casillas Si/No -->
                    <div style="margin-top:12px; padding:10px; background:#f7f7f7; border:1px solid #e0e0e0;">
                        <div style="font-weight:700; margin-bottom:6px;">Declaración de Veracidad y Autorizaciones Especiales:</div>
                        <div style="font-size:11px; margin-bottom:8px;">
                            Declaro y afirmo que los datos proveídos en este PIC, constituyen información veraz y verificable.<br>
                            Autorizo a Crediblamen S.A a verificar, por cualquier medio legal, toda la información que he proveído para efectos de las relaciones que sustentan este perfil
                        </div>
                        <?php $decl = isset($perfil->declara_veracidad)?$perfil->declara_veracidad:'Si'; ?>
                        <div style="font-size:11px;">
                            <label style="margin-right:14px;"><span style="display:inline-block;width:16px;height:16px;border:1px solid #333;text-align:center;vertical-align:middle;line-height:16px;margin-right:8px;font-size:12px;color:#0b3d91;font-weight:700;border-radius:2px;">
                                <?php echo ($decl=='Si' || $decl===true)?'✓':''; ?></span> Si</label>
                            <label style="margin-right:14px;"><span style="display:inline-block;width:16px;height:16px;border:1px solid #333;text-align:center;vertical-align:middle;line-height:16px;margin-right:8px;font-size:12px;color:#0b3d91;font-weight:700;border-radius:2px;">
                                <?php echo ($decl=='No' || $decl===false)?'✓':''; ?></span> No</label>
                        </div>
                    </div>

                    <!-- Lugar y fecha de llenado: mostrar justo debajo de la Declaración de Veracidad -->
                    <?php
                        $lugar = '';
                        if (!empty($perfil) && isset($perfil->ciudad) && $perfil->ciudad !== null && $perfil->ciudad !== '') {
                            $lugar = $perfil->ciudad;
                        } elseif (!empty($solicitud) && isset($solicitud->ciudad) && $solicitud->ciudad !== null && $solicitud->ciudad !== '') {
                            $lugar = $solicitud->ciudad;
                        }
                        $hoy = date('d/m/Y');
                        $lf = trim(($lugar !== '' ? $lugar . ', ' : '') . $hoy);
                    ?>
                    <div style="margin-top:6px; font-size:11px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; white-space:nowrap;">
                            <div style="font-weight:600;">Lugar y fecha de llenado del presente perfil:</div>
                            <div style="text-align:right; margin-left:12px;"><?php echo htmlspecialchars($lf); ?></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <footer>
      <span style="display:inline-block; margin-right:18px;"><strong>Generado por:</strong> <?php echo htmlspecialchars($generated_by); ?></span>
      <span style="display:inline-block; margin-right:18px;"><strong>Fecha:</strong> <?php echo htmlspecialchars($generated_at); ?></span>
      <span style="display:inline-block;"><strong>Solicitud ID:</strong> <?php echo htmlspecialchars($sol_id); ?></span>
      
    </footer>

</body>
</html>
