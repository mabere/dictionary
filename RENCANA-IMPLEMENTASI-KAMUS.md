# Rencana Implementasi Penguatan Kamus Tolakiâ€“Indonesiaâ€“Inggris

Dokumen ini menerjemahkan hasil audit aplikasi menjadi backlog yang dapat direncanakan, dikerjakan, dan ditinjau. Fokusnya adalah menjadikan aplikasi sebagai kamus akademik yang dapat dipertanggungjawabkan secara linguistik sekaligus tetap mudah dipakai.

## 1. Tujuan dan prinsip rancangan

### Tujuan produk

- Menyajikan Tolaki sebagai bahasa utama dengan padanan Indonesia dan Inggris.
- Mewakili polisemi, homonimi, variasi bentuk, dan padanan jamak tanpa menyatukan makna yang berbeda.
- Menyediakan pencarian tiga arah yang hasilnya menjelaskan asal bahasa kueri.
- Menjaga keterlacakan sumber data, bukti, dan keputusan editorial.
- Membuat data bisa diekspor dan dipakai untuk penelitian dengan struktur yang stabil.

### Prinsip leksikografi

1. **Bedakan lema dan makna.** Satu lema dapat memiliki beberapa makna; tiap makna memiliki definisi, kelas kata, label penggunaan, dan padanannya sendiri.
2. **Padanan tidak selalu ekuivalen satu-ke-satu.** Simpan lebih dari satu padanan dan izinkan keterangan cakupan atau konteks.
3. **Contoh adalah bukti penggunaan.** Simpan contoh Tolaki bersama terjemahan, sumber, dan keterkaitannya dengan makna.
4. **Pisahkan fakta bahasa dan keputusan editorial.** Catat sumber, penutur/informan, reviewer, tanggal, serta status validasi.
5. **Dokumentasikan konvensi.** Ortografi, transkripsi fonetik, label kelas kata, dialek, dan ragam harus mengikuti panduan yang disepakati tim.
6. **Jangan mengarang kepastian.** Data yang belum tervalidasi diberi status atau label yang jelas, bukan diperlakukan sebagai fakta final.

## 2. Ruang lingkup dan prioritas

| Prioritas | Area | Hasil utama | Urutan |
|---|---|---|---|
| P0 | Tata kelola linguistik | Panduan ortografi, label, sumber, dan review | Sebelum migrasi korpus |
| P0 | Model data | Lema, makna, padanan, contoh, varian, sumber | Fondasi fitur |
| P0 | Migrasi dan audit data | Data lama dipetakan, masalah tercatat, tidak hilang | Setelah model disepakati |
| P1 | Pencarian dan tampilan publik | Pencarian Tolaki/Indonesia/Inggris dan hasil yang jelas | Setelah model data |
| P1 | Editorial/backoffice | Review berbukti, antrian, validasi, riwayat | Bersamaan fitur pencarian |
| P2 | Korpus dan ekspor akademik | Ekspor terstruktur, dokumentasi dataset dan statistik | Setelah alur stabil |
| P2 | Aksesibilitas dan kinerja | Navigasi keyboard, indeks, optimasi kueri | Iteratif |

## 3. Keputusan akademik yang perlu ditetapkan

Bagian ini menggunakan *Kamus Tolaki–Indonesia* (Abdul Muthalib, Alimuddin D.P., Husen Chalik, dan Arsamid; Pusat Pembinaan dan Pengembangan Bahasa, 1985) sebagai **rujukan deskriptif dan titik awal konvensi**, bukan sebagai standar yang otomatis mengikat seluruh data baru. Kata pengantar kamus tersebut menyatakan bahwa inventarisnya belum lengkap. Karena itu, kebijakan produk perlu mencatat apakah konvensi 1985 dipertahankan, diperbarui, atau diberi label historis berdasarkan kajian dan konsultasi penutur/editor.

### Konvensi yang dapat dijadikan dasar kerja

| Aspek | Keterangan dari panduan 1985 | Keputusan implementasi yang disarankan |
|---|---|---|
| Arah dan cakupan kamus | Kamus Tolaki–Indonesia menginventarisasi kata dan ungkapan Tolaki beserta makna Indonesianya | Pertahankan Tolaki sebagai lema utama; tambahkan Inggris sebagai lapisan padanan baru yang diberi sumber/reviewer tersendiri |
| Urutan alfabet | Panduan menyebut urutan huruf awal khusus: A, B, D, E, G, H, I, K, L, M, N, O, P, R, S, T, U, W | Gunakan urutan Tolaki sebagai indeks utama yang dapat dikonfigurasi; jangan mengasumsikan A–Z. Verifikasi urutan dari halaman pindai saat transkripsi karena teks OCR pada PDF tampak keliru di satu posisi |
| Inventaris bunyi dan huruf | Vokal pendek a, i, u, e, o; vokal panjang aa, ii, uu, ee, oo; konsonan yang dicantumkan b, d, g, h, k, l, m, n, p, r, s, t, w | Simpan bentuk lema apa adanya. Normalisasi pencarian harus mempertahankan perbedaan vokal panjang dan tidak menyamakan bentuk secara diam-diam |
| Glotal dan pranasal | Glotal hambat dilambangkan apostrof; mb, nd, ngg disebut fonem pranasal dan ditempatkan di bawah M/N dalam pengurutan | Tetapkan aturan input, tampilan, normalisasi, dan pengurutan apostrof serta mb/nd/ngg. Uji dengan contoh yang disahkan editor |
| Fonetik | Panduan memakai tanda garis miring untuk lambang fonetik | Bedakan ejaan lema dari transkripsi. Setiap transkripsi modern harus menyebut sistemnya (misalnya IPA atau sistem praktis); jangan menganggap notasi 1985 identik dengan IPA modern tanpa pemeriksaan |
| Morfologi | Membedakan morfem bebas sebagai kata dasar (headword) dan morfem terikat/afiks; bentuk berimbuhan dicatat sebagai kata bawahan/subentri | Dukung relasi lema dasar–bentuk turunan dan pencarian subentri. Bentuk turunan tidak otomatis disamakan dengan lema dasar |
| Afiksasi | Memberi contoh prefiks, infiks, sufiks dan penggabungan dengan morfem dasar | Tambahkan struktur analisis morfologi secara bertahap: bentuk permukaan, segmentasi, label afiks, lema dasar, dan glos; tandai analisis yang belum diverifikasi |
| Reduplikasi | Membahas bentuk ulang penuh, sebagian, dan berimbuhan; tanda hubung dipakai di antara unsur yang diulang | Pertahankan tanda hubung dalam bentuk tampilan dan pencarian; dokumentasikan apakah bentuk ulang menjadi lema mandiri atau subentri menurut kebijakan editor |
| Pronomina | Mengelompokkan bentuk pronomina persona dan pronomina milik menurut persona/jumlah | Gunakan kelas kata dan fitur gramatikal terstruktur bila data mendukung (persona, jumlah, fungsi); jangan hanya menyimpan semua informasi sebagai catatan bebas |
| Lambang dan singkatan | Menjelaskan tanda rujuk ke entri/subentri, persamaan, keterangan, makna, fonetik, dan penggabungan | Buat legend/label antarmuka untuk rujuk-silang, varian, bentuk turunan, definisi, dan transkripsi; jangan mengekspos simbol cetak tanpa penjelasan |
| Dialek/wilayah | Prakata menyebut penelitian dan pengumpulan data di Sulawesi Tenggara serta bantuan informan, tetapi cuplikan petunjuk tidak menetapkan sistem label dialek yang lengkap | Simpan lokasi/sumber sebagai provenance; jangan menciptakan label dialek tertentu berdasarkan kamus saja. Tetapkan taksonomi setelah verifikasi lapangan |
| Kelas kata dan definisi | Contoh kamus menunjukkan kategori dan makna, tetapi tidak menyediakan di cuplikan ini taksonomi editorial lengkap untuk aplikasi baru | Buat daftar kelas kata dan gaya definisi yang terdokumentasi; petakan label warisan ke label baru tanpa menghapus label sumber |
| Sumber dan hak | Buku mencantumkan tim penyusun, penerbit, tahun, metode pengumpulan data secara umum, serta pembatasan reproduksi | Catat sitasi bibliografis dan asal setiap data. Jangan menyalin isi kamus secara massal ke produk tanpa memastikan izin/hak dan dasar penggunaan; kutipan akademik tetap dibatasi seperlunya |

### Kebijakan yang harus disahkan tim

| Keputusan | Usulan awal | Penanggung jawab |
|---|---|---|
| Versi ortografi | Gunakan konvensi 1985 sebagai profil historis yang terdokumentasi; sahkan perubahan melalui editor/penutur dan beri versi | Editor bahasa/ahli Tolaki |
| Ejaan dan normalisasi | Bedakan bentuk asli, bentuk normalisasi pencarian, dan varian; jangan hilangkan vokal panjang, apostrof, atau tanda hubung | Editor bahasa dan pengembang |
| Pengurutan | Terapkan alfabet Tolaki dari panduan sebagai awal; verifikasi urutan terhadap pindai dan putuskan aturan lema pranasal | Editor bahasa |
| Bentuk turunan | Putuskan kapan afiksasi/reduplikasi menjadi subentri, bentuk rujuk, atau lema tersendiri | Ahli morfologi/editor |
| Transkripsi | Pilih sistem transkripsi untuk data baru dan simpan label sistem serta sumber | Ahli fonetik/editor |
| Dialek/wilayah | Buat label hanya berdasarkan bukti lapangan/rujukan yang jelas; catat tingkat kepastian | Peneliti lapangan |
| Kelas kata dan fitur gramatikal | Definisikan label, padanan istilah Indonesia/Inggris, serta fitur seperti persona/jumlah bila relevan | Editor linguistik |
| Definisi dan padanan | Tentukan bahasa definisi, gaya definisi, pemisahan makna, dan cara memberi konteks pada padanan | Tim leksikografi |
| Provenance, persetujuan, dan hak | Tetapkan sitasi, izin, anonimisasi, akses metadata informan, dan kebijakan penggunaan sumber cetak | Penanggung jawab penelitian |
| Status editorial | Definisikan syarat minimum draft, review, publikasi, koreksi, dan penarikan entri | Editor utama |

Keputusan yang belum disahkan harus tetap berstatus **terbuka**. Catat alasan, bukti, pemilik keputusan, versi panduan, dan tanggal perubahan pada dokumen kebijakan; jangan mengubahnya menjadi asumsi tersembunyi dalam kode.

### Prasyarat tambahan sebelum migrasi korpus

- **Profil ortografi berversi:** simpan aturan ejaan dan pengurutan sebagai versi kebijakan yang bisa ditautkan ke entri. Setiap bentuk yang dinormalisasi harus tetap dapat dilacak ke bentuk sumber.
- **Hak dan provenance per sumber:** bedakan metadata bibliografis, izin penggunaan, data hasil transkripsi, dan data lapangan. Pembatasan reproduksi pada panduan 1985 berarti isi cetak tidak boleh disalin massal ke aplikasi sebelum status hak dan izin diperiksa.
- **Keputusan semantik yang dapat direvisi:** pemecahan satu baris lama menjadi beberapa lema/makna harus mencatat alasan dan pemeriksanya. Jangan gunakan pemisah koma sebagai aturan otomatis.
- **Publikasi berlapis bahasa:** tetapkan apakah entri boleh diterbitkan ketika padanan Inggris belum tersedia, dan tampilkan status kelengkapan secara jujur.

Rancangan awal dapat dikerjakan sebelum semua keputusan ini disahkan: inventaris file lokal, dokumentasi skema, dan prototipe migrasi pada salinan data. Migrasi produksi dan penyalinan isi kamus cetak tetap menunggu pemeriksaan hak, verifikasi ortografi, dan rekonsiliasi editor.
## 4. Rancangan model data

### Entitas yang disarankan

```text
Lemma
 â”œâ”€â”€ LemmaForm (bentuk utama, varian, bentuk lama, catatan ortografi)
 â”œâ”€â”€ Pronunciation (transkripsi, sistem, audio, informan/sumber)
 â”œâ”€â”€ Sense (nomor makna, definisi, kelas kata, domain, ragam, catatan)
 â”‚    â”œâ”€â”€ Translation (bahasa, padanan, tipe padanan, konteks/catatan)
 â”‚    â”œâ”€â”€ Example (kalimat Tolaki + terjemahan per bahasa + sumber)
 â”‚    â””â”€â”€ SenseRelation (sinonim, antonim, hiponim, dll. bila terverifikasi)
 â”œâ”€â”€ Etymology (opsional, hanya bila ada bukti)
 â””â”€â”€ Evidence/Source (rujukan, catatan lapangan, kontributor, tanggal)
```

### Tabel inti (nama konseptual)

| Tabel | Kolom penting | Catatan |
|---|---|---|
| `lemmas` | `id`, `headword`, `headword_normalized`, `status`, `created_by` | Satu lema Tolaki sebagai pusat entri |
| `lemma_forms` | `lemma_id`, `form`, `form_normalized`, `form_type`, `region_id`, `notes` | Variasi ejaan, bentuk alternatif, dan bentuk rujuk |
| `senses` | `lemma_id`, `sense_number`, `definition`, `part_of_speech_id`, `domain_id`, `register_id`, `usage_note`, `cultural_note` | Makna dipisah satu per satu |
| `translations` | `sense_id`, `language_code`, `form`, `normalized`, `translation_type`, `context_note` | Banyak padanan per makna; kode bahasa stabil (`tol`, `id`, `en`) |
| `examples` | `sense_id`, `tolaki_text`, `source_id`, `region_id`, `elicitation_note` | Contoh terikat ke makna, bukan hanya lema |
| `example_translations` | `example_id`, `language_code`, `text`, `translator/source` | Terjemahan per bahasa, dapat ditinjau terpisah |
| `pronunciations` | `lemma_id` atau `sense_id`, `transcription`, `system`, `audio_path`, `source_id` | Penentuan keterikatan lema/makna mengikuti data |
| `sources` | `citation`, `source_type`, `date`, `rights`, `public_note` | Mendukung sitasi buku, korpus, atau catatan lapangan |
| `regions` | `code`, `name`, `description` | Daftar wilayah/dialek terkendali |
| `part_of_speech` | `code`, `label_id`, `label_en`, `definition` | Label konsisten dan dapat diterjemahkan |
| `editorial_events` | entitas, tindakan, pelaku, waktu, komentar | Riwayat review/publikasi yang tidak ditimpa |

### Aturan integritas data

- Identitas duplikat tidak ditentukan oleh pasangan terjemahan. Kandidat duplikat dihitung berdasarkan lema, bentuk, makna, dan keputusan editor.
- Dua makna dengan kelas kata yang sama tetap boleh ada pada lema yang sama.
- Terjemahan yang sama boleh dipakai pada banyak makna; konteks membedakan pemakaiannya.
- Nilai kosong dibedakan dari â€œtidak diketahuiâ€, â€œbelum ditelitiâ€, dan â€œtidak berlakuâ€ bila makna penelitian memerlukannya.
- Simpan kode bahasa, bukan label bebas seperti â€œBahasa Aâ€ atau â€œEnglishâ€ dalam data inti.
- Normalisasi pencarian tidak boleh mengganti atau menghapus bentuk asli yang ditampilkan dan dikutip.

## 5. Rencana migrasi bertahap

### Tahap A â€” Inventaris dan kebijakan

1. Ekspor seluruh entri, contoh, status, dan kategori ke salinan kerja.
2. Hitung jumlah entri menurut status, kelengkapan tiga bahasa, kelas kata, contoh, audio, dan duplikasi kandidat.
3. Tetapkan kebijakan pada Bagian 3, lalu beri versi/tanggal.
4. Buat kamus data (data dictionary) dan CSV contoh baru sesuai model final.

**Keluaran:** laporan baseline, pedoman editorial v1, data dictionary, skema yang disetujui.

### Tahap B â€” Skema kompatibel dan migrasi

1. Tambahkan tabel baru tanpa menghapus kolom lama.
2. Tulis migrasi data yang idempoten: setiap entri lama menjadi lema; nilai lama menjadi makna dan padanan sesuai pemetaan yang disetujui.
3. Pertahankan ID lama sebagai `legacy_entry_id`/referensi migrasi agar URL, audit, dan pelacakan silang tetap mungkin.
4. Simpan laporan baris bermasalah: kata kosong, variasi dipisahkan koma, arti gabungan, contoh tanpa terjemahan, label kelas kata tidak dikenal, atau konflik unik.
5. Jangan menebak pemisahan arti dari tanda koma atau garis miring secara otomatis. Tandai untuk review manusia.
6. Jalankan pemeriksaan jumlah, sampel manual, dan rekonsiliasi sebelum mengganti pembacaan aplikasi.

**Keluaran:** migrasi dapat diulang dengan aman, laporan pemetaan, daftar anomali yang perlu keputusan editor.

### Tahap C â€” Penulisan ganda dan backoffice

1. Ubah formulir admin menjadi alur lema â†’ tambah makna â†’ tambah padanan/contoh/sumber.
2. Saat transisi, tulis ke model baru dan, bila masih dibutuhkan kode lama, pertahankan sinkronisasi sementara yang eksplisit.
3. Jangan izinkan penghapusan massal data lama sebelum rekonsiliasi dan pencadangan.
4. Tambahkan status editorial dan pemeriksaan wajib sesuai pedoman.
5. Catat setiap perubahan status dan reviewer dalam riwayat; perubahan setelah terbit memicu review ulang.

**Keluaran:** editor dapat membuat dan merevisi struktur makna baru dengan jejak audit.

### Tahap D â€” Pembacaan publik dan pencarian

1. Alihkan halaman detail dan daftar ke model baru setelah validasi data.
2. Tampilkan lema, varian, makna bernomor, kelas kata, padanan berlabel bahasa, contoh paralel, sumber, dan label regional.
3. Berikan mode pencarian yang eksplisit: **Tolaki â†’ Indonesia/Inggris**, **Indonesia â†’ Tolaki**, **Inggris â†’ Tolaki**.
4. Beri tahu pengguna bahasa asal hasil; jangan menyamarkan pencarian definisi sebagai pencarian kata.
5. Terapkan pencarian prefix/teks sesuai strategi indeks; untuk volume besar pertimbangkan full-text search.
6. Indeks huruf dibangun dari ortografi yang disepakati, bukan diasumsikan Aâ€“Z.

**Keluaran:** pencarian tiga arah konsisten dan halaman detail mencerminkan struktur makna.

### Tahap E â€” Penutupan transisi

1. Bandingkan hitungan dan sampel lama-baru, termasuk tautan, audio, kategori, dan contoh.
2. Arsipkan ekspor lama serta laporan migrasi.
3. Hentikan pembacaan kolom lama hanya setelah tidak ada fitur atau proses impor yang mengandalkannya.
4. Hapus kolom lama lewat migrasi terpisah setelah periode penerimaan dan pencadangan yang disepakati.

## 6. Backlog implementasi

| ID | Pekerjaan | Prioritas | Dependensi | Kriteria selesai | Status |
|---|---|---|---|---|---|
| LEX-01 | Susun pedoman ortografi, kelas kata, dialek, transkripsi | P0 | Keputusan tim | Dokumen disetujui editor dan dipakai pada formulir | Draf ortografi 1985 tersedia; menunggu pengesahan ahli/penutur |
| LEX-02 | Definisikan sumber data dan metadata informan | P0 | Persetujuan penelitian | Tiap entri dapat ditautkan ke bukti/sumber atau berstatus belum bersumber | Form sumber dan kontrol visibilitas tersedia; protokol informan belum ditetapkan |
| DATA-01 | Finalisasi skema lema-makna-padanan-contoh | P0 | LEX-01 | ERD dan data dictionary ditinjau sebelum migrasi | Skema awal diterapkan; nomenklatur/label masih perlu review editor |
| DATA-02 | Buat migrasi kompatibel dan laporan anomali | P0 | DATA-01 | Tidak menghapus data; semua baris lama terpetakan atau tercatat untuk review | Bridge legacy tersedia; sinkronisasi dry-run berisi 0 data |
| DATA-03 | Alur review, versi perubahan, dan provenance | P0 | LEX-02, DATA-01 | Status dan perubahan memiliki pelaku, waktu, alasan | Perubahan dan status dicatat; adjudikasi dua reviewer belum tersedia |
| ADMIN-01 | Form admin untuk banyak makna/padanan/contoh | P1 | DATA-01 | Editor dapat mengelola struktur tanpa menggabungkan makna | Form lema/makna tersedia di Filament |
| SEARCH-01 | Pencarian tiga arah | P1 | DATA-01 | Kueri tiap bahasa menemukan padanan dan hasil menyebut bahasa asal | Filter Tolaki/Indonesia/Inggris tersedia; hasil label asal kueri masih perlu dipoles |
| UI-01 | Halaman hasil dan detail berbasis makna | P1 | DATA-01 | Padanan dan contoh ditampilkan pada makna yang tepat | Tampilan publik beralih ke skema baru |
| IMPORT-01 | CSV impor/ekspor versi baru | P1 | DATA-01 | Banyak makna/padanan dapat dibawa pulang-pergi tanpa kehilangan hubungan | Belum dikerjakan; endpoint CSV lama tetap ditandai legacy |
| QA-01 | Laporan audit kelengkapan dan kandidat duplikat | P1 | DATA-02 | Editor dapat mengunduh daftar untuk ditinjau, bukan auto-merge | Belum dikerjakan |
| DATASET-01 | Ekspor korpus dan dokumentasi dataset | P2 | DATA-03 | Skema, lisensi, anonimisasi, dan batas penggunaan terdokumentasi | Belum dikerjakan |
| ACCESS-01 | Audit aksesibilitas dan kinerja pencarian | P2 | UI-01, SEARCH-01 | Navigasi keyboard dan waktu pencarian memenuhi target tim | Belum dikerjakan |

## 7. Spesifikasi fitur dan kriteria penerimaan

### Pencarian

- Pengguna memilih atau dikenali menggunakan bahasa kueri: Tolaki, Indonesia, atau Inggris.
- Mode default dijelaskan secara jelas dan tidak mencampur hasil tanpa label.
- Hasil pencarian menunjukkan bentuk yang cocok, bahasa bentuk tersebut, lema Tolaki terkait, serta ringkasan makna.
- Varian ejaan membawa pengguna ke lema utama dan tetap terlihat sebagai varian.
- Filter kelas kata, wilayah/dialek, dan domain hanya muncul bila data tersedia dan memiliki definisi.
- Pencarian kosong, apostrof, diakritik, tanda hubung, dan variasi kapital diuji memakai contoh ortografi yang disetujui.

### Entri kamus

- Lema dapat memuat beberapa bentuk dan beberapa makna bernomor.
- Setiap makna dapat mempunyai kelas kata, definisi, catatan, dan beberapa padanan pada masing-masing bahasa.
- Contoh ditautkan ke makna; terjemahan tidak dianggap sebagai teks Tolaki.
- Sumber dan status validasi tampak bagi editor; tampilan publik mengikuti kebijakan kutipan dan privasi.
- Audio memiliki metadata pelafal/sumber dan hak penggunaan bila diperlukan.

### Editorial

- Alur status minimal: draft â†’ submitted â†’ reviewed â†’ published, dengan rejected/returned untuk revisi.
- Editor tidak dapat menerbitkan data yang belum memenuhi checklist wajib kecuali ada override dengan alasan tercatat.
- Perubahan pada entri terbit membuat versi baru atau mengembalikan entri ke antrian review sesuai kebijakan.
- Hak akses contributor, editor bahasa, reviewer, dan admin dibedakan.

## 8. Rencana audit dan pembersihan data

Buat laporan terpisah, jangan otomatis memperbaiki isi linguistik.

- Entri tanpa padanan Indonesia atau Inggris.
- Entri tanpa kelas kata, definisi, sumber, atau contoh.
- Bentuk Tolaki yang berisi tanda koma, garis miring, atau daftar bentuk.
- Pasangan lema/kelas kata yang terduplikasi atau hampir sama setelah normalisasi.
- Padanan sama dengan makna berbeda yang belum diberi konteks.
- Contoh yang terjemahannya kosong atau bukan terjemahan kalimat Tolaki.
- Audio tanpa metadata atau berkas hilang.
- Label kelas kata dan kategori tidak sesuai daftar terkendali.
- Variasi dialek/ejaan yang belum mempunyai bentuk rujuk.

Setiap anomali memiliki ID entri, jenis masalah, nilai saat ini, tindakan editor, pelaku, dan tanggal keputusan.

## 9. Validasi akademik dan evaluasi

### Validasi isi

- Review dilakukan oleh minimal satu editor yang memahami Tolaki; keputusan krusial dapat memerlukan penilai kedua.
- Gunakan sampel berstrata: kelas kata, wilayah, sumber, status, dan jenis padanan.
- Catat perbedaan penilaian dan cara resolusinya; jangan hanya menyimpan nilai akhir.
- Untuk definisi/terjemahan, evaluasi ketepatan makna, kealamian, konteks, dan kecukupan bukti.

### Evaluasi aplikasi

- Uji integritas migrasi, hubungan antar makna, pencarian tiga arah, izin editorial, impor-ekspor, dan tampilan publik.
- Uji kasus ortografi nyata yang dipilih ahli, termasuk varian yang disepakati.
- Tetapkan target keberhasilan pencarian dengan himpunan kueri uji yang diberi penilaian editor.
- Pantau kueri tanpa hasil, hasil yang sering dibuka, dan koreksi pengguna tanpa merekam data pribadi yang tidak diperlukan.

## 10. Risiko dan mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Migrasi memecah padanan gabungan secara keliru | Makna linguistik berubah | Jangan auto-split; buat antrian review dan simpan nilai sumber |
| Skema terlalu rumit untuk editor | Data tidak terisi konsisten | Form bertahap, pilihan terkendali, panduan kontekstual |
| Standar ortografi belum disepakati | Pencarian dan indeks tidak konsisten | Putuskan kebijakan sebelum normalisasi dan migrasi final |
| Sumber/informan sensitif | Risiko privasi atau pelanggaran persetujuan | Pisahkan metadata privat/publik dan kontrol akses |
| Penulisan ganda lama-baru menyimpang | Data berbeda antar tabel | Batasi masa transisi, log sinkronisasi, rekonsiliasi berkala |
| Padanan Inggris tidak tersedia untuk semua entri | Tampak sebagai kekurangan padahal bukti belum ada | Bedakan belum tersedia dari tidak berlaku; tetapkan kebijakan publikasi |

## 11. Rencana kerja per iterasi

| Iterasi | Fokus | Keluaran untuk ditinjau |
|---|---|---|
| 0 | Keputusan editorial dan baseline data | Pedoman v1, laporan inventaris, ERD awal |
| 1 | Skema inti dan migrasi percobaan | Migrasi contoh, laporan anomali, rekonsiliasi sampel |
| 2 | Backoffice dan provenance | Form lema/makna, sumber, antrian review |
| 3 | Tampilan publik dan pencarian tiga arah | Daftar, detail, hasil pencarian per bahasa |
| 4 | CSV, audit data, evaluasi | Impor-ekspor baru, laporan kualitas, hasil uji kueri |
| 5 | Rilis akademik | Dokumentasi versi dataset, kebijakan sitasi, catatan rilis |

Estimasi durasi ditetapkan setelah jumlah data, kapasitas editor, dan jadwal mata kuliah diketahui. Tiap iterasi berakhir dengan review linguistik dan teknis sebelum data/rute lama dihentikan.

## 12. Struktur dokumentasi pendamping

Untuk menjaga keterlacakan, buat dan pelihara dokumen berikut:

- `docs/pedoman-editorial.md` â€” aturan lema, makna, label, definisi, varian, dan contoh.
- `docs/data-dictionary.md` â€” definisi tabel/kolom, kardinalitas, kode bahasa, nilai kosong.
- `docs/protokol-pengumpulan-data.md` â€” metode, persetujuan, informan, dan sitasi sumber.
- `docs/keputusan-ortografi.md` â€” ortografi dan normalisasi pencarian.
- `docs/protokol-validasi.md` â€” checklist review, adjudikasi, dan versi.
- `docs/riwayat-migrasi.md` â€” pemetaan data lama, jumlah, pengecualian, dan rekonsiliasi.
- `docs/format-dataset.md` â€” ekspor, lisensi, privasi, sitasi, dan batas penggunaan.

## 13. Langkah pertama yang dapat dieksekusi

1. Tunjuk editor bahasa Tolaki dan penanggung jawab data.
2. Setujui keputusan di Bagian 3 dan dokumentasikan hal yang masih belum pasti.
3. Jalankan ekspor cadangan serta inventaris korpus pada database produksi/sumber data yang valid.
4. Tinjau CSV contoh dan beberapa puluh entri yang mewakili variasi data bersama editor.
5. Finalisasi ERD dan contoh data sebelum membuat migrasi skema baru.
6. Implementasikan migrasi hanya pada lingkungan salinan, lalu bandingkan jumlah dan sampel.
7. Setelah hasil diterima, lanjutkan ke form editorial dan pencarian tiga arah.

### Catatan eksekusi awal (17 Oktober 2026)

- Dibuat draf keputusan ortografi dan rancangan kamus data di `docs/`.
- Inventaris lokal menemukan database SQLite berisi **0 entri kamus**, sedangkan tiga dump SQL masing-masing berisi **34 entri dan 34 contoh**; sumber korpus aktif belum ditetapkan.
- CSV contoh lokal berisi 12 baris, seluruhnya berstatus draft; rinciannya tercatat di `docs/inventaris-awal-data.md`.
- Database MySQL `laravel_kamus_multibahasa` telah disiapkan dengan 26 tabel; enam migrasi leksikografi berhasil. Tabel entri tetap kosong; enam kategori awal dan satu profil ortografi kandidat tersimpan dalam status draf.
- Ditambahkan sinkronisasi kompatibilitas dari model lama ke skema baru serta perintah `dictionary:sync-legacy` untuk memproses ulang data lama secara eksplisit. Mode dry-run memastikan database sasaran saat ini berisi nol entri lama.
- Backoffice sekarang mengelola lema, banyak makna, padanan, varian, contoh, pelafalan, sumber, dan profil ortografi. Review/publikasi mencatat event editorial.
- Pencarian publik menggunakan skema lema/makna, menyediakan arah Tolaki/Indonesia/Inggris, memakai indeks awal Tolaki dari profil aktif atau profil kandidat, serta menampilkan provenance publik.
- Form create/edit dirapikan ke tab berurutan tanpa API `Tab::description()` yang tidak tersedia di versi Filament terpasang. Form mendukung definisi utama Indonesia dan definisi Tolaki/Inggris opsional; migrasi `2026_10_17_000007` telah diterapkan.
- Detail publik dan editor disusun ulang: komponen leksikal dikelompokkan, status ditampilkan, sumber diringkas di bagian akhir sesuai visibilitas/hak, relasi dapat dinavigasi, dan analisis morfologi publik dibatasi pada status ditinjau.
- Ditambahkan `docs/panduan-input-entri.md` berisi alur visual, tata cara create/edit per tab, etika sumber/audio, checklist, kesalahan umum, log kegiatan, dan rubrik penilaian usulan. Panduan menandai bahwa peran pengumpul data mahasiswa dan log aktivitas otomatis belum tersedia; pemisahan hak perlu dibereskan sebelum pemakaian kelas tanpa pengawasan.
- Endpoint legacy importer/exporter tetap diberi label lama; alur baru CSV terstruktur v1 sekarang tersedia untuk ekspor/impor lema, bentuk, analisis morfologi, makna, padanan, contoh, relasi makna, sumber, dan pelafalan.
- Impor CSV terstruktur melakukan upsert bertambah (tanpa menghapus data turunan yang tidak tercantum), memvalidasi struktur JSON sebelum transaksi, menurunkan status `published` menjadi `reviewed` untuk editor, serta menahan hak/visibilitas sumber dan audio pada status aman untuk ditinjau.
- Database MySQL yang disiapkan tetap kosong untuk lema. Dump SQL berisi 34 entri belum diimpor karena sumber korpus kanonik masih perlu ditetapkan dan dipetakan secara akademik.
- Langkah berikutnya: tentukan dump sebagai baseline, uji pemetaan pada salinan/data staging bersama editor Tolaki, lalu buat laporan rekonsiliasi dan review akademik.

## 14. Definisi selesai untuk versi akademik awal

Versi awal dapat dianggap siap ditinjau secara akademik bila:

- Struktur mendukung beberapa makna, beberapa padanan, variasi bentuk, dan contoh paralel.
- Pedoman ortografi, kelas kata, dan editorial memiliki versi serta pemilik yang jelas.
- Entri yang ditampilkan dapat ditelusuri ke sumber atau diberi status keterbatasan bukti.
- Pencarian Tolaki, Indonesia, dan Inggris bekerja dengan label bahasa yang benar.
- Migrasi memiliki laporan rekonsiliasi dan tidak membuang bentuk data lama.
- Editor dapat meninjau, memberi alasan, dan melacak perubahan/penerbitan.
- Ekspor dataset menjelaskan skema, sumber, batasan, dan kebijakan penggunaan.

Dokumen ini adalah rancangan kerja. Keputusan linguistik substantif dan pemisahan makna tetap perlu disahkan oleh tim ahli/penutur, bukan ditentukan otomatis oleh aplikasi.

