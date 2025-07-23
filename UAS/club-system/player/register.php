<?php
include "../config/db.php";
include "../functions.php";

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
        echo "Pemain berhasil didaftarkan. Key unik pemain: <strong>$key</strong>";
    } catch (PDOException $e) {
        echo "Gagal mendaftarkan pemain: " . $e->getMessage();
    }
}
?>

<form method="post">
    <label>Nama:</label>
    <input type="text" name="nama" required><br>
    <label>Tanggal Lahir:</label>
    <input type="date" name="tanggal_lahir" required><br>
    <label>ID Klub:</label>
    <input type="number" name="club_id" required><br>
    <button type="submit">Daftar</button>
</form>