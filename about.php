<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tentang Aplikasi TOKOKU</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
            margin: 0; padding: 0;
        }
        .about-box {
            background: #fff;
            max-width: 600px;
            margin: 60px auto;
            padding: 36px 32px 28px 32px;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(52,152,219,0.13), 0 1.5px 6px rgba(44,62,80,0.07);
            animation: fadeIn 0.7s;
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
        .about-content {
            font-size: 18px;
            color: #444;
            margin-bottom: 24px;
            line-height: 1.7;
        }
        .about-list {
            margin: 0 0 18px 0;
            padding-left: 22px;
            color: #2980b9;
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
    </style>
</head>
<body>
    <div class="about-box">
        <h2><i class="fa fa-info-circle"></i> Tentang Aplikasi</h2>
        <div class="about-content">
            <b>Data Barang TOKOKU</b> adalah aplikasi web sederhana untuk mengelola data barang pada toko atau inventaris kantor.<br><br>
            Fitur utama aplikasi ini:
            <ul class="about-list">
                <li><i class="fa fa-check-circle"></i> Manajemen data barang (CRUD)</li>
                <li><i class="fa fa-check-circle"></i> Upload & tampil gambar barang</li>
                <li><i class="fa fa-check-circle"></i> Manajemen kategori barang</li>
                <li><i class="fa fa-check-circle"></i> Data supplier</li>
                <li><i class="fa fa-check-circle"></i> Profil staf & pengunjung</li>
                <li><i class="fa fa-check-circle"></i> Hak akses staf & pengunjung</li>
                <li><i class="fa fa-check-circle"></i> Pencarian data barang</li>
            </ul>
            <div style="margin-top:18px;">
                <b>Developer:</b> <span style="color:#2980b9;">Tim TOKOKU</span><br>
                <b>Versi:</b> 1.0<br>
                <b>Tahun:</b> <?= date('Y') ?>
            </div>
        </div>
        <a href="index.php" class="btn"><i class="fa fa-arrow-left"></i> Kembali ke Dashboard</a>
    </div>
</body>