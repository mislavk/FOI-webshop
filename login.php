<?php
session_start();
$conn = new mysqli('localhost', 'iwa_2019', 'foi2019', 'iwa_2019_kz_projekt');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$admin_user = 'admin';
$admin_pass = 'foi';


$username = $_POST['username'];
$password = $_POST['pwd'];

if($username == $admin_user && $password == $admin_pass){
    $_SESSION['username'] = $username;
    $_SESSION['role'] = "1";
    $_SESSION['user_id'] = "0";
    header("Location: index.php");
}
else{
    $query = $conn->query("SELECT * FROM korisnik WHERE korisnicko_ime = '$username' AND lozinka = '$password'");
    if ($query->num_rows > 0) {
        $row = mysqli_fetch_array($query);
        $_SESSION['role'] = $row['tip_id'];
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $row['korisnik_id'];
        header("Location: index.php");
        $query->close();
        $conn->close();
    }
    else{
        header("Location: index.php");
        $query->close();
        $conn->close();
    }
}
?>