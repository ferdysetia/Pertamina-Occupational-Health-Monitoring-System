# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Sistem Manajemen Data Kesehatan & MCU Karyawan Berbasis Web
### HSE — Pertamina Gas

## 1. PROJECT OVERVIEW

Saya ingin membangun sebuah aplikasi web internal untuk membantu pengelolaan data kesehatan dan Medical Check Up (MCU) karyawan di lingkungan kerja Pertamina Gas.

Saat ini data kesehatan/MCU karyawan masih tersebar dalam dokumen dan kertas sehingga sulit dikelola, dicari, dipantau, dan digunakan untuk memperoleh informasi kesehatan karyawan secara terstruktur.

Aplikasi ini akan menjadi sistem terpusat untuk:

- mengelola data karyawan;
- mengelola data hasil MCU;
- memberikan akses berdasarkan role pengguna;
- menyediakan dashboard informasi kesehatan;
- menampilkan histori kesehatan karyawan;
- mengidentifikasi hasil kesehatan yang membutuhkan perhatian;
- memberikan daftar karyawan yang perlu mendapatkan tindak lanjut;
- membantu HSE dan dokter dalam melakukan pemantauan data kesehatan.

Aplikasi harus memiliki tampilan corporate, formal, profesional, bersih, modern, dan mudah digunakan.

## 2. TUJUAN SISTEM

Tujuan utama aplikasi:

1. Memusatkan data kesehatan dan MCU karyawan dalam satu sistem.
2. Mengurangi ketergantungan terhadap data kesehatan dalam bentuk kertas atau file yang tersebar.
3. Mempermudah HSE dan dokter dalam melakukan input serta pemantauan hasil MCU.
4. Memberikan akses kepada karyawan untuk melihat data kesehatan miliknya sendiri.
5. Memberikan dashboard yang menyajikan informasi kesehatan secara ringkas.
6. Mengidentifikasi hasil kesehatan dengan kondisi yang membutuhkan perhatian.
7. Menyediakan informasi tindak lanjut terhadap karyawan yang masuk kategori perhatian.
8. Menerapkan pembatasan akses berdasarkan role.
9. Menyediakan fondasi sistem yang dapat dikembangkan menjadi sistem HSE/health management yang lebih besar.

## 3. TEKNOLOGI

Gunakan stack berikut:

### Backend

- Laravel
- Laravel Eloquent ORM
- Laravel Migration
- Laravel Seeder/Factory jika diperlukan
- Laravel Authentication
- REST API atau arsitektur backend yang sesuai untuk SPA

### Frontend

- Vue.js
- Tailwind CSS
- shadcn/ui atau implementasi komponen UI yang kompatibel dengan Vue
- SPA (Single Page Application)

### Database

Gunakan database relasional yang kompatibel dengan Laravel.

Prioritaskan struktur database yang:

- normalized;
- mudah dipelihara;
- memiliki foreign key yang jelas;
- memiliki index pada kolom yang sering digunakan untuk pencarian/filter;
- menjaga integritas data;
- mudah dikembangkan.

## 4. ATURAN PALING PENTING TERKAIT DATA SQL

Terdapat file sumber data karyawan:

`C:\Users\ferdy\OneDrive\Desktop\Project HSE\Data kesehatan para karyawan.sql`

WAJIB membaca dan menganalisis file tersebut sebelum membuat migration, model, seeder, atau struktur database final.

Data dalam file tersebut saat ini masih belum terstruktur dengan baik dan perlu dirapikan agar dapat digunakan sebagai database aplikasi.

Namun terdapat aturan mutlak:

### JANGAN MENGUBAH DATA SUMBER.

Jangan:

- menghapus data;
- menambahkan data baru yang tidak berasal dari file;
- mengarang data;
- mengganti nilai data;
- mengubah identitas karyawan;
- mengubah hasil pemeriksaan;
- mengubah nilai MCU;
- mengubah kode P1–P7;
- mengubah arti data;
- membuang kolom yang dianggap tidak diperlukan.

Yang boleh dilakukan adalah mengubah ORGANISASI/STRUKTUR data agar lebih sesuai dengan database relasional.

Contoh:

Jika satu file SQL memiliki data karyawan dan data MCU dalam struktur yang bercampur, data tersebut boleh dipisahkan menjadi tabel:

- employees
- mcu_records
- mcu_results
- users

selama seluruh informasi asli tetap tersimpan dan dapat ditelusuri.

### Prinsip:

`Data tidak boleh berubah. Struktur boleh dirapikan.`

Sebelum melakukan perubahan struktur, analisis terlebih dahulu:

- seluruh tabel;
- seluruh kolom;
- tipe data;
- primary key;
- relasi;
- duplikasi;
- format tanggal;
- nilai kosong;
- kode;
- kategori;
- data P1 sampai P7.

Jika ditemukan data yang ambigu atau struktur sumber tidak konsisten, jangan langsung menghapus atau mengubahnya. Pertahankan data aslinya dan buat solusi struktur yang aman.

## 5. KLASIFIKASI P1–P7

Data kesehatan memiliki klasifikasi P1 sampai P7.

Gunakan aturan berikut sebagai prinsip sistem:

- P1 = kondisi kesehatan paling baik
- P2 = kondisi kesehatan baik
- P3 = kondisi kesehatan relatif baik tetapi perlu perhatian sesuai hasil pemeriksaan
- P4 = kondisi yang membutuhkan perhatian
- P5 = kondisi yang membutuhkan tindak lanjut
- P6 = kondisi kesehatan kurang baik dan membutuhkan perhatian/tindak lanjut
- P7 = kondisi kesehatan paling kurang baik

PENTING:

Jangan mengarang definisi medis spesifik untuk setiap P1–P7 apabila file sumber atau aturan bisnis belum memberikan definisi tersebut.

Jika file SQL memiliki informasi tambahan mengenai arti P1–P7, gunakan informasi tersebut sebagai sumber utama.

Jika belum tersedia aturan detail, buat struktur klasifikasi yang dapat dikonfigurasi sehingga definisi P1–P7 dapat diubah kemudian tanpa mengubah struktur utama aplikasi.

## 6. ROLE USER

Sistem memiliki empat role utama.

### 6.1 Administrator

Administrator memiliki akses tertinggi terhadap pengelolaan sistem.

Hak akses:

- login;
- melihat dashboard;
- mengelola user;
- membuat user;
- mengubah user;
- mengaktifkan/nonaktifkan user;
- mengatur role;
- melihat data karyawan;
- mengelola konfigurasi sistem;
- melihat data MCU sesuai hak akses;
- mengelola data referensi;
- melihat audit/activity log jika modul tersebut dibuat.

Administrator tidak boleh mengubah data kesehatan secara sembarangan tanpa jejak perubahan.

### 6.2 HSE

HSE merupakan role utama untuk pengelolaan informasi kesehatan dari sisi HSE.

Hak akses:

- login;
- melihat dashboard HSE;
- melihat data karyawan;
- input data MCU;
- edit data MCU sesuai hak akses;
- melihat histori MCU;
- mencari karyawan;
- filter data kesehatan;
- melihat klasifikasi P1–P7;
- melihat daftar karyawan yang membutuhkan tindak lanjut;
- mencatat tindak lanjut;
- melihat statistik kesehatan;
- melihat data kesehatan dalam bentuk tabel;
- melihat detail kesehatan seorang karyawan.

### 6.3 Karyawan

Karyawan hanya dapat melihat data miliknya sendiri.

Hak akses:

- login;
- melihat dashboard personal;
- melihat profil sendiri;
- melihat hasil MCU sendiri;
- melihat histori MCU sendiri;
- melihat status kesehatan sendiri;
- melihat informasi tindak lanjut yang berkaitan dengan dirinya jika diizinkan oleh aturan akses.

Karyawan TIDAK BOLEH:

- melihat data karyawan lain;
- melihat seluruh database kesehatan;
- mengedit hasil MCU;
- mengubah klasifikasi kesehatan;
- mengakses halaman administrasi;
- mengakses data HSE karyawan lain.

### 6.4 Dokter

Dokter memiliki akses terhadap data kesehatan yang dibutuhkan untuk pemeriksaan dan pemantauan medis.

Hak akses:

- login;
- dashboard dokter;
- melihat data karyawan;
- input hasil MCU;
- edit hasil MCU sesuai hak akses;
- melihat histori MCU;
- melihat detail pemeriksaan;
- mencari karyawan;
- filter hasil MCU;
- melihat klasifikasi P1–P7;
- memberikan catatan medis/tindak lanjut sesuai struktur data yang tersedia;
- melihat daftar karyawan yang membutuhkan perhatian.

Dokter tidak boleh mengelola user atau role kecuali diberikan hak tersebut oleh Administrator.

## 7. AUTHENTICATION

Implementasikan authentication yang aman.

Minimal:

- login;
- logout;
- password hashing;
- session/authentication management;
- role-based authorization;
- route protection;
- middleware berdasarkan role.

Setiap user setelah login diarahkan ke dashboard sesuai role.

Contoh:

Administrator → Admin Dashboard

HSE → HSE Dashboard

Dokter → Doctor Dashboard

Karyawan → Employee Dashboard

## 8. MODUL SISTEM

Sistem minimal memiliki modul berikut:

### Modul 1 — Dashboard

Dashboard menjadi halaman pertama setelah user login.

Dashboard harus bersifat role-aware.

Dashboard Administrator:

- jumlah user;
- jumlah karyawan;
- jumlah HSE;
- jumlah dokter;
- jumlah data MCU;
- distribusi klasifikasi P1–P7;
- data kesehatan yang membutuhkan perhatian;
- aktivitas sistem jika tersedia.

Dashboard HSE:

- jumlah karyawan;
- jumlah MCU;
- distribusi P1–P7;
- jumlah karyawan yang membutuhkan tindak lanjut;
- tren pemeriksaan;
- ringkasan kondisi kesehatan;
- daftar terbaru MCU;
- daftar karyawan dengan kondisi yang perlu perhatian.

Dashboard Dokter:

- jumlah karyawan;
- jumlah MCU;
- distribusi P1–P7;
- daftar pemeriksaan terbaru;
- daftar karyawan yang membutuhkan perhatian;
- daftar tindak lanjut.

Dashboard Karyawan:

- nama karyawan;
- informasi profil;
- hasil MCU terbaru;
- status kesehatan;
- histori MCU;
- informasi tindak lanjut pribadi jika ada.

Gunakan card, table, badge, chart, dan statistik secara proporsional.

Jangan membuat dashboard terlalu ramai.

### Modul 2 — Manajemen Data MCU

Halaman ini digunakan oleh HSE dan Dokter.

Fitur:

- tabel data MCU;
- search;
- filter;
- sorting;
- pagination;
- detail MCU;
- input MCU;
- edit MCU;
- histori MCU;
- detail karyawan.

Kolom tabel disesuaikan dengan struktur data SQL sumber.

Jangan menghilangkan informasi penting dari sumber.

Gunakan:

- status badge;
- health classification badge;
- date formatting;
- readable table;
- responsive layout.

Untuk data dalam jumlah besar, gunakan server-side pagination jika diperlukan.

### Modul 3 — Data Karyawan

Buat halaman daftar karyawan.

Fitur:

- search nama;
- search NIK/employee ID;
- filter;
- pagination;
- detail karyawan;
- histori MCU;
- ringkasan kondisi kesehatan.

Karyawan hanya dapat melihat record miliknya sendiri.

HSE dan dokter dapat melihat data sesuai hak akses.

Administrator memiliki akses administrasi.

### Modul 4 — Health Alert / Follow-up

Buat modul khusus untuk memberikan perhatian terhadap karyawan yang memiliki hasil kesehatan pada kategori yang perlu ditindaklanjuti.

Nama modul dapat berupa:

`Health Alerts`

atau

`Follow-up`

atau istilah lain yang lebih sesuai dengan konteks corporate/HSE.

Fitur:

- daftar karyawan yang membutuhkan perhatian;
- klasifikasi P1–P7;
- alasan/status berdasarkan data MCU;
- tanggal pemeriksaan;
- status tindak lanjut;
- catatan tindak lanjut;
- PIC;
- tanggal tindak lanjut;
- status Open / In Progress / Completed jika sesuai kebutuhan;
- histori tindak lanjut.

Sistem harus dapat membantu HSE/dokter melihat siapa saja yang membutuhkan perhatian.

Jangan membuat diagnosis medis otomatis.

Sistem hanya mengolah dan menampilkan data berdasarkan parameter/klasifikasi yang tersedia.

## 9. STRUKTUR DATA

Sebelum membuat database final:

1. Baca file SQL.
2. Identifikasi seluruh data.
3. Petakan data sumber ke entitas database.
4. Tentukan primary key.
5. Tentukan foreign key.
6. Normalisasi struktur apabila diperlukan.
7. Pastikan semua data sumber tetap tersimpan.
8. Buat migration.
9. Buat model Eloquent.
10. Buat seeder/importer untuk memasukkan data sumber.

Contoh struktur konseptual:

### users

- id
- employee_id
- role_id / role
- name
- email
- password
- status
- timestamps

### employees

Kolom harus mengikuti data sumber SQL.

Jangan membuat field fiktif yang tidak dibutuhkan.

### mcu_records

Menyimpan satu record pemeriksaan MCU.

### mcu_results

Digunakan apabila hasil pemeriksaan perlu dipisahkan berdasarkan jenis pemeriksaan.

### health_alerts

Menyimpan alert/tindak lanjut.

### follow_ups

Menyimpan histori tindakan/tindak lanjut.

### roles

Jika menggunakan RBAC berbasis tabel.

### activity_logs

Jika diperlukan untuk audit.

CATATAN:

Struktur di atas adalah struktur konseptual.

Jangan langsung membuat tabel tersebut sebelum membaca SQL sumber.

Struktur final harus mengikuti kebutuhan data aktual.

## 10. AUDIT TRAIL

Karena data yang dikelola merupakan data kesehatan, sistem harus memiliki jejak perubahan untuk operasi penting.

Minimal catat:

- siapa yang melakukan perubahan;
- kapan perubahan dilakukan;
- data apa yang diubah;
- jenis operasi;
- record terkait.

Prioritaskan audit trail pada:

- input MCU;
- edit MCU;
- perubahan klasifikasi;
- perubahan tindak lanjut;
- perubahan user/role.

## 11. UI/UX

Desain harus corporate dan profesional.

### Visual Direction

Gunakan:

- background putih;
- warna netral;
- typography yang bersih;
- spacing yang lega;
- card sederhana;
- border tipis;
- shadow ringan;
- komponen modern;
- icon yang konsisten;
- warna status yang informatif namun tetap profesional.

Hindari:

- gradient berlebihan;
- warna terlalu mencolok;
- animasi berlebihan;
- dashboard yang terlalu ramai;
- card dengan dekorasi berlebihan;
- desain seperti aplikasi consumer/social media.

Aplikasi harus terasa seperti enterprise internal application.

## 12. SIDEBAR

Gunakan sidebar pada layout utama.

Sidebar harus:

- dapat dibuka;
- dapat ditutup/collapse;
- memiliki icon;
- memiliki label;
- menunjukkan menu aktif;
- responsif;
- tetap nyaman digunakan pada desktop;
- memiliki state collapsed dan expanded.

Contoh menu:

Dashboard

Data Karyawan

Data MCU

Health Alerts / Follow-up

User Management

Settings

Profile

Menu harus ditampilkan berdasarkan role.

## 13. HEADER

Header dapat berisi:

- breadcrumb;
- nama halaman;
- search jika dibutuhkan;
- notification;
- user profile;
- role;
- logout.

Desain tetap minimal dan corporate.

## 14. COMPONENT SYSTEM

Gunakan shadcn/ui sebagai basis komponen.

Prioritaskan komponen:

- Button
- Input
- Select
- Combobox
- Dialog
- Drawer
- Dropdown
- Tabs
- Card
- Badge
- Table
- Pagination
- Tooltip
- Toast
- Alert
- Form
- Calendar/Date Picker
- Sheet

Pastikan komponen konsisten di seluruh aplikasi.

## 15. TABLE DESIGN

Tabel merupakan salah satu komponen utama aplikasi.

Tabel harus mendukung:

- sorting;
- searching;
- filtering;
- pagination;
- responsive behavior;
- row action;
- detail view;
- status badge.

Untuk data MCU yang banyak, jangan menampilkan terlalu banyak kolom sekaligus.

Gunakan:

- kolom utama;
- detail drawer/dialog untuk informasi lengkap.

## 16. HEALTH CLASSIFICATION UI

P1–P7 harus mudah dibaca.

Gunakan badge/status indicator.

Contoh konsep:

P1 → status terbaik

P2 → baik

P3 → perhatian ringan

P4 → perhatian

P5 → tindak lanjut

P6 → perhatian tinggi

P7 → perhatian sangat tinggi

Namun jangan mengklaim diagnosis medis dari kategori tersebut.

Warna/status harus tetap profesional dan memiliki aksesibilitas yang baik.

## 17. RESPONSIVE DESIGN

Aplikasi harus responsive.

Prioritas utama:

- desktop;
- laptop;
- tablet.

Mobile tetap harus dapat digunakan untuk fungsi dasar.

Sidebar harus berubah menjadi drawer pada layar kecil.

## 18. ROUTING

Gunakan route yang terstruktur.

Contoh:

/login

/dashboard

/employees

/employees/{id}

/mcu

/mcu/create

/mcu/{id}

/health-alerts

/follow-ups

/users

/profile

/settings

Route harus dilindungi berdasarkan authentication dan authorization.

## 19. ROLE-BASED ROUTING

Jangan hanya menyembunyikan menu frontend.

Authorization WAJIB diterapkan pada backend.

Contoh:

Karyawan tidak boleh mengakses:

`/mcu`

jika route tersebut digunakan untuk melihat seluruh data MCU.

Walaupun URL dimasukkan secara manual, backend harus tetap menolak akses.

## 20. API / DATA FLOW

Frontend Vue berkomunikasi dengan backend Laravel.

Gunakan struktur API yang rapi.

Contoh:

GET /api/dashboard

GET /api/employees

GET /api/employees/{id}

GET /api/mcu

POST /api/mcu

GET /api/mcu/{id}

PUT /api/mcu/{id}

GET /api/health-alerts

GET /api/follow-ups

POST /api/follow-ups

Endpoint final disesuaikan dengan implementasi.

Gunakan validation Laravel untuk seluruh input.

## 21. VALIDATION

Semua input harus divalidasi.

Validasi minimal:

- required fields;
- data type;
- date;
- numeric;
- email;
- unique constraint;
- foreign key;
- valid classification.

Gunakan Laravel Form Request apabila sesuai.

Frontend juga harus memberikan feedback validasi yang jelas.

## 22. SECURITY

Perhatikan:

- authentication;
- authorization;
- CSRF;
- validation;
- mass assignment protection;
- SQL injection protection;
- XSS protection;
- password hashing;
- secure session;
- route protection;
- API authorization;
- audit log.

Data kesehatan merupakan data sensitif sehingga akses harus dibatasi sesuai role.

Jangan mengekspos seluruh data kesehatan melalui endpoint yang dapat diakses karyawan biasa.

## 23. IMPORT DATA SQL

Buat proses import data dari:

`C:\Users\ferdy\OneDrive\Desktop\Project HSE\Data kesehatan para karyawan.sql`

Proses:

1. Backup data sumber.
2. Parse SQL.
3. Analisis struktur.
4. Mapping data.
5. Buat database target.
6. Import data.
7. Validasi jumlah record.
8. Validasi field penting.
9. Validasi bahwa tidak ada data sumber yang hilang.
10. Buat laporan hasil import.

Jika memungkinkan, buat command Laravel seperti:

`php artisan data:import-health`

atau mekanisme import yang sesuai.

Import harus dapat dijalankan kembali dengan aman tanpa menghasilkan duplikasi.

## 24. DATA INTEGRITY CHECK

Setelah import:

Bandingkan:

- jumlah karyawan;
- jumlah MCU;
- jumlah record pemeriksaan;
- NIK/employee ID;
- nama;
- tanggal pemeriksaan;
- hasil pemeriksaan;
- klasifikasi P1–P7.

Pastikan data target sesuai data sumber.

Jika ada transformasi struktur, buat mapping yang jelas.

## 25. SEEDING

Gunakan database seeder untuk:

- roles;
- admin;
- HSE;
- dokter;
- sample user jika diperlukan untuk development.

Jangan membuat data kesehatan fiktif apabila data sumber sudah tersedia.

Untuk development, data dummy harus diberi penanda yang jelas dan tidak boleh tercampur dengan data produksi.

## 26. DASHBOARD ANALYTICS

Dashboard harus menghasilkan insight berdasarkan data aktual.

Contoh:

- total karyawan;
- total MCU;
- jumlah P1;
- jumlah P2;
- jumlah P3;
- jumlah P4;
- jumlah P5;
- jumlah P6;
- jumlah P7;
- persentase distribusi P1–P7;
- jumlah karyawan dengan alert;
- jumlah follow-up;
- jumlah follow-up open;
- jumlah follow-up selesai.

Gunakan chart jika benar-benar membantu.

Jangan membuat chart hanya sebagai dekorasi.

## 27. HEALTH ALERT LOGIC

Buat service khusus untuk menentukan apakah suatu hasil MCU perlu masuk ke daftar perhatian.

Contoh:

`HealthAlertService`

Service membaca data MCU dan klasifikasi kesehatan.

Jangan menaruh seluruh business logic di controller.

Gunakan service class agar mudah dikembangkan.

Contoh konsep:

MCU → Evaluasi hasil → Health classification → Jika memenuhi aturan → Health Alert → Follow-up

Aturan final harus mengikuti data dan business rule yang tersedia.

Jangan mengarang aturan medis.

## 28. ARCHITECTURE

Gunakan arsitektur yang bersih dan mudah dikembangkan.

Pisahkan:

- Controllers
- Models
- Requests
- Services
- Policies
- Resources
- Repositories jika benar-benar diperlukan
- Vue pages
- Vue components
- composables
- stores
- API services

Hindari controller yang terlalu besar.

Business logic jangan ditaruh seluruhnya di frontend.

## 29. FRONTEND STRUCTURE

Struktur Vue harus rapi.

Contoh konseptual:

resources/js/

components/

layouts/

pages/

composables/

services/

stores/

types/

utils/

Gunakan reusable components.

Contoh:

DataTable

StatusBadge

HealthClassificationBadge

DashboardCard

PageHeader

Sidebar

Topbar

ConfirmDialog

McuForm

EmployeeProfile

McuHistory

FollowUpForm

## 30. STATE MANAGEMENT

Jika state global dibutuhkan, gunakan state management yang sesuai dengan stack Vue yang digunakan.

Gunakan untuk:

- authenticated user;
- role;
- permissions;
- sidebar state;
- notification;
- filter state jika diperlukan.

Jangan menjadikan global state sebagai tempat seluruh data aplikasi.

## 31. ERROR HANDLING

Buat error handling yang jelas.

Contoh:

- 401 Unauthorized
- 403 Forbidden
- 404 Not Found
- 422 Validation Error
- 500 Server Error

Frontend harus memberikan feedback yang mudah dipahami.

## 32. EMPTY STATES

Setiap halaman harus memiliki empty state.

Contoh:

Belum ada data MCU.

Belum ada health alert.

Belum ada tindak lanjut.

Jangan menampilkan tabel kosong tanpa informasi.

## 33. LOADING STATES

Gunakan loading state untuk:

- dashboard;
- tabel;
- detail;
- submit form;
- update;
- delete;
- API request.

Hindari UI terasa macet saat menunggu response.

## 34. NOTIFICATION

Gunakan toast/notification untuk:

- data berhasil disimpan;
- data berhasil diperbarui;
- data berhasil dihapus;
- error;
- validation failure;
- unauthorized action.

## 35. ACCESS CONTROL MATRIX

Buat matrix hak akses seperti berikut:

| Fitur | Admin | HSE | Dokter | Karyawan |
|---|---|---|---|---|
| Dashboard | Ya | Ya | Ya | Ya |
| Lihat seluruh karyawan | Ya | Ya | Ya | Tidak |
| Lihat data sendiri | Ya | Ya | Ya | Ya |
| Input MCU | Ya* | Ya | Ya | Tidak |
| Edit MCU | Ya* | Ya | Ya | Tidak |
| Lihat seluruh MCU | Ya | Ya | Ya | Tidak |
| Lihat MCU sendiri | Ya | Ya | Ya | Ya |
| Health Alert | Ya | Ya | Ya | Data sendiri bila diperlukan |
| Follow-up | Ya | Ya | Ya | Data sendiri bila diperlukan |
| User Management | Ya | Tidak | Tidak | Tidak |
| Role Management | Ya | Tidak | Tidak | Tidak |
| System Settings | Ya | Terbatas | Terbatas | Tidak |

`Ya*` berarti sesuai kebijakan sistem yang diterapkan.

## 36. DESIGN LANGUAGE

Gunakan design language:

"Corporate Healthcare / HSE Enterprise Dashboard"

Karakter:

- clean;
- professional;
- formal;
- white-first;
- minimal;
- information-dense tetapi tetap mudah dibaca;
- modern enterprise;
- accessible.

Jangan menggunakan desain yang terlalu futuristik.

## 37. HALAMAN MINIMAL

Implementasikan minimal halaman:

1. Login
2. Dashboard
3. Data Karyawan
4. Detail Karyawan
5. Data MCU
6. Detail MCU
7. Input MCU
8. Edit MCU
9. Health Alerts
10. Follow-up
11. User Management
12. User Detail/Create/Edit
13. Profile
14. Settings jika diperlukan

## 38. ACCEPTANCE CRITERIA

Aplikasi dianggap berhasil jika:

### Authentication

- user dapat login;
- user diarahkan ke dashboard sesuai role;
- user dapat logout;
- route terlindungi.

### Authorization

- Admin memiliki akses administrasi;
- HSE dapat mengelola data MCU;
- dokter dapat mengelola data MCU sesuai hak akses;
- karyawan hanya melihat datanya sendiri;
- backend tetap melakukan authorization meskipun URL dimasukkan manual.

### Data

- seluruh data dari SQL sumber tetap tersedia;
- tidak ada data sumber yang hilang;
- tidak ada data sumber yang diubah tanpa alasan transformasi struktur;
- data dapat dicari dan difilter.

### MCU

- HSE dapat input MCU;
- dokter dapat input MCU;
- data MCU dapat dilihat;
- histori MCU tersedia;
- data dapat difilter.

### Health Alert

- sistem dapat mengidentifikasi data yang masuk kategori perhatian berdasarkan business rule;
- daftar karyawan tampil;
- status tindak lanjut dapat dicatat;
- histori tindak lanjut tersedia.

### Dashboard

- dashboard menampilkan data aktual;
- statistik sesuai database;
- tidak ada angka hard-coded.

### UI

- responsive;
- sidebar dapat collapse/expand;
- konsisten;
- corporate;
- white-first;
- profesional;
- menggunakan shadcn/ui;
- tidak ada komponen visual yang tidak diperlukan.

## 39. DEVELOPMENT WORKFLOW

Jangan langsung menulis seluruh aplikasi tanpa analisis.

Ikuti tahapan berikut:

### PHASE 1 — ANALYSIS

1. Periksa project yang tersedia.
2. Identifikasi framework dan dependency.
3. Periksa struktur Laravel.
4. Periksa struktur Vue.
5. Periksa Tailwind.
6. Periksa konfigurasi database.
7. Baca file SQL sumber.
8. Analisis seluruh struktur data.

Pada tahap ini jangan merusak kode yang sudah ada.

### PHASE 2 — DATABASE DESIGN

1. Buat data mapping.
2. Tentukan entity.
3. Tentukan relationship.
4. Tentukan migration.
5. Tentukan index.
6. Tentukan constraint.
7. Tentukan seeder/importer.
8. Pastikan seluruh data sumber terakomodasi.

### PHASE 3 — BACKEND

Implementasikan:

- authentication;
- authorization;
- models;
- migrations;
- policies;
- form requests;
- services;
- controllers;
- API/resources;
- import system;
- health alert logic;
- follow-up logic;
- audit trail.

### PHASE 4 — FRONTEND

Implementasikan:

- layout;
- sidebar;
- header;
- login;
- dashboard;
- employee pages;
- MCU pages;
- health alert;
- follow-up;
- user management;
- profile;
- reusable components.

### PHASE 5 — INTEGRATION

Hubungkan Vue dengan Laravel.

Pastikan:

- authentication berjalan;
- API berjalan;
- authorization berjalan;
- data aktual muncul;
- filtering berjalan;
- pagination berjalan;
- CRUD berjalan.

### PHASE 6 — TESTING

Test:

- login;
- logout;
- role;
- authorization;
- CRUD;
- import;
- search;
- filter;
- pagination;
- dashboard;
- health alert;
- follow-up;
- responsive UI.

### PHASE 7 — DATA VALIDATION

Bandingkan database hasil import dengan file SQL sumber.

Pastikan:

`Source Data Count == Imported Data Count`

untuk setiap entitas yang relevan.

## 40. ATURAN IMPLEMENTASI AI AGENT

Saat mengerjakan project:

1. Jangan menghapus kode existing tanpa alasan.
2. Jangan melakukan refactor besar tanpa kebutuhan.
3. Jangan mengganti framework.
4. Jangan mengganti Laravel.
5. Jangan mengganti Vue.js.
6. Jangan mengganti Tailwind CSS.
7. Gunakan shadcn/ui.
8. Pertahankan SPA architecture.
9. Jangan membuat data kesehatan fiktif.
10. Jangan mengubah data SQL sumber.
11. Jangan membuat business rule medis berdasarkan asumsi.
12. Jangan hard-code statistik dashboard.
13. Jangan hard-code daftar karyawan.
14. Gunakan database sebagai source of truth.
15. Gunakan authorization backend.
16. Jangan hanya mengandalkan hiding menu di frontend.
17. Gunakan reusable components.
18. Hindari duplicate code.
19. Gunakan clean naming.
20. Gunakan migrations.
21. Gunakan validation.
22. Gunakan service untuk business logic kompleks.
23. Gunakan policy/middleware untuk authorization.
24. Pastikan aplikasi dapat dijalankan setelah setiap tahap.
25. Setelah perubahan besar, jalankan test/build/lint yang tersedia.

## 41. PRIORITAS PENGERJAAN

Prioritas implementasi:

P0 — Wajib:

- database;
- authentication;
- roles;
- authorization;
- employee data;
- MCU data;
- dashboard;
- health classification P1–P7;
- health alert;
- follow-up;
- UI layout;
- sidebar;
- responsive design.

P1:

- audit log;
- advanced filtering;
- dashboard charts;
- activity history;
- export/report jika struktur data sudah stabil.

P2:

- fitur tambahan yang dapat dikembangkan kemudian.

Jangan mengerjakan P1/P2 sebelum P0 stabil.

## 42. OUTPUT YANG SAYA INGINKAN DARI AI AGENT

Sebelum coding, berikan:

1. Analisis project existing.
2. Analisis file SQL.
3. Data dictionary.
4. Entity relationship.
5. Database mapping.
6. Rencana migration.
7. Rencana architecture.
8. Rencana route.
9. Rencana API.
10. Rencana UI.
11. Access control matrix.
12. Implementation plan.

Setelah itu mulai implementasi secara bertahap.

Untuk setiap fase:

- jelaskan perubahan;
- implementasikan;
- jalankan test/build;
- perbaiki error;
- lanjut ke fase berikutnya.

Jangan berhenti hanya pada dokumentasi.

Tujuan akhir adalah aplikasi yang benar-benar dapat dijalankan.

## 43. HASIL AKHIR YANG DIHARAPKAN

Hasil akhir berupa aplikasi web internal:

**HSE Employee Health & MCU Management System**

dengan karakter:

- Laravel backend;
- Vue.js frontend;
- Tailwind CSS;
- shadcn/ui;
- SPA;
- white corporate interface;
- collapsible sidebar;
- four roles;
- employee management;
- MCU management;
- health classification P1–P7;
- health alert;
- follow-up management;
- dashboard;
- authentication;
- authorization;
- auditability;
- data berasal dari SQL yang diberikan;
- data source tetap utuh;
- struktur database dirapikan tanpa mengubah substansi data.

Mulai dengan PHASE 1.

Jangan membuat asumsi mengenai struktur data sebelum membaca:

`C:\Users\ferdy\OneDrive\Desktop\Project HSE\Data kesehatan para karyawan.sql`

Baca file tersebut terlebih dahulu, kemudian laporkan hasil analisis struktur data dan mapping database sebelum membuat migration final.