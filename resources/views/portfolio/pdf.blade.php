<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Portofolio Proyek - SIMRS Medika MVP</title>
    <style>
        @page {
            margin: 28px 32px 35px 32px;
            footer: page-footer;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.5;
            background: #ffffff;
        }
        .header {
            border-bottom: 2.5px solid #0d9488;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header table {
            width: 100%;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 11px;
            color: #0d9488;
            font-weight: 600;
            margin-top: 2px;
            margin-bottom: 0;
        }
        .meta-text {
            font-size: 9px;
            color: #64748b;
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 3px 7px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            margin-right: 4px;
            margin-bottom: 4px;
        }
        .badge-teal { background: #ccfbf1; color: #0f766e; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-purple { background: #f3e8ff; color: #7e22ce; }
        .badge-emerald { background: #d1fae5; color: #047857; }
        .badge-dark { background: #0f172a; color: #ffffff; }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            border-left: 3.5px solid #0d9488;
            padding-left: 7px;
            margin-top: 14px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        p {
            margin: 0 0 8px 0;
            text-align: justify;
        }
        .card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 9px 12px;
            margin-bottom: 10px;
        }
        .grid-2 {
            width: 100%;
            margin-bottom: 6px;
        }
        .grid-2 td {
            width: 50%;
            vertical-align: top;
            padding: 0 5px;
        }
        .grid-2 td:first-child { padding-left: 0; }
        .grid-2 td:last-child { padding-right: 0; }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 10px;
        }
        table.data-table th {
            background: #f1f5f9;
            color: #334155;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        table.data-table td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            font-size: 9.5px;
        }
        .workflow-box {
            background: #f0fdfa;
            border: 1px solid #99f6e4;
            border-radius: 6px;
            padding: 8px;
            text-align: center;
            font-size: 9.5px;
            font-weight: bold;
            color: #115e59;
        }
        .arrow {
            text-align: center;
            font-size: 14px;
            color: #0d9488;
            font-weight: bold;
        }
        .highlight {
            font-weight: bold;
            color: #0f766e;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            text-align: center;
        }
        .code-snippet {
            font-family: 'Courier New', Courier, monospace;
            background: #0f172a;
            color: #38bdf8;
            padding: 6px 8px;
            border-radius: 4px;
            font-size: 8.5px;
            line-height: 1.3;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header">
        <table>
            <tr>
                <td style="width: 70%;">
                    <h1 class="title">SIMRS Medika</h1>
                    <p class="subtitle">Sistem Informasi Manajemen Rumah Sakit Terpadu (MVP)</p>
                    <div style="margin-top: 6px;">
                        <span class="badge badge-teal">Laravel 12.x</span>
                        <span class="badge badge-blue">Tailwind CSS 4</span>
                        <span class="badge badge-purple">Alpine.js</span>
                        <span class="badge badge-emerald">Spatie RBAC</span>
                        <span class="badge badge-dark">Google OAuth 2.0</span>
                    </div>
                </td>
                <td style="width: 30%; text-align: right; vertical-align: top;">
                    <div class="meta-text">
                        <strong>Project Portfolio Report</strong><br>
                        Faskes Technology Standard<br>
                        Status: <strong>Production Ready (MVP)</strong><br>
                        Tgl: {{ now()->translatedFormat('d F Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- 1. Executive Summary -->
    <div class="section-title">1. Ringkasan Eksekutif & Latar Belakang Proyek</div>
    <p>
        <strong>SIMRS Medika</strong> adalah aplikasi web sistem informasi manajemen rumah sakit skala MVP yang dibangun dengan standar industri fasilitas pelayanan kesehatan (*healthcare standard*). Proyek ini memecahkan permasalahan silo data medis dengan menghubungkan seluruh siklus operasional rawat jalan mulai dari pendaftaran pasien, pencatatan rekam medis elektronik oleh dokter spesialis, dispensing farmasi berkeamanan konkurensi tinggi, hingga pembayaran billing kasir.
    </p>

    <!-- 2. Core Business Workflow -->
    <div class="section-title">2. Alur Kerja Bisnis (Core Healthcare Workflow)</div>
    <p>Alur operasional pasien dimodelkan melalui mesin status (*state machine*) terintegrasi:</p>
    
    <table style="width: 100%; border-collapse: separate; border-spacing: 4px; margin-bottom: 10px;">
        <tr>
            <td style="width: 22%;">
                <div class="workflow-box">
                    <strong>1. FRONT OFFICE</strong><br>
                    Registrasi Pasien & Poli<br>
                    <small><i>status: registered</i></small>
                </div>
            </td>
            <td style="width: 4%;" class="arrow">&rarr;</td>
            <td style="width: 22%;">
                <div class="workflow-box">
                    <strong>2. DOKTER POLI</strong><br>
                    Pemeriksaan RME & Resep<br>
                    <small><i>status: in_consultation</i></small>
                </div>
            </td>
            <td style="width: 4%;" class="arrow">&rarr;</td>
            <td style="width: 22%;">
                <div class="workflow-box">
                    <strong>3. INSTALASI FARMASI</strong><br>
                    Validasi & Potong Stok<br>
                    <small><i>status: waiting_pharmacy</i></small>
                </div>
            </td>
            <td style="width: 4%;" class="arrow">&rarr;</td>
            <td style="width: 22%;">
                <div class="workflow-box">
                    <strong>4. KASIR & BILLING</strong><br>
                    Bayar Tunai & Kwitansi<br>
                    <small><i>status: waiting_payment</i></small>
                </div>
            </td>
        </tr>
    </table>

    <!-- 3. Key Modules Breakdown -->
    <div class="section-title">3. Modul Aplikasi & Fitur Utama</div>

    <table class="grid-2">
        <tr>
            <td>
                <div class="card">
                    <strong style="color: #0f766e; font-size: 10.5px;">A. Front Office & Pasien</strong>
                    <ul style="margin: 4px 0 0 15px; padding: 0; font-size: 9px;">
                        <li><strong>Standarisasi No. RM</strong>: Generator otomatis format unik seumur hidup <code>RM-YYYYMM-XXXX</code>.</li>
                        <li><strong>Validasi NIK 16 Digit</strong>: Kesiapan data identitas kependudukan terindeks.</li>
                        <li><strong>Pendaftaran Antrean</strong>: Penjadwalan ke poli & alokasi nomor antrean harian terurut.</li>
                    </ul>
                </div>
            </td>
            <td>
                <div class="card">
                    <strong style="color: #1d4ed8; font-size: 10.5px;">B. Dokter Spesialis (RME)</strong>
                    <ul style="margin: 4px 0 0 15px; padding: 0; font-size: 9px;">
                        <li><strong>Rekam Medis Elektronik</strong>: Tanda vital (tensi, nadi, suhu), anamnesis, pemeriksaan fisik.</li>
                        <li><strong>Standar Diagnosa ICD-10</strong>: Kode medis baku internasional (Permenkes 24/2022).</li>
                        <li><strong>Alpine.js E-Prescription</strong>: Builder peresepan multi-item dinamis dan penguncian rekam medis permanen (<code>finalized_at</code>).</li>
                    </ul>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="card">
                    <strong style="color: #047857; font-size: 10.5px;">C. Instalasi Farmasi & Gudang Obat</strong>
                    <ul style="margin: 4px 0 0 15px; padding: 0; font-size: 9px;">
                        <li><strong>Antrean Resep Real-Time</strong>: Verifikasi takaran obat dan aturan pakai (signa).</li>
                        <li><strong>Atomic Stock Deduction</strong>: Pemotongan stok obat terproteksi transaksi database ACID.</li>
                        <li><strong>Peringatan Stok Kritis</strong>: Monitoring inventori obat di bawah batas aman (&lt; 50 unit).</li>
                    </ul>
                </div>
            </td>
            <td>
                <div class="card">
                    <strong style="color: #b45309; font-size: 10.5px;">D. Kasir Pembayaran & Billing</strong>
                    <ul style="margin: 4px 0 0 15px; padding: 0; font-size: 9px;">
                        <li><strong>Agregasi Tagihan Otomatis</strong>: Total biaya konsultasi dokter + total harga resep obat.</li>
                        <li><strong>Kalkulator Kasir Tunai</strong>: Perhitungan instan nominal pembayaran dan uang kembalian.</li>
                        <li><strong>Kwitansi Resmi Lunas</strong>: Dokumen nota pembayaran siap cetak / PDF.</li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>

    <!-- Page Break for Clean Layout -->
    <div class="page-break"></div>

    <!-- 4. Engineering & Security Highlights -->
    <div class="section-title">4. Keunggulan Rekayasa Perangkat Lunak & Keamanan</div>

    <div class="card" style="margin-bottom: 8px;">
        <strong style="color: #0f172a;">1. Concurrency Safety: Pessimistic Locking pada Pemotongan Stok Obat</strong>
        <p style="margin-top: 3px; font-size: 9px;">
            Mencegah terjadinya <em>race condition</em> atau stok minus saat terjadi lonjakan transaksi farmasi bersamaan. Sistem menggunakan <code>lockForUpdate()</code> di dalam <code>DB::transaction</code>:
        </p>
        <div class="code-snippet">
$medicine = Medicine::where('id', $item->medicine_id)->lockForUpdate()->firstOrFail();<br>
if ($medicine->stock < $item->quantity) {<br>
&nbsp;&nbsp;&nbsp;&nbsp;throw new InsufficientStockException("Stok {$medicine->name} tidak mencukupi.");<br>
}<br>
$medicine->decrement('stock', $item->quantity);
        </div>
    </div>

    <div class="card" style="margin-bottom: 8px;">
        <strong style="color: #0f172a;">2. Dual Authentication dengan Enterprise Whitelist Google OAuth 2.0</strong>
        <p style="margin-top: 3px; font-size: 9px;">
            Berbeda dengan aplikasi publik, sistem rumah sakit memblokir registrasi akun Google liar. Hanya staf yang alamat emailnya telah didaftarkan terlebih dahulu oleh Administrator pada modul RBAC yang diizinkan masuk ke sistem data medis.
        </p>
    </div>

    <div class="card" style="margin-bottom: 12px;">
        <strong style="color: #0f172a;">3. Pengiriman Email Transaksional via Gmail SMTP</strong>
        <p style="margin-top: 3px; font-size: 9px;">
            Fitur reset kata sandi terintegrasi langsung dengan server SMTP Google menggunakan token keamanan bertenggat waktu 60 menit dan dilengkapi sistem <em>anti-spam rate limiter</em> (throttling 1 menit).
        </p>
    </div>

    <!-- 5. Database Architecture -->
    <div class="section-title">5. Skema Entitas Relasional Database</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Tabel</th>
                <th style="width: 35%;">Kolom Kunci & Tipe Data</th>
                <th style="width: 40%;">Relasi & Integritas Data</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>users & roles</strong></td>
                <td>id, name, email, google_id, password, is_active</td>
                <td>Spatie RBAC (Admin, Doctor, Pharmacist, Cashier).</td>
            </tr>
            <tr>
                <td><strong>polyclinics & doctors</strong></td>
                <td>id, name, room_number, sip_number, fee</td>
                <td>Doctor berelasi 1-to-1 ke User dan N-to-1 ke Polyclinic.</td>
            </tr>
            <tr>
                <td><strong>patients</strong></td>
                <td>id, medical_record_number (UK), nik (UK), name</td>
                <td>Nomor RM & NIK diindeks unik dengan fitur Soft Deletes.</td>
            </tr>
            <tr>
                <td><strong>registrations</strong></td>
                <td>id, registration_number, queue_number, status</td>
                <td>Foreign key ke patient, doctor, polyclinic. Pengendali alur status.</td>
            </tr>
            <tr>
                <td><strong>medical_records</strong></td>
                <td>id, vital_signs (JSON), icd10_code, finalized_at</td>
                <td>Relasi 1-to-1 dengan registration. Terkunci saat finalized_at terisi.</td>
            </tr>
            <tr>
                <td><strong>medicines & prescriptions</strong></td>
                <td>id, stock, price, quantity, dosage_instructions</td>
                <td>Relasi prescription_items mengunci harga obat saat transaksi.</td>
            </tr>
            <tr>
                <td><strong>invoices & payments</strong></td>
                <td>id, total_amount, amount_paid, change_amount</td>
                <td>Agregasi biaya konsultasi + total resep dengan audit kasir.</td>
            </tr>
        </tbody>
    </table>

    <!-- 6. Automated Testing Verification -->
    <div class="section-title">6. Verifikasi Pengujian Otomatis (Automated Testing)</div>
    <p>
        Aplikasi telah dilengkapi dengan rangkaian tes unit dan fitur menyeluruh menggunakan PHPUnit/Pest untuk menjamin kestabilan kode saat dijalankan:
    </p>

    <div class="card" style="background: #ecfdf5; border-color: #a7f3d0;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 60%;">
                    <strong style="color: #065f46; font-size: 11px;">Status Test Suite: 100% PASSING</strong><br>
                    <span style="font-size: 9.5px; color: #047857;">Total: <strong>14 Test Cases</strong> &bull; <strong>77 Assertions</strong> &bull; Durasi: <strong>~1.3 detik</strong></span>
                </td>
                <td style="width: 40%; text-align: right;">
                    <span class="badge badge-emerald" style="font-size: 10px; padding: 4px 8px;">ALL TESTS GREEN</span>
                </td>
            </tr>
        </table>
        <ul style="margin: 6px 0 0 15px; padding: 0; font-size: 8.5px; color: #065f46;">
            <li><code>SimrsFullHttpWorkflowTest</code>: Menguji alur penuh (Pendaftaran Pasien &rarr; Dokter RME &rarr; Farmasi &rarr; Kasir).</li>
            <li><code>SimrsBusinessLogicTest</code>: Menguji pemotongan stok terkunci & penolakan saat stok tidak mencukupi.</li>
            <li><code>AuthenticationTest</code>: Menguji sesi login, proteksi hak akses, dan multi-role dashboard.</li>
        </ul>
    </div>

    <!-- Footer -->
    <div class="footer">
        SIMRS Medika MVP &bull; Technical Portfolio Report &bull; Built with Laravel 12, Tailwind CSS & Spatie RBAC &bull; Halaman <span class="pagenum"></span>
    </div>

</body>
</html>
