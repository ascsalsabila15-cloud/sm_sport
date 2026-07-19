<!DOCTYPE html>
<html>
<head>
    <title>Login - SM Sport Center</title>
    <!-- Memanggil file style.css untuk mengatur tampilan/warna halaman -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="pelanggan-theme">
    <div class="navbar" style="display:flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center;">
            <img src="images/logo.png" alt="SM Sport Logo" class="logo-small" style="height: 50px; max-width: 100px; object-fit: contain;">
            <h2 style="margin: 0; margin-left: 15px;">SM Sport Center</h2>
        </div>
    </div>

    <div style="display: flex; flex-wrap: wrap; gap: 30px; max-width: 1000px; margin: 40px auto; padding: 0 20px;">
        <div class="news-section" style="flex: 2; min-width: 300px; align-self: flex-start;">
            <h2 style="margin-top:0;">Berita & Tentang SM Sport</h2>
            <p>SM Sport Center adalah pusat olahraga modern yang menyediakan lapangan futsal dan badminton berkualitas tinggi. Dengan fasilitas lengkap, rumput sintetis standar internasional, dan matras badminton profesional, kami siap memberikan pengalaman olahraga terbaik untuk Anda dan tim.</p>
            
            <div class="court-images">
                <div class="court-card">
                    <img src="images/futsal.png" alt="Lapangan Futsal">
                    <h4>Lapangan Futsal Premium</h4>
                </div>
                <div class="court-card">
                    <img src="images/badminton.png" alt="Lapangan Badminton">
                    <h4>Lapangan Badminton Pro</h4>
                </div>
            </div>
        </div>

        <div class="login-container" style="flex: 1; margin: 0; min-width: 250px; max-width: 300px; height: fit-content; align-self: flex-start; padding: 20px; text-align: center;">
            <h2 style="margin-top:0; font-size: 18px;">Selamat Datang!</h2>
            <p style="font-size: 14px; margin-bottom: 20px;">Silakan login atau daftar untuk mulai memesan lapangan.</p>
            
            <a href="login.php" class="btn btn-primary" style="display: block; width: 100%; box-sizing:border-box; margin-bottom: 10px; padding: 10px; text-decoration: none;">Login Sistem</a>
            
            <a href="register.php" class="btn btn-success" style="display: block; width: 100%; box-sizing:border-box; padding: 10px; text-decoration: none;">Daftar Akun Baru</a>
            
            <p style="margin-top: 20px; font-size: 11px; color: #666;">
                (Admin Default: admin@smsport.com / admin123)
            </p>
        </div>
    </div>

    <footer style="text-align: center; padding: 20px; font-size: 13px; color: #fff; margin-top: auto; background: #0f5a43; border-top: 2px solid #0a4231;">
        <p style="margin: 0;">Hubungi Kami: <strong>081210214026</strong> | Lokasi: <strong>Jl Raya Bogor, Kota Depok</strong></p>
    </footer>
</body>
</html>
