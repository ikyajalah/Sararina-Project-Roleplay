<?php
session_start();

// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "anjay12";
$db = "lsrp";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$pesan = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password_input = $_POST['password'];

    $sql = "SELECT * FROM ucp WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $hash = $row['password'];

        if ($row['banned'] == 1) {
            $pesan = "Akun ini telah dibanned oleh <b>{$row['bannedby']}</b><br>Alasan: {$row['banreason']}";
        } else {
            if (password_verify($password_input, $hash)) {
                $_SESSION['ucp_login'] = $username;
                $_SESSION['ucp_id'] = $row['id']; // Simpan ID juga jika dibutuhkan
                header("Location: dashboard.php");
                exit;
            } else {
                $pesan = "Password salah!";
            }
        }
    } else {
        $pesan = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>LOGIN UCP</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Login Akun</h2>

        <?php if (!empty($pesan)): ?>
            <p style="color: red; text-align: center;"><?= $pesan ?></p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Username" autocomplete="username" required>
            <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
            <input type="submit" name="login" value="Login">
            <p>Belum punya akun? <a href="register.php">Daftar</a></p>
            <a href="index.php">Home</a>
        </form>
    </div>
</body>
</html>
