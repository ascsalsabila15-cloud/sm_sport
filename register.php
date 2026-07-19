<!DOCTYPE html>
<html>
<head>
    <title>Pendaftaran Akun Baru</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2 style="text-align: center;">Daftar Akun Pelanggan</h2>
        <form action="register_process.php" method="POST">
            <label>Nama Lengkap:</label>
            <input type="text" name="nama" placeholder="Masukkan nama" required>
            
            <label>No HP:</label>
            <input type="text" name="no_hp" placeholder="Masukkan nomor HP" required>

            <label>Email:</label>
            <input type="email" name="email" placeholder="Masukkan email" required>
            
            <label>Password:</label>
            <input type="password" name="password" placeholder="Buat password" required>
            
            <button type="submit" name="register" class="btn btn-primary" style="width: 100%;">Daftar Sekarang</button>
        </form>
        <p style="text-align: center; margin-top: 15px;">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </p>
    </div>
</body>
</html>
