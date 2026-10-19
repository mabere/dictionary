# Keputusan Ortografi Tolaki

## Status

- Status: draf untuk ditinjau editor/penutur
- Rujukan awal: *Kamus Tolaki–Indonesia* (1985), bagian Petunjuk Pemakaian
- Versi: 0.1 (inventaris awal)

## Konvensi yang tercatat dalam rujukan 1985

- Vokal pendek yang disebut: `a, i, u, e, o`.
- Vokal panjang ditulis rangkap: `aa, ii, uu, ee, oo`.
- Glotal hambat dilambangkan dengan tanda apostrof (`'`).
- Bentuk pranasal `mb`, `nd`, dan `ngg` dibahas dalam urutan alfabet di bawah M/N.
- Urutan huruf awal yang dicantumkan: `A, B, D, E, G, H, I, K, L, M, N, O, P, R, S, T, U, W`.
- Tanda hubung digunakan pada unsur reduplikasi dalam contoh panduan.

## Aturan sementara aplikasi

1. Jangan menghapus apostrof, tanda hubung, atau penggandaan vokal dari bentuk tampilan.
2. Simpan bentuk sumber terpisah dari nilai normalisasi yang dipakai untuk pencarian.
3. Jangan otomatis menganggap ejaan yang berbeda sebagai bentuk sama.
4. Untuk urutan huruf selain alfabet Latin umum, gunakan konfigurasi urutan khusus, bukan asumsi sorting bawaan.
5. Simpan varian sebagai bentuk terhubung ke lema rujuk; jangan menimpa bentuk sumber.

## Keputusan terbuka

- Verifikasi urutan alfabet, khususnya karakter yang pada teks OCR PDF tampak tidak konsisten.
- Tetapkan perlakuan pengurutan `mb`, `nd`, dan `ngg` serta posisi apostrof.
- Tentukan apakah normalisasi pencarian boleh menyediakan pencarian toleran (mis. tanpa tanda hubung) sebagai jalur tambahan; hasil tetap harus menampilkan ejaan baku.
- Sahkan konvensi modern melalui editor/penutur dan catat perubahan beserta alasan, tanggal, serta versi.

Dokumen ini tidak menetapkan bahwa konvensi 1985 adalah standar mutakhir. Setiap perubahan editorial perlu memiliki bukti dan pemilik keputusan.

## Rujukan terpisah: morfologi verba (1983)

**Pattiasina, J.F., Abdul Muthalib, Haloma Balaks, Abdurrauf Tarimana, M. Mattalitti, dan Abd. Kadir Mulya. 1983. _Sistem Morfologi Kata Kerja Bahasa Tolaki_. Jakarta: Pusat Pembinaan dan Pengembangan Bahasa, Departemen Pendidikan dan Kebudayaan.** Salinan digital: `public/sumber 2 - SISTEM MORFOLOGI KATA KERJA BAHASA TOLAKI.pdf`.

Rujukan ini merupakan deskripsi morfologi, bukan pedoman ejaan. Penelitian menggunakan kerangka linguistik struktural dan melaporkan data dialek Konawe dari Unaaha menurut penamaan lokasi pada masa terbitnya. Cakupannya meliputi verba dasar dan turunan, afiksasi, reduplikasi, pemajemukan, serta pembentukan dan makna verba.

Inventaris afiks verba yang dilaporkan meliputi:

- Prefiks: `me-`, `pe-`, `mbe-`, `mo-`, `mbo-`, `poko-`, `ni-`, `in-`.
- Infiks: `-um-`, `-in-`.
- Sufiks: `-i`, `-ako`, `-kee`.
- Prefiks rangkap: `momboko-`, `pinoko-`.
- Konfiks: `-um-…-i`, `me-…-i`, `me-…-ako`, `-um-…-ikee`, `in-…-ako`, `podo-…-i`.
- Sufiks rangkap: `-ikee`, `-ito`.

> Sumber menyebut `mbe-` sebagai pengecualian dari afiks yang berfungsi membentuk kata kerja. Daftar di atas adalah inventaris yang dilaporkan, bukan klaim bahwa semua afiks masih produktif, setara secara fungsi, berlaku lintas dialek, atau menjadi standar bahasa kontemporer. Rincian fungsi dan makna harus diperiksa pada contoh serta uraian buku sebelum analisis lema diputuskan.

Di aplikasi, rujukan ini disimpan sebagai **profil morfologi verba** tersendiri dan tidak ditawarkan sebagai pilihan profil ortografi lema. Analisis morfologi pada bentuk turunan tetap perlu menyimpan segmentasi, lema dasar, sumber, status tinjauan, dan catatan editor secara per entri.
