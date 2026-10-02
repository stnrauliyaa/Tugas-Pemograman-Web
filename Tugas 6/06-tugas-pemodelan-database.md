# Tugas Mandiri Modul 6 – Perancangan ERD E-Library Kampus

| Keterangan | Isi |
|---|---|
| Nama | Siti Nurauliya |
| NIM | D121241003 |
| Mata Kuliah | Pemrograman Web |
| Topik | Pemodelan Data dan Basis Data Relasional |

---

## 1. Deskripsi Skenario

Sistem **E-Library Kampus** digunakan untuk mengelola peminjaman buku perpustakaan. Sistem mencatat:

- data **mahasiswa** sebagai peminjam,
- data **buku** beserta **penerbitnya**,
- **riwayat peminjaman dan pengembalian** buku.

Aturan bisnis yang digunakan:

1. Satu mahasiswa dapat melakukan banyak transaksi peminjaman.
2. Satu transaksi peminjaman dapat memuat satu atau lebih buku.
3. Satu buku dapat dipinjam berkali-kali pada transaksi yang berbeda.
4. Satu penerbit dapat menerbitkan banyak buku, dan satu buku hanya memiliki satu penerbit.
5. Pengembalian dicatat per buku (setiap buku bisa dikembalikan pada tanggal berbeda) beserta denda keterlambatan jika ada.
6. Status buku pada transaksi: `Dipinjam` (belum kembali), `Dikembalikan` (kembali tepat waktu), `Terlambat` (kembali melewati jatuh tempo).
7. Denda keterlambatan dihitung **Rp1.000 per hari per buku** sejak lewat tanggal jatuh tempo (contoh: buku B02 terlambat 2 hari, denda Rp2.000).

**Catatan penulisan:** pada seluruh dokumen, **huruf tebal** menandai Primary Key (PK), *huruf miring* menandai Foreign Key (FK), dan ***tebal-miring*** menandai atribut yang sekaligus PK dan FK.

---


## 2. Identifikasi Entitas dan Atribut

### 2.1 Mahasiswa
| Atribut | Keterangan | Kunci |
|---|---|---|
| nim | Nomor induk mahasiswa | **PK** |
| nama_mahasiswa | Nama lengkap | |
| program_studi | Program studi | |
| email | Email mahasiswa | |
| no_telepon | Nomor telepon | |

### 2.2 Penerbit
| Atribut | Keterangan | Kunci |
|---|---|---|
| id_penerbit | Identitas penerbit | **PK** |
| nama_penerbit | Nama penerbit | |
| alamat_penerbit | Alamat penerbit | |
| kota_penerbit | Kota penerbit | |
| telepon_penerbit | Telepon penerbit | |

### 2.3 Buku
| Atribut | Keterangan | Kunci |
|---|---|---|
| id_buku | Kode unik buku | **PK** |
| isbn | Nomor ISBN | |
| judul_buku | Judul buku | |
| pengarang | Nama pengarang | |
| tahun_terbit | Tahun terbit | |
| stok | Jumlah eksemplar tersedia | |
| id_penerbit | Penerbit buku | **FK** → Penerbit |

### 2.4 Transaksi Peminjaman
Entitas ini diwujudkan dalam dua tabel: **Peminjaman** (header) dan **Detail_Peminjaman** (rincian buku), karena hubungan antara transaksi dan buku adalah *many-to-many* (satu transaksi memuat banyak buku, satu buku dipinjam di banyak transaksi).

**Peminjaman**

| Atribut | Keterangan | Kunci |
|---|---|---|
| id_peminjaman | Nomor transaksi | **PK** |
| nim | Mahasiswa peminjam | **FK** → Mahasiswa |
| tanggal_pinjam | Tanggal transaksi | |
| tanggal_jatuh_tempo | Batas pengembalian | |

**Detail_Peminjaman**

| Atribut | Keterangan | Kunci |
|---|---|---|
| id_peminjaman | Nomor transaksi | **PK, FK** → Peminjaman |
| id_buku | Buku yang dipinjam | **PK, FK** → Buku |
| tanggal_kembali | Tanggal buku dikembalikan (NULL jika belum) | |
| denda | Denda keterlambatan | |
| status | Dipinjam / Dikembalikan / Terlambat | |

---


## 3. Simulasi Normalisasi

Seluruh tahap normalisasi memakai **data contoh yang sama** dengan data pada tabel akhir (bagian 7).

### 3.1 UNF (Unnormalized Form)

Data mentah dari formulir peminjaman. Terdapat **kelompok data berulang** (data buku dan penerbit, ditandai `{ }`).

```
Peminjaman(
  id_peminjaman, tanggal_pinjam, tanggal_jatuh_tempo,
  nim, nama_mahasiswa, program_studi, email, no_telepon,
  { id_buku, isbn, judul_buku, pengarang, tahun_terbit, stok,
    id_penerbit, nama_penerbit, alamat_penerbit, kota_penerbit, telepon_penerbit,
    tanggal_kembali, denda, status }
)
```

Contoh data UNF:

| id_peminjaman | tanggal_pinjam | tanggal_jatuh_tempo | nim | nama_mahasiswa | program_studi | buku yang dipinjam |
|---|---|---|---|---|---|---|
| 1 | 2026-09-01 | 2026-09-08 | 2401001 | Andi Saputra | Informatika | B01 Basis Data (penerbit 1: Penerbit Informatika, Bandung); B02 Algoritma dan Pemrograman (penerbit 2: Erlangga, Jakarta) |
| 2 | 2026-09-03 | 2026-09-10 | 2401002 | Siti Rahma | Sistem Informasi | B01 Basis Data (penerbit 1: Penerbit Informatika, Bandung) |

Masalah: satu sel berisi lebih dari satu buku (*repeating group*).

### 3.2 1NF (First Normal Form)

Syarat: setiap atribut **atomik** dan **tidak ada kelompok berulang**. Setiap buku dalam satu transaksi dijadikan baris tersendiri, sehingga Primary Key menjadi **(id_peminjaman, id_buku)**.

Skema 1NF:

```
Peminjaman_1NF(
  id_peminjaman (PK), id_buku (PK),
  tanggal_pinjam, tanggal_jatuh_tempo,
  nim, nama_mahasiswa, program_studi, email, no_telepon,
  isbn, judul_buku, pengarang, tahun_terbit, stok,
  id_penerbit, nama_penerbit, alamat_penerbit, kota_penerbit, telepon_penerbit,
  tanggal_kembali, denda, status
)
```

Contoh data 1NF (bagian 1 dari 3: data transaksi dan mahasiswa):

| **id_peminjaman** | **id_buku** | tanggal_pinjam | tanggal_jatuh_tempo | nim | nama_mahasiswa | program_studi | email | no_telepon |
|---|---|---|---|---|---|---|---|---|
| 1 | B01 | 2026-09-01 | 2026-09-08 | 2401001 | Andi Saputra | Informatika | andi@kampus.ac.id | 081234567001 |
| 1 | B02 | 2026-09-01 | 2026-09-08 | 2401001 | Andi Saputra | Informatika | andi@kampus.ac.id | 081234567001 |
| 2 | B01 | 2026-09-03 | 2026-09-10 | 2401002 | Siti Rahma | Sistem Informasi | siti@kampus.ac.id | 081234567002 |

Bagian 2 dari 3: data buku dan penerbit (baris sama, urutan sama):

| **id_peminjaman** | **id_buku** | isbn | judul_buku | pengarang | tahun_terbit | stok | id_penerbit | nama_penerbit | alamat_penerbit | kota_penerbit | telepon_penerbit |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 1 | B01 | 9786020000011 | Basis Data | Rahmat Hidayat | 2020 | 5 | 1 | Penerbit Informatika | Jl. Geger Kalong No. 10 | Bandung | 022-1234567 |
| 1 | B02 | 9786020000028 | Algoritma dan Pemrograman | Dewi Lestari | 2019 | 3 | 2 | Erlangga | Jl. H. Baping Raya No. 100 | Jakarta | 021-7654321 |
| 2 | B01 | 9786020000011 | Basis Data | Rahmat Hidayat | 2020 | 5 | 1 | Penerbit Informatika | Jl. Geger Kalong No. 10 | Bandung | 022-1234567 |

Bagian 3 dari 3: data pengembalian:

| **id_peminjaman** | **id_buku** | tanggal_kembali | denda | status |
|---|---|---|---|---|
| 1 | B01 | 2026-09-07 | 0 | Dikembalikan |
| 1 | B02 | 2026-09-10 | 2000 | Terlambat |
| 2 | B01 | NULL | 0 | Dipinjam |

Masalah yang masih ada: **redundansi**. Data Andi Saputra tertulis 2 kali, data buku B01 dan Penerbit Informatika juga tertulis 2 kali. Hal ini rawan anomali *insert*, *update*, dan *delete*.


### 3.3 2NF (Second Normal Form)

Syarat: sudah 1NF dan **tidak ada ketergantungan parsial** (atribut non-kunci bergantung pada sebagian kunci komposit saja).

Analisis ketergantungan fungsional terhadap PK **(id_peminjaman, id_buku)**:

| Ketergantungan | Jenis |
|---|---|
| id_peminjaman → tanggal_pinjam, tanggal_jatuh_tempo, nim, nama_mahasiswa, program_studi, email, no_telepon | Parsial |
| id_buku → isbn, judul_buku, pengarang, tahun_terbit, stok, id_penerbit, nama_penerbit, alamat_penerbit, kota_penerbit, telepon_penerbit | Parsial |
| (id_peminjaman, id_buku) → tanggal_kembali, denda, status | Penuh |

Hasil dekomposisi 2NF:

- **Peminjaman**(**id_peminjaman**, tanggal_pinjam, tanggal_jatuh_tempo, nim, nama_mahasiswa, program_studi, email, no_telepon)
- **Buku**(**id_buku**, isbn, judul_buku, pengarang, tahun_terbit, stok, id_penerbit, nama_penerbit, alamat_penerbit, kota_penerbit, telepon_penerbit)
- **Detail_Peminjaman**(***id_peminjaman***, ***id_buku***, tanggal_kembali, denda, status)

Setelah 2NF, data Andi Saputra cukup tertulis sekali per transaksi dan data buku B01 hanya satu baris. Namun masih ada redundansi data mahasiswa (jika Andi punya banyak transaksi) dan data penerbit (jika penerbit punya banyak buku).

### 3.4 3NF (Third Normal Form)

Syarat: sudah 2NF dan **tidak ada ketergantungan transitif** (atribut non-kunci bergantung pada atribut non-kunci lain).

| Ketergantungan transitif | Penyelesaian |
|---|---|
| id_peminjaman → nim → nama_mahasiswa, program_studi, email, no_telepon | Pisahkan tabel **Mahasiswa**; `nim` tetap di Peminjaman sebagai FK |
| id_buku → id_penerbit → nama_penerbit, alamat_penerbit, kota_penerbit, telepon_penerbit | Pisahkan tabel **Penerbit**; `id_penerbit` tetap di Buku sebagai FK |

Hasil akhir **3NF**:

- **Mahasiswa**(**nim**, nama_mahasiswa, program_studi, email, no_telepon)
- **Penerbit**(**id_penerbit**, nama_penerbit, alamat_penerbit, kota_penerbit, telepon_penerbit)
- **Buku**(**id_buku**, isbn, judul_buku, pengarang, tahun_terbit, stok, *id_penerbit*)
- **Peminjaman**(**id_peminjaman**, *nim*, tanggal_pinjam, tanggal_jatuh_tempo)
- **Detail_Peminjaman**(***id_peminjaman***, ***id_buku***, tanggal_kembali, denda, status)

Setiap atribut non-kunci kini hanya bergantung pada kunci tabelnya, sehingga tidak ada redundansi dan anomali data.

---

