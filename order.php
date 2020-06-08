<?php
session_start();
$conn = new mysqli('localhost', 'iwa_2019', 'foi2019', 'iwa_2019_kz_projekt');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if(isset($_POST['order'])){
    $product_id = $_POST['order'];
    $userid = $_POST['userid'];
    $order_price = $_POST['order_price'];
    $query = $conn->query("SELECT * FROM narudzba WHERE proizvod_id = $product_id AND korisnik_id = '".$userid."'");
//    if ($query->num_rows > 0) {
//        $row = mysqli_fetch_array($query);
//        $kolicina = $row['kolicina'];
//        $query2 = $conn->query("UPDATE narudzba SET kolicina = $kolicina +1 WHERE proizvod_id = " . $product_id);
//        $_SESSION['status-narudzbe'] = 'Uspješno naručeno!';
//    }
//    else{
        $provjera = $conn->query("SELECT * FROM proizvod JOIN kategorija ON kategorija.kategorija_id = proizvod.kategorija_id WHERE proizvod_id = '". $product_id . "'");
        $row = mysqli_fetch_array($provjera);
        $limit = $row['limit'];
        $cijena = $row['cijena'];
        if($limit > $cijena){
            $query2 = $conn->query("INSERT INTO narudzba (korisnik_id, proizvod_id, kolicina, blokirana, prihvacena, datum_kreiranja) VALUES ('". $userid ."', '".$product_id."', '1', '0', '0', NOW())");
        }
        else{
            $query2 = $conn->query("INSERT INTO narudzba (korisnik_id, proizvod_id, kolicina, blokirana, prihvacena, datum_kreiranja) VALUES ('". $userid ."', '".$product_id."', '1', '1', '0', NOW())");
        }
        if($query2){
            $_SESSION['status-narudzbe'] = 'Uspješno naručeno!';
        }
        else
        {
            $_SESSION['status-narudzbe'] = "Neuspješna narudžba!";
        }
//    }
        $query->close();
        header("Location: kosarica.php");
}
if(isset($_POST['order-accept'])){
    $query2 = $conn->query("UPDATE narudzba SET prihvacena = 1 WHERE narudzba_id = " . $_POST['order-accept']);
    header("Location: index.php");
}
if(isset($_POST['order-deny'])){
    $query2 = $conn->query("DELETE FROM narudzba WHERE narudzba_id = " . $_POST['order-deny']);
    header("Location: index.php");
}
if(isset($_POST['order-unblock'])){
    $query2 = $conn->query("UPDATE narudzba SET blokirana = 0 WHERE narudzba_id = " . $_POST['order-unblock']);
    header("Location: index.php");
}
    $conn->close();
?>