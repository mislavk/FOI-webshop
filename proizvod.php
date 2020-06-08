<?php
    session_start();
    if(!isset($_GET["proizvod_id"])){
        header('Location: index.php');
    }
    $conn = new mysqli('localhost', 'iwa_2019', 'foi2019', 'iwa_2019_kz_projekt');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if(isset($_POST['kategorija'])){
        $query = $conn->query("UPDATE proizvod SET kategorija_id = ".$_POST['kategorija']." WHERE proizvod_id = ".$_GET['proizvod_id']);
    }
    if(isset($_POST['naziv']))
    if($_POST['naziv']!=''){
        $query = $conn->query("UPDATE proizvod SET naziv = '".$_POST['naziv']."' WHERE proizvod_id = ".$_GET['proizvod_id']);
    }
    if(isset($_POST['opis']))
    if($_POST['opis']!=''){
        $query = $conn->query("UPDATE proizvod SET opis = '".$_POST['opis']."' WHERE proizvod_id = ".$_GET['proizvod_id']);
    }
    if(isset($_POST['cijena']))
    if($_POST['cijena']!=''){
        $query = $conn->query("UPDATE proizvod SET cijena = '".$_POST['cijena']."' WHERE proizvod_id = ".$_GET['proizvod_id']);
    }
    if(isset($_POST['slika']))
    if($_POST['slika']!=''){
        $query = $conn->query("UPDATE proizvod SET slika = '".$_POST['slika']."' WHERE proizvod_id = ".$_GET['proizvod_id']);
    }
    if(isset($_POST['video']))
    if($_POST['video']!=''){
        $query = $conn->query("UPDATE proizvod SET video = '".$_POST['video']."' WHERE proizvod_id = ".$_GET['proizvod_id']);
    }
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<nav>
    <a href="index.php">Trgovina</a>
    <a href="o_autoru.html">O autoru</a>
    <?php
        if (isset($_SESSION['username'])) {
            echo '<div><a href="logout.php">Logout</a></div>';
            echo '<div><a href="kosarica.php">Kosarica</a></div>';
        } else {
            echo '
            <div>
            <form action="login.php" method="post">
                <label for="username">Username</label>
                <input type="text" id="username" name="username">
                <label for="pwd">Password</label>
                <input type="password" id="pwd" name="pwd">
                <input type="submit" value="Prijava">
            </form>
            </div>
            ';
    }

    ?>
</nav>
<main>
    <?php
    $query = $conn->query("SELECT * FROM proizvod WHERE proizvod_id = " .$_GET['proizvod_id']);
    if ($query->num_rows > 0) {
        while($row = mysqli_fetch_array($query)){
            echo '<img src="'.$row["slika"].'" alt="Slika" width="600" height="600">';
            echo '<h1>' . $row['naziv'] . '</h1>';
            echo '<p>' . $row['opis'] . '</p>';
            echo '<h1 style="color: red;">' . $row['cijena'] . ' KN</h1>';
            echo '<iframe width="800" height="500" src="' . $row['video'] . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            echo '
            <form action="kosarica.php" method="post">
            <input type="hidden" name="cart-add" value="' . $row['proizvod_id'] . '">
            <input type="submit" value="Dodaj u košaricu">
            </form></div>';
        }
    }
    $query->close();
    if (isset($_SESSION['username'])){
        $statement = $conn->query("SELECT moderator_id FROM kategorija JOIN proizvod ON proizvod.kategorija_id = kategorija.kategorija_id WHERE proizvod_id = ". $_GET["proizvod_id"]);
        $rows = mysqli_fetch_array($statement);
        if($rows['moderator_id'] == $_SESSION['user_id']){
            echo '
            <h1>Izmijeni proizvod</h1>
            <form action="" method="post">
            <label for="kategorija">Kategorija proizvoda:</label>
            <select name="kategorija">
            <option></option>';
            $query = $conn->query("SELECT * FROM kategorija");
            if ($query->num_rows > 0) {
                while($row = mysqli_fetch_array($query)){
                    echo '<option value="'. $row['kategorija_id'] .'">'. $row['naziv'] .'</option>';
                }
            }
            $query->close();
            echo '</select><br/>
            <label for="naziv">Naziv proizvoda:</label>
            <input type="text" name="naziv"><br/>
            <label for="opis">Opis:</label>
            <textarea name="opis" rows="4" cols="50"></textarea><br/>
            <label for="cijena">Cijena:</label>
            <input type="number" name="cijena" min="1" max="99999" step="0.01"><br/>
            <label for="slika">Poveznica na sliku:</label>
            <input type="text" name="slika"><br/>
            <label for="video">Poveznica na video:</label>
            <input type="text" name="video"><br/>
            <input type="submit" value="Izmijeni">
            </form></div>';
        }
        $statement->close();
    }
    if($_SESSION['role'] == 1){
        echo '<h2>Popis narudžbi</h2>';
        $count = 0;
        $countp = 0;
        $countn = 0;
        $query = $conn->query("SELECT * FROM iwa_2019_kz_projekt.narudzba WHERE proizvod_id = " . $_GET["proizvod_id"]);
        if ($query->num_rows > 0) {
            while($row = mysqli_fetch_array($query)){
                $count = $count + 1;
                if($row['prihvacena'] == 1){
                    $countp = $countp + 1;
                }
                else{
                    $countn = $countn + 1;
                }
            }
            echo 'Ukupno narudžbi: <b>' . $count . '</b> od čega je <b>'. $countp . '</b> prihvaćeno, a <b>'. $countn . '</b> neprihvaćeno.';
        }
        else{
            echo 'Nema narudžbi za ovaj proizvod.';
        }
        $query->close();
        echo '<h2>Filtriraj</h2>';
        echo '<form action="" method="post">
        <label for="from-date">Od:</label>
        <input type="date" name="from-date">
        <label for="to-date">Do:</label>
        <input type="date" name="to-date">
        <input type="submit" value="Filtriraj">';

        if(!empty($_POST['from-date']))
            if(!empty($_POST['to-date'])){
                if(strtotime($_POST['from-date']) > strtotime($_POST['to-date']))
                echo 'Datum početka mora biti manji ili jednak datumu kraja';
                else{
                    echo '<h2>Ukupno narudžbi</h2>';
                    $from_time = $_POST['from-date'] . ' 00:00:00';
                    $to_time = $_POST['to-date'] . ' 23:59:59';
                    $count = 0;
                    $countp = 0;
                    $countn = 0;
                    $query = $conn->query("SELECT narudzba.* FROM iwa_2019_kz_projekt.narudzba JOIN proizvod ON narudzba.proizvod_id = proizvod.proizvod_id JOIN kategorija ON proizvod.kategorija_id = kategorija.kategorija_id WHERE proizvod.proizvod_id =" . $_GET["proizvod_id"] ." AND datum_kreiranja > '" . $from_time ."' AND datum_kreiranja < '". $to_time ."'");
                    if($query->num_rows > 0) {
                        while($row = mysqli_fetch_array($query)){
                            $count = $count + 1;
                            if($row['prihvacena'] == 1){
                                $countp = $countp + 1;
                            }
                            else{
                                $countn = $countn + 1;
                            }
                        }
                        echo 'Ukupno narudžbi: <b>' . $count . '</b> od čega je <b>'. $countp . '</b> prihvaćeno, a <b>'. $countn . '</b> neprihvaćeno.';
                    }
                    else{
                        echo 'Nema narudžbi za ovu kategoriju.';
                    }
                    $query->close();
                    echo '<h2>Blokirane narudžbe</h2>';
                    $query = $conn->query("SELECT narudzba.narudzba_id AS narudzba_id, korisnik.korisnicko_ime AS korisnik, proizvod.naziv AS naziv, narudzba.blokirana AS blokirana, narudzba.prihvacena AS prihvacena, narudzba.datum_kreiranja AS vrijeme, proizvod.cijena AS cijena
                    FROM narudzba JOIN proizvod ON proizvod.proizvod_id = narudzba.proizvod_id JOIN korisnik ON korisnik.korisnik_id = narudzba.korisnik_id WHERE blokirana = 1 AND proizvod.proizvod_id = ".$_GET["proizvod_id"]." AND  datum_kreiranja > '" . $from_time ."' AND datum_kreiranja < '". $to_time ."'");
                    if ($query->num_rows > 0) {
                        while($row = mysqli_fetch_array($query)){
                                echo $row['narudzba_id'] . ' | ';
                                echo $row['korisnik'] . ' | ';
                                echo $row['naziv']. ' | ';
                                echo $row['blokirana']. ' | ';
                                echo $row['prihvacena']. ' | ';
                                $order_time = date("d.m.Y H:i:s", strtotime($row['vrijeme']));
                                echo $order_time . ' | ';
                                echo $row['cijena']. ' HRK | ';
                                echo '<form action="order.php" method="post">
                                <input type="hidden" value="'. $row['narudzba_id'] .'" name="order-unblock"><input type="submit" value="Odobri">
                                </form>';
                        }
                    }
                    else{
                        echo 'Nema blokiranih narudžbi u kategoriji!';
                    }
                    $query->close();
                }
            }
    }
    $conn->close();
    ?>
</main>
</body>
</html>