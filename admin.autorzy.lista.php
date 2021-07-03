<?php

require_once 'vendor/autoload.php';

use Ibd\Autorzy;
use Ibd\Stronicowanie;

// pobieranie książek
//$ksiazki = new Ksiazki();
//$zapytanie = $ksiazki->pobierzZapytanie($_GET);
$autorzy = new Autorzy();
$zapytanie = $autorzy->pobierzZapytanie($_GET);

// ichniejesz
//$select = $autorzy->pobierzSelect();
//$lista = $autorzy->pobierzWszystko($select);

$stronicowanie = new Stronicowanie($_GET, $zapytanie['parametry']);
$linki = $stronicowanie->pobierzLinki($zapytanie['sql'], 'admin.autorzy.lista.php');
$select = $stronicowanie->dodajLimit($zapytanie['sql']);
$lista = $autorzy->pobierzStrone($select, $zapytanie['parametry']);
$strona = $stronicowanie->getStrona();
$naStrone = $stronicowanie->getnaStronie();
$iloscRekodow = $stronicowanie->getIloscRekordow();


include 'admin.header.php';
?>

<h2>
    Autorzy
    <small><a href="admin.autorzy.dodaj.php">dodaj</a></small>
</h2>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 1): ?>
    <p class="alert alert-success">Autor został dodany.</p>
<?php endif; ?>

    <form method="get" action="" class="form-inline mb-4">
        <input type="text" name="imie" placeholder="imie" class="form-control form-control-sm mr-2"
               value="<?= $_GET['imie'] ?? '' ?>"/>

        <input type="text" name="nazwisko" placeholder="nazwisko" class="form-control form-control-sm mr-2"
               value="<?= $_GET['nazwisko'] ?? '' ?>"/>

        <select name="sortowanie" id="sortowanie" class="form-control form-control-sm mr-2">
            <option value="">sortowanie</option>
            <option value="a.nazwisko ASC"
                <?= ($_GET['sortowanie'] ?? '') == 'a.nazwisko ASC' ? 'selected' : '' ?>
            >nazwisku rosnąco
            </option>
            <option value="a.nazwisko DESC"
                <?= ($_GET['sortowanie'] ?? '') == 'a.nazwisko DESC' ? 'selected' : '' ?>
            >nazwisko malejąco
            </option>
        </select>

        <button class="btn btn-sm btn-primary" type="submit">Szukaj</button>
    </form>

<table id="autorzy" class="table table-striped">
    <thead>
        <tr>
            <th>Id</th>
            <th>Imię</th>
            <th>Nazwisko</th>
            <th>Ilosc</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lista as $a): ?>
            <tr>
                <td><?= $a['id'] ?></td>
                <td><?= $a['imie'] ?></td>
                <td><?= $a['nazwisko'] ?></td>
                <td><?= $a['ilosc'] ?></td>
                <td>
                    <a href="admin.autorzy.edycja.php?id=<?= $a['id'] ?>" title="edycja" class="aEdytujAutora"><em class="fas fa-pencil-alt"></em></a>
                    <a href="admin.autorzy.usun.php?id=<?= $a['id'] ?>" title="usuń" class="aUsunAutora"><em class="fas fa-trash"></em></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

    <div class="d-flex flex-row justify-content-between">
        <nav class="text-center">
            <?= $linki ?>
        </nav>
        <div class="text-center">
            <div class="text-center">
                <p>Wyświetlono <?= ($strona * $naStrone) + 1 ?> -
                    <?= ($strona * $naStrone) + $naStrone > $iloscRekodow ? $iloscRekodow : ($strona * $naStrone) + $naStrone ?>
                    z <?= $iloscRekodow ?>
                </p>
            </div>
        </div>
    </div>

<?php include 'admin.footer.php'; ?>