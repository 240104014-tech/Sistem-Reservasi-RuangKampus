<?php
// SiRuang Kampus - Sistem Informasi Reservasi & Penjadwalan Ruang Diskusi & Rapat Ormawa
// Proyek Mandiri Mata Kuliah Rekayasa Perangkat Lunak (RPL) - D3 Teknik Komputer UDB

$nama_mahasiswa = "Prihatin Puji Ayu Lestari";
$nim = "240104014";
$kelas = "D3 - Teknik Komputer 24A1";
$kampus = "Universitas Duta Bangsa Surakarta";
$tahun = "2026";

$hasil_validasi = null;

// Ambil input POST atau gunakan data sampel default untuk pengujian awal
$ormawa = htmlspecialchars(trim($_POST["ormawa"] ?? "HMP Teknik Komputer"));
$ruangan = htmlspecialchars(trim($_POST["ruangan"] ?? "Ruang Rapat Ormawa Gedung A (Kapasitas 25 Orang)"));
$tanggal = htmlspecialchars(trim($_POST["tanggal"] ?? date("Y-m-d")));
$jam_mulai = htmlspecialchars(trim($_POST["jam_mulai"] ?? "09:00"));
$jam_selesai = htmlspecialchars(trim($_POST["jam_selesai"] ?? "11:30"));
$keperluan = htmlspecialchars(trim($_POST["keperluan"] ?? "Rapat Koordinasi Persiapan Workshop Jaringan Komputer 2026"));
$peserta = intval($_POST["peserta"] ?? 15);

// Evaluasi Logika Sistem
if (!empty($ormawa) && !empty($ruangan) && !empty($tanggal) && !empty($jam_mulai) && !empty($jam_selesai)) {
    $start_time = strtotime("$tanggal $jam_mulai");
    $end_time = strtotime("$tanggal $jam_selesai");

    if ($end_time <= $start_time) {
        $hasil_validasi = [
            "status" => "danger",
            "pesan" => "Waktu tidak valid! Jam selesai harus lebih lambat dari jam mulai peminjaman.",
        ];
    } else {
        $durasi_detik = $end_time - $start_time;
        $durasi_jam = round($durasi_detik / 3600, 2);

        $jam_buka = strtotime("$tanggal 08:00");
        $jam_tutup = strtotime("$tanggal 17:00");

        if ($start_time < $jam_buka || $end_time > $jam_tutup) {
            $hasil_validasi = [
                "status" => "warning",
                "pesan" => "Peminjaman ditolak! Di luar jam operasional fasilitas kampus (08:00 - 17:00 WIB).",
                "durasi" => $durasi_jam . " jam"
            ];
        } elseif ($durasi_jam > 3.0) {
            $hasil_validasi = [
                "status" => "warning",
                "pesan" => "Peminjaman ditolak! Durasi ($durasi_jam jam) melebihi batas maksimal regulasi kampus (maks. 3 jam per sesi).",
                "durasi" => $durasi_jam . " jam"
            ];
        } else {
            $kode_booking = "SRK-" . date("Ymd", $start_time) . "-0414";
            $hasil_validasi = [
                "status" => "success",
                "pesan" => "Lolos Verifikasi Sistem! Jadwal tersedia dan memenuhi regulasi penggunaan ruangan kampus.",
                "kode" => $kode_booking,
                "ormawa" => $ormawa,
                "ruangan" => $ruangan,
                "tanggal" => date("d F Y", $start_time),
                "jam" => "$jam_mulai - $jam_selesai WIB",
                "durasi" => $durasi_jam . " jam",
                "peserta" => $peserta . " orang",
                "keperluan" => $keperluan
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiRuang Kampus - UTS Rekayasa Perangkat Lunak</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #f8fafc; color: #1e293b; padding: 24px; line-height: 1.5; }
        .container { max-width: 900px; margin: 0 auto; }
        .header-card { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; padding: 28px; border-radius: 16px; margin-bottom: 24px; box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.3); }
        .badge { display: inline-block; background: rgba(255, 255, 255, 0.2); padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
        .header-card h1 { font-size: 24px; font-weight: 800; margin-bottom: 8px; }
        .header-card p { font-size: 14px; opacity: 0.9; }
        .meta-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-top: 20px; padding-top: 16px; border-top: 1px solid rgba(255, 255, 255, 0.2); font-size: 13px; }
        .meta-item strong { display: block; opacity: 0.8; font-size: 11px; text-transform: uppercase; }
        .content-card { background: white; padding: 28px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin-bottom: 24px; }
        .section-title { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 4px; display: flex; align-items: center; gap: 8px; }
        .section-subtitle { font-size: 13px; color: #64748b; margin-bottom: 20px; }
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-size: 12px; font-weight: 700; color: #334155; }
        .form-group input, .form-group select, .form-group textarea { padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; outline: none; transition: all 0.2s; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); }
        .btn-submit { background: #2563eb; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; transition: background 0.2s; width: 100%; margin-top: 8px; }
        .btn-submit:hover { background: #1d4ed8; }
        .alert { padding: 16px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; }
        .alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
        .alert-warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
        .alert-danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .result-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-top: 12px; }
        .result-item { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #e2e8f0; font-size: 13px; }
        .result-item:last-child { border-bottom: none; }
        .result-label { color: #64748b; font-weight: 500; }
        .result-val { color: #0f172a; font-weight: 700; }
        .rule-card { background: #f1f5f9; padding: 16px; border-radius: 10px; margin-top: 20px; font-size: 12px; color: #475569; }
        .rule-card strong { color: #1e293b; }
        .footer { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 32px; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Info Proyek -->
        <div class="header-card">
            <span class="badge">Ujian Tengah Semester • Rekayasa Perangkat Lunak</span>
            <h1>SiRuang Kampus — Reservasi & Penjadwalan Ruang Ormawa</h1>
            <p>Sistem Pengelolaan Peminjaman Ruang Rapat Ormawa & Ruang Diskusi Kampus untuk Mencegah Bentrok Jadwal</p>
            <div class="meta-grid">
                <div class="meta-item">
                    <strong>Nama Mahasiswa</strong>
                    <?= htmlspecialchars($nama_mahasiswa) ?>
                </div>
                <div class="meta-item">
                    <strong>NIM</strong>
                    <?= htmlspecialchars($nim) ?>
                </div>
                <div class="meta-item">
                    <strong>Kelas / Semester</strong>
                    <?= htmlspecialchars($kelas) ?>
                </div>
                <div class="meta-item">
                    <strong>Perguruan Tinggi</strong>
                    <?= htmlspecialchars($kampus) ?> (<?= htmlspecialchars($tahun) ?>)
                </div>
            </div>
        </div>

        <!-- Section Logika PHP: Form Reservasi -->
        <div class="content-card">
            <h2 class="section-title">⚡ Simulasi Logika Sistem: Validasi Regulasi Reservasi Ruangan</h2>
            <p class="section-subtitle">Pengujian alur bisnis peminjaman: validasi jam operasional (08:00 - 17:00 WIB) & batas durasi pemakaian (maks. 3 jam).</p>

            <?php if ($hasil_validasi): ?>
                <?php if ($hasil_validasi["status"] === "success"): ?>
                    <div class="alert alert-success">
                        <strong>✔ Berhasil:</strong> <?= $hasil_validasi["pesan"] ?>
                        <div class="result-box">
                            <div class="result-item">
                                <span class="result-label">Kode Reservasi:</span>
                                <span class="result-val" style="color: #2563eb;"><?= $hasil_validasi["kode"] ?></span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Organisasi / Pemohon:</span>
                                <span class="result-val"><?= $hasil_validasi["ormawa"] ?></span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Ruangan:</span>
                                <span class="result-val"><?= $hasil_validasi["ruangan"] ?></span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Waktu Peminjaman:</span>
                                <span class="result-val"><?= $hasil_validasi["tanggal"] ?> (<?= $hasil_validasi["jam"] ?>)</span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Durasi Terhitung:</span>
                                <span class="result-val"><?= $hasil_validasi["durasi"] ?></span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Estimasi Peserta:</span>
                                <span class="result-val"><?= $hasil_validasi["peserta"] ?></span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Agenda Kegiatan:</span>
                                <span class="result-val"><?= $hasil_validasi["keperluan"] ?></span>
                            </div>
                            <div class="result-item">
                                <span class="result-label">Status Sistem:</span>
                                <span class="result-val" style="color: #059669;">JADWAL DIBOOKING (Menunggu Persetujuan Admin)</span>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-<?= $hasil_validasi["status"] ?>">
                        <strong>✕ Validasi Gagal:</strong> <?= $hasil_validasi["pesan"] ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="ormawa">Nama Organisasi / Kelompok Pemohon:</label>
                        <input type="text" id="ormawa" name="ormawa" placeholder="Contoh: HMP Teknik Komputer / Kelompok RPL" value="<?= htmlspecialchars($_POST['ormawa'] ?? 'HMP Teknik Komputer') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="ruangan">Pilih Ruangan Kampus:</label>
                        <select id="ruangan" name="ruangan" required>
                            <option value="Ruang Rapat Ormawa Gedung A (Kapasitas 25 Orang)">Ruang Rapat Ormawa Gedung A (Kapasitas 25 Orang)</option>
                            <option value="Ruang Diskusi Lab Komputer 1 (Kapasitas 15 Orang)">Ruang Diskusi Lab Komputer 1 (Kapasitas 15 Orang)</option>
                            <option value="Co-Working Space Kemahasiswaan (Kapasitas 30 Orang)">Co-Working Space Kemahasiswaan (Kapasitas 30 Orang)</option>
                            <option value="Ruang Multimedia Gedung B (Kapasitas 20 Orang)">Ruang Multimedia Gedung B (Kapasitas 20 Orang)</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="tanggal">Tanggal Peminjaman:</label>
                        <input type="date" id="tanggal" name="tanggal" value="<?= htmlspecialchars($_POST['tanggal'] ?? date('Y-m-d')) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="jam_mulai">Jam Mulai:</label>
                        <input type="time" id="jam_mulai" name="jam_mulai" value="<?= htmlspecialchars($_POST['jam_mulai'] ?? '09:00') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="jam_selesai">Jam Selesai:</label>
                        <input type="time" id="jam_selesai" name="jam_selesai" value="<?= htmlspecialchars($_POST['jam_selesai'] ?? '11:30') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="peserta">Perkiraan Jumlah Peserta:</label>
                        <input type="number" id="peserta" name="peserta" min="1" max="50" value="<?= htmlspecialchars($_POST['peserta'] ?? '12') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="keperluan">Tujuan / Agenda Rapat:</label>
                    <textarea id="keperluan" name="keperluan" rows="2" placeholder="Tuliskan agenda rapat secara singkat..." required><?= htmlspecialchars($_POST['keperluan'] ?? 'Rapat Koordinasi Persiapan Workshop Jaringan Komputer 2026') ?></textarea>
                </div>

                <button type="submit" class="btn-submit">Uji Validasi Logika Reservasi</button>
            </form>

            <div class="rule-card">
                <strong>📌 Aturan Bisnis Sistem (Business Rules):</strong><br>
                1. Jam selesai harus lebih besar dari jam mulai.<br>
                2. Jam operasional fasilitas ruangan kampus adalah pukul <strong>08:00 – 17:00 WIB</strong>.<br>
                3. Batas durasi peminjaman per sesi maksimal <strong>3 jam</strong> untuk mencegah monopoli ruangan.<br>
                4. Jika lolos aturan, sistem menerbitkan kode booking unik dan mengunci slot waktu.
            </div>
        </div>

        <div class="footer">
            © <?= date('Y') ?> SiRuang Kampus • Lembar Jawaban UTS Rekayasa Perangkat Lunak • Universitas Duta Bangsa Surakarta
        </div>
    </div>
</body>
</html>
