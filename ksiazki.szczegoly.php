<?php

// jesli nie podano parametru id, przekieruj do listy książek
if(empty($_GET['id'])) {
    header("Location: ksiazki.lista.php");
    exit();
}

$id = (int)$_GET['id'];

include 'header.php';

use Ibd\Ksiazki;

$ksiazki = new Ksiazki();
$dane = $ksiazki->pobierz($id);
?>

<h2><?=$dane['tytul']?></h2>

<p>
	<a href="ksiazki.lista.php"><i class="fas fa-chevron-left"></i> Powrót</a>
</p>

    <div class="row">
        <div class="col-md-4 col-sm-12">
            <div class="img-fluid">
                <img class="img-thumbnail p-3" src="zdjecia/<?= $dane['zdjecie'] ?>" alt="<?= $dane['tytul'] ?>" class="img-thumbnail" />
            </div>
        </div>
        <div class="col-md-8 col-sm-12 jumbotron">
            <h3> <span class="text-success"><?=$dane['tytul']?></span></h3>
            <h5> Cena: <?=$dane['cena']?> zł</h5>
            <p>  Isbn: <?=$dane['isbn']?> </p>
            <p>  Liczba stron: <?=$dane['liczba_stron']?>  </p>
            <p>  Tytuł: <?=$dane['tytul']?>  </p>
            <p>  Opis: <?=$dane['opis']?>  </p>
        </div>
    </div>


<?php include 'footer.php'; ?>