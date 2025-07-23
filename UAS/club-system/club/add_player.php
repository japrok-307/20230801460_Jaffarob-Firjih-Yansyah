<?php
include __DIR__ . "/../config/db.php";
include __DIR__ . "/../functions.php";
include __DIR__ . "/../includes/header.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = $_POST['nama'];
    $dob      = $_POST['tanggal_lahir'];
    $club_id  = $_POST['club_id'];
    $key      = substr(sha1($dob . $nama), 0, 16);

    $encrypted_nama = encrypt_data($nama, $key);
    $key_hash       = password_hash($key, PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("INSERT INTO pemain (club_id, nama_enkripsi, key_hash) VALUES (?, ?, ?)");
        $stmt->execute([$club_id, $encrypted_nama, $key_hash]);
        echo "<p>Pemain berhasil didaftarkan.</p>";
        echo "<p><strong>Key unik pemain:</strong> <code>$key</code></p>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Gagal mendaftarkan pemain: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<h2>Formulir Pendaftaran Pemain</h2>
<form method="post">
    <label>Nama:</label><br>
    <input type="text" name="nama" required><br><br>

    <label>Tanggal Lahir:</label><br>
    <input type="date" name="tanggal_lahir" required><br><br>

    <label>ID Klub:</label><br>
    <input type="number" name="club_id" required><br><br>

    <button type="submit">Daftar</button>
</form>

<?php
include __DIR__ . "/../includes/footer.php";
?>
