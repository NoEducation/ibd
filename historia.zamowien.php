
<?php
require_once 'vendor/autoload.php';

if(!isset($_SESSION['IS_LOGEDIN']) == 'Y'){
    session_start();
}

$idUzytkonwika = $_SESSION['id_uzytkownika'];

if(empty($idUzytkonwika)) {
    header("Location: index.php");
    exit();
}

include 'header.php';

use Ibd\Zamowienia;
$zamówenia = new Zamowienia();
$dane = $zamówenia->pobierzWszystkie()
?>

<div>
    <h2>Historia zamówien</h2>

    <table class="table table-striped table-condensed" id="koszyk">
        <thead>
        <tr>
            <th>Id użytkownika</th>
            <th>Id status</th>
            <th>Data dodania</th>
            <th>Login</th>
            <th>Status</th>
            <th>Suma</th>
            <th>Liczba produktow</th>
            <th>Liczba sztuk</th>
        </tr>
        </thead>

        <?php if(count($dane) > 0): ?>
            <tbody>
            <?php foreach($dane as $item): ?>
                <tr>
                    <td><?= $item['id_uzytkownika'] ?></td>
                    <td><?= $item['id_statusu'] ?></td>
                    <td><?= $item['data_dodania'] ?></td>
                    <td><?= $item['login'] ?></td>
                    <td><?= $item['status'] ?></td>
                    <td><?= $item['suma'] ?></td>
                    <td><?= $item['liczba_produktow']?></td>
                    <td><?= $item['liczba_sztuk'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        <?php else: ?>
            <tr><td colspan="8" style="text-align: center">Brak zamówień</td></tr>
        <?php endif; ?>
    </table>

</div>

<?php include 'footer.php'; ?>