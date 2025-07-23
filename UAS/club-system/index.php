<?php

$db = new SQLite3('database/club.db');

$clubs = $db->query("SELECT id, nama, logo FROM club");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Klub Sepakbola</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .club { border: 1px solid #ccc; padding: 10px; margin-bottom: 15px; }
        .club img { height: 50px; }
    </style>
</head>
<body>
    <h1>Selamat Datang di Sistem Klub Sepakbola</h1>
    <p>
        <a href="player/register.php">Daftarkan Pemain</a> |
        <a href="club/view_player.php">Lihat Info Pemain</a>
    </p>

    <h2>Daftar Klub:</h2>
    <?php while ($row = $clubs->fetchArray(SQLITE3_ASSOC)) : ?>
        <div class="club">
            <h3><?= htmlspecialchars($row['nama']) ?></h3>
            <?php if (!empty($row['logo'])): ?>
                <img src="<?= htmlspecialchars($row['logo']) ?>" alt="Logo <?= htmlspecialchars($row['nama']) ?>">
            <?php else: ?>
                <em>(tidak ada logo)</em>
            <?php endif; ?>
            <p>ID Klub: <?= $row['id'] ?></p>
        </div>
    <?php endwhile; ?>
</body>
</html>