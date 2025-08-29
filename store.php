<?php
session_start();
if (!isset($_SESSION['ucp_login'])) {
    header("Location: login.php");
    exit;
}

$conn = mysqli_connect("localhost", "root", "anjay12", "lsrp");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$user = $_SESSION['ucp_login'];
$pesan = "";

// Ambil semua karakter milik user untuk dipilih saat beli
$char_list = mysqli_query($conn, "SELECT pID, PlayerName FROM characters WHERE Username = '$user'");

// Ambil saldo dari karakter pertama
$credit_q = mysqli_query($conn, "SELECT PlayerCredits FROM characters WHERE Username = '$user' LIMIT 1");
$credits = ($row = mysqli_fetch_assoc($credit_q)) ? $row['PlayerCredits'] : 0;

$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$where_clause = $kategori_filter ? "WHERE kategori = '" . mysqli_real_escape_string($conn, $kategori_filter) . "'" : "";
$items = mysqli_query($conn, "SELECT * FROM store $where_clause");
$kategori_result = mysqli_query($conn, "SELECT DISTINCT kategori FROM store");

if (isset($_POST['beli'])) {
    $item_id = (int) $_POST['item_id'];
    $char_id = (int) $_POST['char_id'];
    $query = mysqli_query($conn, "SELECT * FROM store WHERE id = $item_id LIMIT 1");
    if (mysqli_num_rows($query) == 1) {
        $item = mysqli_fetch_assoc($query);
        $char_check = mysqli_query($conn, "SELECT PlayerCredits FROM characters WHERE pID = $char_id AND Username = '$user' LIMIT 1");
        if ($char = mysqli_fetch_assoc($char_check)) {
            if ($char['PlayerCredits'] >= $item['harga']) {
                mysqli_query($conn, "UPDATE characters SET PlayerCredits = PlayerCredits - {$item['harga']} WHERE pID = $char_id");
                mysqli_query($conn, "INSERT INTO pembelian (username, character_id, item_id, tanggal) VALUES ('$user', $char_id, {$item['id']}, NOW())");
                $pesan = "<span class='text-green-600'>Pembelian berhasil untuk karakter ID $char_id!</span>";
            } else {
                $pesan = "<span class='text-red-600'>Saldo tidak cukup pada karakter ini!</span>";
            }
        } else {
            $pesan = "<span class='text-red-600'>Karakter tidak ditemukan!</span>";
        }
    }
}

$pembelian = mysqli_query($conn, "SELECT s.nama, p.tanggal, c.PlayerName FROM pembelian p JOIN store s ON s.id = p.item_id JOIN characters c ON c.pID = p.character_id WHERE p.username = '$user' ORDER BY p.tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>STARLIGHT</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
  <style>body { font-family: 'Montserrat', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen">
  <div class="flex">
    <aside class="w-64 bg-white shadow h-screen p-4">
      <div class="flex justify-center">
      </div>
      <h1 class="text-2xl font-bold text-blue-600 mb-8">STARLIGHT</h1>
      <ul class="space-y-4">
        <li>
          <a href="dashboard.php" class="flex items-center space-x-3 bg-gray-100 hover:bg-blue-100 text-gray-700 px-4 py-2 rounded">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 14v-5a2 2 0 012-2h4a2 2 0 012 2v5" />
            </svg>
            <span>Dashboard</span>
          </a>
        </li>
        <li>
          <a href="characters.php" class="flex items-center space-x-3 bg-gray-100 hover:bg-blue-100 text-gray-700 px-4 py-2 rounded">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9.001 9.001 0 0112 15c2.136 0 4.092.745 5.621 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Character</span>
          </a>
        </li>
        <li>
          <a href="#" class="flex items-center space-x-3 bg-gray-100 hover:bg-blue-100 text-gray-700 px-4 py-2 rounded">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18M9 3v18m6-18v18" />
            </svg>
            <span>Store</span>
          </a>
        </li>
        <li>
          <a href="logout.php" class="flex items-center space-x-3 bg-gray-100 hover:bg-red-100 text-red-600 px-4 py-2 rounded">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 11-4 0v-1m4-4V9a2 2 0 00-4 0v1" />
            </svg>
            <span>Logout</span>
          </a>
        </li>
      </ul>
    </aside>

    <main class="flex-1 p-6">
      <h2 class="text-2xl font-bold mb-2">Store</h2>
      <p class="mb-4 text-gray-600">Saldo dari karakter pertama kamu: <span class="font-bold text-blue-600"><?php echo $credits; ?></span> kredit</p>
      <?php if (!empty($pesan)) echo "<div class='mb-4'>$pesan</div>"; ?>

      <!-- Filter kategori -->
      <div class="mb-6">
        <form method="GET" action="store.php" class="flex flex-wrap items-center gap-2">
          <label for="kategori" class="text-sm font-semibold text-gray-600">Filter Kategori:</label>
          <select name="kategori" id="kategori" class="p-2 border rounded">
            <option value="">Semua</option>
            <?php while ($kat = mysqli_fetch_assoc($kategori_result)): ?>
              <option value="<?php echo $kat['kategori']; ?>" <?php echo ($kategori_filter === $kat['kategori']) ? 'selected' : ''; ?>><?php echo $kat['kategori']; ?></option>
            <?php endwhile; ?>
          </select>
          <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Terapkan</button>
        </form>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
        <?php mysqli_data_seek($items, 0); while ($item = mysqli_fetch_assoc($items)): ?>
        <div class="bg-white rounded shadow p-4 text-center">
          <h3 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($item['nama']); ?></h3>
          <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($item['deskripsi']); ?></p>
          <p class="font-semibold text-blue-500 mb-1">Harga: <?php echo $item['harga']; ?> kredit</p>
          <p class="text-xs text-gray-400 mb-2">Kategori: <?php echo htmlspecialchars($item['kategori']); ?></p>
          <form method="POST" action="store.php">
            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
            <select name="char_id" class="mb-2 w-full p-2 border rounded">
              <?php mysqli_data_seek($char_list, 0); while ($c = mysqli_fetch_assoc($char_list)): ?>
                <option value="<?php echo $c['pID']; ?>"><?php echo htmlspecialchars($c['PlayerName']); ?></option>
              <?php endwhile; ?>
            </select>
            <button name="beli" type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded w-full">Beli</button>
          </form>
        </div>
        <?php endwhile; ?>
      </div>

      <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-bold mb-4">Riwayat Pembelian</h3>
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="py-2">Tanggal</th>
              <th class="py-2">Item</th>
              <th class="py-2">Karakter</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($p = mysqli_fetch_assoc($pembelian)): ?>
              <tr class="border-t">
                <td class="py-2 text-gray-600"><?php echo $p['tanggal']; ?></td>
                <td class="py-2 font-medium"><?php echo $p['nama']; ?></td>
                <td class="py-2"><?php echo $p['PlayerName']; ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>
