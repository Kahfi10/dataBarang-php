<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "form");
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

// Ambil data user dari session
$username = $_SESSION['username'];
$user = mysqli_query($conn, "SELECT * FROM user WHERE username='$username' LIMIT 1");
$data = mysqli_fetch_assoc($user);

// Ambil data profile jika ada
$profile = mysqli_query($conn, "SELECT * FROM profile WHERE user_id=" . $data['id'] . " LIMIT 1");
$profile_data = mysqli_fetch_assoc($profile);

$notif = "";

// Proses update profil
if (isset($_POST['update'])) {
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_password = $_POST['password'];
    $id = $data['id'];

    // Data profile tambahan
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $foto = $profile_data ? $profile_data['foto'] : '';

    // Upload foto jika ada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            if (!is_dir('uploads/profile')) mkdir('uploads/profile', 0777, true);
            $newName = uniqid('profile_', true) . '.' . $ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/profile/' . $newName)) {
                // Hapus foto lama jika ada
                if ($foto && file_exists('uploads/profile/' . $foto)) unlink('uploads/profile/' . $foto);
                $foto = $newName;
            }
        }
    }

    // Update user table
    if (!empty($new_password)) {
        $new_password = mysqli_real_escape_string($conn, $new_password);
        $update = mysqli_query($conn, "UPDATE user SET username='$new_username', password='$new_password' WHERE id=$id");
    } else {
        $update = mysqli_query($conn, "UPDATE user SET username='$new_username' WHERE id=$id");
    }

    // Update/insert profile table
    if ($profile_data) {
        $update_profile = mysqli_query($conn, "UPDATE profile SET nama_lengkap='$nama_lengkap', email='$email', no_hp='$no_hp', alamat='$alamat', foto='$foto' WHERE user_id=$id");
    } else {
        $update_profile = mysqli_query($conn, "INSERT INTO profile (user_id, nama_lengkap, email, no_hp, alamat, foto) VALUES ($id, '$nama_lengkap', '$email', '$no_hp', '$alamat', '$foto')");
    }

    if ($update && $update_profile) {
        $_SESSION['username'] = $new_username;
        $notif = "<div class='notif success'><i class='fa fa-check-circle'></i> Profil berhasil diupdate!</div>";
        // Refresh data
        $user = mysqli_query($conn, "SELECT * FROM user WHERE id=$id LIMIT 1");
        $data = mysqli_fetch_assoc($user);
        $profile = mysqli_query($conn, "SELECT * FROM profile WHERE user_id=$id LIMIT 1");
        $profile_data = mysqli_fetch_assoc($profile);
    } else {
        $notif = "<div class='notif error'><i class='fa fa-times-circle'></i> Gagal update profil!</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profil Staf</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', 'Roboto', Arial, sans-serif;
            background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
            margin: 0;
            padding: 0;
        }
        .box {
            background: #fff;
            max-width: 430px;
            margin: 60px auto;
            padding: 32px 28px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(52,152,219,0.13), 0 1.5px 6px rgba(44,62,80,0.07);
            text-align: center;
        }
        h2 {
            color: #2980b9;
            margin-bottom: 24px;
            font-size: 28px;
            letter-spacing: 1px;
        }
        .info {
            font-size: 18px;
            margin: 18px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: center;
        }
        .info i {
            color: #3498db;
            min-width: 24px;
            text-align: center;
        }
        .btn, button[type="submit"] {
            display: inline-block;
            margin: 30px auto 0 auto;
            padding: 10px 24px;
            background: linear-gradient(90deg, #3498db 60%, #6dd5fa 100%);
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(52,152,219,0.08);
            transition: background 0.2s;
        }
        .btn:hover, button[type="submit"]:hover {
            background: linear-gradient(90deg, #217dbb 60%, #3498db 100%);
        }
        form {
            margin-top: 18px;
            text-align: left;
        }
        label {
            display: block;
            margin: 16px 5px 6px 0;
            font-size: 15px;
            color: #333;
        }
        input[type="text"], input[type="password"], input[type="email"], input[type="tel"], textarea {
            width: 90%;
            padding: 8px 12px;
            border: 1px solid #b3c6e0;
            border-radius: 5px;
            font-size: 15px;
            background: #f8fbff;
            transition: border 0.2s;
        }
        input[type="text"]:focus, input[type="password"]:focus, input[type="email"]:focus, input[type="tel"]:focus, textarea:focus {
            border: 1.5px solid #3498db;
            outline: none;
        }
        .notif {
            margin-bottom: 18px;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 15px;
            text-align: center;
            display: block;
        }
        .notif.success { background: #eafaf1; color: #27ae60; border: 1px solid #27ae60; }
        .notif.error { background: #fdeaea; color: #e74c3c; border: 1px solid #e74c3c; }
        .back-link {
            margin-top: 18px;
            display: inline-block;
            color: #2980b9;
            text-decoration: none;
            font-size: 15px;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .profile-pic {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #e0eafc;
            margin-bottom: 10px;
            background: #f8fbff;
            box-shadow: 0 2px 8px rgba(52,152,219,0.10);
        }
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
            padding: 8px 16px;
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
    </style>
    <script>
    function showFileName() {
        var input = document.getElementById('foto');
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
    </script>
</head>
<body>
    <div class="box">
        <h2><i class="fa fa-user-circle"></i> Profil Staf</h2>
        <?= $notif ?>
        <?php if (!empty($profile_data['foto'])): ?>
            <img src="uploads/profile/<?= htmlspecialchars($profile_data['foto']) ?>" class="profile-pic" alt="Foto Profil">
        <?php else: ?>
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($data['username']) ?>&background=cfdef3&color=2980b9&size=90" class="profile-pic" alt="Foto Profil">
        <?php endif; ?>
        <form method="post" autocomplete="off" enctype="multipart/form-data">
            <label><i class="fa fa-user"></i> Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($data['username']) ?>" required>

            <label><i class="fa fa-key"></i> Password <span style="font-weight:400;color:#888;font-size:13px;">(Kosongkan jika tidak ingin mengubah)</span></label>
            <input type="password" name="password" placeholder="Password baru">

            <label><i class="fa fa-address-card"></i> Nama Lengkap</label>
            <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($profile_data['nama_lengkap'] ?? '') ?>">

            <label><i class="fa fa-envelope"></i> Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($profile_data['email'] ?? '') ?>">

            <label><i class="fa fa-phone"></i> No. HP</label>
            <input type="tel" name="no_hp" value="<?= htmlspecialchars($profile_data['no_hp'] ?? '') ?>">

            <label><i class="fa fa-map-marker-alt"></i> Alamat</label>
            <textarea name="alamat" rows="2"><?= htmlspecialchars($profile_data['alamat'] ?? '') ?></textarea>

            <label><i class="fa fa-image"></i> Foto Profil</label>
            <div class="custom-file">
                <input type="file" name="foto" id="foto" accept="image/*" onchange="showFileName()" />
                <label for="foto" id="file-label"><i class="fa fa-upload"></i> Pilih Foto</label>
                <span id="file-chosen" class="file-chosen">Tidak ada file dipilih</span>
            </div>

            <div class="info"><i class="fa fa-id-badge"></i> <b>ID User:</b> <?= $data['id'] ?></div>

            <button type="submit" name="update"><i class="fa fa-save"></i> Simpan Perubahan</button>
        </form>
        <a href="index.php" class="btn" style="margin-top:18px;"><i class="fa fa-arrow-left"></i> Kembali ke Dashboard</a>
    </div>
</body>
</html>