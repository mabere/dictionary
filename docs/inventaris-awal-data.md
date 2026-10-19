# Inventaris Awal Data Kamus

## Status pemeriksaan

Tanggal pemeriksaan: 2026-10-17 (waktu proyek, Asia/Makassar).

Database SQLite proyek dapat dibaca dengan mengaktifkan ekstensi SQLite yang tersedia secara sementara pada perintah CLI; konfigurasi PHP tidak diubah. Inventaris berikut adalah kondisi lokal saat pemeriksaan, bukan klaim tentang database produksi.

## Database SQLite lokal

Database lokal lama: `database/database.sqlite`. Saat audit awal dibaca secara read-only dengan Laravel `db:show --counts`.

| Tabel | Baris |
|---|---:|
| `dictionary_entries` | 0 |
| `example_sentences` | 0 |
| `dictionary_categories` | 0 |
| `dictionary_visits` | 1 |
| `users` | 16 |

Saat audit awal terdapat 13 tabel dan 11 catatan migrasi. Karena tabel entri kamus kosong, baseline kualitas korpus aktif tidak dapat dihitung dari SQLite tersebut.

## Database MySQL sasaran setelah migrasi awal

Database: `laravel_kamus_multibahasa`. Enam migrasi leksikografi dijalankan setelah migrasi dasar; total 26 tabel kini tersedia. Hitungan dibaca secara read-only melalui koneksi Laravel.

| Tabel inti | Baris setelah migrasi |
|---|---:|
| `dictionary_entries` (legacy) | 0 |
| `dictionary_lemmas` | 0 |
| `dictionary_senses` | 0 |
| `dictionary_translations` | 0 |
| `dictionary_examples` | 0 |
| `orthography_profiles` | 1 (profil kandidat 1985, status draf) |
| `dictionary_categories` | 6 (kategori awal dari seeder aplikasi) |

Status migrasi: seluruh migrasi sampai `2026_10_17_000006` tercatat berhasil. Sinkronisasi legacy dalam mode dry-run menghitung 0 baris dan tidak menulis data.

## CSV contoh lokal

Berkas: `database/imports/contoh-lembar-kamus-tolaki.csv`  
Jumlah baris data: 12.

| Field | Terisi | Catatan |
|---|---:|---|
| Kata Tolaki | 12/12 | Ada setidaknya satu bentuk yang berisi koma; perlu penilaian apakah varian atau lema majemuk |
| Padanan Indonesia | 12/12 | Belum menilai akurasi linguistik |
| Padanan Inggris | 9/12 | Tiga baris kosong |
| Kelas kata | 10/12 | Dua baris kosong |
| Fonetik | 0/12 | Belum terisi |
| Definisi | 0/12 | Belum terisi |
| Catatan penggunaan | 0/12 | Belum terisi |
| Catatan budaya | 0/12 | Belum terisi |
| Contoh Tolaki | 12/12 | Hubungan ke makna perlu ditinjau |
| Terjemahan contoh Indonesia | 12/12 | Belum menilai kesepadanan |
| Terjemahan contoh Inggris | 0/12 | Belum terisi |

Seluruh 12 baris berstatus `draft`. Sampel kecil ini menunjukkan skema CSV dapat membawa kolom tiga bahasa, tetapi belum cukup untuk menilai cakupan atau mutu korpus.

## Database dan dump SQL

- `database/database.sqlite` berhasil dibaca dengan ekstensi SQLite dimuat sementara; tidak ada perubahan pada file tersebut.
- Database MySQL sasaran telah dimigrasikan; semua tabel legacy dan leksikografi masih kosong.
- Jumlah baris `INSERT` pada ketiga dump telah dihitung secara statis: masing-masing memiliki 8 kategori, 34 entri, dan 34 contoh. `mysql-data-import.sql` dan `mysql-full-import.sql` juga memiliki 136 kunjungan dan 4 pengguna; dump dictionary-only tidak menyertakan tabel tersebut. Hitungan ini belum membuktikan dump dapat diimpor ke skema aplikasi saat ini.
- Dump-dump tersebut berbeda cakupan (termasuk pengguna dan kunjungan) dan harus diperlakukan sebagai sumber terpisah sampai versi skema, tanggal, serta sumber kebenarannya dipastikan.
- Jangan menjalankan migrasi terhadap database proyek/produksi sebagai bagian inventaris ini.

## Tindak lanjut baseline

1. Tentukan apakah salah satu dump SQL merupakan sumber korpus aktif dan cocokkan komentar hitungan dengan jumlah baris INSERT.
2. Ambil backup read-only dari sumber yang disahkan dan rekam checksum serta waktu pengambilan.
3. Hitung entri per status, kelengkapan field, contoh, audio, kategori, kelas kata, duplikasi kandidat, dan bentuk dengan tanda baca khusus.
4. Cocokkan hitungan database dengan dump SQL dan CSV hanya setelah sumber kebenaran ditetapkan.
5. Simpan anomali sebagai daftar review, bukan koreksi otomatis.
