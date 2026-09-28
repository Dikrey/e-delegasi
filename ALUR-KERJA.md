# Alur Kerja Aplikasi E-Delegasi

Dokumen ini menjelaskan alur kerja (workflow) aplikasi dari sudut pandang pengguna:
mulai dari login, pengelolaan surat, disposisi, delegasi tugas, agenda, hingga laporan.

---

## 1. Login & Peran Pengguna

1. Buka halaman login aplikasi.
2. Masuk dengan **email** dan **kata sandi** (akun dibuat oleh administrator).
3. Aplikasi menyediakan beberapa peran, dan menu yang tampil disesuaikan dengan peran:
   - **Admin** — akses penuh: mengelola surat, pengguna, master data, pengaturan sistem, laporan.
   - **Sekretaris** — mengelola surat masuk, disposisi, delegasi/tugas, agenda pimpinan, memverifikasi hasil kerja (review).
   - **Staff** — menerima/menolak tugas, memperbarui progres, mengunggah bukti pengerjaan, melihat tugas yang diarahkan kepadanya.

> Setelah login, pastikan nama dan profil Anda benar. Jika lupa kata sandi, hubungi admin
> untuk mereset (admin dapat mengatur ulang kata sandi dari menu **Pengguna**).

---

## 2. Pengelolaan Surat Masuk

1. Buka menu **Surat → Surat Masuk**.
2. Klik **Tambah/Input Surat** untuk mencatat surat yang diterima.
   - Isi nomor agenda, tanggal surat, perihal, asal/instansi pengirim, dan unggah file (PDF/gambar).
3. Surat yang baru dicatat berstatus **Menunggu Verifikasi**.
4. Untuk verifikasi data & kelengkapan, buka detail surat lalu klik **Verifikasi**.
   - Sekretaris/Admin memeriksa kelengkapan berkas; status berubah menjadi **Terverifikasi**.
   - Jika tidak lengkap, surat dapat ditolak/dikembalikan beserta catatan verifikasi.
5. Surat yang sudah terverifikasi baru dapat diproses lebih lanjut (disposisi, delegasi, arsip).

---

## 3. Disposisi Surat

1. Pilih surat masuk yang sudah terverifikasi → buka tab **Disposisi**.
2. Klik **Buat Disposisi**, tetapkan:
   - Tujuan (unit/bidang), instruksi/isi disposisi, sifat disposisi, dan batas waktu (opsional).
3. Simpan disposisi → surat menampilkan lembar disposisi yang bisa **dicetak**.
4. Status disposisi dapat dipantau; disposisi dapat diverifikasi/divalidasi oleh sekretaris.

---

## 4. Delegasi Tugas

Delegasi adalah cara **menurunkan pekerjaan** dari sekretaris/admin ke staff:

1. Buka menu **Delegasi → Buat Delegasi**.
2. Isi **judul**, **keterangan**, **kategori tugas** (mis. Surat Masuk), **prioritas**, **batas waktu (deadline)**, pilih **staff penerima**, dan lampirkan surat/berkas terkait.
3. Simpan → delegasi tersimpan sebagai **Konsep/Draf**.
4. Bila sudah siap, kirim delegasi (**Send**). Sistem otomatis membuat **tugas (task)** untuk setiap staff yang ditunjuk dan mengirim **notifikasi**.
5. Semua delegasi dapat dipantau di **Delegasi → Monitoring**:
   - Status delegasi mengikuti status tugas-tugas di dalamnya (aktif / selesai / terlambat).

---

## 5. Pengerjaan Tugas oleh Staff

1. Staff membuka menu **Tugas → Daftar Tugas / Kanban**.
2. Tugas baru masuk dengan status **Baru**:
   - Klik **Terima** untuk mengerjakan → status menjadi **Diterima / Dalam Pengerjaan**.
   - Klik **Tolak** bila tidak dapat mengerjakan, lengkapi alasan penolakan.
3. Selama mengerjakan, staff berkala **memperbarui progres**:
   - Geser persentase progres (0–100%).
   - Tulis **catatan** pengerjaan.
   - **Unggah lampiran hasil kerja** (dokumen/foto, maks. 10 MB) sebagai bukti.
4. Bila progres mencapai **100%**, klik **Ajukan Review** → status menjadi **Menunggu Review**.
5. Riwayat seluruh pembaruan beserta lampirannya tercatat di halaman detail tugas
   dan dapat diunduh oleh pihak yang berwenang.

---

## 6. Review / Verifikasi Hasil Kerja

- Review penyelesaian tugas dilakukan oleh **Admin** dan **Sekretaris**.
- Tugas berstatus **Menunggu Review** menunggu verifikasi penyelesaian oleh sekretaris/admin.
- Sekretaris/admin membuka detail tugas, memeriksa bukti (catatan + lampiran), lalu klik **Verifikasi**.
  - Jika pekerjaan diterima → status menjadi **Selesai** dan staff mendapat notifikasi.
  - Jika belum memenuhi syarat, tugas dapat dikembalikan untuk dikerjakan ulang.
- Hanya admin/sekretaris yang dapat melakukan verifikasi ini (staff tidak).

---

## 7. Agenda Pimpinan

1. Buka menu **Agenda Pimpinan** (kalender) atau **Agenda Hari Ini / Mendatang / Selesai**.
2. Klik **Tambah Agenda / jadwal**:
   - Isi judul agenda, **tautan ke delegasi** (opsional), tanggal, jam mulai–selesai, lokasi, dan keterangan.
3. Agenda tampil di kalender sehingga jadwal pimpinan terlihat dalam satu pandangan.
4. Agenda yang lewat waktu dapat diberi status **Selesai** (atau ditunda/reschedule dengan alasan, tercatat dalam riwayat).

---

## 8. Arsip, Galeri, dan Pencarian

- Surat yang sudah diproses otomatis tersimpan di **Arsip**; di sana dapat difilter per jenis (masuk/keluar) dan rentang tanggal, lalu **diekspor**.
- **Galeri Surat** menampilkan berkas/gambar lampiran surat masuk & keluar dalam bentuk kartu agar mudah ditinjau.
- Gunakan **pencarian** (perihal, nomor, klasifikasi, dsb.) di tiap halaman daftar untuk menemukan data dengan cepat.

---

## 9. Laporan & Ekspor

1. Buka menu **Laporan → Delegasi / Tugas / Agenda**.
2. Gunakan filter (rentang tanggal, status, prioritas) lalu klik **Ekspor CSV**.
   - File CSV berformat UTF-8 (dengan BOM) sehingga aman dibuka di Excel.
3. Laporan dapat disortir per kolom (mis. tenggat waktu, prioritas, progres) langsung dari judul kolom.

---

## 10. Notifikasi

- Setiap kejadian penting mengirim notifikasi: delegasi baru, tugas diterima/ditolak,
  progres diunggah, tugas menunggu review, tugas selesai/terverifikasi, dan tenggat yang mendekati.
- Buka ikon lonceng di pojok kanan atas navbar untuk melihat notifikasi.
- Notifikasi yang belum dibaca ditandai; seluruh notifikasi dapat ditandai sudah dibaca.

---

## 11. Pengaturan & Master Data (khusus Admin)

- **Pengguna**: buat/ubah akun, atur peran & unit/bagian (department), reset kata sandi, aktif/nonaktifkan akun.
- **Referensi**: klasifikasi surat, status surat, departemen/bagian, dan **kategori tugas**.
- **Pengaturan Sistem**: nama aplikasi, nama instansi, alamat, telepon, email, tampilan halaman, dst.
- **Riwayat Aktivitas** tersedia untuk pemantauan dan audit (log semua aktivitas pengguna).

---

## Ringkasan Status (Status Flow)

```
Surat Masuk
  Menunggu Verifikasi → Terverifikasi → (Disposisi / Delegasi / Arsip)

Tugas (dari delegasi yang dikirim)
  Baru → Diterima / Ditolak(Selesai)
  Dalam Pengerjaan → (progres 100%) Menunggu Review
  Menunggu Review → Verifikasi Admin/Sekretaris → Selesai
  Tidak selesai tepat waktu → Terlambat

Agenda Pimpinan
  Terjadwal → Selesai (atau ditunda / reschedule)
```