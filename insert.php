<?php
session_start();
$conn = new mysqli('localhost', 'iwa_2019', 'foi2019', 'iwa_2019_kz_projekt');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if(isset($_POST['kategorija'])){
    $kategorija = $_POST['kategorija'];
    $naziv = $_POST['naziv'];
    $opis = $opis = $_POST['opis'];
    $cijena = $_POST['cijena'];
    $slika = $_POST['slika'];
    $video = $_POST['video'];
    $query = $conn->query("INSERT INTO `iwa_2019_kz_projekt`.`proizvod` (`kategorija_id`, `naziv`, `opis`, `cijena`, `slika`, `video`) VALUES ('$kategorija', '$naziv', '$opis', '$cijena', '$slika', '$video')");
}
if(isset($_POST['tip_id'])){
    $tip_id = $_POST['tip_id'];
    $korisnicko_ime = $_POST['korisnicko_ime'];
    $lozinka = $_POST['lozinka'];
    $ime = $_POST['ime'];
    $prezime = $_POST['prezime'];
    $email = $_POST['email'];
    $slika = $_POST['slika'];
    $query = $conn->query("INSERT INTO `iwa_2019_kz_projekt`.`korisnik` (`tip_id`, `korisnicko_ime`, `lozinka`, `ime`, `prezime`, `email`,`slika`) VALUES ('$tip_id', '$korisnicko_ime', '$lozinka', '$ime', '$prezime', '$email', '$slika')");
}
if(isset($_POST['moderator_id'])){
    $moderator_id = $_POST['moderator_id'];
    $naziv = $_POST['naziv'];
    $opis = $_POST['opis'];
    $limit = $_POST['limit'];
    $query = $conn->query("INSERT INTO `iwa_2019_kz_projekt`.`kategorija` (`moderator_id`, `naziv`, `opis`, `limit`) VALUES ('$moderator_id', '$naziv', '$opis', '$limit')");
}
$conn->close();
header('Location: index.php');
?>