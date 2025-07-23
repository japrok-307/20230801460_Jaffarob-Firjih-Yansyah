<?php
include "../config/db.php";
include "../functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $player_id = $_POST['player_id'];
    $key       = $_POST['key'];

    try {
        $stmt = $conn->prepare("SELECT nama_enkripsi FROM pemain WHERE id = ?");
        $stmt->execute([$player_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $decrypted = decrypt_data($row['nama_enkripsi'], $key);
            if ($decrypted !== false) {
                echo "Informasi pemain: <strong>$decrypted</strong>";
            } else {
                echo "Key tidak cocok atau data tidak valid.";
            }
        } else {
            echo "Pemain tidak ditemukan.";
        }
    } catch (PDOException $e) {
        echo "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<form method="post">
    <label>ID Pemain:</label>
    <input type="number" name="player_id" required><br>
    <label>Key Pemain:</label>
    <input type="text" name="key" required><br>
    <button type="submit">Lihat Info Pemain</button>
</form>