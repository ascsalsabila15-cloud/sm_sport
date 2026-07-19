<?php include 'header.php'; ?>

<div class="row justify-content-center mt-5 mb-5">
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h3 class="text-center text-red fw-bold mb-4">Login Reservasi</h3>
                
                <form action="login_process.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label text-muted">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-danger w-100 fw-bold py-2">Masuk</button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="text-muted mb-2">Belum punya akun?</p>
                    <a href="register.php" class="btn btn-outline-danger w-100">Daftar Akun Baru</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
