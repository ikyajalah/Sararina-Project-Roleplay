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

$account_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM ucp"))[0];
$char_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM characters"))[0];

$user = $_SESSION['ucp_login'];
$char_query = "SELECT * FROM characters WHERE Username = '$user'";
$char_result = mysqli_query($conn, $char_query);

$top_query = "SELECT PlayerName, PlayerPlayingHours, PlayerMinute FROM characters ORDER BY PlayerPlayingHours DESC, PlayerMinute DESC LIMIT 5";
$top_result = mysqli_query($conn, $top_query);
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
      <h2 class="text-2xl font-bold mb-6">Selamat Datang, <?php echo htmlspecialchars($user); ?>!</h2>

      <!-- Info Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-4 rounded shadow">
          <h3 class="font-semibold text-gray-600">Server Status</h3>
          <p>Status: <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-sm">Online</span></p>
          <p>Players: 0 / 200</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <h3 class="font-semibold text-gray-600">Registered Account</h3>
          <p class="text-xl font-bold text-blue-500"><?php echo $account_count; ?></p>
        </div>
        <div class="bg-white p-4 rounded shadow">
          <h3 class="font-semibold text-gray-600">Registered Characters</h3>
          <p class="text-xl font-bold text-blue-500"><?php echo $char_count; ?></p>
        </div>
      </div>

      <!-- Your Characters -->
      <div class="bg-white p-4 rounded shadow mb-6">
        <h3 class="text-lg font-semibold mb-4">Your Characters</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <?php while ($char = mysqli_fetch_assoc($char_result)): ?>
          <div class="border rounded-lg p-4 shadow">
            <div class="flex items-center space-x-4">
              <img src="https://assets.open.mp/assets/images/skins/<?php echo $char['PlayerSkin']; ?>.png" alt="Skin" class="w-12 h-12 rounded">
              <div>
                <p class="font-bold"><?php echo $char['PlayerName']; ?></p>
                <p class="text-sm text-gray-500">Level: <?php echo $char['PlayerLevel']; ?></p>
              </div>
            </div>
          </div>
          <?php endwhile; ?>
        </div>
      </div>

      <!-- Top Playing Time -->
      <div class="bg-white p-4 rounded shadow">
        <h3 class="text-lg font-semibold mb-4">Top Playing Time</h3>
        <table class="w-full text-left">
          <thead>
            <tr>
              <th class="py-2">Player</th>
              <th class="py-2">Hours</th>
              <th class="py-2">Minutes</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($top = mysqli_fetch_assoc($top_result)): ?>
            <tr class="border-t">
              <td class="py-2"><?php echo $top['PlayerName']; ?></td>
              <td class="py-2"><?php echo $top['PlayerPlayingHours']; ?>h</td>
              <td class="py-2"><?php echo $top['PlayerMinute']; ?>m</td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

    </main>
  </div>
</body>
</html>
