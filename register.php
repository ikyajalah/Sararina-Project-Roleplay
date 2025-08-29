<?php
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

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah username sudah dipakai
    $check = mysqli_query($conn, "SELECT * FROM ucp WHERE username = '$username'");
    if (mysqli_num_rows($check) > 0) {
        $pesan = "Username sudah digunakan!";
    } else {
        // Tambahkan status = 1 saat insert
        $sql = "INSERT INTO ucp (username, password, status) VALUES ('$username', '$password', 0)";
        if (mysqli_query($conn, $sql)) {
            $pesan = "<span style='color:green;'>Registrasi berhasil! Silakan login.</span>";
        } else {
            $pesan = "Gagal registrasi: " . mysqli_error($conn);
        }
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>REGISTER UCP</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="register-container">
        <h2>Registrasi Akun</h2>

        <?php if (!empty($pesan)): ?>
            <p style="color: red; text-align: center;"><?= $pesan ?></p>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <input type="text" name="username" placeholder="Username" autocomplete="username" required>
            <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
            <input type="submit" name="register" value="Daftar">
            <p>Sudah punya akun? <a href="login.php">Login</a></p>
            <a href="index.php">Home</a>
        </form>
    </div>
</body>
</html>
