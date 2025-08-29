<?php
session_start();
if (!isset($_SESSION['ucp_login'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['ucp_login'];
$conn = mysqli_connect("localhost", "root", "anjay12", "lsrp");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$query = "SELECT * FROM characters WHERE Username = '$username' ORDER BY pID ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STARLIGHT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
  <div class="flex">
    <!-- Sidebar -->
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
          <a href="store.php" class="flex items-center space-x-3 bg-gray-100 hover:bg-blue-100 text-gray-700 px-4 py-2 rounded">
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

    <!-- Main Content -->
    <main class="flex-1 p-6">
      <h2 class="text-2xl font-bold mb-6 text-center">Daftar Karakter untuk: <?php echo htmlspecialchars($username); ?></h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <?php
        $slot = 1;
        while ($char = mysqli_fetch_assoc($result)):
        ?>
            <div class="bg-white rounded-lg shadow p-4 relative">
                <span class="absolute top-2 right-2 text-xs bg-gray-200 text-gray-800 px-2 py-0.5 rounded">Slot <?php echo $slot++; ?></span>
                <div class="flex items-center space-x-4 mb-4">
                    <img src="https://assets.open.mp/assets/images/skins/<?php echo $char['PlayerSkin']; ?>.png" alt="Skin" class="w-16 h-16 rounded">
                    <div>
                        <h3 class="text-lg font-bold text-blue-600"><?php echo htmlspecialchars($char['PlayerName']); ?></h3>
                        <p class="text-sm text-gray-500">Skin ID: <?php echo $char['PlayerSkin']; ?></p>
                    </div>
                </div>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li><strong>Level:</strong> <?php echo $char['PlayerLevel']; ?></li>
                    <li><strong>Uang:</strong> $<?php echo number_format($char['PlayerMoney']); ?></li>
                    <li><strong>Umur:</strong> <?php echo $char['PlayerAge']; ?> tahun</li>
                    <li><strong>Gender:</strong> <?php echo ($char['PlayerGender'] == 1) ? 'Laki-laki' : 'Perempuan'; ?></li>
                    <li><strong>HP:</strong> <?php echo $char['PlayerHealth']; ?></li>
                </ul>
                <button onclick="openModal(<?php echo $char['pID']; ?>)" class="mt-4 bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded">Hapus</button>
            </div>
        <?php endwhile; ?>
      </div>
    </main>
  </div>

  <!-- Modal Hapus -->
  <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
    <div class="bg-white p-6 rounded shadow-md max-w-sm text-center">
      <h3 class="text-lg font-semibold mb-4">Konfirmasi Hapus</h3>
      <p>Yakin ingin menghapus karakter ini?</p>
      <form id="deleteForm" method="POST" action="hapus_character.php" class="mt-4">
        <input type="hidden" name="char_id" id="modalCharId">
        <div class="flex justify-center gap-4">
          <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Hapus</button>
          <button type="button" onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded">Batal</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openModal(charId) {
      document.getElementById('modalCharId').value = charId;
      document.getElementById('deleteModal').classList.remove('hidden');
      document.getElementById('deleteModal').classList.add('flex');
    }
    function closeModal() {
      document.getElementById('deleteModal').classList.add('hidden');
      document.getElementById('deleteModal').classList.remove('flex');
    }
  </script>
</body>
</html>
