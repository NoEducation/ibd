<?php
require_once 'vendor/autoload.php';
// jesli nie podano parametru id, przekieruj do listy książek
if(empty($_GET['id'])) {
    header("Location: admin.zmowienia.lista.php");
    exit();
}

use Ibd\Zamowienia;

$id = (int)$_GET['id'];
$zamówienia = new Zamowienia();

if (!empty($_POST)) {

    if ($zamówienia->changeStatus($_POST, $id)) {
        header("Location: admin.zamowienia.szczegoly.php?id=$id&msg=1");
        exit();
    }
}

include 'admin.header.php';

$dane = $zamówienia->getById($id);
$statusy = $zamówienia->getStatuses();

?>
<div class="row">
    <div class="col-sm-12 col-md-6 ">
        <div class="border rounded p-3 mb-1">
            <h2>Zamówienie sczegóły</h2>
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 1): ?>
                <p class="alert alert-success">Status został zmieniony.</p>
            <?php endif; ?>
            <form method="post" action="admin.zamowienia.szczegoly.php?id=<?=$id?>" class=" mb-4">

                <div class="form-group row">
                    <div class="col-md-6 col-sm-12">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control >
                <?php foreach ($statusy as $status) : ?>
                    <option value="<?= $status['id'] ?>" <?= ($dane['id_statusu'] ?? '') == $status['id'] ? 'selected="selected"' : '' ?>><?= $status['nazwa'] ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>

                </div>
                <input class="btn btn-primary" type="submit">
            </form>

            <p>
                <a href="admin.zamowienia.lista.php"><i class="fas fa-chevron-left"></i> Powrót</a>
            </p>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 row">
        <div class="col-md-12 col-sm-12 jumbotron">
            <h4> <span class="text-success">Data dodania: <?=$dane['data_dodania']?></span></h4>
            <h5> Id: <?=$dane['id']?></h5>
            <p>  Id uzytkownika: <?=$dane['id_uzytkownika']?> </p>
            <p>  Id statusu: <?=$dane['id_statusu']?>  </p>
            <p>  Login: <?=$dane['login']?>  </p>
            <p>  Suma: <?=$dane['suma']?>  </p>
            <p>  Liczba produktow: <?=$dane['liczba_produktow']?>  </p>
            <p>  Liczba sztuk: <?=$dane['liczba_sztuk']?>  </p>
        </div>
    </div>
</div>






<?php include 'admin.footer.php'; ?>