<!DOCTYPE html>
<html>
<head>
    <title>Login - SM Sport Center</title>
    <!-- Memanggil file style.css untuk mengatur tampilan/warna halaman -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="pelanggan-theme">
    <div class="login-container">
        <!-- Heading atau Judul Halaman -->
        <h2 style="text-align: center; margin-top:0;">Login Sistem Reservasi</h2>
        
        <!-- Form HTML. Data akan dikirim ke login_process.php menggunakan metode POST yang aman -->
        <form action="login_process.php" method="POST">
            <!-- Input Box untuk mengisi Email -->
            <label>Email:</label>
            <input type="email" name="email" placeholder="Masukkan email" required>
            
            <!-- Input Box untuk mengisi Password (otomatis tersensor) -->
            <label>Password:</label>
            <input type="password" name="password" placeholder="Masukkan password" required>
            
            <!-- Tombol Submit untuk mengirim seluruh data di atas -->
            <button type="submit" name="login" class="btn btn-primary" style="width: 100%;">Masuk</button>
        </form>
        
        <div style="text-align: center; margin-top: 15px;">
            <p style="margin-bottom: 5px; font-size: 14px;">Belum punya akun?</p>
            <!-- Tombol Navigasi Hyperlink menuju halaman Pendaftaran -->
            <a href="register.php" class="btn btn-success" style="width: 100%; box-sizing:border-box;">Daftar Akun Baru</a>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <a href="index.php" style="font-size: 14px; text-decoration: none;">&larr; Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
