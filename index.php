<?php include 'header.php'; ?>

<div class="row align-items-center mb-5">
    <div class="col-md-7">
        <h1 class="display-4 fw-bold text-dark">Selamat Datang di<br><span class="text-utama">SM Sport Center</span></h1>
        <p class="lead text-secondary mt-3">Pusat olahraga modern dengan fasilitas lapangan futsal dan badminton berkualitas tinggi. Sistem reservasi online kami memudahkan Anda untuk mengecek jadwal dan memesan lapangan kapan saja.</p>
        <?php if(isset($_SESSION['nama'])): ?>
            <a href="reservasi.php" class="btn btn-utama btn-lg mt-3 shadow">Pesan Lapangan</a>
        <?php endif; ?>
    </div>
    <div class="col-md-5 d-none d-md-block text-center">
        <img src="images/logo.png" alt="Hero Image" class="img-fluid" style="max-height: 300px;" width="400" height="300" fetchpriority="high">
    </div>
</div>

<!-- Mengapa Memilih Kami Section -->
<div class="row text-center mt-5 mb-5">
    <div class="col-12 mb-4">
        <h2 class="fw-bold">Mengapa Memilih SM Sport Center?</h2>
        <div class="mx-auto bg-utama" style="width: 60px; height: 3px;"></div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="p-4 bg-white rounded shadow-sm h-100 border-top border-utama border-4">
            <div class="text-utama mb-3">
                <i class="bi bi-star-fill" style="font-size: 2.5rem;"></i>
            </div>
            <h4 class="fw-bold">Fasilitas Premium</h4>
            <p class="text-muted">Lapangan berstandar internasional dengan material berkualitas tinggi untuk kenyamanan dan keamanan saat berolahraga.</p>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="p-4 bg-white rounded shadow-sm h-100 border-top border-utama border-4">
            <div class="text-utama mb-3">
                <i class="bi bi-cash-coin" style="font-size: 2.5rem;"></i>
            </div>
            <h4 class="fw-bold">Harga Terjangkau</h4>
            <p class="text-muted">Nikmati fasilitas olahraga terbaik dengan harga yang bersahabat dan sistem pemesanan online yang sangat praktis.</p>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="p-4 bg-white rounded shadow-sm h-100 border-top border-utama border-4">
            <div class="text-utama mb-3">
                <i class="bi bi-geo-alt-fill" style="font-size: 2.5rem;"></i>
            </div>
            <h4 class="fw-bold">Lokasi Strategis</h4>
            <p class="text-muted">Terletak di area yang mudah dijangkau dengan fasilitas pendukung lengkap serta area parkir yang luas dan aman.</p>
        </div>
    </div>
</div>

<div class="row text-center mt-5">
    <div class="col-12 mb-4">
        <h2 class="fw-bold">Fasilitas Olahraga Kami</h2>
        <div class="mx-auto bg-utama" style="width: 60px; height: 3px;"></div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <img src="images/futsal.png" class="card-img-top" alt="Futsal" style="height: 250px; object-fit: cover;" width="600" height="250" loading="lazy">
            <div class="card-body">
                <h3 class="card-title text-utama fw-bold">Lapangan Futsal</h3>
                <p class="card-text text-muted">Tersedia 2 lapangan futsal premium dengan rumput sintetis standar internasional. Cocok untuk pertandingan dan latihan rutin.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <img src="images/badminton.png" class="card-img-top" alt="Badminton" style="height: 250px; object-fit: cover;" width="600" height="250" loading="lazy">
            <div class="card-body">
                <h3 class="card-title text-utama fw-bold">Lapangan Badminton</h3>
                <p class="card-text text-muted">Tersedia 3 lapangan badminton profesional dengan matras berkualitas. Dilengkapi dengan pencahayaan yang sangat baik.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
