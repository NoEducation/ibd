<?php

require_once 'vendor/autoload.php';

use Ibd\Kategorie;

if(empty($_GET['id'])) {
    header("Location: admin.kategorie.lista.php");
    exit();
} else {
    $id = (int)$_GET['id'];
}

$kategorie = new Kategorie();

if(!empty($_POST)) {
    if($kategorie->edytuj($_POST, $id)) {
        header("Location: admin.kategorie.edycja.php?id=$id&msg=1");
        exit();
    }
}

include 'admin.header.php';

$kategorie = new Kategorie();

$dane = $kategorie->pobierz($id);
?>

    <h2>
        Kategorie
        <small>edycja</small>
    </h2>

<?php if(isset($_GET['msg']) && $_GET['msg'] == 1): ?>
    <p class="alert alert-success">Kategoria została zapisana</p>
<?php endif; ?>

    <form method="post" action="" class="">
        <div class="form-group">
            <label for="nazwa">Nazwa</label>
            <input type="text" id="nazwa" name="nazwa" class="form-control" value="<?=$dane['nazwa'] ?>" />
        </div>

        <button type="submit" class="btn btn-primary">Zapisz</button>

    </form>

<?php include 'admin.footer.php'; ?>