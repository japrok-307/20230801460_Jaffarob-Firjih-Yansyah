<?php
session_start();
include "../config/db.php";
include "../functions.php";

if (!isset($_SESSION['player_id']) || !isset($_SESSION['key'])) {
    header("Location: login.php");
    exit;
}

$player_id = $_SESSION['player_id'];
$key = $_SESSION['key'];

$stmt = $conn->prepare("SELECT nama_enkripsi FROM pemain WHERE id = ?");
$stmt->execute([$player_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    die("Data pemain tidak ditemukan.");
}

$current_nama = decrypt_data($row['nama_enkripsi'], $key);
if ($current_nama === false) {
    die("Key tidak valid untuk mendekripsi data.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_baru = $_POST['nama'];
    $nama_enkripsi_baru = encrypt_data($nama_baru, $key);

    $stmt = $conn->prepare("UPDATE pemain SET nama_enkripsi = ? WHERE id = ?");
    $stmt->execute([$nama_enkripsi_baru, $player_id]);

    $success = "Nama berhasil diperbarui.";
    $current_nama = $nama_baru; // update tampilan
}
?>

<?php include "../includes/header.php"; ?>
<h2>Edit Profil Pemain</h2>

<?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

<form method="post">
    <label>Nama Saat Ini:</label>
    <input type="text" value="<?= htmlspecialchars($current_nama) ?>" name="nama" required><br>
    <button type="submit">Simpan Perubahan</button>
</form>

<p><a href="logout.php">Logout</a></p>
<?php include "../includes/footer.php"; ?>
