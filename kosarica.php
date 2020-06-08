<?php
    session_start();
    if($_SESSION['user_id'] == "0"){
        header('Location: index.php');
    }
    $cart_array=array();
    if(isset($_SESSION['cart']))
    $cart_array = $_SESSION['cart'];
    if(isset($_POST['cart-remove'])){
        unset($cart_array[$_POST['cart-remove']]);
        $_SESSION['cart']=$cart_array;
    }
    if(isset($_POST['cart-add'])){
        if (!in_array($_POST['cart-add'], $cart_array))
        array_push($cart_array, $_POST['cart-add']);
        $_SESSION['cart']=$cart_array;
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
            echo '<div><a href="kosarica.php" id="active">Kosarica</a></div>';
        } else{
            header('Location: index.php');
        }
    ?>
</nav>
<main>
    <?php
    $conn = new mysqli('localhost', 'iwa_2019', 'foi2019', 'iwa_2019_kz_projekt');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    echo '<h1>Kosarica</h1>';
    if(isset($_SESSION['status-narudzbe'])){
        echo $_SESSION['status-narudzbe'] . '<br/>';
        unset($_SESSION['status-narudzbe']);
    }
    $query = $conn->query("SELECT * FROM korisnik WHERE korisnicko_ime = '".$_SESSION['username']."'");
    $row = mysqli_fetch_array($query);
    $userid = $row['korisnik_id'];
    $query->close();
            if(isset($_SESSION['cart'])){
                foreach($_SESSION['cart'] as $key=>$value){
                $query = $conn->query("SELECT * FROM proizvod WHERE proizvod_id = $value");
                while($row = mysqli_fetch_array($query)){
                        echo '<img src='.$row["slika"].' alt="Slika" width="50" height="50">';
                        echo '<div>' . $row['naziv'] . '</div>';
                        echo '<div>' . $row['cijena'] . ' KN</div>';
                        echo '<form action="order.php" method="post"><input type="hidden" value="'. $row["proizvod_id"] .'" name="order"><input type="hidden" value="'. $row['cijena'] .'" name="order_price"><input type="hidden" value="'. $userid .'" name="userid"><input type="submit" value="Naruči"></form>';
                        echo '<form action="" method="post"><input type="hidden" value="'. $key .'" name="cart-remove"><input type="submit" value="Obriši"></form>';
                }
                $query->close();
                }
            }
            if(count($cart_array) == 0){ echo 'Košarica je prazna!'; } 
            echo '<hr/><br/><h1> Vaše narudžbe </h1>';
            $query = $conn->query("SELECT * FROM proizvod INNER JOIN narudzba ON narudzba.proizvod_id = proizvod.proizvod_id WHERE korisnik_id = '". $userid ."'");
            if ($query->num_rows > 0) {
                while($row = mysqli_fetch_array($query)){
                    echo '<img src="'.$row["slika"].'" alt="Slika" width="50" height="50">';
                    echo '<h3>' . $row['naziv'] . '</h3>';
                    echo '<div>Količina:<b>' . $row['kolicina'] . '</b></div>';
                    echo '<div>Blokirana:';
                    if($row['blokirana']==1) echo '<b>Da</b>';
                    else echo '<b>Ne</b>';
                    echo '</div><div>Prihvaćena:';
                    if($row['prihvacena']==1) echo '<b>Da</b>';
                    else echo '<b>Ne</b>';
                    $order_time = date("d.m.Y H:i:s", strtotime($row['datum_kreiranja']));
                    echo '</div><div>Naručeno: <b>' . $order_time . '</b></div><br/>';
                }
            }
            else{
                echo 'Nema narudžbi!';
            }
            $query->close();
            $conn->close();
    ?>
</main>
</body>
</html>