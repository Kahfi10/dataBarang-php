<?php
// Koneksi database
$conn = mysqli_connect("localhost", "root", "", "form");
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

// Tambah data
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];
    mysqli_query($conn, "INSERT INTO barang (nama, jumlah, harga) VALUES ('$nama', '$jumlah', '$harga')");
    header("Location: barang.php");
    exit;
}

// Update data
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];
    mysqli_query($conn, "UPDATE barang SET nama='$nama', jumlah='$jumlah', harga='$harga' WHERE id=$id");
    header("Location: barang.php");
    exit;
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM barang WHERE id=$id");
    header("Location: barang.php");
    exit;
}

// Ambil data untuk edit
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q = mysqli_query($conn, "SELECT * FROM barang WHERE id=$id");
    $edit = mysqli_fetch_assoc($q);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Barang</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; margin: 0; padding: 0; }
        h2 { text-align: center; margin-top: 20px; font-size: 30px; }
        form { background: #fff; max-width: 400px; margin: 40px auto; padding: 24px 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07);}
        label { display: block; margin-top: 16px; font-size: 18px; }
        input[type="text"], input[type="number"] {font-size: 15px; width: 100%; padding: 8px; margin-top: 6px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        button { margin-top: 22px; padding: 10px 24px; background: #3498db; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 15px;}
        button:hover { background: #217dbb; }
        table { margin: 40px auto; border-collapse: collapse; width: 90%; background: #fff; }
        th, td { border: 1px solid #ccc; padding: 10px 14px; text-align: center; }
        th { background: #eee; }
        a { text-decoration: none; color: #3498db; }
        a:hover { text-decoration: underline; }
        .header { background: #f2f2f2; text-align: center; font-size: 13px; }
        .tabel-row { font-size: large; }
    </style>
</head>
<body>
    <form method="post">
        <h2><?= $edit ? 'Edit Barang' : 'Tambah Barang' ?></h2>
        <?php if ($edit): ?>
            <input type="hidden" name="id" value="<?= $edit['id'] ?>">
        <?php endif; ?>
        <label>Nama Barang</label>
        <input type="text" name="nama" value="<?= $edit ? htmlspecialchars($edit['nama']) : '' ?>" required>
        <label>Jumlah</label>
        <input type="number" name="jumlah" value="<?= $edit ? $edit['jumlah'] : '' ?>" required>
        <label>Harga</label>
        <input type="number" name="harga" value="<?= $edit ? $edit['harga'] : '' ?>" required>
        <button type="submit" name="<?= $edit ? 'update' : 'tambah' ?>">
            <?= $edit ? 'Update' : 'Simpan' ?>
        </button>
        <?php if ($edit): ?>
            <a href="barang.php" style="margin-left:10px;">Batal</a>
        <?php endif; ?>
    </form>

    <table>
        <caption><h2>Daftar Barang</h2></caption>
        <tr class="header">
            <th>No</th><th>Nama</th><th>Jumlah</th><th>Harga</th><th>Aksi</th>
        </tr>
        <?php
        $no = 1;
        $barang = mysqli_query($conn, "SELECT * FROM barang");
        while ($row = mysqli_fetch_assoc($barang)):
        ?>
        <tr class="tabel-row">
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= $row['jumlah'] ?></td>
            <td><?= $row['harga'] ?></td>
            <td>
                <a href="barang.php?edit=<?= $row['id'] ?>">Edit</a> | 
                <a href="barang.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus data?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>