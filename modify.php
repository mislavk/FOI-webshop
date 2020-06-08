<?php
    session_start();
    if(!isset($_GET["korisnik_id"])){
        if(!isset($_GET["kategorija_id"])){
            header('Location: index.php');
        }
    }
    if(!($_SESSION['role'] == 1))
        header('Location: index.php');
    $conn = new mysqli('localhost', 'iwa_2019', 'foi2019', 'iwa_2019_kz_projekt');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if(isset($_POST['korisnicko_ime'])){
        $query = $conn->query("UPDATE korisnik SET tip_id = ".$_POST['tip_id'].", korisnicko_ime = '".$_POST['korisnicko_ime']."', lozinka = '".$_POST['lozinka']."', ime = '".$_POST['ime']."', prezime = '".$_POST['prezime']."', email = '".$_POST['email']."', slika = '".$_POST['slika']."' WHERE korisnik_id = ".$_GET['korisnik_id']);
    }
    if(isset($_POST['limit'])){
        $query = $conn->query("UPDATE kategorija SET moderator_id = ".$_POST['moderator_id'].", naziv = '".$_POST['naziv']."', limit = '".$_POST['limit']."' WHERE kategorija_id = ".$_GET['kategorija_id']);
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
            </div>';
    }
    ?>
</nav>
<main>
<?php
if(isset($_GET['korisnik_id'])){
    echo '<h2>Uredi korisnika</h2>';
    echo '<form action="" method="post">';
    $query = $conn->query("SELECT * FROM korisnik WHERE korisnik_id = " . $_GET["korisnik_id"]);
    if ($query->num_rows > 0) {
        while($row = mysqli_fetch_array($query)){
            echo '<label for="tip_id">Tip korisnika:</label>
            <select name="tip_id">';
            $query2 = $conn->query("SELECT * FROM tip_korisnika");
            if ($query2->num_rows > 0) {
                while($rows = mysqli_fetch_array($query2)){
                    if($rows['tip_id'] == $row['tip_id'])
                        echo '<option value="'. $rows['tip_id'] .'" selected>'. $rows['naziv'] .'</option>';
                    else{
                        echo '<option value="'. $rows['tip_id'] .'">'. $rows['naziv'] .'</option>';
                    }
                }
            }
            $query2->close();
            echo '</select><br/>
            <label for="korisnicko_ime">Korisničko ime:</label>
            <input type="text" name="korisnicko_ime" value="'. $row['korisnicko_ime'] .'"><br/>
            <label for="lozinka">Lozinka:</label>
            <input type="password" name="lozinka" value="'.$row['lozinka'].'"><br/>
            <label for="ime">Ime:</label>
            <input type="text" name="ime" value="'. $row['ime'] .'"><br/>
            <label for="prezime">Prezime:</label>
            <input type="text" name="prezime" value="'. $row['prezime'] .'"><br/>
            <label for="email">E-mail:</label>
            <input type="text" name="email" value="'. $row['email'] .'"><br/>
            <label for="slika">Slika:</label>
            <input type="text" name="slika" value="'. $row['slika'] .'"><br/>
            <input type="submit" value="Dodaj">
            </form></div>';
        }
    }
}
if(isset($_GET['kategorija_id'])){
    echo '<h2>Uredi kategoriju</h2>';
    echo '<form action="" method="post">';
    $query = $conn->query("SELECT * FROM kategorija WHERE kategorija_id = " . $_GET["kategorija_id"]);
    if ($query->num_rows > 0) {
        while($row = mysqli_fetch_array($query)){
            echo '<label for="moderator_id">Moderator:</label>
            <select name="moderator_id">';
            $query2 = $conn->query("SELECT korisnik.* FROM korisnik JOIN tip_korisnika ON korisnik.tip_id = tip_korisnika.tip_id WHERE tip_korisnika.tip_id = 2");
            if ($query2->num_rows > 0) {
                while($rows = mysqli_fetch_array($query2)){
                    if($rows['korisnik_id'] == $row['moderator_id'])
                        echo '<option value="'. $rows['korisnik_id'] .'" selected>'. $rows['korisnicko_ime'] .'</option>';
                    else{
                        echo '<option value="'. $rows['korisnik_id'] .'">'. $rows['korisnicko_ime'] .'</option>';
                    }
                }
            }
            $query2->close();
            echo '</select><br/>
            <label for="naziv">Naziv kategorije:</label>
            <input type="text" name="naziv" value="'. $row['naziv'] .'" required><br/>
            <label for="opis">Opis:</label>
            <textarea name="opis" rows="4" cols="50" required>'.$row['opis'].'</textarea><br/>
            <label for="limit">Limit:</label>
            <input type="number" name="limit" min="1" max="99999" value="'. $row['limit'] .'" required><br/>
            <input type="submit" value="Dodaj">
            </form></div>';
        }
    }
}
$conn->close();
?>
</main>
</body>
</html>