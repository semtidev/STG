<?php
session_name('semtiGarantiaSession');
session_start();

if ((isset($_POST['action'])) && ($_POST['action'] == 'checksession')) {

    if ($_SESSION['idsession'] && $_SESSION['idsession'] != '') {

        $response = 'true';
    } else {

        $response = 'false';
    }

    echo $response;
}