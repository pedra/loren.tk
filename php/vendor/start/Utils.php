<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* Printar um MIXED (string, array, object...)
 * $ex true/false exit/echo
 */

function p($val, $ex = true) {
    $val = '<pre>' . print_r($val, true) . '</pre>';
    if ($ex)
        exit($val);
    echo $val;
}

function go($uri = '', $metodo = '', $cod = 302) {
    if (strpos($uri, 'http://') === false || strpos($uri, 'https://') === false)
        $uri = URL . $uri; //se tiver 'http' na uri então será externo.
    if (strtolower($metodo) == 'refresh') {
        header('Refresh:0;url=' . $uri);
    } else {
        header('Location: ' . $uri, TRUE, $cod);
    }
    exit;
}
