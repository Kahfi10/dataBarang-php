<?php
session_start();
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "form");
if (!$conn) die("Koneksi gagal: " . mysqli_connect_error());

$error = '';
$success = '';

// Proses Login
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $result = mysqli_query($conn, "SELECT * FROM user WHERE username='$username' LIMIT 1");
    $user = mysqli_fetch_assoc($result);

    if ($user && $password === $user['password']) { // Untuk produksi, gunakan password_verify
        $_SESSION['login'] = true;
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}

// Proses Sign Up
if (isset($_POST['signup'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if ($password !== $password2) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        $cek = mysqli_query($conn, "SELECT * FROM user WHERE username='$username'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Username sudah terdaftar!";
        } else {
            $query = mysqli_query($conn, "INSERT INTO user (username, password) VALUES ('$username', '$password')");
            if ($query) {
                $success = "Registrasi berhasil! Silakan login.";
            } else {
                $error = "Registrasi gagal!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login & Sign Up Staf</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #fff;
            max-width: 370px;
            width: 100%;
            margin: 40px auto;
            padding: 38px 32px 28px 32px;
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
            margin-bottom: 24px;
            color: #2980b9;
            letter-spacing: 1px;
            font-size: 26px;
        }
        label {
            color: #202020;
            display: block;
            margin-top: 16px;
            font-size: 15px;
            font-weight: 500;
        }
        input[type="text"], input[type="password"] {
            width: 85%;
            padding: 10px 12px;
            margin-top: 6px;
            border: 1px solid #b3c6e0;
            border-radius: 5px;
            font-size: 15px;
            background: #f8fbff;
            transition: border 0.2s;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border: 1.5px solid #3498db;
            outline: none;
            background: #f0f8ff;
        }
        button {
            font-size: 16px;
            font-weight: bold;
            margin-top: 26px;
            padding: 12px 0;
            background: linear-gradient(90deg, #3498db 60%, #6dd5fa 100%);
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            box-shadow: 0 2px 8px rgba(52,152,219,0.08);
            transition: background 0.2s;
        }
        button:hover {
            background: linear-gradient(90deg, #217dbb 60%, #3498db 100%);
        }
        .error {
            color: #e74c3c;
            text-align: center;
            margin-top: 12px;
            font-size: 15px;
        }
        .success {
            color: #27ae60;
            text-align: center;
            margin-top: 12px;
            font-size: 15px;
        }
        .switch-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #3498db;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }
        .switch-link:hover {
            color: #217dbb;
            text-decoration: underline;
        }
        .hidden { display: none; }
        .icon-input {
            position: relative;
        }
        .icon-input i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #b3c6e0;
            font-size: 16px;
        }
        .icon-input input {
            padding-left: 34px;
        }
    </style>
    <script>
    function showForm(form) {
        document.getElementById('login-form').classList.add('hidden');
        document.getElementById('signup-form').classList.add('hidden');
        document.getElementById(form).classList.remove('hidden');
    }
    </script>
</head>
<body>
    <div class="login-box">
        <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <?php if ($success): ?><div class="success"><?= $success ?></div><?php endif; ?>

        <!-- Login Form -->
        <form id="login-form" method="post" autocomplete="off" <?= isset($_POST['signup']) ? 'class="hidden"' : '' ?>>
            <h2><i class="fa fa-sign-in-alt"></i> Login Staf</h2>
            <div class="icon-input">
                <i class="fa fa-user"></i>
                <input type="text" name="username" placeholder="Username" required autofocus>
            </div>
            <div class="icon-input">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" name="login"><i class="fa fa-sign-in-alt"></i> Login</button>
            <span class="switch-link" onclick="showForm('signup-form')">Belum punya akun? Daftar di sini</span>
        </form>

        <!-- Sign Up Form -->
        <form id="signup-form" method="post" autocomplete="off" <?= isset($_POST['signup']) ? '' : 'class="hidden"' ?>>
            <h2><i class="fa fa-user-plus"></i> Sign Up Staf</h2>
            <div class="icon-input">
                <i class="fa fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="icon-input">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="icon-input">
                <i class="fa fa-lock"></i>
                <input type="password" name="password2" placeholder="Konfirmasi Password" required>
            </div>
            <button type="submit" name="signup"><i class="fa fa-user-plus"></i> Sign Up</button>
            <span class="switch-link" onclick="showForm('login-form')">Sudah punya akun? Login di sini</span>
        </form>
    </div>
</body>
</html>