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