<?php 
session_start();
require 'koneksi.php';

// PROSES LOGOUT
if(isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

// PROSES LOGIN
if(isset($_POST['login'])) {
    // Sanitisasi dan penangkapan input form
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Eksekusi query menggunakan Prepared Statement untuk mencegah SQL Injection
    $stmt = $conn->prepare("SELECT * FROM pelanggan WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if($user && password_verify($password, $user['password'])) {
        // Set session variables setelah autentikasi berhasil
        $_SESSION['id_pelanggan'] = $user['id_pelanggan'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];

        header("Location: dashboard.php");
        exit;
    } else {
        // Penanganan jika autentikasi gagal
        echo "<script>alert('Opps, Email atau password salah!'); window.location='login.php';</script>";
        exit;
    }
}

include 'header.php'; 
?>

<div class="row justify-content-center mt-5 mb-5">
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h3 class="text-center text-utama fw-bold mb-4">Login Reservasi</h3>
                
                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label text-muted">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-utama w-100 fw-bold py-2">Masuk</button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="text-muted mb-2">Belum punya akun?</p>
                    <a href="register.php" class="btn btn-outline-utama w-100">Daftar Akun Baru</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
