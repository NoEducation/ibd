<?php

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);
session_start();

require_once 'vendor/autoload.php';

use Ibd\Kategorie;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kategorie = new Kategorie();
    if ($kategorie->usun($_GET['id'])) {
        echo 'ok';
    }
}
