<?php
ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);
session_start();
require_once 'vendor/autoload.php';

use Ibd\Koszyk;

$koszyk = new Koszyk();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['id'])) {
    if ($koszyk->czyIstnieje($_GET['id'], session_id())) {

        $rekord = $koszyk->pobierzKsiazkeWKoszyku($_GET['id'], session_id());
        $koszyk->zaktualizuj($rekord["liczba_sztuk"], $rekord["id"]);

        echo 'ok';
    } else {
			if ($koszyk->dodaj($_GET['id'], session_id())) {
				echo 'ok';
			}
    }
}