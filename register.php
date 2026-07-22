<?php include 'header.php'; ?>

<!-- Tampilan Form Registrasi Pelanggan -->
<div class="row justify-content-center mt-5 mb-5">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h3 class="text-center text-utama fw-bold mb-4">Pendaftaran Akun</h3>
                
                <form action="register_process.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label text-muted">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan email aktif" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">No. Handphone (WA)</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="Misal: 08123456789" required maxlength="13" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Buat password" required>
                    </div>
                    
                    <button type="submit" name="register" class="btn btn-utama w-100 fw-bold py-2">Daftar Sekarang</button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="text-muted mb-2">Sudah punya akun?</p>
                    <a href="login.php" class="btn btn-outline-utama w-100">Masuk di sini</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
