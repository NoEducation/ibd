<?php
use Ibd\Ksiazki;
$ksiazki = new Ksiazki();
$dane = $ksiazki->pobierzBestsellery();
?>
<div class="col-md-2">
    <div class="card">
        <div class="card-header">
            Bestsellery
        </div>
        <?php foreach ($dane as $ks) : ?>
            <a href="ksiazki.szczegoly.php?id=<?= $ks['id'] ?>">
                <div class="d-flex flex-row text-default">
                    <div class="p-2"> <img style="width: 35px"  src="zdjecia/<?= $ks['zdjecie'] ?>" alt="<?= $ks['tytul'] ?>"  /></div>
                    <div class="p-2"><b><?= $ks['tytul'] ?> </b> -
                        <span>
                    <?= $ks['imie'] ?> <?= $ks['nazwisko'] ?>
                </span>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
