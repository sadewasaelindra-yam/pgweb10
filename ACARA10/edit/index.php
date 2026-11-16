<?php
// Memanggil koneksi database (keluar 1 folder ke direktori utama)
include '../koneksi.php';

// Memeriksa apakah parameter id ada di URL
if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'];

// Menggunakan Prepared Statement untuk keamanan SQL Injection
$stmt = mysqli_prepare($koneksi, "SELECT * FROM pariwisata WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

// Jika data tidak ditemukan
if (!$data) {
    echo "Data tidak ditemukan!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Destinasi Pariwisata</title>
    <!-- Menghubungkan berkas style.css dari direktori utama -->
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <div class="card-form">
        <h2>Edit Data Pariwisata</h2>

        <!-- Form mengirimkan data baru ke edit.php via metode POST -->
        <form action="edit.php" method="POST">
            <!-- ID disimpan secara tersembunyi (hidden input) -->
            <input type="hidden" name="id" value="<?= $data['id']; ?>">

            <div class="form-group">
                <label>OGC FID:</label>
                <input type="text" name="ogc_fid" value="<?= htmlspecialchars($data['ogc_fid']); ?>" required>
            </div>
            <div class="form-group">
                <label>Nama / Remark:</label>
                <input type="text" name="remark" value="<?= htmlspecialchars($data['remark']); ?>" required>
            </div>
            <div class="form-group">
                <label>Longitude:</label>
                <input type="text" name="longitude" value="<?= htmlspecialchars($data['longitude']); ?>" required>
            </div>
            <div class="form-group">
                <label>Latitude:</label>
                <input type="text" name="latitude" value="<?= htmlspecialchars($data['latitude']); ?>" required>
            </div>
            <div class="form-group">
                <label>Namobj:</label>
                <input type="text" name="namobj" value="<?= htmlspecialchars($data['namobj']); ?>" required>
            </div>
            <div class="form-group">
                <label>Simbol:</label>
                <input type="text" name="simbol" value="<?= htmlspecialchars($data['simbol']); ?>" required>
            </div>

            <button type="submit" class="btn-submit">Simpan Perubahan</button>
            <a href="../index.php" class="btn-cancel">Batal</a>
        </form>
    </div>

</body>
</html>