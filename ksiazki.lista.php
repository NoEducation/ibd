    <?php
include 'header.php';

use Ibd\Ksiazki;
use Ibd\Kategorie;
use Ibd\Stronicowanie;

// pobieranie kategorii
$kategorie = new Kategorie();
$listaKategorii = $kategorie->pobierzWszystkie();

// pobieranie książek
$ksiazki = new Ksiazki();
$zapytanie = $ksiazki->pobierzZapytanie($_GET);

// dodawanie warunków stronicowania i generowanie linków do stron
$stronicowanie = new Stronicowanie($_GET, $zapytanie['parametry']);
$linki = $stronicowanie->pobierzLinki($zapytanie['sql'], 'ksiazki.lista.php');
$select = $stronicowanie->dodajLimit($zapytanie['sql']);
$lista = $ksiazki->pobierzStrone($select, $zapytanie['parametry']);
$strona = $stronicowanie->getStrona();
$naStrone = $stronicowanie->getnaStronie();
$iloscRekodow = $stronicowanie->getIloscRekordow();

?>

    <h1>Książki</h1>

    <form method="get" action="" class="form-inline mb-4">
        <input type="text" name="fraza" placeholder="szukaj" class="form-control form-control-sm mr-2"
               value="<?= $_GET['fraza'] ?? '' ?>"/>

        <select name="id_kategorii" id="id_kategorii" class="form-control form-control-sm mr-2">
            <option value="">kategoria</option>

            <?php foreach ($listaKategorii as $kat): ?>
                <option
                        value="<?= $kat['id'] ?>"
                    <?= ($_GET['id_kategorii'] ?? '') == $kat['id'] ? 'selected' : '' ?>
                ><?= $kat['nazwa'] ?></option>
            <?php endforeach; ?>
        </select>

        <select name="sortowanie" id="sortowanie" class="form-control form-control-sm mr-2">
            <option value="">sortowanie</option>
            <option value="k.tytul ASC"
                <?= ($_GET['sortowanie'] ?? '') == 'k.tytul ASC' ? 'selected' : '' ?>
            >tytule rosnąco
            </option>
            <option value="k.tytul DESC"
                <?= ($_GET['sortowanie'] ?? '') == 'k.tytul DESC' ? 'selected' : '' ?>
            >tytule malejąco
            </option>
            <option value="k.cena ASC"
                <?= ($_GET['sortowanie'] ?? '') == 'k.cena ASC' ? 'selected' : '' ?>
            >cenie rosnąco
            </option>
            <option value="k.cena DESC"
                <?= ($_GET['sortowanie'] ?? '') == 'k.cena DESC' ? 'selected' : '' ?>
            >cenie malejąco
            </option>
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

    <table class="table table-striped table-condensed" id="ksiazki">
        <thead>
        <tr>
            <th>&nbsp;</th>
            <th>Tytuł</th>
            <th>Autor imie</th>
            <th>Autor nazwisko</th>
            <th>Kategoria</th>
            <th>Cena PLN</th>
            <th>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($lista as $ks) : ?>
            <tr>
                <td style="width: 100px">
                    <?php if (!empty($ks['zdjecie'])) : ?>
                        <img src="zdjecia/<?= $ks['zdjecie'] ?>" alt="<?= $ks['tytul'] ?>" class="img-thumbnail" />
                    <?php else : ?>
                        brak zdjęcia
                    <?php endif; ?>
                </td>
                <td><?= $ks['tytul'] ?></td>
                <td><?= $ks['imie'] ?></td>
                <td><?= $ks['nazwisko'] ?></td>
                <td><?= $ks['nazwa'] ?></td>
                <td><?= $ks['cena'] ?></td>
                <td>
                    <a href="koszyk.dodaj.php?id=<?=$ks['id'] ?>" title="dodaj do koszyka" class="aDodajDoKoszyka">
                        <i class="fas fa-cart-plus"></i>
                    </a>
                    <a href="ksiazki.szczegoly.php?id=<?= $ks['id'] ?>" title="szczegóły">
                        <i class="fas fa-folder-open"></i>
                    </a>
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

<?php include 'footer.php'; ?>