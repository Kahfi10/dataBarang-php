<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "form");
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);
$q = mysqli_query($conn, "SELECT * FROM barang WHERE id=$id");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    echo "<h2 style='text-align:center;margin-top:60px;'>Data barang tidak ditemukan.</h2>";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Detail Barang</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Flex:opsz,wght@8..144,100..1000&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Roboto Flex", sans-serif;
            font-optical-sizing: auto;
            font-weight: 300;
            font-style: normal;
            background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
            margin: 0;
            padding: 0;
        }
        .box {
            background: #fff;
            max-width: 420px;
            margin: 60px auto;
            padding: 36px 32px 28px 32px;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(52,152,219,0.13), 0 1.5px 6px rgba(44,62,80,0.07);
            position: relative;
            animation: fadeIn 0.7s;
        }
        .img-detail {
            display: block;
            margin: 0 auto 22px auto;
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid #e0eafc;
            box-shadow: 0 2px 8px rgba(52,152,219,0.10);
            background: #f8fbff;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }
        h2 {
            text-align: center;
            margin-bottom: 28px;
            color: #2980b9;
            letter-spacing: 1px;
        }
        .info {
            font-size: 18px;
            margin: 18px 0 0 0;
            padding: 12px 0 12px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .info:last-child {
            border-bottom: none;
        }
        .info i {
            color: #3498db;
            min-width: 24px;
            text-align: center;
        }
        .btn {
            display: block;
            margin: 32px auto 0 auto;
            padding: 12px 32px;
            background: linear-gradient(90deg, #3498db 60%, #6dd5fa 100%);
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-size: 17px;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(52,152,219,0.08);
            transition: background 0.2s;
        }
        .btn:hover {
            background: linear-gradient(90deg, #217dbb 60%, #3498db 100%);
        }
        .back {
            position: absolute;
            left: 18px;
            top: 18px;
            color: #2980b9;
            font-size: 20px;
            text-decoration: none;
            transition: color 0.2s;
        }
        .back:hover {
            color: #145a8a;
        }
    </style>
</head>
<body>
    <div class="box">
        <a href="index.php" class="back" title="Kembali"><i class="fa fa-arrow-left"></i></a>
        <h2><i class="fa fa-box"></i> Detail Barang</h2>
        <?php if (!empty($data['gambar'])): ?>
            <img src="uploads/<?= htmlspecialchars($data['gambar']) ?>" alt="Gambar Barang" class="img-detail">
        <?php else: ?>
            <img src="https://via.placeholder.com/160x160?text=No+Image" alt="Tidak ada gambar" class="img-detail">
        <?php endif; ?>
        <div class="info"><i class="fa fa-tag"></i> <b>Nama Barang:</b> <?= htmlspecialchars($data['nama']) ?></div>
        <div class="info"><i class="fa fa-layer-group"></i> <b>Jumlah:</b> <?= $data['jumlah'] ?></div>
        <div class="info"><i class="fa fa-money-bill-wave"></i> <b>Harga:</b> Rp <?= number_format($data['harga']) ?></div>
        <div class="info"><i class="fa fa-barcode"></i> <b>ID Barang:</b> <?= $data['id'] ?></div>
        <a href="index.php" class="btn"><i class="fa fa-list"></i> Kembali ke Daftar</a>
    </div>
</body>
</html>