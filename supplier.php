<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Hanya staf yang boleh akses tambah/edit/hapus
$isStaf = isset($_SESSION['role']) && $_SESSION['role'] === 'staf';

$conn = mysqli_connect("localhost", "root", "", "form");
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

$notif = '';
// Tambah supplier
if ($isStaf && isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    $kontak = mysqli_real_escape_string($conn, $_POST['kontak']);
    if (mysqli_query($conn, "INSERT INTO supplier (nama_supplier, kontak) VALUES ('$nama', '$kontak')")) {
        $notif = "Supplier berhasil ditambahkan!";
    } else {
        $notif = "Gagal menambah supplier!";
    }
    header("Location: supplier.php?notif=" . urlencode($notif));
    exit;
}

// Edit supplier
if ($isStaf && isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    $kontak = mysqli_real_escape_string($conn, $_POST['kontak']);
    if (mysqli_query($conn, "UPDATE supplier SET nama_supplier='$nama', kontak='$kontak' WHERE id=$id")) {
        $notif = "Supplier berhasil diupdate!";
    } else {
        $notif = "Gagal update supplier!";
    }
    header("Location: supplier.php?notif=" . urlencode($notif));
    exit;
}

// Hapus supplier
if ($isStaf && isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    if (mysqli_query($conn, "DELETE FROM supplier WHERE id=$id")) {
        $notif = "Supplier berhasil dihapus!";
    } else {
        $notif = "Gagal menghapus supplier!";
    }
    header("Location: supplier.php?notif=" . urlencode($notif));
    exit;
}

// Ambil data untuk edit
$edit = null;
if ($isStaf && isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q = mysqli_query($conn, "SELECT * FROM supplier WHERE id=$id");
    $edit = mysqli_fetch_assoc($q);
}

// Ambil semua supplier
$supplier = mysqli_query($conn, "SELECT * FROM supplier ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Supplier</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { font-family: 'Poppins', Arial, sans-serif; background: #e0eafc; margin:0; padding:0;}
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 10px; box-shadow: 0 2px 12px rgba(52,152,219,0.13); padding: 32px 28px;}
        h2 { font-size: 20px; text-align:center; color:#2980b9; margin-bottom:24px;}
        form { margin-bottom: 24px; }
        label {font-weight: 600; font-size:16px; margin-top: 20px; }
        input[type="text"] { width:95%; padding:8px 12px; border:1px solid #b3c6e0; border-radius:5px; font-size:15px; margin-top:7px; margin-bottom: 10px;}
        button { margin-top:12px; padding:8px 22px; background:#3498db; color:#fff; border:none; border-radius:5px; font-size:15px; cursor:pointer;}
        button:hover { background:#217dbb; }
        table { width:100%; border-collapse:collapse; margin-top:18px;}
        th, td {font-size: 16px; color: #303030; border:1px solid #eee; padding:10px 12px; text-align:left;}
        th { background:#f2f2f2;}
        .aksi a { margin-right:10px; color:#3498db; font-weight:bold;}
        .aksi a.hapus { color:#e74c3c;}
        .notif { margin-bottom:18px; padding:10px 16px; border-radius:6px; font-size:15px; text-align:center; background:#eafaf1; color:#27ae60;}
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fa fa-truck"></i> Data Supplier</h2>
        <?php if (isset($_GET['notif'])): ?>
            <div class="notif"><?= htmlspecialchars($_GET['notif']) ?></div>
        <?php endif; ?>

        <?php if ($isStaf): ?>
        <form method="post" autocomplete="off">
            <?php if ($edit): ?>
                <input type="hidden" name="id" value="<?= $edit['id'] ?>">
            <?php endif; ?>
            <label>Nama Supplier</label>
            <input type="text" name="nama_supplier" value="<?= $edit ? htmlspecialchars($edit['nama_supplier']) : '' ?>" required>
            <label>Kontak</label>
            <input type="text" name="kontak" value="<?= $edit ? htmlspecialchars($edit['kontak']) : '' ?>">
            <button type="submit" name="<?= $edit ? 'update' : 'tambah' ?>">
                <i class="fa <?= $edit ? 'fa-edit' : 'fa-plus' ?>"></i> <?= $edit ? 'Update' : 'Tambah' ?>
            </button>
            <?php if ($edit): ?>
                <a href="supplier.php" style="margin-left:10px;">Batal</a>
            <?php endif; ?>
        </form>
        <?php endif; ?>

        <table>
            <tr>
                <th>No</th>
                <th>Nama Supplier</th>
                <th>Kontak</th>
                <?php if ($isStaf): ?><th>Aksi</th><?php endif; ?>
            </tr>
            <?php $no=1; while($row = mysqli_fetch_assoc($supplier)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_supplier']) ?></td>
                <td><?= htmlspecialchars($row['kontak']) ?></td>
                <?php if ($isStaf): ?>
                <td class="aksi">
                    <a href="supplier.php?edit=<?= $row['id'] ?>"><i class="fa fa-edit"></i> Edit</a>
                    <a href="supplier.php?hapus=<?= $row['id'] ?>" class="hapus" onclick="return confirm('Yakin hapus supplier ini?')"><i class="fa fa-trash"></i> Hapus</a>
                </td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </table>
        <a href="index.php" style="font-size: 15px; display:inline-block;margin-top:24px;color:#2980b9;text-decoration:none;"><i class="fa fa-arrow-left"></i> Kembali ke Dashboard</a>
    </div>
</body>
</html>