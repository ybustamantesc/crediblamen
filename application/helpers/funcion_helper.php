<?php


defined('BASEPATH') or exit('Acción no permitida');

function formatoFechaHora($string)
{
    $dia_sem = date('w', strtotime($string));
    if ($dia_sem == 0) {
        $semana = "Domingo";
    } elseif ($dia_sem == 1) {
        $semana = "Lunes";
    } elseif ($dia_sem == 2) {
        $semana = "Martes";
    } elseif ($dia_sem == 3) {
        $semana = "Miércoles";
    } elseif ($dia_sem == 4) {
        $semana = "Jueves";
    } elseif ($dia_sem == 5) {
        $semana = "Viernes";
    } else {
        $semana = "Sábado";
    }

    $dia = date('d', strtotime($string));

    $mes_num = date('m', strtotime($string));

    $ano = date('Y', strtotime($string));
    $hora = date('h:i:s a', strtotime($string));

    return $dia . '/' . $mes_num . '/' . $ano . ' ' . $hora;
}

function formatoFechaCorta($string)
{

    $dia_sem = date('w', strtotime($string));

    if ($dia_sem == 0) {
        $semana = "Domingo";
    } elseif ($dia_sem == 1) {
        $semana = "Lunes";
    } elseif ($dia_sem == 2) {
        $semana = "Martes";
    } elseif ($dia_sem == 3) {
        $semana = "Miércoles";
    } elseif ($dia_sem == 4) {
        $semana = "Jueves";
    } elseif ($dia_sem == 5) {
        $semana = "Viernes";
    } else {
        $semana = "Sábado";
    }

    $dia = date('d', strtotime($string));

    $mes_num = date('m', strtotime($string));

    $ano = date('Y', strtotime($string));
    $hora = date('h:i:s', strtotime($string));

    return $dia . '/' . $mes_num . '/' . $ano;
}
