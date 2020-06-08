<?php
    session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<nav>
    <a href="index.php" id="active">Trgovina</a>
    <a href="o_autoru.html">O autoru</a>
    <?php
        if (isset($_SESSION['username'])) {
            if($_SESSION['user_id'] == "0"){
                echo '<div><a href="logout.php">Logout</a></div>';
            }
            else{
                echo '<div><a href="logout.php">Logout</a></div>';
                echo '<div><a href="kosarica.php">Kosarica</a></div>';
            }
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
<div class="clearfix"></div>
<main>
    <?php
        $conn = new mysqli('localhost', 'iwa_2019', 'foi2019', 'iwa_2019_kz_projekt');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        echo '<h1> Najprodavanije </h1>';
        $query = $conn->query("SELECT * FROM proizvod WHERE proizvod_id IN (SELECT proizvod_id FROM narudzba ORDER BY narudzba.kolicina) LIMIT 0,20");
        if ($query->num_rows > 0) {
            while($row = mysqli_fetch_array($query)){
                echo '<div style="float:left;"><img src="'.$row["slika"].'" alt="Slika" width="300" height="300">';
                echo '<h3>' . $row['naziv'] . '</h3>';
                echo '<p>' . $row['opis'] . '</p>';
                echo '<p style="color: red;">' . $row['cijena'] . ' KN</p>';
                echo '
                <form action="proizvod.php" method="get">
                <input type="hidden" name="proizvod_id" value="' . $row['proizvod_id'] . '">
                <input type="submit" value="Pregledaj">
                </form></div>';
            }
        }
        else{
            echo 'Nijedan proizvod nije prodan.';
        }
        echo '<div class="clearfix"></div>';
        $query->close();
        echo '<hr/>';
        if(isset($_SESSION['role'])){
            if($_SESSION['role']<='3'){
                echo '<div class="clearfix"></div>';
                echo '<h1> Po kategorijama </h1>';
                $query = $conn->query("SELECT * FROM kategorija");
                if ($query->num_rows > 0) {
                    while($row = mysqli_fetch_array($query)){
                        $kategorijaid = $row['kategorija_id'];
                        echo '<h2>' . $row['naziv'] . '</h2>';
                        $query2 = $conn->query("SELECT * FROM proizvod WHERE kategorija_id = $kategorijaid");
                        if ($query2->num_rows > 0) {
                            while($row2 = mysqli_fetch_array($query2)){
                                echo '<div style="float:left;"><img src="'.$row2["slika"].'" alt="Slika" width="300" height="300">';
                                echo '<h3>' . $row2['naziv'] . '</h3>';
                                echo '<p>' . $row2['opis'] . '</p>';
                                echo '<p style="color: red;">' . $row2['cijena'] . ' KN</p>';
                                echo '
                                <form action="proizvod.php" method="get">
                                <input type="hidden" name="proizvod_id" value="' . $row2['proizvod_id'] . '">
                                <input type="submit" value="Pregledaj">
                                </form></div>';
                            }
                        }
                        else{
                            echo 'Nema raspoloživih proizvoda.';
                        }
                        echo '<div class="clearfix"></div>';
                    }
                }
                $query2->close();
                $query->close();
                echo '<hr/>';
                if($_SESSION['role']<='2'){
                    echo '<h1>Moderator upravljački dio</h1>';
                    echo '<h2>Unos proizvoda</h2>';
                    echo '
                    <form action="insert.php" method="post">
                    <label for="kategorija">Kategorija proizvoda:</label>
                    <select name="kategorija">';
                    $query = $conn->query("SELECT * FROM kategorija");
                    if ($query->num_rows > 0) {
                        while($row = mysqli_fetch_array($query)){
                            echo '<option value="'. $row['kategorija_id'] .'">'. $row['naziv'] .'</option>';
                        }
                    }
                    $query->close();
                    echo '</select><br/>
                    <label for="naziv">Naziv proizvoda:</label>
                    <input type="text" name="naziv" required><br/>
                    <label for="opis">Opis:</label>
                    <textarea name="opis" rows="4" cols="50" required></textarea><br/>
                    <label for="cijena">Cijena:</label>
                    <input type="number" name="cijena" min="1" max="99999" step="0.01" required><br/>
                    <label for="slika">Poveznica na sliku:</label>
                    <input type="text" name="slika" required><br/>
                    <label for="video">Poveznica na video:</label>
                    <input type="text" name="video" required><br/>
                    <input type="submit" value="Dodaj">
                    </form></div>';
                    echo '<h2>Narudžbe</h2>';
                    $query = $conn->query("SELECT * FROM kategorija WHERE moderator_id = ".$_SESSION['user_id']);
                    if ($query->num_rows > 0) {
                        while($row = mysqli_fetch_array($query)){
                            $kategorijaid = $row['kategorija_id'];
                            echo '<h3>' . $row['naziv'] . '</h3>';
                            $query2 = $conn->query("SELECT narudzba.narudzba_id AS narudzba_id, korisnik.korisnicko_ime AS korisnik, proizvod.naziv AS naziv, narudzba.blokirana AS blokirana, narudzba.prihvacena AS prihvacena, narudzba.datum_kreiranja AS vrijeme, proizvod.cijena AS cijena
                            FROM narudzba JOIN proizvod ON proizvod.proizvod_id = narudzba.proizvod_id JOIN korisnik ON korisnik.korisnik_id = narudzba.korisnik_id WHERE proizvod.kategorija_id = ". $kategorijaid);
                            if ($query2->num_rows > 0) {
                                while($row2 = mysqli_fetch_array($query2)){
                                    echo $row2['narudzba_id'] . ' | ';
                                    echo $row2['korisnik'] . ' | ';
                                    echo $row2['naziv']. ' | ';
                                    echo $row2['blokirana']. ' | ';
                                    echo $row2['prihvacena']. ' | ';
                                    $order_time = date("d.m.Y H:i:s", strtotime($row2['vrijeme']));
                                    echo $order_time . ' | ';
                                    echo $row2['cijena']. ' HRK | ';
                                    if($row2['blokirana'] == false && $row2['prihvacena'] == false){
                                        echo '<form action="order.php" method="post">
                                        <input type="hidden" value="'. $row2['narudzba_id'] .'" name="order-accept"><input type="submit" value="Prihvati">
                                        </form>
                                        <form action="order.php" method="post">
                                        <input type="hidden" value="'. $row2['narudzba_id'] .'" name="order-deny"><input type="submit" value="Odbij">
                                        </form>';
                                    }
                                    elseif($row2['blokirana'] == false && $row2['prihvacena'] == true){
                                        echo 'Narudžba je već prihvaćena!';
                                    }
                                    elseif($row2['blokirana'] == true && $row2['prihvacena'] == false){
                                        echo 'Narudžba je trenutno blokirana!';
                                    }
                                    else{
                                        echo 'Greška! Narudžba je prihvaćena i blokirana!';
                                    }
                                    echo '<br/>';
                                }
                            }
                            else{
                                echo 'Nema narudžbi u kategoriji!';
                            }
                            $query2->close();
                        }
                    }
                    else{
                        echo 'Nema narudžbi u vašim kategorijama!';
                    }
                    $query->close();
                    echo '<hr/>';
                    
                    $query = $conn->query("SELECT * FROM kategorija WHERE moderator_id = ".$_SESSION['user_id']);
                    if ($query->num_rows > 0) {
                        echo '<h1> Po kategorijama </h1>';
                        while($row = mysqli_fetch_array($query)){
                            $kategorijaid = $row['kategorija_id'];
                            echo '<h2>' . $row['naziv'] . '</h2>';
                            $query2 = $conn->query("SELECT * FROM proizvod WHERE kategorija_id = $kategorijaid");
                            if ($query2->num_rows > 0) {
                                while($row2 = mysqli_fetch_array($query2)){
                                    echo '<div style="float:left;"><img src="'.$row2["slika"].'" alt="Slika" width="300" height="300">';
                                    echo '<h3>' . $row2['naziv'] . '</h3>';
                                    echo '<p>' . $row2['opis'] . '</p>';
                                    echo '<p style="color: red;">' . $row2['cijena'] . ' KN</p>';
                                    echo '
                                    <form action="proizvod.php" method="get">
                                    <input type="hidden" name="proizvod_id" value="' . $row2['proizvod_id'] . '">
                                    <input type="submit" value="Pregledaj">
                                    </form></div>';
                                }
                            }
                            else{
                                echo 'Nema raspoloživih proizvoda.';
                            }
                            echo '<div class="clearfix"></div>';
                        }
                    }
                    $query->close();

                    if($_SESSION['role']<='1'){
                        echo '<h2>Uredi kategoriju</h2>
                        <form action="modify.php" method="get">
                        <label for="kategorija_id">Uredi kategoriju:</label>
                        <select name="kategorija_id" required>';
                        $query = $conn->query("SELECT * FROM kategorija");
                        if ($query->num_rows > 0) {
                            while($row = mysqli_fetch_array($query)){
                                echo '<option value="'. $row['kategorija_id'] .'">'. $row['naziv'] .'</option>';
                            }
                        }
                        $query->close();
                        echo '</select><br/>
                        <input type="submit" value="Uredi">
                        </form></div>';
                        echo '<h2>Unos kategorije</h2>';
                        echo '<form action="insert.php" method="post">';
                        echo '<label for="moderator_id">Moderator:</label>
                        <select name="moderator_id">';
                        $query = $conn->query("SELECT korisnik.* FROM korisnik JOIN tip_korisnika ON korisnik.tip_id = tip_korisnika.tip_id WHERE tip_korisnika.tip_id = 2");
                        if ($query->num_rows > 0) {
                            while($row = mysqli_fetch_array($query)){
                                    echo '<option value="'. $row['korisnik_id'] .'">'. $row['korisnicko_ime'] .'</option>';
                            }
                        }
                        $query->close();
                        echo '</select><br/>
                        <label for="naziv">Naziv kategorije:</label>
                        <input type="text" name="naziv" value="'. $row['naziv'] .'" required><br/>
                        <label for="opis">Opis:</label>
                        <textarea name="opis" rows="4" cols="50" required>'.$row['opis'].'</textarea><br/>
                        <label for="limit">Limit:</label>
                        <input type="number" name="limit" min="1" max="99999" value="'. $row['limit'] .'" required><br/>
                        <input type="submit" value="Dodaj">
                        </form></div>';
                        echo '<h2>Filtriraj</h2>';
                        echo '<form action="" method="post">
                        <label for="filter-kategorija">Kategorija:</label>
                        <select name="filter-kategorija">
                            <option></option>';
                            $query = $conn->query("SELECT * FROM kategorija");
                            if ($query->num_rows > 0) {
                                while($row = mysqli_fetch_array($query)){
                                    echo '<option value="'. $row['kategorija_id'] .'">'. $row['naziv'] .'</option>';
                                }
                            }
                            $query->close();
                        echo '</select><br/>
                        <label for="from-date">Od:</label>
                        <input type="date" name="from-date">
                        <label for="to-date">Do:</label>
                        <input type="date" name="to-date">
                        <input type="submit" value="Filtriraj">
                        </form>';
                        if(!empty($_POST['filter-kategorija'])){
                            echo '<h2>Ukupno narudžbi</h2>';
                            $count = 0;
                            $countp = 0;
                            $countn = 0;
                            $query = $conn->query("SELECT narudzba.* FROM iwa_2019_kz_projekt.narudzba JOIN proizvod ON narudzba.proizvod_id = proizvod.proizvod_id JOIN kategorija ON proizvod.kategorija_id = kategorija.kategorija_id WHERE kategorija.kategorija_id = " . $_POST['filter-kategorija']);
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
                                echo 'Nema narudžbi za ovu kategoriju.';
                            }
                            $query->close();
                            echo '<h2>Blokirane narudžbe</h2>';
                            $query = $conn->query("SELECT * FROM kategorija WHERE kategorija_id = " . $_POST['filter-kategorija']);
                            if ($query->num_rows > 0) {
                                while($row = mysqli_fetch_array($query)){
                                    $kategorijaid = $row['kategorija_id'];
                                    echo '<h3>' . $row['naziv'] . '</h3>';
                                    $query2 = $conn->query("SELECT narudzba.narudzba_id AS narudzba_id, korisnik.korisnicko_ime AS korisnik, proizvod.naziv AS naziv, narudzba.blokirana AS blokirana, narudzba.prihvacena AS prihvacena, narudzba.datum_kreiranja AS vrijeme, proizvod.cijena AS cijena
                                    FROM narudzba JOIN proizvod ON proizvod.proizvod_id = narudzba.proizvod_id JOIN korisnik ON korisnik.korisnik_id = narudzba.korisnik_id WHERE blokirana = 1 AND proizvod.kategorija_id = ". $kategorijaid);
                                    if ($query2->num_rows > 0) {
                                        while($row2 = mysqli_fetch_array($query2)){
                                                echo $row2['narudzba_id'] . ' | ';
                                                echo $row2['korisnik'] . ' | ';
                                                echo $row2['naziv']. ' | ';
                                                echo $row2['blokirana']. ' | ';
                                                echo $row2['prihvacena']. ' | ';
                                                $order_time = date("d.m.Y H:i:s", strtotime($row2['vrijeme']));
                                                echo $order_time . ' | ';
                                                echo $row2['cijena']. ' HRK | ';
                                                echo '<form action="order.php" method="post">
                                                <input type="hidden" value="'. $row2['narudzba_id'] .'" name="order-unblock"><input type="submit" value="Odobri">
                                                </form>';
                                        }
                                    }
                                    else{
                                        echo 'Nema blokiranih narudžbi u kategoriji!';
                                    }
                                    $query2->close();
                                }
                            }
                            $query->close();
                        }
                        elseif(!empty($_POST['from-date'])){
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
                                    $query = $conn->query("SELECT narudzba.* FROM iwa_2019_kz_projekt.narudzba JOIN proizvod ON narudzba.proizvod_id = proizvod.proizvod_id JOIN kategorija ON proizvod.kategorija_id = kategorija.kategorija_id WHERE datum_kreiranja > '" . $from_time ."' AND datum_kreiranja < '". $to_time ."'");
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
                                    FROM narudzba JOIN proizvod ON proizvod.proizvod_id = narudzba.proizvod_id JOIN korisnik ON korisnik.korisnik_id = narudzba.korisnik_id WHERE blokirana = 1 AND  datum_kreiranja > '" . $from_time ."' AND datum_kreiranja < '". $to_time ."'");
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
                        else{
                            $count = 0;
                            $countp = 0;
                            $countn = 0;
                            $query = $conn->query("SELECT * FROM narudzba");
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
                                echo 'Nema narudžbi za ovu kategoriju.';
                            }
                            $query->close();
                            $query = $conn->query("SELECT * FROM kategorija");
                            echo '<h2>Blokirane narudžbe</h2>';
                            if ($query->num_rows > 0) {
                                while($row = mysqli_fetch_array($query)){
                                    $kategorijaid = $row['kategorija_id'];
                                    echo '<h3>' . $row['naziv'] . '</h3>';
                                    $query2 = $conn->query("SELECT narudzba.narudzba_id AS narudzba_id, korisnik.korisnicko_ime AS korisnik, proizvod.naziv AS naziv, narudzba.blokirana AS blokirana, narudzba.prihvacena AS prihvacena, narudzba.datum_kreiranja AS vrijeme, proizvod.cijena AS cijena
                                    FROM narudzba JOIN proizvod ON proizvod.proizvod_id = narudzba.proizvod_id JOIN korisnik ON korisnik.korisnik_id = narudzba.korisnik_id WHERE blokirana = 1 AND proizvod.kategorija_id = ". $kategorijaid);
                                    if ($query2->num_rows > 0) {
                                        while($row2 = mysqli_fetch_array($query2)){
                                                echo $row2['narudzba_id'] . ' | ';
                                                echo $row2['korisnik'] . ' | ';
                                                echo $row2['naziv']. ' | ';
                                                echo $row2['blokirana']. ' | ';
                                                echo $row2['prihvacena']. ' | ';
                                                $order_time = date("d.m.Y H:i:s", strtotime($row2['vrijeme']));
                                                echo $order_time . ' | ';
                                                echo $row2['cijena']. ' HRK | ';
                                                echo '<form action="order.php" method="post">
                                                <input type="hidden" value="'. $row2['narudzba_id'] .'" name="order-unblock"><input type="submit" value="Odobri">
                                                </form>';
                                        }
                                    }
                                    else{
                                        echo 'Nema blokiranih narudžbi u kategoriji!';
                                    }
                                    $query2->close();
                                }
                            }
                            $query->close();
                        }
                        echo '<h2>Uredi korisnika</h2>
                        <form action="modify.php" method="get">
                        <label for="korisnik_id">Uredi korisnika:</label>
                        <select name="korisnik_id" required>';
                        $query = $conn->query("SELECT * FROM korisnik");
                        if ($query->num_rows > 0) {
                            while($row = mysqli_fetch_array($query)){
                                echo '<option value="'. $row['korisnik_id'] .'">'. $row['korisnicko_ime'] .'</option>';
                            }
                        }
                        $query->close();
                        echo '</select><br/>
                        <input type="submit" value="Uredi">
                        </form></div>';
                        echo '<h2>Dodaj korisnika</h2>';
                        echo '
                        <form action="insert.php" method="post">
                        <label for="tip_id">Tip korisnika:</label>
                        <select name="tip_id" required>';
                        $query = $conn->query("SELECT * FROM tip_korisnika");
                        if ($query->num_rows > 0) {
                            while($row = mysqli_fetch_array($query)){
                                echo '<option value="'. $row['tip_id'] .'">'. $row['naziv'] .'</option>';
                            }
                        }
                        $query->close();
                        echo '</select><br/>
                        <label for="korisnicko_ime">Korisničko ime:</label>
                        <input type="text" name="korisnicko_ime" required><br/>
                        <label for="lozinka">Lozinka:</label>
                        <input type="password" name="lozinka" required><br/>
                        <label for="ime">Ime:</label>
                        <input type="text" name="ime" required><br/>
                        <label for="prezime">Prezime:</label>
                        <input type="text" name="prezime" required><br/>
                        <label for="email">E-mail:</label>
                        <input type="text" name="email" required><br/>
                        <label for="slika">Slika:</label>
                        <input type="text" name="slika" required><br/>
                        <input type="submit" value="Dodaj">
                        </form></div>';
                    }
                }
            }
        }
        $conn->close();
        ?>
</main>
</body>
</html>