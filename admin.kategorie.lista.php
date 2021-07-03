<?php

require_once 'vendor/autoload.php';

use Ibd\Kategorie;
use Ibd\Stronicowanie;

$kategorie = new Kategorie();
$zapytanie = $kategorie->pobierzZapytanie($_GET);

// ichniejesz
//$select = $autorzy->pobierzSelect();
//$lista = $autorzy->pobierzWszystko($select);

$stronicowanie = new Stronicowanie($_GET, $zapytanie['parametry']);
$linki = $stronicowanie->pobierzLinki($zapytanie['sql'], 'admin.kategorie.lista.php');
$select = $stronicowanie->dodajLimit($zapytanie['sql']);
$lista = $kategorie->pobierzStrone($select, $zapytanie['parametry']);
$strona = $stronicowanie->getStrona();
$naStrone = $stronicowanie->getnaStronie();
$iloscRekodow = $stronicowanie->getIloscRekordow();


include 'admin.header.php';
?>

    <h2>
        Kategorie
        <small><a href="admin.kategorie.dodaj.php">dodaj</a></small>
    </h2>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 1): ?>
    <p class="alert alert-success">Kategoria została dodana.</p>
<?php endif; ?>

    <form method="get" action="" class="form-inline mb-4">
        <input type="text" name="nazwa" placeholder="nazwa" class="form-control form-control-sm mr-2"
               value="<?= $_GET['nazwa'] ?? '' ?>"/>

        <select name="sortowanie" id="sortowanie" class="form-control form-control-sm mr-2">
            <option value="">sortowanie</option>
            <option value="a.nazwa ASC"
                <?= ($_GET['sortowanie'] ?? '') == 'k.nazwa ASC' ? 'selected' : '' ?>
            >nazwa rosnąco
            </option>
            <option value="a.nazwa DESC"
                <?= ($_GET['sortowanie'] ?? '') == 'k.nazwa DESC' ? 'selected' : '' ?>
            >nazwa malejąco
            </option>
        </select>

        <button class="btn btn-sm btn-primary" type="submit">Szukaj</button>
    </form>

    <table id="autorzy" class="table table-striped">
        <thead>
        <tr>
            <th>Id</th>
            <th>Nazwa</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($lista as $a): ?>
            <tr>
                <td><?= $a['id'] ?></td>
                <td><?= $a['nazwa'] ?></td>
                <td>
                    <a href="admin.kategorie.edycja.php?id=<?= $a['id'] ?>" title="edycja" class="aEdytujKategorie"><em class="fas fa-pencil-alt"></em></a>
                    <a href="admin.kategorie.usun.php?id=<?= $a['id'] ?>" title="usuń" class="aUsunKategorie"><em class="fas fa-trash"></em></a>
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