<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Lista de claves/etiquetas para los reportes financieros (Estado de Resultados - ER)
$config['report_lines'] = [
    'er' => [
        // Agrupaciones (sin títulos ni totales)
        'Disponibilidades',
        'Inversiones negociables y a vencimiento',
        'Utilidad en venta de inversiones en valores',
        'Cartera de créditos',
        'Diferencia Cambiaria',
        'Otros Ingresos Financieros',

        'Obligaciones financieras',
        'Obligaciones con instituciones financieras y otros financiamientos',
        'Pérdida en venta de inversiones en valores',
        'Deuda subordinada y obligaciones convertibles en acciones',
        'Diferencia Cambiaria (gastos)',
        'Otros gastos financieros',

        'Gasto por provisión por incobrabilidad de la cartera de créditos directa',
        'DISMINUCION DE PROVISION PARA CARTERA DE CREDITOS',
        'Ingresos por recuperación de la cartera de creditos directa saneada',
        'Gastos por deterioro de inversiones',
        'Gasto por saneamiento de ingresos financieros',

        'Ingresos operativos diversos',
        'Gastos operativos diversos',

        'Participación en resultados de asociadas',
        'Utilidades en asociadas',
        'Pérdidas en asociadas',

        'Gastos de administración y otros',
        'Gastos con personas vinculadas',

        'Impuesto a la renta'
    ],
    // Líneas para Estado de Situación Financiera (Balance) - agrupaciones (sin títulos ni totales)
    'bs' => [
        // Activo
        'Fondos disponibles',
        'Inversiones negociables y a vencimiento, neto',
        'Cartera de créditos, neto de provisiones por incobrabilidad',
        'Provisiones por incobrabilidad',
        'Bienes recibidos en pago y adjudicados, neto',
        'Otras cuentas por cobrar, neto',
        'Inversiones permanentes',
        'Inmuebles, mobiliario y equipo, neto',
        'Otros activos, neto',

        // Pasivo
        'Obligaciones financieras',
        'Obligaciones con instituciones financieras y por otros financiamientos',
        'Otras cuentas por pagar',
        'Provisiones',
        'Otros pasivos',
        'Deuda Subordinada y Obligaciones convertibles en acciones',

        // Patrimonio
        'Capital social / Aportes',
        'Capital adicional / Aporte adicional',
        'Ajustes al patrimonio',
        'Reservas',
        'Resultados acumulados',
        'Resultados del Ejercicio',

        // Otros
        'Cuentas contingentes',
        'Cuentas de orden'
    ]
];
