# PRODUCT REQUIREMENTS DOCUMENT (PRD)
# EIMS Travel Booking System

**Perusahaan:** Rapid Infrastruktur Indonesia (RII)  
**Versi:** 1.3 — Updated Business Workflow  
**Tanggal:** 10 November 2026  
**Status:** Acuan pengembangan tahap Travel Booking  
**Basis:** PRD v1.2, repository review EIMS, dan keputusan bisnis terbaru.

---

## 1. Tujuan

Membangun modul Travel Booking yang terintegrasi dengan EIMS untuk mengelola permintaan perjalanan mulai dari sumber permintaan (SPD atau Project Travel), inquiry vendor, quotation, pemilihan alternatif, konfirmasi booking final, hingga tiket/voucher diterbitkan dan disimpan.

**Batas tahap pengembangan ini adalah Ticket/Voucher Issued dan tercatat di EIMS.**

Invoice vendor, verifikasi invoice, RFP, pembayaran, outstanding, aging, credit limit, dan credit exposure tidak termasuk scope tahap ini dan ditunda untuk pengembangan berikutnya.

## 2. Prinsip Utama

1. `PRD.md` ini menjadi acuan kebutuhan bisnis Travel Booking.
2. Repository EIMS yang aktual menjadi acuan teknis. Pelajari implementasi existing sebelum mengubah kode.
3. Pertahankan modul dan workflow existing, terutama Authentication, User Management, Employee, SPD, dan notifikasi.
4. Integrasikan dengan pola Laravel, database, autentikasi, authorization, UI, dan konvensi yang sudah digunakan EIMS.
5. Jangan membuat akun, login, atau portal untuk vendor.
6. Vendor berkomunikasi dengan RII hanya melalui email. EIMS mengirim dan menerima email serta menghubungkan komunikasi ke booking yang tepat.
7. Seluruh kontrol operasional internal dilakukan melalui EIMS oleh HR/GA Site dan HR/GA HO.
8. Dokumen perjalanan disimpan secara privat dan hanya dapat diakses pengguna internal yang berwenang.
9. Jangan mengarang aturan bisnis yang belum ditentukan. Tanyakan hanya jika ada keputusan bisnis yang benar-benar menghalangi implementasi.
10. Jangan mengubah konfigurasi atau database production. Jangan menghapus data existing.

## 3. Stakeholder dan Role/Permission

### 3.1 HR/GA Site — Role Baru

HR/GA Site adalah role internal RII yang perlu ditambahkan pada sistem Role Permission EIMS.

Tanggung jawab:
- Membuat Project Travel Request.
- Melihat antrean dan status request sesuai site/project yang menjadi kewenangannya.
- Melihat alternatif quotation yang diteruskan HR/GA HO.
- Memilih alternatif perjalanan, menolak alternatif, atau meminta revisi.
- Memantau status sampai tiket/voucher diterbitkan.

### 3.2 HRD — Role Existing untuk HR/GA HO

HR/GA HO menggunakan role existing **HRD**. Jangan membuat role baru bernama HR/GA HO.

Tanggung jawab:
- Melihat dan mengelola antrean Travel Request dari SPD dan Project Travel.
- Memeriksa kelengkapan request.
- Mengelola Master Vendor.
- Mengirim inquiry melalui email kepada vendor.
- Memproses balasan email vendor di EIMS.
- Memeriksa quotation dan meneruskannya kepada HR/GA Site.
- Menindaklanjuti pilihan Site.
- Mengirim Final Booking Confirmation kepada vendor.
- Memeriksa konfirmasi booking dan tiket/voucher yang diterima.

### 3.3 Administrator

Administrator mengelola administrasi sistem sesuai permission yang diberikan. Role Administrator tidak otomatis memperoleh seluruh kewenangan operasional Travel hanya karena berstatus administrator.

### 3.4 Travel Vendor

**Tidak ada role Travel Vendor di EIMS.**

Vendor:
- Tidak memiliki akun EIMS.
- Tidak login ke EIMS.
- Tidak menggunakan Vendor Portal.
- Tidak menggunakan Vendor User Management.
- Hanya berkomunikasi melalui email yang dikirim EIMS dan membalas email tersebut.

Authorization harus ditegakkan pada server-side berdasarkan role dan permission internal EIMS. HR/GA Site dibatasi sesuai site/project atau cakupan kewenangannya; HRD menangani proses HO sesuai permission.

## 4. Keputusan Bisnis

### D-01 — SPD sebagai sumber Travel Request

- Ketika SPD mencapai status `Approved`, sistem membuat Travel Request secara otomatis.
- Satu SPD hanya menghasilkan satu Travel Request.
- Referensi SPD asli harus dipertahankan dan dapat ditelusuri.
- Proses integrasi tidak boleh mengubah atau merusak workflow SPD existing.
- Pembatalan/perubahan SPD setelah Approved harus ditangani sesuai tahap booking dan tidak boleh menghapus histori transaksi yang telah terjadi.
- Mekanisme trigger harus mengikuti audit terhadap model, status, event, dan workflow SPD yang aktual.

### D-02 — Project Travel Request

- HR/GA Site membuat Project Travel Request langsung di EIMS.
- Kategori perjalanan: `Rotation On`, `Rotation Off`, `Mobilization`, dan `Demobilization`.
- Satu request mendukung banyak penumpang secara dinamis.
- Request masuk ke antrean Travel untuk diproses HR/GA HO.

### D-03 — Quotation dan Final Booking

1. HR/GA HO mengirim inquiry kepada vendor melalui email.
2. Vendor mengirim alternatif quotation dengan membalas email tersebut.
3. HR/GA HO memeriksa kelengkapan dan kesesuaian quotation.
4. HR/GA HO meneruskan alternatif kepada HR/GA Site.
5. HR/GA Site memilih alternatif atau meminta revisi.
6. Pilihan/approval Site bukan instruksi booking final.
7. HR/GA HO melakukan pemeriksaan akhir dan mengirim Final Booking Confirmation.
8. Vendor hanya boleh mengeksekusi booking setelah menerima Final Booking Confirmation dari HR/GA HO.
9. Vendor tidak boleh mengubah harga atau itinerary secara sepihak. Perubahan harus disampaikan sebagai revisi untuk ditinjau kembali.

### D-04 — Master Vendor dan Vendor Aktif

- EIMS menyimpan beberapa perusahaan travel pada Master Vendor.
- Pada satu waktu hanya ada satu vendor aktif untuk inquiry/booking baru.
- Master Vendor merupakan data administrasi internal dan tetap dipertahankan.
- Data minimal mencakup nama perusahaan, alamat/email kontak, nomor telepon, status aktif/nonaktif, dan informasi relevan lainnya.
- Pergantian vendor aktif harus memiliki riwayat, tanggal efektif, pelaksana, dan alasan.
- Vendor yang tidak aktif tetap tersimpan sebagai referensi transaksi historis.

### D-05 — Komunikasi Vendor Berbasis Email

- EIMS mengirim email keluar ke alamat vendor yang terdaftar di Master Vendor.
- Email inquiry dan tindak lanjut harus mencantumkan Booking Number yang unik sebagai identitas transaksi.
- Vendor cukup membalas email yang diterima menggunakan fungsi Reply pada email tersebut.
- EIMS menerima balasan email dan menghubungkannya dengan booking yang sesuai.
- Pencocokan tidak boleh hanya mengandalkan teks subject. Sistem perlu memanfaatkan Booking Number dan metadata percakapan email (misalnya message/thread reference) serta memvalidasi alamat pengirim terhadap kontak vendor yang terdaftar.
- Balasan dari pengirim yang tidak dikenal, Booking Number yang tidak valid, atau email yang ambigu harus masuk ke penanganan manual/exception queue; jangan otomatis mengubah status booking.
- Email asli, pengirim, waktu diterima, isi relevan, dan lampiran harus memiliki riwayat yang dapat ditelusuri.
- Lampiran email harus divalidasi dan disimpan secara aman.

### D-06 — Respons Vendor dan Tindak Lanjut HR/GA HO

Balasan vendor yang berhasil dipetakan ke booking ditampilkan pada halaman detail booking EIMS. HR/GA HO menindaklanjuti respons melalui kontrol aplikasi:

- **Approve:** respons/quotation vendor diterima untuk dilanjutkan ke tahap review/penerusan sesuai workflow.
- **Reject:** respons/penawaran vendor ditolak; alasan dicatat.
- **Revision:** HR/GA HO meminta perubahan dan mengisi form detail revisi. EIMS mengirim permintaan revisi tersebut melalui email kepada vendor.

Tombol Approve, Reject, dan Revision adalah kontrol internal HR/GA HO di EIMS, **bukan tombol atau akses yang digunakan vendor**. Vendor merespons melalui email.

Jika vendor membalas dengan informasi yang tidak dapat dipetakan secara pasti ke salah satu respons, HR/GA HO memeriksa dan mengklasifikasikannya secara manual. Sistem tidak boleh menyimpulkan persetujuan hanya dari kata-kata email tanpa kontrol yang memadai.

### D-07 — Penumpang Diisi Manual

- Daftar penumpang diisi manual pada Travel Request.
- Penumpang tidak wajib terdaftar di Master Employee EIMS.
- Penumpang dapat berupa karyawan RII, kontraktor, konsultan, tamu, atau pihak lain yang perlu melakukan perjalanan.
- Tidak boleh mewajibkan `employee_id` atau relasi wajib ke tabel Employee untuk setiap penumpang.
- Satu Travel Request mendukung banyak penumpang.
- Data identitas penumpang hanya digunakan untuk kebutuhan pemesanan dan harus dilindungi sesuai authorization.

### D-08 — Batas Tahap Pengembangan

Tahap ini berakhir ketika tiket/voucher diterima, dicatat, dan dapat diakses oleh pengguna internal yang berwenang di EIMS.

Invoice, pembayaran, RFP, outstanding, aging, credit limit, dan credit exposure ditunda dan bukan bagian dari implementasi tahap ini.

## 5. Ruang Lingkup

### 5.1 In Scope

- Integrasi SPD Approved menjadi Travel Request tanpa duplikasi.
- Project Travel Request oleh HR/GA Site.
- Multi-passenger dengan input manual.
- Antrean Travel untuk HR/GA Site dan HRD sesuai permission.
- Master Vendor internal.
- Penetapan satu vendor aktif untuk booking baru.
- Email inquiry keluar dari EIMS.
- Penerimaan dan pemetaan balasan email vendor berdasarkan Booking Number dan metadata email.
- Penyimpanan riwayat komunikasi email.
- Tampilan respons vendor pada detail booking.
- Kontrol Approve, Reject, Revision untuk HR/GA HO.
- Form detail revisi dan pengiriman revisi melalui email.
- Pengelolaan alternatif quotation dan lampirannya.
- Pemilihan alternatif oleh HR/GA Site.
- Final Booking Confirmation oleh HR/GA HO.
- Penerimaan konfirmasi booking dan tiket/voucher melalui email.
- Penyimpanan privat dan verifikasi tiket/voucher.
- Status, notifikasi internal, audit trail, pencarian, filter, dan laporan operasional Travel.

### 5.2 Out of Scope

- Vendor Portal, Vendor Login, Vendor User Management, dan role/permission Travel Vendor.
- Invoice management dan invoice verification.
- RFP workflow.
- Finance payment recording dan payment allocation.
- Outstanding, invoice aging, credit limit, credit exposure, dan credit exception.
- Integrasi API langsung ke maskapai, hotel, bus, atau sistem reservasi eksternal.
- Otomatisasi pembelian/pemesanan langsung melalui API eksternal.

## 6. Alur Bisnis To-Be

### 6.1 Sumber Travel Request

**A. Dari SPD**
1. SPD disetujui (`Approved`).
2. EIMS membuat satu Travel Request terkait SPD.
3. Sistem mencegah request duplikat.
4. HRD/HR/GA HO dapat memproses request melalui antrean Travel.

**B. Project Travel**
1. HR/GA Site membuat request.
2. Memilih kategori Rotation On, Rotation Off, Mobilization, atau Demobilization.
3. Mengisi detail perjalanan dan satu atau banyak penumpang secara manual.
4. Mengirim request.
5. Request tampil pada antrean HR/GA Site dan HRD sesuai hak akses.

### 6.2 Pemeriksaan Request

1. HRD/HR/GA HO memeriksa kelengkapan request.
2. Jika tidak lengkap, request dikembalikan ke HR/GA Site dengan catatan revisi.
3. Jika lengkap, HR/GA HO melanjutkan ke inquiry vendor.

### 6.3 Email Inquiry dan Respons Vendor

1. HR/GA HO memilih vendor aktif dan mengirim inquiry dari EIMS.
2. Email berisi Booking Number dan detail perjalanan yang diperlukan.
3. Vendor menerima email dan membalas email tersebut.
4. EIMS menerima email masuk, memvalidasi pengirim, mengidentifikasi Booking Number, dan memetakan balasan ke booking.
5. EIMS menyimpan isi email dan lampiran serta menampilkan respons pada halaman booking.
6. Jika email tidak dapat dipetakan dengan aman, email masuk ke penanganan manual dan tidak mengubah status booking otomatis.

### 6.4 Review Quotation dan Revisi

1. HR/GA HO meninjau respons dan quotation vendor.
2. HR/GA HO dapat memilih Approve, Reject, atau Revision pada EIMS.
3. Reject harus disertai alasan.
4. Revision membuka form untuk mengisi detail perubahan yang diminta.
5. EIMS mengirim permintaan revisi melalui email kepada vendor.
6. Vendor membalas email revisi; EIMS mengaitkan balasan dengan booking yang sama.
7. Setelah quotation lengkap, HR/GA HO meneruskan alternatif kepada HR/GA Site.

### 6.5 Pemilihan Alternatif oleh Site

1. HR/GA Site meninjau alternatif yang diteruskan HR/GA HO.
2. Site memilih alternatif atau meminta revisi sesuai kewenangannya.
3. Pilihan Site dicatat beserta aktor dan waktu.
4. Pilihan Site dikembalikan kepada HR/GA HO untuk pemeriksaan akhir.
5. Pilihan Site tidak mengirim instruksi booking final kepada vendor.

### 6.6 Final Booking dan Tiket/Voucher

1. HR/GA HO memeriksa pilihan Site dan detail akhir.
2. HR/GA HO mengirim Final Booking Confirmation melalui EIMS.
3. EIMS mengirim email final kepada vendor dengan Booking Number.
4. Vendor melaksanakan booking dan membalas email dengan konfirmasi serta tiket/voucher.
5. EIMS memetakan email dan lampiran ke booking yang sesuai.
6. HR/GA HO memeriksa nama penumpang, rute, tanggal, jadwal, dan detail dokumen.
7. Jika ada ketidaksesuaian, HR/GA HO meminta koreksi melalui email EIMS.
8. Jika sesuai, tiket/voucher ditandai verified dan dapat diakses oleh pengguna internal yang berwenang.
9. Status Travel Request menjadi selesai sesuai definisi status yang diterapkan.

## 7. Kebutuhan Fungsional

### FR-01 — SPD Integration
- Mendeteksi SPD Approved.
- Membuat satu Travel Request per SPD.
- Mencegah duplikasi, termasuk pada retry/job berulang.
- Menjaga referensi SPD dan histori.

### FR-02 — Project Travel Request
- HR/GA Site dapat membuat request.
- Mendukung kategori Rotation On, Rotation Off, Mobilization, dan Demobilization.
- Mendukung banyak penumpang manual.
- Request dapat diperiksa dan dikembalikan untuk revisi oleh HRD.

### FR-03 — Passenger Management
- Form multi-passenger dengan tambah/hapus baris penumpang.
- Field minimum: nama lengkap sesuai identitas, jenis identitas (KTP/Paspor), nomor identitas, jenis kelamin, dan nomor telepon/WhatsApp bila tersedia.
- Kebutuhan khusus/catatan perjalanan dapat diisi opsional.
- Tidak mensyaratkan data penumpang terdaftar sebagai Employee.
- Validasi server-side dan perlindungan data pribadi wajib diterapkan.

### FR-04 — Travel Queue and Authorization
- HR/GA Site dapat melihat request sesuai cakupan site/project dan permission.
- HRD dapat mengelola antrean HO sesuai permission.
- Menu Antrean Travel tersedia untuk HR/GA Site dan HRD sesuai kebutuhan kerja.
- Menu operasional tidak ditempatkan sebagai fitur khusus Admin.
- Administrator tidak otomatis memperoleh hak mengambil keputusan operasional.

### FR-05 — Master Vendor
- HRD/pengguna internal yang diberi permission dapat melihat, menambah, mengubah, mengaktifkan, dan menonaktifkan Master Vendor.
- Master Vendor menyimpan identitas perusahaan dan kontak email.
- Mendukung banyak vendor dengan satu vendor aktif untuk booking baru.
- Menyimpan histori perubahan status vendor.
- Master Vendor bukan akun pengguna dan tidak menyediakan login vendor.

### FR-06 — Outbound Vendor Email
- HRD dapat mengirim inquiry dan email tindak lanjut dari EIMS ke email vendor.
- Email memuat Booking Number unik dan konteks request yang diperlukan.
- Email dan status pengiriman dicatat.
- Pengiriman menggunakan queue/retry sesuai arsitektur EIMS.
- Kegagalan pengiriman harus terlihat dan dapat ditindaklanjuti; transaksi tidak boleh hilang.

### FR-07 — Inbound Vendor Email
- EIMS menerima balasan vendor melalui mekanisme email masuk yang sesuai dengan infrastruktur email yang tersedia.
- Sistem mengidentifikasi booking menggunakan Booking Number dan metadata percakapan email.
- Sistem memvalidasi alamat pengirim terhadap kontak vendor yang terdaftar.
- Sistem menangani email duplikat secara idempotent.
- Email tidak dikenal, ambigu, atau tidak memiliki referensi yang dapat diverifikasi masuk ke antrean penanganan manual.
- Isi email dan metadata penting disimpan sebagai riwayat komunikasi.
- Lampiran divalidasi sebelum disimpan.

### FR-08 — Vendor Response Handling
- Respons email tampil pada halaman detail booking.
- HRD dapat melakukan Approve, Reject, atau Revision.
- Reject memerlukan alasan.
- Revision menyediakan form detail revisi dan mengirimkannya melalui email.
- Aksi internal dan respons email vendor dicatat terpisah dalam histori.
- Email vendor tidak otomatis dianggap sebagai persetujuan internal.

### FR-09 — Quotation and Alternatives
- Menyimpan satu atau beberapa alternatif quotation.
- Data dapat mencakup moda/operator, rute, jadwal, harga, layanan, bagasi, ketentuan perubahan/refund, masa berlaku, dan lampiran jika relevan.
- Perubahan quotation memiliki versi/riwayat.
- HRD meninjau kelengkapan sebelum meneruskan alternatif kepada Site.

### FR-10 — Site Selection
- HR/GA Site dapat melihat alternatif yang diteruskan HRD.
- Site dapat memilih alternatif atau meminta revisi.
- Keputusan Site menyimpan aktor, waktu, dan catatan.
- Keputusan Site tidak sama dengan Final Booking Confirmation.

### FR-11 — Final Booking Confirmation
- Hanya HRD/HR/GA HO dengan permission yang sesuai dapat mengirim instruksi final.
- Instruksi mengacu pada alternatif terpilih dan mencatat harga/detail final, pengirim, waktu, serta isi email.
- Vendor tidak boleh dianggap mendapat instruksi final sebelum email final berhasil dikirim sesuai mekanisme sistem.
- Perubahan setelah instruksi final harus melalui proses koreksi yang tercatat.

### FR-12 — Booking Confirmation and Ticket/Voucher
- Balasan vendor dan dokumen tiket/voucher dipetakan ke booking terkait.
- HRD dapat memeriksa dan memverifikasi dokumen.
- HRD dapat meminta koreksi melalui email.
- Status verifikasi, aktor, waktu, dan catatan disimpan.

### FR-13 — Document Management
- Dokumen perjalanan disimpan di private storage.
- Download/preview harus melewati authorization.
- Validasi tipe, ukuran, dan nama file.
- Nama file fisik tidak boleh mudah ditebak.
- Catat uploader/sumber email, waktu, tipe dokumen, dan relasi booking.
- Jangan menyimpan dokumen sensitif pada public directory tanpa kontrol akses.

### FR-14 — Notifications
Notifikasi internal minimal:
- Travel Request baru untuk HRD.
- Request dikembalikan untuk revisi.
- Respons vendor diterima.
- Email vendor gagal dipetakan atau perlu penanganan.
- Quotation diteruskan kepada Site.
- Site memilih alternatif atau meminta revisi.
- Final Booking Confirmation dikirim.
- Konfirmasi booking/tiket diterima.
- Tiket/voucher diverifikasi atau perlu koreksi.

### FR-15 — Audit Trail
Catat minimal:
- Perubahan status request, inquiry, quotation, dan booking.
- Pengiriman dan penerimaan email (message reference, pengirim/penerima, waktu, status).
- Keputusan Approve, Reject, Revision dan alasannya.
- Perubahan alternatif/harga.
- Pemilihan Site.
- Final Booking Confirmation.
- Upload, penggantian, dan verifikasi tiket/voucher.
- Aktor dan timestamp setiap tindakan.

### FR-16 — Search, Filter, and Reporting
Pencarian/filter minimal berdasarkan Booking Number, nomor SPD, sumber request, kategori perjalanan, site/project, nama penumpang sesuai hak akses, vendor, tanggal perjalanan, dan status.

Laporan tahap ini hanya mencakup operasional Travel Request, inquiry, quotation, booking, serta tiket/voucher. Tidak termasuk laporan invoice/keuangan.

## 8. Data dan Konsep Entitas

Model konseptual berikut bukan instruksi migration final. Antigravity harus menyesuaikannya dengan schema aktual EIMS.

| Entitas konseptual | Fungsi |
|---|---|
| TravelRequest | Induk request dari SPD atau Project Travel |
| TravelPassenger | Penumpang manual, tidak wajib terkait Employee |
| Vendor | Master perusahaan travel internal |
| VendorActiveHistory | Histori pergantian vendor aktif |
| TravelInquiry | Inquiry/email yang dikirim untuk booking |
| VendorEmailMessage | Riwayat email masuk/keluar dan metadata percakapan |
| Quotation | Informasi penawaran vendor |
| QuotationOption | Alternatif harga/jadwal/layanan |
| QuotationRevision | Histori revisi quotation |
| SiteSelection | Keputusan HR/GA Site atas alternatif |
| Booking | Instruksi dan konfirmasi booking final |
| TravelDocument | Quotation, konfirmasi, tiket, voucher, dan dokumen perjalanan |
| AuditLog | Jejak perubahan dan keputusan |
| Notification | Notifikasi proses internal |

Ketentuan:
- Penumpang disimpan sebagai data Travel tersendiri; `employee_id` tidak wajib.
- Master Vendor tidak boleh memiliki ketergantungan pada akun login vendor.
- Email harus memiliki identitas unik dari provider/message ID untuk mencegah pemrosesan ganda.
- Relasi email ke booking harus dapat ditelusuri.
- Jangan membuat tabel atau kolom hanya berdasarkan nama konseptual tanpa memeriksa struktur existing dan kebutuhan aktual.

## 9. Status Proses yang Diusulkan

Status akhir dan transisi harus konsisten dengan pola EIMS dan diimplementasikan sebagai transisi yang tervalidasi.

### Travel Request
- Draft
- Submitted
- Under Review
- Need Revision
- Ready for Vendor Inquiry
- In Vendor Process
- Waiting Site Selection
- Site Revision Requested
- Selected by Site
- Awaiting Final Booking
- Booking Confirmed
- Ticket/Voucher Received
- Under Document Verification
- Completed
- Rejected
- Cancelled

### Vendor Inquiry / Email
- Draft
- Queued
- Sent
- Delivery Failed
- Vendor Replied
- Needs Manual Review
- Closed

### Quotation
- Received
- Under HRD Review
- Revision Requested
- Forwarded to Site
- Selected by Site
- Rejected
- Superseded/Expired (jika relevan)

### Booking / Ticket
- Awaiting Final Confirmation
- Final Confirmation Sent
- Vendor Processing
- Booking Confirmed
- Ticket/Voucher Received
- Under Verification
- Verified
- Correction Requested
- Unable to Book
- Cancelled

Status tidak boleh berubah hanya karena email masuk yang tidak tervalidasi atau tidak dapat dipetakan secara pasti.

## 10. Keamanan dan Non-Functional Requirements

1. Authorization server-side untuk setiap route, aksi, dan dokumen.
2. Tidak ada autentikasi atau session vendor.
3. HR/GA Site hanya mengakses request dalam cakupan kewenangannya.
4. HRD mengakses fungsi HO sesuai permission.
5. Validasi server-side dan proteksi mass assignment.
6. CSRF protection dan rate limiting mengikuti praktik EIMS.
7. Jangan mencatat password, token, atau credential email pada log.
8. Email masuk harus divalidasi: pengirim, Booking Number, message ID, dan konteks percakapan.
9. Tangani email duplikat, replay, spoofing, malformed email, dan lampiran berbahaya.
10. Jangan menjalankan aksi sensitif hanya berdasarkan teks email bebas. Keputusan internal tetap dilakukan oleh pengguna HRD yang berwenang di EIMS.
11. Lampiran email dan dokumen perjalanan disimpan privat, dengan validasi dan authorization saat diakses.
12. Gunakan queue/retry/idempotency untuk email; kegagalan email tidak boleh menghapus request atau histori.
13. Lindungi data identitas penumpang dan batasi penyebarannya ke vendor pada data yang memang diperlukan untuk pemesanan.
14. Gunakan database transaction untuk perubahan status dan pencatatan proses yang saling terkait.
15. Hindari N+1 query; gunakan pagination/filter.
16. UI responsive dan konsisten dengan tampilan EIMS.
17. Perubahan database harus aman terhadap data existing dan disertai rollback plan.
18. Automated tests harus mencakup authorization, workflow, email mapping, duplicate email, dan perlindungan dokumen.
19. Jangan mengklaim test lulus tanpa menjalankannya dan melaporkan hasil aktual.

## 11. Acceptance Criteria

1. SPD Approved menghasilkan tepat satu Travel Request dan tetap mempertahankan referensi SPD.
2. Project Travel dapat dibuat oleh HR/GA Site dengan kategori yang ditentukan.
3. Satu request dapat memiliki banyak penumpang yang diinput manual.
4. Penumpang yang tidak terdaftar sebagai Employee tetap dapat disimpan dan diproses.
5. HR/GA Site memiliki menu dan permission Antrean Travel sesuai cakupan kewenangannya.
6. HR/GA HO menggunakan role existing HRD; tidak dibuat role HR/GA HO baru.
7. Master Vendor tersedia untuk administrasi internal.
8. Tidak ada role Travel Vendor, akun login vendor, Vendor Portal, atau Vendor User Management.
9. HRD dapat mengirim inquiry email ke vendor dengan Booking Number.
10. Vendor dapat membalas email tanpa login ke EIMS.
11. EIMS dapat memetakan balasan ke booking yang tepat dan memvalidasi pengirim.
12. Email ambigu/tidak dikenal tidak mengubah status booking otomatis.
13. Respons vendor dan lampiran terlihat pada detail booking serta memiliki histori.
14. HRD dapat memilih Approve, Reject, atau Revision di EIMS.
15. Revision memiliki form detail dan dapat dikirim kembali melalui email.
16. HR/GA Site dapat memilih alternatif tanpa mengirim instruksi final kepada vendor.
17. Hanya HRD yang berwenang dapat mengirim Final Booking Confirmation.
18. Vendor baru melaksanakan booking setelah menerima instruksi final tersebut.
19. Balasan konfirmasi dan tiket/voucher vendor tersimpan pada booking yang benar.
20. HRD dapat memverifikasi tiket/voucher dan menyelesaikan request.
21. Dokumen tidak dapat diakses pengguna yang tidak berwenang.
22. Workflow SPD dan modul EIMS existing tidak mengalami regresi.

## 12. Milestone Pengembangan

Urutan milestone pengembangan mengikuti rencana project yang telah disepakati, dengan scope tahap ini dibatasi sampai Ticket/Voucher Issued:

- **P0 — Repository Audit & Gap Analysis:** gunakan hasil audit repository yang telah tersedia sebagai baseline; jangan mengulang audit/dokumen tanpa alasan.
- **P1 — Foundation, Master Vendor, Permission Mapping:** fondasi Travel, Master Vendor internal, role HR/GA Site, pemetaan HRD existing, authorization.
- **P2 — SPD Integration & Project Travel Request:** integrasi SPD Approved dan request Project Travel dengan penumpang manual.
- **P3 — Email Inquiry & Vendor Response:** email keluar/masuk, Booking Number, pemetaan balasan, histori email.
- **P4 — Quotation Review & Site Selection:** alternatif quotation, Approve/Reject/Revision oleh HRD, pemilihan Site.
- **P5 — Booking & Ticket/Voucher:** Final Booking Confirmation, konfirmasi vendor, penerimaan dan verifikasi tiket/voucher.
- **P6 — Operational Dashboard, Reporting & Notifications:** dashboard dan laporan operasional Travel serta notifikasi internal.
- **P7 — Security, UAT, Regression & Deployment Readiness:** pengujian keamanan, workflow, regresi, dan kesiapan deployment.

Invoice dan Finance Management menjadi scope pengembangan terpisah di masa mendatang dan tidak boleh dikerjakan dalam tahap ini.

## 13. Instruksi untuk Antigravity

1. Baca seluruh repository aktual, termasuk Controllers, Models, Migrations, database schema, Authentication, Authorization, middleware, routes, services, jobs, events, listeners, notifications, views, storage, konfigurasi relevan, dan tests.
2. Baca PRD.md ini secara menyeluruh.
3. Pahami workflow existing EIMS sebelum mengubah atau menambahkan kode.
4. Lanjutkan development berdasarkan milestone di atas; jangan berhenti hanya pada analisis atau membuat dokumen review baru.
5. Jangan membuat Vendor Portal, Vendor Login, Vendor User Management, role Travel Vendor, atau permission portal vendor.
6. Jangan menghapus Master Vendor atau data vendor. Master Vendor adalah data referensi internal.
7. Jangan mengubah workflow SPD existing selain integrasi yang dibutuhkan dan diuji.
8. Jangan mengimplementasikan invoice, RFP, pembayaran, outstanding, aging, credit limit, atau credit exposure dalam tahap ini.
9. Gunakan standar production-ready Laravel, secure coding, authorization, validation, audit trail, idempotency, dan private document storage.
10. Jalankan test yang relevan pada environment development/testing. Jangan menjalankan migration atau perubahan data pada production.
11. Setelah setiap bagian implementasi, laporkan ringkas fitur, file yang berubah, migration, test yang benar-benar dijalankan, hasil, dan blocker bisnis/teknis yang nyata.
12. Jika ada keputusan bisnis yang benar-benar belum tercakup dan menghalangi implementasi, ajukan pertanyaan spesifik. Jangan mengulang pertanyaan yang sudah dijawab dalam PRD.

**Instruksi akhir:** Bangun Travel Booking System sesuai PRD ini dan repository EIMS yang aktual. Prioritasnya adalah proses pemesanan yang berjalan benar dari Travel Request sampai Ticket/Voucher Issued, dengan vendor berkomunikasi hanya melalui email.
