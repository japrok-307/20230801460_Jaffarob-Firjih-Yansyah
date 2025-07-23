<?php
session_start();
include "../config/db.php";
include "../functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $player_id = $_POST['player_id'];
    $key = $_POST['key'];

    $stmt = $conn->prepare("SELECT key_hash FROM pemain WHERE id = ?");
    $stmt->execute([$player_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($key, $row['key_hash'])) {
        $_SESSION['player_id'] = $player_id;
        $_SESSION['key'] = $key;
        header("Location: edit_profil.php");
        exit;
    } else {
        $error = "Login gagal. Periksa ID dan key.";
    }
}
?>

<?php include "../includes/header.php"; ?>
<h2>Login Pemain</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
    <label>ID Pemain:</label>
    <input type="number" name="player_id" required><br>
    <label>Key Pemain:</label>
    <input type="text" name="key" required><br>
    <button type="submit">Login</button>
</form>
<?php include "../includes/footer.php"; ?>
