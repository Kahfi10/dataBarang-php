<?php

session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Koneksi database
$conn = mysqli_connect("localhost", "root", "", "form");
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

$notif = '';
$uploadDir = "uploads/";

// Cek role staf
$isStaf = isset($_SESSION['role']) && $_SESSION['role'] === 'staf';

// Tambah data
if ($isStaf && isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];
    $gambar = '';

    // Upload gambar jika ada
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            if (!is_dir($uploadDir)) mkdir($uploadDir);
            $newName = uniqid('img_', true) . '.' . $ext;
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $newName)) {
                $gambar = $newName;
            }
        }
    }

    if (tambahBarang($conn, $nama, $jumlah, $harga, $gambar)) {
        $notif = 'Data berhasil ditambahkan!';
    } else {
        $notif = 'Gagal menambah data!';
    }
    header("Location: index.php?notif=" . urlencode($notif));
    exit;
}

// Update data
if ($isStaf && isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];
    $gambar = $_POST['gambar_lama'];

    // Upload gambar baru jika ada
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            if (!is_dir($uploadDir)) mkdir($uploadDir);
            $newName = uniqid('img_', true) . '.' . $ext;
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $newName)) {
                // Hapus gambar lama jika ada
                if ($gambar && file_exists($uploadDir . $gambar)) unlink($uploadDir . $gambar);
                $gambar = $newName;
            }
        }
    }

    if (updateBarang($conn, $id, $nama, $jumlah, $harga, $gambar)) {
        $notif = 'Data berhasil diupdate!';
    } else {
        $notif = 'Gagal update data!';
    }
    header("Location: index.php?notif=" . urlencode($notif));
    exit;
}

// Hapus data
if ($isStaf && isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    // Hapus gambar dari folder
    $q = mysqli_query($conn, "SELECT gambar FROM barang WHERE id=$id");
    $d = mysqli_fetch_assoc($q);
    if ($d && $d['gambar'] && file_exists($uploadDir . $d['gambar'])) {
        unlink($uploadDir . $d['gambar']);
    }
    if (mysqli_query($conn, "DELETE FROM barang WHERE id=$id")) {
        $notif = 'Data berhasil dihapus!';
    } else {
        $notif = 'Gagal menghapus data!';
    }
    header("Location: index.php?notif=" . urlencode($notif));
    exit;
}

// Ambil data untuk edit
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q = mysqli_query($conn, "SELECT * FROM barang WHERE id=$id");
    $edit = mysqli_fetch_assoc($q);
}

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchQuery = '';
if (!empty($search)) {
    $searchQuery = "WHERE nama LIKE '%$search%' OR jumlah LIKE '%$search%' OR harga LIKE '%$search%'";
}

// Tambahan function untuk tambah dan update barang
function tambahBarang($conn, $nama, $jumlah, $harga, $gambar) {
    $gambar = mysqli_real_escape_string($conn, $gambar);
    return mysqli_query($conn, "INSERT INTO barang (nama, jumlah, harga, gambar) VALUES ('$nama', '$jumlah', '$harga', '$gambar')");
}

function updateBarang($conn, $id, $nama, $jumlah, $harga, $gambar) {
    $gambar = mysqli_real_escape_string($conn, $gambar);
    return mysqli_query($conn, "UPDATE barang SET nama='$nama', jumlah='$jumlah', harga='$harga', gambar='$gambar' WHERE id=$id");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Data Barang</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Flex:opsz,wght@8..144,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { font-family: "Roboto Flex", sans-serif; font-optical-sizing: auto; font-weight: 300; font-style: normal; background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%); margin: 0; padding: 0; }
        h1 { text-align: center; margin-top: 20px; font-size: 40px; color: #404040; font-family:'Poppins', 'Roboto', Arial, sans-serif; font-weight: 1000; }
        h2 { text-align: center; margin-top: 10px; font-size: 30px; color: #404040; }
        form { background: #fff; max-width: 400px; margin: 30px auto; padding: 24px 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07);}
        label { display: block; margin-top: 16px; font-size: 18px; }
        .search-info { text-align: center; color: #666; margin: 10px 0; font-size: 16px; }
        input[type="text"], input[type="number"] {font-size: 15px; width: 100%; padding: 8px; margin-top: 6px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;}
        button { margin-top: 22px; padding: 10px 24px; background: #3498db; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 15px;}
        button:hover { background: #217dbb; }
        table { margin: 40px auto; border-collapse: collapse; width: 90%; background: #fff; box-shadow: 0 4px 24px rgba(52,152,219,0.10), 0 1.5px 6px rgba(44,62,80,0.07);}
        th, td { border: 1px solid #ccc; padding: 10px 14px; text-align: center; }
        th { background: #eee; }
        a { text-decoration: none; color: #3498db; }
        a:hover { text-decoration: underline; }
        .header { background: #f2f2f2; text-align: center; font-size: 13px; }
        .tabel-row { font-size: large; transition: background 0.2s; }
        .tabel-row:hover { background: #e3f2fd; }
        .img-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; border:1px solid #eee; }
        /* Search Box */
        .search-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .search-box {
            display: flex;
            gap: 10px;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            transition: box-shadow 0.2s;
        }
        .search-box:hover {
            box-shadow: 0 8px 32px rgba(52,152,219,0.18), 0 3px 12px rgba(44,62,80,0.13);
        }
        .search-box input[type="text"] {
            flex: 1;
            margin-top: 0;
            font-size: 16px;
        }
        .search-box button {
            margin-top: 0;
            padding: 8px 16px;
            background: #27ae60;
        }
        .search-box button:hover {
            background: #219a52;
        }
        .search-info {
            text-align: center;
            color: #666;
            margin: 10px 0;
        }
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
        .custom-file {
            position: relative;
            margin-bottom: 10px;
        }
        .custom-file input[type="file"] {
            opacity: 0;
            width: 100%;
            height: 40px;
            position: absolute;
            left: 0; top: 0;
            cursor: pointer;
            z-index: 2;
        }
        .custom-file label {
            display: inline-block;
            background: linear-gradient(90deg, #3498db 70%, #6dd5fa 120%);
            color: #fff;
            padding: 10px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 15px;
            transition: background 0.2s;
            margin-right: 12px;
            z-index: 1;
            position: relative;
        }
        .custom-file label:hover {
            background: linear-gradient(90deg, #217dbb 60%, #3498db 100%);
        }
        .file-chosen {
            font-size: 14px;
            color: #2980b9;
            vertical-align: middle;
        }
        /* Responsive Table */
        @media (max-width: 700px) {
            table, thead, tbody, th, td, tr { display: block; }
            th, td { text-align: left; }
            tr { margin-bottom: 15px; }
        }
    </style>
</head>
<body>
    <div style="display:flex; justify-content:space-between; align-items:center; margin:20px 30px 0 30px;">
        <div style="color:#2980b9; font-size:17px; background:#eaf6fb; padding:8px 18px 8px 14px; border-radius:22px; box-shadow:0 2px 8px rgba(52,152,219,0.08); display:flex; align-items:center; gap:10px;">
            <i class="fa fa-user-circle" style="font-size:22px;"></i>
            <span>Login sebagai: <b><?= htmlspecialchars($_SESSION['username']) ?></b></span>
            <a href="profile.php" style="margin-left:16px; display:inline-flex; align-items:center; gap:6px; background:linear-gradient(90deg,#6dd5fa 60%,#3498db 100%); color:#fff; font-weight:600; padding:6px 18px; border-radius:18px; font-size:15px; text-decoration:none; box-shadow:0 2px 8px rgba(52,152,219,0.08); transition:background 0.2s;">
                <i class="fa fa-id-badge"></i> Profil
            </a>
        </div>
        <a href="logout.php" style="display:flex; align-items:center; gap:8px; background:linear-gradient(90deg,#e74c3c 60%,#ff7675 100%); color:#fff; font-weight:bold; padding:8px 22px; border-radius:22px; box-shadow:0 2px 8px rgba(231,76,60,0.08); font-size:16px; text-decoration:none; transition:background 0.2s;">
            <i class="fa fa-sign-out-alt"></i> Logout
        </a>
    </div>
    <div style="display:flex; justify-content:center; gap:18px; margin:24px 0 0 0;">
    <a href="kategori.php" style="background:linear-gradient(90deg,#f1c40f 60%,#f9e79f 100%); color:#444; font-weight:600; padding:7px 22px; border-radius:18px; font-size:15px; text-decoration:none; box-shadow:0 2px 8px rgba(241,196,15,0.08); transition:background 0.2s;">
        <i class="fa fa-tags"></i> Kategori
    </a>
    <a href="supplier.php" style="background:linear-gradient(90deg,#16a085 60%,#48c9b0 100%); color:#fff; font-weight:600; padding:7px 22px; border-radius:18px; font-size:15px; text-decoration:none; box-shadow:0 2px 8px rgba(22,160,133,0.08); transition:background 0.2s;">
        <i class="fa fa-truck"></i> Supplier
    </a>
    <a href="about.php" style="background:linear-gradient(90deg,#8e44ad 60%,#d2b4de 100%); color:#fff; font-weight:600; padding:7px 22px; border-radius:18px; font-size:15px; text-decoration:none; box-shadow:0 2px 8px rgba(142,68,173,0.08); transition:background 0.2s;">
        <i class="fa fa-info-circle"></i> Tentang
    </a>
    </div>
    <h1>PENDATAAN BARANG TOKOKU</h1>
    <div id="notif" class="notif"></div>
    <div id="modal" class="modal-bg">
        <div class="modal-box">
            <div id="modal-msg" style="margin-bottom:18px;font-size:18px;">Yakin ingin menghapus data?</div>
            <button id="modal-yes" style="background:#e74c3c;">Hapus</button>
            <button id="modal-no" style="background:#aaa;">Batal</button>
        </div>
    </div>
    
    <!-- Search Box -->
    <div class="search-container">
        <form method="get" class="search-box">
            <input type="text" name="search" placeholder="Cari barang..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit"><i class="fa fa-search"></i> Cari</button>
            <?php if (!empty($search)): ?>
                <a href="index.php" style="justify-content:center; align-items:center;font-size:10px; padding: 16px 16px; background: #e74c3c; color: white; text-decoration: none; border-radius: 4px;">Reset</a>
            <?php endif; ?>
        </form>
        <?php if (!empty($search)): ?>
            <div class="search-info">Hasil pencarian untuk: "<strong><?= htmlspecialchars($search) ?></strong>"</div>
        <?php endif; ?>
    </div>

    <?php if ($isStaf): ?>
    <form method="post" autocomplete="off" enctype="multipart/form-data">
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
        <label>Gambar</label>
        <div class="custom-file">
            <input type="file" name="gambar" id="gambar" accept="image/*" onchange="showFileName()" />
            <label for="gambar" id="file-label"><i class="fa fa-upload"></i> Pilih Gambar</label>
            <span id="file-chosen" class="file-chosen">Tidak ada file dipilih</span>
        </div>
        <?php if ($edit && $edit['gambar']): ?>
            <div style="margin:10px 0;">
                <img src="uploads/<?= htmlspecialchars($edit['gambar']) ?>" class="img-thumb">
                <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($edit['gambar']) ?>">
            </div>
        <?php else: ?>
            <input type="hidden" name="gambar_lama" value="">
        <?php endif; ?>
        <button type="submit" name="<?= $edit ? 'update' : 'tambah' ?>">
            <i class="fa <?= $edit ? 'fa-edit' : 'fa-save' ?>"></i> <?= $edit ? 'Update' : 'Simpan' ?>
        </button>
        <?php if ($edit): ?>
            <a href="index.php" style="margin-left:10px;">Batal</a>
        <?php endif; ?>
    </form>
    <?php endif; ?>

    <table>
        <caption><h2>Daftar Barang</h2></caption>
        <tr class="header">
            <th>No</th><th>Nama</th><th>Jumlah</th><th>Harga</th><th>Gambar</th><th>Aksi</th>
        </tr>
        <?php
        $no = 1;
        $barang = mysqli_query($conn, "SELECT * FROM barang $searchQuery ORDER BY id DESC");
        $totalRows = mysqli_num_rows($barang);

        if ($totalRows > 0) {
            while ($row = mysqli_fetch_assoc($barang)):
        ?>
        <tr class="tabel-row">
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= $row['jumlah'] ?></td>
            <td><?= number_format($row['harga']) ?></td>
            <td>
                <?php if ($row['gambar']): ?>
                    <img src="uploads/<?= htmlspecialchars($row['gambar']) ?>" class="img-thumb">
                <?php else: ?>
                    <span style="color:#aaa;">(Tidak ada gambar)</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="detail.php?id=<?= $row['id'] ?>" style="color:#2980b9; font-weight:bold;">
                    <i class="fa fa-eye"></i> Detail
                </a>
                <?php if ($isStaf): ?>
                 | 
                <a href="index.php?edit=<?= $row['id'] ?>" style="color:#f39c12; font-weight:bold;">
                    <i class="fa fa-edit"></i> Edit
                </a> | 
                <a href="#" class="hapus-link" data-id="<?= $row['id'] ?>" style="color:#e74c3c; font-weight:bold;">
                    <i class="fa fa-trash"></i> Hapus
                </a>
                <?php endif; ?>
            </td>
        </tr>
        <?php 
            endwhile;
        } else {
        ?>
        <tr>
            <td colspan="6" style="text-align: center; color: #666; font-style: italic;">
                <?= !empty($search) ? "Tidak ada data yang ditemukan untuk '$search'" : "Belum ada data barang" ?>
            </td>
        </tr>
        <?php } ?>
    </table>
    
    <?php if (!empty($search) && $totalRows > 0): ?>
        <div style="text-align: center; margin: 20px 0; color: #666;">
            Menampilkan <?= $totalRows ?> hasil
        </div>
    <?php endif; ?>

    <footer style="text-align:center; color:#888; margin:40px 0 10px 0;">
        &copy; <?= date('Y') ?> TOKOKU. All rights reserved.
    </footer>

    <script>
        function showFileName() {
        var input = document.getElementById('gambar');
        var label = document.getElementById('file-label');
        var chosen = document.getElementById('file-chosen');
        if (input.files.length > 0) {
            chosen.textContent = input.files[0].name;
            label.style.background = "#27ae60";
        } else {
            chosen.textContent = "Tidak ada file dipilih";
            label.style.background = "";
        }
    }
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

    // untuk konfirmasi hapus
    document.querySelectorAll('.hapus-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var id = this.getAttribute('data-id');
            var modal = document.getElementById('modal');
            modal.style.display = 'flex';
            document.getElementById('modal-yes').onclick = function() {
                window.location = 'index.php?hapus=' + id;
            };
            document.getElementById('modal-no').onclick = function() {
                modal.style.display = 'none';
            };
        });
    });

    // button tutup jika klik di luar box
    document.getElementById('modal').addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
    </script>
</body>
</html>