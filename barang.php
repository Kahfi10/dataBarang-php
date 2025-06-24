<?php
// Koneksi database
$conn = mysqli_connect("localhost", "root", "", "form");
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

$notif = '';
// Tambah data
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];
    if (mysqli_query($conn, "INSERT INTO barang (nama, jumlah, harga) VALUES ('$nama', '$jumlah', '$harga')")) {
        $notif = 'Data berhasil ditambahkan!';
    } else {
        $notif = 'Gagal menambah data!';
    }
    header("Location: barang.php?notif=" . urlencode($notif));
    exit;
}

// Update data
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];
    if (mysqli_query($conn, "UPDATE barang SET nama='$nama', jumlah='$jumlah', harga='$harga' WHERE id=$id")) {
        $notif = 'Data berhasil diupdate!';
    } else {
        $notif = 'Gagal update data!';
    }
    header("Location: barang.php?notif=" . urlencode($notif));
    exit;
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    if (mysqli_query($conn, "DELETE FROM barang WHERE id=$id")) {
        $notif = 'Data berhasil dihapus!';
    } else {
        $notif = 'Gagal menghapus data!';
    }
    header("Location: barang.php?notif=" . urlencode($notif));
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
        .tabel-row { font-size: large; transition: background 0.2s; }
        .tabel-row:hover { background: #e3f2fd; }
        /* Notifikasi */
        .notif {
            display: none;
            position: fixed;
            top: 30px; left: 50%; transform: translateX(-50%);
            background: #4caf50; color: #fff; padding: 16px 32px;
            border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.13);
            font-size: 18px; z-index: 1000;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }
        .notif.error { background: #e74c3c; }
        @keyframes fadein { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeout { from { opacity: 1; } to { opacity: 0; } }
        /* Modal */
        .modal-bg {
            display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.25); justify-content: center; align-items: center;
        }
        .modal-box {
            background: #fff; padding: 28px 32px; border-radius: 8px; box-shadow: 0 2px 12px rgba(0,0,0,0.18);
            text-align: center; min-width: 300px;
        }
        .modal-box button { margin: 0 12px; }
    </style>
</head>
<body>
    <div id="notif" class="notif"></div>
    <div id="modal" class="modal-bg">
        <div class="modal-box">
            <div id="modal-msg" style="margin-bottom:18px;font-size:18px;">Yakin ingin menghapus data?</div>
            <button id="modal-yes" style="background:#e74c3c;">Hapus</button>
            <button id="modal-no" style="background:#aaa;">Batal</button>
        </div>
    </div>
    <form method="post" autocomplete="off">
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
                <a href="#" class="hapus-link" data-id="<?= $row['id'] ?>">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <script>
    // Notifikasi dari PHP
    <?php if (isset($_GET['notif'])): ?>
        window.addEventListener('DOMContentLoaded', function() {
            var notif = document.getElementById('notif');
            notif.textContent = <?= json_encode($_GET['notif']) ?>;
            notif.classList.remove('error');
            <?php if (strpos($_GET['notif'], 'Gagal') !== false): ?>
                notif.classList.add('error');
            <?php endif; ?>
            notif.style.display = 'block';
            setTimeout(function() { notif.style.display = 'none'; }, 3000);
        });
    <?php endif; ?>

    // Modal konfirmasi hapus
    document.querySelectorAll('.hapus-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var id = this.getAttribute('data-id');
            var modal = document.getElementById('modal');
            modal.style.display = 'flex';
            document.getElementById('modal-yes').onclick = function() {
                window.location = 'barang.php?hapus=' + id;
            };
            document.getElementById('modal-no').onclick = function() {
                modal.style.display = 'none';
            };
        });
    });

    // Tutup modal jika klik di luar box
    document.getElementById('modal').addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
    </script>
</body>
</html>